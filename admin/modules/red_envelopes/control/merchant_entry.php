<?php
/**
 * 商家申请记录
 *
 */
defined('In33hao') or exit('Access Invild!');
class merchant_entryControl extends SystemControl {


    public function __construct()
    {
        parent::__construct();
    }

    public function indexOp()
    {
        $this->merchant_entryOp();
    }

    /**
     * 商家申请记录列表
     */
    public function merchant_entryOp(){
        Tpl::setDirquna('red_envelopes');
        Tpl::showpage('merchant_entry.list');
    }

    public function get_xmlOp()
    {
        $business=Model('red_business');
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
        $upgrade_list = $business->getPdRechargeList($condition, $page, $fields = '*', $order, $limit = '');
        $data = array();
        $data['now_page'] = 1;
        $data['total_num'] = count($upgrade_list);
        foreach ($upgrade_list as $v) {
            $param = array();
            if ($v['state'] == 0) {
                $param['operation'] = "<a class='btn blue' href='index.php?act=merchant_entry&op=entry&id=" . $v['business_id'] . "&state=2'><i class='fa fa-pencil-square-o'></i>通过</a><a class='btn blue' href='index.php?act=merchant_entry&op=entry&id=" . $v['business_id'] . "&state=1'><i class='fa fa-pencil-square-o'></i>拒绝</a><a class='btn blue' href='index.php?act=merchant_entry&op=see&id=" . $v['business_id'] . "'><i class='fa fa-pencil-square-o'></i>编辑</a><a class='btn blue' href='index.php?act=merchant_entry&op=del&id=" . $v['business_id'] . "' onclick='fg_delete(".$v['business_id'].")'><i class='fa fa-pencil-square-o'></i>删除</a>";
            } else {
                if ($v['state'] == 2) {
                    $param['operation'] = "<span style='color: #3AA55A;'>已审核</span><a class='btn blue' href='index.php?act=merchant_entry&op=see&id=" . $v['business_id'] . "'><i class='fa fa-pencil-square-o'></i>编辑</a><a class='btn blue' href='index.php?act=merchant_entry&op=del&id=" . $v['business_id'] . "' onclick='fg_delete(".$v['business_id'].")'><i class='fa fa-pencil-square-o'></i>删除</a>";
                } else {
                    $param['operation'] = "<span style='color: #ff3c00;'>已拒绝</span><a class='btn blue' href='index.php?act=merchant_entry&op=see&id=" . $v['business_id'] . "'><i class='fa fa-pencil-square-o'></i>编辑</a><a class='btn blue' href='index.php?act=merchant_entry&op=del&id=" . $v['business_id'] . "' onclick='fg_delete(".$v['business_id'].")'><i class='fa fa-pencil-square-o'></i>删除</a>";
                }
            }
            $addtime=date("Y-m-d H:i:s",$v['addtime']);
            $param['member_mobile'] = $v['member_mobile'];
            $param['member_name'] = $v['member_name'];
            $param['title'] = $v['title'];
            $param['address'] = $v['address'];
            $param['mobile'] = $v['mobile'];
            switch ($v['classify']) {
                case '1':
                    $param['classify'] = '衣';
                    break;
                case '2':
                    $param['classify'] = '吃';
                    break;
                case '3':
                    $param['classify'] = '住';
                    break;
                case '4':
                    $param['classify'] = '行';
                    break;
                default:
                    $param['classify'] = '';
            }
            $param['content'] = $v['content'];
            $param['licence'] = "<a href='javascript:void(0);' class='pic-thumb-tip' onMouseOut='toolTip()' onMouseOver='toolTip(\"<img src=".UPLOAD_SITE_URL.DS.ATTACH_MOBILE.DS.$v['licence'].">\")'><i class='fa fa-picture-o'></i></a>";
            $param['store'] = "<a href='javascript:void(0);' class='pic-thumb-tip' onMouseOut='toolTip()' onMouseOver='toolTip(\"<img src=".UPLOAD_SITE_URL.DS.ATTACH_MOBILE.DS.$v['store'].">\")'><i class='fa fa-picture-o'></i></a>";
            $param['addtime'] = $addtime;
            $data['list'][$v['business_id']] = $param;
        }
        Tpl::flexigridXML($data);
    }

