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
        $total_bean = 0;
        $total_equity = 0;
        $total_consume = 0;
        $model_member_extend = Model('member_extend');
        $availInfo1 = $model_member_extend->getAllMemberExtendAvail();
        $availInfo2 = $model_member_extend->getAllMemberAvail();
//        var_dump($availInfo1,$availInfo2);die();
        $total_set_meal_amount = $availInfo1[0]['set_meal_amount'];
        $total_bonus = $availInfo2[0]['bonus'];
        $total_balance = $availInfo2[0]['balance'];
        $total_equity = $availInfo1[0]['member_equity'];
        $total_consume = $availInfo1[0]['total_consume'];

        Tpl::output('total_bonus', floatFormat($total_bonus));
        Tpl::output('total_balance', floatFormat($total_balance));
        Tpl::output('total_equity', floatFormat($total_equity));
        Tpl::output('total_consume', floatFormat($total_consume));
        Tpl::output('total_set_meal_amount', floatFormat($total_set_meal_amount));
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
				echo json_encode(array('id' => $info['member_id'], 'name' => $info['member_name'], 'balance' => $info['balance'], 'equity' => $info['member_equity'], ));
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
        $model_member_extend = Model('member_extend');
        $condition = array();
        if ($_POST['query'] != '') {
            $condition[$_POST['qtype']] = array('like', '%' . $_POST['query'] . '%');
        }

        $order = '';
        $order_member_extend = '';

        $param_member = array('member_id', 'member_name', 'member_mobile', 'member_time', 'member_login_time');
        $param_member_extend = array('member_golden_bean', 'member_golden_bean_payed', 'member_equity', 'total_performance', 'new_performance', 'surplus_performance', 'day_total_consume', 'total_consume', 'depth');
        if (in_array($_POST['sortname'], $param_member) && in_array($_POST['sortorder'], array('asc', 'desc'))) {
            $order = 'member.' . $_POST['sortname'] . ' ' . $_POST['sortorder'];
        } elseif (in_array($_POST['sortname'], $param_member_extend) && in_array($_POST['sortorder'], array('asc', 'desc'))) {
            $order = 'member_extend.' . $_POST['sortname'] . ' ' . $_POST['sortorder'];
        }

        $page = $_POST['rp'];
        $fields = 'member_id,member_mobile,member_name,balance,bonus,member_equity,set_meal_amount,total_consume';
        $member_list = $model_member_extend->getMemberExtendList($condition, $fields, 'union', $page, $order);
        $data = array();
        $data['now_page'] = $model_member_extend->shownowpage();
        $data['total_num'] = $model_member_extend->gettotalnum();

        foreach ($member_list as $v) {
            //以下变量不要随意改动顺序
            $param = array();
            $operation = "----";
//            $operation = "<a class='btn blue' href='index.php?act=account&op=account_adjust&member_id=" . $v['member_id'] . "'><i class='fa fa-pencil-square-o'></i>编辑</a>";
            $param['operation'] = $operation;
            $param['member_mobile'] = $v['member_mobile'];
            $param['member_name'] = $v['member_name'];
            $param['balance'] = $v['balance'];
//            $total_bean += $availBean;
            $param['bonus'] = $v['bonus'];
            $param['member_equity'] = $v['member_equity'];
            $param['set_meal_amount'] = $v['set_meal_amount'];
            $param['total_consume'] = $v['total_consume'];
            $data['list'][$v['member_id']] = $param;
        }
//        var_dump($data);die();
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
        $model_member_extend = Model('member_extend');
        $id = $_GET['id'];
        if ($id) {
            $condition['member_id'] = array('in', $id);
            $data = $model_member_extend->getMemberExtendList($condition, '*', 'union');

        } else {
            $data = $model_member_extend->getAllMemberExtendList();
        }

        $excel_obj = new Excel();
        $excel_data = array();
        // 设置样式
        $excel_obj->setStyle(array('id' => 's_title', 'Font' => array('FontName' => '宋体', 'Size' => '12', 'Bold' => '1')));
        // header
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '会员手机');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '真实姓名');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '余额');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '分红');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '股权数');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '会员套餐金额');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '消费金额');
//        $excel_data[0][] = array('styleid' => 's_title', 'data' => "银豆");
        foreach ((array) $data as $k => $v) {
            $tmp = array();
            $tmp[] = array('data' => $v['member_mobile']);
            $tmp[] = array('data' => $v['member_name']);
            $tmp[] = array('data' => floatval($v['balance']));
            $tmp[] = array('data' => floatval($v['bonus']));
            $tmp[] = array('data' => floatval($v['member_equity']));
            $tmp[] = array('data' => floatval($v['set_meal_amount']));
            $tmp[] = array('data' => floatval($v['total_consume']));
//            $tmp[] = array('data' => floatval($model_member_extend->getAvailSilverBeanById($v['member_id'])));
            $excel_data[] = $tmp;
        }
        $excel_data = $excel_obj->charset($excel_data, CHARSET);
        $excel_obj->addArray($excel_data);
        $excel_obj->addWorksheet($excel_obj->charset('会员账户', CHARSET));
        $excel_obj->generateXML($excel_obj->charset('会员账户', CHARSET) . $_GET['curpage'] . '-' . date('Y-m-d-H', time()));
    }
}