<?php
/**
 * 商家优恵劵记录
 *
 */
defined('In33hao') or exit('Access Invild!');
class business_securitiesControl extends SystemControl {


    public function __construct()
    {
        parent::__construct();
    }

    public function indexOp()
    {
        $this->business_securitiesOp();
    }

    /**
     * 商家优恵劵记录列表
     */
    public function business_securitiesOp(){
        Tpl::setDirquna('red_envelopes');
        Tpl::showpage('business_securities.list');
    }

    public function get_xmlOp()
    {
        $issue=Model('red_redbag_issue');
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
        $condition['state']='2';
        $condition['payment_state']='1';
        $condition['red_examine']='2';
        $condition['securities_state']='0';
        $upgrade_list = $issue->getPdRechargeList($condition,$page, $fields = '*', $order, $limit = '');
        $data = array();
        $data['now_page'] = 1;
        $data['total_num'] = count($upgrade_list);
        foreach ($upgrade_list as $v) {
            $redbag['red_envelopes_order_number']=$v['red_envelopes_order_number'];
            $redbag['receivestate']='1';
            $excellentsecurities=$issue->getexcellentsecurities($redbag);
            $count=count($excellentsecurities);
            $param = array();
            $param['operation'] = "<a class='btn blue' href='index.php?act=business_securities&op=see&id=" . $v['red_envelopes_id'] . "' onclick='fg_delete(".$v['red_envelopes_id'].")'><i class='fa fa-pencil-square-o'></i>查看</a><a class='btn blue' href='index.php?act=business_securities&op=del&id=" . $v['red_envelopes_id'] . "' onclick='fg_delete(".$v['red_envelopes_id'].")'><i class='fa fa-pencil-square-o'></i>删除</a>";
            $addtime=date("Y-m-d H:i:s",$v['red_envelopes_start_time']);
            $endtime=date("Y-m-d",$v['ter_of_validity']);
            $param['member_mobile'] = $v['member_mobile'];
            $param['member_name'] = $v['member_name'];
            $param['red_redeem_code'] = $v['red_redeem_code'];
            $param['red_title'] = $v['red_title'];
            $param['red_address'] = $v['red_address'];
            $param['red_discount'] = $v['red_discount'];
            $param['num'] = $v['red_envelopes_num'];
            $param['already_issued'] = $v['red_envelopes_already_issued'];
            $param['swap']=$count;
            $param['addtime'] = $addtime;
            $param['ter_of_validity'] = $endtime;
            $data['list'][$v['red_envelopes_id']] = $param;
        }
        Tpl::flexigridXML($data);
    }

    /*
     * 删除单个商家优恵劵
     */
    public function delOp(){
        $issue=Model('red_redbag_issue');
        $id['red_envelopes_id']=$_GET['id'];
        $data['securities_state']='1';
        $find=$issue->modify($id,$data);
        if($find){
            showMessage('删除成功', 'index.php?act=business_securities&op=index');
        }else{
            showMessage('删除失败', 'index.php?act=business_securities&op=index');
        }
    }
    /*
     * 查看商家优恵劵
     */
    public function seeOp(){
        $issue=Model('red_redbag_issue');
        $issue_rob=Model('red_rob_red_envelopes');
        $condition['red_envelopes_id']=$_GET['id'];
        $upgrade_list = $issue->getenvelopesfind($condition);
        $redbag['red_envelopes_order_number']=$upgrade_list['red_envelopes_order_number'];
        $redbag['receivestate']='1';
        $excellentsecurities=$issue->getexcellentsecurities($redbag);
        foreach ($excellentsecurities as $k=>$v){
            $data['red_envelopes_id']=$v['red_envelopes_id'];
            $data['order_number']=$v['order_number'];
            $rob=$issue_rob->getmanagementList($data);
            if($rob){
                $data_rob[]=$rob['member_mobile'];
                //$data_rob[$k]['member_name']=$rob['member_name'];
            }
        }
        $param = array();
        $addtime=date("Y-m-d H:i:s",$upgrade_list['addtime']);
        $endtime=date("Y-m-d",$upgrade_list['ter_of_validity']);
        $param['member_mobile'] = $upgrade_list['member_mobile'];
        $param['member_name'] = $upgrade_list['member_name'];
        $param['red_redeem_code'] =$upgrade_list['red_redeem_code'];
        $param['red_title'] = $upgrade_list['red_title'];
        $param['red_address'] = $upgrade_list['red_address'];
        $param['red_discount'] = $upgrade_list['red_discount'];
        $param['num'] = $upgrade_list['red_envelopes_num'];
        $param['already_issued'] = $upgrade_list['red_envelopes_already_issued'];
        $param['swap']=implode(',',$data_rob);
        $param['addtime'] = $addtime;
        $param['ter_of_validity'] = $endtime;
        Tpl::output('info', $param);
        Tpl::setDirquna('red_envelopes');
        Tpl::showpage('business_securities.see');
    }

    /**
     * 导出红包推送记录
     *
     */
    public function export_step1Op() {
        $record=Model('red_business');
        $return_list=$record->getPdRechargeList();
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
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '手机号码');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '店名');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '地址');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '电话');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '分类');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '申请时间');

        foreach ((array) $data as $k => $v) {
            $tmp = array();
            $tmp[] = array('data' => $v['member_mobile']);
            $tmp[] = array('data' => $v['member_name']);
            $tmp[] = array('data' => $v['title']);
            $tmp[] = array('data' => $v['address']);
            $tmp[] = array('data' => $v['mobile']);
            $tmp[] = array('data' => $classify);
            $tmp[] = array('data' => $v['content']);
            $tmp[] = array('data' => date('Y-m-d H:i:s', $v['addtime']));
            $excel_data[] = $tmp;
        }
        $excel_data = $excel_obj->charset($excel_data, CHARSET);
        $excel_obj->addArray($excel_data);
        $excel_obj->addWorksheet($excel_obj->charset('商家申请记录', CHARSET));
        $excel_obj->generateXML($excel_obj->charset('商家申请记录', CHARSET) . $_GET['curpage'] . '-' . date('Y-m-d-H', time()));
    }
    
}