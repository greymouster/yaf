<?php

/**
 * m2ml0086 service file
 *
 * @author  子龙 <songyang@747.cn>
 * @final 2015-3-24
 */
class SignService {

    // 加密密钥
    const SER_KEY = 'e42dd67124831e0abf8f1b4a9d7e0014d1f05f9c';


    /**
     * 验证数字签名
     * @param $params
     * @return bool
     */
    public static function checkSign($params) {
        if (empty($params['sign'])) {
            return false;
        }
        $culSign = $params['sign'];
        $datas = $params;
        unset($datas['sign']);
        $serSign = self::getSign($datas);
        if ($serSign !== $culSign) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * 生成签名
     * @param $params
     * @return bool|string
     */
    public static function getSign($params) {
        if (empty($params)) {
            return false;
        }
        $datas = $params;
        $paramStr = '';
        ksort($datas);
        reset($datas);
        foreach ($datas as $key => $val) {
            $paramStr .= ($key . $val);
        }
        // 生成签证串
        $paramStr = sha1(self::SER_KEY . $paramStr . self::SER_KEY);
        return $paramStr;
    }
}
