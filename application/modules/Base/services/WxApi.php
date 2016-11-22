<?php

/**
 * 公共页面服务
 * 
 * @author zilong <songyang@747.cn>
 * @final 2013-05-16
 */
class WxApiService {

    private $appId = 'wxb0d4971ee949bf5c';
    private $appSecret = '3f1e7e13e7e8b9352125498c4249f2fc';
    
    private $jsapi_ticket_prefix = 'jsapi_ticket';
    private $access_token_prefix = 'access_token';
    
    public function getSignPackage() {
        $jsapiTicket = $this->getJsApiTicket();
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $timestamp = time();
        $nonceStr = $this->createNonceStr();

        // 这里参数的顺序要按照 key 值 ASCII 码升序排序
        $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";

        $signature = sha1($string);

        $signPackage = array(
            "appId" => $this->appId,
            "nonceStr" => $nonceStr,
            "timestamp" => $timestamp,
            "url" => $url,
            "signature" => $signature,
            "rawString" => $string
        );
        return $signPackage;
    }

    private function createNonceStr($length = 16) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    private function getJsApiTicket() {
        // jsapi_ticket 应该全局存储与更新，以下代码以写入到文件中做示例
        $redis = TZ_Redis::connect("weixin");
        $strDatas = $redis->get($this->jsapi_ticket_prefix);
        if (empty($strDatas)) {
            $strDatas = '{"jsapi_ticket":"","expire_time":0}';
        }
        $data = json_decode($strDatas);
        if ($data->expire_time < time()) {
            $accessToken = $this->getAccessToken();
            $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=" . $accessToken;
            $res = json_decode($this->httpGet($url));
            $ticket = $res->ticket;
            if ($ticket) {
                $data->expire_time = time() + 7000;
                $data->jsapi_ticket = $ticket;
                $redis->set($this->jsapi_ticket_prefix, json_encode($data));
            }
        } else {
            $ticket = $data->jsapi_ticket;
        }
        $redis->close();
        return $ticket;
    }

    private function getAccessToken() {
        // access_token 应该全局存储与更新，以下代码以写入到文件中做示例
        $redis = TZ_Redis::connect("weixin");
        $strDatas = $redis->get($this->access_token_prefix);
        if (empty($strDatas)) {
            $strDatas = '{"access_token":"","expire_time":0}';
        }
        $data = json_decode($strDatas);
        if ($data->expire_time < time()) {
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . $this->appId . "&secret=" . $this->appSecret;
            $result = $this->httpGet($url);
            $res = json_decode($result);
            $access_token = $res->access_token;
            if ($access_token) {
                $data->expire_time = time() + 7000;
                $data->access_token = $access_token;
                $redis->set($this->access_token_prefix, json_encode($data));
            }
        } else {
            $access_token = $data->access_token;
        }
        $redis->close();
        return $access_token;
    }

    private function httpGet($url) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 500);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_URL, $url);
        $res = curl_exec($curl);
        curl_close($curl);

        return $res;
    }

}
