<?php
/**
 * 会员系统公共模块
 *
 */
class commonControl extends BaseCommonControl {
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
			array('input' => $_GET['mobile'], 'require' => "true", 'message' => "请输入手机号码"),
			array('input' => $_GET['mobile'], 'validator' => "mobile", 'message' => "手机号码格式不正确"),
			array('input' => $_GET['sms_type'], 'require' => "true", 'message' => "请输入短信类型"),
		);
		$error = $validate->validate();
		if ($error != '') {
			echo json_encode(array('state' => "false", 'msg' => $error));
		}

		$logic_connect_api = Logic('connect_api');
		$result = $logic_connect_api->sendCaptcha($_GET['mobile'], $_GET['sms_type']);
		if ($result['state']) {
			echo json_encode(array('state' => "true", 'msg' => "验证码已发出，请注意查收"));
		} else {
			echo json_encode(array('state' => "false", 'msg' => $result['msg']));
		}
	}

}