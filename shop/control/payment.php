<?php
/**
 * 支付入口
 *
 *
 * * (c)  ()
 * @license
 * @link
 * @since
 */
defined('In33hao') or exit('Access Invalid!');

class paymentControl extends BaseHomeControl {

	public function __construct() {
		Language::read('common,home_layout');
	}

	/**
	 * 实物商品订单
	 */
	public function real_orderOp() {
		$pay_sn = $_POST['pay_sn'];
		$payment_code = $_POST['payment_code'];//支付类型标识
		//$payment_code=str_replace("_scan","",$payment_code);
		//var_dump($payment_code);die;
		$url = 'index.php?act=member_order';

		if (!preg_match('/^\d{18}$/', $pay_sn)) {
			showMessage('参数错误', '', 'html', 'error');
		}

		// 取订单列表
		$logic_payment = Logic('payment');
		$order_pay_info = $logic_payment->getRealOrderInfo($pay_sn, $_SESSION['member_id']);
		if (!$order_pay_info['state']) {
			showMessage($order_pay_info['msg'], $url, 'html', 'error');
		}

		$order_list = $order_pay_info['data']['order_list'];
		$pay_amount = 0;
		$api_pay_amount = 0;
		$pay_order_id_list = array();
		if (!empty($order_list)) {
			foreach ($order_list as $order_info) {
				if ($order_info['order_state'] == ORDER_STATE_NEW) {
					$pay_order_id_list[] = $order_info['order_id'];
					$api_pay_amount += $order_info['order_amount'] - $order_info['bean_amount'];
				}
				$pay_amount += $order_info['order_amount'];
			}
		}
		if (empty($api_pay_amount)) {
			redirect(SHOP_SITE_URL . '/index.php?act=buy&op=pay_ok&pay_sn=' . $order_pay_info['data']['pay_sn'] . '&pay_amount=' . ncPriceFormat($pay_amount));
		}

		if ($payment_code == 'beanpay') {
			$result = $this->_bean_pay($order_pay_info['data']['order_list'], $_POST);
			if (isset($result['error'])) {
				showMessage('订单支付失败', $url, 'html', 'error');
			} else {
				redirect(SHOP_SITE_URL . '/index.php?act=buy&op=pay_ok&pay_sn=' . $order_pay_info['data']['pay_sn'] . '&pay_amount=' . ncPriceFormat($pay_amount));
			}
		} elseif ($payment_code == 'silverpay') {
			$result = $this->_silver_bean_pay($order_pay_info['data']['order_list'], $_POST);
			if (isset($result['error'])) {
				showMessage('订单支付失败', $url, 'html', 'error');
			} else {
				redirect(SHOP_SITE_URL . '/index.php?act=buy&op=pay_ok&pay_sn=' . $order_pay_info['data']['pay_sn'] . '&pay_amount=' . ncPriceFormat($pay_amount));
			}
		} elseif ($payment_code == 'balancepay') {
            $result = $this->_balance_pay($order_pay_info['data']['order_list'], $_POST);
            if (isset($result['error'])) {
                showMessage('订单支付失败', $url, 'html', 'error');
            } else {

                redirect(SHOP_SITE_URL . '/index.php?act=buy&op=pay_ok&pay_sn=' . $order_pay_info['data']['pay_sn'] . '&pay_amount=' . ncPriceFormat($pay_amount));
            }
        }else {
			// 计算本次需要在线支付（分别是含站内支付、纯第三方支付接口支付）的订单总金额
			$result = Model('order')->editOrder(array('api_pay_time' => TIMESTAMP), array('order_id' => array('in', $pay_order_id_list)));
			if (!$result) {
				showMessage('更新订单信息发生错误，请重新支付', $url, 'html', 'error');
			}
			//获取支付配置信息
			$result = $logic_payment->getPaymentInfo($payment_code);
			if (!$result['state']) {
				showMessage($result['msg'], $url, 'html', 'error');
			}
			$payment_info = $result['data'];

			$order_pay_info['data']['api_pay_amount'] = ncPriceFormat($api_pay_amount);

			// 如果是开始支付尾款，则把支付单表重置了未支付状态，因为支付接口通知时需要判断这个状态
			if ($order_pay_info['data']['if_buyer_repay']) {
				$update = Model('order')->editOrderPay(array('api_pay_state' => 0), array('pay_id' => $order_pay_info['data']['pay_id']));
				if (!$update) {
					showMessage('订单支付失败', $url, 'html', 'error');
				}
				$order_pay_info['data']['api_pay_state'] = 0;
			}
			// 转到第三方API支付
			
			$this->_api_pay($order_pay_info['data'], $payment_info);
		}

	}

