<?php

/**
 * author: mengqi<zhangxuan@heimilink.com>.
 * Time: 2016/9/7 15:59
 * Info:
 */
class VirtualMerchantService
{

    public function index()
    {
        $url = Yaf_Registry::get('config')->virtual->url;
        $_POST['partner'] = Yaf_Registry::get('config')->virstual->partner;
        //生成签名
        $sign = $this->_getSign($_POST);

        foreach ($_POST as $k => $v) {
            $_POST[$k] = urlencode($v);
        }
        $_POST['sign'] = $sign;

        $res = CurlTool::sendcurl($url, 'post', $_POST);
        echo $res;
    }


    public function createLinkstring($para)
    {
        $arg = "";
        while (list ($key, $val) = each($para)) {
            $arg .= $key . "=" . $val . "&";
        }
        //去掉最后一个&字符
        $arg = substr($arg, 0, count($arg) - 2);

        //如果存在转义字符，那么去掉转义
        if (get_magic_quotes_gpc()) {
            $arg = stripslashes($arg);
        }
        return $arg;
    }


    /**
     * 把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串，并对字符串做urlencode编码
     * @param $para 需要拼接的数组
     * @return string 拼接完成以后的字符串
     */
    public function createLinkstringUrlencode($para)
    {
        $arg = "";
        while (list ($key, $val) = each($para)) {
            $arg .= $key . "=" . urlencode($val) . "&";
        }
        //去掉最后一个&字符
        $arg = substr($arg, 0, count($arg) - 2);

        //如果存在转义字符，那么去掉转义
        if (get_magic_quotes_gpc()) {
            $arg = stripslashes($arg);
        }

        return $arg;
    }

    /**
     * 除去数组中的空值和签名参数
     * @param $para 签名参数组
     * @return array 去掉签名参数后的新签名参数组
     */
    public function paraFilter($para)
    {
        $para_filter = array();
        while (list ($key, $val) = each($para)) {
            if ($key == "sign")
                continue;
            else
                $para_filter[$key] = $para[$key];
        }
        return $para_filter;
    }

    /**
     * 对数组排序
     * @param $para 排序前的数组
     * @return mixed 排序后的数组
     */
    public function argSort($para)
    {
        ksort($para);
        reset($para);
        return $para;
    }


    /**
     * 生成签名
     * @param $para_temp
     * @return string
     */

    public function _getSign($para_temp)
    {
        $hmkey = Yaf_Registry::get('config')->virtual->hmkey;
        //待请求参数数组
        $para = $this->paraFilter($para_temp);

        //对待签名参数数组排序
        $para_sort = $this->argSort($para);

        //把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
        $prestr = $this->createLinkstring($para_sort);

        $prestr = $hmkey . $prestr . $hmkey;

        $sign = sha1($prestr);

        return $sign;
    }






    /**
     * --- 回调 ---
     * 验证签名
     * @return 验证结果
     */
    public function verifyNotify()
    {
        if (empty($_POST)) {//判断POST来的数组是否为空
            return false;
        } else {
            return $this->getSignVeryfy($_POST, $_POST["sign"]);
        }
    }


    /**
     * --- 回调 ---
     * 获取回调时的签名验证结果
     * @param $para_temp 通知返回来的参数数组
     * @param $sign 返回的签名结果
     * @return 签名验证结果
     */
    function getSignVeryfy($para_temp, $sign)
    {
        $hmkey = Yaf_Registry::get('config')->virtual->hmkey;

        //除去待签名参数数组中签名参数
        $para_filter = $this->callbackParaFilter($para_temp);

        //对待签名参数数组排序
        $para_sort = $this->argSort($para_filter);

        //把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
        $prestr = $this->createLinkstring($para_sort);

        $prestr = $hmkey . $prestr . $hmkey;

        $createSign = sha1($prestr);

        if ($sign === $createSign) {
            return true;
        }
        return false;
    }


    /**
     * --- 回调 ---
     * 除去数组中的空值和签名参数
     * @param $para 签名参数组
     * @return array 去掉签名参数后的新签名参数组
     */
    public function callbackParaFilter($para)
    {
        $para_filter = array();
        while (list ($key, $val) = each($para)) {
            if ($key == "sign")
                continue;
            else
                $para_filter[$key] = urldecode($para[$key]);
        }
        return $para_filter;
    }

}