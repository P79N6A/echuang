<?php
/**
 * 支付
 *
 *  (c) ()
 * @license    
 * @link       
 * @since      
 */

defined('In33hao') or exit('Access Invalid!');

class member_paymentControl extends apiEncryptMemberControl {

	private $payment_code;
	private $payment_config;

	public function __construct() {
		parent::__construct();

		if ($_GET['op'] != 'payment_list' && !$_POST['payment_code']) {
			$payment_code = 'alipay';

			if (in_array($_GET['op'], array('wx_app_pay', 'wx_app_pay3', 'wx_app_vr_pay', 'wx_app_vr_pay3'), true)) {
				$payment_code = 'wxpay';
			} else if (in_array($_GET['op'], array('alipay_native_pay', 'alipay_native_vr_pay'), true)) {
				$payment_code = 'alipay_native';
			} else if (isset($_GET['payment_code'])) {
				$payment_code = $_GET['payment_code'];
			}

			$model_mb_payment = Model('mb_payment');
			$condition = array();
			$condition['payment_code'] = $payment_code;
			$mb_payment_info = $model_mb_payment->getMbPaymentOpenInfo($condition);
//            if(!$mb_payment_info) {
			//                responseApiJson(0,'支付方式未开启','');
			//            }

			$this->payment_code = $payment_code;
			$this->payment_config = $mb_payment_info['payment_config'];

		}
	}

	/**
	 * 实物订单支付 新方法
	 */
	public function pay_newOp() {
//		$payment_code = $_POST['payment_code'];
//        var_dump($payment_code);die();
		//var_dump($_POST['payment_code']);die();
//	    $_POST['product_num'] = $_GET['product_num'];
//	    if (empty($_POST['product_num'])){
//            responseApiJson(0, 'num error', '');
//        }elseif($_POST['product_num'] == 1){
//            responseApiJson(0, 'num 1', '');
//        }
		@header("Content-type: text/html; charset=" . CHARSET);
		$pay_sn = $_POST['pay_sn'];
//		$pay_param['pay_sn']=$pay_sn;
		//		$pay_infoma=Model('order')->getOrderInfo($pay_param);
		//		if($pay_infoma['order_state']=='20'){
		//			responseApiJson(0,'订单重复支付','');
		//		}
//		if (!preg_match('/^\d{18}$/', $pay_sn)) {
//			responseApiJson(0, '支付单号错误', '');
//		}
//		var_dump($_POST['payment_code']);die();
		if (in_array($_POST['payment_code'], array('alipay', 'wxpay', 'beanpay', 'sliver_beanpay','balancepay'))) {
			if ($_POST['payment_code'] == "beanpay") {
				$pay_info = $this->_get_real_order_info($pay_sn, $_POST);
			}
			if ($_POST['payment_code'] == "sliver_beanpay") {
				$pay_info = $this->_get_real_order_info($pay_sn, $_POST);
			}
			if ($_POST['payment_code'] == "alipay") {
				$pay_info = $this->alipay_native_payOp($pay_sn);
			}
			if ($_POST['payment_code'] == "wxpay") {
				$pay_info = $this->wx_app_payOp($pay_sn);
			}
            if ($_POST['payment_code'] == "balancepay") {
                $pay_info = $this->_get_real_order_info($pay_sn, $_POST);
            }
			if (isset($pay_info['error'])) {
				responseApiJson(0, $pay_info['error'], '');
			}
		} else {
            responseApiJson(0, '暂无该支付方式11', '');
		}
//		$pay_info = $this->_get_real_order_info($pay_sn, $_POST);
		//		if (isset($pay_info['error'])) {
		//			exit($pay_info['error']);
		//		}

//        //第三方API支付
		//        $this->_api_pay($pay_info['data']);

	}

