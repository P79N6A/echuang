<?php
/**
 * 积分明细
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
	 * bonus_detailsOp 积分明细
	 * @return [type] [description]
	 */
	public function bonus_detailsOp() {
		Tpl::setDirquna('member_center');
		Tpl::showpage('bonus.bonus_details');
	}

	/**
	 * get_xmlOp 获取积分明细
	 * @return [type] [description]
	 */
	public function get_xmlOp() {
		$model_member_integral_log = Model('member_integral_log');
		$condition = array();
        $this->_get_condition($condition);
        $order = 'add_time desc';
		$page = $_POST['rp'];

        $balance_list = $model_member_integral_log->getMemberIntegralLogList($condition,'*', $page, $order);
        $mobile_arr = Model('member_extend')->getMemberMobileArr();
		$data = array();
		$data['now_page'] = $model_member_integral_log->shownowpage();
		$data['total_num'] = $model_member_integral_log->gettotalnum();
        $type = $model_member_integral_log->state;
		foreach ($balance_list as $v) {
			$param = array();
			$param['operation'] = "----";
			$param['member_mobile'] = $mobile_arr[$v['member_id']];
			$param['bdl_member_name'] = Model('member')->getMemberInfo(array('member_id'=>$v['member_id']),'member_name')['member_name'];
			$param['type'] = $type[$v['type']];
			$param['variable_integral'] = "<span ".(($v['variable_integral']<0)?"class='red'":"").">".$v['variable_integral']."</span>";
			$param['stable_integral'] = $v['stable_integral'];
			$param['variable_estimate_integral'] = "<span ".(($v['variable_estimate_integral']<0)?"class='red'":"").">".$v['variable_estimate_integral']."</span>";
			$param['stable_estimate_integral'] = $v['stable_estimate_integral'];
            $param['add_time'] = $v['add_time'] ? date('Y-m-d H:i:s', $v['add_time']) : '';
            $param['content'] = $v['remarks'];
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
        $model_member_integral_log = Model('member_integral_log');
        $type = $model_member_integral_log->state;
        $mobile_arr = Model('member_extend')->getMemberMobileArr();
		$model_balance = Model('balance');
		$condition = array();
		$id = $_GET['id'];
		if ($id) {
			$condition['id'] = array('in', $id);
		} else {
			$this->_get_condition($condition);
		}
		$data = $model_member_integral_log->getMemberIntegralLogList($condition,'*', null, 'add_time desc',false);;
		$excel_obj = new Excel();
		$excel_data = array();
		// 设置样式
		$excel_obj->setStyle(array('id' => 's_title', 'Font' => array('FontName' => '宋体', 'Size' => '12', 'Bold' => '1')));
		// header
		$excel_data[0][] = array('styleid' => 's_title', 'data' => '会员手机');
		$excel_data[0][] = array('styleid' => 's_title', 'data' => '会员名字');
		$excel_data[0][] = array('styleid' => 's_title', 'data' => '类型');
		$excel_data[0][] = array('styleid' => 's_title', 'data' => '调整积分');
		$excel_data[0][] = array('styleid' => 's_title', 'data' => '调整后积分');
		$excel_data[0][] = array('styleid' => 's_title', 'data' => '调整预期积分');
		$excel_data[0][] = array('styleid' => 's_title', 'data' => '调整后预期积分');
		$excel_data[0][] = array('styleid' => 's_title', 'data' => '时间');
		$excel_data[0][] = array('styleid' => 's_title', 'data' => '备注');

		foreach ((array) $data as $k => $v) {
			$tmp = array();
			$tmp[] = array('data' => $mobile_arr[$v['member_id']]);
			$tmp[] = array('data' => Model('member')->getMemberInfo(array('member_id'=>$v['member_id']),'member_name')['member_name']);
			$tmp[] = array('data' => $type[$v['type']]);
			$tmp[] = array('data' => $v['variable_integral']);
			$tmp[] = array('data' => $v['stable_integral']);
			$tmp[] = array('data' => $v['variable_estimate_integral']);
			$tmp[] = array('data' => $v['stable_estimate_integral']);
			$tmp[] = array('data' => date('Y-m-d H:i:s', $v['add_time'] ? $v['add_time'] : ''));
			$tmp[] = array('data' => $v['remarks'] );
			$excel_data[] = $tmp;
		}
		$excel_data = $excel_obj->charset($excel_data, CHARSET);
		$excel_obj->addArray($excel_data);
		$excel_obj->addWorksheet($excel_obj->charset('会员积分', CHARSET));
		$excel_obj->generateXML($excel_obj->charset('会员积分', CHARSET) . $_GET['curpage'] . '-' . date('Y-m-d-H', time()));
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
                $list = Model('member')->getMemberList(array('member_name' => array('like', '%' . $param['query'] . '%')));
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