<?php
/**
 * response object
 * 
 * @author vincent <vincent@747.cn>
 * @final 2013-06-02
 */

class TZ_Response
{
	/**
	 * 错误配置
	 * 
	 * @var unknown
	 */
	static private $_error = null;
	
	/**
	 * 返回结果是否加密
	 * 
	 * @var boolean
	 */
	static private $_cipherEnable = false;
	
	/**
	 * 请求成功
	 * 
	 * @param array $data
	 * @return void
	 */
	static public function success($data=array(), $message='')
	{
		if (is_array($data) && empty($data)) {
			$data = array2object($data);
		}
		$response = array(
			'code'		=> 0,
			'message'	=> $message,
			'data'		=> $data
		);
		
		//send
		self::sendJson($response);
	}
	
	/**
	 * 请求错误
	 * 
	 * @param int $code
	 * @return void
	 */
	static public function error($code, $errorMessage = null, $data = array())
	{
		// $errorMessage = (null === $errorMessage) ? self::_getErrorMessage($code) : $errorMessage;
		$response = array(
			'code' => $code,
			'message' => $errorMessage,
			'data'	=> array2object($data)
		);
		//send
		self::sendJson($response);
	}
	
	/**
	 * 发送JSON数据
	 * 
	 * @param array $response
	 * @param boolean $exists
	 * @return void
	 */
	static public function sendJson($response, $exists = true)
	{
		$jsonData = json_encode($response);
		if (empty($_GET['debug']) && self::$_cipherEnable) {
			$cipherResponse = (new TZ_Mcrypt(Yaf_Registry::get('config')->cipher->key))->encode($jsonData);
			$jsonData = json_encode(array('response' => $cipherResponse));
		}
		
		switch (Yaf_Registry::get('FORMAT')) {
			case 'json':
				header("Content-type:application/json;charset=utf-8");
				break;
					
			case 'html':
				header("Content-type:text/html;charset=utf-8");
				break;
		
			default:
				#--------------- 测试 -------------------
				# header("Access-Control-Allow-Origin: *");
				#---------------------------------------
				header("Content-type:application/json;charset=utf-8");
		
		}
		
		echo $jsonData;
		if ($exists)
			exit;
	}
	
	/**
	 * 设置返回结果的加密状态
	 * 
	 * @param boolean $status
	 * @return boolean
	 */
	static public function setCipherEnable($status = true)
	{
		return self::$_cipherEnable = $status;
	}
	
	/**
	 * 获取错误信息
	 * 
	 * @return string
	 */
	static private function _getErrorMessage($code)
	{
		if (null === self::$_error) {
			$config = new Yaf_Config_Ini(APP_PATH.'/config/error.ini');
			self::$_error = $config->error->toArray();
		}
		if (!isset(self::$_error[$code]))
			throw new Exception('不存在的错误码');
		return self::$_error[$code];
	}
	
	/**
	 * 获取需要记录的日志信息
	 * 
	 * @param int $code
	 * @param string $detail
	 * @return string
	 */
	static public function getLogMessage($code, $detail)
	{	
		$logMessage = '';
		$logMessage .= ' [API_NAME]:'.Yaf_Registry::get('API_NAME');
		$logMessage .= ' [OS_NAME]:'.Yaf_Registry::get('OS_NAME');
		$logMessage .= ' [MALL_NO]:'.Yaf_Registry::get('MALL_NO');
		$logMessage .= ' [APP_NAME]:'.Yaf_Registry::get('APP_NAME');
		$logMessage .= ' [APP_VERSION]:'.Yaf_Registry::get('APP_VERSION');
		$logMessage .= ' [PARAMS_GET]:'.json_encode($_GET, true);
		$logMessage .= ' [PARAMS_POST]:'.json_encode($_POST, true);
		$logMessage .= " [RESPONSE_CODE]:{$code} [RESPONSE_DETAIL]:{$detail}";
		return $logMessage;
	}

}