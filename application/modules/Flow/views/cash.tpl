<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="format-detection" content="telephone=no" />
	<title>确认订单</title>
	<link rel="stylesheet" type="text/css" href="/static/xiubao/new/css/reset.css">
	<link rel="stylesheet" type="text/css" href="/static/xiubao/new/css/com.css">
	<link rel="stylesheet" type="text/css" href="/static/xiubao/new/css/cash.css">
	<script src="/static/xiubao/new/js/jquery-2.2.3.min.js"></script>
	<script src="/static/xiubao/new/js/cash.js" type="text/javascript"></script>
    <script src="/static/xiubao/common/js/showboom-url.js" type="text/javascript"></script>
</head>
<body>
	<!-- <div id="header">确认订单</div> -->
	<div id="main">
		<div class="list clear">
			<div {if $result['type']=='order'}style="display:block;"{else}style="display:none;"{/if}class="order_id">订单号:<strong class="order">{if $result['type']=='order'}{$result['order_id']}{else}{/if}</strong><span class="status fr">待支付</span></div>
		    <div class="clear container">
				<img class="list_img fl" src="{$pic}">
				<ul class="">
					<li class="list_tip">{if $result['type']=='order'}{$result['order_name']}{else}{$result['pack_name']}{/if}</li>
					<li class="price">￥<span class="current_price">{if $result['type']=='order'}{$result['payable_price']}{else}{$result['pack_price']}{/if}</span></li>
				</ul>
			</div>
			{if $result['type']!='order' && $score!=0}
			<div class="detail_x"><div class="detail">可以用{if $result['type']=='order'}0{else}{$score}{/if}积分抵扣￥<span class="score_price">{if $result['type']=='order'}0{else}{$scorePrice}{/if}</span>元<span class="red_line"></span><span class="red_point"></span></div><div class="detail_z"></div></div>
			{/if}
	    </div>
	</div>
    {if $result['type']!='order' && $result['pack_type']=='package'}
	 <div class="start-time clear">
	    <p class="pay_list">生效方式</p>
	    <label for="radio_1"><span><em class="radio_box"><input checked="checked" class="pay_method" type="radio" id="radio_1" name="method" value="1"><label for="radio_1"></label></em>当月生效</span></label>
	  <!--  <label for="radio_2"><span><em class="radio_box"><input class="pay_method" type="radio" id="radio_2" name="method" value="2"><label for="radio_2"></label></em>次月生效</span></label>-->
	</div>
    {/if}
	<div id="pay">
		 <label><p class="rool">
		 	<img src="/static/xiubao/new/img/weixin_logo.png">微信支付<input checked="checked" class="type" type="radio" id="radio_3" name="pay" value="wx"><label for="radio_3"></label>
		 </p></label>
		 <div class="line"></div>
		  <label><p class="rool">
		 	<img src="/static/xiubao/new/img/zhifubao_logo.png">支付宝支付<input class="type" type="radio" id="radio_4" name="pay" value="ali"><label for="radio_4"></label>
		 </p></label>
	</div>
	<div id="total">
		合计：<span class="menu_price">￥{if $result['type']=='order'}{$result['payable_price']}{else}{$payPrice}{/if}</span><em class="submit">{if $result['type']=='order'}去支付{else}提交订单{/if}</em>
	</div>
	<p {if $result['type']=='order'}style="display:none;"{else}style="display:block;"{/if} class="tip">提示:每卡每月仅限充值10次，您本月还可充值<span class="number">{$times}</span>次</p>
</body>
</html>