<?php
/**
 * 积分回购管理
 *
 */
defined('In33hao') or exit('Access Invild!');
class withdrawControl extends SystemControl {
	public function __construct() {
		parent::__construct();
	}

	public function indexOp() {
		$this->withdraw_manageOp();
	}

	/**
	 * withdraw_manageOp 积分回购管理
	 * @return [type] [description]
	 */
	public function withdraw_manageOp() {
		Tpl::setDirquna('member_center');
		Tpl::showpage('withdraw.withdraw_manage');
	}

	/**
	 * withdraw_paymentOp 积分回购支付
	 * @return [type] [description]
	 */
	public function withdraw_paymentOp() {
	    if (empty($_GET['id'])){
            showMessage('请选择要操作的数据项！', '', '', 'error');
        }
		$ids = explode(',', $_GET['id']);
		if (count($ids) == 0) {
			if (isset($_GET['type'])) {
				showMessage(L('wrong_argument'), '', '', 'error');
			} else {
				exit(json_encode(array('state' => false, 'msg' => L('wrong_argument'))));
			}
		}
		foreach ($ids as $id) {
			$data['id'] = $id;
			$data['admin_name'] = $this->admin_info['name'];
			$result = Model('member_extend')->withdraw_confirm($data);
			if (isset($result['error'])) {
				if (isset($_GET['type'])) {
					showMessage('积分回购失败', '', '', 'error');
				} else {
					exit(json_encode(array('state' => false, 'msg' => '积分回购失败')));
				}
			}
		}
		if (isset($_GET['type'])) {
			showMessage('积分回购成功', '', '', 'error');
		} else {
			exit(json_encode(array('state' => true, 'msg' => "积分回购成功")));
		}
	}

	/**
	 * withdraw_refuseOp 积分回购拒绝
	 * @return [type] [description]
	 */
	public function withdraw_refuseOp() {
        if (empty($_GET['id'])){
            showMessage('请选择要操作的数据项！', '', '', 'error');
        }
		$ids = explode(',', $_GET['id']);
		if (count($ids) == 0) {
			if ($_GET['type'] == 'one') {
				showMessage(L('wrong_argument'), '', '', 'error');
			}
			exit(json_encode(array('state' => false, 'msg' => L('wrong_argument'))));
		}

		// sort($ids);
		foreach ($ids as $id) {
			$data['id'] = $id;
			$data['admin_name'] = $this->admin_info['name'];
			$result = Model('member_extend')->withdraw_refuse($data);
			if (isset($result['error'])) {
				if (isset($_GET['type'])) {
					showMessage("拒绝失败", '', '', 'error');
				} else {
					exit(json_encode(array('state' => false, 'msg' => '拒绝失败')));
				}
			}
			// $withdraw_id['wl_id'] = $value;
			// $withdraw_info = Model('bean')->getBeanWithdrawLogInfo($withdraw_id);
			// if (!empty($withdraw_info)) {
			// 	$withdraw_sn[] = $withdraw_info['wl_sn'];
			// }
		}
		if (isset($_GET['type'])) {
			showMessage('拒绝成功', '', '', 'succ');
		} else {
			exit(json_encode(array('state' => true, 'msg' => '拒绝成功')));
		}

		// $data = array();
		// $data['withdraw_sn'] = $withdraw_sn;
		// $data['admin_name'] = $this->admin_info['name'];
		// $model_member_extend = Model('member_extend');
		// $result = $model_member_extend->withdraw_refuse($data);
		// if ($result) {
		// 	if ($_GET['type'] == 'one') {
		// 		showMessage('拒绝成功', '', '', 'succ');
		// 	}
		// 	exit(json_encode(array('state' => true, 'msg' => '拒绝成功')));
		// } else {
		// 	if ($_GET['type'] == 'one') {
		// 		showMessage('拒绝失败', '', '', 'error');
		// 	}
		// 	exit(json_encode(array('state' => true, 'msg' => '拒绝失败')));
		// }
	}

