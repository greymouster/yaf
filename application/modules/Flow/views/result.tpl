<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="format-detection" content="telephone=no" />
	<title>支付结果</title>
	<link rel="stylesheet" type="text/css" href="/static/xiubao/new/css/reset.css">
	<link rel="stylesheet" type="text/css" href="/static/xiubao/new/css/com.css">
	<link rel="stylesheet" type="text/css" href="/static/xiubao/new/css/result.css">
	<script src="/static/xiubao/new/js/jquery-2.2.3.min.js"></script>
	<script src="/static/xiubao/common/js/showboom-url.js" type="text/javascript"></script>
	<script src="/static/xiubao/common/js/showboom-cookie.js" type="text/javascript"></script>
</head>
<body>
	<!-- <div id="header">支付结果</div> -->
	<div id="main">
		<div id="container" class="clear container"> 
			<img class="list_img fl" src="/static/xiubao/new/img/suc.png">
			<ul class="fl">
				<li class="list_tip">支付成功</li>
				<li class="price">预计十分钟内到账，高峰期会有延迟。</li>
			</ul>
	     </div>
	     <div class="line-c"><div class="line"><div class="ball_l"></div><div class="ball_r"></div></div></div>
	     <div class="detail">
	     	<p>充值卡号<span class="fr">{$code}</span></p>
	     	<p>流量<span class="fr">{$order['pack_flow']}</span></p>
	     </div>
	     <div id="sum"><div><p>实付:<span>￥{$order['payable_price']}</span></p></div></div>
		<input type="hidden" id="order_iccid" value="{$order['iccid']}">
		<input type="hidden" id="order_imei" value="{$order['imei']}">
	</div>
	<p class="tip">提示:每卡每月仅限充值10次，您本月还可充值<span class="number">{$times}</span>次</p>
</body>
</html>
<script type="text/javascript">
	var num =0;
	var stoken=getQueryString('stoken'),iccid=getQueryString('iccid')||$("#order_iccid").val(),imei=getQueryString('imei')||$("#order_imei").val();
	var time=setInterval(function(){
		if(num==3){
			clearInterval(time);
			num=0;
			window.location='record?stoken='+stoken+'&iccid='+iccid+'&imei='+imei+''
		}

		num++
	},1000)
</script>
