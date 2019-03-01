<?php
/**
 * 余额明细
 *
 */
defined('In33hao') or exit('Access Invild!');
class bonusControl extends SystemControl {
	public function __construct() {
		parent::__construct();
	}

	public function indexOp() {
		$this->bonus_detailsOp();
	}

	/**
	 * bonus_detailsOp 余额明细
	 * @return [type] [description]
	 */
	public function bonus_detailsOp() {
		Tpl::setDirquna('member_center');
		Tpl::showpage('bonus.bonus_details');
	}

	/**
	 * get_xmlOp 获取余额明细
	 * @return [type] [description]
	 */
	public function get_xmlOp() {
//	    var_dump($_POST);die();
		$model_bean = Model('bean');
		$condition = array();
        $this->_get_condition($condition);
        $order = 'add_time desc';
		$page = $_POST['rp'];

        $balance_list = $model_bean->getMemberBalanceRecord($condition,'*', $page, $order);
        $mobile_arr = Model('member_extend')->getMemberMobileArr();
		$data = array();
		$data['now_page'] = $model_bean->shownowpage();
		$data['total_num'] = $model_bean->gettotalnum();

		foreach ($balance_list as $v) {
			$param = array();
			$param['operation'] = "----";
			$param['member_mobile'] = $mobile_arr[$v['member_id']];
			$param['bdl_member_name'] = $v['member_name'];
			$param['type'] = str_replace(array('bonus','consume','withdraw','admin','recharge','equity','inventory'),array("分红","消费","提现","管理员","充值","股权兑换","库存解冻"),$v['type']);
			$param['operate_amount'] = $v['operate_amount'];
			$param['surplus_amount'] = $v['surplus_amount'];
            $param['add_time'] = $v['add_time'] ? date('Y-m-d H:i:s', $v['add_time']) : '';
            $param['content'] = $v['content'];
            $data['list'][$v['id']] = $param;
		}
		echo Tpl::flexigridXML($data);
		exit();
	}

	/**
	 * export_xlsOp 导出execl文件
	 * @return [type] [description]
	 */
	public function export_xlsOp() {
		$model_member_extend = Model('member_extend');
		$model_balance = Model('balance');
		$condition = array();
		$id = $_GET['id'];
		if ($id) {
			$condition['id'] = array('in', $id);
		} else {
			$this->_get_condition($condition);
		}
		$data = $model_balance->getBalanceRecord($condition, '*', null, 'add_time desc', false);
		$excel_obj = new Excel();
		$excel_data = array();
		// 设置样式
		$excel_obj->setStyle(array('id' => 's_title', 'Font' => array('FontName' => '宋体', 'Size' => '12', 'Bold' => '1')));
		// header
		$excel_data[0][] = array('styleid' => 's_title', 'data' => '会员手机');
		$excel_data[0][] = array('styleid' => 's_title', 'data' => '会员名字');
		$excel_data[0][] = array('styleid' => 's_title', 'data' => '类型');
		$excel_data[0][] = array('styleid' => 's_title', 'data' => '调整金额');
		$excel_data[0][] = array('styleid' => 's_title', 'data' => '调整后余额');
		$excel_data[0][] = array('styleid' => 's_title', 'data' => '时间');
		$excel_data[0][] = array('styleid' => 's_title', 'data' => '备注');

		foreach ((array) $data as $k => $v) {
			$tmp = array();
			$tmp[] = array('data' => $v['member_mobile']);
			$tmp[] = array('data' => $v['member_name']);
			$tmp[] = array('data' => str_replace(array('bonus','consume','withdraw','admin','recharge','equity','inventory'),array("分红","消费","提现","管理员","充值","股权","库存解冻"),$v['type']));
			$tmp[] = array('data' => floatval($v['operate_amount']));
			$tmp[] = array('data' => floatval($v['surplus_amount']));
			$tmp[] = array('data' => date('Y-m-d H:i:s', $v['add_time'] ? $v['add_time'] : ''));
			$tmp[] = array('data' => $v['content'] );
			$excel_data[] = $tmp;
		}
		$excel_data = $excel_obj->charset($excel_data, CHARSET);
		$excel_obj->addArray($excel_data);
		$excel_obj->addWorksheet($excel_obj->charset('会员奖金', CHARSET));
		$excel_obj->generateXML($excel_obj->charset('会员奖金', CHARSET) . $_GET['curpage'] . '-' . date('Y-m-d-H', time()));
	}

	/**
	 * _get_condition 搜索条件整理
	 * @param  [type] &$condition [description]
	 * @return [type]             [description]
	 */
	private function _get_condition(&$condition) {
        header("Content-type: text/html; charset=utf-8");
		$param = $_REQUEST;
		if ($param['query'] != '') {
			if ($param['qtype'] == 'member_mobile') {
				$list = Model('member')->getMemberList(array('member_mobile' => array('like', '%' . $param['query'] . '%')));
				if (!empty($list)) {
					$arr = array();
					foreach ($list as $v) {
						$arr[] = $v['member_id'];
					}
					$condition['member_id'] = array('in', $arr);
				} else {
					$condition['member_id'] = null;
				}
			} else {
				$condition['member_name'] = array('like', '%' . $param['query'] . '%');
			}
		}
		if ($param['keyword_type'] && $param['keyword']) {
			$list = array();
			if ($param['keyword_type'] == 'member_mobile') {
				$list = Model('member')->getMemberList(array('member_mobile' => array('like', '%' . $param['keyword'] . '%')));
			} elseif ($param['keyword_type'] == 'member_name') {
				$list = Model('member')->getMemberList(array('member_name' => array('like', '%' . $param['keyword'] . '%')));
			}
			if (!empty($list)) {
				$arr = array();
				foreach ($list as $v) {
					$arr[] = $v['member_id'];
				}
				$condition['member_id'] = array('in', $arr);
			} else {
				$condition['member_id'] = null;
			}
		}

		if ($param['query_start_date'] && $param['query_end_date'] && strtotime($param['query_start_date']) <= strtotime($param['query_end_date'])) {
			$condition['add_time'] = array('between', array(strtotime($param['query_start_date']), strtotime("+1 day", strtotime($param['query_end_date'])) - 1));
		} elseif ($param['query_start_date']) {
			$condition['add_time'] = array('egt', strtotime($param['query_start_date']));
		} elseif ($param['query_end_date']) {
			$condition['add_time'] = array('elt', strtotime("+1 day", strtotime($param['query_end_date'])) - 1);
		}
	}
}