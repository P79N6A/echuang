<?php
/**
 * 支付回调
 *
 */

defined('In33hao') or exit('Access Invalid!');

class callbackControl extends mobileHomeControl {

	private $payment_code;

	public function __construct() {
		parent::__construct();
		if (isset($_GET['payment_code'])) {
			$this->payment_code = $_GET['payment_code'];

		}
	}

	// 同步回调
	public function syncCallbackOp() {
		unset($_GET['act']);
		unset($_GET['op']);
		unset($_GET['payment_code']);
		$callback_data = array_merge(array(), $_GET);
		$result = $this->checkPayState($callback_data);
		if ($result['state']) {
			Tpl::output('result', 'success');
			Tpl::output('message', '支付成功');
		} else {
			Tpl::output('result', 'fail');
			Tpl::output('message', $result['msg']);
		}
		Tpl::showpage('payment_message');
	}

	public function checkPayState($callback_data) {
		$msg = '';
		$payment_info = Model('payment_list')->getPaymentOpenInfo(['payment_code' => $this->payment_code]);
		if (empty($payment_info)) {
			$msg = "系统不支持所选支付方式";
			return ['state' => $state, 'msg' => $msg];
		}
		Plugin([$payment_info['payment_float'], $payment_info['payment_subfloat'], $payment_info['payment_class']]);

		if (isset($callback_data['fund_bill_list'])) {
			$callback_data['fund_bill_list'] = htmlspecialchars_decode($callback_data['fund_bill_list']);
		}

		// 创建支付接口对象
		$payment_obj = new $this->payment_code();
		$verify = $payment_obj->verify($callback_data);
		if (!$verify) {
			$msg = "验签失败";
			$this->writeLog($msg);
			return ['state' => $state, 'msg' => $msg];
		}
		$out_trade_no = $callback_data['out_trade_no'];
		$trade_no = $callback_data['trade_no'];

		// 参数判断
		if (!preg_match('/^\d{18}$/', $out_trade_no)) {
			return ['state' => 0, 'msg' => "out_trade_no参数格式错误"];
		}
		// 订单信息
		$logic_payment = Logic('payment');
		$result = $logic_payment->getRealOrderInfo($out_trade_no);
		if (!$result['state']) {
			return ['state' => 0, 'msg' => "订单号不存在"];
		}

		if (intval($result['data']['api_pay_state'])) {
			return ['state' => 1, 'msg' => ''];
		}

		$order_list = $result['data']['order_list'];
		$order_pay_amount = 0;
		if (!empty($order_list)) {
			foreach ($order_list as $order_info) {
				$order_pay_amount += floatval($order_info['order_amount']);
			}
		}

		// 验证支付金额与订单金额
		if ($callback_data['total_amount'] != $order_pay_amount) {
			return ['state' => 0, 'msg' => "支付金额与订单金额不同"];
		}

		// 购买商品
		$result = $logic_payment->updateRealOrder($out_trade_no, $this->payment_code, $order_list, $trade_no);

		if ($result['state']) {
			// 记录消费日志
			$log_buyer_id = $order_list[0]['buyer_id'];
			$log_buyer_name = $order_list[0]['buyer_name'];
			$log_desc = '实物订单使用' . orderPaymentName($payment_info['payment_code']) . '成功支付，支付单号：' . $out_trade_no;

			QueueClient::push('addConsume', array('member_id' => $log_buyer_id, 'member_name' => $log_buyer_name,
				'consume_amount' => ncPriceFormat($order_pay_amount), 'consume_time' => TIMESTAMP, 'consume_remark' => $log_desc));
			$result = $logic_payment->orderPresentBean($out_trade_no);
			return ['state' => 1, 'msg' => ''];
		}

	}

	// 写入记录文件
	public function writeLog($content) {
		$log_path = BASE_PATH . DS . 'log' . DS . date('y-m-d') . '_log.txt';
		file_put_contents($log_path, $content . "\n", FILE_APPEND);
	}

}