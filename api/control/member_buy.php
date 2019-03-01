<?php
/**
 * 购买
 *
 *  (c) ()
 * @license
 * @link
 * @since
 */
header("Access-Control-Allow-Origin: *");
defined('In33hao') or exit('Access Invalid!');

class member_buyControl extends apiEncryptMemberControl {

	public function __construct() {
		parent::__construct();
	}

	/**
	 * 购物车、直接购买第一步:选择收获地址和配置方式
	 */
	public function buy_step1_bakOp() {
		$cart_id = explode(',', $_GET['cart_id']);
		$logic_buy = logic('buy');

		//得到会员等级
		$model_member = Model('member');
		$member_info = $model_member->getMemberInfoByID($this->member_info['member_id']);
//		if (!$member_info['is_buy']) {
//			responseApiJson(0, '', '您没有商品购买的权限,如有疑问请联系客服人员');
//		}

		if (empty($_POST['ifcart'])) {
			$if_cart = $_GET['ifcart'];
		} else {
			$if_cart = $_POST['ifcart'];
		}
		if ($member_info) {
			$member_gradeinfo = $model_member->getOneMemberGrade(intval($member_info['member_exppoints']));
			$member_discount = $member_gradeinfo['orderdiscount'];
			$member_level = $member_gradeinfo['level'];
		} else {
			$member_discount = $member_level = 0;
		}
		//得到购买数据
		$result = $logic_buy->buyStep1($cart_id, $if_cart, $this->member_info['member_id'], $this->member_info['store_id'], null, $member_discount, $member_level);
//		echo '<pre>';
//		var_dump($result);
//		exit;
		if (!$result['state']) {
			responseApiJson(0, $result['msg'], '');
		} else {
			$result = $result['data'];
		}

		if (intval($_GET['address_id']) > 0) {
			$result['address_info'] = Model('address')->getDefaultAddressInfo(array('address_id' => intval($_GET['address_id']), 'member_id' => $this->member_info['member_id']));
		}
		if ($result['address_info']) {
			$data_area = $logic_buy->changeAddr($result['freight_list'], $result['address_info']['city_id'], $result['address_info']['area_id'], $this->member_info['member_id']);
			if (!empty($data_area) && $data_area['state'] == 'success') {
				if (is_array($data_area['content'])) {
					foreach ($data_area['content'] as $store_id => $value) {
						$data_area['content'] = ncPriceFormat($value);
					}
				}else{
					$data_area['content'] = "";
				}
			}
		}

		//整理数据
		$store_cart_list = array();
		$store_total_list = $result['store_goods_total_1'];
		$order_total_amount = 0;
//		echo '<pre>';
//		var_dump($result['store_cart_list']);
//		exit;
		foreach ($result['store_cart_list'] as $key => $value) {
			foreach ($value as $k=>$va){
				$goods_freight_arr[]=$va['goods_freight'];
			}
			rsort($goods_freight_arr);
			$goods_freight=$goods_freight_arr[0];
			$store_cart_list[$key]['goods_list'] = $value;
			$store_cart_list[$key]['store_goods_total'] = $result['store_goods_total'][$key];
			$order_total_amount += $store_cart_list[$key]['store_goods_total'];
			$store_cart_list[$key]['store_mansong_rule_list'] = $result['store_mansong_rule_list'][$key];

			if (is_array($result['store_voucher_list'][$key]) && count($result['store_voucher_list'][$key]) > 0) {
				reset($result['store_voucher_list'][$key]);
				$store_cart_list[$key]['store_voucher_info'] = current($result['store_voucher_list'][$key]);
				$store_cart_list[$key]['store_voucher_info']['voucher_price'] = ncPriceFormat($store_cart_list[$key]['store_voucher_info']['voucher_price']);
				$store_total_list[$key] -= $store_cart_list[$key]['store_voucher_info']['voucher_price'];
			} else {
				$store_cart_list[$key]['store_voucher_info'] = array();
			}

			$store_cart_list[$key]['store_voucher_list'] = $result['store_voucher_list'][$key];
			if (!empty($result['cancel_calc_sid_list'][$key])) {
				$store_cart_list[$key]['freight'] = '0';
				$store_cart_list[$key]['freight_message'] = $result['cancel_calc_sid_list'][$key]['desc'];
			} else {
				$store_cart_list[$key]['freight'] = '1';
			}
			$store_cart_list[$key]['store_name'] = $value[0]['store_name'];
		}

		$buy_list = array();
		$buy_list['store_cart_list'] = $store_cart_list;
		$buy_list['freight_hash'] = $result['freight_list'];
		$buy_list['address_info'] = $result['address_info'];
		$buy_list['ifshow_offpay'] = $result['ifshow_offpay'];
		$buy_list['vat_hash'] = $result['vat_hash'];
		$buy_list['inv_info'] = $result['inv_info'];
		$buy_list['available_predeposit'] = $result['available_predeposit'];
		$buy_list['available_rc_balance'] = $result['available_rc_balance'];
		if (is_array($result['rpt_list']) && !empty($result['rpt_list'])) {
			foreach ($result['rpt_list'] as $k => $v) {
				unset($result['rpt_list'][$k]['rpacket_id']);
				unset($result['rpt_list'][$k]['rpacket_end_date']);
				unset($result['rpt_list'][$k]['rpacket_owner_id']);
				unset($result['rpt_list'][$k]['rpacket_code']);
			}
		}
		$buy_list['rpt_list'] = $result['rpt_list'] ? $result['rpt_list'] : array();
		$buy_list['zk_list'] = $result['zk_list'];

//        if ($data_area['content']) {
		//            $store_total_list = Logic('buy_1')->reCalcGoodsTotal($store_total_list,$data_area['content'],'freight');
		//            //返回可用平台红包
		//            $result['rpt_list'] = Logic('buy_1')->getStoreAvailableRptList($this->member_info['member_id'],array_sum($store_total_list),'rpacket_limit desc');
		//            reset($result['rpt_list']);
		//            if (is_array($result['rpt_list']) && count($result['rpt_list']) > 0) {
		//                $result['rpt_info'] = current($result['rpt_list']);
		//                unset($result['rpt_info']['rpacket_id']);
		//                unset($result['rpt_info']['rpacket_end_date']);
		//                unset($result['rpt_info']['rpacket_owner_id']);
		//                unset($result['rpt_info']['rpacket_code']);
		//            }
		//        }
		$buy_list['order_amount'] = number_format($order_total_amount + $goods_freight, 2, '.', '');
		$buy_list['rpt_info'] = $result['rpt_info'] ? $result['rpt_info'] : array();
		$buy_list['address_api'] = $data_area ? $data_area : (object)array();
		$buy_list['shipping_fee'] = $goods_freight;
		//$buy_list['order_amount']=$buy_list['order_amount']+$goods_freight;
		//        foreach ($store_total_list as $store_id => $value) {
		//            $store_total_list[$store_id] = ncPriceFormat($value);
		//        }
		//        $buy_list['store_final_total_list'] = $store_total_list;
		responseApiJson(1, '', $buy_list);
	}

