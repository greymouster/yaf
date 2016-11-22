<?php

/**
 * ad controller
 * @author octopus <zhangguipo@747.cn>
 * @final 2016-01-06
 */
class ClickController extends Yaf_Controller_Abstract
{
    /**
     * 点击数据
     */
    public function indexAction()
    {
        //http://www.adapi.com/adapi/click/index?tag_code=kikitag&app_code=111&mac=-mac-&idfa=-idfa-&idfv=-idfv-&imei=-imei-&phone=-phone-&ad_id=478
        //得到其他参数
        $params = TZ_Request::getParams('get');
        //得到应用标签
        if (empty($params['ad_id'])) {
            throw new Exception('ad_id参数不能为空');
        }
        $data['mac'] = empty($params['mac']) ? '-mac-' : $params['mac'];
        $data['idfa'] = empty($params['idfa']) ? '-idfa-' : $params['idfa'];
        $data['idfv'] = empty($params['idfv']) ? '-idfv-' : $params['idfv'];
        $data['imei'] = empty($params['imei']) ? '-imei-' : $params['imei'];
        $data['phone'] = empty($params['phone']) ? '-phone-' : $params['phone'];
        $info = $data;
        $data['ad_id'] = $params['ad_id'];
        $data['platform'] = $params['platform'];
        $data['tag_code'] = $params['tag_code'];
        $data['app_code'] = $params['app_code'];

        TZ_Loader::service('Log', 'Adapi')->addClickLog($data);
        TZ_Response::success();
//		$url=TZ_Loader::service("Ad")->getRealURl($params['ad_id'],$info);
//		header("location:".$url);
    }
}