	/**
	 * withdraw_viewOp 查看积分回购信息
	 * @return [type] [description]
	 */
	public function withdraw_viewOp() {
		$model_balance= Model('balance');
		$sn = trim($_GET['sn']);
		$info = $model_balance->getBalanceWithdraw(array('wl_sn' => $sn));
		if (!empty($info)) {
			$name_arr = Model('member_extend')->getMemberNameArr();
			Tpl::output('n_info', $name_arr);
			Tpl::output('w_info', $info);
			Tpl::setDirquna('member_center');
			Tpl::showpage('withdraw.withdraw_view', 'null_layout');
		} else {
			showMessage('积分回购信息错误', '', '', 'error');
		}
	}

	/**
	 * get_xmlOp 获取积分回购数据
	 * @return [type] [description]
	 */
	public function get_xmlOp() {
		$model_balance = Model('balance');
		$condition = array();
		$this->_get_condition($condition);
		$order = 'wl_addtime desc';
		$page = $_POST['rp'];
		$withdraw_list = $model_balance->getBalanceWithdraw($condition,$page,$order);
		$mobile_arr = Model('member_extend')->getMemberMobileArr();
		$name_arr = Model('member_extend')->getMemberNameArr();
		$data = array();
		$data['now_page'] = $model_balance->shownowpage();
		$data['total_num'] = $model_balance->gettotalnum();
		foreach ($withdraw_list as $v) {
			$param = array();
			if ($v['wl_state'] != 0) {
				$param['operation'] = "<a class='btn green' href='javascript:void(0)' onclick=\"ajax_form('withdraw_info','查看积分回购编号“" . $v['wl_id'] . "”的明细', 'index.php?act=withdraw&op=withdraw_view&sn=" . $v['wl_sn'] . "', 640)\" ><i class='fa fa-list-alt'></i>查看</a>";
			} else {
				$param['operation'] = "<a class='btn blue' href='index.php?act=withdraw&op=withdraw_payment&type=one&id=" . $v['wl_id'] . "'><i class='fa fa-pencil-square-o'></i>支付</a><a class='btn blue' href='index.php?act=withdraw&op=withdraw_refuse&type=one&id=" . $v['wl_id'] . "'><i class='fa fa-pencil-square-o'></i>拒绝</a>";
			}
			$param['member_mobile'] = $mobile_arr[$v['wl_member_id']];
			$param['member_name'] = $name_arr[$v['wl_member_id']];
			$param['wl_sn'] = $v['wl_sn'];
			$param['wl_account_bank_name'] = $v['wl_account_bank_name'];
			$param['wl_account_branch'] = $v['wl_account_branch'];
			$param['wl_account_name'] = $v['wl_account_name'];
			$param['wl_account_num'] = $v['wl_account_num'];
//			$param['wl_type'] = str_replace(array('bean', 'equity'), array('金豆', '股权'), $v['wl_type']);
			$param['wl_amount'] = $v['wl_amount'];
			$param['wl_fee'] = $v['wl_fee'];
			$param['wl_actual_amount'] = floatval($v['wl_amount'] - $v['wl_fee']);
			$param['wl_addtime'] = $v['wl_addtime'] ? date('Y-m-d H:i:s', $v['wl_addtime']) : '';
			$param['wl_state'] = str_replace(array(0, 1, 2), array('待支付', '已支付', '<span style="color:red">已拒绝</span>'), $v['wl_state']);
			$param['wl_paytime'] = $v['wl_paytime'] ? date('Y-m-d H:i:s', $v['wl_paytime']) : '';
			$data['list'][$v['wl_id']] = $param;
		}
		echo Tpl::flexigridXML($data);
		exit();
	}

