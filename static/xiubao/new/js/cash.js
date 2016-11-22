$(function(){
    var need_score=1;
    var packageId=getQueryString('package_id');
    var stoken=getQueryString('stoken'),iccid=getQueryString('iccid'),imei=getQueryString('imei');
    if($('.detail_x').length==0){$('.detail_x').hide();need_score=2;}
    $('.detail_z').click(function(){

    	if($('.red_line').width()==0){
            need_score=1;
            $('.menu_price').text('￥'+($('.current_price').text()-$('.score_price').text())+'')
            $('.red_line').stop().animate({width:'21'})
    	    $('.red_point').stop().animate({right:'0px'})
    	}else{
            need_score=2;
            $('.menu_price').text('￥'+$('.current_price').text()+'')
    		$('.red_line').stop().animate({width:'0'})
    	    $('.red_point').stop().animate({right:'20px'})
    	}
    	
    })
   $('.submit').click(function(){

     
     var order_id=$('.order').text(),method=$('.pay_method[name="method"]:checked ').val(),pay_type=$('.type[name="pay"]:checked').val();
     var param='';

     order_id?param="payType=" + pay_type + "&stoken=" + stoken +"&iccid="+ iccid+"&imei="+imei+"&order_id="+ order_id : param = "payType=" + pay_type + "&stoken=" + stoken +"&iccid="+ iccid+"&imei="+imei+"&need_score="+need_score+'&package_id='+packageId;
     if(method){param+="&method_pay="+ method;}else{param+="&method_pay=1";}
     openURL('https://s.showboom.cn/flow/find/packagecheck?'+param+'')
   })






})