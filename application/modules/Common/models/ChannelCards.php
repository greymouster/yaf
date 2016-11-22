<?php

/**
 * Created by PhpStorm.
 * User: sa
 * Date: 2016/5/24
 * Time: 18:08
 */
class ChannelCardsModel extends TZ_Db_Table
{
    public function __construct()
    {
        parent::__construct( Yaf_Registry::get('xiubao_rebate_db'), 'xiubao_rebate_db.channel_cards' );
    }

}
