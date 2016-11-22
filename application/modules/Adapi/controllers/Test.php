<?php
/**
 * Test controller file
 *
 * @author vincent <piaoqingbin@maxvox.com.cn>
 * @final 2012-12-25
 */
class TestController extends Yaf_Controller_Abstract
{
	//index
	public function indexAction()
   	{
		echo 'Test';
   	}
   
   //xhprof debug
   public function debugAction()
   {
   	   if (empty($_GET['debug']))
   	   	   die('Debug not found.');

   	   $url = TZ_Redis::connect('user_db')->get("debug:xhprof:{$_GET['debug']}");
       echo '<a href="'.$url.'" target="_blank">DEBUG['.$_GET['debug'].']性能测试</a>';
   }
}
