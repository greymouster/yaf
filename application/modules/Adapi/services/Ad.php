<?php

/**
 * ad services
 * @author octopus <zhangguipo@747.cn>
 * @final 2016-01-06
 */
class AdService
{

    /**
     * 广告列表
     *
     */
    public function getAdList($params, $limit, $size)
    {
        $appInfo = TZ_Loader::model('App', 'Adapi')->select(array('app_code:eq' => $params['app_code']), "id", "ROW");
        //print_r($appInfo);
        if (count($appInfo) == 0) {
            throw new Exception('数据不存在');
        }
        $adList = TZ_Loader::model('Tag', 'Adapi')->getAdList($params['tag_code'], $appInfo['id'], $limit, $size);
        $data['tag_code'] = $params['tag_code'];
        $data['app_code'] = $params['app_code'];
        $data['mac'] = empty($params['mac']) ? '-mac-' : $params['mac'];
        $data['idfa'] = empty($params['idfa']) ? '-idfa-' : $params['idfa'];
        $data['idfv'] = empty($params['idfv']) ? '-idfv-' : $params['idfv'];
        $data['imei'] = empty($params['imei']) ? '-imei-' : $params['imei'];
        $data['phone'] = empty($params['phone']) ? '-phone-' : $params['phone'];
        foreach ($adList as &$row) {
            $data['ad_id'] = $row['id'];
            //$row['url']=$row['url'].'?'.http_build_query($data);
            $row['pic'] = $row['pic'];
            $row['url'] = $row['url'];
            try {
                $insert = $data;
                $insert['platform'] = $params['platform'];
                $insert['create_at'] = date('Y-m-d H:i:s');
                TZ_Loader::model('ReqLog', 'Adapi')->insert($insert);
            } catch (Exception $e) {
            }
        }
        return $adList;
    }

    //查询广告信息
    public function getRealURl($id, $params)
    {
        $adInfo = TZ_Loader::model('Object', 'Adapi')->select(array('id:eq' => $id), "*", "ROW");
        if (count($adInfo) == 0) {
            throw new Exception('广告不存在');
        }
        $realUrl = $adInfo['url'];
        foreach ($params as $key => $val) {
            $realUrl = str_replace('-' . $key . '-', $val, $realUrl);
        }
        return $realUrl;
    }
}
		










