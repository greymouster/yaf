<?php

/**
 * author: mengqi<zhangxuan@heimilink.com>.
 * Time: 2016/9/7 18:25
 * Info:
 */
class RechargeStatusService
{
    /**
     * 查询充值状态
     * @param $params
     * @return mixed
     */
    public function getRechargeStatus($params = array())
    {
        $url = Yaf_Registry::get('config')->virtual->url;
        $service_name = 'heimi_card_getRechargeStatus';

        $data = array();
        $data['partner'] = Yaf_Registry::get('config')->virtual->partner;
        $data['service'] = $service_name;
        $data['tradeNo'] = $params['tradeNo'];
        //生成签名
        $sign = TZ_Loader::service('VirtualMerchant', 'Base')->_getSign($data);

        $data['sign'] = $sign;

        return json_decode(TZ_Loader::service('CurlTool', 'Base')->sendcurl($url, 'post', $data), true);
    }

}