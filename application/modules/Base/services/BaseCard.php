<?php

/**
 * 查询卡相关信息
 * @author octopus <zhangguipo@747.cn>
 * @final 2016-04-05
 */
class BaseCardService
{

    //查询卡是否存在
    public function getCardInfo($iccid, $type)
    {
        $cardInfo = TZ_Loader::model("CardInfo", "Flow")->select(array('iccid:eq' => $iccid, 'type_id:eq' => $type), '*', 'ROW');
        if (count($cardInfo) == 0) {
            return false;
        }
        return $cardInfo;
    }

    /**
     * 获取卡类型
     * @param $id
     * @return string
     */
    public function getPackageType($id)
    {
        TZ_Loader::service("Log", "Base")->writeLog($id, $this);
        $title = '';
        switch ($id) {
            case 'package':
                $title = '月套餐';
                break;
            case 'year':
                $title = '年包';
                break;
            case 'half':
                $title = '半年包';
                break;
            case 'quarter':
                $title = '季度包';
                break;
            case 'month':
                $title = '月包';
                break;
            case 'other':
                $title = '其他';
            default:
                break;
        }
        TZ_Loader::service("Log", "Base")->writeLog($id, $this);
        return $title;
    }

}
