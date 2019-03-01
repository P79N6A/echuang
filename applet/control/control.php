<?php
/**
 * 小程序基类
 */

defined('In33hao') or exit('Access Invild!');

class appletBaseControl {
	// 列表默认分页数
	protected $page = 5;

	public function __construct() {
		// 分页数处理
		$page = intval($_GET['page']);
		if ($page > 0) {
			$this->page = $page;
		}
	}

	/**
	 * checkRequesMethod 检测接口请求方法
	 * @param  [type] $method [description]
	 * @return [type]         [description]
	 */
	protected function checkRequesMethod($method = 'post') {
		if (strcasecmp($_SERVER['REQUEST_METHOD'], $method) != 0) {
			responseAppletJson(0, '请求方式错误');
		}
	}
}

class appletMemberControl extends appletBaseControl {

	// 会员信息
	protected $member_info = array();

	public function __construct() {
		parent::__construct();

		$token = '';
		if (isset($_POST['token'])) {
			$token = $_POST['token'];
		} elseif (isset($_GET['token'])) {
			$token = $_GET['token'];
		}

		// 处理token
		$userTokenInfo = Model('mb_user_token')->getMbUserTokenInfoByToken($token);
		if (empty($userTokenInfo)) {
			responseAppletJson(2, "请登录");
		}

		// 通过token信息获取用户信息
		$this->member_info = Model('member_extend')->getMemberExtendInfo(array('member_id' => $userTokenInfo['member_id']), '*', 'union');

		if (empty($this->member_info)) {
			responseAppletJson(2, "请登录");
		} else {
			if ($this->member_info['member_state'] == 0) {
				responseAppletJson(0, "该账号已冻结");
			}
		}
	}

	/**
	 * getMemberEquityNum 获取金券数量
	 * @param  double $equity 金券
	 * @return int(0,1,10)    金券数量
	 */
	protected function getMemberEquityNum($equity) {
		$num = 0;
		if ($equity >= C('equity_dividend_max_limit')) {
			$num = 10;
		} elseif ($equity >= C('equity_dividend_min_limit')) {
			$num = 1;
		}
		return $num;
	}

	/**
	 * checkEmptyMemberPaypwd 检查会员支付密码是否为空
	 * @return [type] [description]
	 */
	protected function checkEmptyMemberPaypwd() {
		return empty($this->member_info['member_paypwd']);
	}
}
