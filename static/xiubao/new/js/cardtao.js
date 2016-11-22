$(function(){
    var stoken=getQueryString('stoken'),iccid=getQueryString('iccid'),imei=getQueryString('imei');
	//切换流量包和套餐
	$('.choose').click(function(){
		var a_left=$(this).index()*-100;
		$('.choose').removeClass('active');
		$(this).addClass('active');
	    $('#content').stop().animate({left:''+a_left+'%'})
	    $('.line').stop().animate({left:''+$(this).index()*50+'%'})
	})
  //判断是否显示原始价格
  for(var i =0;i<$('.current_price').length;i++){
    if($('.current_price').eq(i).text()==$('.old_price').eq(i).text()){$('.old_price').eq(i).hide()}
  }
	// getpackage()
   /* function getpackage(){
    	$.ajax({
			type:'POST',
			url:'https://s.showboom.cn/flow/find/package',
            data:{stoken:'61dfb195486c2e20499629275c97c794',iccid:'11122233344455566130',imei:'11111'},
			dataType:'json',
			success:function(res){
                 console.log(res.data)
                 console.log(res.data.month['1']['0'])
                 var obj=createObj(res.data);
                 $('.flow').html(''+obj.flow+'');
                 $('.package').html(''+obj.package+'');

			},
			error:function(error){

			}
		})
    }*/
    function createObj(list){
           var obj={},mtr='<h3 class="list_type">月包</h3>',qtr='<h3 class="list_type">季包</h3>',ytr='<h3 class="list_type">年包</h3>',ptr='<h3 class="list_type">加油包</h3>';
           if(list.month['1']){mtr+=createStr(list.month['1'])}else{mtr=''};
           if(list.quarter['1']){qtr+=createStr(list.quarter['1'])}else{qtr=''};
           if(list.year['1']){ytr+=createStr(list.year['1'])}else{ytr=''};
           if(list.package['1']){ptr+=createStr(list.package['1'])}else{ptr=''};
           console.log(mtr)
           obj.flow=mtr+qtr+ytr;
           obj.package=ptr;
           return obj;
    }
    function createStr(arr){
      var d_list='';
      for(var i in arr){
        d_list+='<div package="'+arr[i].package_id+'" class="list clear">\
				<img class="list_img fl" src="'+arr[i].pic+'">\
				<ul class="fl">\
					<li class="list_tip">'+arr[i].title+'</li>\
					<li class="price">￥<span class="current_price">'+arr[i].current_price+'</span><em style="'+(arr[i].current_price+'=='+arr[i].original_price?'display:none':'display:block')+'" class="old_price">'+arr[i].original_price+'</em></li>\
				</ul>\
			</div>'
      }
      return d_list;
    }
    //跳转套餐支付页
    $('.flow').on('click','.list',function(){
      $('#loadingToast').show();
    	var package=$(this).attr('package');
    	window.location='cashier?package_id='+package+'&stoken='+stoken+'&imei='+imei+'&iccid='+iccid;
    })
    $('.package').on('click','.list',function(){
      $('#loadingToast').show();
      var package=$(this).attr('package');
        if($('#times').val()<5){
            window.location='cashier?package_id='+package+'&stoken='+stoken+'&imei='+imei+'&iccid='+iccid;
        }

    })



})