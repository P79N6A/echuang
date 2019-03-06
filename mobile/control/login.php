<?php
/**
 * 前台登录 退出操作
 *
 *
 *  (c) ()
 * @license
 * @link
 * @since
 */

defined('In33hao') or exit('Access Invalid!');

class loginControl extends mobileHomeControl {

	public function __construct() {
		parent::__construct();
	}

	/**
	 * 会员登录
	 */
	public function indexOp() {
		$validate = new Validate();
		$validate->validateparam = array(
			array('input' => $_POST['username'], 'require' => "true", 'message' => "手机号必填"),
			array('input' => $_POST['username'], 'validator' => "mobile", 'message' => "手机号格式错误"),
			array('input' => $_POST['password'], 'require' => "true", 'message' => "密码必填"),
		);
		$error = $validate->validate();
		if ($error != '') {
			output_error($error);
		}

		if (!in_array($_POST['client'], $this->client_type_array)) {
			output_error('登录失败');
		}

		$login_info['member_mobile'] = $_POST['username'];
		$login_info['member_pwd'] = $_POST['password'];
		$result = Model('member_extend')->memberLogin($login_info);
		if (isset($result['error'])) {
			output_error($result['error']);
		}
		$member_info = $result;
		$token = $this->_get_token($member_info['member_id'], $member_info['member_name'], $_POST['client']);
		if ($token) {
			output_data(array('username' => $member_info['member_name'], 'userid' => $member_info['member_id'], 'key' => $token));
		} else {
			output_error('登录失败');
		}
	}

	/**
	 * 会员注册
	 */
	public function registerOp() {

		if (process::islock('reg')) {
			output_error('您的操作过于频繁，请稍后再试');
		}
		$validate = new Validate();
		$validate->validateparam = array(
			array('input' => $_POST['username'], 'require' => "true", 'message' => "请输入用户名"),
			array('input' => $_POST['username'], 'validator' => "chinese", 'message' => "用户名须为中文"),
			array('input' => $_POST['mobile'], 'require' => "true", 'message' => "请输入手机号"),
			array('input' => $_POST['mobile'], 'validator' => "mobile", 'message' => "手机号格式错误"),
			array('input' => $_POST['captcha'], 'require' => "true", 'message' => "请输入验证码"),
			array('input' => $_POST['passwd'], 'require' => "true", 'message' => "请输入密码"),
			array('input' => $_POST['paypasswd'], 'require' => "true", 'message' => "请输入支付密码"),
		);
		$error = $validate->validate();
		if ($error != '') {
			output_error($error);
		}

		//手机验证码
		$result = Logic('connect_api')->checkSmsCaptcha($_POST['mobile'], $_POST['captcha'], 1);
		if (!$result['state']) {
			output_error($result['msg']);
		}


		$model_member_extend = Model('member_extend');
		$info = $model_member_extend->getMemberExtendInfo(array('member_mobile' => $_POST['mobile']), '*', 'union');
		if (!empty($info)) {
			output_error("手机号已注册,请更换手机号注册");
		}

		$_POST['area'] = 0;
		$register_data = array();
		$register_data['username'] = trim($_POST['username']);
		$register_data['member_mobile'] = trim($_POST['mobile']);
		$register_data['password'] = trim($_POST['passwd']);
		$register_data['paypasswd'] = trim($_POST['paypasswd']);
		$register_data['left_right_area'] = trim($_POST['area']);
		$result = $model_member_extend->memberRegister($register_data, false, true);
		if (isset($result['error'])) {
			output_error($result['error']);
		}
		$member_info = $result;
		process::addprocess('reg');
		$token = $this->_get_token($member_info['member_id'], $member_info['member_name']);
		if ($token) {
			output_data(array('username' => $member_info['member_name'], 'userid' => $member_info['member_id'], 'key' => $token));
		} else {
			output_error('注册失败');
		}

	}

	/**
	 * bankListOp 支持的银行列表
	 * @return [type] [description]
	 */
	public function bankListOp() {
		output_data(array('list' => Model('bank')->getBankList()));
	}

	/**
	 * 登录生成token
	 */
	private function _get_token($member_id, $member_name, $client = 'wap') {
		$model_mb_user_token = Model('mb_user_token');

		//重新登录后以前的令牌失效
		//暂时停用
		//$condition = array();
		//$condition['member_id'] = $member_id;
		//$condition['client_type'] = $client;
		//$model_mb_user_token->delMbUserToken($condition);

		//生成新的token
		$mb_user_token_info = array();
		$token = md5($member_name . strval(TIMESTAMP) . strval(rand(0, 999999)));
		$mb_user_token_info['member_id'] = $member_id;
		$mb_user_token_info['member_name'] = $member_name;
		$mb_user_token_info['token'] = $token;
		$mb_user_token_info['login_time'] = TIMESTAMP;
		$mb_user_token_info['client_type'] = $client;

		$result = $model_mb_user_token->addMbUserToken($mb_user_token_info);

		if ($result) {
			return $token;
		} else {
			return null;
		}

	}

	/**
	 * 递归查询下级的所有人
	 * @param  [type] $seller_id   [description]
	 * @param  [type] $seller_name [description]
	 * @param  [type] $client      [description]
	 * @return [type]              [description]
	 */
	private function _get_children_id($pid, &$cids = array()) {
		$inviter_id['access_id'] = $pid;
		$children_id_list = Model('member_extend')->getMemberExtendList($inviter_id, 'member_mobile,member_id', 'union');
		foreach ($children_id_list as $key => $value) {
			if (!empty($value['member_mobile'])) {
				$cids[] = $value['member_mobile'];
				$this->_get_children_id($value['member_id'], $cids);
			}
		}
		return $cids;
	}
}
