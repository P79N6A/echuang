<?php
/**
 * 账户管理
 *
 */
defined('In33hao') or exit('Access Invild!');
class accountControl extends SystemControl {
	private $links = array(
		array('url' => 'act=account&op=account_manage', 'lang' => 'member_account_manage'),
		//array('url' => 'act=account&op=account_adjust', 'lang' => 'member_account_adjust'),
	);

    public function __construct()
    {
        parent::__construct();
        Language::read('member');
    }

    public function indexOp()
    {
        $this->account_manageOp();
    }

    /**
     * account_manageOp 账户管理
     * @return [type] [description]
     */
    public function account_manageOp()
    {
        $integral_total = Model('declaration_form')->getIntegralTotal();

        Tpl::output('integral_total', floatFormat($integral_total['integral_total']));
        Tpl::output('estimate_integral_total', floatFormat($integral_total['estimate_integral_total']));
        $getGoldNumType1=Model('member_extend')->getGoldNumType1();
        $getGoldNumType2=Model('member_extend')->getGoldNumType2();
		Tpl::output('total_golden_coupon', floatFormat($getGoldNumType1[0]['min_count']+$getGoldNumType2[0]['max_count']*10));
        Tpl::output('top_link', $this->sublink($this->links, 'account_manage'));
        Tpl::setDirquna('member_center');
        Tpl::showpage('account.account_manage');
    }

    /**
     * account_adjustOp 账户调整
     * @return [type] [description]
     */
    public function account_adjustOp()
    {
        if (chksubmit()) {
            $validate = new Validate();
            $validate->validateparam = array(
                array('input' => $_POST['member_mobile'], 'require' => "true", 'message' => "请输入会员手机号"),
                array('input' => $_POST['adjust_amount'], 'require' => "true", 'message' => "请输入调整金额"),
                array('input' => $_POST['adjust_amount'], 'validator' => "double", 'message' => "调整金额须为数字"),
            );
            $error = $validate->validate();
            if ($error != '') {
                showMessage($error, '', '', 'error');
            }
            $model_member_extend = Model('member_extend');
            $member_id = trim($_POST['member_id']);
            $amount = $_POST['adjust_amount'];
            $account_type = $_POST['account_type'];
            $operate_type = $_POST['operate_type'];
            $info = $model_member_extend->getMemberExtendInfo(array('member_id' => $member_id), 'member_equity,balance,member_state,member_name', 'union');
            if (empty($info)) {
                showMessage('会员信息错误', '', '', 'error');
            }
            if (!$info['member_state']) {
                showMessage('该用户已冻结,不能修改账户', '', '', 'error');
            }
            $data = array();
            $data['member_id'] = $member_id;
            $data['amount'] = $amount;
            $data['member_name'] = $info['member_name'];
            $data['admin_name'] = $this->admin_info['name'];
            $data['desc'] = trim($_POST['adjust_sdesc']);
            Tpl::output('operate_type', $operate_type);
            Tpl::output('account_type', $account_type);

            if ($operate_type == 1) {
                $data['operate_type'] = 'add';
            } else {
                $data['operate_type'] = 'sub';
                if ($account_type == 1) {
                    if ($info['balance'] < $amount) {
                        showMessage('账户可用余额不足', '', '', 'error');
                    }
                } else {
                    if ($info['member_equity'] < $amount) {
                        showMessage('账户可用股权不足', '', '', 'error');
                    }
                }
            }

            if ($account_type == 1) {
                // 余额调整
                $result = $model_member_extend->changeBalance('admin', $data);
            } elseif ($account_type == 2) {
                // 股权调整
                $result = $model_member_extend->changeEquity('admin', $data);
            }
            if (!isset($result['error'])) {
                showMessage('账户调整成功', 'index.php?act=account&op=account_adjust', '', 'succ');
            } else {
                showMessage('账户调整失败', '', '', 'error');
            }
        }

        Tpl::output('top_link', $this->sublink($this->links, 'account_adjust'));
        Tpl::setDirquna('member_center');
        Tpl::showpage('account.account_adjust');
    }

