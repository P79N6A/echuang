<?php

/**
 * 分红逻辑
 */
defined('In33hao') or exit('Access Invild!');
class dividendLogic {
	/**
	 * staticDividends 静态分红
	 * @return [type] [description]
	 */
	public function staticDividends() {
		$model_member_extend = Model('member_extend');
		// 获取所有会员信息
		$member_list = $model_member_extend->getAllMemberExtendList('member_id,member_equity,member_state,member_name');

		$r_log_data = array();
		$r_log_data['bean_type'] = 'dividend';
		foreach ($member_list as $info) {
			if ($info['member_equity'] > 0 && $info['member_state']) {
				$rebate_amount = $info['member_equity'] * C('static_income_rebate_percentage') / 100.00;
				if ($rebate_amount < 0.01) {
					continue;
				}
				$r_log_data['amount'] = floatFormat($rebate_amount);
				$r_log_data['member_id'] = $info['member_id'];
				$r_log_data['member_name'] = $info['member_name'];
				$model_member_extend->changeBean('rebate', $r_log_data);
			}
		}

	}

	/**
	 * bonusDailyKnot 奖金日结
	 * @return [type] [description]
	 */
	public function bonusDailyKnot() {
		$model_bean = Model('bean');
		$model_member_extend = Model('member_extend');
		$nowtime = TIMESTAMP;
		$bean_type = array('dividend', 'promotion', 'extend');

		try {
			Model()->beginTransaction();
			$condition = array();
			$condition['rl_isclear'] = 0;

			// 返利表以rl_member_id进行分组
			$member_list = $model_bean->getBeanRebateLogGroupList('rl_member_id', $condition, 'rl_member_id');
			foreach ($member_list as $info) {
				$g_member_id = $info['rl_member_id'];
				$g_info = $model_member_extend->getMemberExtendInfo(array('me_member_id' => $g_member_id), 'member_name,member_equity', 'union');
				if (empty($g_info)) {
					throw Exception('获取' . $g_member_id . "信息失败");
				}
				$condition['rl_member_id'] = $g_member_id;
				// 日结记录
				$bdl_log_data = array();
				$bdl_log_data['bdl_member_id'] = $g_member_id;
				$bdl_log_data['bdl_member_name'] = $g_info['member_name'];

				// 金豆记录
				$log_data = array();
				$log_data['lg_member_id'] = $g_member_id;
				$log_data['lg_member_name'] = $g_info['member_name'];
				$bdl_log_data['bdl_addtime'] = $nowtime;

				// 其他返利金额
				$other_rebate_amount = 0;
				// 分红金额
				$dividend_amount = 0;
				// 奖金描述
				$desc = '';
				// 判断每种奖金总金额
				foreach ($bean_type as $type) {
					$condition['rl_type'] = $type;
					$sumAmount = $model_bean->getBeanRebateSumAmount($condition);
					$sumAmount = $sumAmount ? $sumAmount : 0;
					$bdl_log_data['bdl_' . $type . '_amount'] = $sumAmount;
					if ($sumAmount > 0) {
						if ($type == 'dividend') {
							$dividend_amount = $sumAmount;
							$desc .= '商城赠送:' . $sumAmount;
						} else {
							$other_rebate_amount += $sumAmount;
							$desc .= replaceBeanType($type) . ':' . $sumAmount . ' ';
						}
					}
				}
				// 插入日结记录
				$model_bean->addBeanDayLog($bdl_log_data);

				$sn = makeSN('rebate', $g_member_id);
				$amount = $other_rebate_amount <= $g_info['member_equity'] ? $other_rebate_amount : $g_info['member_equity'];
				$amount = $dividend_amount + $amount;

				$log_data['lg_addtime'] = $nowtime;
				$log_data['lg_type'] = 'rebate';
				$log_data['lg_sn'] = $sn;
				$log_data['lg_bean_amount'] = floatFormat(($model_member_extend->getAvailBeanById($g_member_id)) + $amount);
				$log_data['lg_amount'] = $amount;
				$log_data['lg_desc'] = $desc;
				$insert_id = $model_bean->addBeanLog($log_data);
				$model_member_extend->setIncMemberExtendField(array('me_member_id' => $g_member_id), array('member_golden_bean' => $amount));

			}
			$log_list = $model_bean->getAllNoClearBeanRebateLogList();
			if (!empty($log_list)) {
				foreach ($log_list as $v) {
					$model_bean->editBeanRebateLog(array('rl_id' => $v['rl_id']), array('rl_isclear' => 1));
				}
			}

			Model()->commit();
			return true;
		} catch (Exception $exception) {
			Model()->rollback();
			return array('error' => $exception->getMessage());
		}
	}

