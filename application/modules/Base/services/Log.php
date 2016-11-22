<?php

/**
 * author: mengqi<zhangxuan@showboom.cn>
 * Time: 2016/6/30 16:36
 * log service
 */
class LogService
{
    /**
     * @param $data 需要记录的数据
     * @param $request  当前请求对象 $this or $this->getRequest()
     */
    public function writeLog($data, $request)
    {
        if (!count((array)$request)) {
            $controller = $action = '';
            $module = get_class($request);
        } else {
            $module = $request->module;
            $controller = $request->controller;
            $action = $request->action;
        }

        $path = Yaf_Registry::get('config')->logs->path;
        $dir_path = $path . '/' . $module . '/' . $controller . '/' . $action . '/';
        if (!is_dir($dir_path)) {
            mkdir($dir_path, 0777, true);
        }
        $filepath = $dir_path . date("Ymd") . '.log';
        $delimiter_start = "\n+---------------------------- log start -----------------------------+\n记录时间:" . date("Y-m-d H:i:s") . "\n";
        $delimiter_end = "\n+---------------------------- log end -----------------------------+\n";
        file_put_contents($filepath, $delimiter_start . json_encode($data, JSON_UNESCAPED_UNICODE) . $delimiter_end, FILE_APPEND);
    }
}