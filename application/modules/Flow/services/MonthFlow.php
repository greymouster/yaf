<?php

/**
 * author: mengqi<zhangxuan@heimilink.com>.
 * Time: 2016/9/7 17:45
 * Info:
 */
class MonthFlowService
{

    /**
     * 获取月流量
     * @param $params
     * @return mixed
     */
    public function getMonthFlow($params = array())
    {
        $url = Yaf_Registry::get('config')->virtual->url;
        $startMonth = date('Y-m', strtotime($params['startMonth']));
        $endMonth = date('Y-m', strtotime($params['endMonth']));

        $data = array();
        $data['partner'] = Yaf_Registry::get('config')->virtual->partner;
        $data['service'] = 'heimi_flow_monthFlowLog';
        $data['hmCode'] = $params['iccid'];
        $data['startMonth'] = $startMonth;
        $data['endMonth'] = $endMonth;
        //生成签名
        $sign = TZ_Loader::service('VirtualMerchant', 'Base')->_getSign($data);

        $data['sign'] = $sign;

        return json_decode(TZ_Loader::service('CurlTool', 'Base')->sendcurl($url, 'post', $data), true);
    }

}