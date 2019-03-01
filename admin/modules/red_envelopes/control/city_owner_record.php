<?php
/**
 * 抢城主记录
 *
 */
defined('In33hao') or exit('Access Invild!');
class city_owner_recordControl extends SystemControl {


    public function __construct()
    {
        parent::__construct();
    }

    public function indexOp()
    {
        $this->city_owner_recordOp();
    }

    /**
     * 抢城主记录列表
     */
    public function city_owner_recordOp(){ 
        Tpl::setDirquna('red_envelopes');
        Tpl::showpage('redbag_robcity.list');
    }

    public function get_xmlOp()
    {
        $page = $_POST['rp'];
        $return = Model('red_robcity_list');
        $condition = array();
        $condition['state'] = 1;
        if ($_POST['query'] != '') {
            $condition[$_POST['qtype']] = array('like', '%' . $_POST['query'] . '%');
        }
        $return_list = $return->getCityExchangeRecord($condition,$page);
        $data = array();
        $data['now_page'] = $return->shownowpage();
        $data['total_num'] = $return->gettotalnum();
        foreach ($return_list as $v) {
            $addtime=date("Y-m-d H:i:s",$v['addtime']);
            $param = array();
            $param['order_number'] = $v['order_number'];
            $param['member_name_return'] = $v['member_name'];
            $param['member_mobile_return'] = $v['member_mobile'];
            $param['area_name'] = $v['province'].$v['city'].$v['area'];
            $param['time_of_ownership'] = $addtime."至".date("Y-m-d H:i:s",$v['endtime']?$v['endtime']:time());
            $param['mode'] = str_replace(array(0,1,2),array("微信支付","支付宝支付","余额支付"),$v['mode']);
            $param['addtime'] = $addtime;
            $param['endtime'] = date("Y-m-d H:i:s",$v['endtime']);
            $param['payment_number'] = $v['payment_number'];
            $data['list'][$v['id']] = $param;
        }
        Tpl::flexigridXML($data);
    }

    /**
     * 导出抢城主列表
     *
     */
    public function export_step1Op() {
        $return=Model('red_robcity_list');
        $condition['state'] = 1;
        $id = $_GET['id'];
        if ($id) {
            $condition['id'] = array('in', $id);
        }
        $return_list = $return->getCityExchangeRecord($condition);
        $this->createExcel($return_list);
    }

    /**
     * 生成导出抢城主列表excel
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
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '支付单号');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '城主姓名');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '城主手机');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '城主区域');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '所属时间段');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '支付方式');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '抢占时间');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '支付时间');

        foreach ((array) $data as $k => $v) {
            $tmp = array();
            $tmp[] = array('data' => $v['order_number']);
            $tmp[] = array('data' => $v['payment_number']);
            $tmp[] = array('data' => $v['member_name']);
            $tmp[] = array('data' => $v['member_mobile']);
            $tmp[] = array('data' => $v['province'].$v['city'].$v['area']);
            $tmp[] = array('data' => date("Y-m-d H:i:s",$v['addtime'])."-".date("Y-m-d H:i:s",$v['endtime']?$v['endtime']:time()));
            $tmp[] = array('data' => str_replace([0,1,2],["微信支付","支付宝支付","余额支付"],$v['mode']));
            $tmp[] = array('data' => date('Y-m-d H:i:s', $v['addtime']));
            $tmp[] = array('data' => date('Y-m-d H:i:s', $v['addtime']));
            $excel_data[] = $tmp;
        }
        $excel_data = $excel_obj->charset($excel_data, CHARSET);
        $excel_obj->addArray($excel_data);
        $excel_obj->addWorksheet($excel_obj->charset('抢城主记录', CHARSET));
        $excel_obj->generateXML($excel_obj->charset('抢城主记录', CHARSET) . $_GET['curpage'] . '-' . date('Y-m-d-H', time()));
    }

}