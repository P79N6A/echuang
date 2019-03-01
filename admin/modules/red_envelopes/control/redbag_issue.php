<?php
/**
 * 红包审核记录
 *
 */
defined('In33hao') or exit('Access Invild!');

class redbag_issueControl extends SystemControl
{


    public function __construct()
    {
        parent::__construct();
    }

    public function indexOp()
    {
        $this->redbag_manageOp();
    }

    /**
     * 红包审核记录列表
     */
    public function redbag_manageOp()
    {
        // $where['red_envelopes_id']=30;
        // $remainder=$this->remainderOp($where);
        // var_dump($remainder);die;
        Tpl::setDirquna('red_envelopes');
        Tpl::showpage('redbag_examine.list');
    }

    public function get_xmlOp()
    {
        // print_r($_POST);die();
        // if ($_POST['sortname'] && $_POST['sortorder']) {
        //     $order = $_POST['sortname'] . ' ' . $_POST['sortorder'];
        // }
        // if ($_POST['curpage']) {
        //     $limit = $_POST['curpage'];
        // }
        $page = $_POST['rp'];
        $order = $_POST['sortname'] . ' ' . $_POST['sortorder'];
        $return = Model('red_redbag_issue');
        $data = array();
        if ($_POST['query'] != '') {
            $condition[$_POST['qtype']] = array('like', '%' . $_POST['query'] . '%');
        }
        if ($_POST['qtype'] == 'member_name_return' || $_POST['qtype'] == 'member_mobile_return') {
            if ($_POST['qtype'] == 'member_name_return') {
                $name = 'member_name';
            } else {
                $name = 'member_mobile';
            }
            $where[$name] = array('like', '%' . $_POST['query'] . '%');
            $membdefind = $return->getwhere($where);
            if ($membdefind != false) {
                foreach ($membdefind as $key => $value) {
                    $returncondition['inviter_id'] = $value['member_id'];
                    $list[] = $return->getPdRechargeList($returncondition, $page, $fields = '*', $order, $limit = '');
                }
                $list = array_filter($list);
                foreach ($list as $key => $v) {
                    $return_list[] = $v[0];
                }
            }
        } else {
            $return_list = $return->getPdRechargeList($condition, $page, $fields = '*', $order, $limit = '');
        }
        $data = array();
        $data['now_page'] = $return->shownowpage();
        $data['total_num'] = $return->gettotalnum();
        foreach ($return_list as $v) {
            if($v['red_envelopes_gender']==0){
                $gender='不限';
            }elseif($v['red_envelopes_gender']==1){
                $gender='男';
            }else{
                $gender='女';
            }
            if($v['red_envelopes_age_id']){
                $where['age_id']=$v['red_envelopes_age_id'];
                $age=Model()->table('age')->where($where)->find(); 
                $age_name=$age['left_age'] . '-' . $age['right_age']; 
            }else{
                $age_name='不限';
            }
            if($v['red_envelopes_interest_id']){
                $where_id['interest_id']=$v['red_envelopes_interest_id'];
                $interest=Model()->table('interest')->where($where_id)->find(); 
                $name=$interest['name'];
            }else{
                $name='不限';
            }
            $red_envelopes_picture=unserialize($v['red_envelopes_picture']);
            $envelopes=$red_envelopes_picture;
            if($envelopes[0]==false){
                $picture='';
            }else{
                $picture="<img width='100' height='100%' src='".UPLOAD_SITE_URL.DS.ATTACH_MOBILE.DS.$red_envelopes_picture[0]."'>";
            }
            $addtime = date("Y-m-d H:i:s", $v['addtime']);
            $param = array();
            if ($v['state'] == 0) {
                $param['operation'] = "<a class='btn blue' href='index.php?act=redbag_issue&op=redbag_modify&id=" . $v['red_envelopes_id'] . "&state=2'><i class='fa fa-pencil-square-o'></i>通过</a><a class='btn blue' href='index.php?act=redbag_issue&op=redbag_modify&id=" . $v['red_envelopes_id'] . "&state=1'><i class='fa fa-pencil-square-o'></i>拒绝</a><a class='btn blue' href='index.php?act=redbag_issue&op=see&id=" . $v['red_envelopes_id'] . "'><i class='fa fa-pencil-square-o'></i>查看</a>";
            } else {
                if ($v['state'] == 2) {
                    $param['operation'] = "<span style='color: #3AA55A;'>已审核</span><a class='btn blue' href='index.php?act=redbag_issue&op=see&id=" . $v['red_envelopes_id'] . "'><i class='fa fa-pencil-square-o'></i>查看</a>";
                } else {
                    $param['operation'] = "<span style='color: #ff3c00;'>已拒绝</span><a class='btn blue' href='index.php?act=redbag_issue&op=see&id=" . $v['red_envelopes_id'] . "'><i class='fa fa-pencil-square-o'></i>查看</a>";
                }
            }
            $param['red_envelopes_order_number'] = $v['red_envelopes_order_number'];
            $param['member_mobile'] = $v['member_mobile'];
            $param['money'] = $v['money'];
            $param['red_envelopes_num'] = $v['red_envelopes_num'];
            $param['area_name'] = $v['red_envelopes_province'] . $v['red_envelopes_city'] . $v['red_envelopes_area'];
            $param['age'] = $age_name;
            $param['name'] = $name;
            $param['gender'] = $gender;
            $param['red_envelopes_picture'] = $picture;
            $param['red_envelopes_content'] = $v['red_envelopes_contentpicture'];
            $param['link'] = $v['link'];
            $data['list'][$v['red_envelopes_id']] = $param;
        }
        Tpl::flexigridXML($data);
    }

