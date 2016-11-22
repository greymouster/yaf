<?php

/**
 *
 *
 */
class FindController extends Yaf_Controller_Abstract
{
    /*
     * 获取卡剩余流量
     */
    public function indexAction()
    {
        TZ_Runtime::start();
        $param = TZ_Request::getParams('post');
        $is_login = TZ_Loader::service('UserInfo', 'Flow')->getUserLogin($param);
        if (!$is_login) {
            TZ_Response::error(101, '用户未登录！');
        }
        //验证卡是否存在
        if (!TZ_Loader::model('CardInfo', 'Common')->select(['iccid:eq' => $param['iccid']], 'id', 'ROW')['id']) {
            TZ_Response::error('311', '非秀豹卡');
        }
        TZ_Runtime::addTag('begin');
        $data = TZ_Loader::service('Flow', 'Flow')->getRemainFlow($param);
        TZ_Runtime::addTag('end');
        if ($data['leftFlow'] < 1024) {
            $data['leftFlow'] = $data['leftFlow'] . 'MB';
        } elseif ($data['leftFlow'] >= 1024 && $data['leftFlow'] < 1048576) {
            $data['leftFlow'] = number_format($data['leftFlow'] / 1024, 2, '.', '') . 'GB';
        } elseif ($data['leftFlow'] >= 1048576) {
            $data['leftFlow'] = number_format($data['leftFlow'] / 1048576, 2, '.', '') . 'TB';
        }
        $data['query_time'] = date("Y-m-d H:i:s");
        $data['left_flow'] = $data['leftFlow'];
        unset($data['leftFlow']);
        unset($data['resultCode']);
        unset($data['lastUpdateTime']);
        if (!$data) {
            TZ_Response::error(402, '获取剩余流量数据失败！');
        }
        $uid = $is_login['uid'];
        $data['card_nick_name'] = TZ_Loader::model('DeviceCard', 'Common')->select(['uid:eq' => $uid, 'iccid:eq' => $param['iccid']], 'nick_name', 'ROW')['nick_name'];
        TZ_Runtime::close();
        TZ_Response::success($data);
    }

    public function userInfoAction()
    {
        $param = TZ_Request::getParams('post');
        $is_login = TZ_Loader::service('UserInfo', 'Flow')->getUserLogin($param);
        if (!$is_login) {
            TZ_Response::error(101, '用户未登录！');
        } else {
            TZ_Response::success($is_login);
        }
    }

    /*
    *   获取卡基本信息
    */
    public function cardInfoAction()
    {
        $param = TZ_Request::getParams('post');
        $is_login = TZ_Loader::service('UserInfo', 'Flow')->getUserLogin($param);
        if (!$is_login) {
            TZ_Response::error(101, '用户未登录！');
        }
        $data = TZ_Loader::service('CardInfo', 'Flow')->getCardInfo($param);
        if (!$data) {
            TZ_Response::error(403, '获取卡信息失败！');
        }
        TZ_Response::success($data);
    }

    /*
     *   获取卡套餐列表
    */

