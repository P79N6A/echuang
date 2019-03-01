<?php
/**
 * 前台登录 退出操作
 *
 */

defined('In33hao') or exit('Access Invalid!');

class loginControl extends BaseLoginControl {

	public function __construct() {
		parent::__construct();
	}

	/**
	 * indexOp 登录操作
	 * @return [type] [description]
	 */
	public function indexOp() {
		$model_member_extend = Model('member_extend');
		// 检查登录状态
		$model_member_extend->checkLoginMember();

		// if ($_GET['inajax'] == 1 && C('captcha_status_login') == '1') {
		//     $script = "document.getElementById('codeimage').src='index.php?act=seccode&op=makecode&nchash=".getNchash()."&t=' + Math.random();";
		// }
		$result = chksubmit(true, false, 'num');
		if ($result !== false) {
			if ($result === -11) {
				showDialog("非法提交", '', 'error', $script);
			} elseif ($result === -12) {
				showDialog("验证码错误", '', 'error', $script);
			}

			$login_info = array();
			$login_info['member_mobile'] = $_POST['user_name'];
			$login_info['member_pwd'] = $_POST['password'];
			$member_info = $model_member_extend->memberLogin($login_info);

			if (isset($member_info['error'])) {
				showDialog($member_info['error'], '', 'error', $script);
			}

			// 自动登录
			// $member_info['auto_login'] = $_POST['auto_login'];
			$model_member_extend->createSession($member_info, true);
			@header("location: " . urlMemberSystem('member_info'));
		} else {
			Tpl::output('html_title', "会员系统登录");
			Tpl::showpage('login');
		}
	}

	/**
	 * 退出操作
	 *
	 * @param int $id 记录ID
	 * @return array $rs_row 返回数组形式的查询结果
	 */
	public function logoutOp() {
		Language::read("home_login_index");
		$lang = Language::getLangContent();
		// 清理COOKIE
		setNcCookie('msgnewnum' . $_SESSION['member_id'], '', -3600);
		setNcCookie('auto_login', '', -3600);
		setNcCookie('cart_goods_num', '', -3600);
		session_unset();
		session_destroy();
		if (empty($_GET['ref_url'])) {
			$ref_url = getReferer();
		} else {
			$ref_url = $_GET['ref_url'];
		}
		redirect(MEMBER_SITE_URL . '/index.php?act=login');

	}

	/**
	 * registerOp 会员注册
	 * @return [type] [description]
	 */
	public function registerOp() {
		$model_setting = Model('setting');
        $setting=$model_setting->getListSetting();
        $condition=$setting['captcha_status_register'];
		$ref_url = $_GET['ref_url'] ? $_GET['ref_url'] : '';
		Language::read("home_login_register");
		$lang = Language::getLangContent();
		$model_member = Model('member');
		$model_member->checkloginMember();
		if (isset($_GET['t'])) {
			$invite = trim($_GET['t']);
			$info = $model_member->getMemberInfo(array('member_mobile' => $invite));
			if (empty($info)) {
				showMessage('邀请链接失效', 'index.php?act=login&op=register', '', 'error');
			}
			Tpl::output('invite', $invite);
		}
		Tpl::output('condition', $condition);
		Tpl::output('ref_url', $ref_url);
		Tpl::output('html_title', C('site_name') . ' - ' . $lang['login_register_join_us']);
		Tpl::showpage('register');
	}

