<?php

/**
 * Created by PhpStorm.
 * User: sa
 * Date: 2016/5/26
 * Time: 9:38
 */
class ScoreLogsModel extends TZ_Db_Table
{
    public function __construct()
    {
        parent::__construct(Yaf_Registry::get('xiubao_user_center_db'), 'xiubao_user_center_db.user_score_logs');
    }

}