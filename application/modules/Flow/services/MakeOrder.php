<?php

/**
 * 用户购买套餐前的减积分、生成订单操作
 * @param  套餐id 用户id 卡iccid
 * @author  jialuo <wangkan@heimilink.com>
 * @Time    2016.9.8
 */
class MakeOrderService
{
    static $redis = null;

    public function __construct()
    {
        self::$redis = TZ_Redis::connect('user');
    }

    public function MakeOrder($par)
    {
        $params['stoken'] = $par['stoken'];
        $params['iccid'] = $par['iccid'];
        $params['imei'] = $par['imei'];
        $cid=TZ_Loader::model('ChannelCards','Common')->select(['iccid:eq'=>$params['iccid']],'cid','ROW')['cid'];

        $CardInfo = TZ_Loader::service('CardInfo', 'Flow')->getCardInfo($params);
        $Package = TZ_Loader::model('Packages', 'Common')->select(['pack_code:eq' => $par['package_id']], '*', 'ROW');
        $UserInfo = TZ_Loader::service('UserInfo', 'Flow')->getUserInfo($params);
        $uid = TZ_Loader::service('UserInfo', 'Flow')->getUserLogin($params)['uid'];

        $UserInfo = json_decode($UserInfo, true);
        $score = $UserInfo['data']['score'];
        if ($score < 10) {
            $score = 0;
        }
        if ($score < $Package['max_used_score']) {
            $score = $score - $score % 10;//$UserInfo['score']-$UserInfo['score']%10;
        } else {
            $score = $Package['max_used_score'];
        }
        if($score /1000 >= $Package['pack_price']){
            $score = $Package['pack_price']*1000;
        }


        /*
         * 生成订单
         */
        if($par['need_score']==1){
            $url=Yaf_Registry::get('config')->user_center->userinfo_url;
            $info['service']='update_user_score';
            $info['source']=$score;
            $info['uid']=$uid;
            $info['changeScore']='-'.$score;
            $info['desc']='套餐充值减积分';
            TZ_Loader::service('CurlTool','Base')->sendcurl($url,'post',$info);
        }
        $order['uid'] = $uid;
        $order['cid']=$cid;
        $order['order_id'] = TZ_Loader::service('TRandom', 'Base')->getOrderId();
        $order['order_name'] = $Package['pack_name'];
        $order['pack_code'] = $Package['pack_code'];
        $order['imei'] = $par['imei'];
        $order['iccid'] = $CardInfo['iccid'];
        $order['order_price'] = $Package['pack_price'];
        $order['discount_price'] = ($par['need_score'] == 1) ? $score / 1000 : 0;
        $order['payable_price'] = $Package['pack_price'] - $order['discount_price'];
        $order['use_score'] = ($par['need_score'] == 1) ? $score : 0;
        $order['give_score'] = $Package['give_score'];
        $order['pay_type'] = 'ali';
        $order['card_type'] = $CardInfo['cardType'];
        $order['effective_type'] = $par['method_pay'];
        $order['remarks'] = $par['remarks'] ? htmlspecialchars($par['remarks']) : '无';
        if ($order['payable_price'] == 0) {
            $order['status'] = 2;
        } else {
            $order['status'] = 1;
        }
        $order['recharge_status'] = 1;
        if (TZ_Loader::model('RechargeOrders', 'Common')->insert($order)) {
            return $order;
        } else {
            return array();
        }
    }

    /**
     * 生成购买信息
     */
    public function makeCardPackage($params, $condition)
    {
        $cp = TZ_Loader::model('CardPackage', 'Common')->select($condition, '*', 'ROW');
        $OrderData = TZ_Loader::model('RechargeOrders', 'Common')->select($condition, '*', 'ROW');
        $CardInfo = TZ_Loader::model('CardInfo', 'Common')->select(['iccid:eq' => $OrderData['iccid']], '*', 'ROW');
        $PackageInfo = TZ_Loader::model('Packages', 'Common')->select(['card_type:eq' => $CardInfo['card_type']], '*', 'ROW');
        if (!$cp) {

            $card_package = array(
                'type_id' => $CardInfo['card_type'],
                'iccid' => $OrderData['iccid'],
                'order_id' => $OrderData['order_id'],
                'status' => 1,
                'pack_code' => $OrderData['pack_code'],
                'pack_name' => $OrderData['order_name'],
                'pack_type' => $PackageInfo['pack_type'],
                'pack_price' => $OrderData['payable_price'],
                'pack_flow' => $PackageInfo['pack_flow'],
                'pack_duration' => $PackageInfo['pack_duration'],
                'pack_pic' => $PackageInfo['pack_pic'],
                'effective_type' => 0,
                'created_at' => date('Y-m-d H:i:s', time()),
                'updated_at' => date('Y-m-d H:i:s', time()),
            );
            $recharge_primary_id = TZ_Loader::model('CardPackage', 'Common')->insert($card_package);
        } else {
            $recharge_primary_id = $cp['id'];
        }


        $charge_param = [
            'iccid' => $OrderData['iccid'],
            'packageCode' => $OrderData['pack_code'],
            'order_id' => $OrderData['order_id']
        ];
        return array('cp' => $charge_param, 'rpid' => $recharge_primary_id);
    }

    /**
     * 查询用户充值次数
     * @return int
     */
    public function getRechargeTimes($param)
    {
        $condition['recharge_status:in'] = array(2, 3);
        $condition['updated_at:between'] = array(date('Y-m') . '-01 00:00:00', date('Y-m-d H:i:s'));
        $condition['iccid:eq'] = $param['iccid'];
        $count = TZ_Loader::model('RechargeOrders', 'Common')->select($condition, 'count(iccid) as total', 'ROW');
        return $count['total'];
    }


}