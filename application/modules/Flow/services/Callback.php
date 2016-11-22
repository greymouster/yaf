<?php

/**
 * author: mengqi<zhangxuan@heimilink.com>.
 * Time: 2016/9/8 11:15
 * Info: 充值虚商回调
 */
class CallbackService
{
    //虚商回调 -- 更新套餐状态
    public function checkPackageStatus($params = array())
    {
        TZ_Loader::service('Log', 'Base')->writeLog('----------虚商回调业务处理  begin----------', $this);
        TZ_Loader::service('Log', 'Base')->writeLog($params, $this);
        $tradeNo = $params['tradeNo'];
        $result = $this->getOrderInfo($tradeNo);
        TZ_Loader::service('Log', 'Base')->writeLog($result, $this);
        $respTime = date('Y-m-d H:i:s', $params['respTime']);
        if ($params['chargeStatus'] == 1) {
            TZ_Loader::service('Log', 'Base')->writeLog('---充值成功begin---', $this);
            $rechargeLogStatus = 3;//充值成功
            $rechargeOrdersStatus = 3;//充值成功
            $orderStatus = 3;//已完成
            $cardPackageStatus = 2;//已生效
            $effectiveTime = urldecode($params['effectiveTime']);
            //处理失效时间
            $expireTime = $this->dealExpireTime($effectiveTime, $result['pack_code']);
            $rechargeAt = $respTime;
            //推送充值成功消息
            $params = ['iccid' => $result['iccid'], 'order_name' => $result['order_name'], 'pack_code' => $result['pack_code']];
            $this->pushRechargeMsg($params);
            TZ_Loader::service('Log', 'Base')->writeLog('---充值成功end---', $this);
        } elseif ($params['chargeStatus'] == 2) {
            TZ_Loader::service('Log', 'Base')->writeLog('---充值失败重试begin---', $this);
            //重试机制
            $mechanismRe = TZ_Loader::service('RepeatMechanism', 'Flow')->AsyMechanism($tradeNo);
            //进入异步处理
            if ($mechanismRe) {
                $rechargeLogStatus = 2;//充值中
                $rechargeOrdersStatus = 2;//充值中
                $orderStatus = 2;//已支付
                $cardPackageStatus = 1;//未生效
            } else {
                //已重试5次，充值失败
                $rechargeLogStatus = 4;//充值失败
                $rechargeOrdersStatus = 4;//充值失败
                $orderStatus = 2;//已支付
                $cardPackageStatus = 1;//未生效
            }
            $rechargeAt = $effectiveTime = $expireTime = '0000-00-00 00:00:00';
            TZ_Loader::service('Log', 'Base')->writeLog('---充值失败重试end---', $this);
        }
        $errMsg = $params['errorMessage'];

        TZ_Loader::service('Log', 'Base')->writeLog($rechargeLogStatus . '--' . $rechargeOrdersStatus . '--' . $orderStatus . '--' . $cardPackageStatus, $this);
        //排除虚商重复回告
        if ($result['recharge_status'] != 3 || $result['recharge_status'] != 4) {
            TZ_Loader::service('Log', 'Base')->writeLog('----------状态更新  begin----------', $this);
            TZ_Loader::service('Log', 'Base')->writeLog('----------更新充值订单记录表----------', $this);
            //更新充值订单记录表
            TZ_Loader::model('RechargeLog', 'Common')->update(['recharge_status' => $rechargeLogStatus, 'error_message' => $errMsg, 'updated_at' => $respTime], ['trade_no:eq' => $tradeNo]);
            TZ_Loader::service('Log', 'Base')->writeLog('----------更新订单表----------', $this);
            //更新订单表
            TZ_Loader::model('RechargeOrders', 'Common')->update(['recharge_status' => $rechargeOrdersStatus, 'status' => $orderStatus, 'recharge_at' => $rechargeAt, 'updated_at' => $respTime], ['order_id:eq' => $result['order_id']]);
            TZ_Loader::service('Log', 'Base')->writeLog('----------更新卡套餐表----------', $this);
            //更新卡套餐表
            TZ_Loader::model('CardPackage', 'Common')->update(['status' => $cardPackageStatus, 'effective_time' => $effectiveTime, 'expire_time' => $expireTime, 'updated_at' => date('Y-m-d H:i:s')], ['order_id:eq' => $result['order_id']]);
            TZ_Loader::service('Log', 'Base')->writeLog('----------状态更新  end----------', $this);

            //插入接口日志
            TZ_Loader::model('ApiLogs', 'Common')->insert(['order_id' => $result['order_id'], 'trade_no' => $tradeNo, 'iccid' => $result['iccid'], 'interface' => 'heimi_card_chargePackage', 'result' => json_encode($params, JSON_UNESCAPED_UNICODE)]);
        }

        TZ_Loader::service('Log', 'Base')->writeLog('----------虚商回调业务处理  end----------', $this);
    }

