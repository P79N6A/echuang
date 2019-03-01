<?php
/**
 * 会员分润管理
 *
 */
defined('In33hao') or exit('Access Invild!');
class member_rebateControl extends SystemControl {


    public function __construct()
    {
        parent::__construct();
    }

    public function indexOp()
    {
        $this->member_rebateOp();
    }

    /**
     * 会员分润列表
     */
    public function member_rebateOp(){ 
        Tpl::setDirquna('red_envelopes');
        Tpl::showpage('redbag_distribution.list');
    }

    public function get_xmlOp()
    {
        $member=Model('member');
        $return=Model('red_share_benefits');
        if ($_POST['query'] != '') {
            $condition[$_POST['qtype']] = array('like', '%' . $_POST['query'] . '%');
        }
        $page = $_POST['rp'];
        $order = $_POST['sortname'] . ' ' . $_POST['sortorder'];
        $return_list = $return->getPdRechargeList($condition, $page, $fields = '*', $order, $limit = '');
        $data = array();
        $data['now_page'] = $return->shownowpage();
        $data['total_num'] = $return->gettotalnum();
        foreach ($return_list as $v) {
            $addtime=date("Y-m-d H:i:s",$v['addtime']);
            $param = array();
            $param['order_number'] = $v['order_number'];
            $param['red_envelopes_order_number'] = $v['red_envelopes_order_number'];
            $param['wo_member_mobile'] = $v['wo_member_mobile'];
            $param['money'] = $v['money'];
            $param['addtime'] = $addtime;
            if($v['state']==0){
                $param['we_member_mobile'] = '下级用户'.$v['me_member_mobile'].'发红包所得分润';
            }else{
                $param['we_member_mobile'] = '下级用户'.$v['me_member_mobile'].'抢红包所得分润';
            }
            $data['list'][$v['share_benefits_id']] = $param;
        }
        Tpl::flexigridXML($data);
    }

    /**
     * 导出会员分润
     *
     */
    public function export_step1Op() {
        $member=Model('');
        $return=Model('');
        $return_list=$return->getarray();
        foreach ($return_list as $key => $value) {
            $membde_mobile=$member->getMemberInfoByID($value['inviter_id']);
            $return_list[$key]['member_mobile_return']=$membde_mobile['member_mobile'];
        }
        $this->createExcel($return_list);
    }

    /**
     * 生成导出会员分润excel
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
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '金额');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '分润时间');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '备注');

        foreach ((array) $data as $k => $v) {
            if($v['state']==0){
                $we_member_mobile = '下级用户'.$v['me_member_mobile'].'发红包所得分润';
            }else{
                $we_member_mobile = '下级用户'.$v['me_member_mobile'].'抢红包所得分润';
            }
            $tmp = array();
            $tmp[] = array('data' => $v['order_number']);
            $tmp[] = array('data' => $v['red_envelopes_order_number']);
            $tmp[] = array('data' => $v['wo_member_mobile']);
            $tmp[] = array('data' => $v['money']);
            $tmp[] = array('data' => date('Y-m-d H:i:s', $v['addtime']));
            $tmp[] = array('data' => $we_member_mobile);
            $excel_data[] = $tmp;
        }
        $excel_data = $excel_obj->charset($excel_data, CHARSET);
        $excel_obj->addArray($excel_data);
        $excel_obj->addWorksheet($excel_obj->charset('会员分润列表', CHARSET));
        $excel_obj->generateXML($excel_obj->charset('会员分润列表', CHARSET) . $_GET['curpage'] . '-' . date('Y-m-d-H', time()));
    }

}