<?php

/**
 * author: mengqi<zhangxuan@heimilink.com>.
 * Time: 2016/9/7 17:30
 * Info:
 */
class FlowService
{
    /**
     * 剩余流量实时查询
     * @param $params
     * @return mixed
     */
    public function getRemainFlow($params = array())
    {
        $url = Yaf_Registry::get('config')->virtual->url;

        $data = array();
        $data['partner'] = Yaf_Registry::get('config')->virtual->partner;
        $data['service'] = 'heimi_flow_query';
        $data['hmCode'] = $params['iccid'];
        //生成签名
        $sign = TZ_Loader::service('VirtualMerchant', 'Base')->_getSign($data);

        $data['sign'] = $sign;

        return json_decode(TZ_Loader::service('CurlTool', 'Base')->sendcurl($url, 'post', $data), true);

    }

}