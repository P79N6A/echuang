<?php
/**
 * 会员升级返现
 *
 */
defined('In33hao') or exit('Access Invild!');
class upgrade_returnControl extends SystemControl {


    public function __construct()
    {
        parent::__construct();
    }

    public function indexOp()
    {
        $this->upgrade_returnOp();
    }

    /**
     * 会员升级返现例
     */
    public function upgrade_returnOp(){
        // $return=Model('red_upgrade_return');
        // $_POST['query']='redbag';
        // $where['member_name'] = array('like', '%' . $_POST['query'] . '%');
        // $membdefind=$return->getwhere($where);
        // if($membdefind!=false){
        //     foreach ($membdefind as $key => $value) {
        //         $condition['inviter_id']=$value['member_id'];
        //         $list[] = $return->getPdRechargeList($condition, $pagesize = '', $fields = '*', $order = '', $limit = '');
        //     }
        //     $list=array_filter($list);
        //     foreach ($list as $key =>$v){
        //         $return_list[]=$v[0];
        //     }
        // }
        // var_dump($return_list);die;
        Tpl::setDirquna('red_envelopes');
        Tpl::showpage('redbag_return.list');
    }

    public function get_xmlOp()
    {
        $page = $_POST['rp'];
        $order = $_POST['sortname'] . ' ' . $_POST['sortorder'];
         // print_r($_REQUEST);die();
        $member=Model('member');
        $return=Model('red_upgrade_return');
        $data = array();
        if ($_POST['query'] != '') {
            $condition[$_POST['qtype']] = array('like', '%' . $_POST['query'] . '%');
        }
        if($_POST['qtype']=='member_name_return' ||$_POST['qtype']=='member_mobile_return'){
            if($_POST['qtype']=='member_name_return'){
                $name='member_name';
            }else{
                $name='member_mobile';
            }
            $where[$name] = array('like', '%' . $_POST['query'] . '%');
            $membdefind=$return->getwhere($where);
            if($membdefind!=false){
                foreach ($membdefind as $key => $value) {
                    $returncondition['inviter_id']=$value['member_id'];
                    $list[] = $return->getPdRechargeList($returncondition, $page, $fields = '*', $order, $limit = '');
                }
                $list=array_filter($list);
                foreach ($list as $key =>$v){
                    $return_list[]=$v[0];
                }
            }
        }else{
            $return_list = $return->getPdRechargeList($condition, $page, $fields = '*', $order, $limit = '');
        }
        $data = array();
        $data['now_page'] = 1;
        $data['total_num'] = count($return_list);
        foreach ($return_list as $v) {
            $membde_mobile=$member->getMemberInfoByID($v['inviter_id']);
            $addtime=date("Y-m-d H:i:s",$v['addtime']);
            $param = array();
            $param['order_number'] = $v['order_number'];
            $param['member_name_return'] = $membde_mobile['member_name'];
            $param['member_mobile_return'] = $membde_mobile['member_mobile'];
            $param['member_mobile'] = $v['member_mobile'];
            $param['money'] = $v['money'];
            $param['addtime'] = $addtime;
            $data['list'][$v['upgrade_member_id']] = $param;
        }
        Tpl::flexigridXML($data);
    }

    /**
     * 导出会员升级返现
     *
     */
    public function export_step1Op() {
        $member=Model('member');
        $return=Model('red_upgrade_return');
        $return_list=$return->getarray();
        foreach ($return_list as $key => $value) {
           $membde_mobile=$member->getMemberInfoByID($value['inviter_id']);
           $return_list[$key]['member_name_return']=$membde_mobile['member_name'];
           $return_list[$key]['member_mobile_return']=$membde_mobile['member_mobile'];
        }
        $this->createExcel($return_list);
    }

    /**
     * 生成导出会员升级返现excel
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
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '用户名称');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '用户手机');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '升级用户');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '金额（元）');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '时间');

        foreach ((array) $data as $k => $v) {
            $tmp = array();
            $tmp[] = array('data' => $v['order_number']);
            $tmp[] = array('data' => $v['member_name_return']);
            $tmp[] = array('data' => $v['member_mobile_return']);
            $tmp[] = array('data' => $v['member_mobile']);
            $tmp[] = array('format' => 'Number', 'data' => ncPriceFormat($v['money']));
            $tmp[] = array('data' => date('Y-m-d H:i:s', $v['addtime']));
            $excel_data[] = $tmp;
        }
        $excel_data = $excel_obj->charset($excel_data, CHARSET);
        $excel_obj->addArray($excel_data);
        $excel_obj->addWorksheet($excel_obj->charset('升级返现', CHARSET));
        $excel_obj->generateXML($excel_obj->charset('升级返现', CHARSET) . $_GET['curpage'] . '-' . date('Y-m-d-H', time()));
    }

}