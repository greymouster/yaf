<?php
class TestController extends Yaf_Controller_Abstract
{
	public function indexAction()
	{
		Yaf_Dispatcher::getInstance()->enableView();
	}

	public function jsonAction()
	{
		Yaf_Dispatcher::getInstance()->enableView();
	}
	
	public function infoAction()
	{
		phpinfo();	
	}
	
	public function codeAction()
	{	
		$n = $o = array();
		$key = '7b7e7b6165923a5d';
		$data[] = md5('1');
		$data[] = md5('2');
		$data[] = md5('3');
		$data[] = md5('4');
		$data[] = md5('5');
		$data[] = md5('6');
		$data[] = md5('7');
		$data[] = md5('8');
		$data[] = md5('9');
		$data[] = '你好，哈哈！';
		$data[] = '没什么~ 梦_1243的';
		d($data);
 		$cipher = new TZ_Mcrypt($key);
		foreach ($data as $v) {
			$n[] = $cipher->encode($v);
		}
		d($n);
		$cipher2 = new TZ_Mcrypt($key);
		foreach ($n as $v) {
			$o[] = $cipher2->decode($v);
		}
		d($o); 
		
		//$cipher3 = new TZ_Mcrypt($key);
		//d($cipher3->decode('Tdl4CAKwkZ1sMfk9ty4moY6mfJFJHGk2xbD2ghkXlFX8i8YpFvtpYee1dL5n6Gqb'));
		
		
		
		TZ_Request::send(array());
	}

	public function idAction()
	{	
		echo 'date:', date('Y-m-d H:i:s'), '<br/>';
		echo 'rdate:', rdate('Y-m-d H:i:s'), '<br/>';
		
	}

	public function paramAction()
	{
		//login
		$login = array(
			'telephone' => '18600622921',
			'password' => 'ADSF124124ASDF234R2FASDF23RFASWR23FAWSDFAWDE'
		);
		echo json_encode($login), '<br/>';
		
		//change password
		$reg = array(
			'session_id' => '18600622921',
			'password' => 'ADSF124124ASDF234R2FASDF23RFASWR23FAWSDFAWDE',
			'old_password' => 'ADSF124124ASDF234R2FASDF23RFASWR23FAWSDFAWDE'
		);
		echo json_encode($reg), '<br/>';
		
		//reset password
		$reg = array(
			'telephone' => '18600622921',
			'verify_code' => '1024',
			'password' => 'ADSF124124ASDF234R2FASDF23RFASWR23FAWSDFAWDE'
		);
		echo json_encode($reg), '<br/>';
		
		//verify_code
		$reg = array(
				'telephone' => '18600622921',
				'verify_code' => '1024'
		);
		echo json_encode($reg), '<br/>';
		
		//get_code
		$code = array(
			'telephone' => '18600622921',
			'reset' => '1'
		);
		echo json_encode($code), '<br/>';
		
		//modify
		$code = array(
			'session_id' => '18600622921',
			'name' => '88liang',
			'gender' => '1',
			'city'	=>	'beijing',
			'area'	=> 'chaoyangqu'
		);
		echo json_encode($code), '<br/>';
		
		//details
		$code = array(
				'session_id' => '18600622921',
				'user_id' => '88liang'
		);
		echo json_encode($code), '<br/>';
		
		//register
		$reg = array(
			'telephone' => '18600622921',
			'password' => 'ADSF124124ASDF234R2FASDF23RFASWR23FAWSDFAWDE',
			'verify_code' => '1024',
			'name' => '88liang'
		);
		echo json_encode($reg), '<br/>';
		
		
		//logout
		$logout = array(
			'session_id' => '18600622921'
		);
		echo json_encode($logout), '<br/>';
	
	}
	
	public function cipherAction()
	{
		$cipher = new TZ_Mcrypt(Yaf_Registry::get('config')->install->key);
		echo $cipher->encode($_GET['param']);
	}
}
