<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="format-detection" content="telephone=no" />
	<title>支付订单</title>
	<link rel="stylesheet" type="text/css" href="/static/xiubao/new/css/reset.css">
	<link rel="stylesheet" type="text/css" href="/static/xiubao/new/css/com.css">
	<link rel="stylesheet" type="text/css" href="/static/xiubao/new/css/order.css">
	<script src="/static/xiubao/new/js/jquery-2.2.3.min.js"></script>
	<script src="/static/xiubao/new/js/cash.js" type="text/javascript"></script>
	<script src="/static/xiubao/common/js/showboom-url.js" type="text/javascript"></script>
	<script src="/static/xiubao/common/js/showboom-cookie.js" type="text/javascript"></script>
	<script src="/static/xiubao/common/js/lrz.bundle.js" type="text/javascript"></script>
</head>
<body>
	<!-- <div id="header">支付订单</div> -->
	<div id="main">
		<h3>订单号:<span class="order_num">{$order['order_id']}</span></h3>
		<input type="hidden" id="order_ids" value="{$order['order_id']}">

	    <div id="container" class="clear container"> 
			<img class="list_img fl" src="{$pic}">
			<ul class="fl">
				<li class="list_tip">{$order['pack_name']}</li>
				<li class="price">￥<span class="current_price">{$order['payable_price']}</span></li>
			</ul>
	     </div>
	     <div class="scan_num">
		     <img style="height:200px;width:200px;" class="scan" src="{$url}">
			 
		     <div class="description">使用手机微信扫一扫支付</div>
	     </div>
	</div>
	<div id="bg"></div>
	<div id="pay">
		<p><span>商品总额</span><strong class="fr">￥{$order['order_price']}</strong></p>
		<p><span>积分抵扣</span><strong class="fr">-￥{$order['discount_price']}</strong></p>
	</div>
	<div id="sum"><div><p>实付:<span>￥{$order['payable_price']}</span></p></div></div>
</body>
</html>
<script>
	var time=setInterval(function () {
		var order_id=$('#order_ids').val();
		var stoken=getQueryString('stoken'),iccid=getQueryString('iccid'),imei=getQueryString('imei');
		$.ajax({
			type:'POST',
			url:'/flow/find/orderStatus',
			dataType:'json',
			data:{
				order_id:order_id
			},
			success:function (res) {
				if(res.data.status==2){
					window.location.href="/flow/find/receiveSucc?payCustom="+order_id + "&res=success&stoken="+stoken+"&iccid="+iccid+"&imei="+imei+"";
				}
			}
		})
	},1000)

</script>