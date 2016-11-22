<?php

/**
 * author: mengqi<zhangxuan@heimilink.com>.
 * Time: 2016/9/8 10:03
 * Info:
 */
class CardListService
{
    const CARD_TYPE = 'HM_NIU_DL01';

    /**
     * 获取卡列表
     * @param $params
     * @return mixed
     */
    public function getCardList($params = array())
    {
        $url = Yaf_Registry::get('config')->virtual->url;

        $data = array();
        $data['partner'] = Yaf_Registry::get('config')->virtual->partner;
        $data['service'] = 'heimi_card_getList';
        //生成签名
        $sign = TZ_Loader::service('VirtualMerchant', 'Base')->_getSign($data);
        $data['sign'] = $sign;

        $cardLists = json_decode(TZ_Loader::service('CurlTool', 'Base')->sendcurl($url, 'post', $data), true);
        TZ_Loader::service('Log', 'Base')->writeLog($cardLists, $this);
        //print_r($cardLists['list']);exit;
        $batch = date('YmdHis') . rand(10000, 99999);
        TZ_Loader::model('CardInfo', 'Common')->beginTransaction();
        if (!$cardLists['list']) {
            TZ_Response::success('', '无可用卡数据');
        }
        foreach ($cardLists['list'] as $row) {
            $cardData = array();
            $cardData['card_type'] = $row['cardType'];
            $cardData['iccid'] = $row['iccid'];
            TZ_Loader::service('Log', 'Base')->writeLog($cardData, $this);

            if (!TZ_Loader::model('CardInfo', 'Common')->select(['iccid:eq' => $row['iccid']], 'id', 'ROW')) {
                $cardData['batch'] = $batch;
                $cardData['created_at'] = date('Y-m-d H:i:s');
                $re = TZ_Loader::model('CardInfo', 'Common')->insert($cardData);
                $cardData['gid'] = '1001';
                $cpRe = TZ_Loader::model('CardGroup', 'Common')->insert($cardData);
                if (!$re || !$cpRe) {
                    $err[] = '新卡插入失败';
                }
            } else {
                $cardData['updated_at'] = date('Y-m-d H:i:s');
                $re = TZ_Loader::model('CardInfo', 'Common')->update($cardData, ['iccid:eq' => $row['iccid']]);
                TZ_Loader::service('Log', 'Base')->writeLog($re, $this);
                //$cardData['gid'] = '1001';
                $cpRe = TZ_Loader::model('CardGroup', 'Common')->update($cardData, ['iccid:eq' => $row['iccid']]);
                TZ_Loader::service('Log', 'Base')->writeLog($cpRe, $this);
                if (!$re || !$cpRe) {
                    $err[] = '卡更新失败';
                }
            }
        }
        TZ_Loader::service('Log', 'Base')->writeLog($err, $this);
        if (empty($err)) {
            TZ_loader::model('CardInfo', 'Common')->commit();
            TZ_Response::success('', '卡数据获取并存储成功！');
        } else {
            TZ_loader::model('CardInfo', 'Common')->rollback();
            TZ_Response::error('5001', '卡数据获取并存储失败！');
        }
    }

}