    /*
    *  红包审核
    */
    public function redbag_modifyOp()
    {
        $issue = Model('red_redbag_issue');
        $data['state'] = $_GET['state'];
        $where['red_envelopes_id'] = $_GET['id'];
        $return = $issue->modify($where, $data);
        if ($return == false) {
            showMessage('执行失败', 'index.php?act=redbag_issue&op=redbag_manage');
        } else {
            if($data['state']==2){
                $this->red_redbag_miniOp($where);
                showMessage('执行成功', 'index.php?act=redbag_issue&op=redbag_manage');
            }else{
                $addBalance=$this->remainderOp($where);
                showMessage('执行成功', 'index.php?act=redbag_issue&op=redbag_manage');
            }
        }
    }

    /*
    * 审核不通过返余额
    */
    public function remainderOp($id){
        $issue=Model('red_redbag_issue');
        $where['red_envelopes_id'] = $id['red_envelopes_id'];
        $issuefind = $issue->envelopesget($where);
        $member=Model('member');
        $addBalance=$member->addBalance($issuefind['member_id'],$issuefind['money']);
        return $addBalance;
    }

    /*
    * 红包分成小包
    */
    public function red_redbag_miniOp($id){
        $issue=Model('red_redbag_issue');
        $where['red_envelopes_id'] = $id['red_envelopes_id'];
        $issuefind = $issue->envelopesget($where);
        $data['red_envelopes_id']=$issuefind['red_envelopes_id'];
        $data['red_envelopes_order_number']=$issuefind['red_envelopes_order_number'];
        $data['state']='0';
        $data['receivestate']='0';
        $data['addtime']=time();
        $city=$this->city_lord_share_benefitsOp($id);
        $superior=$this->share_benefitsOp($id);
        $data['money']=($issuefind['money']-$city-$superior)/$issuefind['red_envelopes_unissued'];
        $i=1;
        for ($i; $i <= $issuefind['red_envelopes_unissued']; $i++) { 
            $data['order_number']=$issuefind['red_envelopes_order_number'].'-'.$i;
            $return=$issue->miniadd($data);
        }
    }
    /*
    * 城主分润
    */
    public function city_lord_share_benefitsOp($id)
    {
        $sharebenefits = Model('red_city_lord_share_benefits');
        $issue = Model('red_redbag_issue');
        $citydistribution = Model('red_citydistribution_list');
        $member=Model('member');
        //查询城主分润设置
        $row = $sharebenefits->red_getRow(1);
        $where['red_envelopes_id'] = $id['red_envelopes_id'];
        //查询发红包的id
        $return = $issue->envelopesget($where);
        $wherefind['area_name']=$return['red_envelopes_area'];
        $wherefind['endtime']='0';
        //城主分润查询
        $datareturn=$citydistribution->citymemberfind($wherefind);
        if($datareturn){
            $data['money'] = $return['money'] * ($row['member_hair'] / 100);
            $data['order_number'] = 'CIRE' . substr($return['red_envelopes_order_number'], 2);
            $data['rob_red_envelopes_id'] =$return['red_envelopes_id'];
            $data['city_lord_id'] = $datareturn['city_lord_id'];
            $data['red_envelopes_order_number'] =$return['red_envelopes_order_number'];
            $data['red_envelopes_area'] = $datareturn['area_id'];
            $data['state'] ='0';
            $data['addtime'] = time();
            $citydistribution->citydistriadd($data);
            $data_where['member_id'] = $datareturn['member_id'];
            $data_array=$member->getMemberInfo($data_where);
            $rowdata['balance']=$data_array['balance']+$data['money'];
            $member->editMember($data_where,$rowdata);
            $money_logo = Model('red_money_logo');
            $dataarray['member_id']=$data_where['member_id'];
            $dataarray['member_mobile']=$data_array['member_mobile'];
            $dataarray['member_name']=$data_array['member_name'];
            $dataarray['sn']=$data['order_number'];
            $dataarray['type']='city_benefits';
            $dataarray['operate_amount']= $data['money'];
            $dataarray['surplus_amount']=$rowdata['balance'];
            $dataarray['add_time']=time();
            $dataarray['content']='会员'.$dataarray['member_mobile']. '城主区下所发红包的分润金额:'.$data['money'];
            $logo=$money_logo->logoadd($dataarray);
            if($logo){
               return $data['money'];
            }
        }else{
            $data['money'] = $return['money'] * ($row['member_hair'] / 100);
            return $data['money'];
        }
    }

