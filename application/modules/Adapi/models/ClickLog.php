<?php

/**
 * click_log model
 * @author octopus <zhangguipo@747.cn>
 * @final 2016-01-04
 */
class ClickLogModel extends TZ_Db_Table
{
    public function __construct()
    {
        parent::__construct(Yaf_Registry::get('xiubao_ad_db'), 'xiubao_ad_db.ad_click_logs');
    }

}
