<?php
/**
 * author: octopus<zhangguipo@heimilink.com>.
 * Time: 2016-09-25
 * Info:测试接口　
 */
class ApiController extends Yaf_Controller_Abstract
{
	public function getWxAction(){
    	$num=rand(100,999).rand(100,999).rand(100,999);
   		 $datas = array(
	            'oid' => $num,
	            'payCustom' =>$num,
	            'pname' => '黑米测试',
	            'price' => 0.02
			);
			$payCode = Yaf_Registry::get('config')->pay->niu;
			$payUrl = TZ_Loader::service('Pay', 'Base')->pay($datas, 'wx', $payCode, 2);
			
			$this->_view->assign("oid",$data['order_id']);
			$this->_view->assign("price",$data['payable_price']);
			$this->_view->assign("url",urlencode($payUrl));
			$this->_view->display('wx.tpl');
    
    }
	public function getAliAction(){
	  $this->_view->display('ali.tpl');
    }
	public function getAliUrlAction(){
    	$num=rand(100,999).rand(100,999).rand(100,999);
   		 $datas = array(
	            'oid' => $num,
	            'payCustom' =>$num,
	            'pname' => '黑米测试',
	            'price' => 0.02
			);
			$payCode = Yaf_Registry::get('config')->pay->niu;
			$payUrl = TZ_Loader::service('Pay', 'Base')->pay($datas, 'ali', $payCode, 0);
		     $datas['url'] = $payUrl;
	        $datas['order'] = $info;
	        TZ_Response::success($datas);
    }


    /**
     * 检测是否是秀豹设备
     */
    public function checkDeviceAction(){
        $imei = TZ_Request::getParams('post')['imei'];
        $res = TZ_Loader::model('Device','Boxapi')->select(['imei:eq'=>$imei],'id','ROW');
        if($res){
            TZ_Response::success([], '秀豹设备');
        }
        TZ_Response::error('202','非秀豹设备');
    }
}