<?php

/**
 * ad_objects model
 * @author octopus <zhangguipo@747.cn>
 * @final 2016-01-04
 */
class TagModel extends TZ_Db_Table
{
    public function __construct()
    {
        parent::__construct(Yaf_Registry::get('xiubao_ad_db'), 'xiubao_ad_db.ad_tags');
    }

    //查询广告列表
    public function getAdList($tag, $aid, $limit, $size)
    {
        $sql = "select distinct  a.id as ad_id ,a.name,a.content,t.tag_code,a.pic,a.star,a.is_free,a.url as url  ";
        $sql .= " from ad_tags t,ad_object_tag m,ad_objects a";
        $sql .= " where t.id=m.tag_id and a.id=m.ad_id and t.app_id='{$aid}' and t.tag_code='{$tag}' and a.status=1 and m.status=1 and t.status=1 order by `sort` desc limit {$limit},{$size}";
        return $this->_db->query($sql)->fetchAll();
    }

}
