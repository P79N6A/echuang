<?php
/**
 * 城主记录
 *
 */
defined('In33hao') or exit('Access Invild!');
class city_owner_manageControl extends SystemControl {


    public function __construct()
    {
        parent::__construct();
    }

    public function indexOp()
    {
        $this->city_owner_manageOp();
    }

    /**
     * 城主记录列表
     */
    public function city_owner_manageOp(){
        Tpl::setDirquna('red_envelopes');
        Tpl::showpage('redbag_city.list');
    }

    public function get_xmlOp()
    {
        $page = $_POST['rp'];
        $member=Model('member');
        $data = array();

        //获取条件$condition
        $condition = array();
        if ($_POST['query'] != '') {
            $condition[$_POST['qtype']] = array('like', '%' . $_POST['query'] . '%');
        }
        if ($_REQUEST['keyword_type'] && $_REQUEST['keyword']) {
            $condition[$_REQUEST['keyword_type']] = array('like', '%' . $_REQUEST['keyword'] . '%');
        }
        if ($_REQUEST['query_start_date'] && $_REQUEST['query_end_date'] && strtotime($_REQUEST['query_start_date']) <= strtotime($_REQUEST['query_end_date'])) {
            $condition['addtime'] = array('between', array(strtotime($_REQUEST['query_start_date']), strtotime("+1 day", strtotime($_REQUEST['query_end_date'])) - 1));
        } elseif ($_REQUEST['query_start_date']) {
            $condition['addtime'] = array('egt', strtotime($_REQUEST['query_start_date']));
        } elseif ($_REQUEST['query_end_date']) {
            $condition['addtime'] = array('elt', strtotime("+1 day", strtotime($_REQUEST['query_end_date'])) - 1);
        }
        //查询数据
        $return_list = Model()->table('city_member')->where($condition)->page($page)->select();
        $data['now_page'] = $member->shownowpage();
        $data['total_num'] = $member->gettotalnum();
        foreach ($return_list as $v) {
            $addtime=date("Y-m-d H:i:s",$v['addtime']);
            $param = array();
            $param['order_number'] = $v['order_number'];
            $param['member_name_return'] = $v['member_name'];
            $param['member_mobile_return'] = $v['member_mobile'];
            $param['area_name'] = $v['area_name'].$v['city_name'].$v['province_name'];
            $param['time_of_ownership'] = $addtime."至".date("Y-m-d H:i:s",$v['endtime']?$v['endtime']:time());
            $param['addtime'] = $addtime;
            $data['list'][$v['city_lord_id']] = $param;
        }
        Tpl::flexigridXML($data);
    }

    /**
     * 导出城主
     *
     */
    public function export_step1Op() {
        $return=Model('red_city_owner_manage');
        $condition = array();
        $id = $_GET['id'];
        if ($id) {
            $condition['member_id'] = array('in', $id);
        }
        $return_list = Model()->table('city_lord')->where($condition)->select();
        $this->createExcel($return_list);
    }

    /**
     * 生成导出城主excel
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
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '单号');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '城主名称');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '城主手机');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '城主区域');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '所属时间段');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '抢占时间');

        foreach ((array) $data as $k => $v) {
            $tmp = array();
            $addtime=date("Y-m-d H:i:s",$v['addtime']);
            $membde_mobile=Model('member')->getMemberInfoByID($v['member_id']);
            $tmp[] = array('data' => $v['order_number']);
            $tmp[] = array('data' => $membde_mobile['member_name']);
            $tmp[] = array('data' => $membde_mobile['member_mobile']);
            $area = Model()->table('area')->where(['area_id'=>$v['area']])->find();
            $city = Model()->table('area')->where(['area_id'=>$area['area_parent_id']])->find();
            $province = Model()->table('area')->where(['area_id'=>$city['area_parent_id']])->find();
            $tmp[] = array('data' => $province['area_name'].$city['area_name'].$area['area_name']);
            $tmp[] = array('data' => $addtime."至".date("Y-m-d H:i:s",$v['endtime']?$v['endtime']:time()));
            $tmp[] = array('data' => $addtime);
            $excel_data[] = $tmp;
        }
        $excel_data = $excel_obj->charset($excel_data, CHARSET);
        $excel_obj->addArray($excel_data);
        $excel_obj->addWorksheet($excel_obj->charset('城主管理', CHARSET));
        $excel_obj->generateXML($excel_obj->charset('城主管理', CHARSET) . $_GET['curpage'] . '-' . date('Y-m-d-H', time()));
    }

}