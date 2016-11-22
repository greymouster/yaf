<?php

/**
 * author: mengqi<zhangxuan@heimilink.com>.
 * Time: 2016/9/7 17:53
 * Info:
 */
class ChargeService
{
    /**
     * 套餐叠加（充值）
     * @param $params
     * @return mixed
     */
    public function chargePackages($params = array())
    {
        $url = Yaf_Registry::get('config')->virtual->url;

        $data = array();
        $data['partner'] = Yaf_Registry::get('config')->virtual->partner;
        $data['service'] = 'heimi_card_chargePackage';
        $data['hmCode'] = $params['iccid'];
        $data['packageCode'] = $params['packageCode'];
        $data['orderId'] = $params['order_id'];
        $data['notifyUrl'] = Yaf_Registry::get('config')->virtual->notifyUrl;
        //生成签名
        $sign = TZ_Loader::service('VirtualMerchant', 'Base')->_getSign($data);
        $data['notifyUrl'] = urlencode($data['notifyUrl']);
        $data['sign'] = $sign;
        //resultCode
        return json_decode(TZ_Loader::service('CurlTool', 'Base')->sendcurl($url, 'post', $data), true);
    }

}