<?php
/**
 * 会员中心
 *
 */
defined('In33hao') or exit('Access Invild!');

class member_infoControl extends BaseMemberControl {
	public function __construct() {
		parent::__construct();
	}

	public function indexOp() {
		$equity = $this->member_info['member_equity'];
		$equity_num = $this->getMemberEquityNum($equity);
		$consume_amount = ncPriceFormat(Model('bean')->getBeanConsumeSum(array('cl_member_id' => $this->member_info['member_id'])));
		Tpl::output('equity_num', $equity_num);
		Tpl::output('consuem_amount', $consume_amount);
		Tpl::showpage('member_info.index');
	}

	/**
	 * memberAccountOp 账户信息
	 * @return [type] [description]
	 */
	public function member_accountOp() {
		if (chksubmit()) {
			$model_member = Model('member');
			$member_pwd = $_POST['member_pwd'];
			$member_paypwd = $_POST['member_paypwd'];
			$pwd_data = array();
			if (empty($member_pwd) && empty($member_paypwd)) {
				showMessage('当前操作未做修改', '', '', 'succ');
			} else {
				if (!empty($member_pwd)) {
					if (strlen($member_pwd) < 6) {
						showMessage('登录密码格式错误', '', '', 'error');
					}
					$pwd_data['member_passwd'] = md5($member_pwd);
				}
				if (!empty($member_paypwd)) {
					if (strlen($member_paypwd) < 6) {
						showMessage('支付密码格式错误', '', '', 'error');
					}
					$pwd_data['member_paypwd'] = md5($member_paypwd);
				}
				$update = $model_member->editMember(array('member_id' => $_SESSION['member_id']), $pwd_data);
				if ($update) {
					showMessage('密码修改成功', 'index.php?act=member_info&op=member_account', '', 'succ');
				} else {
					showMessage('密码修改失败', '', '', 'error');
				}
			}

		}
		$this->_profile_menu('account', 'member_account');
		Tpl::showpage('member_personal_info');
	}

	/**
	 * bank_accountOp 银行账户
	 * @return [type] [description]
	 */
	public function bank_accountOp() {
		$model_bank = Model('bank');
		$bank_list = $model_bank->getMemberBankList(array('mb_member_id' => $_SESSION['member_id']));
		Tpl::output('list', $bank_list);
		$this->_profile_menu('account', 'bank_account');
		Tpl::showpage('member_bank.info');
	}

	/**
	 * accountAddOp 添加银行卡
	 * @return [type] [description]
	 */
	public function bank_account_addOp() {
		$model_bank = Model('bank');
		$html = $model_bank->buildBankSelectHtml();
		Tpl::output('html', $html);
		if (chksubmit()) {
			$bankArr = $model_bank->getBankListArr();
			$data = array();
			$data['mb_member_id'] = $_SESSION['member_id'];
			$data['mb_account_bank_name'] = $bankArr[$_POST['account_bank_name']];
			$data['mb_account_name'] = trim($_POST['account_name']);
			$data['mb_account_branch'] = trim($_POST['account_branch']);
			$data['mb_account_num'] = trim($_POST['account_num']);
			$insert = $model_bank->addMemberBank($data);
			if ($insert) {
				showMessage('银行卡添加成功', 'index.php?act=member_info&op=bank_account', '', 'succ');
			} else {
				showMessage('银行卡添加失败', '', '', 'error');
			}
		}
		$this->_profile_menu('bank_add', 'bank_account_add');
		Tpl::showpage('member_bank.add');
	}