	/**
	 * member_saveOp 添加会员
	 * @return [type] [description]
	 */
	public function member_saveOp() {
		// 重复注册验证
		//if (process::islock('reg')) {
		//	showDialog('您的操作过于频繁，请稍后再试', '', 'error');
		//}

		$model_member_extend = Model('member_extend');
		$model_member_extend->checkLoginMember();
		$result = chksubmit(false, C('captcha_status_register'), 'num');
		if ($result) {
			if ($result === -11) {
				showDialog("非法访问", '', 'error');
			} elseif ($result === -12) {
				showDialog("验证码错误", '', 'error');
			}
		} else {
			showDialog("非法访问", '', 'error');
		}

		$member_name = trim($_POST['member_name']);
		$member_invite = trim($_POST['member_invite']);
		$member_access = trim($_POST['member_access']);
		$member_mobile = trim($_POST['member_mobile']);
		$logic_connect_api = Logic('connect_api');
////		$result = $logic_connect_api->checkSmsCaptcha($_POST['member_mobile'], $_POST['sms_captcha'], 1);
////		if (!$result['state']) {
//			showDialog($result['msg'], '', 'error');
//		}
		$info = $model_member_extend->getMemberInfo(array('member_mobile' => $member_mobile));
		if (!empty($info)) {
			showDialog('手机号已注册', '', 'error');
		}
		$invite_id = 0;
		$invite_one = 0;
		$invite_two = 0;
		$invite_three = 0;
		$invite_info = $model_member_extend->getMemberInfo(array('member_mobile' => $member_invite));
		if (empty($invite_info)) {
			showDialog('邀请人未注册', '', 'error');
		} else {
			$invite_id = $invite_info['member_id'];
			$invite_one = $invite_id;
			$invite_two = $invite_info['invite_one'];
			$invite_three = $invite_info['invite_two'];
		}
		$mobile_arr = $model_member_extend->getInviteChildMobileArr($invite_id);

		$access_id = 0;
		$depth = 1;
		$access_info = $model_member_extend->getMemberExtendInfo(array('member_mobile' => $member_access), 'member_id,depth,member_mobile', 'union');

//		//判断接点人是否跟推荐人在同一区域
//		$children_list = $this->get_children_id($invite_info['member_id']);
//		if ($access_info['member_mobile'] != $invite_info['member_mobile']) {
//			if (!in_array($access_info['member_mobile'], $children_list)) {
//				showDialog('接点人跟推荐人不在同一区域', 'index.php?act=login&op=register', 'error');
//			}
//		}

//		if (empty($access_info)) {
//			showDialog('接点人未注册', '', 'error');
//		} else {
//			$list = $model_member_extend->getMemberExtendList(array('access_id' => $access_info['member_id']));
//			if (is_array($list) && count($list) >= 2) {
//				showDialog('接点人错误，请重新换接点人', '', 'error');
//				// } elseif (!in_array($member_access, $mobile_arr)) {
//				// 	showDialog('接点人与邀请人不在同一区域', '', 'error');
//			} else {
//				$access_id = $access_info['member_id'];
//				$depth = $access_info['depth'] + 1;
//			}
//		}
        //开始扩大范围
        $record = Model('red_expand_area_record');
        $the_scope=Model('red_expand_the_scope');
        $member=Model('member');
        $member_id['member_mobile']=trim($_POST['member_invite']);
        $MemberInfo=$member->getMemberInfo($member_id);
        $condition=$MemberInfo['member_id'];
        $where['member_id']=$MemberInfo['member_id'];
        $member_select=$model_member_extend->getChildInfoArr($condition);
        $member_count=count($member_select);
        $result=$the_scope->red_getRow('1');
        $record_select=$record->getPdRechargeList($condition);
        $record_count=count($record_select);
        $count=floor($member_count/$result['recommended_registration']);
        //是否达到扩大范围的条件
        if($count>$record_count){
            $where_data['member_id']=$where['member_id'];//用户ID
            $where_data['extended_range_num']=$result['add_red_envelopes_range'];//扩大范围数量（公里）
            $where_data['add_redbag_num']=$result['add_red_envelopes_num'];//增加红包数
            $where_data['addtime']=time();//添加时间
            $where_data['user_num']=$member_count;//添加时间
            //増加扩大范围
            $record->getexpandadd($where_data);
        }
		$register_info = array();
		$register_info['member_mobile'] = $member_mobile;
		$register_info['username'] = $member_name;
		$register_info['password'] = trim($_POST['password']);
		$register_info['password_confirm'] = trim($_POST['password_confirm']);
		$register_info['inviter_id'] = $invite_id;
		//分销
		$register_info['invite_one'] = $invite_one;
		$register_info['invite_two'] = $invite_two;
		$register_info['invite_three'] = $invite_three;
		// 会员系统
		$register_info['invite_id'] = $invite_id;
		$register_info['access_id'] = $access_id;
		$register_info['depth'] = $depth;

		$member_info = $model_member_extend->memberRegister($register_info);

		if (!isset($member_info['error'])) {
			process::addprocess('reg');

			$_POST['ref_url'] = (strstr($_POST['ref_url'], 'logout') === false && !empty($_POST['ref_url']) ? $_POST['ref_url'] : urlMemberSystem('member_info', 'index'));
			if ($_GET['inajax'] == 1) {
				showDialog('', $_POST['ref_url'] == '' ? 'reload' : $_POST['ref_url'], 'js');
			} else {
				redirect($_POST['ref_url']);
			}
		} else {
			showDialog($member_info['error']);
		}
	}
	/**
	 * 递归查询下级的所有人
	 * @param  [type] $seller_id   [description]
	 * @param  [type] $seller_name [description]
	 * @param  [type] $client      [description]
	 * @return [type]              [description]
	 */
	private function get_children_id($pid, &$cids = array()) {
		$inviter_id['access_id'] = $pid;
		$children_id_list = Model('member_extend')->getMemberExtendList($inviter_id, 'member_mobile,member_id', 'union');
		foreach ($children_id_list as $key => $value) {
			if (!empty($value['member_mobile'])) {
				$cids[] = $value['member_mobile'];
				$this->get_children_id($value['member_id'], $cids);
			}
		}
		return $cids;
	}

