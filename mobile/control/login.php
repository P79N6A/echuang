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
			array('input' => $_POST['invite'], 'require' => "true", 'message' => "请输入邀请人手机号"),
			array('input' => $_POST['invite'], 'validator' => "mobile", 'message' => "邀请人手机号格式错误"),
			array('input' => $_POST['access'], 'require' => "true", 'message' => "请输入接点人手机号"),
			array('input' => $_POST['access'], 'validator' => "mobile", 'message' => "接点人手机号格式错误"),
			array('input' => $_POST['bank_account_name'], 'require' => "true", 'message' => "请输入银行账户"),
			array('input' => $_POST['bank_name'], 'require' => "true", 'message' => "请选择银行"),
			array('input' => $_POST['bank_branch_name'], 'require' => "true", 'message' => "请输入银行支行"),
			array('input' => $_POST['bank_account_num'], 'require' => "true", 'message' => "请输入银行卡号"),
			array('input' => $_POST['bank_account_num'], 'validator' => "number", 'message' => "银行卡号格式错误"),
		);
		$error = $validate->validate();
		if ($error != '') {
			output_error($error);
		}

		$result = Logic('connect_api')->checkSmsCaptcha($_POST['mobile'], $_POST['captcha'], 1);
		if (!$result['state']) {
			output_error($result['msg']);
		}

		$bank_id = $_POST['bank_name'];
		$bank_info = Model('bank')->getBankInfo(array('bank_id' => $bank_id));
		if (empty($bank_info)) {
			output_error("银行信息错误");
		}

		$model_member_extend = Model('member_extend');
		$info = $model_member_extend->getMemberExtendInfo(array('member_mobile' => $_POST['mobile']), '*', 'union');
		if (!empty($info)) {
			output_error("手机号已注册,请更换手机号注册");
		}
		$invite_info = $model_member_extend->getMemberExtendInfo(array('member_mobile' => $_POST['invite']), '*', 'union');
		if (empty($invite_info)) {
			output_error("邀请人未注册");
		}
		$access_info = $model_member_extend->getMemberExtendInfo(array('member_mobile' => $_POST['access']), '*', 'union');
		if (empty($access_info)) {
			output_error("接点人未注册");
		}

		// 判断接点人是否跟推荐人在同一区域
		$access_arr = array();
		$model_member_extend->getMemberAccessParentArr($access_info['member_id'], $access_arr);
		if (!in_array($invite_info['member_id'], $access_arr)) {
			output_error("接点人跟推荐人不在同一区域");
		}

		$count = $model_member_extend->getMemberAccessCount($access_info['member_id']);
		if ($count >= 2) {
			output_error("此接点人已接满,请更换接点人");
		}

		// $area_info = $model_member_extend->getMemberExtendInfo(array('access_id' => $access_info['member_id'], 'left_right_area' => $_POST['area']), '*', 'union');
		// if (!empty($area_info)) {
		// 	output_error("选择的区域已接人,请重新选择区域");
		// }
		$_POST['area'] = 0;
		$register_data = array();
		$register_data['username'] = trim($_POST['username']);
		$register_data['member_mobile'] = trim($_POST['mobile']);
		$register_data['password'] = trim($_POST['passwd']);
		$register_data['paypasswd'] = trim($_POST['paypasswd']);
		$register_data['inviter_id'] = $invite_info['member_id'];
		$register_data['invite_one'] = $invite_info['member_id'];
		$register_data['invite_two'] = $invite_info['invite_one'];
		$register_data['invite_three'] = $invite_info['invite_two'];
		$register_data['left_right_area'] = trim($_POST['area']);
		$register_data['invite_id'] = $invite_info['member_id'];
		$register_data['access_id'] = $access_info['member_id'];
		$register_data['depth'] = $access_info['depth'] + 1;
		$register_data['account_name'] = trim($_POST['bank_account_name']);
		$register_data['account_bank_name'] = $bank_info['bank_name'];
		$register_data['account_branch'] = trim($_POST['bank_branch_name']);
		$register_data['account_num'] = trim($_POST['bank_account_num']);
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
