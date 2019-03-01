<?php
/**
 * 会员管理
 *
 */
defined('In33hao') or exit('Access Invild!');
class memberControl extends SystemControl {
	public function __construct() {
		parent::__construct();
	}

	public function indexOp() {
		$this->member_manageOp();
	}

	/**
	 * member_manageOp 会员管理
	 * @return [type] [description]
	 */
	public function member_manageOp() {
		Tpl::setDirquna('member_center');
		Tpl::showpage('member.member_manage');
	}

	/**
	 * member_editOp 会员编辑
	 * @return [type] [description]
	 */
	public function member_editOp() {
//	    var_dump($_POST);die();
		if (chksubmit()) {
			$validate = new Validate();
			$validate->validateparam = array();
			if (!empty($_POST['member_password'])) {
				$validate->validateparam = array(
					array('input' => $_POST['member_password'], 'require' => "true", 'validator' => "length", 'min' => "1", 'max' => "10", 'message' => "密码长度为6~20"),
					array('input' => $_POST['member_password'], 'require' => "true", 'validator' => "compare", 'operator' => "==", 'to' => $_POST['member_password_confirm'], 'message' => "两次输入的登录密码不同"),
				);
			}
			if (!empty($_POST['member_paypwd'])) {
				$validate->validateparam = array(
					array('input' => $_POST['member_paypwd'], 'require' => "true", 'validator' => "length", 'min' => "1", 'max' => "10", 'message' => "密码长度为6~20"),
					array('input' => $_POST['member_paypwd'], 'require' => "true", 'validator' => "compare", 'operator' => "==", 'to' => $_POST['member_paypwd_confirm'], 'message' => "两次输入的支付密码不同"),
				);
			}

			$validate->validateparam = array(
				array('input' => $_POST['member_name'], 'require' => "true", 'message' => "会员姓名不能为空"),
				array('input' => $_POST['member_name'], 'validator' => "chinese", 'message' => "会员姓名须为中文"),
			);
			$error = $validate->validate();
			if ($error != '') {
				showMessage($error, '', '', 'error');
			}
			$member_id = trim($_POST['member_id']);
			$update_data = array();
			if (!empty($_POST['member_password'])) {
				$update_data['member_passwd'] = md5(trim($_POST['member_password']));
			}
			if (!empty($_POST['member_paypwd'])) {
				$update_data['member_paypwd'] = md5(trim($_POST['member_paypwd']));
			}
			$update_data['member_name'] = trim($_POST['member_name']);
			$update_data['member_state'] = $_POST['member_state'];
			$update = Model('member')->editMember(array('member_id' => $member_id), $update_data);
			if ($update) {
                if (isset($_POST['member_level'])){
                    $member_level = $_POST['member_level'];
                    $member_level_update = Model('member_extend')->editMemberLevel(array('me_member_id' => $member_id),array('member_level'=>$member_level));
                    if ($member_level_update){
                        showMessage('会员编辑成功', 'index.php?act=member&op=member_manage', '', 'succ');
                    }else{
                        echo 111;
                        showMessage('会员编辑失败', '', '', 'error');
                    }
                }
			} else {
				showMessage('会员编辑失败', '', '', 'error');
			}
		} else {
			$member_id = intval(trim($_GET['member_id']));
			if ($member_id) {
				$model_member_extend = Model('member_extend');
				$mobile_arr = $model_member_extend->getMemberMobileArr();
				$fields = 'member_name,member_mobile,invite_id,access_id,member_state,member_id,member_level';
				$info = $model_member_extend->getMemberExtendInfo(array('member_id' => $member_id), $fields, 'union');
//				var_dump($info);die();
				Tpl::output('mobilearr', $mobile_arr);
				Tpl::output('info', $info);
				Tpl::setDirquna('member_center');
				Tpl::showpage('member.member_edit');
			}
		}
	}

