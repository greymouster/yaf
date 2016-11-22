<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="format-detection" content="telephone=no" />
	<title>充值记录</title>
	<link rel="stylesheet" type="text/css" href="/static/xiubao/new/css/reset.css">
	<link rel="stylesheet" type="text/css" href="/static/xiubao/new/css/com.css">
	<link rel="stylesheet" type="text/css" href="/static/xiubao/new/css/record.css">
	<script src="/static/xiubao/new/js/jquery-2.2.3.min.js"></script>
	<script src="/static/xiubao/common/js/showboom-url.js" type="text/javascript"></script>
	<script src="/static/xiubao/common/js/showboom-cookie.js" type="text/javascript"></script>
</head>
<body>
<input type="hidden" id="times" name="times" value="{$times}">
	<!-- <div id="header">充值记录</div> -->
	{loop $list $k $v}
	<div class="order" oid="{if $v['status']==1}order_id={$v['order_id']}{else}package_id={$v['pack_code']}{/if}" >
		<h3 class="num">订单号:{$v['order_id']}<span class="status {if $v['status']==1}active{/if} fr">{if $v['status']==1}待支付{elseif $v['status']==2}已支付{elseif $v['status']==3}已完成{/if}</span></h3>
		<div class="tip">
			<p class="title">{$v['order_name']}<span class="total fr">￥{$v['payable_price']}</span></p>
			<P class="card">充值卡号:{$v['iccid']}</P>
		</div>
		<div class="time">下单时间:{$v['created_at']}{if $v['status']==1}<a href="javascript:;" class="submit fr">去支付</a>{/if}</div>
	</div>
	{/loop}
</body>
</html>

<script type="text/javascript">
	$('body').on('click','.submit',function(){
		var stoken=getQueryString('stoken'),iccid=getQueryString('iccid'),imei=getQueryString('imei');
		var oid=$(this).parent().parent().attr('oid');
		//window.location='cashier?stoken='+stoken+'&iccid='+iccid+'&imei='+imei+'&'+oid+''
		if($('#times').val()<5){
			window.location='cashier?stoken='+stoken+'&iccid='+iccid+'&imei='+imei+'&'+oid+'';
		}else{
			alert("充值次数已达上限！");
			return false;
		}

	})
</script>