	/**
	 * bank_account_editOp 编辑银行卡
	 * @return [type] [description]
	 */
	public function bank_account_editOp() {
		$model_bank = Model('bank');
		$member_id = $_SESSION['member_id'];
		if (chksubmit()) {
			$validate = new Validate();
			$validate->validateparam = array(
				array("input" => $_POST["account_name"], "require" => "true", "message" => "开户名不能为空"),
				array("input" => $_POST["account_bank_name"], "require" => "true", "message" => "请选择开户银行"),
				array("input" => $_POST["account_branch"], "require" => "true", "message" => "开户支行不能为空"),
				array("input" => $_POST["account_num"], "require" => "true", "message" => "银行账号不能为空"),
			);
			$error = $validate->validate();
			if ($error != '') {
				showMessage($error, '', '', 'error');
			} else {
				$bank_info = $model_bank->getBankInfo(array('bank_id' => $_POST['account_bank_name']), 'bank_name');
				$bank_id = $_POST['bank_id'];
				$bank_data = array();
				$bank_data['mb_account_name'] = trim($_POST['account_name']);
				$bank_data['mb_account_bank_name'] = $bank_info['bank_name'];
				$bank_data['mb_account_branch'] = trim($_POST['account_branch']);
				$bank_data['mb_account_num'] = trim($_POST['account_num']);
				$bank_data['mb_addtime'] = TIMESTAMP;

				try {
					$model_bank->editMemberBank(array('mb_id' => $bank_id, 'mb_member_id' => $member_id), $bank_data);
					showMessage('银行卡信息修改成功', 'index.php?act=member_info&op=bank_account', '', 'succ');
				} catch (Exception $exception) {
					showMessage('银行卡修改失败', '', '', 'error');
				}
			}
		} else {
			$bank_id = $_GET['id'];
			$bank_info = $model_bank->getMemberBankInfo(array('mb_id' => $bank_id, 'mb_member_id' => $member_id));
			if (empty($bank_info)) {
				showMessage('银行卡数据不存在', '', '', 'error');
			}

			$html = $model_bank->buildBankSelectHtml($bank_info['mb_account_bank_name']);
			Tpl::output('html', $html);
			Tpl::output('bank_info', $bank_info);
			$this->_profile_menu('bank_edit', 'bank_account_edit');
			Tpl::showpage('member_bank.edit');
		}

	}

	/**
	 * bank_account_delOp 删除银行卡操作
	 * @return [type] [description]
	 */
	public function bank_account_delOp() {
		$bank_id = $_GET['id'];
		$model_bank = Model('bank');
		$result = $model_bank->delMemberBank(array('mb_id' => $bank_id, 'mb_member_id' => $_SESSION['member_id']));
		if ($result) {
			showMessage('银行信息删除成功', 'index.php?act=member_info&op=bank_account', '', 'succ');
		} else {
			showMessage('银行信息删除失败', 'index.php?act=member_info&op=bank_account', '', 'error');
		}
	}
	/**
	 * directListOp 直推列表
	 * @return [type] [description]
	 */
	public function direct_listOp() {
		$model_level = Model('level');
		$model_member = Model('member');
		$model_member_extend = Model('member_extend');
		$mobiles = array();
		$list = $model_member->getMemberList(array(), 'member_mobile,member_id');
		foreach ($list as $value) {
			$mobiles[$value['member_id']] = $value['member_mobile'];
		}
		$levels = array();
		$list = $model_level->getMemberLevelList();
		foreach ($list as $value) {
			$levels[$value['ml_id']] = $value['ml_level_name'];
		}

		$fields = 'member_id,member_name,member_mobile,member_state,reg_time,access_id,member_level';
		$member_list = $model_member_extend->getInviteFamilyTreeArr($_SESSION['member_id'], $fields);
		$this->_profile_menu('direct_list', 'direct_list');
		Tpl::output('list', $member_list['child']);
		Tpl::output('mobile', $mobiles);
		Tpl::output('level', $levels);
//var_dump($member_list,$mobiles,$levels);die();
		Tpl::showpage('direct_inviter.list');
	}

	/**
	 * inviteFamilyTreeOp 推荐族谱图
	 * @return [type] [description]
	 */
	public function invite_family_treeOp() {
		$model_member_extend = Model('member_extend');
		$field = 'member_id,member_mobile,member_name,member_level,total_bd_product_consume';
		// $member_id = trim($_GET['member_id']);
		$member_id = $_SESSION['member_id'];
        $inviteArr = array();
        $inviteArr[] = $model_member_extend->getInviteFamilyTreeArr($member_id, $field);
        $treeHtml = $model_member_extend->buildTreeHtml($inviteArr);
        $this->_profile_menu('invite_family_tree', 'invite_family_tree');
		Tpl::output('tree', $treeHtml);
		Tpl::showpage('member_invite.family.tree');
	}

