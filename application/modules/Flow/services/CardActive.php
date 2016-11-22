<?php

/**
 * author: mengqi<zhangxuan@heimilink.com>.
 * Time: 2016/9/7 15:56
 * Info:
 */
class CardActiveService
{
    /**
     * 卡激活服务
     * @param $params
     * @return mixed
     */
    public function active($params = array())
    {
        TZ_Loader::service('Log', 'Base')->writeLog($params, $this);
        $url = Yaf_Registry::get('config')->virtual->url;

        $data = array();
        $data['partner'] = Yaf_Registry::get('config')->virtual->partner;
        $data['service'] = 'heimi_card_acitve';
        $data['hmCode'] = $params['iccid'];
        //生成签名
        $sign = TZ_Loader::service('VirtualMerchant', 'Base')->_getSign($data);

        $data['sign'] = $sign;

        return json_decode(TZ_Loader::service('CurlTool', 'Base')->sendcurl($url, 'post', $data), true);
    }
}