    public function PackageAction()
    {
        $param = TZ_Request::getParams('get');
        if (!TZ_Loader::service('UserInfo', 'Flow')->getUserLogin($param)) {
            TZ_Response::error(101, '用户未登录！');
        }
        $times = TZ_Loader::service('MakeOrder', 'Flow')->getRechargeTimes($param);
        if ($times > 9) {
            $url = "/flow/find/appindex?iccid=" . $param['iccid'] . "&imei=" . $param['imei'] . "&stoken=" . $param['stoken'];
            echo "<script> alert('充值次数已达上限！');location.href='" . $url . "'</script>";
            die;
        }
        //验证卡是否存在
        if (!TZ_Loader::model('CardInfo', 'Common')->select(['iccid:eq' => $param['iccid']], 'id', 'ROW')['id']) {
            $data = array(
                'code' => 311,
                'url' => 'javascript:;',
            );
        } else {
            $data = TZ_Loader::service('PackageList', 'Flow')->getPackageListByCode($param);

            if (!$data) {
                $url = "/flow/find/appindex?iccid=" . $param['iccid'] . "&imei=" . $param['imei'] . "&stoken=" . $param['stoken'];
                echo "<script> alert('无可用套餐数据！');location.href='" . $url . "'</script>";
            }
            foreach ($data as $k => $v) {
                $data[$k]['name'] = $v[0];
                $data[$k]['value'] = $v[1];
                unset($data[$k][0]);
                unset($data[$k][1]);
            }
            foreach ($data as $k => $v) {
                foreach ($v['value'] as $i => $j) {
                    if ($j['pic'] != '') {
                        $data[$k]['value'][$i]['pic'] = $this->getImageBase64Url($j['pic']);
                    }
                }
            }


        }
        $times = TZ_Loader::service('MakeOrder', 'Flow')->getRechargeTimes($param);
        $this->_view->assign('times', $times);
        $this->_view->assign('data', $data);
        $this->_view->display('cardtao.tpl');
    }

    /**
     * 获取套餐详情和用户积分信息
     * @param  $param package_id 套餐id
     *
     */
    public function PackageInfoAction()
    {
        $param = TZ_Request::getParams('post');
        if (!TZ_Loader::service('UserInfo', 'Flow')->getUserLogin($param)) {
            TZ_Response::error(101, '用户未登录！');
        }
        if (!$param['package_id']) {
            TZ_Response::error('params error');
        }
        $data['Package'] = TZ_Loader::model('Packages', 'Common')->select(['pack_code:eq' => $param['package_id']], '*', 'ROW');
        $data['UserInfo'] = TZ_Loader::service('UserInfo', 'Flow')->getUserInfo($param);
        TZ_Response::success($data);
    }

    /**
     * 确认套餐详情和生成订单
     * @param $param 用户信息 卡信息 套餐信息
     */

