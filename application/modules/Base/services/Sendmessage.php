<?php

/**
 * sendmessage service file
 *
 * @author  刑天<wangtongmeng@747.cn>
 * @final 2015-01-09
 */
class SendmessageService {
    /*
     * 与短信模板对应关系
     * 1	发送验证码
     * 2	发送test消息
     */

    private $arrTemplate = array(
        '1' => array('uuid' => '0001', 'id' => '01'),
        '2' => array('uuid' => '0002', 'id' => '02'),
        '3' => array('uuid' => '0003', 'id' => '03'),
        '4' => array('uuid' => '0004', 'id' => '04'),
        '5' => array('uuid' => '0005', 'id' => '05'),
        '6' => array('uuid' => '0006', 'id' => '06'),
        '7' => array('uuid' => '0007', 'id' => '07'),
        '8' => array('uuid' => '0008', 'id' => '08'),
        '9' => array('uuid' => '0009', 'id' => '09'),
        '10' => array('uuid' => '0010', 'id' => '10'),
        '11' => array('uuid' => '0011', 'id' => '11'),
        '12' => array('uuid' => '0012', 'id' => '12'),
        '13' => array('uuid' => '0013', 'id' => '13'),
        '14' => array('uuid' => '0014', 'id' => '14'),
        '15' => array('uuid' => '0015', 'id' => '15')
    );

    /*
     * 发送短信
     * tId		短信模板对应关系ID
     * tel		要发送的手机号
     * params	要发送的内容
     * type		发送方式，get或post
     * delimiter域名和参数之间的分割方式，兼容不同的框架路由形式
     * needcharset	数据需要转换成的编码
     * charset		原数据编码
     *
     * return max
     */

    public function send($tId, $tel, $params, $type = 'post', $delimiter = '?', $charset = 'utf-8', $needcharset = 'utf-8') {
        $config = Yaf_Registry::get('config');
        $smsHost = $config->sms->host;
        if (array_key_exists($tId, $this->arrTemplate)) {
            $params['phone'] = $tel;
            //$params['checkCode']	= '1234';
            $params['uuid'] = $this->arrTemplate[$tId]['uuid'];
            $params['id'] = $this->arrTemplate[$tId]['id'];
            $result = TZ_Loader::service('CurlTool', 'Didi')->sendcurl($smsHost, $type, $params, $charset, $needcharset, $delimiter);
            return $result;
        } else {
            $error = array('result' => 'Not find template ID', 'msg' => 'fail');
            return json_encode($error);
        }
    }

}
