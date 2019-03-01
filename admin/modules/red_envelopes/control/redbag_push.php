<?php
/**
 * 红包推送记录
 *
 */
defined('In33hao') or exit('Access Invild!');
class redbag_pushControl extends SystemControl {


    public function __construct()
    {
        parent::__construct();
    }

    public function indexOp()
    {
        $this->redbag_pushOp();
    }

    /**
     * 红包推送记录列表
     */
    public function redbag_pushOp(){
        Tpl::setDirquna('red_envelopes');
        Tpl::showpage('red_redbag_push.list');
    }

    public function get_xmlOp()
    {   
        $record=Model('red_redbag_push');
        $data = array();
        if ($_GET['member_name']) {
            $condition['member_name'] = array('like', '%' . $_GET['member_name'] . '%');
        }
        if ($_GET['member_mobile']) {
            $condition['member_mobile'] = array('like', '%' . $_GET['member_mobile'] . '%');
        }
        if ($_GET['order_number']) {
            $condition['order_number'] = $_GET['order_number'] ;
        }
        if ($_POST['query'] != '') {
            $condition[$_POST['qtype']] = array('like', '%' . $_POST['query'] . '%');
        }
        $page = $_POST['rp'];
        $order = $_POST['sortname'] . ' ' . $_POST['sortorder'];
        $upgrade_list = $record->getPdRechargeList($condition, $page, $fields = '*', $order, $limit = '');
        $data = array();
        $data['now_page'] = $record->shownowpage();
        $data['total_num'] = $record->gettotalnum();
        foreach ($upgrade_list as $v) {
            $addtime=date("Y-m-d H:i:s",$v['addtime']);
            $overduetime=date("Y-m-d H:i:s",$v['overduetime']);
            $param = array();
            $param['member_name'] = $v['member_name'];
            $param['member_mobile'] = $v['member_mobile'];
            $param['data'] = implode(",", unserialize($v['data']));
            $param['num'] = $v['num'];
            $param['overduetime'] = $overduetime;
            $param['addtime'] = $addtime;
            $data['list'][$v['member_id']] = $param;
        }
        Tpl::flexigridXML($data);
    }

    /**
     * 导出红包推送记录
     *
     */
    public function export_step1Op() {
        $record=Model('red_expand_area_record');
        $return_list=$record->getselect();
        $this->createExcel($return_list);
    }

    /**
     * 生成导出红包推送记录excel
     *
     * @param array $data
     */
    private function createExcel($data = array()) {
        Language::read('export');
        import('libraries.excel');
        $excel_obj = new Excel();
        $excel_data = array();
        // 设置样式
        $excel_obj->setStyle(array('id' => 's_title', 'Font' => array('FontName' => '宋体', 'Size' => '12', 'Bold' => '1')));
        // header
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '用户名称');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '用户手机');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '成功分享用户数量');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '扩大范围（公里）');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '增加红包数');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '时间');

        foreach ((array) $data as $k => $v) {
            $tmp = array();
            $tmp[] = array('data' => $v['member_name']);
            $tmp[] = array('data' => $v['member_mobile']);
            $tmp[] = array('data' => $v['user_num']);
            $tmp[] = array('data' => $v['extended_range_num']);
            $tmp[] = array('data' => $v['add_redbag_num']);
            $tmp[] = array('data' => date('Y-m-d H:i:s', $v['addtime']));
            $excel_data[] = $tmp;
        }
        $excel_data = $excel_obj->charset($excel_data, CHARSET);
        $excel_obj->addArray($excel_data);
        $excel_obj->addWorksheet($excel_obj->charset('扩大范围记录', CHARSET));
        $excel_obj->generateXML($excel_obj->charset('扩大范围记录', CHARSET) . $_GET['curpage'] . '-' . date('Y-m-d-H', time()));
    }

}