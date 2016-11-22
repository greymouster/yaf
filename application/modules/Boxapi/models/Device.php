<?php

/**
 * Class DeviceModel
 */
class DeviceModel extends TZ_Db_Table
{
    public function __construct()
    {
        parent::__construct(Yaf_Registry::get('xiubao_device_center_db'), 'xiubao_device_center_db.device_info');
    }
}
?>