	/**
	 * capping 封顶复投操作
	 * @param  [type] $member_id 会员ID
	 * @return [type]            [description]
	 */
	public function capping($member_id) {
		$consume_condition['cl_member_id'] = $member_id;
		$consume_condition['cl_capping_state'] = 0;
		$consume_sum = Model('bean')->getBeanConsumeSum($consume_condition);
		$consume_sum = $consume_sum ? $consume_sum : 0;
		$dividend_condition['flg_member_id'] = $member_id;
		$dividend_condition['flg_capping_state'] = 0;
		$dividend_sum = Model('float_dividend')->getFloatDividendAmount($dividend_condition);
		$dividend_sum = $dividend_sum ? $dividend_sum : 0;

		// 达到封顶条件
		if ($dividend_sum > 0) {
			if ($dividend_sum >= $consume_sum * C('dividend_consume_multiple')) {
				try {
					Model()->beginTransaction();
					// 更改分红记录封顶状态
					$dividend_list = Model('float_dividend')->getFloatDividendLogList($dividend_condition, '*', null, 'flg_addtime desc', false);
					if (!empty($dividend_list)) {
						foreach ($dividend_list as $info) {
							$flag = Model('float_dividend')->setDividendFieldValue(array('flg_id' => $info['flg_id']), 'flg_capping_state', 1);
							if (!$flag) {
								throw new Exception("更改分红封顶状态失败");
							}
						}
					}
					// 更改消费记录封顶状态
					$consume_list = Model('bean')->getBeanConsumeLogList($consume_condition, '*', null, 'cl_addtime desc', false);
					if (!empty($consume_list)) {
						foreach ($consume_list as $info) {
							$flag = Model('bean')->setBeanConsumeFieldValue(array('cl_id' => $info['cl_id']), 'cl_capping_state', 1);
							if (!$flag) {
								throw new Exception("更改消费记录失败");
							}
						}
					}
					$info = Model('member_extend')->getMemberExtendInfo(array('me_member_id' => $member_id), '*', 'union');
					if (empty($info)) {
						throw new Exception("会员信息查询失败");
					}

					// 添加金券封顶记录
					$new_equity = 0;
					$new_total_consume = 0;
					$data['add_time'] = TIMESTAMP;
					$data['origin_equity'] = $info['member_equity'];
					$data['new_equity'] = $new_equity;
					$data['origin_total_consume'] = $info['total_consume'];
					$data['new_total_consume'] = $new_total_consume;
					$data['member_id'] = $member_id;
					$flag = Model('equity_capping')->addEquityCappingLog($data);
					if (!$flag) {
						throw new Exception("金券封顶记录添加失败");
					}

					// 更改会员金豆消费总额
					if ($info['total_consume'] > 0) {
						$flag = Model('member_extend')->setMemberExtendFieldValue(array('me_member_id' => $member_id), 'total_consume', $new_total_consume);
						if (!$flag) {
							throw new Exception("更改会员金豆消费总额失败");
						}
						Log::memberRecord('会员' . $info['member_mobile'] . '消费总额减少' . $info['total_consume'] - $new_total_consume, $member_id);
					}

					// 清空当前会员金券
					if ($info['member_equity'] > 0) {
						$flag = Model('member_extend')->setMemberExtendFieldValue(array('me_member_id' => $member_id), 'member_equity', $new_equity);
						if (!$flag) {
							throw new Exception("会员金券清空失败");
						}
						$log['elg_amount'] = $new_equity - $info['member_equity'];
						$log['elg_member_id'] = $member_id;
						$log['elg_member_name'] = $info['member_name'];
						$log['elg_type'] = 'capping';
						$log['elg_addtime'] = TIMESTAMP;
						$log['elg_sn'] = makeSN('capping', $member_id);
						$log['elg_equity_amount'] = $new_equity;
						$log['elg_desc'] = "会员" . $member_id . "金券封顶复投";
						Model('equity')->addEquityLog($log);
						Log::memberRecord('会员' . $info['member_mobile'] . '会员金券减少' . $info['member_equity'] - $new_equity, $member_id);
					}

					Model()->commit();
					Log::memberRecord("会员" . $member_id . "金券被封顶\n", $member_id);
					return array('success' => "封顶成功");
				} catch (Exception $e) {
					Model()->rollback();
					Log::record("会员" . $member_id . "封顶失败:" . $e->getMessage() . "\n", 'LOG');
					return array('error' => $e->getMessage());
				}
			}
		}

	}
}
