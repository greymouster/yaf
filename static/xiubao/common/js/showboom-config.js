var HMBaseURL = window.location.origin;

var HMUserURL = 'http://u.heimilink.com';
var HMWXAppID = 'wxf3541fdf71239934';

// 测试平台
if (HMBaseURL.indexOf('.test.') !== -1) {
  HMUserURL = 'http://u.test.heimilink.com';
  HMWXAppID = 'wxb0d4971ee949bf5c';
}