	/**
	 * export_xlsOp 导出数据
	 * @return [type] [description]
	 */
	public function export_xlsOp() {
		$condition = array();
		if ($_GET['id']) {
			$condition['wl_id'] = array('in', $_GET['id']);
		} else {
			$this->_get_condition($condition);
		}

		$data = Model('balance')->getBalanceWithdraw($condition);
		$excel_obj = new Excel();
		$excel_data = array();
		// 设置样式
		$excel_obj->setStyle(array('id' => 's_title', 'Font' => array('FontName' => '宋体', 'Size' => '12', 'Bold' => '1')));
		// header
		$excel_data[0][] = array('styleid' => 's_title', 'data' => '会员手机');
		$excel_data[0][] = array('styleid' => 's_title', 'data' => '会员姓名');
		$excel_data[0][] = array('styleid' => 's_title', 'data' => '积分回购单号');
		$excel_data[0][] = array('styleid' => 's_title', 'data' => '开户行');
		$excel_data[0][] = array('styleid' => 's_title', 'data' => '开户支行');
		$excel_data[0][] = array('styleid' => 's_title', 'data' => '开户姓名');
		$excel_data[0][] = array('styleid' => 's_title', 'data' => '银行帐号');
		$excel_data[0][] = array('styleid' => 's_title', 'data' => '积分回购金额');
		$excel_data[0][] = array('styleid' => 's_title', 'data' => '手续费');
		$excel_data[0][] = array('styleid' => 's_title', 'data' => '到账金额');
		$excel_data[0][] = array('styleid' => 's_title', 'data' => '积分回购时间');
		$excel_data[0][] = array('styleid' => 's_title', 'data' => '积分回购状态');
		$excel_data[0][] = array('styleid' => 's_title', 'data' => '支付时间');
		foreach ((array) $data as $k => $v) {
			$tmp = array();
			$tmp[] = array('data' => Model('member')->getMemberMobileById($v['wl_member_id']));
			$tmp[] = array('data' => Model('member')->getMemberNameById($v['wl_member_id']));
			$tmp[] = array('data' => $v['wl_sn']);
			$tmp[] = array('data' => $v['wl_account_bank_name']);
			$tmp[] = array('data' => $v['wl_account_branch']);
			$tmp[] = array('data' => $v['wl_account_name']);
			$tmp[] = array('data' => $v['wl_account_num']);
			$tmp[] = array('data' => floatval($v['wl_amount']));
			$tmp[] = array('data' => floatval($v['wl_fee']));
			$tmp[] = array('data' => floatval($v['wl_amount'] - $v['wl_fee']));
			$tmp[] = array('data' => $v['wl_addtime'] ? date('Y-m-d H:i:s', $v['wl_addtime']) : '');

			$tmp[] = array('data' => str_replace(array(0, 1, 2), array('待支付', '已支付', '已拒绝'), $v['wl_state']));
			$tmp[] = array('data' => $v['wl_paytime'] ? date('Y-m-d H:i:s', $v['wl_paytime']) : '');
			$excel_data[] = $tmp;
		}
		$excel_data = $excel_obj->charset($excel_data, CHARSET);
		$excel_obj->addArray($excel_data);
		$excel_obj->addWorksheet($excel_obj->charset('积分回购管理', CHARSET));
		$excel_obj->generateXML($excel_obj->charset('积分回购管理', CHARSET) . $_GET['curpage'] . '-' . date('Y-m-d-H', time()));
	}