	/**
	 * member_addOp 会员添加
	 * @return [type] [description]
	 */
	public function member_addOp() {
//	    var_dump($_POST);die();
		if (chksubmit()) {
			$model_member_extend = Model('member_extend');
			$member_invite = trim($_POST['member_invite']);
//			$member_access = trim($_POST['member_access']);
			$invite_info = $model_member_extend->getMemberExtendInfo(array('member_mobile' => $member_invite), '*', 'union');
			if (empty($invite_info)) {
				showMessage('邀请人未注册', '', '', 'error');
			}
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
//			$access_info = $model_member_extend->getMemberExtendInfo(array('member_mobile' => $member_access), '*', 'union');
//			if (empty($access_info)) {
//				showMessage('接点人未注册', '', '', 'error');
//			}
//			$count = $model_member_extend->getMemberAccessCount($access_info['member_id']);
//			if ($count >= 2) {
//				showMessage('此接点人已接满,请更换接点人', '', '', 'error');
//			}
			//判断接点人是否跟推荐人在同一区域
//			$children_list = $this->get_children_id($invite_info['member_id']);
//			if ($access_info['member_mobile'] != $invite_info['member_mobile']) {
//				if (!in_array($access_info['member_mobile'], $children_list)) {
//					responseApiJson(0, '接点人跟推荐人不在同一区域');
//				}
//			}
            $model_setting = Model('setting');
            $where_name['name']='default_user_portrait';
            $setting_data=$model_setting->getRowSetting($where_name);
            $register_data['member_avatar']=$setting_data['value'];
			$register_data = array();
			$register_data['username'] = trim($_POST['member_name']);
			$register_data['member_mobile'] = trim($_POST['member_mobile']);
			$register_data['password'] = trim($_POST['member_password']);
			$register_data['password_confirm'] = trim($_POST['member_password_confirm']);
			$register_data['inviter_id'] = $invite_info['member_id'];
			$register_data['invite_one'] = $invite_info['member_id'];
			$register_data['invite_two'] = $invite_info['invite_one'];
			$register_data['invite_three'] = $invite_info['invite_two'];
			$register_data['invite_id'] = $invite_info['member_id'];
//			$register_data['access_id'] = $access_info['member_id'];
            $register_data['member_level'] = trim($_POST['member_level']);
			$register_data['left_right_area'] = 0;
//			$register_data['depth'] = $access_info['depth'] + 1;
			$result = $model_member_extend->memberRegister($register_data, false);
//			var_dump($result);die();
			if (isset($result['error'])) {
				showMessage('会员添加失败', '', '', 'error');
			} else {
				showMessage('会员添加成功', 'index.php?act=member&op=member_manage', '', 'succ');
			}
		}
		Tpl::setDirquna('member_center');
		Tpl::showpage('member.member_add');
	}

	/**
	 * 递归查询下级的所有人
	 * @param  [type] $seller_id   [description]
	 * @param  [type] $seller_name [description]
	 * @param  [type] $client      [description]
	 * @return [type]              [description]
	 */
	private function get_children_id($pid, &$cids = array()) {
		$children_id_list = Model('member_extend')->getMemberExtendList(array('access_id' => $pid), 'member_mobile,member_id', 'union');
		foreach ($children_id_list as $key => $value) {
			if (!empty($value['member_mobile'])) {
				$cids[] = $value['member_mobile'];
				$this->get_children_id($value['member_id'], $cids);
			}
		}
		return $cids;
	}