	/**
	 * 虚拟商品购买
	 */
	public function vr_orderOp() {
		$order_sn = $_POST['order_sn'];
		$payment_code = $_POST['payment_code'];
		$url = 'index.php?act=member_vr_order';

		if (!preg_match('/^\d{18}$/', $order_sn)) {
			showMessage('参数错误', '', 'html', 'error');
		}

		//计算所需支付金额等支付单信息
		$result = Logic('payment')->getVrOrderInfo($order_sn, $_SESSION['member_id']);
		if (!$result['state']) {
			showMessage($result['msg'], $url, 'html', 'error');
		}

		//站内余额支付
		$order_info = $this->_pd_vr_pay($result['data'], $_POST);
		if ($order_info['order_state'] == ORDER_STATE_PAY) {
			//发送兑换码到手机
			$param = array('order_id' => $order_info['order_id'], 'buyer_id' => $order_info['buyer_id'], 'buyer_phone' => $order_info['buyer_phone'], 'goods_name' => $order_info['goods_name']);
			QueueClient::push('sendVrCode', $param);
		}

		//计算本次需要在线支付金额
		$api_pay_amount = 0;
		if ($order_info['order_state'] == ORDER_STATE_NEW) {
			$api_pay_amount = floatval(ncPriceFormat($order_info['order_amount'] - $order_info['pd_amount'] - $order_info['rcb_amount']));
		}

		//如果所需支付金额为0，转到支付成功页
		if (empty($api_pay_amount)) {
			redirect('index.php?act=buy_virtual&op=pay_ok&order_sn=' . $order_info['order_sn'] . '&order_id=' . $order_info['order_id'] . '&order_amount=' . ncPriceFormat($order_info['order_amount']));
		}

		$result = Model('vr_order')->editOrder(array('api_pay_time' => TIMESTAMP), array('order_id' => $order_info['order_id']));
		if (!$result) {
			showMessage('更新订单信息发生错误，请重新支付', $url, 'html', 'error');
		}

		$result = Logic('payment')->getPaymentInfo($payment_code);
		if (!$result['state']) {
			showMessage($result['msg'], $url, 'html', 'error');
		}
		$payment_info = $result['data'];

		$order_info['api_pay_amount'] = ncPriceFormat($api_pay_amount);

		// 转到第三方API支付
		$this->_api_pay($order_info, $payment_info);
	}

	/**
	 * 预存款充值
	 */
	public function pd_orderOp() {
		$pdr_sn = $_POST['pdr_sn'];
		$payment_code = $_POST['payment_code'];
		$url = urlMember('predeposit');

		if (!preg_match('/^\d{18}$/', $pdr_sn)) {
			showMessage('参数错误', $url, 'html', 'error');
		}

		$logic_payment = Logic('payment');
		$result = $logic_payment->getPaymentInfo($payment_code);
		if (!$result['state']) {
			showMessage($result['msg'], $url, 'html', 'error');
		}
		$payment_info = $result['data'];

		$result = $logic_payment->getPdOrderInfo($pdr_sn, $_SESSION['member_id']);
		if (!$result['state']) {
			showMessage($result['msg'], $url, 'html', 'error');
		}
		if ($result['data']['pdr_payment_state'] || empty($result['data']['api_pay_amount'])) {
			showMessage('该充值单不需要支付', $url, 'html', 'error');
		}

		//转到第三方API支付
		$this->_api_pay($result['data'], $payment_info);
	}

