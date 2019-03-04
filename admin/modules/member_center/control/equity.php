<?php
/**
 * 积分互赠管理
 *
 */
defined('In33hao') or exit('Access Invild!');

class equityControl extends SystemControl
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
     * equity_manageOp 积分互赠管理
     * @return [type] [description]
     */
    public function equity_manageOp()
    {

        Tpl::setDirquna('member_center');
        Tpl::showpage('equity.equity_manage');
    }

    /**
     * get_xmlOp 获取积分互赠日志
     * @return [type] [description]
     */
    public function get_xmlOp()
    {
        $model_equity = Model('equity');
        $condition = array();
        $this->_get_condition($condition);
        $order = 'elg_addtime desc';
        $page = $_POST['rp'];
        $equity_list = $model_equity->getEquityLogList($condition, '*', $page, $order);
        $equity_list_join = $model_equity->joinEquityListArr($equity_list);
//		var_dump($equity_list_join);die();
        $data = array();
        $data['now_page'] = $model_equity->shownowpage();
        $data['total_num'] = $model_equity->gettotalnum();
        foreach ($equity_list_join as $v) {
            $param = array();
            if ($v['elg_type'] == 'exchange') {
                if ($v['elg_state'] != 0) {
                    if ($v['elg_state'] == 1) {
                        $param['operation'] = "<span style='color: #3AA55A;'>已审核</span>";
                    } else {
                        $param['operation'] = "<span style='color: #ff3c00;'>已拒绝</span>";
                    }
                } else {
                    $param['operation'] = "<a class='btn blue' href='index.php?act=equity&op=equity_examine&id=" . $v['elg_id'] . "'><i class='fa fa-pencil-square-o'></i>审核</a><a class='btn blue' href='index.php?act=equity&op=equity_refuse&id=" . $v['elg_id'] . "'><i class='fa fa-pencil-square-o'></i>拒绝</a>";
                }
            } else {
                $param['operation'] = "<span style='color: #3AA55A;'>已调整</span>";
            }
            $param['member_mobile'] = Model('member')->getMemberMobileById($v['elg_member_id']);
            $param['elg_member_name'] = $v['elg_member_name'];
            $param['elg_sn'] = $v['elg_sn'];
            $param['equity_type'] = str_replace(array("admin", "exchange", "consume"), array("管理员调整", "兑换", "消费"), $v['elg_type']);
            $param['elg_amount'] = $v['elg_amount'];
            $param['elg_equity_amount'] = $v['elg_equity_amount'];
            $param['elg_addtime'] = $v['elg_addtime'] ? date('Y-m-d H:i:s', $v['elg_addtime']) : '';
            $param['equity_desc'] = $v['equity_desc'];
            $data['list'][$v['elg_id']] = $param;
        }
//            var_dump($data['list']);die();
        echo Tpl::flexigridXML($data);
        exit();
    }


    /**
     * 积分互赠审核
     */
    public function equity_examineOp()
    {
        $elg_id = $_GET['id'];
        $condition['elg_id'] = $elg_id;
        $data['elg_state'] = 1;
        $data['elg_paytime'] = time();
        Model::beginTransaction();
        try {
            $res = Model('equity')->editEquityLog($condition, $data);
            if ($res) {
                //增加会员积分互赠
                $add_data = Model('equity')->getEquityLogInfo(array('elg_id' => $elg_id), 'elg_member_id,elg_amount');
                $change_equity_res = Model('member_extend')->setIncMemberExtendField(array('me_member_id' => $add_data['elg_member_id']), array('member_equity' => $add_data['elg_amount']));
            }
            Model::commit();
            showMessage('审核通过', '', '', 'succ');
        }catch (Exception $e){
            showMessage('审核失败', '', '', 'error');
            Model::rollback();
        }
    }

    /**
     * 积分互赠拒绝
     */
    public function equity_refuseOp()
    {
        $elg_id = $_GET['id'];
        $condition['elg_id'] = $elg_id;
        $data['elg_state'] = 2;
        $data['elg_paytime'] = time();
        Model::beginTransaction();
        try{
            $res = Model('equity')->editEquityLog($condition, $data);
            if ($res) {
                //退还会员余额
                $add_data = Model('equity')->getEquityLogInfo(array('elg_id' => $elg_id), '*');
                $data['member_id'] = $add_data['elg_member_id'];
                $data['member_name'] = $add_data['elg_member_name'];
                $data['balance_amount'] = -$add_data['elg_balance_amount'];
                $data['member_id'] = $add_data['elg_member_id'];
                $change_balance_res = Model('member_extend')->changeMemberBalance('equity',$data);
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
        $excel_obj->addWorksheet($excel_obj->charset('积分互赠管理', CHARSET));
        $excel_obj->generateXML($excel_obj->charset('积分互赠管理', CHARSET) . $_GET['curpage'] . '-' . date('Y-m-d-H', time()));
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
            if ($param['qtype'] == 'member_mobile') {
                $list = Model('member')->getMemberList(array('member_mobile' => array('like', '%' . $param['query'] . '%')));
                if (!empty($list)) {
                    $arr = array();
                    foreach ($list as $v) {
                        $arr[] = $v['member_id'];
                    }
                    $condition['elg_member_id'] = array('in', $arr);
                } else {
                    $condition['elg_member_id'] = null;
                }
            } else {
                $condition[$param['qtype']] = array('like', '%' . $param['query'] . '%');
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
                $condition['elg_member_id'] = array('in', $arr);
            } else {
                $condition['elg_member_id'] = null;
            }
        }

        if ($param['query_start_date'] && $param['query_end_date'] && strtotime($param['query_start_date']) <= strtotime($param['query_end_date'])) {
            $condition['elg_addtime'] = array('between', array(strtotime($param['query_start_date']), strtotime("+1 day", strtotime($param['query_end_date'])) - 1));
        } elseif ($param['query_start_date']) {
            $condition['elg_addtime'] = array('egt', strtotime($param['query_start_date']));
        } elseif ($param['query_end_date']) {
            $condition['elg_addtime'] = array('elt', strtotime("+1 day", strtotime($param['query_end_date'])) - 1);
        }
    }
}