	/**
	 * 取得支付宝移动支付 订单信息 实物订单
	 */
	public function alipay_native_payOp() {
	    $pay_sn = $_POST['pay_sn'];
//		if (!preg_match('/^\d+$/', $pay_sn)) {
//			responseApiJson(0, '支付单号错误', '');
//		}
//        responseApiJson(0, $pay_sn, '');
		$pay_info = $this->_get_real_order_info($pay_sn);

		if (isset($pay_info['error'])) {
			responseApiJson(0, $pay_info['error'], '');
		}

		$inc_file = '../mobile' . DS . 'api' . DS . 'payment' . DS . 'alipay_native' . DS . 'alipay_native' . '.php';
		if (!is_file($inc_file)) {
			exit('支付接口不存在');
		}
		require $inc_file;
		$pay_info['data']['order_type'] = 'r';
		$payment_api = new alipay_native();
		$payment_api->init($this->payment_config, $pay_info['data']);
		$private_path = "../mobile/api/payment/alipay_native/key/rsa_private_key.pem"; //私钥路径
		//构造业务请求参数的集合(订单信息)
		$content = array();
		$content['product_code'] = "QUICK_MSECURITY_PAY";
		$content['total_amount'] = $pay_info['data']['api_pay_amount'];
		$content['subject'] = "redbag";
		$content['out_trade_no'] = $payment_api->param['out_trade_no'];
		$con = json_encode($content); //$content是biz_content的值,将之转化成json字符串
		//建立请求
		$param['app_id'] = '2018062260356889';
		$param['charset'] = 'utf-8'; //请求使用的编码格式
		$param['biz_content'] = $con;

		$param['method'] = 'alipay.trade.app.pay'; //接口名称，固定值
		$param['notify_url'] = $payment_api->param['notify_url']; //业务请求参数的集合,长度不限,json格式，即前面一步得到的
		$param['sign_type'] = 'RSA2'; //商户生成签名字符串所使用的签名算法类型
		$param['timestamp'] = date("Y-m-d H:i:s", time()); //发送请求的时间
		$param['version'] = '1.0'; //调用的接口版本，固定为：1.0
		$paramStr = $this->getSignContent($param);
		$sign = $this->alonersaSign($paramStr, $private_path, "RSA2", true);
		$param['sign'] = $sign;
		$end_sign = $this->getSignContentUrlencode($param);
		responseApiJson(1, '', array('signStr' => $end_sign));
	}

	/**
	 * 服务订单支付 新方法
	 */
	public function vr_pay_newOp() {
		@header("Content-type: text/html; charset=" . CHARSET);
		$order_sn = $_GET['pay_sn'];
		if (!preg_match('/^\d{18}$/', $order_sn)) {
			exit('订单号错误');
		}
		if (in_array($_GET['payment_code'], array('alipay', 'wxpay_jsapi'))) {
			$model_mb_payment = Model('mb_payment');
			$condition = array();
			$condition['payment_code'] = $_GET['payment_code'];
			$mb_payment_info = $model_mb_payment->getMbPaymentOpenInfo($condition);
			if (!$mb_payment_info) {
				exit('支付方式未开启');
			}

			$this->payment_code = $_GET['payment_code'];
			$this->payment_config = $mb_payment_info['payment_config'];
		} else {
			exit('支付方式提交错误');
		}

		$pay_info = $this->_get_vr_order_info($order_sn, $_GET);
		if (isset($pay_info['error'])) {
			exit($pay_info['error']);
		}

		//第三方API支付
		$this->_api_pay($pay_info['data']);

	}

	/**
	 * _bean_pay 金豆支付
	 * @param  [type] $order_list [description]
	 * @param  [type] $post       [description]
	 * @return [type]             [description]
	 */
	private function _bean_pay($order_list, $post) {
        if (empty($post['password'])) {
            responseApiJson(0, '参数错误', '');
        }
        $model_member = Model('member');
        $buyer_info = $model_member->getMemberInfoByID($this->member_info['member_id']);
        if ($buyer_info['member_paypwd'] == '' || $buyer_info['member_paypwd'] != md5($post['password'])) {
            responseApiJson(0, "用户支付密码不正确", '');
        }
		try {
			$logic_buy_1 = Logic('buy_1');
			$result = $logic_buy_1->beanPay($order_list, $post, $buyer_info);
			if ($result) {
				responseApiJson(1, '支付成功', '');
			} else {
				responseApiJson(0, '支付失败', '');
			}
		} catch (Exception $exception) {
			responseApiJson(0, $exception->getMessage(), '');
		}
	}