	/**
	 * _bean_pay 金豆支付
	 * @param  [type] $order_list [description]
	 * @param  [type] $post       [description]
	 * @return [type]             [description]
	 */
	private function _bean_pay($order_list, $post) {
		if (empty($post['password'])) {
			return array('error' => "参数错误");
		}
		$model_member = Model('member');
		$buyer_info = $model_member->getMemberInfoByID($_SESSION['member_id']);
		if ($buyer_info['member_paypwd'] == '' || $buyer_info['member_paypwd'] != md5($post['password'])) {
			return array('error' => "用户支付密码不正确");
		}

		try {
			$logic_buy_1 = Logic('buy_1');
			$result = $logic_buy_1->beanPay($order_list, $post, $buyer_info);
			return array('succ' => "支付成功");
		} catch (Exception $exception) {
			return array('error' => "支付失败");
		}

	}

	/**
	 * _silver_bean_pay 银豆支付
	 * @param  [type] $order_list [description]
	 * @param  [type] $post       [description]
	 * @return [type]             [description]
	 */
	private function _silver_bean_pay($order_list, $post) {
		if (empty($post['password'])) {
			return array('error' => "参数错误");
		}
		$model_member = Model('member');
		$buyer_info = $model_member->getMemberInfoByID($_SESSION['member_id']);
		if ($buyer_info['member_paypwd'] == '' || $buyer_info['member_paypwd'] != md5($post['password'])) {
			return array('error' => "用户支付密码不正确");
		}

		try {
			$logic_buy_1 = Logic('buy_1');
			$result = $logic_buy_1->sliver_beanPay($order_list, $post, $buyer_info);
			return array('succ' => "支付成功");
		} catch (Exception $exception) {
			return array('error' => "支付失败");
		}

	}

    private function _balance_pay($order_list, $post) {
        if (empty($post['password'])) {
            return array('error' => "参数错误");
        }
        $model_member = Model('member');
        $buyer_info = $model_member->getMemberInfoByID($_SESSION['member_id']);
        if ($buyer_info['member_paypwd'] == '' || $buyer_info['member_paypwd'] != md5($post['password'])) {
            return array('error' => "用户支付密码不正确");
        }

        try {
            $logic_buy_1 = Logic('buy_1');
            $result = $logic_buy_1->balancePay($order_list, $post, $buyer_info);
            //获取报单商品数量
            $product_num = 0;
            foreach ($order_list as $k=>$order_info){
                $res = Model('goods')->getProductNum($order_info['pay_sn']);
                $product_num += $res['product_num'];
            }
            for ($i=1;$i<=(int)$product_num;$i++){
                $diff_profit = Logic('member_level')->buyReportProduct($res['member_id'],$res['pay_sn']);
            }
//            var_dump($diff_profit);die();
            return array('succ' => "支付成功");
        } catch (Exception $exception) {
            return array('error' => "支付失败");
        }

    }
	
	/**
	 * 站内余额支付(充值卡、预存款支付) 实物订单
	 *
	 */
	private function _pd_pay($order_list, $post) {
		if (empty($post['password'])) {
			return $order_list;
		}
		$model_member = Model('member');
		$buyer_info = $model_member->getMemberInfoByID($_SESSION['member_id']);
		if ($buyer_info['member_paypwd'] == '' || $buyer_info['member_paypwd'] != md5($post['password'])) {
			return $order_list;
		}

		if ($buyer_info['available_rc_balance'] == 0) {
			$post['rcb_pay'] = null;
		}
		if ($buyer_info['available_predeposit'] == 0) {
			$post['pd_pay'] = null;
		}
		if (floatval($order_list[0]['rcb_amount']) > 0 || floatval($order_list[0]['pd_amount']) > 0) {
			return $order_list;
		}

		try {
			$model_member->beginTransaction();
			$logic_buy_1 = Logic('buy_1');
			//使用充值卡支付
			if (!empty($post['rcb_pay'])) {
				$order_list = $logic_buy_1->rcbPay($order_list, $post, $buyer_info);
			}

			//使用预存款支付
			if (!empty($post['pd_pay'])) {
				$order_list = $logic_buy_1->pdPay($order_list, $post, $buyer_info);
			}

			//特殊订单站内支付处理
			$logic_buy_1->extendInPay($order_list);

			$model_member->commit();
		} catch (Exception $e) {
			$model_member->rollback();
			showMessage($e->getMessage(), '', 'html', 'error');
		}

		return $order_list;
	}

