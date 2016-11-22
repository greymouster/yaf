<?php

/**
 * author: mengqi<zhangxuan@heimilink.com>.
 * Time: 2016/9/7 17:50
 * Info:
 */
class CardPackagesService
{
    /**
     * 实时获取已订购套餐
     * @param $params
     * @return mixed
     */
    public function getCardPackages($params = array())
    {
        $url = Yaf_Registry::get('config')->virtual->url;

        $data = array();
        $data['partner'] = Yaf_Registry::get('config')->virtual->partner;
        $data['service'] = 'heimi_card_getPackages';
        $data['hmCode'] = $params['iccid'];
        //生成签名
        $sign = TZ_Loader::service('VirtualMerchant', 'Base')->_getSign($data);

        $data['sign'] = $sign;

        return json_decode(TZ_Loader::service('CurlTool', 'Base')->sendcurl($url, 'post', $data), true);
    }


}