    //虚商回调 -- 修改积分
    public function updateScores($params)
    {
        //充值成功
        if ($params['chargeStatus'] == 1) {
            $tradeNo = $params['tradeNo'];
            $result = $this->getOrderInfo($tradeNo);
            if ($result['recharge_status'] != 3 || $result['recharge_status'] != 4) {
                $originResult = TZ_Loader::model('Score', 'Common')->select(['uid:eq' => $result['uid']], '*', 'ROW');
                //当前积分
                $finalScore = $originResult['score'] + $result['give_score'];
                //累计总积分
                $finalTotalScore = $originResult['total_score'] + $result['give_score'];

                //积分信息
                $data = array();
                $data['score'] = $finalScore;
                $data['total_score'] = $finalTotalScore;
                $data['update_at'] = date('Y-m-d H:i:s');
                TZ_Loader::model('Score', 'Common')->update($data, ['uid:eq' => $result['uid']]);

                //积分日志
                $logData = array();
                $logData['uid'] = $result['uid'];
                $logData['user_score'] = $originResult['score'];
                $logData['change_score'] = $result['give_score'];
                $logData['result_score'] = $finalScore;
                $logData['source'] = 'xiubao';
                $logData['desc'] = '充值送积分';
                $logData['create_at'] = date('Y-m-d H:i:s');

                TZ_Loader::model('ScoreLogs', 'Common')->insert($logData);
            }
        }
    }

    /**
     * 根据流水号获取订单信息
     * @param $tradeNo
     * @return mixed
     */
    private function getOrderInfo($tradeNo)
    {
        return TZ_Loader::model('RechargeLog', 'Common')->select(['trade_no:eq' => $tradeNo], '*', 'ROW');
    }


    /**
     * 处理套餐到期时间
     * @param $effectiveTime    生效时间 0000-00-00 00:00:00
     * @param $packCode 套餐编号
     * @return mixed
     */
    public function dealExpireTime($effectiveTime, $packCode)
    {
        //获取套餐类型
        $pkInfo = TZ_Loader::model('Packages', 'Common')->select(['pack_code:eq' => $packCode], '*', 'ROW');
        if ($pkInfo['pack_type'] == 'package') {
            //时间长度
            $duration = $pkInfo['pack_duration'];
            //截止时间
            $expire_time = date('Y-m-d H:i:s', strtotime("+ $duration month"));
            //截止时间的下一个月
            $a = date("Y-m-01 H:i:s", strtotime("+1 month", strtotime($expire_time)));
            //截止时间当月的最后一天
            $expireTime = date('Y-m-d 23:59:59', strtotime("-1 day", strtotime($a)));
        } elseif ($pkInfo['pack_type'] == 'year' || $pkInfo['pack_type'] == 'half' || $pkInfo['pack_type'] == 'quarter') {
            $duration = $pkInfo['pack_duration'];
            $expireTime = date('Y-m-d H:i:s', strtotime("+ $duration day"));
        } elseif ($pkInfo['pack_type'] == 'month') {
            //截止时间的下一个月
            $lastDay = date("Y-m-01 H:i:s", strtotime("+1month", strtotime(date('Y-m-d H:i:s'))));
            //截止时间当月的最后一天
            $expireTime = date('Y-m-d 23:59:59', strtotime("-1 day", strtotime($lastDay)));
        }
        return $expireTime;
    }


