<?php
/**
 * 用户领取优恵劵记录
 *
 */
defined('In33hao') or exit('Access Invild!');
class securitiesControl extends SystemControl {


    public function __construct()
    {
        parent::__construct();
    }

    public function indexOp()
    {
        $this->securitiesOp();
    }

    /**
     * 商家申请记录列表
     */
    public function securitiesOp(){
        Tpl::setDirquna('red_envelopes');
        Tpl::showpage('securities.list');
    }

    public function get_xmlOp()
    {
        $issue=Model('red_redbag_issue');
        $manage=Model('red_redbag_manage');
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
        $condition['red_examine']='2';
        $condition['rob_state']='0';
        $upgrade_list = $manage->getPdRechargeList($condition,$page, $fields = '*', $order, $limit = '');
        $data = array();
        $data['now_page'] = 1;
        $data['total_num'] = count($upgrade_list);
        foreach ($upgrade_list as $v) {
            $where_mini['order_number']=$v['order_number'];
            $getexcel=$issue->getexcel($where_mini);
            $param = array();
            if ($v['state'] == 0) {
                $param['operation'] = "<a class='btn blue' href='index.php?act=securities&op=del&id=" . $v['id'] . "' onclick='fg_delete(".$v['id'].")'><i class='fa fa-pencil-square-o'></i>删除</a>";
            }
            $addtime=date("Y-m-d H:i:s",$v['red_envelopes_start_time']);
            $endtime=date("Y-m-d",$v['ter_of_validity']);
            $param['member_mobile'] = $v['member_mobile'];
            $param['member_name'] = $v['member_name'];
            $param['red_redeem_code'] = $v['red_redeem_code'];
            $param['red_title'] = $v['red_title'];
            $param['red_address'] = $v['red_address'];
            $param['red_discount'] = $v['red_discount'];
            if($getexcel['receivestate']=='1'){
                $param['receivestate']='已领取';
            }else{
                $param['receivestate']='可兑换';
            }
            $param['addtime'] = $addtime;
            $param['ter_of_validity'] = $endtime;
            $data['list'][$v['id']] = $param;
        }
        Tpl::flexigridXML($data);
    }

    /*
     * 删除单个用户优恵劵
     */
    public function delOp(){
        $issue=Model('red_rob_red_envelopes');
        $id['rob_red_envelopes_id']=$_GET['id'];
        $data['state']='1';
        $find=$issue->del($data,$id);
        if($find){
            showMessage('删除成功', 'index.php?act=securities&op=index');
        }else{
            showMessage('删除失败', 'index.php?act=securities&op=index');
        }
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
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '兑换码');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '标题');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '地址');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '折扣');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '使用');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '添加时间');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '到期时间');

        foreach ((array) $data as $k => $v) {
            if($v['receivestate']==1){
                $receivestate='已领取';
            }else{
                $receivestate='可兑换';
            }
            $tmp = array();
            $tmp[] = array('data' => $v['member_mobile']);
            $tmp[] = array('data' => $v['member_name']);
            $tmp[] = array('data' => $v['red_redeem_code']);
            $tmp[] = array('data' => $v['red_title']);
            $tmp[] = array('data' => $v['red_address']);
            $tmp[] = array('data' => $v['red_discount']);
            $tmp[] = array('data' => $receivestate);
            $tmp[] = array('data' => date('Y-m-d H:i:s', $v['addtime']));
            $tmp[] = array('data' => date('Y-m-d H:i:s', $v['ter_of_validity']));
            $excel_data[] = $tmp;
        }
        $excel_data = $excel_obj->charset($excel_data, CHARSET);
        $excel_obj->addArray($excel_data);
        $excel_obj->addWorksheet($excel_obj->charset('用户优恵劵记录', CHARSET));
        $excel_obj->generateXML($excel_obj->charset('用户优恵劵记录', CHARSET) . $_GET['curpage'] . '-' . date('Y-m-d-H', time()));
    }
    
}