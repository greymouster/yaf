<?php

/**
 * ad controller
 * @author octopus <zhangguipo@747.cn>
 * @final 2016-01-06
 */
class AdController extends Yaf_Controller_Abstract
{
    /**
     * 获取广告列表
     */
    public function indexAction()
    {
        //http://www.adapi.com/adapi/ad/index?app_code=111&tag_code=kikitag
        //得到其他参数
        $params = TZ_Request::getParams('get');
        //得到应用标签
        if (empty($params['app_code']) || empty($params['tag_code'])) {
            throw new Exception('参数不能为空');
        }
        //得到页数和每页条数
        $page = intval(empty($params['page']) ? 1 : $params['page']);
        $size = intval(empty($params['count']) ? 20 : $params['count']);
        if ($page <= 0) {
            throw new Exception('页号错误');
        }
        //判断是否是正整数
        if ($size <= 0) {
            throw new Exception('每页数量错误');
        }
        $limit = ($page - 1) * $size;
        $result = TZ_Loader::service('Ad', 'Adapi')->getAdList($params, $limit, $size);
        TZ_Request::success($result);
    }
}