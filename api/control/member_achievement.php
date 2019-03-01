<?php
/**
 * 会员业绩
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/14 0014
 * Time: 上午 10:09
 */
 header("Access-Control-Allow-Origin: *");
defined('In33hao') or exit('Access Invalid!');
class member_achievementControl extends apiEncryptMemberControl
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 我的业绩
     */
    public function member_achievementOp(){
        $member_id = $this->member_info['member_id'];

        $order_model = Model('order');

        //获取报单商品的订单号
        $condition['buyer_id'] = $member_id;
        $condition['goods.is_bd_product'] = 1;
        $order_id_arr = Model('order')->getProductOrder($condition);
        if (!empty($order_id_arr)){
            $bd_product_order_id = $order_id_arr;
        }
        $bd_product_order_id = @array_column($bd_product_order_id,'order_id');

        //获取报单订单的详情
        $member_achievement = $order_model->getOrderList(array('order_id'=>['in',$bd_product_order_id],'order_state'=>['in',[20,30,40]]));
        $achievement_list = array();
        $total_achievement = 0;
        foreach ($member_achievement as $k=>$v){
            $achievement_list[$k]['order_sn'] = $v['order_sn'];
            $achievement_list[$k]['buyer_name'] = $v['buyer_name'];
            $achievement_list[$k]['order_time'] = date('Y-m-d H:i:s',$v['add_time']);
            $achievement_list[$k]['order_amount'] = $v['order_amount'];
            $achievement_list[$k]['order_state'] = str_replace(array(10,20,30,40,0),array("待付款","待发货","待收货","已收货","已取消"),$v['order_state']);
            if (in_array($v['order_state'],[20,30,40])){
                $total_achievement += $v['order_amount'];
            }
        }
        $data['total_achievement'] = (string)$total_achievement;
        $data['achievement_list'] = $achievement_list;

        responseApiJson(1,'',$data);
    }

    /**
     * 我的团队业绩
     */
    public function member_team_achievementOp(){
        $member_id = $this->member_info['member_id'];
        $order_model = Model('order');
        //所有下级ID
        $child_id_arr = Model('member_extend')->getInviteChildInfoArr($member_id);
        array_shift($child_id_arr);
        //获取报单商品的订单号
        $condition['buyer_id'] = ['in',$child_id_arr];
        $condition['goods.is_bd_product'] = 1;
        $order_id_arr = Model('order')->getProductOrder($condition);
        if (!empty($order_id_arr)){
            $bd_product_order_id = $order_id_arr;
        }
        $bd_product_order_id = @array_column($bd_product_order_id,'order_id');

        //获取报单订单的详情
        $member_achievement = $order_model->getOrderList(array('order_id'=>['in',$bd_product_order_id],'order_state'=>['in',[20,30,40]]));
        $achievement_list = array();
        $total_achievement = 0;
        foreach ($member_achievement as $k=>$v){
            $achievement_list[$k]['order_sn'] = $v['order_sn'];
            $achievement_list[$k]['buyer_name'] = $v['buyer_name'];
            $achievement_list[$k]['order_time'] = date('Y-m-d H:i:s',$v['add_time']);
            $achievement_list[$k]['order_amount'] = $v['order_amount'];
            $achievement_list[$k]['order_state'] = str_replace(array(10,20,30,40,0),array("待付款","待发货","待收货","已收货","已取消"),$v['order_state']);
            if (in_array($v['order_state'],[20,30,40])){
                $total_achievement += $v['order_amount'];
            }
        }
        $data['total_achievement'] = (string)$total_achievement;
        $data['achievement_list'] = $achievement_list;

        responseApiJson(1,'',$data);
    }
}