	/**
	 * 购物车、直接购买第二步:保存订单入库，产生订单号，开始选择支付方式
	 *
	 */
	public function buy_step2_bakOp() {
		$param = array();
		$param['ifcart'] = $_POST['ifcart'];
		$param['cart_id'] = explode(',', $_POST['cart_id']);
		$param['address_id'] = $_POST['address_id'];
//        $param['vat_hash'] = $_POST['vat_hash'];
		$param['offpay_hash'] = $_POST['offpay_hash'];
		$param['offpay_hash_batch'] = $_POST['offpay_hash_batch'];
		$param['pay_name'] = $_POST['pay_name'];
		$param['invoice_id'] = $_POST['invoice_id'];
		$param['rpt'] = $_POST['rpt'];

//        //处理代金券
		//        $voucher = array();
		//        $post_voucher = explode(',', $_POST['voucher']);
		//        if(!empty($post_voucher)) {
		//            foreach ($post_voucher as $value) {
		//                list($voucher_t_id, $store_id, $voucher_price) = explode('|', $value);
		//                $voucher[$store_id] = $value;
		//            }
		//        }
		//        $param['voucher'] = $voucher;

//        $_POST['pay_message'] = trim($_POST['pay_message'],',');
		//        $_POST['pay_message'] = explode(',',$_POST['pay_message']);
		//        $param['pay_message'] = array();
		//        if (is_array($_POST['pay_message']) && $_POST['pay_message']) {
		//            foreach ($_POST['pay_message'] as $v) {
		//                if (strpos($v, '|') !== false) {
		//                    $v = explode('|', $v);
		//                    $param['pay_message'][$v[0]] = $v[1];
		//                }
		//            }
		//        }
		$param['pay_message'][1] = $_POST['buyer_message'];
		$param['pd_pay'] = $_POST['pd_pay'];
		$param['rcb_pay'] = $_POST['rcb_pay'];
		$param['password'] = $_POST['password'];
		$param['fcode'] = $_POST['fcode'];
		$param['order_from'] = 2;
		$logic_buy = logic('buy');

		//得到会员等级
		$model_member = Model('member');
		$member_info = $model_member->getMemberInfoByID($this->member_info['member_id']);
		if ($member_info) {
			$member_gradeinfo = $model_member->getOneMemberGrade(intval($member_info['member_exppoints']));
			$member_discount = $member_gradeinfo['orderdiscount'];
			$member_level = $member_gradeinfo['level'];
		} else {
			$member_discount = $member_level = 0;
		}
		$result = $logic_buy->buyStep2($param, $this->member_info['member_id'], $this->member_info['member_name'], $this->member_info['member_email'], $member_discount, $member_level);
		if (!$result['state']) {
			responseApiJson(0, $result['msg'], '');
		}
		//$order_info = current($result['data']['order_list']);
		$this->payOp($result['data']['pay_sn']);
		//responseApiJson(1,'',array('pay_sn' => $result['data']['pay_sn'],'payment_code'=>$order_info['payment_code']));
	}