    /*
     * 商店审核
     */
    public function entryOp(){
        $id=$_GET['id'];
        $where['state']=$_GET['state'];
        $business=Model('red_business');
        $data=$business->businesssupdates($where,$id);
        if($data){
            showMessage('执行成功', 'index.php?act=merchant_entry&op=index');
        }else{
            showMessage('执行失败', 'index.php?act=merchant_entry&op=index');
        }
    }
    /*
     * 删除单个商家
     */
    public function delOp(){
        $business=Model('red_business');
        $business_id=$_GET['id'];
        $find=$business->del($business_id);
        if($find){
            showMessage('删除成功', 'index.php?act=merchant_entry&op=index');
        }else{
            showMessage('删除失败', 'index.php?act=merchant_entry&op=index');
        }
    }
    /*
     * 查看商店
     */
    public function seeOp(){
        $business=Model('red_business');
        $where['business_id']=$_GET['id'];
        $businesssadd=$business->get_businesss_find($where);
        if($businesssadd) {
            $data['licence'] = UPLOAD_SITE_URL . DS . ATTACH_MOBILE . DS . $businesssadd['licence'];
            $data['store'] = UPLOAD_SITE_URL . DS . ATTACH_MOBILE . DS . $businesssadd['store'];
            $data['member_avatar'] = BASE_SITE_URL . "/data/uploadop/avatar/" . $businesssadd['member_avatar'];
            $data['title'] = $businesssadd['title'];
            $data['address'] = $businesssadd['address'];
            $data['mobile'] = $businesssadd['mobile'];
            switch ($businesssadd['classify']) {
                case '1':
                    $data['classify'] = '衣';
                    break;
                case '2':
                    $data['classify'] = '吃';
                    break;
                case '3':
                    $data['classify'] = '住';
                    break;
                case '4':
                    $data['classify'] = '行';
                    break;
                default:
                    $data['classify'] = '';
            }
            $data['content'] = $businesssadd['content'];
            $data['member_mobile'] = $businesssadd['member_mobile'];
            $data['member_name'] = $businesssadd['member_name'];
        }
        if($_POST){
            if (!empty($_FILES['licence']['name'])) {
                $upload = new UploadFile();
                $upload->set('default_dir', ATTACH_MOBILE);
                $result = $upload->upfile('licence');
                if ($result) {
                    $_POST['licence'] = $upload->file_name;
                } else {
                    showMessage($upload->error, '', '', 'error');
                }
            }
            if (!empty($_FILES['store']['name'])) {
                $upload = new UploadFile();
                $upload->set('default_dir', ATTACH_MOBILE);
                $result = $upload->upfile('store');
                if ($result) {
                    $_POST['store'] = $upload->file_name;
                } else {
                    showMessage($upload->error, '', '', 'error');
                }
            }
            $update_array = array();
            if (!empty($_POST['licence'])){
                $update_array['licence'] = $_POST['licence'];
            }
            if (!empty($_POST['store'])){
                $update_array['store'] = $_POST['store'];
            }
            $update_array['title']=$_POST['title'];
            $update_array['address']=$_POST['address'];
            $update_array['mobile']=$_POST['mobile'];
            $update_array['content']=$_POST['content'];
            $result=$business->businesssupdates($update_array,$where['business_id']);
            if($result){
                showMessage('执行成功', 'index.php?act=merchant_entry&op=index');
            }else{
                showMessage('执行失败', 'index.php?act=merchant_entry&op=index');
            }
        }
        Tpl::output('info', $data);
        Tpl::setDirquna('red_envelopes');
        Tpl::showpage('merchant_entry.see');
    }

    /*
     * 使用手机号码注册商家
     */
    public function check_memberOp()
    {
        $where['member_mobile'] = trim($_GET['mobile']);
        if ($where['member_mobile']) {
            $business=Model('red_business');
            $model_member= Model('member');
            $info = $model_member->getMemberInfo($where);
            $where['state']='2';
            $data_business=$business->get_businesss_find($where);
            if (!empty($info) && $data_business==false) {
                $data['name']=$info['member_name'];
                $data['id']=$info['member_id'];
                $data['mobile']=$info['member_mobile'];
                echo json_encode($data);
            } else {
                echo "401";
            }
        } else {
            echo "0";
        }
    }

    /*
     * 申请成为商家
     */
    public function advertising_pushOp(){
        $business=Model('red_business');
        if($_POST){
            $where['member_mobile'] =$_POST['member_mobile'];
            $data_business=$business->get_businesss_find($where);
            if($data_business){
                if (!empty($_FILES['licence']['name'])) {
                    $upload = new UploadFile();
                    $upload->set('default_dir', ATTACH_MOBILE);
                    $result = $upload->upfile('licence');
                    if ($result) {
                        $_POST['licence'] = $upload->file_name;
                    } else {
                        showMessage($upload->error, '', '', 'error');
                    }
                }
                if (!empty($_FILES['store']['name'])) {
                    $upload = new UploadFile();
                    $upload->set('default_dir', ATTACH_MOBILE);
                    $result = $upload->upfile('store');
                    if ($result) {
                        $_POST['store'] = $upload->file_name;
                    } else {
                        showMessage($upload->error, '', '', 'error');
                    }
                }
                $update_array = array();
                if (!empty($_POST['licence'])){
                    $update_array['licence'] = $_POST['licence'];
                }
                if (!empty($_POST['store'])){
                    $update_array['store'] = $_POST['store'];
                }
                $update_array['title']=$_POST['title'];
                $update_array['address']=$_POST['address'];
                $update_array['mobile']=$_POST['mobile'];
                $update_array['content']=$_POST['content'];
                $update_array['member_id']=$_POST['member_id'];
                $result=$business->businesssadd($update_array);
                if($result){
                    showMessage('执行成功', 'index.php?act=merchant_entry&op=index');
                }else{
                    showMessage('执行失败', 'index.php?act=merchant_entry&op=index');
                }
            }else{
                showMessage('会员已注册商家', 'index.php?act=merchant_entry&op=index');
            }

        }
        Tpl::setDirquna('red_envelopes');
        Tpl::showpage('merchant_entry.add');
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
            switch ($v['classify']) {
                case '1':
                    $classify = '衣';
                    break;
                case '2':
                    $classify = '吃';
                    break;
                case '3':
                    $classify = '住';
                    break;
                case '4':
                    $classify = '行';
                    break;
                default:
                    $classify = '';
            }
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