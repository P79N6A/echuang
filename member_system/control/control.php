<?php
/**
 * 会员系统父类
 *
 */

defined('In33hao') or exit('Access Invalid!');

class Control {

	/**
	 * checkMessage 检查短消息数量
	 * @return [type] [description]
	 */
	protected function checkMessage() {
	}

	/**
	 *  输出头部的公用信息
	 *
	 */
	protected function showLayout() {
		// $this->showCartCount();
		// 获取导航
		//Tpl::output('nav_list', rkcache('nav',true));
		// 查询保障服务项目
		// Tpl::output('contract_list',Model('contract')->getContractItemByCache());
	}

	/**
	 * 系统公告
	 */
	protected function systemNotice() {
		$model_notice = Model('notice');
		$condition = array();
		$condition['notice_ifshow'] = 1;
		$condition['notice_ncid'] = 1;
		$order = 'notice_addtime desc';
		$notice_list = $model_notice->getNoticeList($condition, '*', null, $order, 3);
		return $notice_list;
	}

	/**
	 * 验证会员是否登录
	 *
	 */
	protected function checkLogin() {
		if (isset($_GET['flag']) && $_GET['flag'] == 'direct_login') {
			if (isset($_GET['member_id']) && isset($_GET['encrypt'])) {
				if (decrypt($_GET['encrypt']) == $_GET['member_id']) {
					$model_member = Model('member');
					$member_info = $model_member->getMemberInfoByID($_GET['member_id']);
					$model_member->createSession($member_info);
				} else {
					@header("location: " . urlMember('login', 'index'));
					exit();
				}
			} else {
				@header("location: " . urlMember('login', 'index'));
				exit();
			}
		} else {
			if ($_SESSION['is_login'] !== '1') {
				// if ($_GET['inajax']){
				//     showDialog('','','js',"login_dialog();",200);
				// } else {
				@header("location: " . urlMember('login', 'index'));
				// }
				exit();
			}
		}

	}

	/**
	 * 自动登录
	 */
	protected function auto_login() {
		$data = cookie('auto_login');
		if (empty($data)) {
			return false;
		}

		$model_member = Model('member');
		if ($_SESSION['is_login']) {
			$model_member->auto_login();
		}
		$member_id = intval(decrypt($data, MD5_KEY));
		if ($member_id <= 0) {
			return false;
		}
		$member_info = $model_member->getMemberInfoByID($member_id);
		$model_member->createSession($member_info);
	}
}

class BaseCommonControl extends Control {
	public function __construct() {

	}
}

class BaseLoginControl extends Control {

	public function __construct() {
		// 设置布局文件
		Tpl::setLayout('login_layout');
		// // 会员验证
		// $this->checkLogin();
		// 自动登录
		$this->auto_login();
	}
}

class BaseMemberControl extends Control {

	protected $member_info = array(); // 会员信息
	public function __construct() {
		Language::read('common,member_layout');
		// 会员验证

		$this->checkLogin();
		// 输出头部的公用信息
		// $this->showLayout();
		Tpl::setLayout('member_layout');

		// 获得会员信息
		$this->member_info = $this->getMemberAndLevelInfo(true);
		Tpl::output('info', $this->member_info);
		$system_notice = $this->systemNotice();
		Tpl::output('s_notice', $system_notice);
		// 左侧导航11
		$menu_list = $this->_getMenuList();
		Tpl::output('menu', $menu_list);
		$function_menu = $this->_getSellerFunctionList($menu_list);
		$current_menu = $this->_getCurrentMenu($function_menu);
		Tpl::output('current_menu', $current_menu);
		// 页面高亮
	}

	protected function getMemberAndLevelInfo($is_return = false) {
		$fields = 'member_id,member_name,member_mobile,member_passwd,member_paypwd,member_avatar,member_old_login_time,reg_time,member_golden_bean,member_state,member_equity,member_golden_bean_payed,member_level,is_service,is_first_joinin';
		$model_member_extend = Model('member_extend');
		$member_id = $_SESSION['member_id'];
		$info = $model_member_extend->getMemberExtendInfo(array('member_id' => $member_id), $fields, 'union');
		// 获取用户等级
		$levelArr = $this->getLevelNameArr($info['member_level']);
		$urlArr = $this->generalInviteArr($info);
		$info = array_merge($info, $levelArr, $urlArr);
		if ($is_return) {
			return $info;
		} else {
			Tpl::output('info', $info);
		}
	}