	/**
	 * 验证密码
	 */
	public function check_passwordOp() {
		if (empty($_POST['password'])) {
			responseApiJson(0, '参数错误', '');
		}

		$model_member = Model('member');

		$member_info = $model_member->getMemberInfoByID($this->member_info['member_id']);
		if ($member_info['member_paypwd'] == md5($_POST['password'])) {
			responseApiJson(1, '密码正确', '');
		} else {
			responseApiJson(0, '密码错误', '');
		}
	}

	/**
	 * 更换收货地址
	 */
	public function change_addressOp() {
		$logic_buy = Logic('buy');
		if (empty($_POST['city_id'])) {
			$_POST['city_id'] = $_POST['area_id'];
		}

		$data = $logic_buy->changeAddr($_POST['freight_hash'], $_POST['city_id'], $_POST['area_id'], $this->member_info['member_id']);
		if (!empty($data) && $data['state'] == 'success') {
			responseApiJson(1, '', $data);
		} else {
			responseApiJson(0, '地址修改失败' . '');
		}
	}

	/**
	 * 实物订单支付(新接口)
	 */
	public function payOp($pay_sn,$product_num) {
		if (!preg_match('/^\d{18}$/', $pay_sn)) {
			responseApiJson(0, '该订单不存在', '');
		}

		//查询支付单信息
		$model_order = Model('order');
		$pay_info = $model_order->getOrderPayInfo(array('pay_sn' => $pay_sn, 'buyer_id' => $this->member_info['member_id']), true);
		if (empty($pay_info)) {
			responseApiJson(0, '该订单不存在', '');
		}

		//取子订单列表
		$condition = array();
		$condition['pay_sn'] = $pay_sn;
		$condition['order_state'] = array('in', array(ORDER_STATE_NEW, ORDER_STATE_PAY));
		$order_list = $model_order->getOrderList($condition, '', '*', '', '', array(), true);
		if (empty($order_list)) {
			responseApiJson(0, '未找到需要支付的订单', '');
		}

		//定义输出数组
		$pay = array();
		//支付提示主信息
		//订单总支付金额(不包含货到付款)
		$pay['pay_amount'] = 0;
		//充值卡支付金额(之前支付中止，余额被锁定)
		$pay['payed_rcb_amount'] = 0;
		//预存款支付金额(之前支付中止，余额被锁定)
		$pay['payed_pd_amount'] = 0;
		//还需在线支付金额(之前支付中止，余额被锁定)
		$pay['pay_diff_amount'] = 0;
		//账户可用金额
		//        $pay['member_available_pd'] = 0;
		//        $pay['member_available_rcb'] = 0;

		$logic_order = Logic('order');

		//计算相关支付金额
		foreach ($order_list as $key => $order_info) {
			if (!in_array($order_info['payment_code'], array('offline', 'chain'))) {
				if ($order_info['order_state'] == ORDER_STATE_NEW) {
					$pay['payed_rcb_amount'] += $order_info['rcb_amount'];
					$pay['payed_pd_amount'] += $order_info['pd_amount'];
					$pay['pay_diff_amount'] += $order_info['order_amount'] - $order_info['rcb_amount'] - $order_info['pd_amount'];
				}
			}
		}
		if ($order_info['chain_id'] && $order_info['payment_code'] == 'chain') {
			$order_list[0]['order_remind'] = '下单成功，请在' . CHAIN_ORDER_PAYPUT_DAY . '日内前往门店提货，逾期订单将自动取消。';
			$flag_chain = 1;
		}

		//如果线上线下支付金额都为0，转到支付成功页
		if (empty($pay['pay_diff_amount'])) {
			responseApiJson(0, '订单重复支付', '');
		}

		$payment_list = Model('mb_payment')->getMbPaymentOpenList();
		if (!empty($payment_list)) {
			foreach ($payment_list as $k => $value) {
				if ($value['payment_code'] == 'wxpay') {
					$payment_new_list['wxpay'] = 'wxpay';
				}
				if ($value['payment_code'] == 'alipay') {
					$payment_new_list['alipay'] = 'alipay';
				}
				if ($value['payment_code'] == 'beanpay') {
					$payment_new_list['beanpay'] = 'beanpay';
				}
				if ($value['payment_code'] == 'sliver_beanpay') {
					$payment_new_list['sliver_beanpay'] = 'sliver_beanpay';
				}
                if ($value['payment_code'] == 'balancepay') {
                    $payment_new_list['balancepay'] = 'balancepay';
                }
				/*unset($payment_list[$k]['payment_id']);
					                unset($payment_list[$k]['payment_config']);
					                unset($payment_list[$k]['payment_state']);
				*/
			}
		}
		$discount = Model('level')->getMemberDiscount($this->member_info['member_level']);

		//显示余额
		$pay['member_balance'] = $this->member_info['balance'];
		$pay['member_paypwd'] = $this->member_info['member_paypwd'] ? true : false;
		$pay['pay_sn'] = $pay_sn;
//        $pay['payed_amount'] = ncPriceFormat($pay['payed_rcb_amount']+$pay['payed_pd_amount']);
		unset($pay['payed_pd_amount']);unset($pay['payed_rcb_amount']);
		$pay['pay_amount'] =$discount * ncPriceFormat($pay['pay_diff_amount']);
		unset($pay['pay_diff_amount']);
		$pay['payment_list'] = $payment_new_list;
		responseApiJson(1, '', array('pay_info' => $pay,'product_num'=>$product_num));
	}

