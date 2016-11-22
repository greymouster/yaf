<?php

/**
 * Curl Serivce Class
 *
 * @author 刑天<wangtongmeng@747.cn>
 * @version 1.0
 * @date 2015-1-10
 */
class CurlToolService
{

    //等待时长，单位秒
    static public $timeout = 10;

    /**
     * send request
     *
     * @param  $url            需要访问的URL
     * @param  $type            传输类型，get或post
     * @param  $args            参数
     * @param  $needcharset    需要转换成的字符编码
     * @param  $charset        数据本身编码
     *
     * @Returns
     */
    public function sendcurl($url, $type, $args = array(), $charset = 'utf-8', $needcharset = 'utf-8', $delimiter = '?', $ssl = false)
    {
        if (!is_array($args)) {
            throw new Exception('传人参数必须为数组');
        }
        if ($charset == 'gbk' || $needcharset == 'gbk') {
            foreach ($args as &$val) {
                $val = mb_convert_encoding($val, $needcharset, $charset);
            }
        }
        if ($type == 'post') {
            $returnValue = $this->_post($url, $args, $charset, $ssl);
        } else {
            $url .= $delimiter . http_build_query($args);
            $returnValue = $this->_get($url, $charset, $ssl);
        }
        return $returnValue;
    }

    private function _post($url, $arguments, $charset = 'utf-8', $ssl = false)
    {
        if (is_array($arguments)) {
            $postData = http_build_query($arguments);
        } else {
            $postData = $arguments;
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_DNS_USE_GLOBAL_CACHE, false);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_TIMEOUT, self::$timeout);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, self::$timeout);
        if ($ssl === true) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        } 

        $returnValue = curl_exec($ch);
        curl_close($ch);
        if ($charset != 'utf-8') {
            $returnValue = iconv($charset, $charset, $returnValue);
        }
        return $returnValue;
    }

    private function _get($url, $charset = 'utf-8', $ssl = false)
    {
        #-----------------------------------------------------
        // TZ_Loader::service("User","Package")->_addlogs('token', "REAL URL : " .  $url);
        #-----------------------------------------------------
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, self::$timeout);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, self::$timeout);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
        if ($ssl === true) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        }
        $returnValue = curl_exec($ch);
        #---------------------------
        // TZ_Loader::service("User","Package")->_addlogs('token', "CURL RESULT : " .  $returnValue);
        #---------------------------
        curl_close($ch);
        if ($charset != 'utf-8') {
            $returnValue = iconv($charset, $charset, $returnValue);
        }
        return $returnValue;
    }

}
