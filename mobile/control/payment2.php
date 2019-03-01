<?php
/**
 * 支付回调
 *
 *  (c) ()
 * @license
 * @link
 * @since
 */

defined('In33hao') or exit('Access Invalid!');

class payment2Control extends mobileHomeControl {

	private $payment_code;

	public function __construct() {
		parent::__construct();
		if (isset($_GET['payment_code'])) {
			$this->payment_code = $_GET['payment_code'];

		}
	}

	/**
	 * 支付回调
	 */
	public function returnOp() {
		unset($_GET['act']);
		unset($_GET['op']);
		unset($_GET['payment_code']);

		$payment_api = $this->_get_payment_api();

		$payment_config = $this->_get_payment_config();

		$callback_info = $payment_api->getReturnInfo($payment_config);

		if ($callback_info) {
			//验证成功
			$result = $this->_update_order($callback_info['out_trade_no'], $callback_info['trade_no']);
			if ($result['state']) {
				Tpl::output('result', 'success');
				Tpl::output('message', '支付成功');
			} else {
				Tpl::output('result', 'fail');
				Tpl::output('message', '支付失败');
			}
		} else {
			//验证失败
			Tpl::output('result', 'fail');
			Tpl::output('message', '支付失败');
		}

		Tpl::showpage('payment_message');
	}

	/**
	 * 支付提醒
	 */
	public function notifyOp() {
		//适用于PHP 7 之后
        $xml = file_get_contents("php://input");
		//$this->writeLog($xml);
        $_GET = (array)$this->XMLDataParse($xml);
		$result = json_encode($_GET);
		$this->writeLog($result);
		// wxpay_jsapi
//		if ($this->payment_code == 'wxpay_jsapi') {
//			$api = $this->_get_payment_api();
//			$params = $this->_get_payment_config();
//			$api->setConfigs($params);
//			list($result, $output) = $api->notify();
//			if ($result) {
//                $this->writeLog($result);
//				$internalSn = $result['out_trade_no'] . '_' . $result['attach'];
//				$externalSn = $result['transaction_id'];
//				//  手机充值
//				$updateSuccess = $this->_update_order($internalSn, $externalSn, $params);
//				$this->writeLog($updateSuccess);
//				if (!$updateSuccess["state"]) {
//					// @todo
//					// 直接退出 等待下次通知
//					exit;
//				}
//			}
//
//			echo $output;
//			exit;
//		}
//		log::record(unserialize($_POST), 'LOG');
		// 恢复框架编码的post值
//		$_POST['notify_data'] = html_entity_decode($_POST['notify_data']);
//		$inc_file = BASE_PATH . DS . 'api' . DS . 'payment' . DS . 'wxpay' . DS . 'wxpay' . '.php';
//		if (is_file($inc_file)) {
//			require $inc_file;
//		}
//		$wxpay = new wxpay();
//		$payment_config = $this->_get_payment_config();
//		$callback_info = $wxpay->getNotifyInfo($payment_config);
//		$this->writeLog(json_encode($callback_info));
		//if ($callback_info) {
        $string=substr($_GET['out_trade_no'],0, 4);
	    //$this->writelog($string);
        if($string=='RDCT'){
            //$this->writeLog(1);
            //验证成功
            $result =$this->City_master_order($_GET['out_trade_no']);
        }else{
            //$this->writeLog(2);
            $result = $this->_update_order($_GET['out_trade_no']);
        }
        if ($result['state']) {
            //$this->writeLog("ok");
            ob_clean();//清除输出缓存
            echo 'success';
        }
		//验证失败
		echo "fail";
		die;
	}

