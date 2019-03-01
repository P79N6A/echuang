<?php

/**
 * 银豆管理
 */

defined('In33hao') or exit('Access Invild!');
class silver_beanControl extends SystemControl {
	public function __construct() {
		parent::__construct();
	}

	/**
	 * indexOp 入口
	 * @return [type] [description]
	 */
	public function indexOp() {
		$this->bean_manageOp();
	}

	/**
	 * bean_manageOp 银豆管理
	 * @return [type] [description]
	 */
	public function bean_manageOp() {
		Tpl::setDirquna('member_center');
		Tpl::showpage('silver_bean.bean_manage');
	}

	/**
	 * get_xmlOp 获取银豆xml数据
	 * @return [type] [description]
	 */
	public function get_xmlOp() {
		$model_bean = Model('bean');
		$condition = array();

		$this->_get_condition($condition);
		$order = "slg_addtime desc";
		$page = $_POST['rp'];
		$bean_list = $model_bean->getSilverBeanLogList($condition, '*', $page, $order);

		$data['now_page'] = $model_bean->shownowpage();
		$data['total_num'] = $model_bean->gettotalnum();
		foreach ($bean_list as $v) {
			$param['member_mobile'] = Model('member')->getMemberMobileById($v['slg_member_id']);
			$param['slg_member_name'] = $v['slg_member_name'];
			$param['slg_sn'] = $v['slg_sn'];
			$param['slg_type'] = str_replace(array('admin', 'donate', 'order_pay'), array('redbag', '转赠', '订单支付'), $v['slg_type']);
			$param['slg_amount'] = $v['slg_amount'];
			$param['slg_bean_amount'] = $v['slg_bean_amount'];
			$param['slg_addtime'] = date('Y-m-d H:i:s', $v['slg_addtime'] ? $v['slg_addtime'] : '');
			$param['slg_desc'] = $v['slg_desc'];
			$data['list'][$v['slg_id']] = $param;
		}
		Tpl::flexigridXML($data);
	}

	/**
	 * export_xlsOp 导出execl文件
	 *
	 * @return [type] [description]
	 */
	public function export_xlsOp() {
		$condition = array();
		$id = $_GET['id'];
		if ($id) {
			$condition['slg_id'] = array('in', $id);
		} else {
			$this->_get_condition($condition);
		}
		$data = Model('bean')->getSilverBeanLogList($condition, '*', null, 'slg_addtime desc', false);
		$excel_obj = new Excel();
		$excel_data = array();
		// 设置样式
		$excel_obj->setStyle(array('id' => 's_title', 'Font' => array('FontName' => '宋体', 'Size' => '12', 'Bold' => '1')));
		// header
		$excel_data[0] = array(
			array('styleid' => 's_title', 'data' => '会员手机'),
			array('styleid' => 's_title', 'data' => '会员姓名'),
			array('styleid' => 's_title', 'data' => '单号'),
			array('styleid' => 's_title', 'data' => '银豆类型'),
			array('styleid' => 's_title', 'data' => '金额'),
			array('styleid' => 's_title', 'data' => '调整后银豆金额'),
			array('styleid' => 's_title', 'data' => '时间'),
			array('styleid' => 's_title', 'data' => '备注'),
		);

		foreach ((array) $data as $k => $v) {
			$tmp = array();
			$tmp[] = array('data' => Model('member')->getMemberMobileById($v['slg_member_id']));
			$tmp[] = array('data' => $v['slg_member_name']);
			$tmp[] = array('data' => $v['slg_sn']);
			$tmp[] = array('data' => str_replace(array('admin', 'donate', 'order_pay'), array('redbag', '转赠', '订单支付'), $v['slg_type']));
			$tmp[] = array('data' => $v['slg_amount']);
			$tmp[] = array('data' => $v['slg_bean_amount']);
			$tmp[] = array('data' => date('Y-m-d H:i:s', $v['slg_addtime'] ? $v['slg_addtime'] : ''));
			$tmp[] = array('data' => $v['slg_desc']);
			$excel_data[] = $tmp;
		}
		$excel_data = $excel_obj->charset($excel_data, CHARSET);
		$excel_obj->addArray($excel_data);
		$excel_obj->addWorksheet($excel_obj->charset('银豆列表', CHARSET));
		$excel_obj->generateXML($excel_obj->charset('银豆列表', CHARSET) . $_GET['curpage'] . '-' . date('Y-m-d-H', time()));
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
					$condition['slg_member_id'] = array('in', $arr);
				} else {
					$condition['slg_member_id'] = null;
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
				$condition['slg_member_id'] = array('in', $arr);
			} else {
				$condition['slg_member_id'] = null;
			}
		}

		if ($param['silver_type'] != '') {
			$condition['slg_type'] = $param['silver_type'];
		}
		if ($param['query_start_date'] && $param['query_end_date'] && strtotime($param['query_start_date']) <= strtotime($param['query_end_date'])) {
			$condition['slg_addtime'] = array('between', array(strtotime($param['query_start_date']), strtotime("+1 day", strtotime($param['query_end_date'])) - 1));
		} elseif ($param['query_start_date']) {
			$condition['slg_addtime'] = array('egt', strtotime($param['query_start_date']));
		} elseif ($param['query_end_date']) {
			$condition['slg_addtime'] = array('elt', strtotime("+1 day", strtotime($param['query_end_date'])) - 1);
		}
	}
}