    public function PackageCheckAction()
    {
        $param = TZ_Request::getParams('get');
        $times = TZ_Loader::service('MakeOrder', 'Flow')->getRechargeTimes($param);
        if ($times > 9) {
            $url = "/flow/find/appindex?iccid=" . $param['iccid'] . "&imei=" . $param['imei'] . "&stoken=" . $param['stoken'];
            echo "<script> alert('充值次数已达上限！');location.href='" . $url . "'</script>";
            die;
        }
        //echo json_encode($param);die;
        if (!TZ_Loader::service('UserInfo', 'Flow')->getUserLogin($param)) {
            TZ_Response::error(101, '用户未登录！');
        }
        if (isset($param['order_id']) && !empty($param['order_id'])) {
            $res = TZ_Loader::model('RechargeOrders', 'Common')->select(['order_id:eq' => $param['order_id']], '*', 'ROW');
        } else {
            $info['iccid'] = $param['iccid'];
            $card_data = TZ_Loader::service('CardInfo', 'Flow')->getCardInfo($info);
            if (empty($card_data)) {
                $url = "/flow/find/appindex?iccid=" . $param['iccid'] . "&imei=" . $param['imei'] . "&stoken=" . $param['stoken'];
                echo "<script> alert('系统没有找到相应的卡！');location.href='" . $url . "'</script>";
            }
            if (empty($param['package_id'])) {
                $url = "/flow/find/appindex?iccid=" . $param['iccid'] . "&imei=" . $param['imei'] . "&stoken=" . $param['stoken'];
                echo "<script> alert('套餐不存在，请重新选择套餐！');location.href='" . $url . "'</script>";
            }

            /*
             * 生成订单
             */
            $res = TZ_Loader::service('MakeOrder', 'Flow')->MakeOrder($param);
            if ($res) {
                if ($res['status'] == 2) {
                    $t = 'payback';
                    $str = "\n" . '------------------------无需支付，开始写入卡套餐记录！-------------------------' . "\n";
                    $this->wirteLog($str, $t);
                    $cp = TZ_Loader::model('CardPackage', 'Common')->select(['order_id:eq' => $res['order_id']], '*', 'ROW');
                    if (!$cp) {
                        $CardInfo = TZ_Loader::model('CardInfo', 'Common')->select(['iccid:eq' => $res['iccid']]);
                        $PackageInfo = TZ_Loader::model('Packages', 'Common')->select(['pack_code:eq' => $res['pack_code']], '*', 'ROW');
                        $card_package['type_id'] = $PackageInfo['card_type'];
                        $card_package['iccid'] = $res['iccid'];
                        $card_package['pack_code'] = $res['pack_code'];
                        $card_package['order_id'] = $res['order_id'];
                        $card_package['status'] = 1;
                        $card_package['pack_name'] = $res['order_name'];
                        $card_package['pack_type'] = $PackageInfo['pack_type'];
                        $card_package['pack_price'] = $res['payable_price'];
                        $card_package['pack_flow'] = $PackageInfo['pack_flow'];
                        $card_package['pack_duration'] = $PackageInfo['pack_duration'];
                        $card_package['pack_pic'] = $PackageInfo['pack_pic'];
                        $card_package['pack_type'] = $PackageInfo['pack_type'];
                        $card_package['effective_type'] = 0;
                        $card_package['created_at'] = date('Y-m-d H:i:s', time());
                        $card_package['updated_at'] = date('Y-m-d H:i:s', time());
                        $str = "\n" . '------------------------生成卡套餐信息： ' . json_encode($card_package) . '-------------------------' . "\n";
                        $this->wirteLog($str, $t);
                        $recharge_primary_id = TZ_Loader::model('CardPackage', 'Common')->insert($card_package);
                        $str = "\n" . '------------------------插入卡套餐信息成功，id为：' . json_encode($recharge_primary_id) . '！-------------------------' . "\n";
                        $this->wirteLog($str, $t);
                    } else {
                        $recharge_primary_id = $cp['id'];
                    }

                    $charge_param = [
                        'iccid' => $res['iccid'],
                        'packageCode' => $res['pack_code'],
                        'order_id' => $res['order_id']
                    ];
                    $str = "\n" . '------------------------开始调用虚商充值服务！-------------------------' . "\n";
                    $this->wirteLog($str, $t);
                    $da = TZ_Loader::service('Charge', 'Flow')->chargePackages($charge_param);
                    if ($da['data']['resultCode'] == 0) {

                        $str = "\n" . '------------------------调用虚商充值成功,充值结束！！-------------------------' . "\n";
                        $this->wirteLog($str, $t);
                        $str = "\n" . '-----------------------开始更改订单状态！-------------------------' . "\n";
                        $this->wirteLog($str, $t);
                        $str = "\n" . '-----------------------开始赠送积分！-------------------------' . "\n";
                        $this->wirteLog($str, $t);
                        //用户赠送积分
                        $url = Yaf_Registry::get('config')->user_center->userinfo_url;
                        $info['service'] = 'update_user_score';
                        $info['source'] = $res['give_score'];
                        $info['uid'] = $res['uid'];
                        $info['changeScore'] = $res['give_score'];
                        $info['desc'] = '套餐充值赠送积分';
                        $str = "\n" . '-----------------------' . json_encode($info) . '-------------------------' . "\n";
                        $this->wirteLog($str, $t);
                        $aaa = TZ_Loader::service('CurlTool', 'Base')->sendcurl($url, 'post', $info);
                        $str = "\n" . '-----------------------赠送积分成功！' . json_encode($aaa) . '-------------------------' . "\n";
                        $this->wirteLog($str, $t);

                        $dataa = ['recharge_status' => 2, 'status' => 2];
                        TZ_Loader::model('RechargeOrders', 'Common')->update($dataa, ['order_id:eq' => $res['order_id']]);
                        $str = "\n" . '-----------------------订单状态修改为已支付！-------------------------' . "\n";
                        $this->wirteLog($str, $t);
                        $str = "\n" . '-----------------------开始写入订单日志！！-------------------------' . "\n";
                        $this->wirteLog($str, $t);

                        $order_logs = TZ_Loader::model('RechargeLog', 'Common')->select(['order_id:eq' => $res['order_id']], '*', 'ROW');
                        if (!$order_logs) {
                            $order_log['uid'] = $res['uid'];
                            $order_log['order_id'] = $res['order_id'];
                            $order_log['order_name'] = $res['order_name'];
                            $order_log['trade_no'] = $da['tradeNo'];
                            $order_log['pack_code'] = $res['pack_code'];
                            $order_log['iccid'] = $res['iccid'];
                            $order_log['recharge_status'] = $res['recharge_status'];
                            $order_log['created_at'] = date('Y-m-d H:i:s', time());
                            $order_log['updated_at'] = date('Y-m-d H:i:s', time());
                            TZ_Loader::model('RechargeLog', 'Common')->insert($order_log);
                            $str = "\n" . '-----------------------订单日志写入成功！！-------------------------' . "\n";
                            $this->wirteLog($str, $t);

                        } else {
                            $str = "\n" . '-----------------------订单日志已经存在！！-------------------------' . "\n";
                            $this->wirteLog($str, $t);
                        }
                        $url = "/flow/find/receiveSucc?payCustom=" . $res['order_id'] . "&iccid=" . $res['iccid'] . "&imei=" . $param['imei'] . "&stoken=" . $param['stoken'] . "&res=success";
                        echo "<script>location.href='" . $url . "'</script>";
                        die;
                        // $this->redirect("/flow/find/record?iccid=".$res['iccid']);
                    } else if ($da['data']['resultCode'] == 40005) {
                        $orderInfo = TZ_Loader::model('RechargeOrders', 'Common')->select(['order_id:eq' => $res['order_id']], '*', 'ROW');
                        $data = array(
                            'hmCode' => $orderInfo['iccid'],
                            'packageCode' => $orderInfo['pack_code'],
                            'orderId' => $orderInfo['order_id'],
                            'times' => 1
                        );
                        //重试
                        TZ_Loader::service('RepeatMechanism', 'Flow')->_set($data);
                        $url = "/flow/find/receiveSucc?payCustom=" . $res['order_id'] . "&iccid=" . $res['iccid'] . "&imei=" . $param['imei'] . "&stoken=" . $param['stoken'] . "&res=success";
                        echo "<script>location.href='" . $url . "'</script>";
                        die;
                    } else {
                        $str = "\n" . '------------------------调用虚商充值失败！！-------------------------' . "\n";
                        $this->wirteLog($str, $t);
                        $url = "/flow/find/appindex?iccid=" . $res['iccid'] . "&imei=" . $param['imei'] . "&stoken=" . $param['stoken'];
                        echo "<script> alert('充值失败，请稍后重试！');location.href='" . $url . "'</script>";
                    }
                }

            } else {
                $url = "/flow/find/appindex?iccid=" . $res['iccid'] . "&imei=" . $param['imei'] . "&stoken=" . $param['stoken'];
                echo "<script> alert('生成订单失败，请稍后重试！');location.href='" . $url . "'</script>";
            }
        }
        /**
         * 生成链接
         */
        $condition = [
            'order_id:eq' => $res['order_id'],
        ];
        $params['order_id'] = $res['order_id'];
        if ($param['payType'] != 'ali') {
            $payType = [
                'pay_type' => 'wx',
            ];
            TZ_Loader::model('RechargeOrders', 'Common')->update($payType, $condition);
        }
        $arr = [
            'oid' => $res['order_id'],
            'payCustom' => $res['order_id'],
            'pname' => $res['order_name'],
            'price' => $res['payable_price']
        ];

        $payType = $param['payType'];
        if ($payType == 'ali') {
            $isApp = 0;
        } else {
            $isApp = 2;
        }
        $order_data['order'] = $res;
        $order_data['pic'] = TZ_Loader::model('Packages', 'Common')->select(['pack_code:eq' => $res['pack_code']], "pack_pic", "ROW")['pack_pic'];

        $payCode = Yaf_Registry::get('config')->pay->niu;
        $payUrl = TZ_Loader::service('Pay', 'Base')->pay($arr, $payType, $payCode, $isApp);
        $order_data['payUrl'] = $payUrl;
        if ($payType == 'ali') {
            echo "<script>";
            echo "window.location.href='" . $payUrl . "'";
            echo "</script>";
            exit;
        } else {
            $this->_view->assign('order', $order_data['order']);
            $this->_view->assign('url', $this->getImageBase64Url('http://paysdk.weixin.qq.com/example/qrcode.php?data=' . $order_data['payUrl']));
            $this->_view->assign('pic', $this->getImageBase64Url($order_data['pic']));
            $this->_view->display('order.tpl');
        }

        // TZ_Response::success($order_data);
        // exit;
    }

