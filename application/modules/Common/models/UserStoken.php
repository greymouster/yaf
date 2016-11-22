<?php

/**
 * 获取用户信息
 * @author jialuo <wangkan@heimilink.com>
 * @Time  2016-09-23
 */
class UserStokenModel extends TZ_Db_Table
{
    public function __construct()
    {
        parent::__construct( Yaf_Registry::get('xiubao_user_center_db'), 'xiubao_user_center_db.user_stoken_log');
    }

}