<?php

/**
 * pay service file
 *
 * @author  子龙 <songyang@showboom.cn>
 * @final 2015-11-24
 */
class PayService {
	/* * *
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
	 * *
	 * */
	//1qazxsw2#EDCVFR$ 1003
	const ALI_PAY_KEY = '_kd81QK@04k.s234&k-6';

	public function pay($params, $payType = 'ali',$source=1005,$isApp=0) {
		$t='pay';
		$this->wirteLog('-------------开始调用支付平台------------------', $t);
		$datas = array(
		    'isApp' => $isApp,
            'sysCode' => $source,
            'payType' => $payType,
            'orderId' => $params['oid'],
            'pname' => trim($params['pname']),
            'price' => sprintf("%.2f", $params['price']),
            'payCustom' => $params['payCustom'],
		);
		$this->wirteLog($datas, $t);
		// 处理签名
		$datas['sign'] = $this->getSign($datas);
		// 获取支付中心地址
		$config = Yaf_Registry::get('config');
		$url = $config->user->pay->url;
		$this->wirteLog($url, $t);
		$this->wirteLog($datas, $t);
		//调用系统
		
		$result = TZ_Loader::service('CurlTool', 'Base')->sendcurl($url, 'post', $datas);
		
		$this->wirteLog($result, $t);
		$data = json_decode($result, true);
		$this->wirteLog('-------------结束调用支付平台------------------', $t);
		if (isset($data['code']) && $data['code'] == 0) {
			return $data['data'];
		}
		TZ_Response::error('500', '支付异常，请重新支付!');
	}

	/**
	 * 生成签名
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
		$key=self::getKey($params['sysCode']);
		// 生成签证串
		$paramStr = sha1($key . $paramStr . $key);
		return $paramStr;
	}

	/**
	 * 验证签名
	 */
	public static function checkSign($params) {
		$culSign = $params['sign'];
		$datas = $params;
		unset($datas['sign']);
		if (isset($datas['server'])) {
			unset($datas['server']);
		}
		$serSign = self::getSign($datas);
		if ($serSign !== $culSign) {
			return false;
		} else {
			return true;
		}
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
	//    public function checkSign($order_id, $price, $status, $key) {
	//        $sysKey = "e10adc3949ba59abbe56e057f20f883e";
	//        if (strtoupper($status) != 'SUCCESS') {
	//            // file_put_contents('/data/log/a.txt', "status:failed"."\n", FILE_APPEND);
	//            die('Filed Request.');
	//        }
	//        $sec = hash('sha256', $order_id . $price . $sysKey . $status);
	//        // file_put_contents('/data/log/a.txt', $sec."\n", FILE_APPEND);
	//        if ($key == $sec) {
	//            // file_put_contents('/data/log/a.txt', "result:success"."\n", FILE_APPEND);
	//            return true;
	//        } else {
	//            // file_put_contents('/data/log/a.txt', "result:failed"."\n", FILE_APPEND);
	//            die('Result Failed.');
	//        }
	//    }
	public static function getKey($type){
		$key='';
		if($type=='1002'){
			$key='_kd81QK@04k.s234&k-6';
		}elseif($type=='1003'){
			$key='1qazxsw2#EDCVFR$';
        }elseif($type=='1004'){
			$key='zxcvbnm<>LKJHGFDSA';
        }elseif($type=='1005'){
			$key='1qazxsw212qwaszx';
        }
        
        return $key;
	}
	//写入日志
	public function wirteLog($msg, $t)
	{
		$file = APP_PATH . "/logs/pay/" . $t . "/" . date("Ymd") . ".log";
		if (!is_dir(APP_PATH . "/logs/pay/" . $t)) {
			mkdir(APP_PATH . "/logs/pay/" . $t, 0777);
		} else {
			chmod(APP_PATH . "/logs/auto/" . $t, 0777);
			if (file_exists($file)) {
				chmod($file, 0777);
			}
		}
		file_put_contents($file, date('Y-m-d H:i:s') . " : " . json_encode($msg, JSON_UNESCAPED_UNICODE) . "\r\n", FILE_APPEND);
	}
}