	/**
	 * _bean_pay 银豆支付
	 * @param  [type] $order_list [description]
	 * @param  [type] $post       [description]
	 * @return [type]             [description]
	 */
	private function _sliver_bean_pay($order_list, $post) {
		if (empty($post['password'])) {
			responseApiJson(0, '参数错误', '');
		}
		$model_member = Model('member');
		$buyer_info = $model_member->getMemberInfoByID($this->member_info['member_id']);
		if ($buyer_info['member_paypwd'] == '' || $buyer_info['member_paypwd'] != md5($post['password'])) {
			responseApiJson(0, "用户支付密码不正确", '');
		}

		try {
			$logic_buy_1 = Logic('buy_1');
			$result = $logic_buy_1->sliver_beanPay($order_list, $post, $buyer_info);
			if ($result) {
				responseApiJson(1, '支付成功', '');
			} else {
				responseApiJson(0, '支付失败', '');
			}
		} catch (Exception $exception) {
			responseApiJson(0, $exception->getMessage(), '');
		}

	}

	/**
	 * 实物订单支付
	 */
	public function payOp() {
		$pay_sn = $_GET['pay_sn'];

		$pay_info = $this->_get_real_order_info($pay_sn);
		if (isset($pay_info['error'])) {
			responseApiJson(0, $pay_info['error']);
		}

		//第三方API支付
		$this->_api_pay($pay_info['data']);
	}

	/**
	 * 服务订单支付
	 */
	public function vr_payOp() {
		$pay_sn = $_GET['pay_sn'];

		$pay_info = $this->_get_vr_order_info($pay_sn);
		if (isset($pay_info['error'])) {
			responseApiJson(0, $pay_info['error']);
		}

		//第三方API支付
		$this->_api_pay($pay_info['data']);
	}

	/**
	 * 第三方在线支付接口
	 *
	 */
	private function _api_pay($order_pay_info) {
		$inc_file = BASE_PATH . DS . 'api' . DS . 'payment' . DS . $this->payment_code . DS . $this->payment_code . '.php';
		if (!is_file($inc_file)) {
			exit('支付接口不存在');
		}
		require $inc_file;
		$param = $this->payment_config;

		// wxpay_jsapi
		if ($this->payment_code == 'wxpay_jsapi') {
			$param['orderSn'] = $order_pay_info['pay_sn'];
			$param['orderFee'] = (int) (ncPriceFormat(100 * $order_pay_info['api_pay_amount']));
			$param['orderInfo'] = $order_pay_info['pay_sn'] . '订单';
			$param['orderAttach'] = ($order_pay_info['order_type'] == 'real_order' ? 'r' : 'v');
			$api = new wxpay_jsapi();
			$api->setConfigs($param);
			try {
				echo $api->paymentHtml($this);
			} catch (Exception $ex) {
				if (C('debug')) {
					header('Content-type: text/plain; charset=utf-8');
					echo $ex, PHP_EOL;
				} else {
					Tpl::output('msg', $ex->getMessage());
					Tpl::showpage('payment_result');
				}
			}
			exit;
		}

		$param['order_sn'] = $order_pay_info['pay_sn'];
		$param['order_amount'] = $order_pay_info['api_pay_amount'];
		$param['order_type'] = ($order_pay_info['order_type'] == 'real_order' ? 'r' : 'v');
		$payment_api = new $this->payment_code();
		$return = $payment_api->submit($param);
		echo $return;
		exit;
	}