    /*
     * 支付回调接口
     */
    public function payOrderBackAction()
    {
        $params = TZ_Request::getParams('post');
        $condition['order_id:eq'] = $params['payCustom'];
        $or = TZ_Loader::model('RechargeOrders', 'Common')->select($condition, '*', 'ROW');
        if ($or['status'] == 3) {
            TZ_Response::success();
        }

        $t = 'payback';
        $str = "\n" . '------------------------pay back start-------------------------' . "\n";
        $this->wirteLog($str, $t);

        $this->wirteLog($params, $t);
        $isOk = TZ_Loader::service('Pay', 'Base')->checkSign($params);

        if ($isOk) {
            $str = "\n" . '------------------------签名验证成功！-------------------------' . "\n";
            $this->wirteLog($str, $t);
            $charge_param = TZ_Loader::service('MakeOrder', 'Flow')->makeCardPackage($params, $condition);

            $str = "\n" . '------------------------虚商信息字段：' . json_encode($charge_param) . '-------------------------' . "\n";
            $this->wirteLog($str, $t);
            /**
             * 虚商充值服务调用
             */
            $str = "\n" . '------------------------开始调用虚商服务！-------------------------' . "\n";
            $this->wirteLog($str, $t);
            $da = TZ_Loader::service('Charge', 'Flow')->chargePackages($charge_param['cp']);
            $OrderData = TZ_Loader::model('RechargeOrders', 'Common')->select($condition, '*', 'ROW');
            if ($da['data']['resultCode'] == 0) {

                $str = "\n" . '------------------------充值成功！-------------------------' . "\n";
                $this->wirteLog($str, $t);
                $str = "\n" . '------------------------充值结束！-------------------------' . "\n";
                $this->wirteLog($str, $t);
                $str = "\n" . '------------------------开始赠送积分！-------------------------' . "\n";
                $this->wirteLog($str, $t);

                //用户赠送积分
                $url = Yaf_Registry::get('config')->user_center->userinfo_url;
                $info['service'] = 'update_user_score';
                $info['source'] = $OrderData['give_score'];
                $info['uid'] = $OrderData['uid'];
                $info['changeScore'] = $OrderData['give_score'];
                $info['desc'] = '套餐充值赠送积分';
                $str = "\n" . '-----------------------' . json_encode($info) . '-------------------------' . "\n";
                $this->wirteLog($str, $t);
                $aaa = TZ_Loader::service('CurlTool', 'Base')->sendcurl($url, 'post', $info);
                $str = "\n" . '------------------------积分赠送成功！' . json_encode($aaa) . '-------------------------' . "\n";
                $this->wirteLog($str, $t);


                $str = "\n" . '-----------------------开始更新订单状态！-------------------------' . "\n";
                $this->wirteLog($str, $t);
                $data = ['recharge_status' => 2, 'status' => 2];
                TZ_Loader::model('RechargeOrders', 'Common')->update($data, $condition);
                $str = "\n" . '-----------------------订单状态被改为已支付！-------------------------' . "\n";
                $this->wirteLog($str, $t);
                $str = "\n" . '-----------------------开始写入订单日志！！-------------------------' . "\n";
                $this->wirteLog($str, $t);

                $CardInfo = TZ_Loader::model('CardInfo', 'Common')->select(['iccid:eq' => $OrderData['iccid']], '*', 'ROW');
                $PackageInfo = TZ_Loader::model('Packages', 'Common')->select(['card_type:eq' => $CardInfo['card_type']], '*', 'ROW');
                $order_logs = TZ_Loader::model('RechargeLog', 'Common')->select(['order_id:eq' => $OrderData['order_id']], '*', 'ROW');
                if (!$order_logs) {
                    $order_log['uid'] = $OrderData['uid'];
                    $order_log['order_id'] = $OrderData['order_id'];
                    $order_log['order_name'] = $OrderData['order_name'];
                    $order_log['trade_no'] = $da['tradeNo'];
                    $order_log['pack_code'] = $OrderData['pack_code'];
                    $order_log['iccid'] = $OrderData['iccid'];
                    $order_log['recharge_status'] = $OrderData['recharge_status'];
                    $order_log['created_at'] = date('Y-m-d H:i:s', time());
                    $order_log['updated_at'] = date('Y-m-d H:i:s', time());
                    TZ_Loader::model('RechargeLog', 'Common')->insert($order_log);
                    $str = "\n" . '-----------------------订单日志写入成功！！-------------------------' . "\n";
                    $this->wirteLog($str, $t);

                } else {
                    $str = "\n" . '-----------------------订单日志已经存在！！-------------------------' . "\n";
                    $this->wirteLog($str, $t);
                }
                TZ_Response::success();
            } else if ($da['data']['resultCode'] == 40005) {
                $orderInfo = TZ_Loader::model('RechargeOrders', 'Common')->select(['order_id:eq' => $OrderData['order_id']], '*', 'ROW');
                $data = array(
                    'hmCode' => $orderInfo['iccid'],
                    'packageCode' => $orderInfo['pack_code'],
                    'orderId' => $orderInfo['order_id'],
                    'times' => 1
                );
                //重试
                TZ_Loader::service('RepeatMechanism', 'Flow')->_set($data);
                TZ_Response::success();
            } else {
                $str = "\n" . '------------------------虚商充值失败！' . json_encode($da) . '-------------------------' . "\n";
                $this->wirteLog($str, $t);
            }
        } else {
            $str = "\n" . '------------------------签名验证失败！-------------------------' . "\n";
            $this->wirteLog($str, $t);
            TZ_Response::error('500', '数据错误');
        }
    }

