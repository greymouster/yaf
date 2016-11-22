<?php
class ModelController extends Yaf_Controller_Abstract
{
	public function indexAction()
	{
		echo 'demo_model<br/>';
		echo Yaf_Registry::get('APP_TYPE'), '<br/>';
		echo Yaf_Registry::get('FORMAT_TYPE'), '</br>';
	}
	
	public function tableAction()
	{
		echo 'demo_table';	
	}

	public function testAction()
	{
		d($_POST);
		//d($GLOBALS['HTTP_RAW_POST_DATA']);
		d(file_get_contents('php://input'));
		$data = json_decode(file_get_contents('php://input'), 1);
		echo 'demo_test';	
	}
	
}