	/**
	 * AJAX验证支付密码
	 */
	public function check_pd_pwdOp() {
		if (empty($_POST['password'])) {
			responseApiJson(0, '支付密码格式不正确', '');
		}
		$buyer_info = Model('member')->getMemberInfoByID($this->member_info['member_id'], 'member_paypwd');
		if ($buyer_info['member_paypwd'] != '') {
			if ($buyer_info['member_paypwd'] === md5($_POST['password'])) {
				responseApiJson(1, '支付密码验证成功', '');
			}
		}
		responseApiJson(0, '支付密码验证失败', '');
	}


    /**
     * 购物车、直接购买第一步:选择收获地址和配置方式
     */
    public function buy_step1Op() {
        $cart_id = explode(',', $_GET['cart_id']);
        $logic_buy = logic('buy');

        //得到会员等级
//        $model_member = Model('member');
//        $member_info = $model_member->getMemberInfoByID($this->member_info['member_id']);
        $member_extend_model = Model('member_extend');
        $member_info = $member_extend_model->getMemberExtendInfo(array('member_id'=>$this->member_info['member_id']),'*','union');
//		if (!$member_info['is_buy']) {
//			responseApiJson(0, '', '您没有商品购买的权限,如有疑问请联系客服人员');
//		}

        if (empty($_POST['ifcart'])) {
            $if_cart = $_GET['ifcart'];
        } else {
            $if_cart = $_POST['ifcart'];
        }
//        if ($member_info) {
//            $member_gradeinfo = $model_member->getOneMemberGrade(intval($member_info['member_exppoints']));
//            $member_discount = $member_gradeinfo['orderdiscount'];
//            $member_level = $member_gradeinfo['level'];
//        } else {
//            $member_discount = $member_level = 0;
//        }

        $member_level =$member_info['member_level'];
        $member_discount = Model('level')->getMemberDiscount($member_level);
        //得到购买数据
        $result = $logic_buy->buyStep1($cart_id, $if_cart, $this->member_info['member_id'], $this->member_info['store_id'], null, $member_discount, $member_level);
//		echo '<pre>';
//		var_dump($result);
//		exit;
        if (!$result['state']) {
            responseApiJson(0, $result['msg'], '');
        } else {
            $result = $result['data'];
        }

        if (intval($_GET['address_id']) > 0) {
            $result['address_info'] = Model('address')->getDefaultAddressInfo(array('address_id' => intval($_GET['address_id']), 'member_id' => $this->member_info['member_id']));
        }
        if ($result['address_info']) {
            $data_area = $logic_buy->changeAddr($result['freight_list'], $result['address_info']['city_id'], $result['address_info']['area_id'], $this->member_info['member_id']);
            if (!empty($data_area) && $data_area['state'] == 'success') {
                if (is_array($data_area['content'])) {
                    foreach ($data_area['content'] as $store_id => $value) {
                        $data_area['content'] = ncPriceFormat($value);
                    }
                }else{
                    $data_area['content'] = "";
                }
            }
        }

        //整理数据
        $store_cart_list = array();
        $store_total_list = $result['store_goods_total_1'];
        $order_total_amount = 0;
//		echo '<pre>';
//		var_dump($result['store_cart_list']);
//		exit;
        foreach ($result['store_cart_list'] as $key => $value) {
            foreach ($value as $k=>$va){
                $goods_freight_arr[]=$va['goods_freight'];
            }
            rsort($goods_freight_arr);
            $goods_freight=$goods_freight_arr[0];
            $store_cart_list[$key]['goods_list'] = $value;
            $store_cart_list[$key]['store_goods_total'] = $result['store_goods_total'][$key];
            $order_total_amount += $store_cart_list[$key]['store_goods_total'];
            $store_cart_list[$key]['store_mansong_rule_list'] = $result['store_mansong_rule_list'][$key];

            if (is_array($result['store_voucher_list'][$key]) && count($result['store_voucher_list'][$key]) > 0) {
                reset($result['store_voucher_list'][$key]);
                $store_cart_list[$key]['store_voucher_info'] = current($result['store_voucher_list'][$key]);
                $store_cart_list[$key]['store_voucher_info']['voucher_price'] = ncPriceFormat($store_cart_list[$key]['store_voucher_info']['voucher_price']);
                $store_total_list[$key] -= $store_cart_list[$key]['store_voucher_info']['voucher_price'];
            } else {
                $store_cart_list[$key]['store_voucher_info'] = array();
            }

            $store_cart_list[$key]['store_voucher_list'] = $result['store_voucher_list'][$key];
            if (!empty($result['cancel_calc_sid_list'][$key])) {
                $store_cart_list[$key]['freight'] = '0';
                $store_cart_list[$key]['freight_message'] = $result['cancel_calc_sid_list'][$key]['desc'];
            } else {
                $store_cart_list[$key]['freight'] = '1';
            }
            $store_cart_list[$key]['store_name'] = $value[0]['store_name'];
        }
        $buy_list = array();
        $buy_list['store_cart_list'] = $store_cart_list;
        $buy_list['freight_hash'] = $result['freight_list'];
        $buy_list['address_info'] = $result['address_info'];
        $buy_list['ifshow_offpay'] = $result['ifshow_offpay'];
        $buy_list['vat_hash'] = $result['vat_hash'];
        $buy_list['inv_info'] = $result['inv_info'];
        $buy_list['available_predeposit'] = $result['available_predeposit'];
        $buy_list['available_rc_balance'] = $result['available_rc_balance'];
        if (is_array($result['rpt_list']) && !empty($result['rpt_list'])) {
            foreach ($result['rpt_list'] as $k => $v) {
                unset($result['rpt_list'][$k]['rpacket_id']);
                unset($result['rpt_list'][$k]['rpacket_end_date']);
                unset($result['rpt_list'][$k]['rpacket_owner_id']);
                unset($result['rpt_list'][$k]['rpacket_code']);
            }
        }
        $buy_list['rpt_list'] = $result['rpt_list'] ? $result['rpt_list'] : array();
        $buy_list['zk_list'] = $result['zk_list'];

//        if ($data_area['content']) {
        //            $store_total_list = Logic('buy_1')->reCalcGoodsTotal($store_total_list,$data_area['content'],'freight');
        //            //返回可用平台红包
        //            $result['rpt_list'] = Logic('buy_1')->getStoreAvailableRptList($this->member_info['member_id'],array_sum($store_total_list),'rpacket_limit desc');
        //            reset($result['rpt_list']);
        //            if (is_array($result['rpt_list']) && count($result['rpt_list']) > 0) {
        //                $result['rpt_info'] = current($result['rpt_list']);
        //                unset($result['rpt_info']['rpacket_id']);
        //                unset($result['rpt_info']['rpacket_end_date']);
        //                unset($result['rpt_info']['rpacket_owner_id']);
        //                unset($result['rpt_info']['rpacket_code']);
        //            }
        //        }
        $res =$member_discount * number_format($order_total_amount + $goods_freight, 2, '.', '');
        $buy_list['order_amount'] = $res + array_sum((array)$store_total_list);
        $buy_list['rpt_info'] = $result['rpt_info'] ? $result['rpt_info'] : array();
        $buy_list['address_api'] = $data_area ? $data_area : (object)array();
        $buy_list['shipping_fee'] = $goods_freight;
        //$buy_list['order_amount']=$buy_list['order_amount']+$goods_freight;
        //        foreach ($store_total_list as $store_id => $value) {
        //            $store_total_list[$store_id] = ncPriceFormat($value);
        //        }
        //        $buy_list['store_final_total_list'] = $store_total_list;
        responseApiJson(1, '', $buy_list);
    }