    /**
     * 前台回掉地址
     */
    public function receiveSuccAction()
    {
        $t = 'pay';

        $str = "\n" . '------------------------views pay back------------------------' . "\n";
        $this->wirteLog($str, $t);
        $params = TZ_Request::getParams();

        $this->wirteLog($params, $t);
        $orderId = trim($params['payCustom']);

        $res = trim($params['res']);
        $score = 0;
        $orderInfo = TZ_Loader::model('RechargeOrders', 'Common')->select(array('order_id:eq' => $orderId), '*', "ROW");
        $orderInfo['pack_flow'] = TZ_Loader::model('CardPackage', 'Common')->select(['order_id:eq' => $orderId], 'pack_flow', 'ROW')['pack_flow'];
        $this->_view->assign("order", $orderInfo);
        $this->_view->assign('code', $orderInfo['iccid']);
        if ($res == 'success') {
            $this->wirteLog('Virtual recharge result is  success', $t);
            $str = "\n" . '------------------------views pay back  END------------------------' . "\n";
            $this->wirteLog($str, $t);
            if (count($orderInfo) > 0) {
                $score = $orderInfo['give_score'];
            }
            $times = TZ_Loader::service('MakeOrder', 'Flow')->getRechargeTimes($params);
            $this->_view->assign('times', 10 - $times);
            $this->_view->assign('score', $score);
            $this->_view->display('result.tpl');
        } else {
            $this->_view->display('payFailed.tpl');
        }
    }

