<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="format-detection" content="telephone=no" />
	<title>确认订单</title>
	<link rel="stylesheet" type="text/css" href="/static/xiubao/new/css/reset.css">
	<link rel="stylesheet" type="text/css" href="/static/xiubao/new/css/cardtao.css">
	<link rel="stylesheet" type="text/css" href="/static/xiubao/new/css/weui.min.css">
	<script src="/static/xiubao/new/common/js/jquery-2.2.3.min.js"></script>
	<script src="/static/xiubao/new/js/cardtao.js"></script>
	<script src="/static/xiubao/common/js/showboom-url.js" type="text/javascript"></script>
	<script src="/static/xiubao/common/js/showboom-cookie.js" type="text/javascript"></script>
</head>
<body>
<!-- <div id="header">
	确认订单
</div> -->
<div class="package_choose clear">
	<span class="choose active fl">流量包</span>
	<span class="choose fl">套餐</span>
	<div class="line"></div>
</div>
<div id="content">
	<input type="hidden" name="times" id="times" value="{$times}">
	<div class="flow fl">
		{loop $data $k $v}
			{if $k != 'package'}
			<h3 class="list_type">{$v['name']}</h3>
				{loop $v['value'] $i $n}
					<div package="{$n['package_id']}" class="list clear">
						<img class="list_img fl" src="{$n['pic']}">
						<ul class="">
							<li class="list_tip">{if $n['title']}{$n['title']}{else}暂无标题{/if}</li>
							<li class="price">￥<span class="current_price">{$n['current_price']}</span><em class="old_price">{$n['original_price']}</em></li>
						</ul>
					</div>
				{/loop}
			{/if}
		{/loop}
	</div>
	<div class="package fl">
		{loop $data $k $v}
			{if $k =='package'}
			<h3 class="list_type">{$v['name']}</h3>
		{loop $v['value'] $i $n}
			<div package="{$n['package_id']}"  class="list clear">
				<img class="list_img fl" src="{$n['pic']}">
				<ul class="">
					<li class="list_tip">{if $n['title']}{$n['title']}{else}暂无标题{/if}</li>
					<li class="price">￥<span class="current_price">{$n['current_price']}</span><em class="old_price">{$n['original_price']}</em></li>
				</ul>
			</div>
		{/loop}
			{/if}
		{/loop}
	</div>
</div>
<div id="loadingToast" class="weui_loading_toast" style="display: none;z-index:999;position: absolute">
    <div class="weui_mask_transparent"></div>
    <div class="weui_toast">
      <div class="weui_loading">
        <div class="weui_loading_leaf weui_loading_leaf_0"></div>
        <div class="weui_loading_leaf weui_loading_leaf_1"></div>
        <div class="weui_loading_leaf weui_loading_leaf_2"></div>
        <div class="weui_loading_leaf weui_loading_leaf_3"></div>
        <div class="weui_loading_leaf weui_loading_leaf_4"></div>
        <div class="weui_loading_leaf weui_loading_leaf_5"></div>
        <div class="weui_loading_leaf weui_loading_leaf_6"></div>
        <div class="weui_loading_leaf weui_loading_leaf_7"></div>
        <div class="weui_loading_leaf weui_loading_leaf_8"></div>
        <div class="weui_loading_leaf weui_loading_leaf_9"></div>
        <div class="weui_loading_leaf weui_loading_leaf_10"></div>
        <div class="weui_loading_leaf weui_loading_leaf_11"></div>
      </div>
      <p class="weui_toast_content">正在通信</p>
    </div>
  </div>
</body>
</html>