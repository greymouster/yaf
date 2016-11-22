<?php
/**
 * api控制器基类
 *
 *  @author ziyang<hexiangcheng@showboom.cn>
 *  date 2016-06-08
 */
class TZ_Api extends Yaf_Controller_Abstract
{

    protected $params;


    public function init()
    {
        if($this->getRequest()->getParams()['apiMark']!=='link++api++'){
            die('访问失败，该页面不存在！');
        }

        //参数判断
        $params=TZ_Request::getParams('post');
        foreach($params as $k=>$v){
            $params[$k]=trim(urldecode($v));
        }

        $this->params=$params;
        if($this->apiParams){
            foreach($this->apiParams as $val){
                if(empty($this->params[$val])){
                    TZ_Response::errorapi(40003,'必填参数不能为空');
                }
            }

        }


    }
}
