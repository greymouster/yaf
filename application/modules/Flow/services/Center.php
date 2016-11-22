<?php
/**
 * 调用消息中心
 *
 * @author ziyang<hexiangcheng@showboom.cn>
 * date 2016-06-07 高考日
 */


/**
 * 把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
 * @param $para 需要拼接的数组
 * return 拼接完成以后的字符串
 */
class CenterService
{

    protected $partnerid = '22222';

    protected $hmkey = 'e9c154ee06eef752a24c6b657d50f747';

    public function get($params)
    {

        $url = Yaf_Registry::get('config')->push->url;

        //生成签名
        $params['partnerid'] = $this->partnerid;
        $sign = $this->_getSign($params);

        $params['sign'] = $sign;

        $res = TZ_RemoteTool::send($url, 'post', $params);

        return $res;
    }


    function createLinkstring($para)
    {
        $args = array();

        foreach ($para as $key => $val) {
            $args[] = $key . '=' . $val;

        }

        //去掉最后一个&字符
        $arg = implode('&', $args);

        //如果存在转义字符，那么去掉转义
        if (get_magic_quotes_gpc()) {
            $arg = stripslashes($arg);
        }
        return $arg;
    }


    /**
     * 把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串，并对字符串做urlencode编码
     * @param $para 需要拼接的数组
     * return 拼接完成以后的字符串
     */
    function createLinkstringUrlencode($para)
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
     * return 去掉签名参数后的新签名参数组
     */
    function paraFilter($para)
    {
        $para_filter = array();

        foreach ($para as $key => $val) {

            if ($key == "sign") {
                continue;
            }
            // 如果是复杂类型,转为json字串
            if (is_array($val)) {
                // 把数据转为字符串类型
                /*  array_walk_recursive($val, 'TZ_Sign::intToString');
                  $para_filter[$key] = json_encode($val);*/

                $str = implode(',', $val);
                $para_filter[$key] = md5($str);
            } else {
                $para_filter[$key] = strval($para[$key]);
            }
        }

        return $para_filter;
    }

    /**
     * 对数组排序
     * @param $para 排序前的数组
     * return 排序后的数组
     */
    function argSort($para)
    {
        ksort($para);
        reset($para);
        return $para;
    }

    /**
     * 把数字转为字符串
     * @param $value
     * @param $key
     */
    static function intToString(&$value, $key)
    {
        $value = strval($value);
    }


    //生成签名
    private function _getSign($para_temp)
    {
        //待请求参数数组
        $para = $this->paraFilter($para_temp);

        //对待签名参数数组排序
        $para_sort = $this->argSort($para);

        //把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
        $prestr = $this->createLinkstring($para_sort);

        $prestr = $this->hmkey . $prestr . $this->hmkey;


        // echo $prestr;
        // echo '<br/>';

        $sign = sha1($prestr);

        // echo $sign;die;

        return $sign;
    }


}