	/**
	 * accessFamilyTreeOp 接点族谱图
	 * @return [type] [description]
	 */
	public function access_family_treeOp() {
		$model_member_extend = Model('member_extend');
		$master = true;
		if (isset($_GET['master'])) {
			if (!$_GET['master'] && $_GET['member_id']) {
				$master = false;
				$member_id = $_GET['member_id'];
			}
		} else {
			$member_id = $_SESSION['member_id'];
		}
		$this->_profile_menu('access_family_tree', 'access_family_tree');
		$binary_tree[] =  $model_member_extend->buildBinaryTreeArr($member_id, $master);
		$htmlFamilytree = $model_member_extend->buildFamiltree($binary_tree);
		Tpl::output('tree', $htmlFamilytree);
		Tpl::showpage('member_access.family.tree');
	}

	/**
	 * member_paypwd_settingOp 设置支付密码
	 * @return [type] [description]
	 */
	public function member_paypwd_settingOp() {
		if (chksubmit()) {
			$member_paypwd = trim($_POST['member_paypwd']);
			$member_paypwd_confirm = trim($_POST['member_paypwd_confirm']);
			$validate = new Validate();
			$validate->validateparam = array(
				array('input' => $member_paypwd, 'require' => "true", 'message' => "支付密码不能为空"),
				array('input' => $member_paypwd_confirm, 'require' => "true", 'message' => "确认支付密码不能为空"),
			);
			$error = $validate->validate();
			if ($error != '') {
				showMessage($error, '', '', 'error');
			} else {
				if ($member_paypwd == $member_paypwd_confirm) {
					$model_member_extend = Model('member_extend');
					$member_id = $_SESSION['member_id'];
					try {
						$model_member_extend->editMemberInfo(array('member_id' => $_SESSION['member_id']), array('member_paypwd' => md5($member_paypwd)));
						showMessage('支付密码设置成功', 'index.php?act=member_info&op=index', '', 'succ');
					} catch (Exception $exception) {
						showMessage('支付密码设置失败', '', '', 'error');
					}
				} else {
					showDialog('两次输入的密码不一致');
				}
			}
		} else {
			$this->_profile_menu('paypwd_setting', 'member_paypwd_setting');
			Tpl::showpage('member_paypwd_setting');
		}
	}

	/**
	 * access_registerOp 接点人注册
	 * @return [type] [description]
	 */
	public function access_registerOp() {
		$model_member_extend = Model('member_extend');
		// $access_id = $_GET['access_id'] ? $_GET['access_id'] : $POST['access_id'];
		// $result = $model_member_extend->checkAccessInfoById($_GET['access_id']);
		// if (isset($result['error'])) {
		// 	showMessage($result['error'], '', '', 'error');
		// }
		$member_access = $_GET['access_id'] ? trim($_GET['access_id']) : trim($_POST['access_id']);
		$area = $_GET['area'];
		$a_info = $model_member_extend->getMemberExtendInfo(array('member_id' => $member_access), '*', 'union');
//		echo '<pre>';
		//		var_dump($a_info);
		//		exit;
		if (empty($a_info)) {
			showMessage('接点人不存在', 'index.php?act=member_info&op=access_family_tree', '', 'error');
		}
		$a_num = $model_member_extend->getMemberAccessCount($a_info['member_id']);
		if ($a_num >= 2) {
			showMessage('此接点人接点数量超过限制', 'index.php?act=member_info&op=access_family_tree', '', 'error');
		}
		if (chksubmit()) {
			$member_invite = trim($_POST['member_invite']);
			$i_info = $model_member_extend->getMemberExtendInfo(array('member_mobile' => $member_invite), '*', 'union');
			if (empty($i_info)) {
				showMessage('推荐人不存在', 'index.php?act=member_info&op=access_family_tree', '', 'error');
			}
			$member_mobile = trim($_POST['member_mobile']);
			$info = $model_member_extend->getMemberExtendInfo(array('member_mobile' => $member_mobile), '*', 'union');
			if (!empty($info)) {
				showMessage('手机号已注册', 'index.php?act=member_info&op=access_family_tree', '', 'error');
			}
			$register_data = array();
			$register_data['username'] = trim($_POST['member_name']);
			$register_data['member_mobile'] = trim($_POST['member_mobile']);
			$register_data['password'] = trim($_POST['member_pwd']);
			$register_data['password_confirm'] = trim($_POST['member_pwd_confirm']);
			$register_data['inviter_id'] = $i_info['member_id'];
			$register_data['invite_one'] = $i_info['member_id'];
			$register_data['invite_two'] = $i_info['invite_one'];
			$register_data['invite_three'] = $i_info['invite_two'];
			$register_data['invite_id'] = $i_info['member_id'];
			$register_data['access_id'] = $a_info['member_id'];
			$area_post = $_POST['area'];
			if ($area_post == 'left') {
				$register_data['left_right_area'] = 1;
			}
			if ($area_post == 'right') {
				$register_data['left_right_area'] = 2;
			}
			$register_data['depth'] = $a_info['depth'] + 1;
			$result = $model_member_extend->memberRegister($register_data, false);
			if (isset($result['error'])) {
				showMessage('用户注册失败', 'index.php?act=member_info&op=access_family_tree', '', 'error');
			} else {
				showMessage('用户注册成功', 'index.php?act=member_info&op=access_family_tree', '', 'succ');
			}
		} else {
			Tpl::output('a_mobile', $a_info['member_mobile']);
			Tpl::output('area', $area);
			Tpl::output('member_mobile', $member_access);
			$this->_profile_menu('access_register', 'access_register');
			Tpl::showpage('member.access_register');
		}
	}

