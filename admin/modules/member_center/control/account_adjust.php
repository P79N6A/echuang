<?php
/**
 * 账户调整
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/20 0020
 * Time: 上午 10:39
 */
defined('In33hao') or exit('Access Invild!');
class account_adjustControl extends SystemControl {
    private $links = array(
//        array('url' => 'act=account&op=account_manage', 'lang' => 'member_account_manage'),
		array('url' => 'act=account&op=account_adjust', 'lang' => 'member_account_adjust'),
    );

    public function __construct()
    {
        parent::__construct();
        Language::read('member');
    }

    public function indexOp()
    {
        $this->account_adjustOp();
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
                array('input' => $_POST['adjust_amount'], 'require' => "true", 'message' => "请输入调整数量"),
                array('input' => $_POST['adjust_amount'], 'validator' => "double", 'message' => "调整数量须为数字"),
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
                $result = $model_member_extend->changeMemberBalance('admin', $data);
            } elseif ($account_type == 2) {
                // 股权调整
                $result = $model_member_extend->changeEquity('admin', $data);
            }
            if (!isset($result['error'])) {
                showMessage('账户调整成功', 'index.php?act=account_adjust&op=account_adjust', '', 'succ');
            } else {
                showMessage('账户调整失败', '', '', 'error');
            }
        }

        Tpl::output('top_link', $this->sublink($this->links, 'account_adjust'));
        Tpl::setDirquna('member_center');
        Tpl::showpage('account.account_adjust');
    }



}