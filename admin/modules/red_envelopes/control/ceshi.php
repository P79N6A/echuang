<?php
/**
 * 年龄
 *
 */
defined('In33hao') or exit('Access Invild!');
class ceshiControl extends SystemControl {

    public function __construct()
    {
        parent::__construct();
    }

    public function indexOp()
    {
        $this->share_benefitsOp();
    }

    /*
    * 分润下级给上级
    */
    public function share_benefitsOp()
    {
        $id['red_envelopes_id']='29';
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
        $where_array['member_id']=$find['invite_id'];
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
            $dataarray['operate_amount']=$data_member['balance'];
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

}

