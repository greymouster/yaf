
<?php
class ErrorController extends Yaf_Controller_Abstract
{
	//异常捕获
	public function errorAction($exception)
	{	
         header("Content-type:application/json;charset=utf-8"); 
         $code = 500;
         $detail = $exception->getMessage();
         $error = array(
              'code' => $code, 
              'detail' => $detail
              );
         TZ_Request::send($error);
	}
}