	/**
	 * 支付宝移动支付
	 */
	public function notify_alipay_nativeOp() {
		$this->payment_code = 'alipay_native_app';
		$inc_file = BASE_ROOT_PATH . DS . 'mobile' . DS . 'api' . DS . 'payment' . DS . 'alipay_native' . DS . 'alipay_native_app' . '.php';
		require_once $inc_file;
		//$payment_config = $this->_get_payment_config();
		$payment_api = new $this->payment_code();
		$_POST['fund_bill_list'] = htmlspecialchars_decode($_POST['fund_bill_list']);
		//商户订单号
		$out_trade_no = $_POST['out_trade_no'];
		//支付宝交易号
		$trade_no = $_POST['trade_no'];
		Log::record("订单号：" . $out_trade_no);
		Log::record("alipay_reback:" . serialize($_POST));
//		logResult("订单号1" . $out_trade_no);
		//		logResult("支付宝订单号1" . $trade_no);
		if ($payment_api->notify_verify($_POST)) {
			//交易状态
			$trade_status = $_POST['trade_status'];
			if ($_POST['trade_status'] == 'TRADE_FINISHED' || $_POST['trade_status'] == 'TRADE_SUCCESS') {
				$order_pay_info = Model('order')->getOrderPayInfo(array('pay_sn' => $out_trade_no));

				if ($order_pay_info['order_state'] == 20) {
					exit("success");
				}
				$result = $this->_update_order($out_trade_no, $trade_no);
//				logResult("订单号" . $out_trade_no);
				//				logResult("支付宝订单号" . $trade_no);
				//反金豆
				Logic('payment')->orderPresentBean($out_trade_no);
//				if (!$result['state']) {
				//					logResult("订单状态更新失败" . $out_trade_no);
				//				}
			}
			exit("success");
		} else {
			//logResult("verifyNotify验证失败" . $out_trade_no);
			exit("fail");
		}
	}

	/**
	 * 获取支付接口实例
	 */
	private function _get_payment_api() {
		$inc_file = BASE_PATH . DS . 'api' . DS . 'payment' . DS . $this->payment_code . DS . $this->payment_code . '.php';
		if (is_file($inc_file)) {
			require $inc_file;
		}
		$payment_api = new $this->payment_code();
		return $payment_api;
	}

	/**
	 * 获取支付接口信息
	 */
	private function _get_payment_config() {
		$model_mb_payment = Model('mb_payment');
		//读取接口配置信息
		$condition = array();
		if ($this->payment_code == 'wxpay3') {
			$condition['payment_code'] = 'wxpay';
		}elseif($this->payment_code == 'wxpay_redbag'){
            $condition['payment_code'] = 'wxpay_redbag';
        } else {
			$condition['payment_code'] = $this->payment_code;
		}
		$payment_info = $model_mb_payment->getMbPaymentOpenInfo($condition);
        return $payment_info['payment_config'];
	}

