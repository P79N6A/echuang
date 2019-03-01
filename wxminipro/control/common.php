<?php
/**
 * 公用模块
 *
 */
class commonControl extends apiEncryptCommonControl {
	public function __construct() {
		parent::__construct();
	}

	/**
	 * get_captchaOp 获取验证码
	 * @return [type] [description]
	 * sms_type:1 注册 2登录 3修改登录密码 4修改支付密码
	 */
	public function get_sms_captchaOp() {
		$validate = new Validate();
		$validate->validateparam = array(
			array('input' => $_POST['mobile'], 'require' => "true", 'message' => "请输入手机号码"),
			array('input' => $_POST['mobile'], 'validator' => "mobile", 'message' => "手机号码格式不正确"),
			array('input' => $_POST['sms_type'], 'require' => "true", 'message' => "请输入短信类型"),
		);
		$error = $validate->validate();
		if ($error != '') {
			responseApiJson(0, $error);
		}

		$logic_connect_api = Logic('connect_api');
		$result = $logic_connect_api->sendCaptcha($_POST['mobile'], $_POST['sms_type']);
		if ($result['state']) {
			responseApiJson(1, $result['msg']);
		} else {
			responseApiJson(0, $result['msg']);
		}
	}
}