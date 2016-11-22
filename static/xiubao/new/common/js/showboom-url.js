// 下面的JS主要是用来获取当前页面的URL，并提取出cid拼接到所有的URL中去，方便做统计使用
function getQueryString(name) {
    var reg = new RegExp('(^|&)' + name + '=([^&]*)(&|$)', 'i');
    var r = window.location.search.substr(1).match(reg);
    if (r != null) {
        return decodeURIComponent(r[2]);
    }
    return null;
}

function openURL(url) {
    window.open(url, '_self');
    return false;
}

function openLoginWithReferer(url) {
    var tURL = url + '';
    var splits = tURL.split('/');
    var file = splits[splits.length - 1];
    var fileSplits = file.split('?');
    var fileName = fileSplits[0];
    var state = fileName.replace(/\.html/, '');

    var userAgent = window.navigator.userAgent;
    var loginURL = '';
    if (userAgent.indexOf('MicroMessenger') != -1) {
      loginURL = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='+ HMWXAppID +'&redirect_uri='+ HMBaseURL +'/flow/api/userlogin&response_type=code&scope=snsapi_userinfo&state='+ state +'#wechat_redirect'
    }
    else {
    }
    window.open(loginURL, '_self');
}