	/**
	 * get_xmlOp 获取会员列表
	 * @return [type] [description]
	 */
	public function get_xmlOp() {
		$model_member_extend = Model('member_extend');
		$member_model = Model('member');
		$each_member_level_num = $member_model->getMemberLevelNum();
		$condition = array();
		$this->_get_condition($condition);

		$order = '';
		$order_member_extend = '';
		$param_member = array('member_id', 'member_name', 'member_mobile', 'member_time', 'member_login_time');
		$param_member_extend = array('member_golden_bean', 'member_golden_bean_payed', 'member_equity', 'total_performance', 'new_performance', 'surplus_performance', 'day_total_consume', 'total_consume', 'depth','member_level');
		if (in_array($_POST['sortname'], $param_member) && in_array($_POST['sortorder'], array('asc', 'desc'))) {
			$order = 'member.' . $_POST['sortname'] . ' ' . $_POST['sortorder'];
		} elseif (in_array($_POST['sortname'], $param_member_extend) && in_array($_POST['sortorder'], array('asc', 'desc'))) {
			$order = 'member_extend.' . $_POST['sortname'] . ' ' . $_POST['sortorder'];
		}

		$page = $_POST['rp'];
		$member_list = $model_member_extend->getMemberExtendList($condition, '*', 'union', $page, $order);
		$data = array();
		$data['now_page'] = $model_member_extend->shownowpage();
		$data['total_num'] = $model_member_extend->gettotalnum();

		foreach ($member_list as $v) {
			$param = array();
			$encrypt = encrypt($v['member_id']);
			$param['operation'] = "<a class='btn blue' href='index.php?act=member&op=member_edit&member_id=" . $v['member_id'] . "'><i class='fa fa-pencil-square-o'></i>编辑</a>";
			$param['member_mobile'] = $v['member_mobile'];
			$param['member_name'] = $v['member_name'];
			$param['invite_mobile'] = Model('member')->getMemberMobileById($v['invite_id']);
//			$param['access_mobile'] = Model('member')->getMemberMobileById($v['access_id']);
			$param['member_time'] = date('Y-m-d H:i:s', $v['member_time']);
			$param['member_state'] = str_replace(array(0,1), array('冻结', '正常'), $v['member_state']);
			$param['member_level'] = str_replace(array(0,1,2,3,4,5), array('体验用户','VIP','店主','合伙人','高级合伙人','战略合伙人'), $v['member_level']);
			$param['invite_family_tree'] = "<a class='btn blue' href='index.php?act=member&op=invite_family_tree&mobile=" . $v['member_mobile'] . "'><i class='fa fa-pencil-square-o'></i>推荐族谱图</a>";
//			$param['access_family_tree'] = "<a class='btn blue' href='index.php?act=member&op=access_family_tree&mobile=" . $v['member_mobile'] . "'><i class='fa fa-pencil-square-o'></i>接点族谱图</a>";
//			$param['direct_login'] = "<a class='btn blue' target='_bank' href=" . MEMBER_SYSTEM_SITE_URL . DS . "index.php?act=member_info&op=index&flag=direct_login&member_id=" . $v['member_id'] . "&encrypt=" . $encrypt . "><i class='fa fa-pencil-square-o'></i>账号登录</a>";
			$data['list'][$v['member_id']] = $param;
//            var_dump($v['member_level']);
        }
//        $data['level_num'] = $each_member_level_num;
//        die();
		Tpl::flexigridXML($data);
		exit();
	}

	/**
	 * invite_family_treeOp 推荐族谱图
	 * @return [type] [description]
	 */
	public function invite_family_treeOp() {
		$model_member_extend = Model('member_extend');
		$member_mobile = '';
		if (chksubmit()) {
			if ($_POST['mobile']) {
				$member_mobile = $_POST['mobile'];

			}
		} else {
			$member_mobile = trim($_GET['mobile']);
		}

		$info = $model_member_extend->getMemberInfo(array('member_mobile' => $member_mobile));
		if (!empty($info)) {
			Tpl::output('member_id', $info['member_id']);
			Tpl::output('member_truename', $info['member_truename']);
			Tpl::output('member_mobile', $info['member_mobile']);
		} else {
			Tpl::output('member_id', '');
		}
		header('Cache-control:private,must-revalidate');
		Tpl::output('mobile', $member_mobile);
		Tpl::setDirquna('member_center');
		Tpl::showpage('member.invite_family_tree');
	}

