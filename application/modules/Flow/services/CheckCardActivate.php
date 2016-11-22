<?php
/**
 * 验证卡激活状态
 * @author jialuo <wangkan@heimilink.com>
 * @Time 2016-09-23
 */
class CheckCardActivateService{
    /**
     * 验证是否已激活
     */
    public function checkActivate($params){
        $iccid=$params['iccid'];
       return TZ_Loader::model('DeviceCard', 'Common')->select(['iccid:eq'=>$iccid],'*','ROW');

    }
    /**
     * 查看用户的实名状态
     */
    public function checkUserIden($uid){
        return TZ_Loader::model('UserBase','Common')->select(['uid:eq'=>$uid]);
    }
}