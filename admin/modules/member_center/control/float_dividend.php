<?php
/**
 * 浮动分红管理
 *
 */
defined('In33hao') or exit('Access Invild!');
class float_dividendControl extends SystemControl {
	private $links = array(
		array('url' => 'act=float_dividend&op=dividend_manage', 'lang' => 'float_dividend_manage'),
//		array('url' => 'act=float_dividend&op=dividend_distribute', 'lang' => 'float_dividend_distribute'),
	);

	public function __construct() {
		parent::__construct();
		Language::read('member');
	}

	public function indexOp() {
		$this->dividend_manageOp();
	}

	/**
	 * dividend_manageOp 分红管理
	 * @return [type] [description]
	 */
	public function dividend_manageOp() {
		Tpl::output('top_link', $this->sublink($this->links, 'dividend_manage'));
		Tpl::setDirquna('member_center');
		Tpl::showpage('dividend.dividend_manage');
	}

	/**
	 * dividend_distributeOp 分红分发
	 * @return [type] [description]
	 */
	public function dividend_distributeOp() {
		$model_float_dividend = Model('float_dividend');
		if (chksubmit()) {
			$float_dividend_amount = floatval(trim($_POST['float_dividend_amount']));
			if (is_numeric($float_dividend_amount) && $float_dividend_amount > 0) {
				$fields = 'member_id,member_name,member_equity';
				$condition['member_equity'] = array('egt', C('equity_dividend_min_limit'));
				$condition['member_state'] = 1;
				$member_list = Model('member_extend')->getMemberExtendList($condition, $fields, 'union');
				$num = 0;
				$dayStartTime = strtotime(date('Y-m-d'));
				try {

					foreach ($member_list as $v) {

						// 限制每天只能一次分红
						// $condition = array();
						// $condition['flg_member_id'] = $v['member_id'];
						// $condition['flg_addtime'] = array('egt', $dayStartTime);
						// $d_info = $model_float_dividend->getFloatDividendLogInfo($condition);
						// if (empty($d_info)) {

						$dividend_log_data = array();
						$dividend_log_data['flg_member_id'] = $v['member_id'];
						$dividend_log_data['flg_member_name'] = $v['member_name'];
						$dividend_log_data['flg_addtime'] = TIMESTAMP;
						$dividend_log_data['flg_sn'] = makeSN('dividend', $v['member_id']);
						$dividend_log_data['flg_dividend_rate'] = $float_dividend_amount;
						if ($v['member_equity'] >= C('equity_dividend_max_limit')) {
							$dividend_log_data['flg_amount'] = floatFormat(10 * $float_dividend_amount);
							$dividend_log_data['flg_equity_num'] = 10;
						} else {
							$dividend_log_data['flg_amount'] = floatFormat(1 * $float_dividend_amount);
							$dividend_log_data['flg_equity_num'] = 1;
						}

						$model_float_dividend->addFloatDividendLog($dividend_log_data);
						$num++;

						// 增加封顶复投操作
						$result = Logic('dividend')->capping($v['member_id']);
						if (isset($result['error'])) {
							throw new Exception($result['error']);
						}
					}
					// }
				} catch (Exception $exception) {
					showMessage($exception->getMessage(), '', '', 'error');
				}
				showMessage($num . '个会员分红成功', 'index.php?act=float_dividend&op=dividend_manage', '', 'succ');
			} else {
				showMessage('浮动分红数输入错误', '', '', 'error');
			}
		} else {
			Tpl::output('top_link', $this->sublink($this->links, 'dividend_distribute'));
			Tpl::setDirquna('member_center');
			Tpl::showpage('dividend.dividend_distribute');
		}

	}
	/**
	 * dividend_payOp 更改分红支付状态
	 * @return [type] [description]
	 */
	public function dividend_payOp() {
		//$flg_sn = $_GET['sn'];
		$ids = explode(',', $_GET['id']);
		if (count($ids) == 0) {
			//showMessage(L('wrong_argument'), '', '', 'error');
			exit(json_encode(array('state' => false, 'msg' => L('wrong_argument'))));
		}
		if ($ids) {
			$model_float_dividend = Model('float_dividend');
			$f_info = $model_float_dividend->getFloatDividendLogInfo(array('flg_id' => $_GET['id']));
			if (empty($f_info) || $f_info['slg_state']) {
				exit(json_encode(array('state' => false, 'msg' => '分红信息错误')));
				//showMessage('分红信息错误', '', '', 'error');
			}
			$f_param['flg_state'] = 1;
			$f_param['flg_paytime'] = TIMESTAMP;
			try {
				$edit_result = $model_float_dividend->editFloatDividendLog(array('flg_id' => $ids), $f_param);
				if ($edit_result) {
					exit(json_encode(array('state' => true, 'msg' => '更改支付状态成功')));
				} else {
					exit(json_encode(array('state' => false, 'msg' => '更改支付状态失败')));
				}

				//showMessage('更改支付状态成功', '', '', 'succ');
			} catch (Exception $exception) {
				exit(json_encode(array('state' => false, 'msg' => $exception->getMessage())));
				//showMessage('更改支付状态失败', '', '', 'error');
			}
		} else {
			exit(json_encode(array('state' => false, 'msg' => '参数错误')));
			//showMessage('参数错误', '', '', 'error');
		}
	}