	/**
	 * 站内余额支付(充值卡、预存款支付) 虚拟订单
	 *
	 */
	private function _pd_vr_pay($order_info, $post) {
		if (empty($post['password'])) {
			return $order_info;
		}
		$model_member = Model('member');
		$buyer_info = $model_member->getMemberInfoByID($_SESSION['member_id']);
		if ($buyer_info['member_paypwd'] == '' || $buyer_info['member_paypwd'] != md5($post['password'])) {
			return $order_info;
		}

		if ($buyer_info['available_rc_balance'] == 0) {
			$post['rcb_pay'] = null;
		}
		if ($buyer_info['available_predeposit'] == 0) {
			$post['pd_pay'] = null;
		}
		if (floatval($order_info['rcb_amount']) > 0 || floatval($order_info['pd_amount']) > 0) {
			return $order_info;
		}

		try {
			$model_member->beginTransaction();
			$logic_buy = Logic('buy_virtual');
			//使用充值卡支付
			if (!empty($post['rcb_pay'])) {
				$order_info = $logic_buy->rcbPay($order_info, $post, $buyer_info);
			}

			//使用预存款支付
			if (!empty($post['pd_pay'])) {
				$order_info = $logic_buy->pdPay($order_info, $post, $buyer_info);
			}

			$model_member->commit();
		} catch (Exception $e) {
			$model_member->rollback();
			showMessage($e->getMessage(), '', 'html', 'error');
		}

		return $order_info;
	}

	/**
	 * 第三方在线支付接口
	 *
	 */
	private function _api_pay($order_info, $payment_info) {
		$payment_api = new $payment_info['payment_code']($payment_info, $order_info);
		if ($payment_info['payment_code'] == 'chinabank') {
			$payment_api->submit();
		} elseif ($payment_info['payment_code'] == 'wxpay') {
			if (!extension_loaded('curl')) {
				showMessage('系统curl扩展未加载，请检查系统配置', '', 'html', 'error');
			}
			Tpl::setDir('buy');
			Tpl::setLayout('buy_layout');
			if (array_key_exists('order_list', $order_info)) {
				Tpl::output('order_list', $order_info['order_list']);
				Tpl::output('args', 'buyer_id=' . $_SESSION['member_id'] . '&pay_id=' . $order_info['pay_id']);
			} elseif (array_key_exists('pdr_id', $order_info)) {
				Tpl::output('order_list', array($order_info));
				Tpl::output('args', 'pdr_member_id=' . $_SESSION['member_id'] . '&pdr_id=' . $order_info['pdr_id']);
			} else {
				Tpl::output('order_list', array($order_info));
				Tpl::output('args', 'buyer_id=' . $_SESSION['member_id'] . '&order_id=' . $order_info['order_id']);
			}
			Tpl::output('api_pay_amount', $order_info['api_pay_amount']);

			Tpl::output('pay_url', base64_encode(encrypt($payment_api->get_payurl(), MD5_KEY)));
			Tpl::output('nav_list', rkcache('nav', true));
			Tpl::showpage('payment.wxpay');
		} else {
			//跳转至支付dome文件中
			$response = $payment_api->get_alipay_response();
			//var_dump($response);die();
			// die;
			// @header("Location: " . $payment_api->get_alipay_response());
		}
		exit();
	}

