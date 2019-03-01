<?php
/**
 * 城主分润
 *
 */
defined('In33hao') or exit('Access Invild!');
class city_owner_rebateControl extends SystemControl {


    public function __construct()
    {
        parent::__construct();
    }

    public function indexOp()
    {
        $this->ownerrebateOp();
    }

    /**
     * 红包审核记录列表
     */
    public function ownerrebateOp()
    {
        Tpl::setDirquna('red_envelopes');
        Tpl::showpage('redbag_rebate.list');
    }

    public function get_xmlOp(){
        $page = $_POST['rp'];
        $member=Model('member');
        $data = array();
        $condition = array();
        if ($_POST['query'] != '') {
            $condition[$_POST['qtype']] = array('like', '%' . $_POST['query'] . '%');
        }
        $return_list = Model()->table('citydistribution_list')->where($condition)->page($page)->select();
//        var_dump($condition,$return_list);die();
        $data['now_page'] = $member->shownowpage();
        $data['total_num'] = $member->gettotalnum();
        foreach ($return_list as $v) {
            $addtime=date("Y-m-d H:i:s",$v['addtime']);
            $param = array();
            $param['order_number'] = $v['order_number'];
            $param['envelopes_order_number'] = $v['red_envelopes_order_number'];
            $param['member_mobile'] = $v['member_mobile'];
            $param['money'] = $v['money'];
            $param['addtime'] = $addtime;
            $mobile = Model('member')->getMemberMobileById($v['member_id']);
            $param['desc'] = "区域用户".$mobile.($v['state']?"发":"抢")."红包所得分润";
            $data['list'][$v['benefits_id']] = $param;
        }
        Tpl::flexigridXML($data);
    }

}