<?php

/**
 * Error controller file
 *
 * @author vincent <piaoqingbin@maxvox.com.cn>
 * @final 2012-12-25
 */
class ErrorController extends Yaf_Controller_Abstract
{
    //异常捕获
    public function errorAction($exception)
    {
        $code = 500;
        $detail = $exception->getMessage();
        if (Yaf_Registry::get('config')->log->error)
            TZ_Log::set($code, $detail);
        $error = array(
            'code' => $code,
            'message' => $detail,
            'data' => array()
        );
        TZ_Request::send($error);
    }
}