	/**
	 * 获取订单支付信息
	 */
	private function _get_real_order_info($pay_sn, $rcb_pd_pay = array()) {
		$logic_payment = Logic('payment');
		//取订单信息
		$result = $logic_payment->getRealOrderInfo($pay_sn, $this->member_info['member_id']);
		if ($result['data']['order_list'][0]['order_state'] == 20) {
			responseApiJson(0, '订单已经支付，不能重复支付', '');
		}
		if (!$result['state']) {
			return array('error' => $result['msg']);
		}
		//金豆支付
		if ($rcb_pd_pay['payment_code'] == 'beanpay') {
			$result['data']['order_list'] = $this->_bean_pay($result['data']['order_list'], $rcb_pd_pay);
		}
		//银豆支付
		if ($rcb_pd_pay['payment_code'] == 'sliver_beanpay') {
			$result['data']['order_list'] = $this->_sliver_bean_pay($result['data']['order_list'], $rcb_pd_pay);
		}
        if ($rcb_pd_pay['payment_code'] == 'sliver_beanpay') {
            $result['data']['order_list'] = $this->_sliver_bean_pay($result['data']['order_list'], $rcb_pd_pay);
        }
        //余额支付
        if ($rcb_pd_pay['payment_code'] == 'balancepay') {
            $result['data']['order_list'] = $this->_balance_pay($result['data']['order_list'], $rcb_pd_pay);
        }
		//计算本次需要在线支付的订单总金额
		$pay_amount = 0;
		$pay_order_id_list = array();
		if (!empty($result['data']['order_list'])) {
			foreach ($result['data']['order_list'] as $order_info) {
				if ($order_info['order_state'] == ORDER_STATE_NEW) {
					$pay_amount += $order_info['order_amount'] - $order_info['pd_amount'] - $order_info['rcb_amount'];
					$pay_amount = floatval(ncPriceFormat($pay_amount));
					$pay_order_id_list[] = $order_info['order_id'];
				}
			}
		}

//        if ($pay_amount == 0) {
		//            redirect(WAP_SITE_URL.'/tmpl/member/order_list.html');
		//        }

		$result['data']['api_pay_amount'] = ncPriceFormat($pay_amount);

		$update = Model('order')->editOrder(array('api_pay_time' => TIMESTAMP), array('order_id' => array('in', $pay_order_id_list)));
		if (!$update) {
			return array('error' => '更新订单信息发生错误，请重新支付');
		}

		//如果是开始支付尾款，则把支付单表重置了未支付状态，因为支付接口通知时需要判断这个状态
		if ($result['data']['if_buyer_repay']) {
			$update = Model('order')->editOrderPay(array('api_pay_state' => 0), array('pay_id' => $result['data']['pay_id']));
			if (!$update) {
				return array('error' => '订单支付失败');
			}
			$result['data']['api_pay_state'] = 0;
		}

		return $result;
	}

	/**
	 * 获取服务订单支付信息
	 */
	private function _get_vr_order_info($pay_sn, $rcb_pd_pay = array()) {
		$logic_payment = Logic('payment');

		//取得订单信息
		$result = $logic_payment->getVrOrderInfo($pay_sn, $this->member_info['member_id']);
		if (!$result['state']) {
			output_error($result['msg']);
		}

		//站内余额支付
		if ($rcb_pd_pay) {
			$result['data'] = $this->_pd_vr_pay($result['data'], $rcb_pd_pay);
		}
		//计算本次需要在线支付的订单总金额
		$pay_amount = 0;
		if ($result['data']['order_state'] == ORDER_STATE_NEW) {
			$pay_amount += $result['data']['order_amount'] - $result['data']['pd_amount'] - $result['data']['rcb_amount'];
		}

		if ($pay_amount == 0) {
			redirect(WAP_SITE_URL . '/tmpl/member/vr_order_list.html');
		}

		$result['data']['api_pay_amount'] = ncPriceFormat($pay_amount);

		$update = Model('order')->editOrder(array('api_pay_time' => TIMESTAMP), array('order_id' => $result['data']['order_id']));
		if (!$update) {
			return array('error' => '更新订单信息发生错误，请重新支付');
		}

		//计算本次需要在线支付的订单总金额
		$pay_amount = $result['data']['order_amount'] - $result['data']['pd_amount'] - $result['data']['rcb_amount'];
		$result['data']['api_pay_amount'] = ncPriceFormat($pay_amount);

		return $result;
	}

	/**
	 * 微信APP订单支付
	 */
	public function wx_app_payOp($pay_sn) {
		$pay_info = $this->_get_real_order_info($pay_sn);
		if (isset($pay_info['error'])) {
			responseApiJson(0, $pay_info['error'], '');
		}
		$param = array();
		$param['pay_sn'] = $pay_sn;
		$param['subject'] = $pay_info['data']['subject'];
		$param['amount'] = $pay_info['data']['api_pay_amount'] * 100;
		$data = $this->aaaOp($param);
		responseApiJson(1, '', $data);
	}

	public function aaaOp($param) {
		$inc_file = '../mobile' . DS . 'api' . DS . 'payment' . DS . 'wxpay' . '.php';
		require_once $inc_file;
		$wxpay = new Wxpay();
		$pre_param = $wxpay->getPrePayOrder($param['subject'], $param['pay_sn'], $param['amount']);
		$pay_param = $wxpay->getPayParam($pre_param);
		return $pay_param;
	}

