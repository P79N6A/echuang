<?php
/**
 * 抢红包记录
 *
 */
defined('In33hao') or exit('Access Invild!');
class redbag_manageControl extends SystemControl {


    public function __construct()
    {
        parent::__construct();
    }

    public function indexOp()
    {
        $this->redbag_manageOp();
    }

    /**
     * 抢红包记录列表
     */
    public function redbag_manageOp(){
        Tpl::setDirquna('red_envelopes');
        Tpl::showpage('redbag_management.list');
    }

    public function get_xmlOp()
    {
        // print_r($_REQUEST);die();
        $manage=Model('red_redbag_manage');
        $data = array();
        if ($_POST['query'] != '') {
            $condition[$_POST['qtype']] = array('like', '%' . $_POST['query'] . '%');
        }
        $page = $_POST['rp'];
        $return_list = $manage->getPdRechargeList($condition, $page, $fields = '*', $order = '', $limit = '');
        $data = array();
        $data['now_page'] = $manage->shownowpage();
        $data['total_num'] = $manage->gettotalnum();
        foreach ($return_list as $v) {
            if($v['unissued']){
                $unissued=$v['unissued'];
            }else{
                $unissued=$v['red_envelopes_unissued'];
            }
            $addtime=date("Y-m-d H:i:s",$v['rob_addtime']);
            $param = array();
            $param['red_envelopes_order_number'] = $v['order_number'];
            $param['order_number'] = $v['red_envelopes_order_number'];
            $param['member_mobile'] = $v['member_mobile'];
            $param['money'] = $v['money'];
            $param['num'] = $unissued.'/'.$v['red_envelopes_num'];
            $param['red_envelopes_area'] = $v['red_envelopes_area'];
            $param['addtime'] = $addtime;
            $data['list'][$v['id']] = $param;
        }
        Tpl::flexigridXML($data);
    }

    /**
     * 导出抢红包
     *
     */
    public function export_step1Op() {
        $manage=Model('red_redbag_manage');
        $return_list = $manage->getPdRechargeList($condition, $pagesize = '', $fields = '*', $order = '', $limit = '');
        $this->createExcel($return_list);
    }

    /**
     * 生成导出抢红包excel
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
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '订单号');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '红包单号');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '用户手机');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '红包金额');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '红包余数');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '定位范围');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '抢红包时间');

        foreach ((array) $data as $k => $v) {
            $tmp = array();
            $tmp[] = array('data' => $v['red_envelopes_order_number']);
            $tmp[] = array('data' => $v['order_number']);
            $tmp[] = array('data' => $v['member_mobile_return']);
            $tmp[] = array('data' => $v['member_mobile']);
            $tmp[] = array('data' => $v['money']);
            $tmp[] = array('data' => $v['red_envelopes_unissued'].'/'.$v['red_envelopes_num']);
            $tmp[] = array('data' => date('Y-m-d H:i:s', $v['addtime']));
            $excel_data[] = $tmp;
        }
        $excel_data = $excel_obj->charset($excel_data, CHARSET);
        $excel_obj->addArray($excel_data);
        $excel_obj->addWorksheet($excel_obj->charset('抢红包列表', CHARSET));
        $excel_obj->generateXML($excel_obj->charset('抢红包列表', CHARSET) . $_GET['curpage'] . '-' . date('Y-m-d-H', time()));
    }

}