	/**
	 * notifyOp 通知处理(支付宝异步通知和网银在线自动对账)
	 * @return [type] [description]
	 */
	public function notifyOp() {
		switch ($_GET['payment_code']) {
		case 'alipay':
			$success = 'success';
			$fail = 'fail';
			break;
		case 'chinabank':
			$success = 'ok';
			$fail = 'error';
			break;
		default:
			exit();
		}
		//$file_liubinbin = BASE_PATH . DS . 'api' . DS . 'payment' . DS . 'alipay.txt';
		
		$trade_status = $_POST["trade_status"];
		if($trade_status == "TRADE_SUCCESS"){
			//$content = implode("---",$_POST);
			
			//file_put_contents($file_liubinbin, $content,FILE_APPEND);
			
			$model_orders = Model('order');
			$wheres = array(
				"pay_sn" => $_POST["out_trade_no"],
			);
			$model_orders->updateAlipayOrdersStart($wheres);
		}
			
		$notify_data = null;
		if ($_POST) {
			$notify_data = $_POST;
		} else {
			exit($fail);
		}
		//获取支付金额、支付方式
		$notify_data['fund_bill_list'] = htmlspecialchars_decode($notify_data['fund_bill_list']);
		$str = null;

		$this->writeLog($str . "\r\n");
		$this->writeLog("***********" . date('Y-m-d H:i:s') . "***********\r\n");
		foreach ($notify_data as $key => $value) {
			$str .= $key . ' => ' . $value . "\r\n";

		}
		$this->writeLog($str);
		$this->writeLog("***********end***********\r\n");
		//商户订单号
		$out_trade_no = $notify_data['out_trade_no'];
		//支付宝交易号
		$trade_no = $notify_data['trade_no'];

		//参数判断
		if (!preg_match('/^\d{18}$/', $out_trade_no)) {
			exit($fail);
		}

		$model_pd = Model('predeposit');

		$logic_payment = Logic('payment');

		$result = $logic_payment->getRealOrderInfo($out_trade_no);

		if (!$result['state']) {
			exit($fail);
		}

		if (intval($result['data']['api_pay_state'])) {
			exit($success);
		}

		$order_list = $result['data']['order_list'];
		$api_pay_amount = 0;
		if (!empty($order_list)) {
			foreach ($order_list as $order_info) {
				$api_pay_amount += $order_info['order_amount'] - $order_info['bean_amount'];
			}
		}

		$order_pay_info = $result['data'];

		// 取得支付方式
		$result = $logic_payment->getPaymentReturnInfo($_GET['payment_code']);
		if (!$result['state']) {
			exit($fail);
		}

		$payment_info = $result['data'];
		$this->writeLog("*********验签开始*********\r\n");

		// 创建支付接口对象
		$api_payment = new $payment_info['payment_code']($payment_info, $order_pay_info);

		$verify = $api_payment->notify_verify($notify_data);
		if (!$verify) {
			$this->writeLog("验签失败\r\n");
			$this->writeLog("*********end*********\r\n");
			exit($fail);
		} else {
			$this->writeLog("验签成功\r\n");
			$this->writeLog("*********end*********\r\n");
		}

		// 购买商品
		$result = $logic_payment->updateRealOrder($out_trade_no, $payment_info['payment_code'], $order_list, $trade_no);
		

		if ($result['state']) {
			// 记录消费日志
			$log_buyer_id = $order_list[0]['buyer_id'];
			$log_buyer_name = $order_list[0]['buyer_name'];
			$log_desc = '实物订单使用' . orderPaymentName($payment_info['payment_code']) . '成功支付，支付单号：' . $out_trade_no;

			QueueClient::push('addConsume', array('member_id' => $log_buyer_id, 'member_name' => $log_buyer_name,
				'consume_amount' => ncPriceFormat($api_pay_amount), 'consume_time' => TIMESTAMP, 'consume_remark' => $log_desc));
			$result = $logic_payment->orderPresentBean($out_trade_no);
		}
		
		exit($result['state'] ? $success : $fail);
	}

