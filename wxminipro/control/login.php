<?php
/**
 * APP登录、注册操作
 *
 */
defined('In33hao') or exit('Access Invalid!');

class loginControl extends apiEncryptCommonControl {

	public function __construct() {
		parent::__construct();
	}

	/**
	 * forgetPasswordOp 忘记密码
	 * @return [type] [description]
	 */
	public function forgetPasswordOp() {
		if (empty($_POST['mobile'])) {
			responseApiJson(0, "请输入手机号");
		}

		if (empty($_POST['captcha'])) {
			responseApiJson(0, "请输入验证码");
		}

		if (empty($_POST['password'])) {
			responseApiJson(0, "请输入新密码");
		}
		$mobile = trim($_POST['mobile']);
		$captcha = trim($_POST['captcha']);
		$password = trim($_POST['password']);

		$logic_connect_api = Logic('connect_api');
		$result = $logic_connect_api->checkSmsCaptcha($mobile, $captcha, 3);
		if ($result['state'] == false) {
			responseApiJson(0, "验证码错误或已过期");
		}
		// 检查手机号是否已被注册
		$model_member = Model('member');
		$info = $model_member->getMemberInfo(array('member_mobile' => $mobile));
		if (!empty($info)) {
			$model_member->editMember(array('member_id' => $info['member_id']), array('member_passwd' => md5($password)));
			responseApiJson(1, "密码修改成功");
		} else {
			responseApiJson(0, "手机号未注册");
		}
	}
}
