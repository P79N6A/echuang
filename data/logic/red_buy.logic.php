<?php
/**
 * 购买行为
 *
 * @
 * @license
 * @link

 */
defined('In33hao') or exit('Access Invalid!');
class red_buyLogic
{

   public function balancePay($order_list, $input, $buyer_info)
    {
        $member_id = $buyer_info['member_id'];
        $member_name = $buyer_info['member_name'];
        $model_order = Model('order');
        $model_member_extend = Model('member_extend');
        $issue = Model('red_redbag_issue');
        $balance = $model_member_extend->getMemberExtendInfo(array('member_id'=>$member_id),'balance','union');
        $balance = floatval($balance['balance']);
        foreach ($order_list as $key => $order_info) {
            $balance = floatval($balance['balance']);
            $money = floatval($order_list['money']);
            $data_pay = array();
            $data_pay['member_id'] = $member_id;
            $data_pay['pay_sn'] = $order_list['red_envelopes_order_number'];
            $data_pay['amount'] = $order_list['money'];
            if ($balance >= $money) {
                // 立即支付，订单支付完成
                $result = $model_member_extend->order_pay($data_pay);
                if (isset($result['error'])) {
                    throw new Exception('支付失败4');
                }
                $balance -= $order_amount;
                //记录订单日志(已付款)
                $data = array();
                $data['order_id'] = $order_list['red_envelopes_id'];
                $data['log_role'] = 'buyer';
                $data['log_msg'] = '支付订单';
                $data['log_orderstate'] = ORDER_STATE_PAY;
                $insert = $model_order->addOrderLog($data);
                if (!$insert) {
                    throw new Exception('记录订单金豆支付日志出现错误');
                }
                $where['red_envelopes_id']=$order_list['red_envelopes_id'];
                $data_list['payment_state']='1';
                $data_list['payment_method']='0';
                $result=$issue->modify($where,$data_list);
                if (!$result) {
                    throw new Exception('订单更新失败');
                } else {
                    return $result;
                }
            } else {
                throw new Exception('可用余额不足');
            }
        }
    }
}
