<?php
/**
 * 微信扫码支付
 *
 */

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . './../../pay.interface.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . './../lib/WxPay.Api.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . './../WxPay.NativePay.php';

class wxpay_wap{
	private $data;

	public function __construct($data) {
		$this->data = $data;
	}

	public function doPay() {
		$notify = new NativePay();
		$input = new WxPayUnifiedOrder();
		$input->SetBody($this->data['body']);
		$input->SetOut_trade_no($this->data['out_trade_no']);
		$input->SetTotal_fee($this->data['total_amount'] * 100);
		// $input->SetTime_start(date("YmdHis"));
		// $input->SetTime_expire(date("YmdHis", (time() + 600)));
		$input->SetNotify_url($this->data['notify_url']);
		$input->SetTrade_type("MWEB");
		$input->SetProduct_id($this->data['subject']);
		$result = $notify->GetPayUrl($input);
		return $result;
	}
}

$data = array(
	'body' => "测试",
	'subject' => 'test1111',
	'out_trade_no' => date('YmdHis') . rand(1000, 9999),
	'total_amount' => 0.01,
	'notify_url' => "http://paysdk.weixin.qq.com/notify.php",
);

$wxpay = new wxpay_wap($data);
$result = $wxpay->doPay();
//var_dump($result);