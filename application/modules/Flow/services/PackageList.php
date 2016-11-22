<?php

/**
 * author: mengqi<zhangxuan@heimilink.com>.
 * Time: 2016/9/9 11:42
 * Info:
 */
class PackageListService
{

    /**
     * 获取可用产品套餐列别
     * @param array $params
     * @return mixed
     */
    public function getPackageListByCode($params = array())
    {
        $cardType = TZ_Loader::model('CardInfo', 'Common')->select(['iccid:eq' => $params['iccid']], 'card_type', 'ROW')['card_type'];
        $packageList = TZ_Loader::model('Packages', 'Common')->select(array('card_type:eq' => $cardType, 'pack_status:eq' => 2, 'order' => 'sort_no ASC,updated_at DESC'));
        if (!$packageList) {
            return [];
        }
        foreach ($packageList as $row) {
            $type = $row['pack_type'];
            //查询原价、套餐描述
            $result = TZ_Loader::model('Packages', 'Common')->select(array('pack_code:eq' => $row['pack_code']), 'market_price,pack_sub_name,pack_pic,monthly_clearing,effective_type', 'ROW');
            $package['current_price'] = $row['pack_price'];
            $package['desc'] = $result['pack_sub_name'];
            $package['original_price'] = $result['market_price'];
            $package['package_id'] = $row['pack_code'];
            $package['pic'] = $result['pack_pic'] == 'nopic' ? '' : $row['pack_pic'];
            $package['title'] = $row['pack_name'];
            if ($result['monthly_clearing'] == 1 && $result['effective_type'] == 1) {
                $package['flag'] = 1;
            } else {
                $package['flag'] = 0;
            }

            $key = TZ_Loader::service('BaseCard', 'Base')->getPackageType($type);
            /*if ($type == self::MONTH_TYPE_CODE) {
                if (empty($list[1][$type]['title'])) {
                    $list[1][$type]['title'] = $key;
                }
                $list[1][$type]['list'][] = $package;
            } else {*/
            if (empty($list[$type]['title'])) {
                $list[$type]['title'] = $key;
            }
            $list[$type]['list'][] = $package;
            //}
        }

        foreach ($list as $key => &$val) {
            $tempArr = array();
            foreach ($val as $key => $value) {
                $tempArr[] = $value;
            }
            $val = $tempArr;
        }
        TZ_Loader::service("Log", "Base")->writeLog($list, $this);
        return $list;
    }

}