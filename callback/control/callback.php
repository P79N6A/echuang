<?php
/**
 * 回调处理类
 *
 */

class callbackControl extends CallbackBaseControl {
	public function __construct() {
		if (isset($_GET['payment_code']) && $_GET['payment_code']) {
			// 支付方式
			$payment_info = Model('payment_list')->getPaymentOpenInfo(['payment_code' => $_GET['payment_code']]);
			if (empty($payment_info)) {
				$this->writeLog("系统不支持所选支付方式");
				exit('fail');
			}
			Plugin([$payment_info['payment_float'], $payment_info['payment_subfloat'], $payment_info['payment_class']]);
			$this->payment_info = $payment_info;
		} else {
			$this->writeLog('缺少payment_code参数');
			exit('fail');
		}
	}

	// 异步回调通知
	public function asynNotifyOp() {
		$fail = 'fail';
		$success = 'success';
		if (empty($_POST)) {
			$this->writeLog('缺少回调数据');
			exit($fail);
		}
		$notify_data = array_merge(array(), $_POST);
		if (isset($notify_data['fund_bill_list'])) {
			$notify_data['fund_bill_list'] = htmlspecialchars_decode($notify_data['fund_bill_list']);
		}
		$this->writeLog("*********验签开始*********");
		// 创建支付接口对象
		$payment_class = $this->payment_info['payment_class'];
		$payment_obj = new $payment_class();
		$verify = $payment_obj->verify($notify_data);
		if (!$verify) {
			$this->writeLog("验签失败");
			$this->writeLog("*********end*********");
			exit($fail);
		} else {
			$this->writeLog("验签成功");
			$this->writeLog("*********end*********");
		}

		$out_trade_no = $notify_data['out_trade_no'];
		$trade_no = $notify_data['trade_no'];

		// 参数判断
		if (!preg_match('/^\d{18}$/', $out_trade_no)) {
			$this->writeLog('out_trade_no参数格式错误');
			exit($fail);
		}

		// 订单信息
		$logic_payment = Logic('payment');
		$result = $logic_payment->getRealOrderInfo($out_trade_no);
		if (!$result['state']) {
			$this->writeLog('订单号不存在');
			exit($fail);
		}

		if (intval($result['data']['api_pay_state'])) {
			exit($success);
		}

		$order_list = $result['data']['order_list'];
		$order_pay_amount = 0;
		if (!empty($order_list)) {
			foreach ($order_list as $order_info) {
				$order_pay_amount += floatval($order_info['order_amount']);
			}
		}

		// 验证支付金额与订单金额
		if ($notify_data['total_amount'] != $order_pay_amount) {
			$this->writeLog('支付金额与订单金额不同');
			exit($fail);
		}

		// 购买商品
		$result = $logic_payment->updateRealOrder($out_trade_no, $this->payment_info['payment_code'], $order_list, $trade_no);

		if ($result['state']) {
			// 记录消费日志
			$log_buyer_id = $order_list[0]['buyer_id'];
			$log_buyer_name = $order_list[0]['buyer_name'];
			$log_desc = '实物订单使用' . orderPaymentName($payment_info['payment_code']) . '成功支付，支付单号：' . $out_trade_no;

			QueueClient::push('addConsume', array('member_id' => $log_buyer_id, 'member_name' => $log_buyer_name,
				'consume_amount' => ncPriceFormat($api_pay_amount), 'consume_time' => TIMESTAMP, 'consume_remark' => $log_desc));
		}

		exit($success);
	}

	// 同步回调通知
	public function syncNotifyOp() {
        $fail = 'fail';
        $success = 'success';
        if (empty($_POST)) {
            $this->writeLog('缺少回调数据');
            exit($fail);
        }
        $notify_data = array_merge(array(), $_POST);
        if (isset($notify_data['fund_bill_list'])) {
            $notify_data['fund_bill_list'] = htmlspecialchars_decode($notify_data['fund_bill_list']);
        }
        $this->writeLog("*********验签开始*********");
        // 创建支付接口对象
        $payment_class = $this->payment_info['payment_class'];
        $payment_obj = new $payment_class();
        $verify = $payment_obj->verify($notify_data);
        if (!$verify) {
            $this->writeLog("验签失败");
            $this->writeLog("*********end*********");
            exit($fail);
        } else {
            $this->writeLog("验签成功");
            $this->writeLog("*********end*********");
        }

        $out_trade_no = $notify_data['out_trade_no'];
        $trade_no = $notify_data['trade_no'];

        // 参数判断
        if (!preg_match('/^\d{18}$/', $out_trade_no)) {
            $this->writeLog('out_trade_no参数格式错误');
            exit($fail);
        }

        // 订单信息
        $logic_payment = Logic('payment');
        $result = $logic_payment->getRealOrderInfo($out_trade_no);
        if (!$result['state']) {
            $this->writeLog('订单号不存在');
            exit($fail);
        }

        if (intval($result['data']['api_pay_state'])) {
            exit($success);
        }

        $order_list = $result['data']['order_list'];
        $order_pay_amount = 0;
        if (!empty($order_list)) {
            foreach ($order_list as $order_info) {
                $order_pay_amount += floatval($order_info['order_amount']);
            }
        }

        // 验证支付金额与订单金额
        if ($notify_data['total_amount'] != $order_pay_amount) {
            $this->writeLog('支付金额与订单金额不同');
            exit($fail);
        }

        // 购买商品
        $result = $logic_payment->updateRealOrder($out_trade_no, $this->payment_info['payment_code'], $order_list, $trade_no);

        if ($result['state']) {
            // 记录消费日志
            $log_buyer_id = $order_list[0]['buyer_id'];
            $log_buyer_name   = $order_list[0]['buyer_name'];
            $log_desc = '实物订单使用' . orderPaymentName($payment_info['payment_code']) . '成功支付，支付单号：' . $out_trade_no;

            QueueClient::push('addConsume', array('member_id' => $log_buyer_id, 'member_name' => $log_buyer_name,
                'consume_amount' => ncPriceFormat($api_pay_amount), 'consume_time' => TIMESTAMP, 'consume_remark' => $log_desc));
            $result = $logic_payment->orderPresentBean($out_trade_no);
        }

        exit($result['state'] ? $success : $fail);
	}

	// 写入记录文件
	public function writeLog($content) {
		$log_path = BASE_PATH . DS . 'log' . DS . date('y-m-d') . '_log1.txt';
		file_put_contents($log_path, $content . "\n", FILE_APPEND);
	}
}