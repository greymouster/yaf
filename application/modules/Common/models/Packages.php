<?php

/**
 * author: mengqi<zhangxuan@heimilink.com>.
 * Time: 2016/9/9 11:53
 * Info:
 */
class PackagesModel extends TZ_Db_Table
{
    public function __construct()
    {
        parent::__construct( Yaf_Registry::get('xiubao_flow_center_db'), 'xiubao_flow_center_db.packages' );
    }

}