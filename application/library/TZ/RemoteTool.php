<?php
/**
* Curl Serivce Class
*
* @author 子龙<songyang@747.cn>
* @version 1.0
* @date 2014-05-10
*/
class TZ_RemoteTool {

	static public $timeout = 10;

	/**
	* send request
	*
	* @param  $url
	* @param  $type
	* @param  $args
	* @param  $charset
	*
	* @Returns
	*/
	public static function  send($url, $type = 'get', $args = array(), $charset = 'utf-8') {
		if ($type == 'post') {
			$returnValue = 	self::_post($url, $args, $charset);
		} else {
			$url .= '?' . http_build_query($args);
			$returnValue = self::_get($url, $charset);
		}
		return $returnValue;
	}

	private static function _post($url, $arguments, $charset = 'utf-8')
	{
		if(is_array($arguments)){
			$postData =  http_build_query($arguments);
		}else{
			$postData = $arguments;
		}

		$ch = curl_init(); 
		curl_setopt($ch, CURLOPT_DNS_USE_GLOBAL_CACHE, false);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
		curl_setopt($ch, CURLOPT_TIMEOUT, self::$timeout);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, self::$timeout);

		$returnValue = curl_exec($ch);
		curl_close($ch);
		if($charset != 'utf-8'){
			$returnValue = iconv($charset,'utf-8',$returnValue);
		}
		return $returnValue;
	}

	private static function _get($url, $charset = 'utf-8')
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_TIMEOUT, self::$timeout);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, self::$timeout);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
		$returnValue = curl_exec($ch);
		curl_close($ch);
		if($charset != 'utf-8'){
			$returnValue = iconv($charset,'utf-8',$returnValue);
		}
		return $returnValue;
	}
    
    public static function sendJson($url, $arguments, $charset = 'utf-8')
	{
		if(is_array($arguments)){
			$postData = json_encode($arguments);
		}else{
			$postData = $arguments;
		}

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_DNS_USE_GLOBAL_CACHE, false);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length:'. strlen($postData)));
		curl_setopt($ch, CURLOPT_TIMEOUT, self::$timeout);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, self::$timeout);

		$returnValue = curl_exec($ch);
		curl_close($ch);
		if($charset != 'utf-8'){
			$returnValue = iconv($charset,'utf-8',$returnValue);
		}
		return $returnValue;
	}
}