	/**
	 * 获取支付参数
	 */
	private function _get_wx_pay_info($pay_param) {
		$access_token = $this->_get_wx_access_token();
		if (empty($access_token)) {
			return array('error' => '支付失败code:1001');
		}

		$package = $this->_get_wx_package($pay_param);

		$noncestr = md5($package + TIMESTAMP);
		$timestamp = TIMESTAMP;
		$traceid = $this->member_info['member_id'];

		// 获取预支付app_signature
		$param = array();
		$param['appid'] = $this->payment_config['wxpay_appid'];
		$param['noncestr'] = $noncestr;
		$param['package'] = $package;
		$param['timestamp'] = $timestamp;
		$param['traceid'] = $traceid;
		$app_signature = $this->_get_wx_signature($param);

		// 获取预支付编号
		$param['sign_method'] = 'sha1';
		$param['app_signature'] = $app_signature;
		$post_data = json_encode($param);
		$prepay_result = http_postdata('https://api.weixin.qq.com/pay/genprepay?access_token=' . $access_token, $post_data);
		$prepay_result = json_decode($prepay_result, true);
		if ($prepay_result['errcode']) {
			return array('error' => '支付失败code:1002');
		}
		$prepayid = $prepay_result['prepayid'];

		// 生成正式支付参数
		$data = array();
		$data['appid'] = $this->payment_config['wxpay_appid'];
		$data['noncestr'] = $noncestr;
		$data['package'] = 'Sign=WXPay';
		$data['partnerid'] = $this->payment_config['wxpay_partnerid'];
		$data['prepayid'] = $prepayid;
		$data['timestamp'] = $timestamp;
		$sign = $this->_get_wx_signature($data);
		$data['sign'] = $sign;
		return $data;
	}

	/**
	 * 获取微信access_token
	 */
	private function _get_wx_access_token() {
		// 尝试读取缓存的access_token
		$access_token = rkcache('wx_access_token');
		if ($access_token) {
			$access_token = unserialize($access_token);
			// 如果access_token未过期直接返回缓存的access_token
			if ($access_token['time'] > TIMESTAMP) {
				return $access_token['token'];
			}
		}

		$url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=%s&secret=%s';
		$url = sprintf($url, $this->payment_config['wxpay_appid'], $this->payment_config['wxpay_appsecret']);
		$re = http_get($url);
		$result = json_decode($re, true);
		if ($result['errcode']) {
			return '';
		}

		// 缓存获取的access_token
		$access_token = array();
		$access_token['token'] = $result['access_token'];
		$access_token['time'] = TIMESTAMP + $result['expires_in'];
		wkcache('wx_access_token', serialize($access_token));

		return $result['access_token'];
	}

	/**
	 * 获取package
	 */
	private function _get_wx_package($param) {
		$array = array();
		$array['bank_type'] = 'WX';
		$array['body'] = $param['subject'];
		$array['fee_type'] = 1;
		$array['input_charset'] = 'UTF-8';
		$array['notify_url'] = MOBILE_SITE_URL . '/api/payment/wxpay/notify_url.php';
		$array['out_trade_no'] = $param['pay_sn'];
		$array['partner'] = $this->payment_config['wxpay_partnerid'];
		$array['total_fee'] = $param['amount'];
		$array['spbill_create_ip'] = get_server_ip();

		ksort($array);

		$string = '';
		$string_encode = '';
		foreach ($array as $key => $val) {
			$string .= $key . '=' . $val . '&';
			$string_encode .= $key . '=' . urlencode($val) . '&';
		}

		$stringSignTemp = $string . 'key=' . $this->payment_config['wxpay_partnerkey'];
		$signValue = md5($stringSignTemp);
		$signValue = strtoupper($signValue);

		$wx_package = $string_encode . 'sign=' . $signValue;
		return $wx_package;
	}

	/**
	 * 获取微信支付签名
	 */
	private function _get_wx_signature($param) {
		$param['appkey'] = $this->payment_config['wxpay_appkey'];

		$string = '';

		ksort($param);
		foreach ($param as $key => $value) {
			$string .= $key . '=' . $value . '&';
		}
		$string = rtrim($string, '&');

		$sign = sha1($string);

		return $sign;
	}

