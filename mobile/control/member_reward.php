<?php
/**
 * 我的奖励
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/19 0019
 * Time: 下午 19:57
 */

class member_rewardControl extends mobileMemberControl
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 会员奖励种类对应数量
     */
    public function reward_numOp(){
        $member_id = $this->member_info['member_id'];

        $condition['member_id'] = $member_id;
        $member_profit_model = Model('member_profit');
        $reward_num_arr =$member_profit_model->getRewardNumArr($condition);

        foreach ($reward_num_arr as $k=>$v){
            switch ($v['type']){
                case 'direct':
                    $param['直推奖励'] = $v['num']?$v['num']:0;
                    break;
                case 'eql':
                    $param['平级奖励'] = $v['num']?$v['num']:0;
                    break;
                default:
                    $param['级差奖励'] = $v['num']?$v['num']:0;
            }
        }
        if (!$param['级差奖励']){
            $param['级差奖励'] = 0;
        }
        if (!$param['平级奖励']){
            $param['平级奖励'] = 0;
        }
        if (!$param['直推奖励']){
            $param['直推奖励'] = 0;
        }
        responseApiJson(1,'',$param);
    }

    /**
     * 我的级差奖励
     */
    public function member_diff_rewardOp(){
        $member_id = $this->member_info['member_id'];
        $condition['member_id'] = $member_id;
        $condition['type'] = 'diff';
        $member_profit_model = Model('member_profit');
        $member_profit_record =$member_profit_model->getOrderProfitRecord($condition);
        //分润订单号数组
        $pay_sn_arr = array_column($member_profit_record,'order_sn');
        //获取订单信息
        $condition2['pay_sn'] = ['in',$pay_sn_arr];
        $order_list = Model('order')->getOrderList($condition2);
        $profit_reward_list = array();
        foreach ($order_list as $k=>$v){
            $profit_reward_list[$k]['order_sn'] = $v['order_sn'];
            $profit_reward_list[$k]['buyer_name'] = $v['buyer_name'];
            $profit_reward_list[$k]['payment_time'] = date("Y-m_d H:i:s",$v['payment_time']);
            $profit_reward_list[$k]['order_amount'] = $v['order_amount'];
            $profit_reward_list[$k]['prize'] = $member_profit_record[$k]['prize'];
            $profit_reward_list[$k]['order_state'] = $v['order_state']? '已确认':'待确认';
        }
        $total_prize = array_sum(array_column($profit_reward_list,'order_amount'));
        $data['profit_reward_list'] = $profit_reward_list;
        $data['total_prize'] = (string)$total_prize;
        responseApiJson(1,'',$data);
    }


    /**
     * 我的平级奖励
     */
    public function member_eql_rewardOp(){
        $member_id = $this->member_info['member_id'];
        $condition['member_id'] = $member_id;
        $condition['type'] = 'eql';
        $member_profit_model = Model('member_profit');
        $member_profit_record =$member_profit_model->getOrderProfitRecord($condition);
        //分润订单号数组
        $pay_sn_arr = array_column($member_profit_record,'order_sn');
        $total_prize = array_sum(array_column($member_profit_record,'prize'));
        //获取订单信息
        $condition2['pay_sn'] = ['in',$pay_sn_arr];
        $order_list = Model('order')->getOrderList($condition2);
        $profit_reward_list = array();
        foreach ($order_list as $k=>$v){
            $profit_reward_list[$k]['order_sn'] = $v['order_sn'];
            $profit_reward_list[$k]['buyer_name'] = $v['buyer_name'];
            $profit_reward_list[$k]['payment_time'] = date("Y-m_d H:i:s",$v['payment_time']);
            $profit_reward_list[$k]['order_amount'] = $v['order_amount'];
            $profit_reward_list[$k]['order_amount'] = $v['order_amount'];
            $profit_reward_list[$k]['prize'] = $member_profit_record[$k]['prize'];
            $profit_reward_list[$k]['order_state'] = $v['order_state']? '已确认':'待确认';
        }
        $data['profit_reward_list'] = $profit_reward_list;
        $data['total_prize'] = (string)$total_prize;
        responseApiJson(1,'',$data);
    }


    /**
     * 我的直推奖励
     */
    public function member_direct_rewardOp(){
        $member_id = $this->member_info['member_id'];
        $condition['member_id'] = $member_id;
        $condition['type'] = 'direct';
        $member_profit_model = Model('member_profit');
        $member_profit_record =$member_profit_model->getOrderProfitRecord($condition);
        //分润订单号数组
        $pay_sn_arr = array_column($member_profit_record,'order_sn');
        $total_prize = array_sum(array_column($member_profit_record,'prize'));
        //获取订单信息
        $condition2['pay_sn'] = ['in',$pay_sn_arr];
        $order_list = Model('order')->getOrderList($condition2);
        $profit_reward_list = array();
        foreach ($order_list as $k=>$v){
            $profit_reward_list[$k]['order_sn'] = $v['order_sn'];
            $profit_reward_list[$k]['buyer_name'] = $v['buyer_name'];
            $profit_reward_list[$k]['payment_time'] = date("Y-m_d H:i:s",$v['payment_time']);
            $profit_reward_list[$k]['order_amount'] = $v['order_amount'];
            $profit_reward_list[$k]['order_amount'] = $v['order_amount'];
            $profit_reward_list[$k]['prize'] = $member_profit_record[$k]['prize'];
            $profit_reward_list[$k]['order_state'] = $v['order_state']? '已确认':'待确认';
        }
        $data['profit_reward_list'] = $profit_reward_list;
        $data['total_prize'] = (string)$total_prize;
        responseApiJson(1,'',$data);
    }
}