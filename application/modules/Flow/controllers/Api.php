<?php

/**
 * author: mengqi<zhangxuan@heimilink.com>.
 * Time: 2016/9/7 18:32
 * Info:
 */
class ApiController extends Yaf_Controller_Abstract
{
    /**
     * 充值回调处理
     */
    public function notifyAction()
    {
        TZ_Loader::service('Log', 'Base')->writeLog('---------- 虚商回调 ----------', $this->getRequest());
        //验证签名通过
        $result = TZ_Loader::service('VirtualMerchant', 'Base')->verifyNotify();
        TZ_Loader::service('Log', 'Base')->writeLog($result, $this->getRequest());
        $params = TZ_Request::getParams('post');
        TZ_Loader::service('Log', 'Base')->writeLog($params, $this->getRequest());
        if ($result) {
            //更新卡套餐状态
            TZ_Loader::service('Callback', 'Flow')->checkPackageStatus($params);
            //增加积分
            TZ_Loader::service('Callback', 'Flow')->updateScores($params);

            echo 'SUCCESS';
            exit;
        } else {
            TZ_Loader::service('Log', 'Base')->writeLog('签名验证错误', $this->getRequest());

            echo 'FAILED';
            exit;
        }
    }


    /**
     * 充值重试定时任务
     */
    public function AutoAction()
    {
        TZ_Loader::service('RepeatMechanism', 'Flow')->AsyRecharge();
    }

    //获取卡列表
    public function getCardListAction()
    {
        $result = TZ_Loader::service('CardList', 'Flow')->getCardList();
        TZ_Response::success($result, '成功!');
    }

    //获取套餐列表
    public function getPackagesAction()
    {
        $result = TZ_Loader::service('Packages', 'Flow')->getPackages();
        TZ_Response::success($result, '成功!');
    }

    //套餐叠加
    public function rechargeAction()
    {
        TZ_Loader::service('Log', 'Base')->writeLog('---充值begin---', $this->getRequest());
        $params = TZ_Request::getParams('post');
        $data = array(
            'iccid' => $params['iccid'],
            'packageCode' => $params['packageCode'],
            'order_id' => $params['order_id'],
        );
        //更新充值次数
        TZ_Loader::model('RechargeLog', 'Common')->update(['times' => '0'], ['order_id:eq' => $params['order_id']]);
        TZ_Loader::service('Log', 'Base')->writeLog($data, $this->getRequest());
        $result = TZ_Loader::service('Charge', 'Flow')->chargePackages($data);
        TZ_Loader::service('Log', 'Base')->writeLog($result, $this->getRequest());
        //余额不足
        if ($result['resultCode'] == 40005) {
            $orderInfo = TZ_Loader::model('RechargeOrders', 'Common')->select(['order_id:eq' => $params['order_id']], '*', 'ROW');
            $data = array(
                'hmCode' => $orderInfo['iccid'],
                'packageCode' => $orderInfo['pack_code'],
                'orderId' => $orderInfo['order_id'],
                'times' => 1
            );
            //重试
            TZ_Loader::service('RepeatMechanism', 'Flow')->_set($data);
        }
        TZ_Loader::service('Log', 'Base')->writeLog('---充值end---', $this->getRequest());
        TZ_Response::success($result, '成功!');
    }


    /**
     * 验证卡是否激活
     */
    public function checkCardActivateAction()
    {
        $params = TZ_Request::getParams('post');
        $userinfo = TZ_Loader::service('UserInfo', 'Flow')->getUserLogin($params);
        if (!$userinfo) {
            TZ_Response::error('用户未登录！');
        }
        $params['uid'] = $userinfo['uid'];
        $is_iccid = TZ_Loader::model('CardInfo', 'Common')->select(['iccid:eq' => $params['iccid']], '*', 'ROW');
        if ($is_iccid) {
            $result = TZ_Loader::service('CheckCardActivate', 'Flow')->checkActivate($params);
            if ($result['is_iden'] == 0) {
                TZ_Response::error('317', '卡未实名，请尽快进行实名认证!!!');
            } elseif ($result['is_iden'] == 1) {
                TZ_Response::error('315', '实名审核中！');
            } elseif ($result['is_iden'] == 2) {
                TZ_Response::error('318', '卡已实名！');
            } elseif ($result['is_iden'] == 3) {
                TZ_Response::error('319', '审核失败！');
            }
        } else {
            TZ_Response::error(311, '非秀豹卡！');
        }
    }