    private function wirteLog($msg, $t)
    {
        $file = APP_PATH . "/logs/Flow/" . $t . "/" . date("Ymd") . ".log";
        if (!is_dir(APP_PATH . "/logs/Flow/" . $t)) {
            mkdir(APP_PATH . "/logs/Flow/" . $t, 0777);
        } else {
            chmod(APP_PATH . "/logs/auto/" . $t, 0777);
            if (file_exists($file)) {
                chmod($file, 0777);
            }
        }
        file_put_contents($file, date('Y-m-d H:i:s') . " : " . json_encode($msg, JSON_UNESCAPED_UNICODE) . "\r\n", FILE_APPEND);
    }

    /**
     * 充值记录
     */
    public function recordAction()
    {
        if (isset($_GET['iccid']) && !empty($_GET['iccid'])) {
        } else {
            TZ_Response::error('param error');
        }
        $params['iccid'] = $_GET['iccid'];
        $times = TZ_Loader::service('MakeOrder', 'Flow')->getRechargeTimes($params);
        $condition['iccid:eq'] = $params['iccid'];
        $condition['order'] = 'created_at DESC';
        $condition['status:lt'] = 4;
        $dlList = TZ_Loader::model("RechargeOrders", 'Common')->select($condition, '*', "ALL");
        $this->_view->assign('times', $times);
        $this->_view->assign('stoken', $_COOKIE['stoken']);
        $this->_view->assign('times', $times);
        $this->_view->assign('list', $dlList);
        $this->_view->display('record.tpl');
    }

