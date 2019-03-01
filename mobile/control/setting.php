<?php
/**
 * 系统设置
 */
class settingControl extends mobileMemberControl {

	/**
	 * changeLoginPasswdOp 修改登录密码
	 * @return [type] [description]
	 */
	public function changeLoginPasswdOp() {
		$validate = new Validate();
		$validate->validateparam = array(
			array('input' => $_POST['old_passwd'], 'require' => "true", 'message' => "请输入旧密码"),
			array('input' => $_POST['new_passwd'], 'require' => "true", 'message' => "请输入新密码"),
			array('input' => $_POST['passwd_confirm'], 'require' => "true", 'message' => "请输入确认密码"),
			array('input' => $_POST['new_passwd'], 'validator' => "compare", 'operator' => "==", 'to' => $_POST['passwd_confirm'], 'message' => "两次输入的新密码不同"),
			array('input' => $_POST['new_passwd'], 'validator' => "compare", 'operator' => "!=", 'to' => $_POST['old_passwd'], 'message' => "输入的新密码与旧密码相同"),
		);
		$error = $validate->validate();
		if ($error != '') {
			output_error($error);
		}

		if ($this->member_info['member_passwd'] != md5(trim($_POST['old_passwd']))) {
			output_error("输入的旧密码错误");
		}

		$result = Model('member')->editMember(array('member_id' => $this->member_info['member_id']), array('member_passwd' => md5(trim($_POST['new_passwd']))));
		if (!$result) {
			output_error("密码修改失败");
		}
		output_data(1);
	}

	/**
	 * changePayPasswdOp 设置支付密码
	 * @return [type] [description]
	 */
	public function changePayPasswdOp() {
		$validate = new Validate();
		$validate->validateparam = array(
			array('input' => $_POST['mobile'], 'require' => "true", 'message' => "请输入手机号"),
			array('input' => $_POST['mobile'], 'validator' => "mobile", 'message' => "手机号格式错误"),
			array('input' => $_POST['captcha'], 'require' => "true", 'message' => "请输入验证码"),
			array('input' => $_POST['passwd'], 'require' => "true", 'message' => "请输入支付密码"),
			array('input' => $_POST['passwd_confirm'], 'require' => "true", 'message' => "请输入确认密码"),
			array('input' => $_POST['passwd_confirm'], 'validator' => "compare", 'operator' => "==", 'to' => $_POST['passwd']),
		);
		$error = $validate->validate();
		if ($error != '') {
			output_error($error);
		}
		$result = Logic('connect_api')->checkSmsCaptcha($_POST['mobile'], $_POST['captcha'], 4);
		if (!$result['state']) {
			output_error($result['msg']);
		}

		if ($_POST['mobile'] != $this->member_info['member_mobile']) {
			output_error("无权修改其他会员的支付密码");
		}

		if (md5(trim($_POST['passwd'])) == $this->member_info['member_paypwd']) {
			output_error("输入的密码跟之前一样，密码修改失败");
		}
		$update = Model('member')->editMember(array('member_id' => $this->member_info['member_id']), array('member_paypwd' => md5(trim($_POST['passwd']))));
		if ($update) {
			output_data('1');
		} else {
			output_error("支付密码修改失败");
		}
	}

	/**
	 * getMemberMobileOp 获取会员手机号
	 * @return [type] [description]
	 */
	public function getMemberMobileOp() {
		output_data(array('mobile' => $this->member_info['member_mobile'], 'mobile_show' => formatPhoneNo($this->member_info['member_mobile'])));
	}
}