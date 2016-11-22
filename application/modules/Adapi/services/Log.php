<?php

/**
 * log services
 * @author octopus <zhangguipo@747.cn>
 * @final 2013-7-24
 */
class LogService
{
    /**
     * 插入广告访问日志
     */
    public function addClickLog($condition)
    {
        $condition['create_at'] = date('Y-m-d H:i:s');
        return TZ_Loader::model('ClickLog', 'Adapi')->insert($condition);
    }

}
		