	/**
	 * get_xmlOp 获取分红数据
	 * @return [type] [description]
	 */
	public function get_xmlOp() {
		$model_float_dividend = Model('float_dividend');
		$condition = array();
		$this->_get_condition($condition);

		// 排序
		$order = 'add_time desc';
		$page = $_POST['rp'];
		$dividend_list = $model_float_dividend->getMemberProfitRecordList($condition, '*', $page, $order);
//		var_dump($dividend_list);
		$data = array();
		$data['now_page'] = $model_float_dividend->shownowpage();
		$data['total_num'] = $model_float_dividend->gettotalnum();
		$mobile_arr = Model('member_extend')->getMemberMobileArr();
//		$model_bank = Model('bank');
//		$bank_arr = array();
//		$bank_list = $model_bank->getMemberBankList('', '*');
//		foreach ($bank_list as $value) {
//			$bank_arr[$value['mb_member_id']] = $value;
//		}
		foreach ($dividend_list as $v) {
			$param = array();
            $param['operation'] = "----";
			$param['member_name'] = $v['member_name'];
			$param['member_mobile'] = $mobile_arr[$v['member_id']];
			$param['dr_no'] = $v['dr_no'];
			$param['content'] = $v['content'];
            $param['prize'] = $v['prize'];
            $param['add_time'] = $v['add_time'] ? date('Y-m-d', $v['add_time']) : '';
//            $param['flg_account_bank_name'] = $bank_arr[$v['flg_member_id']]['mb_account_bank_name'];
//			$param['flg_account_branch'] = $bank_arr[$v['flg_member_id']]['mb_account_branch'];
//			$param['flg_account_num'] = $bank_arr[$v['flg_member_id']]['mb_account_num'];
//			$param['state'] = str_replace(array(0, 1), array('待支付', '已支付'), $v['flg_state']);
//			$param['flg_paytime'] = $v['flg_paytime'] ? date('Y-m-d H:i:s', $v['flg_paytime']) : '';

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
        $condition = array();
        if ($_GET['id']) {
            $condition['id'] = array('in', $_GET['id']);
        } else {
            $this->_get_condition($condition);
        }

        $data = Model('member_profit')->getBonusList($condition);
        $excel_obj = new Excel();
        $excel_data = array();
        // 设置样式
        $excel_obj->setStyle(array('id' => 's_title', 'Font' => array('FontName' => '宋体', 'Size' => '12', 'Bold' => '1')));
        // header
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '真实姓名');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '会员手机');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '单号');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '描述');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '金额');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '时间');

        foreach ((array) $data as $k => $v) {
            $tmp = array();
            $tmp[] = array('data' => $v['member_name']);
            $tmp[] = array('data' => Model('member')->getMemberMobileById($v['member_id']));
            $tmp[] = array('data' => $v['dr_no']);
            $tmp[] = array('data' => $v['content']);
            $tmp[] = array('data' => $v['prize']);
            $tmp[] = array('data' => date("Y-m-d H:i:s",$v['add_time']));
            $excel_data[] = $tmp;
        }
        $excel_data = $excel_obj->charset($excel_data, CHARSET);
        $excel_obj->addArray($excel_data);
        $excel_obj->addWorksheet($excel_obj->charset('分润管理', CHARSET));
        $excel_obj->generateXML($excel_obj->charset('分润管理', CHARSET) . $_GET['curpage'] . '-' . date('Y-m-d-H', time()));
    }

	/**
	 * export_detail_xlsOp 导出分红详情数据
	 * @return [type] [description]
	 */
	public function export_detail_xlsOp() {
		$condition = array();
		$id = $_GET['id'];
		if ($id) {
			$condition['flg_id'] = array('in', $id);
		} else {
			$this->_get_condition($condition);
		}

		if ($_GET['type'] == 'unhandle') {
			$condition['flg_state'] = 0;
		}
		if ($_GET['type'] == 'handle') {
			$condition['flg_state'] = 1;
		}

		$data = Model('float_dividend')->getFloatDividendLogList($condition,'*',100000);

		$excel_obj = new Excel();
		$excel_data = array();
		// 设置样式
		$excel_obj->setStyle(array('id' => 's_title', 'Font' => array('FontName' => '宋体', 'Size' => '12', 'Bold' => '1')));

		// header
		$excel_data[0][] = array('styleid' => 's_title', 'data' => '会员姓名');
		$excel_data[0][] = array('styleid' => 's_title', 'data' => '会员手机');
		$excel_data[0][] = array('styleid' => 's_title', 'data' => '分红金额');
		$excel_data[0][] = array('styleid' => 's_title', 'data' => '分红时间');
		$excel_data[0][] = array('styleid' => 's_title', 'data' => '开户名');
		$excel_data[0][] = array('styleid' => 's_title', 'data' => '开户银行');
		$excel_data[0][] = array('styleid' => 's_title', 'data' => '开户支行');
		$excel_data[0][] = array('styleid' => 's_title', 'data' => '银行账号');
		$excel_data[0][] = array('styleid' => 's_title', 'data' => '支付状态');
		$excel_data[0][] = array('styleid' => 's_title', 'data' => '支付时间');

		foreach ((array) $data as $k => $v) {
			$tmp = array();
			$tmp[] = array('data' => $v['flg_member_name']);
			$tmp[] = array('data' => Model('member')->getMemberMobileById($v['flg_member_id']));
			$tmp[] = array('data' => $v['flg_amount']);
			$tmp[] = array('data' => $v['flg_addtime'] ? date('Y-m-d H:i:s', $v['flg_addtime']) : '');
			$bank_info = Model('bank')->getMemberBankInfo(array('mb_member_id' => $v['flg_member_id']));
			if (!empty($bank_info)) {
				$tmp[] = array('data' => $bank_info['mb_account_name']);
				$tmp[] = array('data' => $bank_info['mb_account_bank_name']);
				$tmp[] = array('data' => $bank_info['mb_account_branch']);
				$tmp[] = array('data' => $bank_info['mb_account_num']);
			} else {
				$tmp[] = array('data' => '');
				$tmp[] = array('data' => '');
				$tmp[] = array('data' => '');
				$tmp[] = array('data' => '');
			}

			$tmp[] = array('data' => str_replace(array(0, 1), array('待支付', '已支付'), $v['flg_state']));
			$tmp[] = array('data' => $v['flg_paytime'] ? date('Y-m-d H:i:s', $v['flg_paytime']) : '');
			$excel_data[] = $tmp;
		}
		$excel_data = $excel_obj->charset($excel_data, CHARSET);
		$excel_obj->addArray($excel_data);
		$excel_obj->addWorksheet($excel_obj->charset('浮动分红详情列表', CHARSET));
		$excel_obj->generateXML($excel_obj->charset('浮动分红详情列表', CHARSET) . $_GET['curpage'] . '-' . date('Y-m-d-H', time()));
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
					$condition['member_id'] = array('in', $arr);
				} else {
					$condition['member_id'] = null;
				}
			} else {
				$condition['member_name'] = array('like', '%' . $param['query'] . '%');
			}
		}

		if ($param['keyword_type'] && $param['keyword']) {
			if ($param['keyword_type'] == 'member_mobile') {
				$list = Model('member')->getMemberList(array('member_mobile' => array('like', '%' . $param['keyword'] . '%')));
				if (!empty($list)) {
					$arr = array();
					foreach ($list as $v) {
						$arr[] = $v['member_id'];
					}
					$condition['member_id'] = array('in', $arr);
				} else {
					$condition['member_id'] = null;
				}
			} elseif ($param['keyword_type'] == 'member_name') {
				$condition['member_name'] = array('like', '%' . $param['keyword'] . '%');
			}
		}

		if ($param['pay_state'] != '') {
			$condition['state'] = $param['pay_state'];
		}
		if ($param['query_start_date'] && $param['query_end_date'] && strtotime($param['query_start_date']) <= strtotime($param['query_end_date'])) {
			$condition['add_time'] = array('between', array(strtotime($param['query_start_date']), strtotime("+1 day", strtotime($param['query_end_date'])) - 1));
		} elseif ($param['query_start_date']) {
			$condition['add_time'] = array('egt', strtotime($param['query_start_date']));
		} elseif ($param['query_end_date']) {
			$condition['add_time'] = array('elt', strtotime("+1 day", strtotime($param['query_end_date'])) - 1);
		}
//		var_dump($condition);die();
	}
}