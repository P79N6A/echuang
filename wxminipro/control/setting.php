<?php
/**
 * APP设置操作
 *
 */

defined('In33hao') or die("Access Invilid!");

class settingControl extends apiEncryptMemberControl {

	public function __construct() {
		parent::__construct();
	}

	/**
	 * change_passwdOp 修改用户登录密码
	 * @return [type] [description]
	 */
	public function change_passwdOp() {
		$validate = new Validate();
		$validate->validateparam = array(
			array('input' => $_POST['password_old'], 'require' => "true", 'message' => "请输入旧密码"),
			array('input' => $_POST['password'], 'require' => "true", 'message' => "请输入新密码"),
			array('input' => $_POST['password'], 'validator' => 'length', 'min' => 6, 'max' => 20, 'message' => "输入的密码至少6位"),
		);
		if (md5(trim($_POST['password_old'])) != $this->member_info['member_passwd']) {
			responseApiJson(0, '输入的旧密码错误');
		}

		$model_member = Model('member');
		$update = $model_member->editMember(array('member_id' => $this->member_info['member_id']), array('member_passwd' => md5(trim($_POST['password']))));
		if ($update) {
			responseApiJson(1, "登录密码修改成功");
		} else {
			responseApiJson(0, "登录密码修改失败");
		}
	}

	/**
	 * change_paypwdOp 修改支付密码
	 * @return [type] [description]
	 */
	public function change_paypwdOp() {
		$validate = new Validate();
		$validate->validateparam = array(
			array('input' => $_POST['paypwd'], 'require' => "true", 'message' => "请填写支付密码"),
			array('input' => $_POST['paypwd'], 'validator' => 'length', 'min' => 6, 'max' => 20, 'message' => "输入的密码至少6位"),
			array('input' => $_POST['paypwd'], 'validator' => "compare", 'operator' => "==", 'to' => $_POST['paypwd_confirm'], 'message' => "输入的支付密码与确认密码不相同"),
			array('input' => $_POST['captcha'], 'require' => "true", 'message' => "请输入验证码"),
		);

		$error = $validate->validate();
		if ($error != '') {
			responseApiJson(0, $error);
		}
		$logic_connect_api = Logic('connect_api');
		$result = $logic_connect_api->checkSmsCaptcha($this->member_info['member_mobile'], $_POST['captcha'], 4);
		if (!$result['state']) {
			responseApiJson(0, $result['msg']);
		}
		$model_member = Model('member');
		$update = $model_member->editMember(array('member_id' => $this->member_info['member_id']), array('member_paypwd' => md5(trim($_POST['paypwd']))));
		if ($update) {
			responseApiJson(1, "支付密码修改成功");
		} else {
			responseApiJson(0, "支付密码修改失败");
		}
	}

}