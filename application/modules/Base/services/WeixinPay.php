<?php

/**
 * pay service file
 *
 * @author  刑天 <wangtongmeng@747.cn>
 * @final 2015-3-24
 */
class WeixinPayService {
    /*     * *
     * 调用支付请求demo
     * 应用系统在调用第三方支付系统时（HTTP方式），需要提供以下参数
     * *
     * * platform : 支付平台名称（可为空，默认为支付宝快捷支付）
     * * bankCode : (各支付平台银行代码) 通过支付平台直连银行，缺省参数，暂未使用，传递0000即可 
     * * uid :  用户的UID， 如果获取不到用户信息，传递 nouser 即可
     * * orderNumber 应用系统订单号。
     * * subject  产品描述（可以为空,中文请使用urlencode 编码）
     * * price  支付金额 （不可为空，单位/元 [最多保留两位小数点]）
     * * sysCode  由支付系统系统分配的系统编码,不能为空
     * * sysKey  系统秘钥，各系统自行生成，系统密匙需要告知支付系统（最长64位）
     * * key 用户uid+订单号+价格+系统编码+加密密匙(字符串加密顺序要一致) 用SHA256方式加密后生成key传递给支付平台
     * * 支付平台将进行参数验证，验证成功后 将返回根据参数生成的支付地址
     * * 实例程序如下
     * * */

    public function pay($orderNumber, $subject, $price, $openId) {
        $url = 'http://activity.showboom.cn/pin/weixin/pay/callback';
        // 加截文件
        ini_set('date.timezone', 'Asia/Shanghai');
        $config = Yaf_Registry::get('config');
        $wxpath = $config->weixin->path;
        require_once $wxpath . "/lib/WxPay.Api.php";
        require_once $wxpath . "/unit/WxPay.JsApiPay.php";
        require_once $wxpath . "/unit/log.php";
        //获取用户openid
        $tools = new JsApiPay();
        
        $input = new WxPayUnifiedOrder();
        $input->SetBody($subject);
        $input->SetAttach($subject);
        $input->SetOut_trade_no($orderNumber);
        $input->SetTotal_fee($price);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        $input->SetGoods_tag($subject);
        $input->SetNotify_url($url);
        $input->SetTrade_type("JSAPI");
        $input->SetOpenid($openId);
        $order = WxPayApi::unifiedOrder($input);
        
        $jsApiParameters = $tools->GetJsApiParameters($order);
        return $jsApiParameters;
    }

    public function CallBack($data) {
        
    }

    /**
     * 检测接口
     *
     * $order_id	订单id
     * $price		价格
     * $status		订单状态
     *
     * return bool
     */
    public function checkSign($order_id, $price, $status, $key) {
        $sysKey = "e10adc3949ba59abbe56e057f20f883e";
        if (strtoupper($status) != 'SUCCESS') {
            // file_put_contents('/data/log/a.txt', "status:failed"."\n", FILE_APPEND);
            die('Filed Request.');
        }
        $sec = hash('sha256', $order_id . $price . $sysKey . $status);
        // file_put_contents('/data/log/a.txt', $sec."\n", FILE_APPEND);
        if ($key == $sec) {
            // file_put_contents('/data/log/a.txt', "result:success"."\n", FILE_APPEND);
            return true;
        } else {
            // file_put_contents('/data/log/a.txt', "result:failed"."\n", FILE_APPEND);
            die('Result Failed.');
        }
    }

}