	/**
	 * 获取支付参数
	 */
	private function _get_wx_pay_info3($pay_param) {
		$noncestr = md5(rand());

		$param = array();
		$param['appid'] = $this->payment_config['wxpay_appid'];
		$param['mch_id'] = $this->payment_config['wxpay_partnerid'];
		$param['nonce_str'] = $noncestr;
		$param['body'] = $pay_param['subject'];
		$param['out_trade_no'] = $pay_param['pay_sn'];
		$param['total_fee'] = $pay_param['amount'];
		$param['spbill_create_ip'] = get_server_ip();
		$param['notify_url'] = MOBILE_SITE_URL . '/api/payment/wxpay3/notify_url.php';
		$param['trade_type'] = 'APP';

		$sign = $this->_get_wx_pay_sign3($param);
		$param['sign'] = $sign;

		$post_data = '<xml>';
		foreach ($param as $key => $value) {
			$post_data .= '<' . $key . '>' . $value . '</' . $key . '>';
		}
		$post_data .= '</xml>';

		$prepay_result = http_postdata('https://api.mch.weixin.qq.com/pay/unifiedorder', $post_data);
		$prepay_result = simplexml_load_string($prepay_result);
		if ($prepay_result->return_code != 'SUCCESS') {
			return array('error' => '支付失败code:1002');
		}

		// 生成正式支付参数
		$data = array();
		$data['appid'] = $this->payment_config['wxpay_appid'];
		$data['noncestr'] = $noncestr;
		//微信修改接口参数，否则IOS报解析失败
		//$data['package'] = 'prepay_id=' . $prepay_result->prepay_id;
		$data['package'] = 'Sign=WXPay';
		$data['partnerid'] = $this->payment_config['wxpay_partnerid'];
		$data['prepayid'] = (string) $prepay_result->prepay_id;
		$data['timestamp'] = TIMESTAMP;
		$sign = $this->_get_wx_pay_sign3($data);
		$data['sign'] = $sign;
		return $data;
	}

	private function _get_wx_pay_sign3($param) {
		ksort($param);
		foreach ($param as $key => $val) {
			$string .= $key . '=' . $val . '&';
		}
		$string .= 'key=' . $this->payment_config['wxpay_partnerkey'];
		return strtoupper(md5($string));
	}

	//此方法对value做urlencode
	public function getSignContentUrlencode($params) {
		ksort($params);

		$stringToBeSigned = "";
		$i = 0;
		foreach ($params as $k => $v) {
			if (false === $this->checkEmpty($v) && "@" != substr($v, 0, 1)) {

				// 转换成目标字符集
				$v = $this->characet($v, $this->postCharset);

				if ($i == 0) {
					$stringToBeSigned .= "$k" . "=" . urlencode($v);
				} else {
					$stringToBeSigned .= "&" . "$k" . "=" . urlencode($v);
				}
				$i++;
			}
		}

		unset($k, $v);
		return $stringToBeSigned;
	}
	public function alonersaSign($data, $privatekey, $signType = "RSA", $keyfromfile = false) {

		if (!$keyfromfile) {
			$priKey = $privatekey;
			$res = "-----BEGIN RSA PRIVATE KEY-----\n" .
			wordwrap($priKey, 64, "\n", true) .
				"\n-----END RSA PRIVATE KEY-----";
		} else {
			$priKey = file_get_contents($privatekey);
			$res = openssl_get_privatekey($priKey);
		}

		($res) or die('您使用的私钥格式错误，请检查RSA私钥配置');

		if ("RSA2" == $signType) {
			openssl_sign($data, $sign, $res, OPENSSL_ALGO_SHA256);
		} else {
			openssl_sign($data, $sign, $res);
		}

		if ($keyfromfile) {
			openssl_free_key($res);
		}
		$sign = base64_encode($sign);
		return $sign;
	}
	public function getSignContent($params) {
		ksort($params);

		$stringToBeSigned = "";
		$i = 0;
		foreach ($params as $k => $v) {
			if (false === $this->checkEmpty($v) && "@" != substr($v, 0, 1)) {

				// 转换成目标字符集
				$v = $this->characet($v, $this->postCharset);

				if ($i == 0) {
					$stringToBeSigned .= "$k" . "=" . "$v";
				} else {
					$stringToBeSigned .= "&" . "$k" . "=" . "$v";
				}
				$i++;
			}
		}

		unset($k, $v);
		return $stringToBeSigned;
	}