	/**
	 * access_family_treeOp 接点族谱图
	 * @return [type] [description]
	 */
	public function access_family_treeOp() {
		$model_member_extend = Model('member_extend');
		$member_mobile = '';
		if (chksubmit()) {
			if ($_POST['mobile']) {
				$member_mobile = $_POST['mobile'];
			}
		} else {
			$member_mobile = trim($_GET['mobile']);
		}
		$info = Model('member')->getMemberInfo(array('member_mobile' => $member_mobile));
		if (!empty($info)) {
			$binary_tree[] = $model_member_extend->buildBinaryTreeArr($info['member_id']);
			$htmlFamilytree = $model_member_extend->buildFamiltree($binary_tree);
			Tpl::output('tree', $htmlFamilytree);
		} else {
			Tpl::output('tree', array());
		}
		header('Cache-control:private,must-revalidate');
		Tpl::output('mobile', $member_mobile);
		Tpl::setDirquna('member_center');
		Tpl::showpage('member.access_family_tree');
	}

	/**
	 * export_xlsOp 导出execl文件
	 * @return [type] [description]
	 */
	public function export_xlsOp() {
		$model_member_extend = Model('member_extend');
		$id = $_GET['id'];
		$condition = array();
		if ($id) {
			$condition['member_id'] = array('in', $id);
		} else {
			$this->_get_condition($condition);
		}
		$data = $model_member_extend->getMemberExtendList($condition, '*', 'union', null, 'me_member_id desc', false);
		$excel_obj = new Excel();
		$excel_data = array();
		// 设置样式
		$excel_obj->setStyle(array('id' => 's_title', 'Font' => array('FontName' => '宋体', 'Size' => '12', 'Bold' => '1')));
		// header
		$excel_data[0][] = array('styleid' => 's_title', 'data' => '会员ID');
		$excel_data[0][] = array('styleid' => 's_title', 'data' => '会员手机');
		$excel_data[0][] = array('styleid' => 's_title', 'data' => '会员名字');
		$excel_data[0][] = array('styleid' => 's_title', 'data' => '推荐人手机');
//		$excel_data[0][] = array('styleid' => 's_title', 'data' => '接点人手机');
		$excel_data[0][] = array('styleid' => 's_title', 'data' => '注册时间');
		$excel_data[0][] = array('styleid' => 's_title', 'data' => '会员等级');
		$excel_data[0][] = array('styleid' => 's_title', 'data' => '会员状态');

		foreach ((array) $data as $k => $v) {
			$tmp = array();
			$tmp[] = array('data' => $v['member_id']);
			$tmp[] = array('data' => $v['member_mobile']);
			$tmp[] = array('data' => $v['member_name']);
			$tmp[] = array('data' => Model('member')->getMemberMobileById($v['invite_id']));
			$tmp[] = array('data' => date('Y-m-d H:i:s', $v['reg_time']));
            $tmp[] = array('data' =>str_replace(array(0,1,2,3,4,5), array('体验用户','VIP','店主','合伙人','高级合伙人','战略合伙人'), $v['member_level']));
            $tmp[] = array('data' =>str_replace(array(0,1), array('冻结', '正常'), $v['member_state']));
			$excel_data[] = $tmp;
		}
		$excel_data = $excel_obj->charset($excel_data, CHARSET);
		$excel_obj->addArray($excel_data);
		$excel_obj->addWorksheet($excel_obj->charset('会员资料', CHARSET));
		$excel_obj->generateXML($excel_obj->charset('会员资料', CHARSET) . $_GET['curpage'] . '-' . date('Y-m-d-H', time()));
	}

	/**
	 * ajax_check_member_mobileOp 用户手机号
	 * @return [type] [description]
	 */
	public function ajax_check_member_mobileOp() {
		$model_member_extend = Model('member_extend');
		$member_mobile = $_GET['member_mobile'];
		$info = $model_member_extend->getMemberExtendInfo(array('member_mobile' => $member_mobile), '*', 'union');
		if (empty($info)) {
			echo "true";
		} else {
			echo "false";
		}
	}