    /*
    * 分润下级给上级
    */
    public function share_benefitsOp($id)
    {
        $distribution = Model('red_membership_distribution');
        $issue = Model('red_redbag_issue');
        $benefits = Model('red_share_benefits');
        $manage = Model('red_city_owner_manage');
        $member=Model('member');
        //查询红包订单订单
        $return = $issue->envelopesget($id);
        $row_where['me_member_id'] = $return['member_id'];
        //下级查上级
        $find = $manage->getfind($row_where);
        $where_array['me_member_id']=$find['invite_id'];
        //上级的基本信息
        $find_va = $manage->getfind($where_array);
        //查询会员分润设置信息
        $row = $distribution->red_getRow(1);
        if ($find_va['member_level'] != 0) {//上级是否是会员
            $data['money'] = $return['money'] * ($row['recommender_giving_red_packets'] / 100);
            $data['order_number'] = 'MERE' . substr($return['red_envelopes_order_number'], 2);
            $data['me_member_id'] = $return['member_id'];
            $data['envelopes_id'] = $return['red_envelopes_id'];
            //会员的基本信息
            $data_member=$member->getMemberInfo($where_array);
            $rowdata['balance']=$data_member['balance']+$data['money'];
            $where_memberid['member_id']=$data_member['member_id'];
            //更改上级余额
            $member->editMember($where_memberid,$rowdata);
            $data['member_id'] = $find['invite_id'];
            $data['state'] = 0;
            $data['addtime'] = time();
            //添加分润信息
            $benefits->sharebenefitsadd($data);
            $money_logo = Model('red_money_logo');
            $dataarray['member_id']=$data['member_id'];
            $dataarray['member_mobile']=$data_member['member_mobile'];
            $dataarray['member_name']=$data_member['member_name'];
            $dataarray['sn']=$data['order_number'];
            $dataarray['type']='redbag_benefits';
            $dataarray['operate_amount']=$data['money'];
            $dataarray['surplus_amount']=$rowdata['balance'];
            $dataarray['add_time']=time();
            $dataarray['content']='会员'.$dataarray['member_mobile']. '您的下级所发的红包分润金额:'.$data['money'];
            //加进余额明细中
            $logo=$money_logo->logoadd($dataarray);
            if($logo){
               return $data['money'];
            }
        }else{
            $data['money'] = $return['money'] * ($row['recommender_giving_red_packets'] / 100);
            return $data['money'];
        }
    }

