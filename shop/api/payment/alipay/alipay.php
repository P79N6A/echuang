<?php
/**
 * 支付宝接口类
 *
 */
defined('In33hao') or exit('Access Invalid!');
require_once dirname(__FILE__) . DS . 'service' . DS . 'AlipayTradeService.php';
require_once dirname(__FILE__) . DS . 'buildermodel' . DS . 'AlipayTradePagePayContentBuilder.php';

class alipay {

	public $pay_name = 'alipay';

	// 商户订单号
	public $out_trade_no;

	// 订单名称，必填
	public $subject;

	// 付款金额
	public $total_amount;

	// 商品描述，可空
	public $body;

	// 支付信息
	public $payment;

	// 订单信息
	public $order;

	public $config = array(
		// 应用ID,您的APPID。
		'app_id' => "2018112362287482",

		// 商户私钥
		'merchant_private_key' => "MIIEpQIBAAKCAQEAnP0avJ+npRfEL6A1XVCxmnxA4yvXEKtpesdBQ3yT1IQeuKshXMg8MOuyLORpprkBm2DxlMC5jZbJGVa1CZXTLP7XV8g7e6kqxbcbVbG6u10Or9fkR9Wnzkr7cjsV3FMkmaapLqbISGlKlwi9RR/+6bv8agj1gJr1qA0+32iaBFOelDpaJIDTkszUClcRyE7AR6zSMyR0KiYUxlp+cIKFk5E2HZxQZ/QSwu+bcueufkMki3lVpwUwfzn1MVVjSWdehKViu34QIQks/OZ9Rn4sC1RFxN1URb3Ej/eo5XKgxFUEr5xxFVMhHwuWoCezWPEzaz5N7131ho4sBuG1NqOUWQIDAQABAoIBAFH+K9m6dT/X0uKhBV4wiCJMpUNBIXmWXWf/68lIpplHTBhdYPZpqm5Om+ewgLHQTNOPZX2wzvjxeSTf0XwpIXuNuoEhlle2+hz7zq4KPqIfL02OL6+JGrS3Gu8Fhxxk3GfX0OMJvu969rc9nEUfOe9v/JyRA1BhHs7f5CehJVf+VxmK+M96Pfhcmm8fpgrUyMnjLp0plMPU5I3qKLGy/80YlZp+x/0YoYNYicn22OkmxWU9F1hvW5EXN5hUrYDPk1phHwjHrHV0Lrbs8PZxVeDscCIJrKgVpbMc2AbD3qiPalqwS/d40WLv9j6MgpMc0BNXa+at17/4Nt0Unn5ob1ECgYEAycuXOnGrRRweoblIXFUN6lWjBrhY3hUrxIjA2xdqWOkvHOTIJ4jpjLQEPBenpSknVitYDTnbiB537G8DXgQs+kUjgNbKbTU4qe/RrGeUV7kN+/PnTgKhAQk8voak4t7O4QAWbkdp3te1Z+Zog3DUCU53cn4d68l6ceQdfsQt+DUCgYEAxyhl9SWfQV16sooP46UHDjZ067vzgJ8abPBrKpAHvyv2Ti4yun0yGSI8xfj6ceeFSVLGutY5FkOdHrp3IgBQzhd9rv3krqHjCzD0Dv0gYQuHKT3R41Y75o9Vnk3P5IAKVQBrW7WbHIqQKJ5tgAo5EUA9GVaRHXZTXCjUDnTcWBUCgYEAxm+b8O7iWv2B6crHq3RAUNWRkQQq74imF4fbDheCjpgPqjwXzzjFYYWMPIOOac2ETyokVi01Ai/kAe5Qw10b1aezd/i/6pBRflrENuNMbs8dP0vjg0T2ioxI3ZqN/kLX8U4iwKMdKvO4AcoDsG19NQGiWEZIqhrge6q0jnngMpkCgYEAnOcG7IwnW6f58o8y1XY+iMP+eXop7Ttr5kfDJlUyxTotYoqjDKl1vrdqic4OAY8VQx8j0i7T6uZ58quVEASPpM59BlCgybRg24BFx0lFKS+b3LIHmcNmjc5+9aj4EF2Pxkf+TNJW0bWYvWeTfTyQmTay7DN3QG7oXcMMzhbjQcECgYEAhnrPHI4XTtqeuKptU9V3ztFtGZOwBdvXRPC8Q70Ra/OjTsEJqsy/REibPtA5uByqeT3Gt6Rap2APXg2FQiTWH5rNwUFSh5Q4elhXD5M0d2w8H28hXl1G3oIqAeN61mhZv01KZx6OY+PEuGxLEeTL4BGqu0cn7O8XQO1i3cQRiEE=",

		// 异步通知地址
		'notify_url' => "http://shop.divona.xin/shop/api/payment/alipay/notify_url.php",

		// 同步跳转
		'return_url' => "http://shop.divona.xin/shop/api/payment/alipay/return_url.php",

		// 编码格式
		'charset' => "UTF-8",

		// 签名方式
		'sign_type' => "RSA2",

		// 支付宝网关
		'gatewayUrl' => "https://openapi.alipay.com/gateway.do",

		// 支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
		'alipay_public_key' => "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAq5+Kxs3+yNYRvw2NjbWEENsIoy0uehQ+3UZcoQ83/VqmAh5TJPjDRw+pR+8uuvTP46oVq/Wp+LmAfwddyMJilTuY0MnocB7VCtv0GE86tfXCPZutquAVZq7RSdk8xDkPWQZumAc3WPeBLyhi9LW8Eg4fVyH4D+Z/M5mFTzZDEGmKntjF8eF2O1lk3QiHWiyWFXOIvtOm/w2q2fkwbujTmgHQn9SMxVsr2ufvn2SYz6y4FYPsB9fKy/9nXDgG6WYIX6wu82nSzR2rWrfEDEusG2sX/uF0kli3r/O7uz8d8lOwCXGIYMIhiRdenTLPHyqqqW2gMlEBULdzC2VpsNdtmQIDAQAB",
	);

