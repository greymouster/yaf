<?php

/**
 * 用户信息服务
 * @param $param
 * @author jialuo <wangkan@heimilink.com>
 * @Time 2016.9.9
 */
class UserInfoService
{
    /*
     * 判断用户是否登陆
     */
    public function getUserLogin($param)
    {
        $redis = TZ_Redis::connect('user');
        $sToken = 'stoken:' . $param['stoken'];
        $rtoken = $redis->get($sToken);
        if ($rtoken) {
            return json_decode($rtoken, true);
        } else {
            //获取stoken缓存表数据
            if (TZ_Loader::model('UserStoken', 'Common')->select(['stoken:eq' => $param['stoken']], 'uid', 'ROW')) {
                //获取用户信息
                return json_decode($this->getUserInfo($param), true)['data'];
            } else {
                return false;
            }
        }
    }

    /*
     * 获取用户信息
     */
    public function getUserInfo($params)
    {
        $url = Yaf_Registry::get('config')->user_center->userinfo_url;
        $params['service'] = 'getUserInfo';
        $result = TZ_Loader::service('CurlTool', 'Base')->sendcurl($url, 'post', $params);
        return $result;

        //print_r($result);exit;
    }
}