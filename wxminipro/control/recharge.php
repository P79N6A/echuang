<?php
/**
 * 账户充值
 *
 *
 *
 *
 *
 */

defined('In33hao') or exit('Access Invalid!');

class rechargeControl extends apiEncryptMemberControl {

	public function __construct() {
		parent::__construct();
	}

	/**
	 * 申请提现
	 */
	public function withdraw_addOp() {
		$obj_validate = new Validate();
		$pdc_amount = abs(floatval($_POST['pdc_amount']));
		if (intval($pdc_amount) < intval(C('withdraw_rule1'))) {
			responseApiJson(0, '满' . C('withdraw_rule1') . '可提', '');
		}
		$mole = intval($pdc_amount) % 100;
		if ($mole != 0) {
			responseApiJson(0, '整百可提', '');
		}
		$member_id = $this->member_info['member_id'];
		$model_member_verify = Model('member_verify');
		$verify_info = $model_member_verify->where(array('member_id' => $member_id))->find();
		if (empty($verify_info)) {
			responseApiJson(0, '请到网页端实名认证', '');
		}
		if ($verify_info == 20 || $verify_info == 30) {
			responseApiJson(0, '您的实名认证还未通过', '');
		}
		$validate_arr[] = array("input" => $pdc_amount, "require" => "true", 'validator' => 'Compare', 'operator' => '>=', "to" => '0.01', "message" => Language::get('predeposit_cash_add_pricemin_error'));
		$validate_arr[] = array("input" => $_POST["pdc_bank_name"], "require" => "true", "message" => Language::get('predeposit_cash_add_shoukuanbanknull_error'));
		$validate_arr[] = array("input" => $_POST["pdc_bank_no"], "require" => "true", "message" => Language::get('predeposit_cash_add_shoukuanaccountnull_error'));
		$validate_arr[] = array("input" => $_POST["pdc_bank_user"], "require" => "true", "message" => Language::get('predeposit_cash_add_shoukuannamenull_error'));
		$validate_arr[] = array("input" => $_POST["password"], "require" => "true", "message" => '请输入支付密码');
		$obj_validate->validateparam = $validate_arr;
		$error = $obj_validate->validate();
		if ($error != '') {
			responseApiJson(0, $error, '');
		}
		$model_member_bonus = Model('member_bonus');
		$model_member = Model('member');
		$member_info = $model_member->table('member')->where(array('member_id' => $this->member_info['member_id']))->master(true)->lock(true)->find(); //锁定当前会员记录 v5.5
		// 验证支付密码
		if (md5($_POST['password']) != $member_info['member_paypwd']) {
			responseApiJson(0, '支付密码错误', '');
		}
		// 验证可提金额是否足够
		$info = $this->member_info;
		$change_type = "";

		if ($_POST['bonus_type'] == "shop_bonus") {
			$bonus_available = $info['shop_bonus'] - $info['shop_bonus_payed'];
			$sql = "select SUM(pdc_amount) as withdrawed from shanhao_pd_cash WHERE pdc_member_id='" . $_SESSION['member_id'] . "' and pdc_type='shop_bonus'";
			$result = Model('member_bonus')->query($sql);
			$shop_bonus_withdrawed = $result[0]['withdrawed'];
			$bonus_withdraw = $info['shop_bonus'] - $shop_bonus_withdrawed;
			if ($bonus_withdraw < $pdc_amount) {
				responseApiJson(0, "商城奖金可提金额不足", '');
			}
			$change_type = "shop_withdraw";
		} elseif ($_POST['bonus_type'] == "store_bonus") {
			$bonus_available = $info['store_ally_bonus'] + $info['store_city_bonus'] - $info['store_bonus_payed'];
			//月初
			$firstday = strtotime(date('Y-m-01 00:00:00'));
			$sql = "SELECT SUM(pdc_amount) as withdrawed FROM shanhao_pd_cash WHERE pdc_add_time >= " . $firstday . " AND pdc_add_time <= " . time() . " and pdc_type='store_bonus'";
			$result = Model('member_bonus')->query($sql);
			$store_bonus_withdrawed = $result[0]['withdrawed'];
			$bonus_withdraw = min($info['store_ally_bonus'] + $info['store_city_bonus'], $info['store_ally_bonus_quato'] + $info['store_city_bonus_quato']) - $store_bonus_withdrawed;

			if ($bonus_withdraw < $pdc_amount) {
				responseApiJson(0, "店铺管理奖金可提金额不足", '');
			}

			$change_type = "store_withdraw";
		} elseif ($_POST['bonus_type'] == "global_bonus") {
			$bonus_available = $info['member_global_bonus'] + $info['agent_global_bonus'] - $info['global_bonus_payed'];
			$sql = "select SUM(pdc_amount) as withdrawed from shanhao_pd_cash WHERE pdc_member_id='" . $_SESSION['member_id'] . "' and pdc_type='global_bonus'";
			$result = Model('member_bonus')->query($sql);
			$global_bonus_withdrawed = $result[0]['withdrawed'];
			$bonus_withdraw = $bonus_available - $global_bonus_withdrawed;
			if ($bonus_withdraw < $pdc_amount) {
				responseApiJson(0, "全球分红可提金额不足", '');
			}
			$change_type = "global_withdraw";
		} elseif ($_POST['bonus_type'] == "card_bonus") {
			$bonus_available = $info['member_card_bonus'] - $info['member_card_bonus_payed'];
			$sql = "select SUM(pdc_amount) as withdrawed from shanhao_pd_cash WHERE pdc_member_id='" . $_SESSION['member_id'] . "' and pdc_type='card_bonus'";
			$result = Model('member_bonus')->query($sql);
			$card_bonus_withdrawed = $result[0]['withdrawed'];
			$bonus_withdraw = min($info['member_card_bonus'], $info['member_card_bonus_quato']) - $card_bonus_withdrawed;
			if ($bonus_withdraw < $pdc_amount) {
				responseApiJson(0, "会员卡可提金额不足", '');
			}
			$change_type = "card_withdraw";
		}
		try {
			$model_member_bonus->beginTransaction();
			$pdc_sn = $model_member_bonus->makeSn();
			$data = array();
			$data['pdc_sn'] = $pdc_sn;
			$data['pdc_member_id'] = $member_info['member_id'];
			$data['pdc_member_name'] = $member_info['member_name'];
			$data['pdc_amount'] = $pdc_amount;
			$data['pdc_bank_name'] = $_POST['pdc_bank_name'];
			$data['pdc_bank_no'] = $_POST['pdc_bank_no'];
			$data['pdc_bank_user'] = $_POST['pdc_bank_user'];
			$data['pdc_add_time'] = TIMESTAMP;
			$data['pdc_payment_state'] = 0;
			$data['mobilenum'] = $member_info['member_mobile'];
			$data['pdc_type'] = $_POST['bonus_type'];
			$insert = $model_member_bonus->addPdCash($data);
			if (!$insert) {
				throw new Exception('提现信息添加失败');
			}
			// 增加修改记录
			$withdraw_data = array();
			$withdraw_data['member_id'] = $member_info['member_id'];
			$withdraw_data['member_name'] = $member_info['member_name'];
			$withdraw_data['amount'] = $pdc_amount;
			$withdraw_data['order_sn'] = $pdc_sn;
			Model('bonus')->changeBonus($model_member_bonus, $change_type, $withdraw_data);
			$model_member_bonus->commit();
			responseApiJson(1, "您的提现申请已成功提交，请等待系统处理", '');
		} catch (Exception $e) {
			$model_member_bonus->rollback();
			responseApiJson(0, $e->getMessage(), '');
		}
	}
}