	protected function getMemberEquityNum($equity) {
		$equity_num = 0;
		if ($equity >= C('equity_dividend_max_limit')) {
			$equity_num = 10;
		} elseif ($equity >= C('equity_dividend_min_limit')) {
			$equity_num = 1;
		}
		return $equity_num;
	}

	/**
	 * generalInviteArr 生成邀请数组
	 * @param  [type] $info [description]
	 * @return [type]       [description]
	 */
	protected function generalInviteArr($info) {
		$myInviteUrl = APP_SITE_URL . '/index.php?act=login&op=register&t=' . $info['member_mobile'];
		$code_dir = 'member_system';
		$code_name = 'invite_' . $info['member_id'];
		return array('url' => $myInviteUrl, 'qr_url' => make_QR_code($code_dir, $code_name, $myInviteUrl));
	}

	/**
	 * getLevelNameArr 获取会员等级
	 * @param  [type] $level [description]
	 * @return [type]        [description]
	 */
	protected function getLevelNameArr($level) {
		$leve_name = "普通会员";
		$levelArr = Model('level')->getLevelArr();
		if (!empty($levelArr[$level])) {
			$leve_name = $levelArr[$level]['ml_level_name'];
		}
		return array('leverl' => $leve_name);
	}

	/**
	 * _getSellerFunctionList
	 * @param  [type] $menu_list [description]
	 * @return [type]            [description]
	 */
	private function _getSellerFunctionList($menu_list) {
		$format_menu = array();
		foreach ($menu_list as $key => $menu_value) {
			foreach ($menu_value['child'] as $submenu_value) {
				$format_menu[$submenu_value['op']] = array(
					'model' => $key,
					'model_name' => $menu_value['name'],
					'name' => $submenu_value['name'],
					'act' => $submenu_value['act'],
					'op' => $submenu_value['op'],
				);
			}
		}
		return $format_menu;
	}

	private function _getCurrentMenu($member_function_list) {
		$current_menu = $member_function_list[$_GET['op']];
		if (empty($current_menu)) {
			$current_menu = array(
				'model' => 'index',
				'model_name' => '首页',
			);
		}
		return $current_menu;
	}

	/**
	 * 导航栏
	 * 菜单数组中child的下标要和其链接的act对应。否则面包屑不能正常显示
	 * @return array
	 */
	private function _getMenuList() {
		$menu_list = array(
			'member_info' => array('name' => '用户中心', 'child' => array(
				array('name' => '账户信息', 'act' => 'member_info', 'op' => 'member_account'),
//				array('name' => '银行账户', 'act' => 'member_info', 'op' => 'bank_account'),
				array('name' => '直推列表', 'act' => 'member_info', 'op' => 'direct_list'),
				array('name' => '推荐族谱图', 'act' => 'member_info', 'op' => 'invite_family_tree'),
//				array('name' => '接点族谱图', 'act' => 'member_info', 'op' => 'access_family_tree'),
			)),
			'property' => array('name' => '财产中心', 'child' => array(
				array('name' => '分红明细', 'act' => 'member_property', 'op' => 'bonus_list'),
//				array('name' => '服务提', 'act' => 'member_property', 'op' => 'consume_list'),
				array('name' => '提现管理', 'act' => 'member_property', 'op' => 'withdraw_list'),
//				array('name' => '重销分红', 'act' => 'member_property', 'op' => 'donate_list'),
//				array('name' => '提现管理', 'act' => 'member_property', 'op' => 'float_dividend_list'),
				array('name' => "股权管理", 'act' => "member_property", 'op' => "silver_bean_list"),
			)),
//			'service' => array('name' => '服务中心', 'child' => array(
////				array('name' => '服务中心申请', 'act' => 'service', 'op' => 'service_apply'),
//			)),
		);
		return $menu_list;
	}
}

/**
 * 公用控制器
 */
class PubControl extends Control {
	public function __construct() {}
}

class MemberOperateControl extends BaseMemberControl {
	public function __construct() {
		parent::__construct();
		if (!$this->member_info['member_state']) {
			showMessage('会员已冻结，不能操作账户', '', '', 'error');
		}
		if (is_null($this->member_info['member_paypwd'])) {
			showMessage('请先设置支付密码', 'index.php?act=member_info&op=change_paypwd', '', 'error');
		}
	}

	protected function checkPaypwd($paypwd) {
		return ($paypwd == $this->member_info['member_paypwd']);
	}
}