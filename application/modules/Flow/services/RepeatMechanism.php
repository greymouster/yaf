<?php

/**
 * author: mengqi<zhangxuan@heimilink.com>.
 * Time: 2016/9/14 14:58
 * Info: 异步处理失败情况
 */
class RepeatMechanismService
{
    static $redis = null;
    static $redis_key = 'xiubao:flow:recharge';

    public function __construct()
    {
        self::$redis = TZ_Redis::connect('user');
    }

    /**
     * 重试机制存数据
     * @param $trade_no
     * @return bool
     */
    public function AsyMechanism($trade_no)
    {
        TZ_Loader::service('Log', 'Base')->writeLog('----重试机制----', $this);
        $orderInfo = TZ_Loader::model('RechargeLog', 'Common')->select(['trade_no:eq' => $trade_no], '*', 'ROW');
        TZ_Loader::service('Log', 'Base')->writeLog($orderInfo, $this);
        $data = array(
            'hmCode' => $orderInfo['iccid'],
            'packageCode' => $orderInfo['pack_code'],
            'orderId' => $orderInfo['order_id'],
            'times' => $orderInfo['times']
        );
        if ($orderInfo['times'] < 5) {
            return $this->_set($data);
        } else {
            //充值失败记录日志
            TZ_Loader::service('Log', 'Base')->writeLog($orderInfo, $this);
            return false;
        }
    }

    /**
     * 取数据充值
     */
    public function AsyRecharge()
    {
        set_time_limit(0);
        while (true) {
            TZ_Loader::service('Log', 'Base')->writeLog('----充值失败重试begin----', $this);
            $data = json_decode($this->_get(self::$redis_key), true);
            TZ_Loader::service('Log', 'Base')->writeLog($data, $this);
            TZ_Loader::service('Log', 'Base')->writeLog('----订单状态begin----', $this);
            $statusRe = TZ_Loader::model('RechargeOrders', 'Common')->select(['order_id:eq' => $data['order_id']], 'status,recharge_status', 'ROW');
            TZ_Loader::service('Log', 'Base')->writeLog($statusRe, $this);
            TZ_Loader::service('Log', 'Base')->writeLog('----订单状态end----', $this);
            TZ_Loader::service('Log', 'Base')->writeLog('----卡套餐状态begin----', $this);
            $packStatus = TZ_Loader::model('CardPackage', 'Common')->select(['order_id:eq' => $data['order_id']], 'status', 'ROW')['status'];
            TZ_Loader::service('Log', 'Base')->writeLog($packStatus, $this);
            TZ_Loader::service('Log', 'Base')->writeLog('----卡套餐状态end----', $this);
            if ($statusRe['status'] == 2 && $statusRe['recharge_status'] == 2 && $packStatus == 1) {
                //充值
                TZ_Loader::service('Charge', 'Flow')->chargePackages($data);
                //更新充值重试次数
                $times = $data['times'] + 1;
                TZ_Loader::model('RechargeLog', 'Common')->update($times, ['trade_no:eq' => $data['trade_no']]);
            } else {
                die;
            }
            TZ_Loader::service('Log', 'Base')->writeLog('----充值失败重试end----', $this);
        }
    }

    public function _set($data)
    {
        return self::$redis->LPUSH(self::$redis_key, json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    private function _get($key)
    {
        return self::$redis->RPOP($key);
    }

}