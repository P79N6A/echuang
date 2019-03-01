<?php
/**
 * 地区
 *
 *
 *  (c) ()
 * @license
 * @link
 * @since
 */
header("Access-Control-Allow-Origin: *");

defined('In33hao') or exit('Access Invalid!');
class redbay_paymentControl extends apiEncryptMemberControl {
	private $payment_code;
	private $payment_config;
	
	public function __construct() {
		parent::__construct();
		if ($_GET['op'] != 'payment_list' && !$_POST['payment_code']) {
			    $payment_code = 'alipay';
			if (in_array($_GET['op'], array('wx_app_pay', 'wx_app_pay3', 'wx_app_vr_pay', 'wx_app_vr_pay3'), true)) {
				$payment_code = 'wxpay';
			} else if (in_array($_GET['op'], array('alipay_native_pay', 'alipay_native_vr_pay'), true)) {
				$payment_code = 'alipay_native';
			} else if (isset($_GET['payment_code'])) {
				$payment_code = $_GET['payment_code'];
			}
			$model_mb_payment = Model('mb_payment');
			$condition = array();
			$condition['payment_code'] = $payment_code;
			$mb_payment_info = $model_mb_payment->getMbPaymentOpenInfo($condition);
			$this->payment_code = $payment_code;
			$this->payment_config = $mb_payment_info['payment_config'];
		}
	}

	/*
	 * 实物订单支付 新方法
	 */
	public function pay_newOp() {
		//@header("Content-type: text/html; charset=" . CHARSET);
		$pay_sn = $_POST['pay_sn'];
		var_dump($_POST);die;
		if (in_array($_POST['payment_code'], array('alipay', 'wxpay', 'beanpay', 'sliver_beanpay','balancepay'))) {
			if ($_POST['payment_code'] == "beanpay") {
				$pay_info = $this->_get_real_order_info($pay_sn, $_POST);
			}
			if ($_POST['payment_code'] == "sliver_beanpay") {
				$pay_info = $this->_get_real_order_info($pay_sn, $_POST);
			}
			if ($_POST['payment_code'] == "alipay") {
				$pay_info = $this->alipay_native_payOp($pay_sn);
			}
			if ($_POST['payment_code'] == "wxpay") {
				$pay_info = $this->wx_app_payOp($pay_sn);
			}
            if ($_POST['payment_code'] == "balancepay") {
                $pay_info = $this->_get_real_order_info($pay_sn, $_POST);
            }
			if (isset($pay_info['error'])) {
				responseApiJson(0, $pay_info['error'], '');
			}
		} else {
            responseApiJson(0, '暂无该支付方式11', '');
		}
	}

    /*
     * 获取订单支付信息
     */
    private function _get_real_order_info($pay_sn, $rcb_pd_pay = array()) {
        $redbay_issue = Model('red_redbay_issue');
		$red_where['member_id']=$this->member_info['member_id'];
        //取订单信息
        $red_where['red_envelopes_order_number']=$pay_sn;
        $order_list = $redbay_issue->table('red_envelopes')->where($red_where)->find();
        if ($order_list['payment_state']==1) {
            responseApiJson(0, '订单已经支付，不能重复支付', '');
        }
        //余额支付
        if ($rcb_pd_pay['payment_code'] == 'balancepay') {
            $result['data']['order_list'] = $this->_balance_pay($order_list, $rcb_pd_pay,$red_where['member_id']);
        }
        if($rcb_pd_pay['payment_code'] == ''){
            $result = $order_list;
        }
        return $result;
    }

    private function _balance_pay($order_list,$post,$memberid){
        if (empty($post['password'])) {
            responseApiJson(0, '参数错误', '');
        }
        $model_member = Model('red_advertisement');
        $money_logo = Model('red_money_logo');
        $where['member_id']=$memberid;
        $buyer_info = $model_member->getMemberInfoByID($where);
        if ($buyer_info['member_paypwd'] == '' || $buyer_info['member_paypwd'] != $post['password']) {
            responseApiJson(0, "用户支付密码不正确", '');
        }
        try {
            $member_id = $buyer_info['member_id'];
	        $member_name = $buyer_info['member_name'];
	        $model_order = Model('order');
	        $model_member_extend = Model('member_extend');
	        $issue = Model('red_redbag_issue');
	        $balance = $model_member_extend->getMemberExtendInfo(array('member_id'=>$member_id),'balance','union');
	        $balance = floatval($balance['balance']);
            $money = floatval($order_list['money']);
            $data_pay = array();
            $data_pay['member_id'] = $member_id;
            $data_pay['type'] = 'redbay';
            $data_pay['member_name'] = $member_name;
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
                if ($result) {
	                responseApiJson(1, '支付成功', '');
	            } else {
	                responseApiJson(0, '支付失败1', '');
	            }
                }
            } else {
                throw new Exception('可用余额不足');
            }
        } catch (Exception $exception) {
            responseApiJson(0, $exception->getMessage(),'');
        }
    }
    /**
     * 微信APP订单支付
     */
    public function wx_app_payOp($pay_sn) {
        $pay_info = $this->_get_real_order_info($pay_sn);
        if (isset($pay_info['red_envelopes_order_number']!=$pay_sn)) {
            responseApiJson(0, '订单号错误', '');
        }
        $param = array();
        $param['pay_sn'] = $pay_sn;
        $param['subject'] = $pay_info['red_envelopes_contentpicture'];
        $param['amount'] = $pay_info['money'] * 100;
        var_dump($param);die;
        $data = $this->aaaOp($param);
        var_dump($data);die;
        responseApiJson(1, '', $data);
    }
    /*
     * 微信API接口
     */
    public function aaaOp($param) {
        $inc_file = '../mobile' . DS . 'api' . DS . 'payment' . DS . 'wxpay' . '.php';
        require_once $inc_file;
        $wxpay = new Wxpay();
        $pre_param = $wxpay->getPrePayOrder($param['subject'], $param['pay_sn'], $param['amount']);
        var_dump($pre_param);die;
        $pay_param = $wxpay->getPayParam($pre_param);
        return $pay_param;
    }
}
