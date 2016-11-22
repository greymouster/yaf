<?php

/**
 * author: mengqi<zhangxuan@heimilink.com>.
 * Time: 2016/9/7 17:33
 * Info:
 */
class DailyFlowService
{
    public function getDailyFlow($params =array())
    {
        $url = Yaf_Registry::get('config')->virtual->url;
        $hmCode = $params['iccid'];
        $month = date('Y-m', strtotime($params['month']));

        $data = array();
        $data['partner'] = Yaf_Registry::get('config')->virtual->partner;
        $data['service'] = 'heimi_flow_dailyFlowLog';
        $data['hmCode'] = $hmCode;
        $data['month'] = $month;
        //生成签名
        $sign = TZ_Loader::service('VirtualMerchant', 'Base')->_getSign($data);

        $data['sign'] = $sign;

        return json_decode(TZ_Loader::service('CurlTool', 'Base')->sendcurl($url, 'post', $data), true);
    }

}