	/**
	 * export_xlsOp 导出已处理,未处理的execl文件
	 * @return [type] [description]
	 */
	public function export_handle_exlsOp() {
		$condition = array();

		if ($_GET['type'] == 'unhandle') {
			$condition['wl_state'] = array('in', 0);
		}
		if ($_GET['type'] == 'handle') {
			$condition['wl_state'] = array('in', array(1, 2));
		}
		if ($_GET['type'] == 'refused') {
			$condition['wl_state'] = array('in', 2);
		}

		$data = Model('balance')->getBalanceWithdraw($condition);

		$excel_obj = new Excel();
		$excel_data = array();
		// 设置样式
		$excel_obj->setStyle(array('id' => 's_title', 'Font' => array('FontName' => '宋体', 'Size' => '12', 'Bold' => '1')));
		// header
		$excel_data[0][] = array('styleid' => 's_title', 'data' => '会员手机');
		$excel_data[0][] = array('styleid' => 's_title', 'data' => '会员姓名');
		$excel_data[0][] = array('styleid' => 's_title', 'data' => '积分回购单号');
		$excel_data[0][] = array('styleid' => 's_title', 'data' => '开户行');
		$excel_data[0][] = array('styleid' => 's_title', 'data' => '开户支行');
		$excel_data[0][] = array('styleid' => 's_title', 'data' => '开户姓名');
		$excel_data[0][] = array('styleid' => 's_title', 'data' => '银行帐号');
		$excel_data[0][] = array('styleid' => 's_title', 'data' => '积分回购金额');
		$excel_data[0][] = array('styleid' => 's_title', 'data' => '手续费');
		$excel_data[0][] = array('styleid' => 's_title', 'data' => '到账金额');
		$excel_data[0][] = array('styleid' => 's_title', 'data' => '积分回购时间');
		$excel_data[0][] = array('styleid' => 's_title', 'data' => '积分回购状态');
		$excel_data[0][] = array('styleid' => 's_title', 'data' => '支付时间');
		foreach ((array) $data as $k => $v) {
			$tmp = array();
			$tmp[] = array('data' => Model('member')->getMemberMobileById($v['wl_member_id']));
			$tmp[] = array('data' => Model('member')->getMemberNameById($v['wl_member_id']));
			$tmp[] = array('data' => $v['wl_sn']);
			$tmp[] = array('data' => $v['wl_account_bank_name']);
			$tmp[] = array('data' => $v['wl_account_branch']);
			$tmp[] = array('data' => $v['wl_account_name']);
			$tmp[] = array('data' => $v['wl_account_num']);
			$tmp[] = array('data' => floatval($v['wl_amount']));
			$tmp[] = array('data' => floatval($v['wl_fee']));
			$tmp[] = array('data' => floatval($v['wl_amount'] - $v['wl_fee']));
			$tmp[] = array('data' => $v['wl_addtime'] ? date('Y-m-d H:i:s', $v['wl_addtime']) : '');

			$tmp[] = array('data' => str_replace(array(0, 1, 2), array('未处理', '已支付', '已拒绝'), $v['wl_state']));
			$tmp[] = array('data' => $v['wl_paytime'] ? date('Y-m-d H:i:s', $v['wl_paytime']) : '');
			$excel_data[] = $tmp;
		}
		$excel_data = $excel_obj->charset($excel_data, CHARSET);
		$excel_obj->addArray($excel_data);
		$excel_obj->addWorksheet($excel_obj->charset('积分回购管理', CHARSET));
		$excel_obj->generateXML($excel_obj->charset('积分回购管理', CHARSET) . $_GET['curpage'] . '-' . date('Y-m-d-H', time()));
	}

	/**
	 * _get_condition 搜索条件整理
	 * @param  [type] &$condition [description]
	 * @return [type]             [description]
	 */
	private function _get_condition(&$condition) {
		$param = $_REQUEST;
		if ($param['query'] != '') {
			if ($param['qtype'] == 'member_mobile' || $param['qtype'] == 'member_name') {
				$list = array();
				if ($param['qtype'] == 'member_mobile') {
					$list = Model('member')->getMemberList(array('member_mobile' => array('like', '%' . $param['query'] . '%')));
				} else {
					$list = Model('member')->getMemberList(array('member_name' => array('like', '%' . $param['query'] . '%')));
				}
				if (!empty($list)) {
					$arr = array();
					foreach ($list as $v) {
						$arr[] = $v['member_id'];
					}
					$condition['wl_member_id'] = array('in', $arr);
				} else {
					$condition['wl_member_id'] = null;
				}
			} else {
				$condition[$param['qtype']] = array('like', '%' . $param['query'] . '%');
			}
		}

		if ($param['keyword_type'] && $param['keyword']) {
			$list = array();
			if ($param['keyword_type'] == 'member_mobile') {
				$list = Model('member')->getMemberList(array('member_mobile' => array('like', '%' . $param['keyword'] . '%')));
			} else {
				$list = Model('member')->getMemberList(array('member_name' => array('like', '%' . $param['keyword'] . '%')));
			}
			if (!empty($list)) {
				$arr = array();
				foreach ($list as $v) {
					$arr[] = $v['member_id'];
				}
				$condition['wl_member_id'] = array('in', $arr);
			} else {
				$condition['wl_member_id'] = null;
			}
		}

		if ($param['pay_state'] != '') {
			$condition['wl_state'] = $param['pay_state'];
		}
		if ($param['query_start_date'] && $param['query_end_date'] && strtotime($param['query_start_date']) <= strtotime($param['query_end_date'])) {
			$condition['wl_addtime'] = array('between', array(strtotime($param['query_start_date']), strtotime("+1 day", strtotime($param['query_end_date'])) - 1));
		} elseif ($param['query_start_date']) {
			$condition['wl_addtime'] = array('egt', strtotime($param['query_start_date']));
		} elseif ($param['query_end_date']) {
			$condition['wl_addtime'] = array('elt', strtotime("+1 day", strtotime($param['query_end_date'])) - 1);
		}
	}
}