	/**
	 * change_passwdOp 修改登录密码
	 * @return [type] [description]
	 */
	public function change_passwdOp() {
		if (chksubmit()) {
			$validate = new Validate();
			$validate->validateparam = array(
				array('input' => $_POST['password_old'], 'require' => "true", 'message' => "请输入旧密码"),
				array('input' => $_POST['password'], 'require' => "true", 'message' => "请输入新密码"),
				array('input' => $_POST['password'], 'validator' => "length", 'min' => 6, 'max' => 20, 'message' => "密码长度至少是6位"),
				array('input' => $_POST['password'], 'validator' => "compare", 'operator' => "==", 'to' => $_POST['password_confirm'], 'message' => "输入的密码和确认密码不相同"),
			);
			$error = $validate->validate();
			if ($error != '') {
				showMessage($error, '', '', 'error');
			}

			if ($this->member_info['member_passwd'] != md5(trim($_POST['password_old']))) {
				showMessage("输入的旧密码不正确", '', '', 'error');
			}
			$model_member = Model('member');
			$member_id = $this->member_info['member_id'];
			$update = $model_member->editMember(array('member_id' => $member_id), array('member_passwd' => md5(trim($_POST['password']))));
			if ($update) {
				showMessage('登录密码修改成功', 'index.php?act=member_info&op=member_account', '', 'succ');
			} else {
				showMessage("登录密码修改失败", '', '', 'error');
			}
		}
		$this->_profile_menu('account', 'change_passwd');
		Tpl::showpage('member.member_changepasswd');
	}

	/**
	 * change_paypwdOp 修改支付密码
	 * @return [type] [description]
	 */
	public function change_paypwdOp() {
		if (chksubmit()) {
			$validate = new Validate();
			$validate->validateparam = array(
				array('input' => $_POST['captcha'], 'require' => "true", 'message' => "请输入验证码"),
				array('input' => $_POST['paypwd'], 'require' => "true", 'message' => "请输入新密码"),
				array('input' => $_POST['paypwd'], 'validator' => "length", 'min' => 6, 'max' => 20, 'message' => "密码长度至少是6位"),
				array('input' => $_POST['paypwd'], 'validator' => "compare", 'operator' => "==", 'to' => $_POST['paypwd_confirm'], 'message' => "输入的密码和确认密码不相同"),
			);
			$error = $validate->validate();
			if ($error != '') {
				showMessage($error, '', '', 'error');
			}
			$logic_connect_api = Logic('connect_api');
			$result = $logic_connect_api->checkSmsCaptcha($this->member_info['member_mobile'], $_POST['captcha'], 4);
			if (!$result['state']) {
				showMessage("验证码错误", "", "", "error");
			}
			$model_member = Model('member');
			$member_id = $this->member_info['member_id'];
			$update = $model_member->editMember(array('member_id' => $member_id), array('member_paypwd' => md5(trim($_POST['paypwd']))));
			if ($update) {
				showMessage('支付密码修改成功', 'index.php?act=member_info&op=member_account', '', 'succ');
			} else {
				showMessage("支付密码修改失败", '', '', 'error');
			}
		}
		$this->_profile_menu('account', 'change_paypwd');
		Tpl::showpage('member.member_changepaypwd');
	}

