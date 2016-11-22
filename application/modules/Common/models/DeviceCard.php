<?php

/**
 * 获取用户信息
 * @author jialuo <wangkan@heimilink.com>
 * @Time  2016-09-23
 */
class DeviceCardModel extends TZ_Db_Table
{
       public function __construct()
    {
        parent::__construct( Yaf_Registry::get('xiubao_device_center_db'), 'xiubao_device_center_db.device_card');
    }

}