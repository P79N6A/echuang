<?php
/**
 * 银行卡管理
 *
 */
class bankControl extends SystemControl {
	public function __construct() {
		parent::__construct();
	}

	public function indexOp() {
		$model_bank = Model('bank');
		Tpl::setDirquna('member_center');
		Tpl::showpage('bank.bank_index');
	}

	/**
	 * get_xmlOp 获取会员银行卡数据
	 * @return [type] [description]
	 */
	public function get_xmlOp() {
		$model_bank = Model('bank');
		$condition = array();
		$this->_get_condition($condition);
		$page = $_POST['rp'];
		$order = $_POST['sortname'] . ' ' . $_POST['sortorder'];
		$member_bank_list = $model_bank->getMemberBankList($condition, '*', $page, $order);
		$data = array();
		$data['now_page'] = $model_bank->shownowpage();
		$data['total_num'] = $model_bank->gettotalnum();
		foreach ($member_bank_list as $v) {
			$info = Model('member_extend')->getMemberInfo(array('member_id' => $v['mb_member_id']));
			$param = array();
            $param['operation'] = "----";
			$param['member_mobile'] = $info['member_mobile'];
			$param['member_name'] = $info['member_name'];
			$param['mb_account_bank_name'] = $v['mb_account_bank_name'];
//			$param['mb_account_branch'] = $v['mb_account_branch'];
			$param['mb_account_name'] = $v['mb_account_name'];
			$param['mb_account_num'] = $v['mb_account_num'];
			$param['mb_addtime'] = $v['mb_addtime'] ? date('Y-m-d H:i:s', $v['mb_addtime']) : '';
			$data['list'][$v['mb_id']] = $param;
		}
		Tpl::flexigridXML($data);
		exit();
	}

    /**
     * export_xlsOp 导出数据
     * @return [type] [description]
     */
    public function export_xlsOp() {
        $condition = array();
        if ($_GET['id']) {
            $condition['id'] = array('in', $_GET['id']);
        } else {
            $this->_get_condition($condition);
        }

        $data = Model('bank')->getMemberBankList($condition);
        $excel_obj = new Excel();
        $excel_data = array();
        // 设置样式
        $excel_obj->setStyle(array('id' => 's_title', 'Font' => array('FontName' => '宋体', 'Size' => '12', 'Bold' => '1')));
        // header
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '会员手机');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '会员姓名');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '开户行');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '开户姓名');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '银行账号');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '添加时间');

        foreach ((array) $data as $k => $v) {
            $tmp = array();
            $tmp[] = array('data' => Model('member')->getMemberMobileById($v['mb_member_id']));
            $tmp[] = array('data' => Model('member')->getMemberNameById($v['mb_member_id']));
            $tmp[] = array('data' => $v['mb_account_bank_name']);
            $tmp[] = array('data' => $v['mb_account_name']);
            $tmp[] = array('data' => $v['mb_account_num']);
            $tmp[] = array('data' => date("Y-m-d H:i:s",$v['mb_addtime']));
            $excel_data[] = $tmp;
        }
        $excel_data = $excel_obj->charset($excel_data, CHARSET);
        $excel_obj->addArray($excel_data);
        $excel_obj->addWorksheet($excel_obj->charset('分润管理', CHARSET));
        $excel_obj->generateXML($excel_obj->charset('分润管理', CHARSET) . $_GET['curpage'] . '-' . date('Y-m-d-H', time()));
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
					$condition['mb_member_id'] = array('in', $arr);
				} else {
					$condition['mb_member_id'] = null;
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
				$condition['mb_member_id'] = array('in', $arr);
			} else {
				$condition['mb_member_id'] = null;
			}
		}

		if ($param['query_start_date'] && $param['query_end_date'] && strtotime($param['query_start_date']) <= strtotime($param['query_end_date'])) {
			$condition['mb_addtime'] = array('between', array(strtotime($param['query_start_date']), strtotime("+1 day", strtotime($param['query_end_date'])) - 1));
		} elseif ($param['query_start_date']) {
			$condition['mb_addtime'] = array('egt', strtotime($param['query_start_date']));
		} elseif ($param['query_end_date']) {
			$condition['mb_addtime'] = array('elt', strtotime("+1 day", strtotime($param['query_end_date'])) - 1);
		}
	}
}
