<?php
/**
 * 会员升级
 *
 */
defined('In33hao') or exit('Access Invild!');
class member_upgradeControl extends SystemControl {


    public function __construct()
    {
        parent::__construct();
    }

    public function indexOp()
    {
        $this->member_upgradeOp();
    }

    /**
     * 会员升级例
     */
    public function member_upgradeOp(){
        Tpl::setDirquna('red_envelopes');
        Tpl::showpage('redbag_upgrade.list');
    }

    public function get_xmlOp()
    {
        $upgrade=Model('red_member_upgrade');
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
        $upgrade_list = $upgrade->getPdRechargeList($condition,$page, $fields = '*', $order, $limit = '');
        $data = array();
        $data['now_page'] = 1;
        $data['total_num'] = count($upgrade_list);
        foreach ($upgrade_list as $v) {
            if($v['state']==1){
                $state='已支付';
            }else{
                $state='未支付';
            }
            $addtime=date("Y-m-d H:i:s",$v['addtime']);
            $param = array();
            // $param['operation'] = "<a class='btn blue' href='index.php?act=member_upgrade&op=member_upgradeupdates&upgrade_member_id=" . $v['upgrade_member_id'] . "'><i class='fa fa-pencil-square-o'></i>编辑</a><a class='btn red' onclick='fg_delete(".$v['upgrade_membert_id'].")'><i class='fa fa-trash-o'></i>删除</a>";
            $param['order_number'] = $v['order_number'];
            $param['payment_number'] = $v['payment_number'];
            $param['member_name'] = $v['member_name'];
            $param['member_mobile'] = $v['member_mobile'];
            $param['money'] = $v['money'];
            $param['state'] = $state;
            $param['mode'] = $v['mode'];
            $param['addtime'] = $addtime;
            $data['list'][$v['upgrade_member_id']] = $param;
        }
        Tpl::flexigridXML($data);
    }

    /**
     * 导出会员升级
     *
     */
    public function export_step1Op() {
        $upgrade=Model('red_member_upgrade');
        $upgrade_list=$upgrade->getarray();
        foreach ($upgrade_list as $key => $value) {
            $upgrade_list[$key]['state']= $value['state']=='0' ? '未支付' : '已支付';
        }
        $this->createExcel($upgrade_list);
    }

    /**
     * 生成导出会员升级excel
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
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '用户名称');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '用户手机');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '金额（元）');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '支付状态');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '支付方式');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '支付时间');

        foreach ((array) $data as $k => $v) {
            $tmp = array();
            $tmp[] = array('data' => $v['order_number']);
            $tmp[] = array('data' => $v['payment_number']);
            $tmp[] = array('data' => $v['member_name']);
            $tmp[] = array('data' => $v['member_mobile']);
            $tmp[] = array('format' => 'Number', 'data' => ncPriceFormat($v['money']));
            $tmp[] = array('data' => $v['state']);
            $tmp[] = array('data' => $v['mode']);
            $tmp[] = array('data' => date('Y-m-d H:i:s', $v['addtime']));
            $excel_data[] = $tmp;
        }
        $excel_data = $excel_obj->charset($excel_data, CHARSET);
        $excel_obj->addArray($excel_data);
        $excel_obj->addWorksheet($excel_obj->charset('会员升级', CHARSET));
        $excel_obj->generateXML($excel_obj->charset('会员升级', CHARSET) . $_GET['curpage'] . '-' . date('Y-m-d-H', time()));
    }

}