	public function notify_testOp() {
		$notify_json = '{"gmt_create":"2018-04-17 14:09:52","charset":"UTF-8","gmt_payment":"2018-04-17 14:09:57","notify_time":"2018-04-17 14:09:57","subject":"\u5b9e\u7269\u8ba2\u5355_800577289382820008","sign":"bRJRj\/BD9TDlCY2SoMY1n74xB0Smtk8glveldW0fAhVYYyABLT6XelrmGkM8Lm5S8y6ok7GsfYAkUF0raqR8Cc1hGYmX8FTKW9+2SPlKcYqweBzSGmOrz+WznwsKJdzEfwoCvNDQA8XmdSqnMwU6iOD27LE1vhdvShyWc7m170HwlyCJWHSmGwhow1AZ2\/zRMRC\/5EN8A\/5pnve85141JBcnrGSsPvuE7Fmp\/5pxUF2nce6+kqApveeLBzeUkopkN21Y7zrmxkIGsaSHlZHBa7k\/yRELOCI0Zzh3W+9YaHFaUedJqIPTiLNtCkAYUMyMTrYE4uerMi8Gt2Zky6QxjA==","buyer_id":"2088412733623335","body":"\u5b9e\u7269\u8ba2\u5355","invoice_amount":"0.01","version":"1.0","notify_id":"58cdc3fd6de76ff0fcd0c172462d470ijt","fund_bill_list":"[{&quot;amount&quot;:&quot;0.01&quot;,&quot;fundChannel&quot;:&quot;ALIPAYACCOUNT&quot;}]","notify_type":"trade_status_sync","out_trade_no":"800577289382820008","total_amount":"0.01","trade_status":"TRADE_SUCCESS","trade_no":"2018041721001004330575484867","auth_app_id":"2018041502561126","receipt_amount":"0.01","point_amount":"0.00","app_id":"2018041502561126","buyer_pay_amount":"0.01","sign_type":"RSA2","seller_id":"2088031924675964"}';
		$param = json_decode($notify_json);
		$_POST = $param;
		// $this->notifyOp();
		$url = "http://120.79.232.46/shop/api/payment/alipay/notify_url.php";
		$response = $this->postParams($url, $param);
		var_dump($response);die;
		//dump($response);

	}
	

	/**
	 * 支付接口返回
	 *
	 */
	public function returnOp() {
		$payment_code = $_GET['payment_code'];
		unset($_GET['act']);
		unset($_GET['op']);
		unset($_GET['payment_code']);
		$return_data = $_GET;

		$url = SHOP_SITE_URL . "/index.php?act=member_order";
		if (!$return_data['out_trade_no'] || !$return_data['trade_no'] || !preg_match('/^\d{18}$/', $return_data['out_trade_no'])) {
			showMessage('参数错误', $url, '', 'html', 'error');
		}
		$out_trade_no = $return_data['out_trade_no'];
		$trade_no = $return_data['trade_no'];

		$logic_payment = Logic('payment');
		$order_result = $logic_payment->getRealOrderInfo($out_trade_no);
		if (!$order_result['state']) {
			showMessage($order_result['msg'], $url, 'html', 'error');
		}

		// 取得支付方式
		$payment_result = $logic_payment->getPaymentReturnInfo($payment_code);
		if (!$payment_result['state']) {
			showMessage($payment_result['msg'], $url, 'html', 'error');
		}

		$payment_info = $payment_result['data'];
		$order_list = $order_result['data']['order_list'];

		// 支付成功页面展示在线支付了多少金额
		$order_result['data']['api_pay_amount'] = 0;
		if (!empty($order_list)) {
			foreach ($order_list as $order_info) {
				$order_result['data']['api_pay_amount'] += $order_info['order_amount'] - $order_info['bean_amount'];
			}
		}
		$order_pay_info = $order_result['data'];
		$api_pay_amount = $order_result['data']['api_pay_amount'];

		if (!$order_result['data']['api_pay_state']) {
			// 创建支付接口对象
			$payment_api = new $payment_info['payment_code']($payment_info, $order_pay_info);

			// 返回参数判断
			$verify = $payment_api->return_verify($return_data);
			if (!$verify) {
				showMessage('支付数据验证失败', $url, 'html', 'error');
			}

			$result = $logic_payment->updateRealOrder($out_trade_no, $payment_info['payment_code'], $order_list, $trade_no);

			if (!$result['state']) {
				showMessage('支付状态更新失败', $url, 'html', 'error');
			} else {
				$log_buyer_id = $order_list[0]['buyer_id'];
				$log_buyer_name = $order_list[0]['buyer_name'];
				$log_desc = '实物订单使用' . orderPaymentName($payment_info['payment_code']) . '成功支付，支付单号：' . $out_trade_no;
				QueueClient::push('addConsume', array('member_id' => $log_buyer_id, 'member_name' => $log_buyer_name, 'consume_amount' => ncPriceFormat($api_pay_amount), 'consume_time' => TIMESTAMP, 'consume_remark' => $log_desc));
				$result = $logic_payment->orderPresentBean($out_trade_no);
			}

		}
		// 支付成功后跳转
		$pay_ok_url = SHOP_SITE_URL . '/index.php?act=buy&op=pay_ok&pay_sn=' . $out_trade_no . '&pay_amount=' . ncPriceFormat($api_pay_amount);
		redirect($pay_ok_url);
	}