	/**
	 * 更新红包订单状态
	 */
	private function _update_order($out_trade_no)
    {
//		$model_order = Model('order');
//		$logic_payment = Logic('payment');
        $tmp = explode('_', $out_trade_no);
        $out_trade_no = $tmp[0];
        if (!empty($tmp[1])) {
            $order_type = $tmp[1];
        } else {
            $issue = Model('red_redbag_issue');
            $where['red_envelopes_order_number'] = $out_trade_no;
            $order_list = $issue->getenvelopesfind($where);
            $data_list['payment_state'] = '1';
            $data_list['payment_number'] = $out_trade_no;
            $data_list['payment_method'] = '1';
            $order_pay_info = $issue->modify($where, $data_list);
            if (!$order_pay_info) {
                throw new Exception('订单更新失败');
            } else {
                if ($order_pay_info) {
                    $money_logo = Model('red_money_logo');
                    $dataarray['member_id'] = $order_list['member_id'];
                    $dataarray['member_mobile'] = $order_list['member_mobile'];
                    $dataarray['member_name'] = $order_list['member_name'];
                    $dataarray['sn'] = $order_list['red_envelopes_order_number'];
                    $dataarray['type'] = 'redbag';
                    $dataarray['operate_amount'] = '-' . $order_list['money'];
                    $dataarray['surplus_amount'] = $order_list['balance'];
                    $dataarray['add_time'] = time();
                    $dataarray['content'] = '会员' . $dataarray['member_mobile'] . '发红包:' . $order_list['money'];
                    $money_logo->logoadd($dataarray);
                    responseApiJson(1, '支付已完成', '');
                } else {
                    responseApiJson(0, '支付失败', '');
                }
            }
        }
    }
    /*
     * 更新城主订单
     */
    private function City_master_order($out_trade_no){
        $city_lord_where['order_number']=$out_trade_no;
        $order_list = Model()->table('rob_city_lord')->where($city_lord_where)->find();
        //修改该区域城主现价
        $city_now_price =  Model()->table('city_price')->where(['area'=>$order_list['area_id']])->find();
        if (!empty($city_now_price)){
            Model()->table('city_price')->where(['area'=>$order_list['area_id']])->update(['present_price'=>$order_list['money']]);
        }
        //订单支付完成，修改支付状态
        Model()->table('rob_city_lord')->where(['rob_city_lord_id'=>$order_list['rob_city_lord_id']])->update(['state'=>1,'mode'=>'balancepay']);
        //上个城主订单
        $last_order = Model()->table('rob_city_lord')->where(['member_id'=>$order_list['last_lord_id'],'area_id'=>$order_list['area_id'],'addtime'=>['lt',time()]])->order('addtime desc')->find();
        if (!empty($last_order)){
            //修改上个城主结束时间
            Model()->table('rob_city_lord')->where(['rob_city_lord_id'=>$last_order['rob_city_lord_id']])->update(['endtime'=>time()]);
        }
        //上个城主
        $city_lord =  Model()->table('city_lord')->where(['area'=>$last_order['area_id']])->order('addtime desc')->find();
        if (!empty($city_lord)){
            Model()->table('city_lord')->where(['city_lord_id'=>$city_lord['city_lord_id']])->update(['endtime'=>time()]);
        }
        //城主表添加新城主数据
        $insert['order_number'] = 'RDCZ'.time().rand('0000','9999');
        $insert['area'] = $order_list['area_id'];
        $insert['member_id'] = $order_list['member_id'];
        $insert['addtime'] = time();
        Model()->table('city_lord')->insert($insert);
        Db::commit();
        //上次城主金额返还
        if (!empty($order_list['last_lord_id'])){
            $data_raward = array();
            $data_raward['member_id'] = $order_list['last_lord_id'];
            $data_raward['member_name'] = Model('member')->getMemberNameById($order_list['rob_city_lord_id']);
            $data_raward['order_sn'] = $order_list['order_number'];
            $primary_money = $this-> _get_subsidy($order_list['last_lord_money'],$order_list['money']);
            $data_raward['amount'] = $primary_money;
            $data_raward['type'] = 'city';
            $raward = Model('member_extend')->changeMemberBalance('city',$data_raward);
            if (isset($raward['error'])) {
                throw new Exception('城主被抢占，金额返还失败' . $raward['error']);
            }
        }
        responseApiJson(1, "支付成功", '');
    }

