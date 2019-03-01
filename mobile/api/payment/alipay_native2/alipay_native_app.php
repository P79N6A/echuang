<?php
/**
 * 支付宝接口类
 *
 */
defined('In33hao') or exit('Access Invalid!');
require_once dirname(__FILE__) . DS . 'service' . DS . 'AlipayTradeService.php';
require_once dirname(__FILE__) . DS . 'buildermodel' . DS . 'AlipayTradePagePayContentBuilder.php';

class alipay_native_app {

	public $pay_name = 'alipay';

	// 商户订单号
	public $out_trade_no;

	// 订单名称，必填
	public $subject;

	// 付款金额
	public $total_amount;

	// 商品描述，可空
	public $body;

	public $config = array(
		// 应用ID,您的APPID。
		'app_id' => "2018062260356889",

		// 商户私钥
		'merchant_private_key' => "MIIEowIBAAKCAQEArMP17ykxSHKQ8+2apLS2oH/qtj1y1S/asjzp3ZK2HMkLULX8G+BrpC6v5keDk1VLiuWAQbSc5xuRp+oP/yAfXh1ZcAqbF81KSlyAdUCWsJITSed7DDjU28+dTPYLCVUtfEEJuMOErxbs/d0WjVdruecLEZGoiDnTrQZQMj8S1lQuPPjf4dhBnp+GQrQVdYgPgGlUXevfbjCAk8rtF1gA8CI+C0S0k1PCfMMVKQeXgt7OZzNp/3FZdvBrEs+4pgp8Bx6eTZdC2QG9LsgA5iI33qTa08ftwC2Dn0fYGw9UkTwjZzGXPYnsBGHn4WvulUOrat2UXgEIgB1pVlUAsNPPEQIDAQABAoIBAG8+36XYiHt2ChCH9wQHuXEv7RrcBo1v+wq6Xnzmc42lhzu5c1Uf9TALQAiwE/FTAwm1LhkhC8dk4S2WciZ9sLURgP28VpHFBwlK9wyJQn8AEJuOCpWcIrRHw6pC5GhmCJvx5Sir/UwhHwl4y4THvf4+38m4WSUe10aawhkYtDB9hLgquqO+7mDyc4QSE+jfGA3Rvxo2SPTSMRxH07lYf2xGSbjfAZSBoZIv3w/hGOiRAEnKD/5Zhr7Y/EggvhssfG6MLxMpoAINl6pOaXVWQNVrNAd6KxykpbEE+57PfC+iD7IC52+t05lfv3g1MPTXu7GgcfPGpNxVQZV5AzbjuoECgYEA4MdVjyIho7nqH2UoSG8vOLOE7tWH9wu76EgXP5563yhZvxkvwxDgeBhGsQEBorPt8nKVqls+RWjwZXwnmEl1wU1Y6ldtS6nH+kbnuPiEGh+5Gg8Ya7WzGOzOeNysJlomNxsUDe3t2uJScwCluY4RoUfHs5PP0MX2v2lrR/krvk0CgYEAxMMlHkk9eJLt3VaMSUtW8NTjiq7wh+LyKBoMlgZOND3fXbUGkDuHAXnhFcfVkPhsUP6yRC0xWU5OFsL7wXXNH+TdKGQU+yRFsINhZgsHcGuFP0WYJiF7toQ1QVNh5opgCJ1XFcq/t5k7UsLZ2uThR/UBLqf5c7FzgCwiA1u+3dUCgYBt6Gnog+ro8KYO1dqdmGgBlJ3iXlyOKpml2WIsxGQWK9e+PiWBfwYTEuO19uUC1nH5w5d05gqLy+UH6m4942WFsDlS1I7nmUvkD7mMU0/cqjhLCqiJOh4piNjodhZi5l+bQE1NBlLwXQutmdpu4Y2zS7LVFuhdj/6312cSmT6PXQKBgQCpvdOSqgo+rzpGnLhPLpBlGLLnW/2irJTNJZPABNaIWOwN8fIkkUXpbxRPbuJ9uuHuiZqOGBKfgV76YeE/xvlbT1u+0YAMHAGGwgBPQKp9QDRmHgqgsngAP1OV9HHbqXopVFrhMNRtdeJohjHclWEHa4RGlKAtP2NtqiT5krc4tQKBgAiuNNPP9TOxoir27pgK307ok+LEBgZr7s1GvwzHsazMVaLzZWnRPSLJYosN0Lp8CmAOjmu8WKlm3JfJUOLZh67xInuG/WJgSqFAx3jRsN+VxanR+GdnlADmLaMbY9oFmN6BWmmPMuJhoq4s1y6iowU9BVsx+A3BoqgL8yWVb2c6",

		// 异步通知地址
		'notify_url' => "http://121.196.201.195/mobile/api/payment/alipay_native/notify_url.php",

		// 同步跳转
		'return_url' => "http://121.196.201.195/shop/api/payment/alipay/return_url.php",

		// 编码格式
		'charset' => "UTF-8",

		// 签名方式
		'sign_type' => "RSA2",

		// 支付宝网关
		'gatewayUrl' => "https://openapi.alipay.com/gateway.do",

		// 支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
		'alipay_public_key' => "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAq5+Kxs3+yNYRvw2NjbWEENsIoy0uehQ+3UZcoQ83/VqmAh5TJPjDRw+pR+8uuvTP46oVq/Wp+LmAfwddyMJilTuY0MnocB7VCtv0GE86tfXCPZutquAVZq7RSdk8xDkPWQZumAc3WPeBLyhi9LW8Eg4fVyH4D+Z/M5mFTzZDEGmKntjF8eF2O1lk3QiHWiyWFXOIvtOm/w2q2fkwbujTmgHQn9SMxVsr2ufvn2SYz6y4FYPsB9fKy/9nXDgG6WYIX6wu82nSzR2rWrfEDEusG2sX/uF0kli3r/O7uz8d8lOwCXGIYMIhiRdenTLPHyqqqW2gMlEBULdzC2VpsNdtmQIDAQAB",
	);

	public function __construct() {
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