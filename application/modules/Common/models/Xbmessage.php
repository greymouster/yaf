<?php

/**
 * Created by PhpStorm.
 * User: sa
 * Date: 2016/5/24
 * Time: 18:08
 */
class XbmessageModel extends TZ_Db_Table
{
    public function __construct()
    {
        parent::__construct(Yaf_Registry::get('xiubao_msg_db'), 'xiubao_msg_db.xb_message');
    }

}