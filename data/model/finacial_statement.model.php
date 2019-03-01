<?php
defined('In33hao') or exit('Access Invalid!');
/**
 * 财务报表
 */
class finacial_statementModel extends Model {

	/**
	 * getFinacialStatementList 取财务报表列表
	 * @param  array  $condition [description]
	 * @param  string $field     [description]
	 * @param  [type] $page      [description]
	 * @param  string $order     [description]
	 * @param  string $limit     [description]
	 * @return [type]            [description]
	 */
	public function getFinacialStatementList($condition = array(), $field = '*', $page = null, $order = 'fs_addtime desc', $limit = '') {
		return $this->table('financial_statement')->field($field)->where($condition)->page($page)->order($order)->limit($limit)->select();
	}

	/**
	 * getFinacialStatementInfo 获取财务报表信息
	 * @param  [type] $condition [description]
	 * @param  string $field     [description]
	 * @return [type]            [description]
	 */
	public function getFinacialStatementInfo($condition, $field = '*') {
		return $this->table('financial_statement')->field($field)->where($condition)->find();
	}

	/**
	 * addBeanLog 添加财务报表
	 * @param [type] $data 财务报表数据
	 */
	public function addFinacialStatement($data) {
		return $this->table('financial_statement')->insert($data);

	}

	/**
	 * editBeanLog 更新财务报表
	 * @param  [type] $condition [description]
	 * @param  [type] $data      [description]
	 * @return [type]            [description]
	 */
	public function editFinacialStatement($condition, $data) {
		return $this->table('financial_statement')->where($condition)->update($data);
	}

	/**
	 * getTotalConsumeAmount 获取总消费额
	 * @param  [type] $condition [description]
	 * @return [type]            [description]
	 */
	public function getTotalConsumeAmount($condition) {
		return $this->table('financial_statement')->where($condition)->sum('bean_consume_amount');
	}

	/**
	 * getTotalBonusAmount 获取总奖金额
	 * @param  [type] $condition [description]
	 * @return [type]            [description]
	 */
	public function getTotalBonusAmount($condition) {
		return $this->table('financial_statement')->where($condition)->sum('bonus_amount');
	}

	/**
	 * getFinacialStatementArr 获取统计数组
	 * @param  [type] $condition [description]
	 * @return [type]            [description]
	 */
	public function getFinacialStatementArr($condition) {
		$statement_arr['member_regnum'] = Model('member_extend')->getNewMemberNum(array('reg_time' => $condition));
		$statement_arr['consume_num'] = Model('bean')->getBeanConsumeNum(array('cl_addtime' => $condition));
		$consume_amount = Model('bean')->getBeanConsumeAmount(array('cl_addtime' => $condition));
		$statement_arr['bean_consume_amount'] = ncPriceFormat($consume_amount);
		$statement_arr['consume_item_num'] = Model('bean')->getBeanConsumeItemNum(array('cl_addtime' => $condition));
		$statement_arr['bean_recharge_amount'] = ncPriceFormat(Model('bean')->getBeanRechargeAmount(array('lg_addtime' => $condition)));
		$statement_arr['bean_donate_amount'] = ncPriceFormat(Model('bean')->getBeanDonateAmount(array('dl_addtime' => $condition)));
		$statement_arr['bean_donate_num'] = Model('bean')->getBeanCountNum(array('lg_addtime' => $condition), 'donate');
		$statement_arr['withdraw_amount'] = ncPriceFormat(Model('bean')->getBeanWithdrawAmount(array('wl_addtime' => $condition)));
		$statement_arr['withdraw_stay_amount'] = ncPriceFormat(Model('bean')->getBeanWithdrawStayAmount(array('wl_addtime' => $condition)));
		$statement_arr['withdraw_fee_amount'] = ncPriceFormat(Model('bean')->getBeanWithdrawFeeAmount(array('wl_addtime' => $condition)));
		// 修改奖金统计

		$statement_arr['static_dividend_amount'] = ncPriceFormat(Model('bean')->getStaticToatalAmount(array('bdl_addtime' => $condition)));
		$statement_arr['promotion_bonus_amount'] = ncPriceFormat(Model('bean')->getPromotionTotalAmount(array('bdl_addtime' => $condition)));
		$statement_arr['extend_bonus_amount'] = ncPriceFormat(Model('bean')->getExtendTotalAmount(array('bdl_addtime' => $condition)));
		$bonus_amount = $statement_arr['static_dividend_amount'] + $statement_arr['promotion_bonus_amount'] + $statement_arr['extend_bonus_amount'];
		$statement_arr['bonus_amount'] = ncPriceFormat($bonus_amount);
		$statement_arr['float_dividend_amount'] = ncPriceFormat(Model('float_dividend')->getFloatDividendAmount(array('flg_addtime' => $condition)));
		$statement_arr['new_service_center_num'] = Model('service')->getNewServiceNum(array('slg_audittime' => $condition));

		$dail_ratio = ncPriceFormat($bonus_amount / $consume_amount) * 100;
		$statement_arr['dail_ratio'] = $dail_ratio;
		$statement_arr['equity_amount'] = ncPriceFormat(Model('equity')->getConsumeEquityAmount(array('elg_addtime' => $condition)));
		$statement_arr['silver_recharge_amount'] = ncPriceFormat(Model('bean')->getSilverAmount(array('slg_addtime' => $condition), 'admin'));
		$statement_arr['silver_donate_amount'] = ncPriceFormat(Model('bean')->getSilverDonateAmount(array('slg_addtime' => $condition)));

		$statement_arr['silver_donate_num'] = Model('bean')->getSilverDonateCountNum(array('slg_addtime' => $condition));

		$statement_arr['bean_present_amount'] = ncPriceFormat(Model('bean')->getBeanAmount(array('lg_addtime' => $condition), 'present'));

		$statement_arr['bean_pay_amount'] = ncPriceFormat(abs(Model('bean')->getBeanAmount(array('lg_addtime' => $condition), 'order_pay')));
		$statement_arr['bean_pay_num'] = Model('bean')->getBeanCountNum(array('lg_addtime' => $condition), 'order_pay');
		$statement_arr['silver_pay_amount'] = ncPriceFormat(abs(Model('bean')->getSilverAmount(array('slg_addtime' => $condition), 'order_pay')));
		$statement_arr['silver_pay_num'] = Model('bean')->getSilverCountNum(array('slg_addtime' => $condition), 'order_pay');

		$statement_arr['wx_pay_amount'] = ncPriceFormat(Model('order')->getWxPayAmount(array('add_time' => $condition)));
		$statement_arr['wx_pay_num'] = Model('order')->getWxPayCountNum(array('add_time' => $condition));
		$statement_arr['ali_pay_amount'] = ncPriceFormat(Model('order')->getAliPayAmount(array('add_time' => $condition)));
		$statement_arr['ali_pay_num'] = Model('order')->getAliPayCountNum(array('add_time' => $condition));
		return $statement_arr;
	}
}