    /**
     * account_entryOp 账户录入
     * @return [type] [description]
     */
    public function account_entryOp()
    {
        if (chksubmit()) {

        }
        Tpl::output('top_link', $this->sublink($this->links, 'account_entry'));
        Tpl::setDirquna('member_center');
        Tpl::showpage('account.account_entry');
    }

    /**
     * check_memberOp 检查用户是否存在
     * @return [type] [description]
     */
    public function check_memberOp()
    {
        $member_mobile = trim($_GET['mobile']);
        if ($member_mobile) {
            $model_member_extend = Model('member_extend');
            $fields = 'member_id,member_name,balance,member_equity,member_golden_bean_payed,member_golden_bean_freeze,member_silver_bean,member_silver_bean_payed';
            $info = $model_member_extend->getMemberExtendInfo(array('member_mobile' => $member_mobile), $fields, 'union');
            if (!empty($info)) {
                $integral_total = Model('declaration_form')->getIntegralTotal($info['member_id'],2);
				echo json_encode(array('id' => $info['member_id'], 'name' => $info['member_name'], 'balance' => $integral_total['m_integral'], 'equity' => $integral_total['estimate_integral'], ));
            } else {
                echo "";
            }
        } else {
            echo "";
        }

    }

    /**
     * get_xmlOp 获取账户信息
     * @return [type] [description]
     */
    public function get_xmlOp()
    {
        $model_declaration_form = Model('declaration_form');
        $condition = array();
        if ($_POST['query'] != '') {
            $condition[$_POST['qtype']] = array('like', '%' . $_POST['query'] . '%');
        }

        $page = $_POST['rp'];
        $fields = 'member.member_id,member_mobile,member_name,member.integral,estimate_integral';

        $member_list = $model_declaration_form->getMemberDeclarationList($condition, $fields, $page);
        $data = array();
        $data['now_page'] = $model_declaration_form->shownowpage();
        $data['total_num'] = $model_declaration_form->gettotalnum();

        foreach ($member_list as $v) {
            //以下变量不要随意改动顺序
            $times = $model_declaration_form->getOutgoTimes($v['member_id']);
            $param = array();
            $operation = "----";
            $param['operation'] = $operation;
            $param['member_mobile'] = $v['member_mobile'];
            $param['member_name'] = $v['member_name'];
            $param['integral'] = $v['integral'];
            $param['estimate_integral'] = $v['estimate_integral']?$v['estimate_integral']:0;
            $param['declaration_times'] = $times['declaration_times'];
            $param['outgo_times'] = $times['outgo_times'];
            $data['list'][$v['member_id']] = $param;
        }
        echo Tpl::flexigridXML($data);
        exit();
    }

    /**
     * export_xlsOp 导出xls文件
     * @return [type] [description]
     */
    public function export_xlsOp()
    {
        import('libraries.excel');
        $model_declaration_form = Model('declaration_form');
        $condition = array();
        $data = $model_declaration_form->getMemberDeclarationList($condition);


        $excel_obj = new Excel();
        $excel_data = array();
        // 设置样式
        $excel_obj->setStyle(array('id' => 's_title', 'Font' => array('FontName' => '宋体', 'Size' => '12', 'Bold' => '1')));
        // header
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '会员手机');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '真实姓名');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '积分');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '预期积分');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '报单次数');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '出局次数');
        foreach ((array) $data as $k => $v) {
            $times = $model_declaration_form->getOutgoTimes($v['member_id']);
            $tmp = array();
            $tmp[] = array('data' => $v['member_mobile']);
            $tmp[] = array('data' => $v['member_name']);
            $tmp[] = array('data' => $v['integral']);
            $tmp[] = array('data' => $v['estimate_integral']);
            $tmp[] = array('data' => $times['declaration_times']);
            $tmp[] = array('data' => $times['outgo_times']);
            $excel_data[] = $tmp;
        }
        $excel_data = $excel_obj->charset($excel_data, CHARSET);
        $excel_obj->addArray($excel_data);
        $excel_obj->addWorksheet($excel_obj->charset('会员账户', CHARSET));
        $excel_obj->generateXML($excel_obj->charset('会员账户', CHARSET) . $_GET['curpage'] . '-' . date('Y-m-d-H', time()));
    }
}