    /**
     * 购物车页面
     */
    public function cashierAction()
    {
        $params = TZ_Request::getParams('get');
        $times = TZ_Loader::service('MakeOrder', 'Flow')->getRechargeTimes($params);
        if ($times > 9) {
            $url = "/flow/find/appindex?iccid=" . $params['iccid'] . "&imei=" . $params['imei'] . "&stoken=" . $params['stoken'];
            echo "<script> alert('充值次数已达上限！');location.href='" . $url . "'</script>";
            die;
        }
        //如果传过来的是order_id
        if (empty($params['order_id']) && empty($params['package_id'])) {
            TZ_Response::error('params error');
        }
        if (isset($params['order_id']) && !empty($params['order_id'])) {
            $result = TZ_Loader::model('RechargeOrders', 'Common')->select(['order_id:eq' => $params['order_id']], '*', 'ROW');
            $pic = TZ_Loader::model('Packages', 'Common')->select(['pack_code:eq' => $result['pack_code']], '*', 'ROW')['pack_pic'];
            $result['type'] = 'order';
        }
        if (isset($params['package_id']) && !empty($params['package_id'])) {
            $result = TZ_Loader::model('Packages', 'Common')->select(['pack_code:eq' => $params['package_id']], '*', 'ROW');
            $pic = $result['pack_pic'];
            $order['type'] = 'package';
            //$params['getFlow'] = '1';//不查询流量数据
            $userScore = TZ_Loader::service('UserInfo', 'Flow')->getUserInfo($params);
            unset($params['getFlow']);
            $userScore = json_decode($userScore, true)['data']['score'];
            if ($userScore < 10) {
                $userScore = 0;
            }
            if ($userScore < $result['max_used_score']) {
                $userScore = $userScore - $userScore % 10;
            } else {
                $userScore = $result['max_used_score'];
            }
            if ($userScore / 1000 >= $result['pack_price']) {
                $userScore = $result['pack_price'] * 1000;
            }

            $this->_view->assign('payPrice', $result['pack_price'] - $userScore / 1000);
        }
        $this->_view->assign('pic', $this->getImageBase64Url($pic));
        $this->_view->assign('scorePrice', $userScore / 1000);
        $times = TZ_Loader::service('MakeOrder', 'Flow')->getRechargeTimes($params);
        $this->_view->assign('times', 10 - $times);
        $this->_view->assign('score', $userScore);
        $this->_view->assign('result', $result);
        $this->_view->display('cash.tpl');
        die;
    }

