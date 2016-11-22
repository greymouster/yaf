<?php
/**
 * cookie code service
 *
 * @author octopus <zhangguipo@747.cn>
 * @final 2015-10-22
 */
class CookieService
{
	static private $_preMe = 'me:';
	static private $_preOther = 'other:';
	static private $_master_cookie_key = 'HM_MASTER_CLI_INM';
	
	//设置自己cookie
	public function setMeCookie($telephone,$pid=0){
		$key='';
		$data=array();
		$cookie=$this->getCookie(self::$_preMe);
		$cipher = new TZ_Mcrypt(Yaf_Registry::get('config')->cookie->key);
		//如果不存在
		if(empty($cookie)||empty($pid)){
			$key=$this->getOnlyNum();
			$data=array('key'=>$key,'telephone'=>$telephone,'data'=>$pid);
		}else{
			$cookieinfo = json_decode($cipher->decode(base64_decode($cookie)), true);
			$key=$cookieinfo['key'];
			$data=array_merge_recursive($cookieinfo,array('data'=>$pid));
		}
		$cookieData = base64_encode($cipher->encode(json_encode($data)));
		$this->setCookie(self::$_preMe, $cookieData);
		return $key;
	}
	//设置其他人
	public function setOtherCookie($aid){
		$key='';
		//查询是否已经参加过活动
		$data=array();
		$cookie=$this->getCookie(self::$_preOther);
		$cipher = new TZ_Mcrypt(Yaf_Registry::get('config')->cookie->key);
		//如果不存在
		if(empty($cookie)){
			$key=$this->getOnlyNum();
			$data=array('key'=>$key,'data'=>$aid);
		}else{
			$cookieinfo = json_decode($cipher->decode(base64_decode($cookie)), true);
			$key=$cookieinfo['key'];
			$data=array_merge_recursive($cookieinfo,array('data'=>$aid));
		}
		$cookieData = base64_encode($cipher->encode(json_encode($data)));
		$this->setCookie(self::$_preOther, $cookieData);
		return $key;
	}
	//查询cookie,判断是否是自己参加的活动
	public function getMeCookie($telephone){
		
		$cookie=$this->getCookie(self::$_preMe);
		if(!$cookie){
			return false;
		}
		$cipher = new TZ_Mcrypt(Yaf_Registry::get('config')->cookie->key);
		$cookie = json_decode($cipher->decode(base64_decode($cookie)), true);
		if(!empty($cookie)&&$cookie['telephone']==$telephone){
			return true;
		}
		return false;
	}

	//查询cookie,判断是否可以帮用户拼活动
	public function getOtherCookie($aid){
		$cipher = new TZ_Mcrypt(Yaf_Registry::get('config')->cookie->key);
		if(!$this->getCookie(self::$_preOther)){
			return false;
		}
		$cookie = json_decode($cipher->decode(base64_decode($this->getCookie(self::$_preOther))), true);
		//print_r($cookie);print_r('|');
		if(!empty($cookie)){
			$list=$cookie['data'];
			//判断是否是数组
			if(is_array($list)){
				$isTrue=false;
				foreach ($list as $key=>$val){
					if($val==$aid){
						$isTrue=true;
						break;
					}
				}
				return $isTrue;
			}elseif($list==$aid){
				return true;
			}
		}
		return false;
	}
	//生气唯一标识
	public function getOnlyNum(){
		return md5(time().rand(1000,9999));
	}
	//设置cookie
	public function setCookie($key,$date){
		$key=$this->getKey($key);
		$redis = TZ_Redis::connect('user');
		$redis->set($key, $date);
	}
	//设置redis
	public function getCookie($key){
		$key=$this->getKey($key);
		$redis = TZ_Redis::connect('user');
		return $redis->get($key);
	}
	
	
	//判断用户是否登录
	public function isLogin(){
		$cookie=$this->getCookie(self::$_preMe);
		if(!$cookie){
			return false;
		}
		$cipher = new TZ_Mcrypt(Yaf_Registry::get('config')->cookie->key);
		$cookie = json_decode($cipher->decode(base64_decode($cookie)), true);
		if(!empty($cookie)){
			$telephone=$cookie['telephone'];
			$user = TZ_Loader::model('PinUser', 'Pin')->select(array('telephone:eq' => $telephone), '*', 'ROW');
			if(count($user)==0){
				return false;
			}
			return $user;
		}
		return false;
	}
	//得到key值
	public function getKey($key){
		return $key.$this->getUniqueKey();
	}
	//得到浏览器信息
	public function getUniqueKey(){
		$agent=$_REQUEST['server'];
		$ip=TZ_Request::getRemoteIp();
		return md5($agent.strval($ip));
	}


	/**
	 * 设置联合登陆用户信息cookie信息
	 * @param $infoArr
	 */
	public function setUserInfoByMasterCookie($infoArr, $expire = 7200) {
		$info = base64_encode(json_encode($infoArr));
		setcookie(self::$_master_cookie_key, $info, time() + $expire, '/');
	}

	/**
	 * 获取联合登陆用户信息cookie信息
	 * @return mixed
	 */
	public function getUserInfoByMasterCookie() {
		return json_decode(base64_decode($_COOKIE[self::$_master_cookie_key]), true);
	}
}