    /**
     * 秀豹卡激活
     */
    public function cardActiveAction()
    {
        TZ_Loader::service('Log', 'Base')->writeLog('----------秀豹卡激活  begin----------', $this->getRequest());
        $params = TZ_Request::getParams('post');
        if (empty($params['imei']) || empty($params['stoken']) || empty($params['iccid'])) {
            TZ_Response::error('102', '参数错误');
        }

        $userInfo = TZ_Loader::service('UserInfo', 'Flow')->getUserLogin($params['stoken']);
        if (!$userInfo) {
            TZ_Response::error('101', '用户未登录!');
        }
        TZ_Loader::service('Log', 'Base')->writeLog($userInfo, $this->getRequest());

        //检查是否showboom设备
        $restDevice = TZ_Loader::model('DeviceInfo', 'Common')->select(['imei:eq' => $params['imei']], 'id', 'ROW');
        if (!$restDevice) {
            TZ_Response::error('413', '设备不支持');
        }
        //检查是否秀豹卡以及是否实名
        $restCard = TZ_Loader::model('CardInfo', 'Common')->select(['iccid:eq' => $params['iccid']], 'id,iden_id', 'ROW');
        if (!$restCard['id']) {
            TZ_Response::error('411', '不支持此卡');
        }
        if (!$restCard['iden_id']) {
            TZ_Response::error('317', '卡未实名，请尽快进行实名认证!!!');
        }
        /*//检查是否实名
        $restIdent = TZ_Loader::model('UserIdentify', 'Common')->select(['uid:eq' => $userInfo['uid']], 'id', 'ROW');
        if (!$restIdent) {
            TZ_Response::error('314', '用户未实名， 请先实名');
        }*/
        //卡激活
        $data = ['iccid' => $params['iccid']];
        $rest = TZ_Loader::service('CardActive', 'Flow')->active($data);
        TZ_Loader::service('Log', 'Base')->writeLog($rest, $this->getRequest());
        if ($rest['resultCode'] != 0) {
            TZ_Response::error('412', '激活失败');
        }
        TZ_Response::success('', '卡激活成功');
        TZ_Loader::service('Log', 'Base')->writeLog('----------秀豹卡激活  end----------', $this->getRequest());
    }


    //--------------------------------------------------接口服务测试------------------------------------------------------
    //卡激活
    public function activeAction()
    {
        $params = TZ_Request::getParams('post');
        $data = array(
            'iccid' => $params['iccid']
        );
        $result = TZ_Loader::service('CardActive', 'Flow')->active($data);
        print_r($result);
    }


    //根据卡号查询可用的充值套餐列表
    public function testPackageListAction()
    {
        $params = TZ_Request::getParams('post');
        $data = array(
            'iccid' => $params['iccid']
        );
        $result = TZ_Loader::service('PackageList', 'Flow')->getPackageListByCode($data);
        print_r($result);
    }

    //获取卡信息
    public function getCardInfoAction()
    {
        $params = TZ_Request::getParams('post');
        $data = array(
            'iccid' => $params['iccid']
        );
        TZ_Loader::service('Log', 'Base')->writeLog($data, $this->getRequest());
        $result = TZ_Loader::service('CardInfo', 'Flow')->getCardInfo($data);
        TZ_Response::success($result, '成功!');
    }

    //实时获取流量
    public function getRemainFlowAction()
    {
        $params = TZ_Request::getParams('post');
        $data = array(
            'iccid' => $params['iccid']
        );
        $result = TZ_Loader::service('Flow', 'Flow')->getRemainFlow($data);
        TZ_Response::success($result, '成功!');
    }

    //每日流量查询
    public function getDailyFlowAction()
    {
        $params = TZ_Request::getParams('post');
        $data = array(
            'iccid' => $params['iccid'],
            'month' => $params['month']
        );
        $result = TZ_Loader::service('DailyFlow', 'Flow')->getDailyFlow($data);
        TZ_Response::success($result, '成功!');
    }

    //月流量查询
    public function getMonthFlowAction()
    {
        $params = TZ_Request::getParams('post');
        $data = array(
            'iccid' => $params['iccid'],
            'startMonth' => $params['startMonth'],
            'endMonth' => $params['endMonth']
        );
        $result = TZ_Loader::service('MonthFlow', 'Flow')->getMonthFlow($data);
        TZ_Response::success($result, '成功!');
    }


    //充值状态主动查询
    public function getRechargeStatusAction()
    {
        $params = TZ_Request::getParams('post');
        $data = array(
            'tradeNo' => $params['tradeNo'],
        );
        $result = TZ_Loader::service('RechargeStatus', 'Flow')->getRechargeStatus($data);
        TZ_Response::success($result, '成功!');
    }

    //实时获取已订购套餐
    public function getCardPackagesAction()
    {
        $params = TZ_Request::getParams('post');
        $data = array(
            'iccid' => $params['iccid'],
        );
        $result = TZ_Loader::service('CardPackages', 'Flow')->getCardPackages($data);
        TZ_Response::success($result, '成功!');
    }

    //时间处理测试
    public function testAction()
    {
        $rest = TZ_Loader::service('Callback', 'Flow')->dealExpireTime(date('Y-m-d H:i:s'), 'heimi1473412509157263');
        print_r($rest);
    }

    public function testPushAction()
    {
        $params = TZ_Request::getParams('post');
        if (empty($params['iccid']) || empty($params['order_name']) || empty($params['pack_code'])) {
            TZ_Response::error(201, '参数错误');
        }
        $rest = TZ_Loader::service('Callback', 'Flow')->pushRechargeMsg($params);
        print_r($rest);
    }
}