<?php
/**
 * 报单列表
 *
 */
defined('In33hao') or exit('Access Invild!');

class declaration_listControl extends SystemControl
{
    public function __construct()
    {
        parent::__construct();
    }

    public function indexOp()
    {
        $this->equity_manageOp();
    }

    /**
     * equity_manageOp 报单列表
     * @return [type] [description]
     */
    public function equity_manageOp()
    {

        Tpl::setDirquna('declaration_from');
        Tpl::showpage('declaration_list.list');
    }

    /**
     * get_xmlOp 获取报单列表
     * @return [type] [description]
     */
    public function get_xmlOp()
    {
        $model_declaration_form = Model('declaration_form');
        $condition = array();
        $this->_get_condition($condition);
        $order = 'add_time desc';
        $page = $_POST['rp'];
        $equity_list = $model_declaration_form->getDeclarationFromList($condition, '*', $page, $order);
        $data = array();
        $data['now_page'] = $model_declaration_form->shownowpage();
        $data['total_num'] = $model_declaration_form->gettotalnum();
        foreach ($equity_list as $v) {
            $param = array();
            $param['order_sn'] = $v['order_sn'];
            $param['member_id'] = $v['member_id'];
            $param['member_name'] = Model('member')->getMemberNameById($v['member_id']);
            $param['inviter_id'] = $v['inviter_id'];
            $param['invite_one'] = $v['invite_one'];
            $param['add_time'] = $v['add_time'] ? date('Y-m-d H:i:s', $v['add_time']) : '';
            $param['integral_start_time'] = $v['integral_start_time'] ? date('Y-m-d H:i:s', $v['integral_start_time']) : '';
            $param['integral_end_time'] = $v['integral_end_time'] ? date('Y-m-d H:i:s', $v['integral_end_time']) : '';
            $param['state'] = $model_declaration_form->state[$v['state']];
            $param['operation'] = "<a class='btn blue' href='index.php?act=declaration_list&op=equity_edit&id=" . $v['id'] . "'><i class='fa fa-pencil-square-o'></i>编辑</a>";
            if ($v['state'] == 0)  {
                $param['operation'] .= "<a class='btn blue' href='index.php?act=declaration_list&op=equity_examine&id=" . $v['id'] . "'><i class='fa fa-pencil-square-o'></i>审核</a><a class='btn blue' href='index.php?act=declaration_list&op=equity_refuse&id=" . $v['id'] . "'><i class='fa fa-pencil-square-o'></i>拒绝</a>";
            }
            $data['list'][$v['id']] = $param;
        }
        echo Tpl::flexigridXML($data);
        exit();
    }


    /**
     * 报单审核
     */
    public function equity_examineOp()
    {
        $id = $_GET['id'];
        $condition['id'] = $id;
        $declaration_info = Model('declaration_form')->getDeclarationFromInfo($condition);
        if (empty($declaration_info) || $declaration_info['state'] != 0){
            showMessage('操作失败', '', '', 'error');exit;
        }
        $member_id = $declaration_info['member_id'];
        $time = time();
        $data['state'] = 1;
        $data['audit_time'] = $time;
        $data['integral_start_time'] = $time;
        $data['integral_end_time'] = $time+$declaration_info['declaration_time']*86400;
        Model::beginTransaction();
        try {
            $res = Model('declaration_form')->editDeclarationFrom($condition, $data);
            if ($res) {
                Logic('distribution')->implementDistribution($declaration_info);
            }
            Model::commit();
            showMessage('审核通过', '', '', 'succ');
        }catch (Exception $e){
            showMessage('操作失败', '', '', 'error');
            Model::rollback();
        }
    }