	/**
	 * 二维码显示(微信扫码支付)
	 */
	public function qrcodeOp() {
		$data = base64_decode($_GET['data']);
		$data = decrypt($data, MD5_KEY, 30);
		require_once BASE_RESOURCE_PATH . '/phpqrcode/phpqrcode.php';
		QRcode::png($data);
	}

	/**
	 * 接收微信请求，接收productid和用户的openid等参数，执行（【统一下单API】返回prepay_id交易会话标识
	 */
	public function wxpay_returnOp() {
		$result = Logic('payment')->getPaymentInfo('wxpay');
		if (!$result['state']) {
			Log::record('wxpay not found', 'RUN');
		}
		new wxpay($result['data'], array());
		require_once BASE_PATH . '/api/payment/wxpay/native_notify.php';
	}

	/**
	 * 支付成功，更新订单状态
	 */
	public function wxpay_notifyOp() {
		$datas = array();
        $xml = file_get_contents("php://input");
        //$this->writeLog($xml);
        $xml = simplexml_load_string($xml1, 'SimpleXMLElement', LIBXML_NOCDATA);
        foreach ($xml as $k => $v) {
            $datas[$k] = $v;
        }
        $out_trade_no = $datas['out_trade_no'];
        $file  = BASE_PATH . DS . 'api' . DS . 'payment' . DS . 'wx_pc.txt';
        $content = $out_trade_no;
        file_put_contents($file, $content."__",FILE_APPEND);
        if($out_trade_no){
            $order_model = Model('order');
            $order_model->updateWxPayStart($out_trade_no);
        }
//        $inc_file = BASE_PATH . DS . 'api' . DS . 'payment' . DS . 'wxpay' . DS . 'wxpay' . '.php';
//        if (is_file($inc_file)) {
//            require $inc_file;
//        }
        $logic_payment = Logic('payment');
        $result = $logic_payment->getRealOrderInfo($out_trade_no);
//        if (intval($result['data']['api_pay_state'])) {
//            return array('state' => true);
//        }
        $log_path = BASE_PATH . DS . date('y-m-d') . '_log1852369.txt';

        $order_list = $result['data']['order_list'];
        $result = $logic_payment->updateRealOrder($out_trade_no[0], 'wxpay', $order_list, $datas['trade_no']);
        $data['order_state'] = 20;
        $data['payment_time'] = time();
        $data['payment_code'] = 'wxpay';
        $condition['pay_sn'] = $out_trade_no[0];
        $res = Model('order')->editOrder($data, $condition, $limit = '');
        if ($result['state']) {
            ob_clean();//清除输出缓存
            echo 'success';
            //判断是否为报单产品
            //获取该订单报单产品的数量
            $data = Model('goods')->getProductNum($out_trade_no);
            $member_id = $data['member_id'];
            if ($data['product_num']) {
                for ($i = 1; $i <= (int)$data['product_num']; $i++) {
                    $res = Logic('member_level')->buyReportProduct($member_id,$out_trade_no);
                }
            }
            die;
        }
        //验证失败
        echo "fail";
        die;
//        $result = Logic('payment')->getPaymentInfo('wxpay');
//
//        if (!$result['state']) {
//			Log::record('wxpay not found', 'RUN');
//		}
//		new wxpay($result['data'], array());
//		require_once BASE_PATH . '/api/payment/wxpay/notify.php';
	}

