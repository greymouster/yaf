<?php
/**
 * verify code service
 *
 * @author octopus <zhangguipo@747.cn>
 * @final 2015-08-26
 */
class VerifyCodeService
{

	static private $_preCode = 'verifycode:';
	static private $_preTimes = 'verifytimes:';

	static private $_seed = '0123456789';


	static private $_maxIndex = 9;

	//发送验证码
	public function send($telephone)
	{
		//查询是否已经发送过
		$code=$this->getCode($telephone);
		if($code){
			//查询已发次数
			$times=$this->getData(self::$_preTimes.$telephone);
			if($times>5){
				throw new Exception("验证码获取过于频繁，请稍后重试");
			}
			$sendStatus = $this->sendSms($telephone,$code);
			if (!$sendStatus){
				throw new Exception('验证码获取过于频繁，请稍后重试。');
			}
		}else{
			//生成四位验证码
			$code = $this->_createCode($telephone);
			$saveStatus = $this->_saveCode($telephone, $code);
			if (!$saveStatus){
				throw new Exception('保存验证码失败。');
			}
			$sendStatus = $this->sendSms($telephone,$code);
			if (!$sendStatus){
				throw new Exception('验证码发送失败。');
			}
		}
		return true;
	}
	//查询是否已经发送过
	public function getCode($telephone){
		$redis = TZ_Redis::connect('user');
		$key=self::$_preCode.$telephone;
		$code=$redis->get($key);
		if($code){
			$redis->incr(self::$_preTimes.$telephone);
			$redis->expire($key,3600);
			return $code;
		}
		return false;
	}
	//查询数据
	public function getData($key){
		$redis = TZ_Redis::connect('user');
		return $redis->get($key);
	}
	public function delData($telephone){
		$redis = TZ_Redis::connect('user');
		$redis->del(self::$_preCode.$telephone);
		return $redis->del(self::$_preTimes.$telephone);
	}
	
	
	//保存验证码
	private function _saveCode($telephone, $code)
	{
		$redis = TZ_Redis::connect('user');
		$key=self::$_preCode.$telephone;
		$redis->set($key, $code);
		$redis->expire($key,3600);
		$count=self::$_preTimes.$telephone;
		$redis->incr($count);
		$redis->expire($count,3600);
		return true;
	}
	//生成随机码
	private function _createCode($telephone, $length = 4)
	{
		$code = '';
		for ($i=0;$i<$length;$i++) {
			$code .= self::$_seed{rand(0, self::$_maxIndex)};
		}
		return $code;
	}
	//发送短信
	private function sendSms($telephone,$code){

		$params = array();
		$params = array('day'=>date('Y-m-d H:i:s'),'company'=>'黑米世纪','checkCode'=>$code);
		$result = TZ_Loader::service('Sendmessage','Base')->send(11,$telephone,$params);
		if($result){
			return true;
		}
		return false;
	}
	//验证验证码
	public function valid($telephone, $validCode)
	{
		$key=self::$_preCode.$telephone;
		$code=$this->getData($key);
		if($validCode==$code){
			$this->delData($telephone);
			return true;
		}
		return false;
	}	

}