    /**
     * 报单拒绝
     */
    public function equity_refuseOp()
    {
        $id = $_GET['id'];
        $condition['id'] = $id;
        $declaration_info = Model('declaration_form')->getDeclarationFromInfo($condition);
        if (empty($declaration_info) || $declaration_info['state'] != 0){
            showMessage('操作失败', '', '', 'error');exit;
        }
        $time = time();
        $data['state'] = 3;
        $data['audit_time'] = $time;
        Model::beginTransaction();
        try{
            $res = Model('declaration_form')->editDeclarationFrom($condition, $data);
            if ($res) {
                //退款操作

                Model::commit();
                showMessage('已拒绝', '', '', 'succ');
            }
        }catch (Exception $e){
            Model::rollback();
            showMessage('操作失败', '', '', 'error');
        }
    }

    /**
     * export_xlsOp 导出execl文件
     * @return [type] [description]
     */
    public function export_xlsOp()
    {
        $condition = array();
        $id = $_GET['id'];
        if ($id) {
            $condition['elg_id'] = array('in', $id);
        } else {
            $this->_get_condition($condition);
        }
        $data = Model('equity')->getEquityLogList($condition, '*', null, 'elg_addtime desc', false);
        $excel_obj = new Excel();
        $excel_data = array();
        // 设置样式
        $excel_obj->setStyle(array('id' => 's_title', 'Font' => array('FontName' => '宋体', 'Size' => '12', 'Bold' => '1')));
        // header
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '会员手机');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '会员姓名');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '单号');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '类型');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '金额');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '调整后金额');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '时间');
        foreach ((array)$data as $k => $v) {
            $tmp = array();
            $tmp[] = array('data' => Model('member')->getMemberMobileById($v['elg_member_id']));
            $tmp[] = array('data' => $v['elg_member_name']);
            $tmp[] = array('data' => $v['elg_sn']);
            $tmp[] = array('data' => str_replace(array('admin', 'consume', 'exchange'), array('管理调整', '消费', '兑换'), $v['elg_type']));
            $tmp[] = array('data' => floatval($v['elg_amount']));
            $tmp[] = array('data' => floatval($v['elg_equity_amount']));
            $tmp[] = array('data' => $v['elg_addtime'] ? date('Y-m-d H:i:s', $v['elg_addtime']) : '');
            $excel_data[] = $tmp;
        }
        $excel_data = $excel_obj->charset($excel_data, CHARSET);
        $excel_obj->addArray($excel_data);
        $excel_obj->addWorksheet($excel_obj->charset('报单列表', CHARSET));
        $excel_obj->generateXML($excel_obj->charset('报单列表', CHARSET) . $_GET['curpage'] . '-' . date('Y-m-d-H', time()));
    }

    /**
     * _get_condition 搜索条件整理
     * @param  [type] &$condition [description]
     * @return [type]             [description]
     */
    private function _get_condition(&$condition)
    {
        $param = $_REQUEST;
        if ($param['query'] != '') {
            if ($param['qtype'] == 'order_sn') {
                $condition[$param['qtype']] = array('like', '%' . $param['query'] . '%');
            } else {
                $condition[$param['qtype']] = intval($param['query']);
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


    /**
     * 报单编辑页面
     */
    public function equity_editOp()
    {
        if (chksubmit()) {
            $validate = new Validate();
            $validate->validateparam = array();
            $validate->validateparam = array(
                array('input' => $_POST['declaration_time'], 'require' => "true", 'message' => "报单时间不能为空"),
                array('input' => $_POST['declaration_time'], 'validator' => "number", 'message' => "报单时间必须为整数"),
                array('input' => $_POST['cycle_time'], 'require' => "true", 'message' => "绩效考核时间不能为空"),
                array('input' => $_POST['cycle_time'], 'validator' => "number", 'message' => "绩效考核时间必须为整数"),
                array('input' => $_POST['estimate_integral'], 'require' => "true", 'message' => "预期收益积分不能为空"),
                array('input' => $_POST['estimate_integral'], 'validator' => "number", 'message' => "预期收益积分必须为整数"),
                array('input' => $_POST['deduction_integral'], 'require' => "true", 'message' => "绩效扣除本人分数不能为空"),
                array('input' => $_POST['deduction_integral'], 'validator' => "number", 'message' => "绩效扣除本人分数必须为整数"),
                array('input' => $_POST['superior_deduction_integral'], 'require' => "true", 'message' => "绩效扣除上级分数不能为空"),
                array('input' => $_POST['superior_deduction_integral'], 'validator' => "number", 'message' => "绩效扣除上级分数必须为整数"),
                array('input' => $_POST['inviter_id'], 'require' => "true", 'message' => "用户填写的推荐人ID不能为空"),
                array('input' => $_POST['inviter_id'], 'validator' => "number", 'message' => "用户填写的推荐人ID必须为整数"),
                array('input' => $_POST['invite_one'], 'require' => "true", 'message' => "实际绑定的推荐人ID不能为空"),
                array('input' => $_POST['invite_one'], 'validator' => "number", 'message' => "实际绑定的推荐人ID必须为整数"),
                array('input' => $_POST['invite_two'], 'require' => "true", 'message' => "上二级的推荐人ID不能为空"),
                array('input' => $_POST['invite_two'], 'validator' => "number", 'message' => "上二级的推荐人ID必须为整数"),
                array('input' => $_POST['invite_three'], 'require' => "true", 'message' => "上三级的推荐人ID不能为空"),
                array('input' => $_POST['invite_three'], 'validator' => "number", 'message' => "上三级的推荐人ID必须为整数"),
                array('input' => $_POST['invite_four'], 'require' => "true", 'message' => "上四级的推荐人ID不能为空"),
                array('input' => $_POST['invite_four'], 'validator' => "number", 'message' => "上四级的推荐人ID必须为整数"),
                array('input' => $_POST['invite_five'], 'require' => "true", 'message' => "上五级的推荐人ID不能为空"),
                array('input' => $_POST['invite_five'], 'validator' => "number", 'message' => "上五级的推荐人ID必须为整数"),
                array('input' => $_POST['invite_six'], 'require' => "true", 'message' => "上六级的推荐人ID不能为空"),
                array('input' => $_POST['invite_six'], 'validator' => "number", 'message' => "上六级的推荐人ID必须为整数"),
                array('input' => $_POST['invite_seven'], 'require' => "true", 'message' => "上七级的推荐人ID不能为空"),
                array('input' => $_POST['invite_seven'], 'validator' => "number", 'message' => "上七级的推荐人ID必须为整数"),
            );
            $error = $validate->validate();
            if ($error != '') {
                showMessage($error, '', '', 'error');
            }

            $id = trim($_POST['id']);
            $update_data = array();
            $update_data['declaration_time'] = intval($_POST['declaration_time']);
            $update_data['cycle_time'] = intval($_POST['cycle_time']);
            $update_data['estimate_integral'] = intval($_POST['estimate_integral']);
            $update_data['deduction_integral'] = intval($_POST['deduction_integral']);
            $update_data['superior_deduction_integral'] = intval($_POST['superior_deduction_integral']);
            $update_data['inviter_id'] = intval($_POST['inviter_id']);
            $update_data['invite_one'] = intval($_POST['invite_one']);
            $update_data['invite_two'] = intval($_POST['invite_two']);
            $update_data['invite_three'] = intval($_POST['invite_three']);
            $update_data['invite_four'] = intval($_POST['invite_four']);
            $update_data['invite_five'] = intval($_POST['invite_five']);
            $update_data['invite_six'] = intval($_POST['invite_six']);
            $update_data['invite_seven'] = intval($_POST['invite_seven']);

            $update = Model('declaration_form')->editDeclarationFrom(array('id' => $id), $update_data);
            if ($update) {
                showMessage('编辑成功', 'index.php?act=declaration_list', '', 'succ');
            } else {
                showMessage('编辑失败', '', '', 'error');
            }
        } else {
            $id = intval(trim($_GET['id']));
            if ($id) {
                $info = Model('declaration_form')->getDeclarationFromInfo($condition);
                Tpl::output('info', $info);
                Tpl::setDirquna('declaration_from');
                Tpl::showpage('declaration_list.edit');
            }
        }
    }
}