	/**
	 * _profile_menu 导航栏
	 * @param  [type] $menu_type [description]
	 * @param  string $menu_key  [description]
	 * @return [type]            [description]
	 */
	private function _profile_menu($menu_type, $menu_key = '') {
		$menu_arr = array();
		switch ($menu_type) {
		case 'account':
			$menu_arr = array(
				1 => array('menu_key' => 'member_account', 'menu_name' => '个人信息', 'menu_url' => 'index.php?act=member_info&op=member_account'),
				2 => array('menu_key' => 'bank_account', 'menu_name' => '银行账户', 'menu_url' => 'index.php?act=member_info&op=bank_account'),
//				3 => array('menu_key' => "change_passwd", 'menu_name' => "修改登录密码", 'menu_url' => "index.php?act=member_info&op=change_passwd"),
//				4 => array('menu_key' => "change_paypwd", 'menu_name' => "修改支付密码", 'menu_url' => "index.php?act=member_info&op=change_paypwd"),
			);
			break;
		case 'bank_add':
			$menu_arr = array(
				1 => array('menu_key' => 'member_account', 'menu_name' => '个人信息', 'menu_url' => 'index.php?act=member_info&op=member_account'),
				2 => array('menu_key' => 'bank_account', 'menu_name' => '银行账户', 'menu_url' => 'index.php?act=member_info&op=bank_account'),
				3 => array('menu_key' => 'bank_account_add', 'menu_name' => '添加银行账户', 'menu_url' => 'index.php?act=member_info&op=bank_account_add'),
			);
			break;
		case 'bank_edit':
			$menu_arr = array(
				1 => array('menu_key' => 'member_account', 'menu_name' => '个人信息', 'menu_url' => 'index.php?act=member_info&op=member_account'),
				2 => array('menu_key' => 'bank_account', 'menu_name' => '银行账户', 'menu_url' => 'index.php?act=member_info&op=bank_account'),
				3 => array('menu_key' => 'bank_account_edit', 'menu_name' => '编辑银行账户', 'menu_url' => 'index.php?act=member_info&op=bank_account_edit'),
			);
			break;

		case 'paypwd_setting':
			$menu_arr = array(
				1 => array('menu_key' => 'member_paypwd_setting', 'menu_name' => "设置支付密码", 'menu_url' => 'index.php?act=member_info&op=member_paypwd_setting'),
			);
			break;
		case 'direct_list':
			$menu_arr = array(
				1 => array('menu_key' => 'direct_list', 'menu_name' => "直推列表", 'menu_url' => 'index.php?act=member_info&op=direct_list'),
			);
			break;

		case 'invite_family_tree':
			$menu_arr = array(
				1 => array('menu_key' => 'invite_family_tree', 'menu_name' => "推荐族谱图", 'menu_url' => 'index.php?act=member_info&op=invite_family_tree'),
			);
			break;
		case 'access_family_tree':
			$menu_arr = array(
//				1 => array('menu_key' => 'access_family_tree', 'menu_name' => "接点族谱图", 'menu_url' => 'index.php?act=member_info&op=access_family_tree'),
			);
			break;
		case 'access_register':
			$menu_arr = array(
//				1 => array('menu_key' => 'access_family_tree', 'menu_name' => "接点族谱图", 'menu_url' => 'index.php?act=member_info&op=access_family_tree'),
//				2 => array('menu_key' => 'access_register', 'menu_name' => "接点人注册", 'menu_url' => 'index.php?act=member_info&op=access_register'),
			);
			break;
		}
		Tpl::output('member_menu', $menu_arr);
		Tpl::output('menu_key', $menu_key);
	}
}