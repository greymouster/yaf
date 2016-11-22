<?php
/**
 * author: octopus<zhangguipo@heimilink.com>.
 * Time: 2016-09-23
 * Info:盒子配置接口
 */
class ConfigureController extends Yaf_Controller_Abstract
{
	//s.showboom.cn
	//http://s.showboom.cn/boxapi/configure/getMainConfigure?imei=867252020000096&version=57e5f693-7954
    //得到主屏配置
    public function getMainConfigureAction()
    {
    	TZ_Loader::service('Log','Base')->writeLog($_POST,  $this->getRequest());
    	TZ_Loader::service('Log','Base')->writeLog($_SERVER,  $this->getRequest());
    	 $params = TZ_Request::getParams('post');
    	 if(!isset($params['imei'])||empty($params['imei'])){
    	 	TZ_Response::error('102','参数错误或不足');
    	 }
    	 $version=isset($params['version'])?$params['version']:'';
        $result=TZ_Loader::service('Configure','Boxapi')->getMainConfigure($params['imei'],$version);
        if($result===true){
        	$result=array();
        }
        TZ_Response::success($result,'ok');
    }
//http://www.xbservice.com/boxapi/configure/getSecondaryConfigure?imei=8986011472311233842&version=57e4c29f-5810
//http://s.showboom.cn/boxapi/configure/getSecondaryConfigure?imei=867252020000096&version=57e5f6bb-6969
    //得到主屏配置
    public function getSecondaryConfigureAction()
    {
    	TZ_Loader::service('Log','Base')->writeLog($_POST,  $this->getRequest());
    	TZ_Loader::service('Log','Base')->writeLog($_SERVER,  $this->getRequest());
    	 $params = TZ_Request::getParams('post');
    	 if(!isset($params['imei'])||empty($params['imei'])){
    	 	TZ_Response::error('102','参数错误或不足');
    	 }
    	 $version=isset($params['version'])?$params['version']:'';
        $result=TZ_Loader::service('Configure','Boxapi')->getSecondaryConfigure($params['imei'],$version);
        if($result===true){
        	$result=array();
        }
        TZ_Response::success($result,'ok');
    }

}