    /*
    * 查看详情
    */
    public function seeOp()
    {
        $return = Model('red_redbag_issue');
        $condition['red_envelopes_id'] = $_GET['id'];
        $return_list = $return->getPdRechargeList($condition, $pagesize = '', $fields = '*', $order = '', $limit = '');
        foreach ($return_list as $k => $v) {
            if($v['red_envelopes_gender']==0){
                $data['gender']='不限';
            }elseif($v['red_envelopes_gender']==1){
                $data['gender']='男';
            }else{
                $data['gender']='女';
            }
            if($v['red_envelopes_age_id']){
                $where['age_id']=$v['red_envelopes_age_id'];
                $age=Model()->table('age')->where($where)->find(); 
                $data['age_name']=$age['left_age'] . '-' . $age['right_age']; 
            }else{
                $data['age_name']='不限';
            }
            if($v['red_envelopes_interest_id']){
                $where_id['interest_id']=$v['red_envelopes_interest_id'];
                $interest=Model()->table('interest')->where($where_id)->find(); 
                $data['name']=$interest['name'];
            }else{
                $data['name']='不限';
            }
            $red_envelopes_picture=unserialize($v['red_envelopes_picture']);
            $data['envelopes']=array_filter($red_envelopes_picture);
            $data['issue']=$v;
        }
        Tpl::output('info', $data);
        Tpl::setDirquna('red_envelopes');
        Tpl::showpage('redbag_examine.see');
    }

    /**
     * 导出红包审核
     *
     */
    public function export_step1Op()
    {
        $return = Model('red_redbag_issue');
        $return_list = $return->getPdRechargeList();
        $this->createExcel($return_list);
    }

    /**
     * 生成导出红包审核excel
     *
     * @param array $data
     */
    private function createExcel($data = array())
    {
        Language::read('export');
        import('libraries.excel');
        $excel_obj = new Excel();
        $excel_data = array();
        // 设置样式
        $excel_obj->setStyle(array('id' => 's_title', 'Font' => array('FontName' => '宋体', 'Size' => '12', 'Bold' => '1')));
        // header
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '红包单号');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '用户手机');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '红包金额');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '红包个数');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '定位范围');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '年龄阶段');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '爱好');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '广告词');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '链接');

        foreach ((array)$data as $k => $v) {
            $tmp = array();
            $tmp[] = array('data' => $v['red_envelopes_order_number']);
            $tmp[] = array('data' => $v['member_mobile']);
            $tmp[] = array('data' => $v['money']);
            $tmp[] = array('data' => $v['red_envelopes_num']);
            $tmp[] = array('data' => $v['red_envelopes_province'] . $v['red_envelopes_city'] . $v['red_envelopes_area']);
            $tmp[] = array('data' => $v['left_age'] . '-' . $v['right_age']);
            $tmp[] = array('data' => $v['name']);
            $tmp[] = array('data' => $v['red_envelopes_content']);
            $tmp[] = array('data' => $v['link']);
            $excel_data[] = $tmp;
        }
        $excel_data = $excel_obj->charset($excel_data, CHARSET);
        $excel_obj->addArray($excel_data);
        $excel_obj->addWorksheet($excel_obj->charset('红包列表', CHARSET));
        $excel_obj->generateXML($excel_obj->charset('红包列表', CHARSET) . $_GET['curpage'] . '-' . date('Y-m-d-H', time()));
    }

}