	protected function curl($url, $postFields = null) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_FAILONERROR, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		$postBodyString = "";
		$encodeArray = Array();
		$postMultipart = false;

		if (is_array($postFields) && 0 < count($postFields)) {

			foreach ($postFields as $k => $v) {
				if ("@" != substr($v, 0, 1)) //判断是不是文件上传
				{

					$postBodyString .= "$k=" . urlencode($this->characet($v, $this->postCharset)) . "&";
					$encodeArray[$k] = $this->characet($v, $this->postCharset);
				} else {
					// 文件上传用multipart/form-data，否则用www-form-urlencoded
					$postMultipart = true;
					$encodeArray[$k] = new \CURLFile(substr($v, 1));
				}

			}
			unset($k, $v);
			curl_setopt($ch, CURLOPT_POST, true);
			if ($postMultipart) {
				curl_setopt($ch, CURLOPT_POSTFIELDS, $encodeArray);
			} else {
				curl_setopt($ch, CURLOPT_POSTFIELDS, substr($postBodyString, 0, -1));
			}
		}

		if ($postMultipart) {

			$headers = array('content-type: multipart/form-data;charset=' . $this->postCharset . ';boundary=' . $this->getMillisecond());
		} else {

			$headers = array('content-type: application/x-www-form-urlencoded;charset=' . $this->postCharset);
		}
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		$reponse = curl_exec($ch);

		if (curl_errno($ch)) {

			throw new Exception(curl_error($ch), 0);
		} else {
			$httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			if (200 !== $httpStatusCode) {
				throw new Exception($reponse, $httpStatusCode);
			}
		}

		curl_close($ch);
		return $reponse;
	}
	function getHttpResponsePOST($url, $cacert_url, $para, $input_charset = '') {

		if (trim($input_charset) != '') {
			$url = $url . "_input_charset=" . $input_charset;
		}
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); //SSL证书认证
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2); //严格认证
		curl_setopt($curl, CURLOPT_CAINFO, $cacert_url); //证书地址
		curl_setopt($curl, CURLOPT_HEADER, 0); // 过滤HTTP头
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 显示输出结果
		curl_setopt($curl, CURLOPT_POST, true); // post传输数据
		curl_setopt($curl, CURLOPT_POSTFIELDS, $para); // post传输数据
		$responseText = curl_exec($curl);
		//var_dump( curl_error($curl) );//如果执行curl过程中出现异常，可打开此开关，以便查看异常内容
		curl_close($curl);

		return $responseText;
	}
	/**
	 * 校验$value是否非空
	 *  if not set ,return true;
	 *    if is null , return true;
	 **/
	protected function checkEmpty($value) {
		if (!isset($value)) {
			return true;
		}

		if ($value === null) {
			return true;
		}

		if (trim($value) === "") {
			return true;
		}

		return false;
	}

	/**
	 * 转换字符集编码
	 * @param $data
	 * @param $targetCharset
	 * @return string
	 */
	function characet($data, $targetCharset) {

		if (!empty($data)) {
			$fileType = $this->fileCharset;
			if (strcasecmp($fileType, $targetCharset) != 0) {
				$data = mb_convert_encoding($data, $targetCharset, $fileType);
				//				$data = iconv($fileType, $targetCharset.'//IGNORE', $data);
			}
		}

		return $data;
	}

	private function _balance_pay($order_list,$post){
        if (empty($post['password'])) {
            responseApiJson(0, '参数错误', '');
        }
        $model_member = Model('member');
        $buyer_info = $model_member->getMemberInfoByID($this->member_info['member_id']);
//        if ($buyer_info['member_paypwd'] == '' || $buyer_info['member_paypwd'] != md5($post['password'])) {
//            responseApiJson(0, "用户支付密码不正确", '');
//        }
        try {
            $logic_buy_1 = Logic('buy_1');
            $result = $logic_buy_1->balancePay($order_list, $post, $buyer_info);
            if ($result) {
                responseApiJson(1, '支付成功', '');
            } else {
                responseApiJson(0, '支付失败1', '');
            }
        } catch (Exception $exception) {
            responseApiJson(0, $exception->getMessage(),'');
        }
    }
}