	public function __construct($payment_info = array(), $order_info = array()) {
		if (!empty($payment_info) and !empty($order_info)) {
			$this->payment = $payment_info;
			$this->order = $order_info;
		}
	}

	public function set_subject($subject) {
		$this->subject = $subject;
	}

	public function set_out_trade_no($out_trade_no) {
		$this->out_trade_no = $out_trade_no;
	}

	public function set_total_amount($total_amount) {
		$this->total_amount = floatval($total_amount);
	}

	public function set_body($body) {
		$this->body = $body;
	}

	/**
	 * get_alipay_response description
	 * @return [type] [description]
	 */
	public function get_alipay_response() {
		$subject = trim($this->order['subject']);
		$this->set_subject($subject);
		$out_trade_no = $this->order['pay_sn'];
		$this->set_out_trade_no($out_trade_no);
		$body = "实物订单";
		$this->set_body($body);
		$total_amount = 0;
		$order_list = $this->order['order_list'];
		foreach ($order_list as $order_info) {
			$total_amount = $order_info['order_amount'] - $order_info['bean_amount'];
		}
		$this->set_total_amount($total_amount);
		$respose = '
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html>
			<head>
				<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
				<title>支付</title>
			</head>';
		// 构造参数
		$payRequestBuilder = new AlipayTradePagePayContentBuilder();
		$payRequestBuilder->setBody($this->body);
		$payRequestBuilder->setSubject($this->subject);
		$payRequestBuilder->setTotalAmount($this->total_amount);
		$payRequestBuilder->setOutTradeNo($this->out_trade_no);

		$aop = new AlipayTradeService($this->config);
		$response .= $aop->pagePay($payRequestBuilder, $this->config['return_url'], $this->config['notify_url']);
		$response .= '</body>
					</html>';
		return $response;
	}

	/**
	 * 通知地址验证
	 *
	 * @return bool
	 */
	public function notify_verify($param) {
		$aop = new AlipayTradeService($this->config);
		$result = $aop->check($param);
		return $result;
	}

	public function return_verify($param) {
		$aop = new AlipayTradeService($this->config);
		$result = $aop->check($param);
		return $result;
	}
}