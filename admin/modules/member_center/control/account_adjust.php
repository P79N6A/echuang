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
            $model_declaration_form = Model('declaration_form');
            $member_id = trim($_POST['member_id']);//用户id
            $amount = $_POST['adjust_amount'];//调整数量
            $account_type = $_POST['account_type'];//账户类型  1、积分 2、预期积分
            $operate_type = $_POST['operate_type'];//增减类型  1、增加 2、减少
            $info = $model_declaration_form->getIntegralTotal($member_id,2);
            if (empty($info)) {
                showMessage('会员信息错误', '', '', 'error');
            }
            if (!$info['member_state']) {
                showMessage('该用户已冻结,不能修改账户', '', '', 'error');
            }
            if (!$info['id'] && $account_type==2){
                showMessage('该用户未购买报单，不能调整预期积分', '', '', 'error');
            }
//            $data = array();
//            $data['member_id'] = $member_id;
//            $data['amount'] = $amount;
//            $data['member_name'] = $info['member_name'];
//            $data['admin_name'] = $this->admin_info['name'];
//            $data['desc'] = trim($_POST['adjust_sdesc']);
            Tpl::output('operate_type', $operate_type);
            Tpl::output('account_type', $account_type);

            //调整方法
            $result = $model_declaration_form->changeMemberIntegral($member_id,$account_type,$operate_type,$amount,$info,trim($_POST['adjust_sdesc']),9);

            if ($result) {
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