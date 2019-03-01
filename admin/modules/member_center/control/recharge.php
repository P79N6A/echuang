<?php
/**
 * 充值管理
 *
 */
defined('In33hao') or exit('Access Invild!');
class rechargeControl extends SystemControl
{
    public function __construct()
    {
        parent::__construct();
    }

    public function indexOp()
    {
        $this->recharge_manageOp();
    }

    /**
     * recharge_manageOp 充值管理
     * @return [type] [description]
     */
    public function recharge_manageOp()
    {
        Tpl::setDirquna('member_center');
        Tpl::showpage('recharge.recharge_manage');
    }

	/**
	 * get_xmlOp
	 * @return [type] [description]
	 */
	public function get_xmlOp() {
		$model_bean = Model('bean');
		$condition = array();
		if ($_POST['query'] != '') {
			if ($_POST['qtype'] == 'member_mobile') {
				$info = Model('member')->getMemberInfo(array('member_mobile' => array('like', '%' . $_POST['query'] . '%')));
				if (!empty($info)) {
					$condition['rl_member_id'] = $info['member_id'];
				} else {
					$condition['rl_member_id'] = null;
				}
			} elseif ($_POST['qtype'] == 'rl_member_name') {
				$info = Model('member')->getMemberInfo(array('member_name' => array('like', '%' . $_POST['query'] . '%')));
				if (!empty($info)) {
					$condition['rl_member_id'] = $info['member_id'];
				} else {
					$condition['rl_member_id'] = null;
				}
			} else {
				$condition[$_POST['qtype']] = array('like', '%' . $_POST['query'] . '%');
			}
		}

		$order = 'rl_addtime desc';
		$page = $_POST['rp'];
		$bean_list = $model_bean->getBeanRechargeLogList($condition, '*', $page, $order);
		$mobileArr = Model('member_extend')->getMemberMobileArr();
		$nameArr = Model('member_extend')->getMemberNameArr();
		$data = array();
		$data['now_page'] = $model_bean->shownowpage();
		$data['total_num'] = $model_bean->gettotalnum();
		foreach ($bean_list as $v) {
			$param = array();
			$param['operation'] = '';
			$param['rl_sn'] = $v['rl_sn'];
			$param['member_mobile'] = $mobileArr[$v['rl_member_id']];
			$param['rl_member_name'] = $nameArr[$v['rl_member_id']];
			$param['rl_account_type'] = str_replace(array('bean', 'equity'), array('金豆', '金券'), $v['rl_account_type']);
			$param['rl_amount'] = $v['rl_amount'];
			$param['rl_addtime'] = $v['rl_addtime'] ? date('Y-m-d H:i:s', $v['rl_addtime']) : '';
			$param['rl_state'] = str_replace(array(0, 1), array('待支付', '已支付'), $v['rl_state']);
			$param['rl_recharge_type'] = "支付宝";
			$param['rl_paytime'] = $v['rl_paytime'] ? date('Y-m-d', $v['rl_paytime']) : '';
			$data['list'][$v['rl_id']] = $param;
		}
		echo Tpl::flexigridXML($data);
		exit();
	}

	/**
	 * export_xlsOp 导出execl文件
	 * @return [type] [description]
	 */
	public function export_xlsOp() {
		import('libraries.excel');
		$model_bean = Model('bean');
		$model_member_extend = Model('member_extend');
		$id = $_GET['id'];
		if ($id) {
			$condition['rl_id'] = array('in', $id);
			$data = $model_bean->getBeanRechargeLogList($condition, '*');
		} else {
			$data = $model_bean->getAllBeanRechargeLogList();
		}

		$mobile_arr = $model_member_extend->getMemberMobileArr();
		$name_arr = $model_member_extend->getMemberNameArr();
		foreach ((array)$data as $key => $value) {
			$data[$key]['member_mobile'] = $mobile_arr[$value['rl_member_id']];
			$data[$key]['member_name'] = $name_arr[$value['rl_member_id']];
		}
		$excel_obj = new Excel();
		$excel_data = array();
		// 设置样式
		$excel_obj->setStyle(array('id' => 's_title', 'Font' => array('FontName' => '宋体', 'Size' => '12', 'Bold' => '1')));
		// header
		$excel_data[0][] = array('styleid' => 's_title', 'data' => '充值单号');
		$excel_data[0][] = array('styleid' => 's_title', 'data' => '会员手机');
		$excel_data[0][] = array('styleid' => 's_title', 'data' => '真实姓名');
		$excel_data[0][] = array('styleid' => 's_title', 'data' => '充值账户');
		$excel_data[0][] = array('styleid' => 's_title', 'data' => '充值金额');
		$excel_data[0][] = array('styleid' => 's_title', 'data' => '申请时间');
		$excel_data[0][] = array('styleid' => 's_title', 'data' => '充值状态');
		$excel_data[0][] = array('styleid' => 's_title', 'data' => '支付方式');
		$excel_data[0][] = array('styleid' => 's_title', 'data' => '支付时间');

		foreach ((array) $data as $k => $v) {
			$tmp = array();
			$tmp[] = array('data' => $v['rl_sn']);
			$tmp[] = array('data' => $v['member_mobile']);
			$tmp[] = array('data' => $v['member_name']);
			$tmp[] = array('data' => str_replace(array('bean', 'equity'), array('金豆', '金券'), $v['rl_account_type']));
			$tmp[] = array('data' => floatval($v['rl_amount']));
			$tmp[] = array('data' => $v['rl_addtime'] ? date('Y-m-d H:i:s', $v['rl_addtime']) : '');
			$tmp[] = array('data' => str_replace(array(0, 1), array('待支付', '已支付'), $v['rl_state']));
			$tmp[] = array('data' => str_replace(array('alipay', 'wxpay'), array('支付宝', '微信'), $v['rl_recharge_type']));
			$tmp[] = array('data' => $v['rl_paytime'] ? date('Y-m-d H:i:s', $v['rl_paytime']) : '');
			$excel_data[] = $tmp;
		}
		$excel_data = $excel_obj->charset($excel_data, CHARSET);
		$excel_obj->addArray($excel_data);
		$excel_obj->addWorksheet($excel_obj->charset('充值管理', CHARSET));
		$excel_obj->generateXML($excel_obj->charset('充值管理', CHARSET) . $_GET['curpage'] . '-' . date('Y-m-d-H', time()));
	}
}