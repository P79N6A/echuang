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
	 * get_xmlOp 获取积分回购数据
	 * @return [type] [description]
	 */
	public function get_xmlOp() {
        $model_integral_selling = Model('integral_selling');
		$condition = array('state'=>2,'sell_member_id'=>array('neq',0));
		$this->_get_condition($condition);
		$order = 'buy_time desc';
		$page = $_POST['rp'];
		$list = $model_integral_selling->getIntegralSellingList($condition,'*',$page,$order);
		$mobile_arr = Model('member_extend')->getMemberMobileArr();
		$name_arr = Model('member_extend')->getMemberNameArr();
		$data = array();
		$data['now_page'] = $model_integral_selling->shownowpage();
		$data['total_num'] = $model_integral_selling->gettotalnum();
		foreach ($list as $v) {
			$param = array();
            $param['operation'] = '';
            $param['sell_sn'] = $v['sell_sn'];
			$param['sell_member_mobile'] = $mobile_arr[$v['sell_member_id']];
			$param['sell_member_name'] = $name_arr[$v['sell_member_id']];
			$param['member_mobile'] = $mobile_arr[$v['member_id']];
			$param['member_name'] = $name_arr[$v['member_id']];
			$param['actual_integral'] = $v['actual_integral'];
			$param['buy_time'] = $v['buy_time'] ? date('Y-m-d H:i:s', $v['buy_time']) : '';
			$data['list'][$v['id']] = $param;
		}
		echo Tpl::flexigridXML($data);
		exit();
	}

	/**
	 * export_xlsOp 导出数据
	 * @return [type] [description]
	 */
	public function export_xlsOp() {
        $condition = array('state'=>2,'sell_member_id'=>array('neq',0));
		if ($_GET['id']) {
			$condition['id'] = array('in', $_GET['id']);
		} else {
			$this->_get_condition($condition);
		}

        $model_integral_selling = Model('integral_selling');
        $data = $model_integral_selling->getIntegralSellingList($condition);
        $mobile_arr = Model('member_extend')->getMemberMobileArr();
        $name_arr = Model('member_extend')->getMemberNameArr();
		$excel_obj = new Excel();
		$excel_data = array();
		// 设置样式
		$excel_obj->setStyle(array('id' => 's_title', 'Font' => array('FontName' => '宋体', 'Size' => '12', 'Bold' => '1')));
		// header
		$excel_data[0][] = array('styleid' => 's_title', 'data' => '回购单号');
		$excel_data[0][] = array('styleid' => 's_title', 'data' => '会员手机');
		$excel_data[0][] = array('styleid' => 's_title', 'data' => '真实姓名');
		$excel_data[0][] = array('styleid' => 's_title', 'data' => '被回购手机号');
		$excel_data[0][] = array('styleid' => 's_title', 'data' => '被回购姓名');
		$excel_data[0][] = array('styleid' => 's_title', 'data' => '回购积分');
		$excel_data[0][] = array('styleid' => 's_title', 'data' => '时间');
		foreach ((array) $data as $k => $v) {
			$tmp = array();
			$tmp[] = array('data' => $v['sell_sn']);
			$tmp[] = array('data' => $mobile_arr[$v['sell_member_id']]);
			$tmp[] = array('data' => $name_arr[$v['sell_member_id']]);
			$tmp[] = array('data' => $mobile_arr[$v['member_id']]);
			$tmp[] = array('data' => $name_arr[$v['member_id']]);
			$tmp[] = array('data' => $v['actual_integral']);
			$tmp[] = array('data' => $v['buy_time'] ? date('Y-m-d H:i:s', $v['buy_time']) : '');
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
					$condition['sell_member_id'] = array('in', $arr);
				} else {
					$condition['sell_member_id'] = null;
				}
			} else {
				$condition[$param['qtype']] = array('like', '%' . $param['query'] . '%');
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