    /**
     * 取出用户的有效期内套餐
     */
    public function userPackageAction()
    {
        $params = TZ_Request::getParams('post');
        $condition['iccid:eq'] = $params['iccid'];
        $condition['order'] = 'created_at DESC';
        //$condition['expire_time:gt'] = date('Y-m-d H:i:s');
        $data = TZ_Loader::model("CardPackage", 'Common')->select($condition, '*', 'ALL');
        foreach ($data as $k => $v) {
            $data[$k]['pack_pic'] = $this->getImageBase64Url(TZ_Loader::model('Packages', 'Common')->select(['pack_code:eq' => $v['pack_code']], 'pack_pic', 'ROW')['pack_pic']);
        }
        TZ_Response::success($data);

    }

    /**
     * 查看微信有没有支付成功
     */
    public function orderStatusAction()
    {
        $params = TZ_Request::getParams('post');
        $condition['order_id:eq'] = $params['order_id'];
        $data = TZ_Loader::model('RechargeOrders', 'Common')->select($condition, 'status', 'ROW');
        TZ_Response::success($data);
    }

    public function appIndexAction()
    {
        $this->_view->display('card_info.tpl');
    }

    /**
     * 转换二维码图片数据格式为Base64
     * @param $imgUrl
     */
    public function getImageBase64Url($imgUrl)
    {
        $msg = file_get_contents($imgUrl);
        $msg = base64_encode($msg);
        if (strpos(',', $msg) === false) {
            $msg = "data:image/png;base64," . $msg;
        }
        return $msg;
    }

    /**
     * 修改卡备注
     */
    public function changeNickNameAction()
    {
        $param = TZ_Request::getParams('post');
        $is_login = TZ_Loader::service('UserInfo', 'Flow')->getUserLogin($param);
        if (!$is_login) {
            TZ_Response::error(101, '用户未登录！');
        }
        if (!TZ_Loader::model('CardInfo', 'Common')->select(['iccid:eq' => $param['iccid']], 'id', 'ROW')['id']) {
            TZ_Response::error('311', '非秀豹卡');
        }

        $nick_name = htmlspecialchars(trim($param['nick_name']));
        if (!$nick_name || $nick_name == '') {
            TZ_Response::error('20002', '昵称不能为空！');
        }
        $condition = [
            'nick_name' => $nick_name,
        ];

        TZ_Loader::model('DeviceCard', 'Common')->update($condition, ['iccid:eq' => $param['iccid'], 'uid:eq' => $is_login['uid']]);
        TZ_Response::success();
    }

    public function removeOrderAction()
    {
        $currentTime = date("Y-m-d H:i:s", time() - 3600 * 12);
        $condition['created_at:lt'] = $currentTime;
        $condition['status:eq'] = 1;

        $order_data = TZ_Loader::model('RechargeOrders', 'Common')->select($condition, '*', "ALL");
        if (!empty($order_data)) {
            foreach ($order_data as $k => $v) {
                $order_condition['status'] = 4;
                TZ_Loader::model('RechargeOrders', 'Common')->update($order_condition, ['order_id:eq' => $v['order_id']]);
                //用户积分退回
                $url = Yaf_Registry::get('config')->user_center->userinfo_url;
                $info['service'] = 'update_user_score';
                $info['source'] = $v['use_score'];
                $info['uid'] = $v['uid'];
                $info['changeScore'] = '充值回退积分';
                $info['desc'] = '订单取消回退积分';
                TZ_Loader::service('CurlTool', 'Base')->sendcurl($url, 'post', $info);
            }
        } else {

            TZ_Loader::service('Log', 'Base')->writeLog('---------- 本次没有订单需要被处理！ ----------', $this->getRequest());
            return true;
        }
        $arr = [];
        foreach ($order_data as $k => $v) {
            $arr[] = $v['order_id'];
        }
        TZ_Loader::service('Log', 'Base')->writeLog('---------- ' . json_encode($arr) . '订单取消，已回退用户积分 ----------', $this->getRequest());
        return true;

    }
}