    /**
     * 购物车、直接购买第二步:保存订单入库，产生订单号，开始选择支付方式
     *
     */
    public function buy_step2Op() {
        $param = array();
        $param['ifcart'] = $_POST['ifcart'];
        $param['cart_id'] = explode(',', $_POST['cart_id']);
        $param['address_id'] = $_POST['address_id'];
//        $param['vat_hash'] = $_POST['vat_hash'];
        $param['offpay_hash'] = $_POST['offpay_hash'];
        $param['offpay_hash_batch'] = $_POST['offpay_hash_batch'];
        $param['pay_name'] = $_POST['pay_name'];
        $param['invoice_id'] = $_POST['invoice_id'];
        $param['rpt'] = $_POST['rpt'];

        //处理代金券
                $voucher = array();
                $post_voucher = explode(',', $_POST['voucher']);
                if(!empty($post_voucher)) {
                    foreach ($post_voucher as $value) {
                        list($voucher_t_id, $store_id, $voucher_price) = explode('|', $value);
                        $voucher[$store_id] = $value;
                    }
                }
                $param['voucher'] = $voucher;

        $_POST['pay_message'] = trim($_POST['pay_message'],',');
                $_POST['pay_message'] = explode(',',$_POST['pay_message']);
                $param['pay_message'] = array();
                if (is_array($_POST['pay_message']) && $_POST['pay_message']) {
                    foreach ($_POST['pay_message'] as $v) {
                        if (strpos($v, '|') !== false) {
                            $v = explode('|', $v);
                            $param['pay_message'][$v[0]] = $v[1];
                        }
                    }
                }
        $param['pay_message'][1] = $_POST['buyer_message'];
        $param['pd_pay'] = $_POST['pd_pay'];
        $param['rcb_pay'] = $_POST['rcb_pay'];
        $param['password'] = $_POST['password'];
        $param['fcode'] = $_POST['fcode'];
        $param['order_from'] = 2;
        $logic_buy = logic('buy');

        //得到会员等级
        $model_member = Model('member');
        $model_member_extend = Model('member_extend');
        $member_info = $model_member->getMemberInfoByID($this->member_info['member_id']);
        $member_level = $model_member_extend->getMemberLevel($member_info['member_id']);
        $result = $logic_buy->buyStep2($param, $this->member_info['member_id'], $this->member_info['member_name'], $this->member_info['member_email'], $member_level);
		if (!$result['state']) {
            responseApiJson(0, $result['msg'], '');
        }
//        $result['data']['pay_sn']['is_bd_product'] = $result['data']['goods_list']['is_bd_product'];
        $result['data']['is_bd_product_num'] = 0;
        foreach ($result['data']['goods_list'] as $v){
            $result['data']['is_bd_product_num'] += ($v['goods_num'] * $v['is_bd_product']);
        }
        $result['data']['is_bd_product_num'] = (string) $result['data']['is_bd_product_num'];
        //$order_info = current($result['data']['order_list']);
        $this->payOp($result['data']['pay_sn'],$result['data']['is_bd_product_num']);
        //responseApiJson(1,'',array('pay_sn' => $result['data']['pay_sn'],'payment_code'=>$order_info['payment_code']));
    }
}
