<?php

/**
 * Created by PhpStorm.
 * User: sa
 * Date: 2016/5/24
 * Time: 18:17
 */
class ScoreModel extends TZ_Db_Table
{
    /**
     * UserScore constructor.
     */
    public function __construct()
    {
        parent::__construct(Yaf_Registry::get('xiubao_user_center_db'), 'xiubao_user_center_db.user_score');
    }

}