<?php

/**
 * author: mengqi<zhangxuan@heimilink.com>.
 * Time: 2016/9/8 10:06
 * Info:
 */
class PackagesService
{
    const PACKAGE_TYPE = 'HM_NIU_DL01';

    /**
     * 获取套餐列表
     * @param $params
     * @return mixed
     */
    public function getPackages($params = array())
    {
        $url = Yaf_Registry::get('config')->virtual->url;

        $data = array();
        $data['partner'] = Yaf_Registry::get('config')->virtual->partner;
        $data['service'] = 'heimi_pack_getList';
        //生成签名
        $sign = TZ_Loader::service('VirtualMerchant', 'Base')->_getSign($data);
        $data['sign'] = $sign;

        $packageList = json_decode(TZ_Loader::service('CurlTool', 'Base')->sendcurl($url, 'post', $data), true);
        if (!$packageList['list']) {
            TZ_Response::success('', '未获取到套餐数据');
        }
        TZ_Loader::model('Packages', 'Common')->beginTransaction();
        foreach ($packageList['list'] as $row) {
            $package['pack_code'] = $row['code'];
            $package['pack_name'] = $row['title'];
            $package['card_type'] = $row['cardType'];
            $package['pack_type'] = $row['pkgType'];
            $package['cost_price'] = $row['price'];
            $package['pack_flow'] = $row['packFlow'];//流量值
            $package['pack_duration'] = $row['packDuration'];//套餐有效时限
            $package['created_at'] = date('Y-m-d H:i:s');


            if (!TZ_Loader::model('Packages', 'Common')->select(['pack_code:eq' => $row['code']], 'id', 'ROW')) {
                $re = TZ_Loader::model('Packages', 'Common')->insert($package);
                if (!$re) {
                    $err[] = '新套餐数据插入失败';
                }
            } else {
                $newData['cost_price'] = $row['price'];
                $newData['updated_at'] = date('Y-m-d H:i:s');

                $re = TZ_Loader::model('Packages', 'Common')->update($newData, ['pack_code:eq' => $row['code']]);
                if (!$re) {
                    $err[] = '套餐数据更新失败';
                }
            }
        }

        if (empty($err)) {
            TZ_loader::model('Packages', 'Common')->commit();
            TZ_Response::success('', '套餐数据获取并存储成功！');
        } else {
            TZ_Loader::model('Packages', 'Common')->rollback();
            TZ_Response::error('5001', '套餐数据获取并存储失败！');
        }

    }


}