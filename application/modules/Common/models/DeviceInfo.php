<?php

/**
 * Created by PhpStorm.
 * User: mengqi
 * Date: 2016/09/23
 * Time: 18:08
 */
class DeviceInfoModel extends TZ_Db_Table
{
    public function __construct()
    {
        parent::__construct(Yaf_Registry::get('xiubao_device_center_db'), 'xiubao_device_center_db.device_info');
    }

}