    /*
     * 上个城主分利
     */
    private  function  _get_subsidy($last_lord_money,$money)
    {
        $benefits=Model('red_city_lord_share_benefits');
        $databenefits=$benefits->red_getRow('1');
        $primary_money = $last_lord_money+($money*$databenefits['city_rebate'])/100;
        return $primary_money;
    }

//		// wxpay_jsapi
//		$paymentCode = $this->payment_code;
//		if ($paymentCode == 'wxpay_jsapi') {
//			$paymentCode = 'wx_jsapi';
//		} elseif ($paymentCode == 'wxpay3') {
//			$paymentCode = 'wxpay';
//		} elseif ($paymentCode == 'alipay_native') {
//			$paymentCode = 'ali_native';
//		}
//
//		if ($order_type == 'r') {
//			$result = $logic_payment->getRealOrderInfo($out_trade_no);
//			if (intval($result['data']['api_pay_state'])) {
//				return array('state' => true);
//			}
//			$order_list = $result['data']['order_list'];
//			$result = $logic_payment->updateRealOrder($out_trade_no, $paymentCode, $order_list, $trade_no);
//
//			$api_pay_amount = 0;
//			if (!empty($order_list)) {
//				foreach ($order_list as $order_info) {
//					$api_pay_amount += $order_info['order_amount'] - $order_info['pd_amount'] - $order_info['rcb_amount'];
//				}
//			}
//			$log_buyer_id = $order_list[0]['buyer_id'];
//			$log_buyer_name = $order_list[0]['buyer_name'];
//			$log_desc = '实物订单使用' . orderPaymentName($paymentCode) . '成功支付，支付单号：' . $out_trade_no;
//
//		} elseif ($order_type == 'v') {
//			$result = $logic_payment->getVrOrderInfo($out_trade_no);
//			$order_info = $result['data'];
//			if (!in_array($result['data']['order_state'], array(ORDER_STATE_NEW, ORDER_STATE_CANCEL))) {
//				return array('state' => true);
//			}
//			$result = $logic_payment->updateVrOrder($out_trade_no, $paymentCode, $result['data'], $trade_no);
//
//			$api_pay_amount = $order_info['order_amount'] - $order_info['pd_amount'] - $order_info['rcb_amount'];
//			$log_buyer_id = $order_info['buyer_id'];
//			$log_buyer_name = $order_info['buyer_name'];
//			$log_desc = '虚拟订单使用' . orderPaymentName($paymentCode) . '成功支付，支付单号：' . $out_trade_no;
//		} elseif ($order_type == 'pd') {
//
//			$result = $logic_payment->getPdOrderInfo($out_trade_no);
//
//			if (!$result['state']) {
//
//				return array('state' => true);
//
//			}
//
//			if ($result['data']['pdr_payment_state'] == 0) {
//
//				$result = $logic_payment->updatePdOrder($out_trade_no, $trade_no, $payment_info, $result['data']);
//
//				$payment_state = 'success';
//
//			}
//			return $result;

//		}
//		if ($result['state']) {
//			//记录消费日志
//			QueueClient::push('addConsume', array('member_id' => $log_buyer_id, 'member_name' => $log_buyer_name,
//				'consume_amount' => ncPriceFormat($api_pay_amount), 'consume_time' => TIMESTAMP, 'consume_remark' => $log_desc));
//		}

//		return $result;
//	}

	public function syncCallbackOp() {
		unset($_GET['act']);
		unset($_GET['op']);
		unset($_GET['payment_code']);

		// $payment_api = $this->_get_payment_api();

		// $payment_config = $this->_get_payment_config();

		// $callback_info = $payment_api->getReturnInfo($payment_config);

		if ($callback_info) {
			//验证成功
			$result = $this->_update_order($callback_info['out_trade_no'], $callback_info['trade_no']);
			if ($result['state']) {
				Tpl::output('result', 'success');
				Tpl::output('message', '支付成功');
			} else {
				Tpl::output('result', 'fail');
				Tpl::output('message', '支付失败');
			}
		} else {
			//验证失败
			Tpl::output('result', 'fail');
			Tpl::output('message', '支付失败');
		}

		Tpl::showpage('payment_message');
	}
	
	    //xml格式数据解析函数
    public function XMLDataParse($data){
        $xml = simplexml_load_string($data,'SimpleXMLElement', LIBXML_NOCDATA);
        //$array=json_decode(json_encode($xml),true);
        return $xml;
    }
	
	//记录日志(调试用)
	public function writeLog($content){
		$log_path ='../../'.date('y-m-d') . '_log.txt';
		file_put_contents($log_path, $content . "\n", FILE_APPEND);
	}

}
