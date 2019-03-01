<?php
/**
 * 支持银行
 */
defined('In33hao') or exit('Access Invalid!');

class bankControl extends SystemControl
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * indexOp 入口
     * @return [type] [description]
     */
    public function indexOp()
    {
        $this->bankListOp();
    }

    /**
     * bankListOp 银行列表
     * @return [type] [description]
     */
    public function bankListOp()
    {
        $model_bank = Model('bank');
        $bank_list = $model_bank->getAllBankList();
        Tpl::setDirquna('shop');
        Tpl::showpage('bank.list');
    }

    /**
     * bankEditOp 编辑银行信息
     * @return [type] [description]
     */
    public function bankEditOp()
    {
        $model_bank = Model('bank');

        if (chksubmit()) {
            $validate = new Validate();
            $validate->validateparam = array(
                array('input' => $_POST['bank_name'], 'require' => "true", 'message' => "请输入银行名称"),
                array('input' => $_POST['bank_abbreviation'], 'require' => "true", 'message' => "请输入银行简称"),
                array('input' => $_POST['bank_logo'], 'require' => "true", 'message' => "请输入银行图标"),
            );
            $error = $validate->validate();
            if ($error != '') {
                showMessage($error, '', '', 'error');
            }

            $bank_data = array(
                'bank_name' => $_POST['bank_name'],
                'bank_abbreviation' => $_POST['bank_abbreviation'],
                'bank_logo' => $_POST['bank_logo'],
                'bank_state' => $_POST['bank_state'],
            );
            $update = $model_bank->updateBankInfo(array('bank_id' => $_POST['bank_id']), $bank_data);
            if (!$update) {
                showMessage('银行信息更新失败', '', '', 'error');
            } else {
                showMessage('银行信息更新成功', 'index.php?act=bank&op=index', '', 'succ');
            }
        } else {
            $bank_id = $_GET['bank_id'];
            $bank_info = $model_bank->getBankInfo(array('bank_id' => $bank_id));
            Tpl::setDirquna('shop');
            Tpl::output('bank_info', $bank_info);
            Tpl::showpage('bank.edit');
        }
    }

    /**
     * bankAddOp 添加银行信息
     * @return [type] [description]
     */
    public function bankAddOp()
    {
        $model_bank = Model('bank');

        if (chksubmit()) {

            $validate = new Validate();
            $validate->validateparam = array(
                array('input' => $_POST['bank_name'], 'require' => "true", 'message' => "请输入银行名称"),
                array('input' => $_POST['bank_abbreviation'], 'require' => "true", 'message' => "请输入银行简称"),
                array('input' => $_POST['bank_logo'], 'require' => "true", 'message' => "请输入银行图标"),
            );
            $error = $validate->validate();
            if ($error != '') {
                showMessage($error, '', '', 'error');
            }
            $bank_data = array(
                'bank_name' => trim($_POST['bank_name']),
                'bank_abbreviation' => trim($_POST['bank_abbreviation']),
                'bank_logo' => trim($_POST['bank_logo']),
                'bank_state' => $_POST['bank_state'],
            );
            $result = $model_bank->addBankInfo($bank_data);
            if (!$result) {
                showMessage('银行信息添加失败', '', '', 'error');
            } else {
                showMessage('银行信息添加成功', 'index.php?act=bank&op=bankList', '', 'succ');
            }

        } else {
            Tpl::setDirquna('shop');
            Tpl::showpage('bank.add');
        }
    }

    /**
     * bankDeleteOp 银行信息删除
     * @return [type] [description]
     */
    public function bankDeleteOp()
    {
        $bank_id = $_GET['bank_id'];
        $update = Model('bank')->deleteBankInfo(array('bank_id' => $bank_id));
        if ($update) {
            showMessage('删除成功', 'index.php?act=bank&op=index', '', 'succ');
        } else {
            showMessage('删除失败', '', '', 'error');
        }
    }

    /**
     * get_xmlOp 获取银行列表
     * @return [type] [description]
     */
    public function get_xmlOp()
    {
        $model_bank = Model('bank');
        $bank_list = $model_bank->getAllBankList();
        $data = array();
        $data['now_page'] = 1;
        $data['total_num'] = count($bank_list);
        foreach ($bank_list as $v) {
            $param = array();
            $param['operation'] = "<a class='btn blue' href='index.php?act=bank&op=bankEdit&bank_id=" . $v['bank_id'] . "'><i class='fa fa-pencil-square-o'></i>编辑</a><a class='btn blue' href='index.php?act=bank&op=bankDelete&bank_id=" . $v['bank_id'] . "'><i class='fa fa-pencil-square-o'></i>删除</a>";
            $param['bank_name'] = $v['bank_name'];
            $param['bank_abbreviation'] = $v['bank_abbreviation'];
            $param['bank_logo'] = "<img width='30' height='30' src='" . $v['bank_logo'] . "'>";
            $param['bank_state'] = str_replace(array(0, 1), array('<span class="no"><i class="fa fa-ban"></i>不支持</span>', '<span class="yes"><i class="fa fa-check-circle"></i>支持</span>'), $v['bank_state']);
            $data['list'][$v['bank_id']] = $param;
        }
        Tpl::flexigridXML($data);
    }
}