    //推送充值成功消息
    private function pushRechargeMsg($params)
    {
        TZ_Loader::service('Log', 'Base')->writeLog('---推送充值成功消息begin---', $this);
        //获取当前卡所在设备
        $imei = TZ_Loader::model('DeviceCard', 'Common')->select(['iccid:eq' => $params['iccid'], 'is_usednow:eq' => 1], 'imei', 'ROW')['imei'];
        TZ_Loader::service('Log', 'Base')->writeLog($imei, $this);
        $pack_flow = TZ_Loader::model('Packages', 'Common')->select(['pack_code:eq' => $params['pack_code']], 'pack_flow', 'ROW')['pack_flow'];
        TZ_Loader::service('Log', 'Base')->writeLog($pack_flow, $this);
        //调用消息中心通知
        $args = [];
        $args['partnerid'] = '22222';
        $args['service'] = 'msg';
        $args['send_type'] = 0;
        $args['send_scope'] = 2;
        $args['imeis'] = ["$imei"];
        $args['expire_time'] = time() + 3600 * 2;
        $args['is_immediate'] = 1;
        $args['msg_type'] = 3001;
        $args['msg_title'] = '充值成功提醒';
        $args['is_top'] = 0;
        $args['msg_page'] = '';
        $args['msg_app'] = '';
        $args['msg_url'] = '';
        $args['msg_image'] = '';
        //组合消息
        $msgData = [];
        $msgData['msg_content'] = "您充值的流量套餐：" . $params['order_name'] . "；流量值：" . $pack_flow . "已经生效！";
        $msgData['msg_title'] = $args['msg_title'];
        $msgData['expire_time'] = $args['expire_time'];
        $msgData['msg_type'] = $args['msg_type'];
        $msgData['expire_time'] = $args['expire_time'];
        $msgData['is_top'] = $args['is_top'];
        $msgData['msg_page'] = $args['msg_page'];
        $msgData['msg_app'] = $args['msg_app'];
        $msgData['msg_url'] = $args['msg_url'];
        $msgData['msg_image'] = $args['msg_image'];
        $args['msg_data'] = serialize($msgData);
        $args['send_scope'] = 2;

        //插入消息库
        $data = $args;
        $data['audit_status'] = 1;//已审核
        $data['status'] = 1;//正常
        $data['imeis'] = json_encode($args['imeis'], JSON_UNESCAPED_UNICODE);
        $data['msg_title'] = $msgData['msg_title'];
        $data['msg_content'] = $msgData['msg_content'];
        $data['created_at'] = date('Y-m-d H:i:s');
        unset($data['service']);
        unset($data['send_type']);
        TZ_Loader::service('Log', 'Base')->writeLog($data, $this);
        $id = TZ_Loader::model('Xbmessage', 'Common')->insert($data);

        $args['callerid'] = $id;
        TZ_Loader::service('Log', 'Base')->writeLog($args, $this);
        //发送消息
        $rest = TZ_Loader::service('Center', 'Flow')->get($args);
        //更新消息状态
        if (json_decode($rest, true)['code'] == 0) {
            $status = 2;
        }
        TZ_Loader::model('Xbmessage', 'Common')->update(['audit_status' => $status], ['id:eq' => $id]);
        TZ_Loader::service('Log', 'Base')->writeLog('---推送充值成功消息end---', $this);
        //TZ_Response::success($rest);
    }
}