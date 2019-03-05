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
        $model_member_integral_log = Model('member_integral_log');
        $model_member = Model('member');
        $condition = array('type'=>5);
        $this->_get_condition($condition);
        $order = 'add_time desc';
        $page = $_POST['rp'];
        $member_integral_log_list = $model_member_integral_log->getMemberIntegralLogList($condition, '*', $page, $order);
        $data = array();
        $data['now_page'] = $model_member_integral_log->shownowpage();
        $data['total_num'] = $model_member_integral_log->gettotalnum();
        foreach ($member_integral_log_list as $v) {
            $param = array();
            $member = $model_member->getMemberInfoByID($v['member_id']);
            $receiver = $model_member->getMemberInfoByID($v['invite_id']);
            $param['operation'] = "----";
            $param['member_mobile'] = $member['member_mobile'];
            $param['member_name'] = $member['member_name'];
            $param['receiver_mobile'] = $receiver['member_mobile'];
            $param['receiver_name'] = $receiver['member_name'];
            $param['integral'] = abs($v['variable_integral']);
            $param['add_time'] = $v['add_time'] ? date('Y-m-d H:i:s', $v['add_time']) : '';
            $param['remarks'] = $v['remarks'];
            $data['list'][$v['id']] = $param;
        }
        echo Tpl::flexigridXML($data);
        exit();
    }

    /**
     * export_xlsOp 导出execl文件
     * @return [type] [description]
     */
    public function export_xlsOp()
    {
        $model_member_integral_log = Model('member_integral_log');
        $model_member = Model('member');
        $condition = array('type'=>5);
        $id = $_GET['id'];
        if ($id) {
            $condition['id'] = array('in', $id);
        } else {
            $this->_get_condition($condition);
        }
        $data = $model_member_integral_log->getMemberIntegralLogList($condition, '*', null, 'add_time desc', false);
        $excel_obj = new Excel();
        $excel_data = array();
        // 设置样式
        $excel_obj->setStyle(array('id' => 's_title', 'Font' => array('FontName' => '宋体', 'Size' => '12', 'Bold' => '1')));
        // header
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '转赠人手机');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '转赠人');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '接收人手机');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '接收人');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '数量');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '转赠时间');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '备注');
        foreach ((array)$data as $k => $v) {
            $tmp = array();
            $member = $model_member->getMemberInfoByID($v['member_id']);
            $receiver = $model_member->getMemberInfoByID($v['invite_id']);
            $tmp[] = array('data' =>$member['member_mobile']);
            $tmp[] = array('data' => $member['member_name']);
            $tmp[] = array('data' => $receiver['member_mobile']);
            $tmp[] = array('data' => $receiver['member_name']);
            $tmp[] = array('data' => abs($v['variable_integral']));
            $tmp[] = array('data' => $v['add_time'] ? date('Y-m-d H:i:s', $v['add_time']) : '');
            $tmp[] = array('data' => $v['remarks']);
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
                    $condition['member_id'] = array('in', $arr);
                } else {
                    $condition['member_id'] = null;
                }
            } else {
                $list = Model('member')->getMemberList(array('member_name' => array('like', '%' . $param['query'] . '%')));
                if (!empty($list)) {
                    $arr = array();
                    foreach ($list as $v) {
                        $arr[] = $v['member_id'];
                    }
                    $condition['member_id'] = array('in', $arr);
                } else {
                    $condition['member_id'] = null;
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