	/**
	 * ajax_check_member_inviteOp 邀请人
	 * @return [type] [description]
	 */
	public function ajax_check_member_inviteOp() {
		$model_member_extend = Model('member_extend');
		$member_invite = $_GET['member_invite'];
		$info = $model_member_extend->getMemberExtendInfo(array('member_mobile' => $member_invite), '*', 'union');
		if (empty($info)) {
			echo "false";
		} else {
			echo "true";
		}
	}

	/**
	 * ajax_check_member_accessOp 接点人
	 * @return [type] [description]
	 */
	public function ajax_check_member_accessOp() {
		$model_member_extend = Model('member_extend');
		$member_access = $_GET['member_access'];
		$info = $model_member_extend->getMemberExtendInfo(array('member_mobile' => $member_access), '*', 'union');
		if (empty($info)) {
			echo "false";
		} else {
			$access_member_id = $info['member_id'];
			$count = $model_member_extend->getMemberAccessCount($access_member_id);
			if ($count >= 2) {
				echo "false";
			} else {
				echo "true";
			}
		}
	}


	/**
	 * get_invite_listOp 获取推荐人列表
	 * @return [type] [description]
	 */
	public function get_invite_listOp() {
		$member_id = trim($_GET['member_id']);
		$invite_list = Model('member_extend')->getMemberExtendList(array('invite_id' => $member_id), '*', 'union', null, 'reg_time desc');
		echo json_encode($invite_list);
	}

	/**
	 * get_access_listOp 获取接点人列表
	 * @return [type] [description]
	 */
	public function get_access_listOp() {
		$member_id = trim($_GET['member_id']);
		$access_list = Model('member_extend')->getMemberExtendList(array('access_id' => $member_id), '*', 'union', null, 'reg_time desc');
		echo json_encode($access_list);
	}

	/**
	 * access_registerOp 接点人注册
	 * @return [type] [description]
	 */
	public function access_registerOp() {
		$model_member_extend = Model('member_extend');
		$access_id = $_GET['access_id'] ? $_GET['access_id'] : $_POST['access_id'];

		$result = $model_member_extend->checkAccessInfoById($_GET['access_id']);
		if (isset($result['error'])) {
			showMessage($result['error'], '', '', 'error');
		}
		$access_info = $result;
		if (chksubmit()) {

			$validate = new Validate();
			$validate->validateparam = array(
				array('input' => $_POST['member_mobile'], 'require' => "true", 'message' => "未填会员手机号"),
				array('input' => $_POST['member_mobile'], 'validator' => "mobile", 'message' => "会员手机号格式不正确"),
				array('input' => $_POST['member_name'], 'require' => "true", 'message' => "未填写用户名"),
				array('input' => $_POST['member_name'], 'validator' => "chinese", 'message' => "会员名须为中文"),
				array('input' => $_POST['member_invite'], 'require' => "true", 'message' => "未填写邀请人"),
				array('input' => $_POST['member_invite'], 'validator' => "mobile", 'message' => "邀请人手机号格式不正确"),
				array('input' => $_POST['member_access'], 'require' => "true", 'message' => "未填写接点人"),
				array('input' => $_POST['member_access'], 'validator' => "mobile", 'message' => "接点手机号格式不正确"),
				array('input' => $_POST['member_password'], 'require' => "true", 'message' => "用户密码必填"),
				array('input' => $_POST['member_password'], 'validator' => "length", 'min' => 6, 'max' => 20, 'message' => "用户密码至少6位,至多20位"),
				array('input' => $_POST['member_password'], 'validator' => "compare", 'to' => $_POST['member_password_confirm'], 'message' => "用户密码与确认密码填写不一致"),
			);
			$error = $validate->validate();
			if ($error != '') {
				showMessage($error, '', '', 'error');
			}
			$result = $model_member_extend->checkInviteInfoByMobile($_POST['member_invite']);
			if (isset($result['error'])) {
				showMessage($result['error'], '', '', 'error');
			}
			$invite_info = $result;
			//判断接点人是否跟推荐人在同一区域
			$children_list = $this->get_children_id($invite_info['member_id']);
			if ($access_info['member_mobile'] != $invite_info['member_mobile']) {
				if (!in_array($access_info['member_mobile'], $children_list)) {
					showMessage('接点人跟推荐人不在同一区域', '', '', 'error');
				}
			}
			$register_data = array();
			$register_data['username'] = trim($_POST['member_name']);
			$register_data['member_mobile'] = trim($_POST['member_mobile']);
			$register_data['password'] = trim($_POST['member_password']);
			$register_data['password_confirm'] = trim($_POST['member_password_confirm']);
			$register_data['inviter_id'] = $invite_info['member_id'];
			$register_data['invite_one'] = $invite_info['member_id'];
			$register_data['invite_two'] = $invite_info['invite_one'];
			$register_data['invite_three'] = $invite_info['invite_two'];
			$register_data['invite_id'] = $invite_info['member_id'];
			$register_data['access_id'] = $access_info['member_id'];
			$area = $_POST['area'];
			if ($area == 'left') {
				$register_data['left_right_area'] = 1;
			}
			if ($area == 'right') {
				$register_data['left_right_area'] = 2;
			}
			$register_data['depth'] = $access_info['depth'] + 1;
			$result = $model_member_extend->memberRegister($register_data, false);
			if (isset($result['error'])) {
				showMessage('会员注册失败', '', '', 'error');
			} else {
				showMessage('会员注册成功', 'index.php?act=member&op=member_manage', '', 'succ');
			}
		} else {
			Tpl::output('area', $_GET['area']);
			Tpl::output('access_id', $access_id);
			Tpl::output('member_access', $access_info['member_mobile']);
		}
		$validate = new Validate();

		Tpl::setDirquna('member_center');
		Tpl::showpage('member.member_add');
	}