	public function query_stateOp() {
		if ($_GET['pay_id'] && intval($_GET['pay_id']) > 0) {
			$info = Model('order')->getOrderPayInfo(array('pay_id' => intval($_GET['pay_id']), 'buyer_id' => intval($_GET['buyer_id'])));
			exit(json_encode(array('state' => ($info['api_pay_state'] == '1'), 'pay_sn' => $info['pay_sn'], 'type' => 'r')));
		} elseif (intval($_GET['order_id']) > 0) {
			$info = Model('vr_order')->getOrderInfo(array('order_id' => intval($_GET['order_id']), 'buyer_id' => intval($_GET['buyer_id'])));
			exit(json_encode(array('state' => ($info['order_state'] == '20'), 'pay_sn' => $info['order_sn'], 'type' => 'v')));
		} elseif (intval($_GET['pdr_id']) > 0) {
			$info = Model('predeposit')->getPdRechargeInfo(array('pdr_id' => intval($_GET['pdr_id']), 'pdr_member_id' => intval($_GET['pdr_member_id'])));
			exit(json_encode(array('state' => ($info['pdr_payment_state'] == '1'), 'pdr_sn' => $info['pdr_sn'], 'type' => 'pd')));
		}
	}

	/**
	 *  请确保项目文件有可写权限
	 */
	public function writeLog($text) {
		$log_path = BASE_DATA_PATH . DS . 'log' . DS . $_GET['payment_code'] . DS . 'log.txt';
		// $text = iconv("GBK", "UTF-8//IGNORE", $text . "\r\n");
		file_put_contents($log_path, $text, FILE_APPEND);
	}

	/**
	 * 以post方式提交到对应的接口url
	 *
	 * @param string $url  url
	 * @param array $date  需要post的数据
	 * @param bool $useCert 是否需要证书，默认不需要
	 * @param int $second   url执行超时时间，默认30s
	 *
	 * @return string   返回结果
	 * @throws WxPayException
	 */
	public function postParams($url, $params, $second = 6, $json = false, $useCert = false, $sslCertPath = '', $sslKeyPath = '', $curlProxyHost = '0.0.0.0', $curlProxyPort = 0) {

		// DI()->logger->debug('postParams', $params);
		// DI()->logger->debug('curl url', $url);
		$ch = curl_init();
		//设置超时
		curl_setopt($ch, CURLOPT_TIMEOUT, $second);

		//如果有配置代理这里就设置代理
		if ($curlProxyHost != "0.0.0.0"
			&& $curlProxyPort != 0) {
			curl_setopt($ch, CURLOPT_PROXY, $curlProxyHost);
			curl_setopt($ch, CURLOPT_PROXYPORT, $curlProxyPort);
		}
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); //2 严格校验 ,不想验证也可设为0
		//设置header
		curl_setopt($ch, CURLOPT_HEADER, false);
		//要求结果为字符串且输出到屏幕上
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		if ($useCert == true) {
			curl_setopt($ch, CURLOPT_VERBOSE, '1'); //debug模式，方便出错调试
			//设置证书
			//使用证书：cert 与 key 分别属于两个.pem文件
			curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');
			//DI()->logger->debug('certPath',dirname(dirname(dirname(__FILE__))) . '/' . $this->config['sslCertPath']);
			curl_setopt($ch, CURLOPT_SSLCERT, $sslCertPath);
			//curl_setopt($ch, CURLOPT_SSLCERTPASSWD,"Abcd1234");
			curl_setopt($ch, CURLOPT_SSLKEYTYPE, 'PEM');
			curl_setopt($ch, CURLOPT_SSLKEY, $sslKeyPath);
			//curl_setopt($ch, CURLOPT_SSLKEYPASSWD,"Abcd1234");
		}
		if ($json && is_array($params)) {
			$params = json_encode($params);
		}
		//设置post方式提交
		curl_setopt($ch, CURLOPT_POST, 1);
		//设置post数据
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);

		if ($json) {
			//发送JSON数据
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_HTTPHEADER,
				array('Content-Type: application/json; charset=utf-8',
					'Content-Length:' . strlen($params),
				)
			);
		}
		// } else {
		//     curl_setopt($ch, CURLOPT_HTTPHEADER,
		//         array('Content-Type: application/x-www-form-urlencoded; charset=utf-8'));
		// }
		//运行curl
		$data = curl_exec($ch);
		//返回结果
		if ($data) {
			curl_close($ch);
			// DI()->logger->debug('curl reback data', $data);
			return $data;
		} else {
			$error = curl_errno($ch);
			curl_close($ch);
			// DI()->logger->error("curl出错", "错误码:$error");
			return false;
		}
	}

}
