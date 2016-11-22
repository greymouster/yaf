<?php
/**
 *布局模型
 * @author ziyang <hexiangcheng@showboom.cn>
 * @final 2016-09-18
 */
class LayoutModel extends TZ_Db_Table
{

    //init
    public function __construct()
    {
        parent::__construct(Yaf_Registry::get('xiubao_device_center_db'), 'xiubao_device_center_db.cf_layout');
    }
}