	/**
	 * _get_condition 搜索条件整理
	 * @param  [type] &$condition [description]
	 * @return [type]             [description]
	 */
	private function _get_condition(&$condition) {
		$param = $_REQUEST;

		if ($param['query'] != '') {
			if ($param['qtype'] == 'invite_mobile' || $param['qtype'] == 'access_mobile') {
				$list = Model('member_extend')->getMemberList(array('member_mobile' => array('like', '%' . $param['query'] . '%')));
				if (!empty($list)) {
					$arr = array();
					foreach ($list as $v) {
						$arr[] = $v['member_id'];
					}
					if ($param['qtype'] == 'invite_mobile') {
						$condition['invite_id'] = array('in', $arr);
					} else {
						$condition['access_id'] = array('in', $arr);
					}
				} else {
					$condition[$param['qtype']] = array('like', '%' . $param['query'] . '%');
				}
			} else {
				$condition[$param['qtype']] = array('like', '%' . $param['query'] . '%');
			}
		}

		if ($param['keyword_type'] && $param['keyword']) {
			$condition[$param['keyword_type']] = array('like', '%' . $param['keyword'] . '%');
		}

		if ($param['member_state'] != '') {
			$condition['member_state'] = $param['member_state'];
		}

		if ($param['query_start_date'] && $param['query_end_date'] && strtotime($param['query_start_date']) <= strtotime($param['query_end_date'])) {
			$condition['member_time'] = array('between', array(strtotime($param['query_start_date']), strtotime("+1 day", strtotime($param['query_end_date'])) - 1));
		} elseif ($param['query_start_date']) {
			$condition['member_time'] = array('egt', strtotime($param['query_start_date']));
		} elseif ($param['query_end_date']) {
			$condition['member_time'] = array('elt', strtotime("+1 day", strtotime($param['query_end_date'])) - 1);
		}
	}

    /**
     * 获取各等级会员总人数
     */
	public function getMemberLevelNumOp(){
	    $member = Model('member');
	    $level_num = $member->getMemberLevelNum();
	    exit(json_encode($level_num));
    }
}