	/**
	 * ajax_check_member_mobileOp 会员手机号检测
	 * @return [type] [description]
	 */
	public function ajax_check_member_mobileOp() {
		$info = Model('member')->getMemberInfo(array('member_mobile' => $_GET['member_mobile']));
		if (empty($info)) {
			echo 'true';
		} else {
			echo 'false';
		}
	}

	/**
	 * ajax_check_member_inviteOp 邀请人检测
	 * @return [type] [description]
	 */
	public function ajax_check_member_inviteOp() {
		$info = Model('member')->getMemberInfo(array('member_mobile' => $_GET['member_invite']));
		if (!empty($info)) {
			echo 'true';
		} else {
			echo 'false';
		}
	}

	/**
	 * ajax_check_member_accessOp 接点人检测
	 * @return [type] [description]
	 */
	public function ajax_check_member_accessOp() {
		$info = Model('member')->getMemberInfo(array('member_mobile' => $_GET['member_access']));
		if (!empty($info)) {
			$member_id = $info['member_id'];
			$info = Model('member_extend')->getMemberExtendList(array('access_id' => $member_id));
			if (empty($info) || count($info) < 2) {
				echo 'true';
			} else {
				echo 'false';
			}
		} else {
			echo 'false';
		}
	}

	/**
	 * get_captchaOp 获取验证码
	 * @return [type] [description]
	 * sms_type:1 注册 2登录 3修改登录密码 4修改支付密码
	 */
	public function get_sms_captchaOp() {
		$validate = new Validate();
		$validate->validateparam = array(
			array('input' => $_POST['mobile'], 'require' => "true", 'message' => "请输入手机号码"),
			array('input' => $_POST['mobile'], 'validator' => "mobile", 'message' => "手机号码格式不正确"),
			array('input' => $_POST['sms_type'], 'require' => "true", 'message' => "请输入短信类型"),
		);
		$error = $validate->validate();
		if ($error != '') {
			responseApiJson(0, $error);
		}

		$logic_connect_api = Logic('connect_api');
		$result = $logic_connect_api->sendCaptcha($_POST['mobile'], $_POST['sms_type']);
		if ($result['state']) {
			responseApiJson(1, $result['msg']);
		} else {
			responseApiJson(0, $result['msg']);
		}
	}

}
