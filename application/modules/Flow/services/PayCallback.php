<?php

/**
 * author: mengqi<zhangxuan@heimilink.com>.
 * Time: 2016/9/8 17:41
 * Info: 支付回调
 */
class PayCallbackService
{

    public function doAfterPay($params = array())
    {
        $primaryKey = $params['id'];
        $iccid = $params['iccid'];
        $packCode = $params['packCode'];

        //查询卡套餐数据
        $cardPack = TZ_Loader::model('CardPackage', 'Common')->select(['id:eq' => $primaryKey], '*', 'ROW');
        //更新订单数据
        TZ_Loader::model('RechargeOrders', 'Common')->update(['status' => 2, 'recharge_status' => 2], ['order_id:eq' => $cardPack['order_id']]);
        //更新卡套餐表
        TZ_Loader::model('CardPackage', 'Common')->update(['status' => 1], ['id:eq' => $primaryKey]);

        $notifyUrl = Yaf_Registry::get('config')->virtual->notifyUrl;
        $data = array(
            'iccid' => $iccid,
            'packageCode' => $packCode,
            'order_id' => $cardPack['order_id'],
            'notifyUrl' => $notifyUrl
        );

        //获取交易流水号
        $rest = TZ_Loader::service('RechargeLog', 'Common')->select(['order_id:eq' => $cardPack['order_id']], '*', 'ROW');
        //虚商充值
        if (!TZ_Loader::service('Charge', 'Flow')->chargePackages($data)) {
            //插入队列
            TZ_Loader::service('RepeatMechanism', 'Flow')->AsyMechanism($rest['trade_no']);
        }
    }

}