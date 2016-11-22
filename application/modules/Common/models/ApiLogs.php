<?php

/**
 * Created by PhpStorm.
 * User: sa
 * Date: 2016/5/26
 * Time: 9:38
 */
class ApiLogsModel extends TZ_Db_Table
{
    public function __construct()
    {
        parent::__construct(Yaf_Registry::get('xiubao_flow_center_db'), 'xiubao_flow_center_db.api_logs');
    }

}