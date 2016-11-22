$(function(){
	var stoken=getQueryString('stoken')||getCookie('stoken'),iccid=getQueryString('iccid')||getCookie('iccid'),imei=getQueryString('imei')||getCookie('imei'); 
    if(iccid&&iccid.length==20){$('.iccid').text(''+iccid+'')}else{$('.iccid').text('----')};
    $('.pay').attr('href','package?stoken='+stoken+'&iccid='+iccid+'&imei='+imei+'');
    $('.record').attr('href','record?stoken='+stoken+'&iccid='+iccid+'&imei='+imei+'');
    //获取卡信息
	$.ajax({
            type:"POST",
            url:"https://s.showboom.cn/flow/find/index",
            dataType:'json',
            async:false,
            data:{stoken:stoken,iccid:iccid,imei:imei},
            success:function(res){
                      if(res.data.left_flow){$('.flow').text(res.data.left_flow);}else{$('.flow').text('获取失败');}
                      if(res.data.query_time){$('.end_time').text('截止:'+res.data.query_time);}else{$('.end_time').text('截止:-- -- --');}
                      if(res.data.card_nick_name){$('.note').text(res.data.card_nick_name);}else{$('.note').text('-- --');}
            },
            error:function(res){
                      $('.flow').text('获取失败');
                      $('.end_time').text('截止:-- -- --');
                      $('.note').text('-- --');
            }

        })
   //跳转修改昵称页面
  $('.content').click(function(){
    var note=$('.note').text();
    if(note=='-- --'){note='';}
    window.location='/static/xiubao/html/note.html?iccid='+iccid+'&imei='+imei+'&stoken='+stoken+'&note='+note;
  })
  //获取卡套餐
  $.ajax({
            type:"POST",
            url:"https://s.showboom.cn/flow/find/userpackage",
            dataType:'json',
            async:false,
            data:{stoken:stoken,iccid:iccid,imei:imei},
            success:function(res){
                      var num=0;
                      var str=''
                     
                      for(var i in res.data){
                        num++
                        //1是未生效套餐2是已生效套餐
                        if(res.data[i].status==1){
                          str+='<div class="package_order clear" style="background: #ffffff url(/static/xiubao/new/img/Trafficq_daisx.png) no-repeat;background-size: 36px;background-position: 96% 18px;">\
                               <img class="package_img fl" src="'+res.data[i].pack_pic+'">\
                               <ul class="package_tips"><li class="package_title">'+res.data[i].pack_name+'</li><li class="package_time">有效期 --  --  --</li></ul>\
                              </div>'
                        }else{
                           str+='<div class="package_order clear" style="background: #ffffff url(/static/xiubao/new/img/Trafficq_ysx.png) no-repeat;background-size: 36px;background-position: 96% 18px;">\
                               <img class="package_img fl" src="'+res.data[i].pack_pic+'">\
                               <ul class="package_tips"><li class="package_title">'+res.data[i].pack_name+'</li><li class="package_time">有效期：'+res.data[i].expire_time.substring(0,10)+'</li></ul>\
                              </div>'
                        }
                      } 
                     if(num==0){
                        $('.tip').hide();
                      }
                    $('#package').html(str);
                     
            },
            error:function(res){
                      
            }


        })

})