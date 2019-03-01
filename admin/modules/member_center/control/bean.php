<?php
/**
 * 金豆管理
 *
 */
defined('In33hao') or exit('Access Invild!');
class beanControl extends SystemControl {
	public function __construct() {
		parent::__construct();
	}

	public function indexOp() {
		$this->bean_manageOp();
	}

	/**
	 * bean_manageOp 金豆管理
	 * @return [type] [description]
	 */
	public function bean_manageOp() {
		Tpl::setDirquna('member_center');
		Tpl::showpage('bean.bean_manage');
	}

	/**
	 * get_xmlOp 获取模板数据
	 * @return [type] [description]
	 */
	public function get_xmlOp() {
		$model_bean = Model('bean');
		$condition = array();

		$this->_get_condition($condition);
		$order = 'lg_addtime desc';
		$order_member_extend = '';

		$param_member = array('member_id', 'member_name', 'member_mobile', 'member_time', 'member_login_time');
		$param_member_extend = array('member_golden_bean', 'member_golden_bean_payed', 'member_equity', 'total_performance', 'new_performance', 'surplus_performance', 'day_total_consume', 'total_consume');
		if (in_array($_POST['sortname'], $param_member) && in_array($_POST['sortorder'], array('asc', 'desc'))) {
			$order = 'member.' . $_POST['sortname'] . ' ' . $_POST['sortorder'];
		} elseif (in_array($_POST['sortname'], $param_member_extend) && in_array($_POST['sortorder'], array('asc', 'desc'))) {
			$order = 'member_extend.' . $_POST['sortname'] . ' ' . $_POST['sortorder'];
		}

		$page = $_POST['rp'];
		$fields = 'member_id,member_mobile,member_name,member_golden_bean,member_equity,total_consume';

		$bean_list = $model_bean->getBeanLogList($condition, '*', $page, $order);
		$bean_list = $model_bean->joinBeanListArr($bean_list);
		$mobile_arr = Model('member_extend')->getMemberMobileArr();
		$data = array();
		$data['now_page'] = $model_bean->shownowpage();
		$data['total_num'] = $model_bean->gettotalnum();

		foreach ($bean_list as $v) {
			$param = array();
			$param['operation'] = "";
			$param['member_mobile'] = $mobile_arr[$v['lg_member_id']];
			$param['lg_member_name'] = $v['lg_member_name'];
			$param['lg_sn'] = $v['lg_sn'];
			$param['lg_type'] = replaceBeanType($v['lg_type']);
			$param['lg_amount'] = $v['lg_amount'];
			$param['lg_bean_amount'] = $v['lg_bean_amount'];
			$param['lg_addtime'] = $v['lg_addtime'] ? date('Y-m-d H:i:s', $v['lg_addtime']) : '';
			$param['lg_desc'] = $v['lg_desc'];
			$data['list'][$v['lg_id']] = $param;
		}
		echo Tpl::flexigridXML($data);
		exit();
	}

	/**
	 * export_xlsOp 导出execl文件
	 * @return [type] [description]
	 */
	public function export_xlsOp() {
		$condition = array();
		$id = $_GET['id'];
		if ($id) {
			$condition['lg_id'] = array('in', $id);
		} else {
			$this->_get_condition($condition);
		}
		$data = Model('bean')->getBeanLogList($condition, '*', null, 'lg_addtime desc', false);
		$excel_obj = new Excel();
		$excel_data = array();
		// 设置样式
		$excel_obj->setStyle(array('id' => 's_title', 'Font' => array('FontName' => '宋体', 'Size' => '12', 'Bold' => '1')));
		// header
		$excel_data[0][] = array('styleid' => 's_title', 'data' => '会员手机');
		$excel_data[0][] = array('styleid' => 's_title', 'data' => '会员姓名');
		$excel_data[0][] = array('styleid' => 's_title', 'data' => '单号');
		$excel_data[0][] = array('styleid' => 's_title', 'data' => '类型');
		$excel_data[0][] = array('styleid' => 's_title', 'data' => '金额');
		$excel_data[0][] = array('styleid' => 's_title', 'data' => '调整后金额');
		$excel_data[0][] = array('styleid' => 's_title', 'data' => '时间');
		$excel_data[0][] = array('styleid' => 's_title', 'data' => '奖金备注');
		foreach ((array) $data as $k => $v) {
			$tmp = array();
			$tmp[] = array('data' => Model('member')->getMemberMobileById($v['lg_member_id']));
			$tmp[] = array('data' => $v['lg_member_name']);
			$tmp[] = array('data' => $v['lg_sn']);
			$tmp[] = array('data' => str_replace(array('rebate', 'consume', 'withdraw', 'admin', 'recharge', 'donate', 'order_pay', 'present'), array('分享奖励', '消费', '提现', 'redbag', '充值', '转赠', '订单支付', '赠送'), $v['lg_type']));
			$tmp[] = array('data' => floatval($v['lg_amount']));
			$tmp[] = array('data' => floatval($v['lg_bean_amount']));
			$tmp[] = array('data' => $v['lg_addtime'] ? date('Y-m-d H:i:s', $v['lg_addtime']) : '');
			$tmp[] = array('data' => $v['lg_desc']);
			$excel_data[] = $tmp;
		}
		$excel_data = $excel_obj->charset($excel_data, CHARSET);
		$excel_obj->addArray($excel_data);
		$excel_obj->addWorksheet($excel_obj->charset('金豆管理', CHARSET));
		$excel_obj->generateXML($excel_obj->charset('金豆管理', CHARSET) . $_GET['curpage'] . '-' . date('Y-m-d-H', time()));
	}

	/**
	 * _get_condition 搜索条件整理
	 * @param  [type] &$condition [description]
	 * @return [type]             [description]
	 */
	private function _get_condition(&$condition) {
		$param = $_REQUEST;
		if ($param['query'] != '') {
			if ($param['qtype'] == 'member_mobile') {
				$list = Model('member')->getMemberList(array('member_mobile' => array('like', '%' . $param['query'] . '%')));
				if (!empty($list)) {
					$arr = array();
					foreach ($list as $v) {
						$arr[] = $v['member_id'];
					}
					$condition['lg_member_id'] = array('in', $arr);
				} else {
					$condition['lg_member_id'] = null;
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
				$condition['lg_member_id'] = array('in', $arr);
			} else {
				$condition['lg_member_id'] = null;
			}
		}

		if ($param['bean_type'] != '') {
			$condition['lg_type'] = $param['bean_type'];
		}
		if ($param['query_start_date'] && $param['query_end_date'] && strtotime($param['query_start_date']) <= strtotime($param['query_end_date'])) {
			$condition['lg_addtime'] = array('between', array(strtotime($param['query_start_date']), strtotime("+1 day", strtotime($param['query_end_date'])) - 1));
		} elseif ($param['query_start_date']) {
			$condition['lg_addtime'] = array('egt', strtotime($param['query_start_date']));
		} elseif ($param['query_end_date']) {
			$condition['lg_addtime'] = array('elt', strtotime("+1 day", strtotime($param['query_end_date'])) - 1);
		}
	}
}