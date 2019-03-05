<?php
/**
 * 会员扩展模块
 */

defined('In33hao') or exit('Access Invild!');
class member_extendModel extends Model {
    private $level_ratio;

	public function __construct() {
		parent::__construct();
		$this->_getLevelRatio();
	}

	private function _getLevelRatio(){
	    $level_ratio = Model('member_level')->field('ml_id,ml_discount_ratio')->select();
	    foreach ($level_ratio as $v){
	        $this->level_ratio[$v['ml_id']] = $v['ml_discount_ratio'];
        }
    }
	public function editMemberLevel($condition, $data){
	    //获取会员当前等级
        $res = $this->table('member_extend')->where($condition)->field('member_level')->find();
       // var_dump($res,$data);
        $now_level = $res['member_level'];
        $update = $this->table('member_extend')->where($condition)->update($data);
        $up_log_data['member_id'] = $condition['me_member_id'];
        $member_info = Model('member_extend')->getMemberExtendInfo(array('member_id'=>$up_log_data['member_id']),'*','union');
        $up_log_data['member_name'] = $member_info['member_name'];
        $up_log_data['member_level'] = $member_info['member_level'];
        $up_log_data['before_level'] = $now_level;
        $up_log_data['up_time'] = time();
        Model('level')->addUpLevelRecord($up_log_data);
        Logic('member_level')->changeTeamNum($condition['me_member_id'],$now_level,$data['member_level']);
        return $update;
    }

	/**
	 * getInviteFamilyTreeArr 获取邀请人族谱
	 * @param  [type] $member_id [description]
	 * @return [type]            [description]
	 */
	public function getInviteFamilyTreeArr($member_id, $fields = '*') {
		$arr = array();
		$member_info = $this->getMemberExtendInfo(array('member_id' => $member_id), $fields, 'union');
		$extend_info = $this->getMemberExtendList(array('invite_id' => $member_id), '*', '', null, 'reg_time asc');
//        var_dump($extend_info);die();
        if (empty($extend_info)) {
            $member_info['child'] = array();
        } else {
            $info = array();
            foreach ($extend_info as $value) {
                $info[] = $this->getInviteFamilyTreeArr($value['me_member_id'], $fields);
            }
            $member_info['child'] = $info;
        }
		$arr = $member_info;
		return $arr;
	}

	/**
	 * getAccessFamilyTreeArr 获取接点人族谱
	 * @param  [type] $member_id [description]
	 * @return [type]            [description]
	 */
	public function getAccessFamilyTreeArr($member_id, $fields = '*') {
		$arr = array();
		$member_info = $this->getMemberExtendInfo(array('member_id' => $member_id), $fields, 'union');
		$extend_info = $this->getMemberExtendList(array('access_id' => $member_id), '*', '', null, 'reg_time asc');
		if (empty($extend_info)) {
			$member_info['child'] = array();
		} else {
			$info = array();
			foreach ($extend_info as $value) {
				$info[] = $this->getAccessFamilyTreeArr($value['me_member_id'], $fields);
			}
			$member_info['child'] = $info;
		}
		$arr = $member_info;
		return $arr;
	}

	/**
	 * getInviteChildMobileArr 获取推荐下级手机号数组
	 * @param  [type] $member_id [description]
	 * @return [type]            [description]
	 */
	public function getInviteChildMobileArr($member_id) {
		$mobile_info = array();
		$invite[] = $this->getInviteFamilyTreeArr($member_id, 'member_mobile');
		$this->_parseChildFieldInfo($invite, $mobile_info, 'member_mobile');
		return $mobile_info;
	}

	/**
	 * getAccessChildMobileArr 获取接点下级手机号数组
	 * @param  [type] $member_id [description]
	 * @return [type]            [description]
	 */
	public function getAccessChildMobileArr($member_id) {
		$mobile_info = array();
		$access[] = $this->getAccessFamilyTreeArr($member_id, 'member_mobile');
		$this->_parseChildFieldInfo($access, $mobile_info, 'member_mobile');
		return $mobile_info;
	}

	/**
	 * getInviteParentMemberIdArr 获取推荐上级会员ID数组
	 * @param  [type] $member_id [description]
	 * @return [type]            [description]
	 */
	public function getInviteParentMemberIdArr($member_id) {
		$member_id_arr = array();
		$invite_arr = $this->getInviteParentArr($member_id, 'member_id');
		$this->_parseParentFieldInfo($invite_arr, $member_id_arr, 'member_id');
		return $member_id_arr;
	}

	/**
	 * getAccessParentMemberIdArr 获取接点上级会员ID数组
	 * @param  [type] $member_id [description]
	 * @return [type]            [description]
	 */
	public function getAccessParentMemberIdArr($member_id) {
		$member_id_arr = array();
		$access_arr = $this->getAccessParentArr($member_id, 'member_id');
		$this->_parseParentFieldInfo($access_arr, $member_id_arr, 'member_id');
		return $member_id_arr;
	}

	/**
	 * changeBean 更改会员金豆
	 * @param  [type] $type [description]
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	public function changeBean($type, $data) {
		if (!in_array($type, array('rebate', 'consume', 'donate', 'recharge', 'withdraw', 'admin', 'present'))) {
			return array('error' => '参数错误');
		}

		$member_id = $data['member_id'];
		$amount = floatFormat($data['amount']);
		$member_name = $data['member_name'];
		$addtime = TIMESTAMP;
		$sn = makeSN($type, $member_id);
		$info = $this->getMemberExtendInfo(array('me_member_id' => $member_id), "*", 'union');
		if (empty($info)) {
			return array('error' => '会员信息错误');
		}

		try {
			$model_bean = Model('bean');
			$log_data = array();
			$log_data['lg_member_id'] = $member_id;
			$log_data['lg_member_name'] = $member_name;
			$log_data['lg_addtime'] = $addtime;
			$log_data['lg_type'] = $type;
			$log_data['lg_sn'] = $sn;
			$condition = array('me_member_id' => $member_id);
			Model()->beginTransaction();
			switch ($type) {
			// 返利
			case 'rebate':
				$bean_type = $data['bean_type'];
				$rl_log_data = array();
				$rl_log_data['rl_member_id'] = $member_id;
				$rl_log_data['rl_type'] = $bean_type;
				$rl_log_data['rl_amount'] = $amount;
				$rl_log_data['rl_addtime'] = $addtime;
				$rl_log_data['rl_sn'] = $sn;
				if ($bean_type == 'promotion') {
					// 推广奖
					$rl_log_data['rl_desc'] = "会员" . $info['member_mobile'] . "拿到" . $data['level'] . "分销" . $data['consume_mobile'] . ", " . replaceBeanType($bean_type) . "推广返利,金额:" . $amount;
				} else {
					if ($bean_type == 'dividend') {
						// 静态分红
						$rl_log_data['rl_desc'] = "会员" . $info['member_mobile'] . ", 金券" . replaceBeanType($bean_type) . "商城赠送,金额:" . $amount;
					} elseif ($bean_type == 'extend') {
						// 扩展奖
						$rl_log_data['rl_desc'] = "会员" . $info['member_mobile'] . ", 双轨碰撞" . replaceBeanType($bean_type) . "拓展返利,金额:" . $amount;
					} elseif ($bean_type == 'service') {
						// 服务中心奖
						$rl_log_data['rl_desc'] = "会员" . $info['member_mobile'] . ", " . replaceBeanType($bean_type) . "返利,金额:" . $amount;
					}
				}
				Log::memberRecord($rl_log_data['rl_desc'], $member_id);
				$model_bean->addBeanRebateLog($rl_log_data);

				break;
			// 金豆消费
			case 'consume':
				$log_data['lg_bean_amount'] = floatFormat(($this->getAvailBeanByInfo($info)) - $amount);
				// 金豆记录
				$log_data['lg_amount'] = 0 - $amount;
				$log_data['lg_desc'] = "会员" . $info['member_mobile'] . " 消费金豆:" . $amount;
				$insert_id = $model_bean->addBeanLog($log_data);

				// 金豆消费记录
				$cl_log_data = array();
				$cl_log_data['cl_member_id'] = $member_id;
				$cl_log_data['cl_amount'] = $amount;
				$cl_log_data['cl_addtime'] = $addtime;
				$cl_log_data['cl_sn'] = $sn;
				$cl_log_data['cl_desc'] = '会员：' . $info['member_mobile'] . ',' . $amount . '金豆消费成金券';
				$model_bean->addBeanConsumeLog($cl_log_data);
				Log::memberRecord($log_data['lg_desc'], $member_id);
				// 金券
				$model_equity = Model('equity');
				$equity_amount = floatFormat($amount / C('bean_equity_raito'));
				$elg_log_data = array();
				$elg_log_data['elg_amount'] = $equity_amount;
				$elg_log_data['elg_member_id'] = $member_id;
				$elg_log_data['elg_member_name'] = $member_name;
				$elg_log_data['elg_type'] = 'consume';

				$elg_log_data['elg_addtime'] = $addtime;
				$elg_log_data['elg_sn'] = $sn;
				$elg_log_data['elg_equity_amount'] = $info['member_equity'] + $equity_amount;
				$model_equity->addEquityLog($elg_log_data);
				$ecl_log_data = array();
				$ecl_log_data['ecl_member_id'] = $member_id;
				$ecl_log_data['ecl_amount'] = $equity_amount;
				$ecl_log_data['ecl_sn'] = $sn;
				$ecl_log_data['ecl_desc'] = '会员' . $info['member_mobile'] . '金豆消费成金券:' . $equity_amount;
				$model_equity->addEquityConsumeLog($ecl_log_data);
				Log::memberRecord($ecl_log_data['ecl_desc'], $member_id);
				// 金豆数减少
				// $this->setDecMemberExtendField($condition, array('member_golden_bean' => $amount));
				// 金豆消费额增加
				$this->setIncMemberExtendField($condition, array('member_golden_bean_payed' => $amount));
				Log::memberRecord('会员' . $info['member_mobile'] . '已用金豆增加' . $amount, $member_id);
				// 金券增加
				$this->setIncMemberExtendField($condition, array('member_equity' => $equity_amount));
				Log::memberRecord('会员' . $info['member_mobile'] . '金券增加' . $amount, $member_id);
				// 业绩
				$this->incMemberAndParentPerformance($member_id, $amount);

				// 三级分销扩展奖
				$this->consumeDistribute($member_id, $amount);

				// 消费总额
				$this->setIncMemberExtendField($condition, array('total_consume' => $amount));
				Log::memberRecord('会员' . $info['member_mobile'] . '消费总额增加' . $amount, $member_id);
				// $this->setIncMemberExtendField($condition, array('history_total_consume' => $amount));
				// Log::memberRecord('会员' . $info['member_mobile'] . '历史消费总额增加' . $amount, $member_id);
				break;
			// 转赠金豆
			case 'donate':
				$to_member_id = $data['to_member_id'];
				$to_member_name = $data['to_member_name'];
				$to_info = $this->getMemberInfo(array('member_id' => $to_member_id));
				if (empty($to_info)) {
					throw new Exception('接收人信息错误');
				}
				$log_data['lg_bean_amount'] = $info['member_golden_bean'] - $amount;
				$log_data['lg_amount'] = 0 - $amount;
				$log_data['lg_bean_amount'] = floatFormat(($this->getAvailBeanByInfo($info)) - $amount);
				$log_data['lg_desc'] = "会员" . $info['member_mobile'] . "转赠给会员" . $to_info['member_mobile'] . "金豆:" . $amount;
				$model_bean->addBeanLog($log_data);
				Log::memberRecord($log_data['lg_desc'], $member_id);
				unset($log_data['lg_member_id']);
				unset($log_data['lg_member_name']);
				unset($log_data['lg_amount']);
				unset($log_data['lg_bean_amount']);
				$log_data['lg_member_id'] = $to_member_id;
				$log_data['lg_member_name'] = $to_member_name;
				$log_data['lg_amount'] = $amount;
				$log_data['lg_bean_amount'] = floatFormat(($this->getAvailBeanById($to_member_id)) + $amount);
				$log_data['lg_desc'] = "会员" . $to_info['member_mobile'] . "接收会员" . $info['member_mobile'] . "转赠金豆:" . $amount;
				$model_bean->addBeanLog($log_data);
				Log::memberRecord($log_data['lg_desc'], $to_member_id);
				$dl_log_data = array();
				$dl_log_data['dl_from_member_id'] = $member_id;
				$dl_log_data['dl_to_member_id'] = $to_member_id;
				$dl_log_data['dl_amount'] = $amount;
				$dl_log_data['dl_addtime'] = $addtime;
				$dl_log_data['dl_desc'] = '会员' . $info['member_mobile'] . " 转赠" . $amount . "金豆给会员" . $to_info['member_mobile'];
				$dl_log_data['dl_sn'] = $sn;
				$model_bean->addBeanDonateLog($dl_log_data);
				$this->setIncMemberExtendField($condition, array('member_golden_bean_payed' => $amount));
				Log::memberRecord('会员' . $info['member_mobile'] . '已用金豆增加' . $amount, $member_id);
				$this->setIncMemberExtendField(array('me_member_id' => $to_member_id), array('member_golden_bean' => $amount));
				Log::memberRecord('会员' . $to_info['member_mobile'] . '金豆增加' . $amount, $to_member_id);
				// 累计接收金豆数
				$this->setIncMemberExtendField(array('me_member_id' => $to_member_id), array('accumulate_receive_amount' => $amount));
				Log::memberRecord('会员' . $to_info['member_mobile'] . '累计接收金豆数增加' . $amount, $to_member_id);
				break;
			// 提现申请
			case 'withdraw':
				$model_bank = Model('bank');
				$bank_info = $model_bank->getMemberBankInfo(array('mb_id' => $data['mb_id']));
				$log_data['lg_bean_amount'] = floatFormat(($this->getAvailBeanByInfo($info)) - $amount);
				$log_data['lg_amount'] = 0 - $amount;
				$log_data['lg_desc'] = "会员" . $info['member_mobile'] . "申请提现,金额:" . $amount;
				$insert_id = $model_bean->addBeanLog($log_data);
				Log::memberRecord($log_data['lg_desc'], $member_id);
				$wl_log_data = array();
				$wl_log_data['wl_member_id'] = $member_id;
				$wl_log_data['wl_sn'] = $sn;
				$wl_log_data['wl_amount'] = $amount;
				$wl_log_data['wl_addtime'] = $addtime;
				$wl_log_data['wl_desc'] = '会员' . $info['member_mobile'] . ' 申请提现,提现金额为:' . $amount;
				$wl_log_data['wl_account_name'] = $bank_info['mb_account_name'];
				$wl_log_data['wl_account_bank_name'] = $bank_info['mb_account_bank_name'];
				$wl_log_data['wl_account_branch'] = $bank_info['mb_account_branch'];
				$wl_log_data['wl_account_num'] = $bank_info['mb_account_num'];
				$wl_log_data['wl_fee'] = floatFormat($amount * C('withdraw_charge_percentage') / 100.00);
				$model_bean->addBeanWithdrawLog($wl_log_data);
				Log::memberRecord($wl_log_data['wl_desc'], $member_id);
				$this->setIncMemberExtendField($condition, array('member_golden_bean_freeze' => $amount));
				Log::memberRecord('会员' . $info['member_mobile'] . "冻结金豆增加" . $amount, $member_id);
				break;
			// 管理员调整
			case 'admin':
				$operate_type = $data['operate_type'];
				$admin_name = $data['admin_name'];
				$desc = $data['desc'];
				$al_log_data = array();
				if ($operate_type == 'add') {
					$log_data['lg_bean_amount'] = floatFormat(($this->getAvailBeanByInfo($info)) + $amount);
					$log_data['lg_amount'] = $amount;
					$lg_desc = $desc . 'redbag增加会员' . $info['member_mobile'] . '金豆:' . $amount;
					$this->setIncMemberExtendField($condition, array('member_golden_bean' => $amount));
					Log::memberRecord('会员' . $info['member_mobile'] . '金豆增加' . $amount, $member_id);
				} elseif ($operate_type == 'sub') {
					$log_data['lg_bean_amount'] = floatFormat(($this->getAvailBeanByInfo($info)) - $amount);
					$log_data['lg_amount'] = 0 - $amount;
					$lg_desc = $desc . 'redbag减少会员' . $info['member_mobile'] . '金豆:' . $amount;
					$this->setDecMemberExtendField($condition, array('member_golden_bean' => $amount));
					Log::memberRecord('会员' . $info['member_mobile'] . '金豆减少' . $amount, $member_id);
				}
				Log::memberRecord($lg_desc, $member_id);
				$al_log_data['al_desc'] = $lg_desc;
				$log_data['lg_desc'] = $lg_desc;
				$insert_id = $model_bean->addBeanLog($log_data);
				$al_log_data['al_amount'] = $amount;
				$al_log_data['al_sn'] = $sn;
				$al_log_data['al_addtime'] = $addtime;
				$al_log_data['al_member_id'] = $member_id;
				$al_log_data['al_admin_name'] = $admin_name;
				$model_bean->addBeanAdminLog($al_log_data);
				break;
			// 赠送金豆
			case 'present':
				$log_data['lg_bean_amount'] = floatFormat(($this->getAvailBeanByInfo($info)) + $amount);
				$log_data['lg_amount'] = $amount;
				$log_data['lg_desc'] = "用户订单返金豆：" . $amount;
				$insert_id = $model_bean->addBeanLog($log_data);
				Log::memberRecord($log_data['lg_desc'], $member_id);
				$this->setIncMemberExtendField($condition, array('member_golden_bean' => $amount));
				Log::memberRecord('会员' . $info['member_mobile'] . '金豆增加' . $amount, $member_id);
				break;
			}
			Model()->commit();
			return $log_data;
		} catch (Exception $e) {
			Model()->rollback();
			return array('error' => $e->getMessage());
		}
	}

	/**
	 * changeSilverBean 银豆调整
	 * @param  [type] $type [description]
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	public function changeSilverBean($type, $data) {
		$model_bean = Model('bean');
		$member_id = $data['member_id'];
		$member_name = $data['member_name'];
		$amount = $data['amount'];
		$info = $this->getMemberExtendInfo(array('me_member_id' => $member_id), "*", "union");
		if (empty($info)) {
			return array('error' => '会员信息错误');
		}
		try {
			Model()->beginTransaction();
			$log_data = array();
			$log_data['slg_member_id'] = $member_id;
			$log_data['slg_member_name'] = $member_name;
			$log_data['slg_addtime'] = TIMESTAMP;
			$sn = make_sn();
			switch ($type) {
			case 'admin':
				$desc = $data['desc'];
				$log_data['slg_sn'] = $sn;
				$log_data['slg_type'] = "admin";
				$operate_type = $data['operate_type'];
				if ($operate_type == 'add') {
					$this->setIncMemberExtendField(array('me_member_id' => $member_id), array('member_silver_bean' => $amount));
					Log::memberRecord('会员' . $info['member_mobile'] . '银豆增加' . $amount, $member_id);
					$log_data['slg_amount'] = $amount;
					$log_data['slg_desc'] = "redbag增加会员" . $info['member_mobile'] . "银豆：" . $amount . ($desc ? " [备注]" . $desc : '');
				} elseif ($operate_type == 'sub') {
					$this->setDecMemberExtendField(array('me_member_id' => $member_id), array('member_silver_bean' => $amount));
					Log::memberRecord('会员' . $info['member_mobile'] . '银豆减少' . $amount, $member_id);
					$log_data['slg_amount'] = 0 - $amount;
					$log_data['slg_desc'] = "redbag减少会员" . $info['member_mobile'] . "银豆:" . $amount . ($desc ? " [备注]" . $desc : '');
				}
				Log::memberRecord($log_data['slg_desc'], $member_id);
				$log_data['slg_bean_amount'] = $this->getAvailSilverBeanById($member_id);
				$result = $model_bean->addSilverBeanLog($log_data);
				if (!$result) {
					throw new Exception('银豆记录添加失败');
				}
				break;
			case 'donate':
				$log_data['slg_sn'] = $sn;
				$log_data['slg_type'] = "donate";
				$to_member_id = $data['to_member_id'];
				$to_member_name = $data['to_member_name'];
				$to_info = $this->getMemberInfo(array('member_id' => $to_member_id));
				if (empty($to_info)) {
					return array('error' => '接受会员信息错误');
				}
				$log_data['slg_amount'] = 0 - $amount;
				$log_data['slg_desc'] = "会员" . $info['member_mobile'] . "赠送会员" . $to_info['member_mobile'] . " 银豆:" . $amount;
				$this->setIncMemberExtendField(array('me_member_id' => $member_id), array('member_silver_bean_payed' => $amount));
				Log::memberRecord('会员' . $info['member_mobile'] . '已用银豆增加' . $amount, $member_id);
				$log_data['slg_bean_amount'] = $this->getAvailSilverBeanById($member_id);
				$result = $model_bean->addSilverBeanLog($log_data);
				Log::memberRecord($log_data['slg_desc'], $member_id);
				if (!$result) {
					throw new Exception('银豆记录添加失败');
				}
				$to_log_data = array();
				$to_log_data['slg_member_id'] = $to_member_id;
				$to_log_data['slg_member_name'] = $to_member_name;
				$to_log_data['slg_addtime'] = TIMESTAMP;
				$to_log_data['slg_sn'] = $sn;
				$to_log_data['slg_type'] = "donate";
				$to_log_data['slg_amount'] = $amount;
				$to_log_data['slg_desc'] = "会员" . $to_info['member_mobile'] . "接收会员" . $info['member_mobile'] . " 转赠的银豆:" . $amount;
				$this->setIncMemberExtendField(array('me_member_id' => $to_member_id), array('member_silver_bean' => $amount));
				Log::memberRecord('会员' . $to_info['member_mobile'] . '银豆增加' . $amount, $to_member_id);
				if (!$result) {
					throw new Exception('调整会员银豆失败');
				}
				$to_log_data['slg_bean_amount'] = $this->getAvailSilverBeanById($to_member_id);
				$result = $model_bean->addSilverBeanLog($to_log_data);
				Log::memberRecord($to_log_data['slg_desc'], $to_member_id);
				if (!$result) {
					throw new Exception('银豆记录添加失败');
				}
				break;
			case 'order_pay':
				$order_sn = $data['order_sn'];
				$log_data['slg_sn'] = $order_sn;
				$log_data['slg_type'] = "order_pay";
				$log_data['slg_amount'] = 0 - $amount;
				$log_data['slg_desc'] = "会员" . $info['member_mobile'] . "支付银豆:" . $amount . ",订单:" . $order_sn;
				$this->setIncMemberExtendField(array('me_member_id' => $member_id), array('member_silver_bean_payed' => $amount));
				Log::memberRecord('会员' . $info['member_mobile'] . '已用银豆增加' . $amount, $member_id);
				if (!$result) {
					throw new Exception('调整会员银豆失败');
				}
				$log_data['slg_bean_amount'] = $this->getAvailSilverBeanById($member_id);
				$result = $model_bean->addSilverBeanLog($log_data);
				Log::memberRecord($log_data['slg_desc'], $member_id);
				if (!$result) {
					throw new Exception('银豆记录添加失败');
				}
				break;
			}
			Model()->commit();
			return array('success' => "成功");
		} catch (Exception $exception) {
			Model()->rollback();
			return array('error' => $exception->getMessage());
		}
	}

	/**
	 * order_pay 订单支付
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	public function order_pay_bak($data) {
		$model_bean = Model('bean');
		try {
			$this->beginTransaction();
			$log_data = array();
			$member_id = $data['member_id'];
			$amount = $data['amount'];
			$log_data['lg_member_id'] = $member_id;
			$log_data['lg_member_name'] = $data['member_name'];
			$log_data['lg_sn'] = $data['order_sn'];
			$log_data['lg_type'] = 'order_pay';
			$log_data['lg_addtime'] = TIMESTAMP;
			$log_data['lg_bean_amount'] = floatFormat(($this->getAvailBeanById($member_id)) - $amount);
			$log_data['lg_amount'] = 0 - $amount;
			$log_data['lg_desc'] = '订单支付，支付金额为' . $amount;

			$model_bean->addBeanLog($log_data);
			$this->setIncMemberExtendField(array('me_member_id' => $member_id), array('member_golden_bean_payed' => $amount));
			Log::memberRecord('会员' . $member_id . '已用金豆增加' . $amount, $member_id);
			Log::memberRecord($log_data['lg_desc'], $member_id);
			$this->commit();
			return array('succ' => "支付成功");
		} catch (Exception $exception) {
			$this->rollback();
			return array('error' => "支付失败3");
		}

	}

	/**
	 * order_pay 银豆订单支付
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	public function sliver_order_pay($data) {
		$model_bean = Model('bean');
		try {
			$this->beginTransaction();
			$log_data = array();
			$member_id = $data['member_id'];
			$amount = $data['amount'];
			$log_data['slg_member_id'] = $member_id;
			$log_data['slg_member_name'] = $data['member_name'];
			$log_data['slg_sn'] = $data['order_sn'];
			$log_data['slg_type'] = 'order_pay';
			$log_data['slg_addtime'] = TIMESTAMP;
			$log_data['slg_bean_amount'] = floatFormat(($this->getAvailSilverBeanById($member_id)) - $amount);
			$log_data['slg_amount'] = 0 - $amount;
			$log_data['slg_desc'] = '订单支付，支付金额为' . $amount;

			$model_bean->addSilverBeanLog($log_data);
			$this->setIncMemberExtendField(array('me_member_id' => $member_id), array('member_silver_bean_payed' => $amount));
			Log::memberRecord('会员' . $member_id . '已用银豆增加' . $amount, $member_id);
			Log::memberRecord($log_data['slg_desc'], $member_id);
			$this->commit();
			return array('succ' => "支付成功");
		} catch (Exception $exception) {
			$this->rollback();
			return array('error' => "支付失败");
		}

	}

	/**
	 * changeEquity 更改股权
	 * @param  [type] $type [description]
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	public function changeEquity($type, $data) {
        $member_id = $data['member_id'];
        $amount = floatFormat($data['amount']);
        $member_name = $data['member_name'];
        $addtime = TIMESTAMP;
        $sn = makeSN($type, $member_id);
        $info = $this->getMemberExtendInfo(array('member_id'=>$member_id), $fields = '*', 'union');
        if (empty($info)) {
            return array('error' => "会员信息错误");
        }
        try {
            $model_equity = Model('equity');
            $log_data = array();
            $log_data['elg_member_id'] = $member_id;
            $log_data['elg_member_name'] = $member_name;
            $log_data['elg_addtime'] = $addtime;
            $log_data['elg_type'] = $type;
            $log_data['elg_sn'] = $sn;
            $condition = array('me_member_id' => $member_id);
            $operate_type = $data['operate_type'];
            $log_data['elg_desc'] = "管理员为会员调整股权";
            if ($operate_type == 'add') {
                $this->setIncMemberExtendField($condition, array('member_equity' => $amount));
                $log_data['elg_amount'] = $amount;
                $log_data['elg_equity_amount'] = $info['member_equity']+$amount;
            } else{
                $this->setDecMemberExtendField($condition, array('member_equity' => $amount));
                $log_data['elg_amount'] = $amount;
                $log_data['elg_equity_amount'] = $info['member_equity']-$amount;
            }
            $log_data['elg_balance_amount'] = (float)0;
            $log_data['elg_state'] = 2;
            $admin_log_data['eal_amount'] =$amount;
            $admin_log_data['eal_sn'] = makeSN('admin',$member_id);
            $admin_log_data['eal_desc'] = "管理员为会员调整股权";
            $admin_log_data['eal_addtime'] =$addtime;
            $admin_log_data['eal_member_id'] =$member_id;
            $admin_log_data['eal_admin_name'] = $data['admin_name'];
            try{
                $model_equity->addEquityLog($log_data);
                $model_equity->addAdminEquityLog($admin_log_data);
            }catch (Exception $exception){
                return array('error'=>$exception->getMessage());
            }
			return $log_data;
		} catch (Exception $exception) {
			return array('error' => $exception->getMessage());
		}
	}

	/**
	 * consumeDistribute 消费分销
	 * @return [type] [description]
	 */
	public function consumeDistribute($member_id, $amount) {
		$info = $this->getMemberInfo(array('member_id' => $member_id));
		$rebate_log_data = array();
		$rebate_log_data['bean_type'] = 'promotion';
		$rebate_log_data['consume_mobile'] = $info['member_mobile'];
		if ($info['invite_one']) {
			$rebate_log_data['member_id'] = $info['invite_one'];
			$rebate_log_data['member_name'] = $this->getMemberNameById($info['invite_one']);
			$rebate_log_data['amount'] = floatFormat($amount * C('one_rebate_percentage') / 100.00);
			$rebate_log_data['level'] = "一级";
			$this->changeBean('rebate', $rebate_log_data);
		}
		if ($info['invite_two']) {
			$rebate_log_data['member_id'] = $info['invite_two'];
			$rebate_log_data['member_name'] = $this->getMemberNameById($info['invite_two']);
			$rebate_log_data['amount'] = floatFormat($amount * C('two_rebate_percentage') / 100.00);
			$rebate_log_data['level'] = "二级";
			$this->changeBean('rebate', $rebate_log_data);
		}
		if ($info['invite_three']) {
			$rebate_log_data['member_id'] = $info['invite_three'];
			$rebate_log_data['member_name'] = $this->getMemberNameById($info['invite_three']);
			$rebate_log_data['amount'] = floatFormat($amount * C('three_rebate_percentage') / 100.00);
			$rebate_log_data['level'] = "三级";
			$this->changeBean('rebate', $rebate_log_data);
		}
	}

	/**
	 * withdraw_confirm 确认提现
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	public function withdraw_confirm($data) {
		$model_balance = Model('balance');
		try {
			Model()->beginTransaction();
			$withdraw_info = $model_balance->getBalanceWithdraw(array('wl_id' => $data['id'], 'wl_state' => 0));
			if (empty($withdraw_info)) {
				return array('error' => '提现信息错误');
			}
            $member_id = $withdraw_info[0]['wl_member_id'];
            $amount = $withdraw_info['wl_amount'];
            $condition = array('me_member_id' => $member_id);
			$info = $this->getMemberExtendInfo($condition, '*', 'union');
			if (empty($info)) {
				return array('error' => '会员信息错误');
			}
			$withdraw_info_param['wl_state'] = 1;
			$withdraw_info_param['wl_admin_name'] = $data['admin_name'];
			$withdraw_info_param['wl_paytime'] = TIMESTAMP;
            $model_balance->editBalanceWithdrawRecord(array('wl_id' => $data['id']), $withdraw_info_param);
			Log::memberRecord('会员' . $info['member_mobile'] . '提现，余额减少' . $amount, $member_id);
			Log::memberRecord('会员' . $info['member_mobile'] . '确认提现', $member_id);
			Model()->commit();
			// sleep(1);
			return $withdraw_info;
			// }
		} catch (Exception $exception) {
			Model()->rollback();
			return array('error' => $exception->getMessage());
		}
	}

	/**
	 * withdraw_refuse 拒绝提现
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	public function withdraw_refuse($data) {
        $model_balance = Model('balance');
		try {
			Model()->beginTransaction();
            $withdraw_info = $model_balance->getBalanceWithdraw(array('wl_id' => $data['id'], 'wl_state' => 0));
			if (empty($withdraw_info)) {
				return array('error' => '提现信息错误');
			}

			$member_id = $withdraw_info[0]['wl_member_id'];
			$amount = $withdraw_info[0]['wl_amount'];
			$info = $this->getMemberExtendInfo(array('me_member_id' => $member_id), '*', 'union');
			if (empty($info)) {
				return array('error' => '会员信息错误');
			}
			// 余额记录
			$log_data = array();
			$log_data['member_id'] = $member_id;
			$log_data['member_mobile'] = $info['member_mobile'];
			$log_data['member_name'] = $info['member_name'];
			$log_data['add_time'] = TIMESTAMP;
			$log_data['type'] = 'withdraw';
			$log_data['sn'] = $withdraw_info[0]['wl_sn'];
			$log_data['operate_amount'] = $amount;
            $member_info = $this->getMemberInfo(['member_id'=>$member_id]);
            $balance = $member_info['balance'];
			$log_data['surplus_amount'] = ($balance + $amount);
			$log_data['content'] = "会员" . $info['member_name'] . ",提现申请被拒绝";
            $model_balance->addBalanceLog($log_data);
			$this->setIncMemberField(array('member_id' => $member_id), array('balance' => $amount));
			Log::memberRecord('会员' . $info['member_mobile'] . '余额增加' . $amount, $member_id);
			Log::memberRecord($log_data['lg_desc'], $member_id);
			// sleep(1);
			// }
			$withdraw_info_param['wl_state'] = 2;
			$withdraw_info_param['wl_admin_name'] = $data['admin_name'];
			$withdraw_info_param['wl_paytime'] = TIMESTAMP;
            $model_balance->editBalanceWithdrawRecord(array('wl_id' => $data['id']), $withdraw_info_param);
			Model()->commit();
			return true;
		} catch (Exception $exception) {
			Model()->rollback();
			return array('error' => $exception->getMessage());
		}

	}

	/**
	 * recharge_apply 充值申请
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	public function recharge_apply($data) {
		$model_bean = Model('bean');
		$member_id = $data['member_id'];
		$amount = floatFormat($data['amount']);
		// 充值方式
		$recharge_type = $data['recharge_type'];
		// 充值账户
		$account_type = $data['account_type'];

		$sn = makeSN('recharge', $member_id);
		$rl_log_data = array();
		$rl_log_data['rl_member_id'] = $member_id;
		$rl_log_data['rl_addtime'] = TIMESTAMP;
		$rl_log_data['rl_amount'] = $amount;
		$rl_log_data['rl_sn'] = $sn;
		$rl_log_data['rl_recharge_type'] = $recharge_type;
		$rl_log_data['rl_account_type'] = $account_type;

		try {
			Model()->beginTransaction();
			$model_bean->addBeanRechargeLog($rl_log_data);
			Model()->commit();
			return $rl_log_data;
		} catch (Exception $exception) {
			Model()->rollback();
			return array('error' => $exception->getMessage());
		}
	}

	/**
	 * recharge_confirm 充值确认
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	public function recharge_confirm($data) {
		$model_bean = Model('bean');
		$recharge_sn = $data['sn'];
		$paytime = $data['paytime'];
		// 外部单号
		$trade_no = $data['trade_no'];
		$recharge_info = $model_bean->getBeanRechargeInfo(array('rl_sn' => $recharge_sn));
		if (empty($recharge_info) || !$recharge_info['rl_state']) {
			return array('error' => '充值信息错误');
		}

		$member_id = $recharge_info['rl_member_id'];
		$amount = $recharge_info['rl_amount'];
		$condition = array('me_member_id' => $member_id);
		$info = $this->getMemberExtendInfo($condition, '*', 'union');
		if (empty($info)) {
			return array('error' => '会员信息错误');
		}

		try {
			Model()->beginTransaction();
			$log_data = array();
			$log_data['lg_member_id'] = $member_id;
			$log_data['lg_member_name'] = $info['member_name'];
			$log_data['lg_addtime'] = $paytime;
			$log_data['lg_type'] = 'recharge';
			$log_data['lg_sn'] = $recharge_sn;
			$log_data['lg_amount'] = $amount;
			$log_data['lg_bean_amount'] = floatFormat(($this->getAvailBeanByInfo($info)) + $amount);
			$model_bean->addBeanLog($log_data);
			$recharge_info['rl_state'] = 1;
			$recharge_info['rl_paytime'] = $paytime;
			$recharge_info['rl_trade_no'] = $trade_no;
			$recharge_info['rl_desc'] = "会员" . $member_id . " 充值金豆:" . $amount;
			$model_bean->editBeanRechargeLog(array('rl_sn' => $recharge_sn), $recharge_info);
			$this->setIncMemberExtendField($condition, array('member_golden_bean' => $amount));
			Model()->commit();
			return $recharge_info;
		} catch (Exception $exception) {
			Model()->rollback();
			return array('error' => $exception->getMessage());
		}
	}

	/**
	 * auditService 审核服务器中心申请
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function auditService($id) {
		$model_service = Model('service');
		try {
			Model()->beginTransaction();
			$s_info = $model_service->getServiceLogInfo(array('slg_id' => $id));
			if (!empty($s_info)) {
				$member_id = $s_info['slg_member_id'];
				$update = $model_service->editServiceLog(array('slg_id' => $id), array('slg_state' => 1, 'slg_audittime' => TIMESTAMP));
				$this->editMemberExtend(array('me_member_id' => $member_id), array('is_service' => 1));
			} else {
				return array('error' => "服务中心信息错误");
			}
			Model()->commit();
			return $update;
		} catch (Exception $exception) {
			Model()->rollback();
			return array('error' => $exception->getMessage());
		}
	}

	/**
	 * getAllMemberList 获取所有会员信息
	 * @param  string $field [description]
	 * @param  string $order [description]
	 * @return [type]        [description]
	 */
	public function getAllMemberList($fields = '*', $order = 'member_id desc') {
		$sql = "SELECT " . $fields . " FROM " . DBPRE . 'member order by ' . $order;
		return Model()->query($sql);
	}

	/**
	 * getAllMemberExtendList 获取所有会员扩展信息
	 * @param  string $fields [description]
	 * @param  string $order  [description]
	 * @return [type]         [description]
	 */
	public function getAllMemberExtendList($fields = '*', $order = 'member_id desc') {
		$sql = "SELECT " . $fields . " FROM " . DBPRE . "member AS member INNER JOIN " . DBPRE . "member_extend AS member_extend ON member.member_id=member_extend.me_member_id ORDER BY " . $order;
		return Model()->query($sql);
	}

	/**
	 * getAllMemberExtendAvail 获取所有会员可用的账户信息
	 * @return [type] [description]
	 */
	public function getAllMemberExtendAvail() {
		$sql = "SELECT sum(member_equity) as member_equity, sum(set_meal_amount) as set_meal_amount, sum(total_consume) as total_consume FROM " . DBPRE . "member_extend";
		return Model()->query($sql);
	}
    /**
     * getAllMemberExtendAvail 获取所有会员可用的账户信息
     * @return [type] [description]
     */
    public function getAllMemberAvail() {
        $sql = "SELECT sum(balance) as balance, sum(bonus) as bonus  FROM " . DBPRE . "member";
        return Model()->query($sql);
    }
	/**
	 * getAllMemberExtendAvail 获取所有会员金券数量类型1
	 * @return [type] [description]
	 */
	public function getGoldNumType1() {
		$sql = "select COUNT(*) as min_count from dfn_member_extend WHERE member_equity>=" . C('equity_dividend_min_limit') . " and member_equity<" . C('equity_dividend_max_limit') . "";
		return Model()->query($sql);
	}
	/**
	 * getAllMemberExtendAvail 获取所有会员金券数量类型2
	 * @return [type] [description]
	 */
	public function getGoldNumType2() {
		$sql = "select COUNT(*) as max_count from dfn_member_extend WHERE member_equity>=" . C('equity_dividend_max_limit') . "";
		return Model()->query($sql);
	}
	/**
	 * getInviteParentArr 获取当前用户上级推荐关系
	 * @param  [type] $member_id [description]
	 * @param  string $fields     [description]
	 * @return [type]            [description]
	 */
	public function getInviteParentArr($member_id, $fields = '*') {
		$arr = array();
		if ($fields != '*' && !strpos($fields, 'invite_id')) {
			$fields .= ',invite_id';
		}

		$info = $this->getMemberExtendInfo(array('member_id' => $member_id), $fields, 'union');
		if (!empty($info)) {
			$invite_id = $info['invite_id'];
			$info['parent'] = $this->getInviteParentArr($invite_id, $fields);
			$arr[] = $info;
		}
		return $arr;
	}

	/**
	 * getAccessParentArr 获取当前用户上级接点关系
	 * @param  [type] $member_id [description]
	 * @param  string $fields     [description]
	 * @return [type]            [description]
	 */
	public function getAccessParentArr($member_id, $fields = '*') {
		$arr = array();
		if ($fields != '*' && !strpos($fields, 'access_id')) {
			$fields .= ',access_id';
		}

		$info = $this->getMemberExtendInfo(array('member_id' => $member_id), $fields, 'union');

		if (!empty($info)) {
			$access_id = $info['access_id'];
			$info['parent'] = $this->getAccessParentArr($access_id, $fields);
			$arr[] = $info;
		}
		return $arr;
	}

	/**
	 * getMaxField 获取字段最大值
	 * @param  [type] $field [description]
	 * @return [type]        [description]
	 */
	public function getMaxField($field) {
		return $this->table('member_extend')->max($field);
	}

	/**
	 * getMinField 获取字段最小值
	 * @param  [type] $field [description]
	 * @return [type]        [description]
	 */
	public function getMinField($field) {
		return $this->table('member_extend')->min($field);
	}

    /**
     * setIncMemberExtendField 增加会员表字段值
     * @param [type] $condition [description]
     * @param [type] $data      [description]
     */
    public function setIncMemberField($condition, $data) {
        foreach ($data as $key => $value) {
            $update = $this->table('member')
                ->where($condition)
                ->setInc($key, $value);
            if (!$update) {
                throw new Exception('更新失败');
            }
        }
    }

	/**
	 * setIncMemberExtendField 增加扩展表字段值
	 * @param [type] $condition [description]
	 * @param [type] $data      [description]
	 */
	public function setIncMemberExtendField($condition, $data) {
		foreach ($data as $key => $value) {
			$update = $this->table('member_extend')->where($condition)->setInc($key, $value);
			if (!$update) {
				throw new Exception('更新失败');
			}
		}
	}

	/**
	 * setDecMemberExtendField 减少扩展表字段值
	 * @param [type] $condition [description]
	 * @param [type] $data      [description]
	 */
	public function setDecMemberExtendField($condition, $data) {
		foreach ($data as $key => $value) {
			$update = $this->table('member_extend')
				->where($condition)
				->setDec($key, $value);
			if (!$update) {
				throw new Exception('更新失败');
			} else {
				return true;
			}
		}
	}

	/**
	 * setFieldMemberExtend 设置字段值
	 * @param [type] $condition [description]
	 * @param [type] $data      [description]
	 */
	public function setFieldMemberExtend($condition, $field, $value) {
		$update = $this->table('member_extend')
			->where($condition)
			->set_field($field, $value);
		if (!$update) {
			throw new Exception('更新失败');
		}

	}
	/**
	 * getMemberExtendInfo 获取会员以及扩展信息
	 * @param  array  $condition [description]
	 * @param  string $type      [description]
	 * @param  string $fields    [description]
	 * @return [type]            [description]
	 */
	public function getMemberExtendInfo($condition = array(), $fields = '*', $type = '') {
		if ($type == 'union') {
			$res = $this
				->table('member,member_extend')
				->field($fields)
				->where($condition)
				->join('inner')
				->on('member.member_id=member_extend.me_member_id')
				->find();
			return $res;
		} else {
			return $this->table('member_extend')->where($condition)->field($fields)->find();
		}

	}

	/**
	 * getMemberExtendList 获取会员以及扩展列表
	 * @param  [type] $condition [description]
	 * @param  string $fields     [description]
	 * @return [type]            [description]
	 */
	public function getMemberExtendList($condition = array(), $fields = '*', $type = '', $page = null, $order = 'me_member_id desc', $limit = '') {
		if (empty($order)) {
			if ($type == 'union') {
				return $this
					->table('member,member_extend')
					->field($fields)
					->where($condition)
					->join('inner')
					->on('member.member_id=member_extend.me_member_id')
					->page($page)
					->limit($limit)
					->select();
			} else {
				return $this->table('member_extend')->where($condition)->field($fields)->page($page)->limit($limit)->select();
			}
		} else {
			if ($type == 'union') {
				$res = $this
					->table('member,member_extend')
					->field($fields)
					->where($condition)
					->join('inner')
					->on('member.member_id=member_extend.me_member_id')
					->page($page)
					->order($order)
					->limit($limit)
					->select();
//				var_dump($res);die();
				return $res;
			} else {
				return $this->table('member_extend')->where($condition)->field($fields)->page($page)->order($order)->limit($limit)->select();
			}
		}
	}

	/**
	 * getBankList 获取银行列表
	 * @param  array  $condition [description]
	 * @param  string $fields    [description]
	 * @return [type]            [description]
	 */
	public function getBankList($condition = array(), $fields = '*') {
		return $this->table('bank')->where($condition)->field($fields)->select();
	}

	/**
	 * getMemberInfo 获取会员信息
	 * @param  [type]  $condition [description]
	 * @param  string  $field     [description]
	 * @param  boolean $master    [description]
	 * @return [type]             [description]
	 */
	public function getMemberInfo($condition, $field = '*', $master = false) {
		return $this->table('member')->field($field)->where($condition)->master($master)->find();
	}

	/**
	 * 会员列表
	 * @param array $condition
	 * @param string $field
	 * @param number $page
	 * @param string $order
	 */
	public function getMemberList($condition = array(), $field = '*', $page = null, $order = 'member_id desc', $limit = '') {
		return $this->table('member')->field($field)->where($condition)->page($page)->order($order)->limit($limit)->select();
	}

	/**
	 * addMemberExtend 插入会员扩展信息
	 * @param [type] $data [description]
	 *
	 */
	public function addMemberExtend($data) {
		$insert = $this->table('member_extend')->insert($data);
		if (!$insert) {
			throw new Exception('添加会员扩展信息失败');
		} else {
			return $insert;
		}
	}

	/**
	 * addMemberInfo 添加会员信息
	 * @param [type] $data [description]
	 */
	public function addMemberInfo($data) {
		$insert = $this->table('member')->insert($data);
		if (!$insert) {
			throw new Exception("添加会员信息失败");
		} else {
			return $insert;
		}
	}

	/**
	 * editMemberExtend 编辑会员扩展信息
	 * @param  [type] $data      [description]
	 * @param  [type] $condition [description]
	 *
	 */
	public function editMemberExtend($condition, $data) {
		$update = $this->table('member_extend')->where($condition)->update($data);
		if (!$update) {
			throw new Exception('更新会员扩展信息失败');
		}
	}

	/**
	 * editMemberInfo 编辑会员信息
	 * @param  [type] $condition [description]
	 * @param  [type] $data      [description]
	 * @return [type]            [description]
	 */
	public function editMemberInfo($condition, $data) {
		$update = $this->table('member')->where($condition)->update($data);
		if (!$update) {
			throw new Exception("更新会员信息失败");
		}
	}

	/**
	 * getMemberAccessCount 获取用户接点人的数量
	 * @param  [type] $member_id [description]
	 * @return [type]            [description]
	 */
	public function getMemberAccessCount($member_id) {
		return $this->table('member_extend')->where(array('access_id' => $member_id))->count();
	}

	/**
	 * addCollisionLog 插入碰撞信息
	 * @param [type] $data [description]
	 *
	 */
	public function addCollisionLog($data) {
		$insert = $this->table('collision_log')->insert($data);
		if (!$insert) {
			throw new Exception('添加碰撞信息失败');
		} else {
			return $insert;
		}
	}

	/**
	 * checkLoginMember 会员登录检查
	 * @return [type] [description]
	 */
	public function checkLoginMember() {
		if ($_SESSION['is_login'] == '1') {
			@header("Location: index.php");
			exit();
		}
	}

	/**
	 * memberLogin 会员登陆操作
	 * @param  [type] $login_info [description]
	 * @return [type]             [description]
	 */
	public function memberLogin($login_info) {
		if (process::islock('login')) {
			return array('error' => "您的操作过于频繁，请稍后再试");
		}
		process::addprocess('login');
		$member_mobile = $login_info['member_mobile'];
		$member_pwd = $login_info['member_pwd'];
		//$fields = 'member_mobile,member_id,member_name,member_passwd,member_paypwd,member_state,member_login_time,member_login_num,member_login_ip,member_avatar,RCtoken';
		$fields = '*';
		$info = Model('member')->getMemberInfo(array('member_mobile' => $member_mobile), $fields);
		if (empty($info)) {
			return array('error' => "账号不存在");
		}
		if ($info['member_passwd'] != md5($member_pwd)) {
			return array('error' => "密码不正确");
		}
		if ($info['member_state'] == 0) {
			return array('error' => "账号已冻结");
		}
		process::clear('login');

		$update_data = array(
			'member_login_num' => ($info['member_login_num'] + 1),
			'member_login_time' => TIMESTAMP,
			'member_old_login_time' => $info['member_login_time'],
			'member_login_ip' => getIp(),
			'member_old_login_ip' => $info['member_login_ip'],
		);
		Model('member')->editMember(array('member_id' => $info['member_id']), $update_data);
		Log::memberRecord('会员' . $info['member_mobile'] . '登录', $info['member_id']);
		return $info;
	}

	/**
	 * memberRegister 会员注册
	 * @param  [type] $register_info [description]
	 * @return [type]                [description]
	 */
	public function memberRegister($register_info, $createSession = true, $bankInsert = false) {
		// 重复注册验证
		if (process::islock('reg')) {
			return array('error' => '您的注册操作过于频繁，请稍后再试');
		}

		try {
			$this->beginTransaction();
			$nowtime = TIMESTAMP;
			// 会员添加
			$member_info = array();
			$member_info['member_name'] = $register_info['username'];
			$member_info['member_truename'] = $register_info['username'];
            $member_info['member_mobile'] = $register_info['member_mobile'];
            $member_info['member_avatar'] = $register_info['member_avatar'];
			$member_info['member_passwd'] = md5($register_info['password']);
			$member_info['member_time'] = $nowtime;
			$member_info['member_login_time'] = $nowtime;
			$member_info['member_old_login_time'] = $nowtime;
			$member_info['member_login_ip'] = getIp();
			$member_info['member_old_login_ip'] = getIp();
			$member_info['member_mobile_bind'] = 1;
			$member_info['member_level'] = $register_info['member_level'];
			$insert_id = $this->addMemberInfo($member_info);
			$member_info['member_id'] = $insert_id;
			$info = $member_info;
            //将用户加入child_level_num表
            $this->addToChildLevelNumTable($insert_id);
			if ($createSession) {
				$this->createSession($info);
			}
			Log::memberRecord('会员' . $info['member_mobile'] . '注册', $info['member_id']);
			$this->commit();
			return $info;
		} catch (Exception $exception) {
			$this->rollback();
			return array('error' => $exception->getMessage());
		}
	}


	public function addToChildLevelNumTable($member_id){
	    $insert['member_id'] = $member_id;
	    return $this->table('child_level_num')->insert($insert);
    }

	/**
	 * createSession 登录创建会话
	 * @param  array   $info [description]
	 * @return [type]               [description]
	 */
	public function createSession($info = array()) {
		if (is_array($info) && !empty($info)) {
			$_SESSION['is_login'] = '1';
			$_SESSION['member_id'] = $info['member_id'];
			$_SESSION['member_name'] = $info['member_name'];
			if (!empty($info['member_login_time'])) {
				$data = array(
					'member_login_num' => ($info['member_login_num'] + 1),
					'member_login_time' => TIMESTAMP,
					'member_old_login_time' => $info['member_login_time'],
					'member_login_ip' => getIp(),
					'member_old_login_ip' => $info['member_login_ip'],
				);
				$this->editMemberInfo(array('member_id' => $info['member_id']), $data);
			}
			// 自动登录
			// if ($member_info['auto_login'] == 1) {
			//        $this->auto_login();
			// }
		}
	}

	/**
	 * getAvailBeanById 获取会员可用金豆
	 * @param  [type] $member_id [description]
	 * @return [type]            [description]
	 */
	public function getAvailBeanById($member_id) {
		$info = $this->getMemberExtendInfo(array('me_member_id' => $member_id), 'balance,member_golden_bean,member_golden_bean_payed,member_golden_bean_freeze');
		if (!empty($info)) {
			return floatval(($info['member_golden_bean'] * 100 - $info['member_golden_bean_payed'] * 100 - $info['member_golden_bean_freeze'] * 100) / 100);
		} else {
			return 0;
		}
	}

	public function getMemberBalanceById($member_id){
        $info = $this->getMemberExtendInfo(array('me_member_id' => $member_id), 'balance');
        if (!empty($info)) {
            return floatval(($info['member_golden_bean'] * 100 - $info['member_golden_bean_payed'] * 100 - $info['member_golden_bean_freeze'] * 100) / 100);
        } else {
            return 0;
        }
    }

	/**
	 * getAvailBeanByInfo 获取会员的可用金豆
	 * @param  [type] $info [description]
	 * @return [type]       [description]
	 */
	public function getAvailBeanByInfo($info) {
		if (isset($info['member_golden_bean']) && isset($info['member_golden_bean_payed']) && isset($info['member_golden_bean_freeze'])) {
			return floatval(($info['member_golden_bean'] * 100 - $info['member_golden_bean_payed'] * 100 - $info['member_golden_bean_freeze'] * 100) / 100);
		} else {
			return 0;
		}
	}

	/**
	 * getAvailSilverBeanById 获取会员可用银豆
	 * @param  [type] $member_id [description]
	 * @return [type]            [description]
	 */
	public function getAvailSilverBeanById($member_id) {
		$info = $this->getMemberExtendInfo(array('me_member_id' => $member_id), 'member_silver_bean,member_silver_bean_payed');
		if (!empty($info)) {
			return floatval($info['member_silver_bean'] - $info['member_silver_bean_payed']);
		} else {
			return 0;
		}
	}

	/**
	 * getAvailSilverBeanByInfo 获取会员可用银豆
	 * @param  [type] $info [description]
	 * @return [type]       [description]
	 */
	public function getAvailSilverBeanByInfo($info) {
		if (isset($info['member_silver_bean']) && isset($info['member_silver_bean_payed'])) {
			return floatval($info['member_silver_bean'] - $info['member_silver_bean_payed']);
		} else {
			return 0;
		}
	}

	/**
	 * getAvailEquityById 获取可用金券
	 * @param  [type] $member_id [description]
	 * @return [type]            [description]
	 */
	public function getAvailEquityById($member_id) {
		$info = $this->getMemberExtendInfo(array('me_member_id' => $member_id), 'member_equity');
		if (!empty($info)) {
			return $info['member_equity'];
		} else {
			return 0;
		}
	}
	/**
	 * getMemberInviteParentArr 获取会员及上级数组
	 * @param  [type] $member_id [description]
	 * @param  [type] &$arr      [description]
	 * @return [type]            [description]
	 */
	public function getMemberInviteParentArr($member_id, &$arr) {
		$info = $this->getMemberExtendInfo(array('member_id' => $member_id), 'member_id,invite_id', 'union');
		if (empty($info)) {
			return;
		} else {
			$arr[] = $info;
			if ($info['invite_id']) {
				$this->getMemberInviteParentArr($info['invite_id'], $arr);
			}
		}
	}

	/**
	 * getMemberAccessParentArr 获取会员及上级接点人信息
	 * @param  [type] $member_id [description]
	 * @param  [type] &$arr      [description]
	 * @return [type]            [description]
	 */
	public function getMemberAccessParentArr($member_id, &$arr) {
		$info = $this->getMemberExtendInfo(array('member_id' => $member_id), 'member_id,access_id', 'union');
		if (!empty($info)) {
			$arr[] = $member_id;
			if ($info['access_id']) {
				$this->getMemberAccessParentArr($info['access_id'], $arr);
			}
		}
	}

	/**
	 * incMemberAndParentPerformance 增加会员及上级业绩(新增业绩、总业绩)
	 * @param  [type] $member_id [description]
	 * @return [type]            [description]
	 */
	public function incMemberAndParentPerformance($member_id, $amount) {
		$model_performance = Model('performance');
		// 获取会员及上级数组
		$access_arr = array();
		$this->getMemberAccessParentArr($member_id, $access_arr);
		try {
			Model()->beginTransaction();
			foreach ($access_arr as $access_id) {
				$member_id = $access_id;
				$model_performance->addMemberPerformanceIncLog($member_id, $amount);
				$this->setIncMemberExtendField(array('me_member_id' => $member_id), array('new_performance' => $amount, 'total_performance' => $amount));
				Log::memberRecord('会员' . $member_id . '新增业绩、总业绩增加' . $amount, $member_id);
			}
			Model()->commit();
			return true;
		} catch (Exception $exception) {
			Model()->rollback();
			return array('error' => $exception->getMessage());
		}
	}

	/**
	 * clearAllMemberPerformance 清空所有会员新增业绩
	 * @return [type] [description]
	 */
	public function clearAllMemberPerformance() {
		$member_arr = $this->getMemberExtendList(array('new_performance' => array('neq', 0)), "me_member_id,new_performance");
		try {
			Model()->beginTransaction();
			foreach ($member_arr as $v) {
				$member_id = $v['me_member_id'];
				$this->setFieldMemberExtend(array('me_member_id' => $member_id), 'new_performance', 0);
				$this->setIncMemberExtendField(array('me_member_id' => $member_id), array('surplus_performance' => $v['new_performance']));
				Model('performance')->addMmeberPerformanceClearLog($member_id);
			}
			Model()->commit();
			return true;
		} catch (Exception $exception) {
			Model()->rollback();
			return array('error' => $exception->getMessage());
		}

	}

	public function getMemberNameById($member_id) {
		$info = $this->getMemberInfo(array('member_id' => $member_id));
		if (!empty($info)) {
			return $info['member_name'];
		} else {
			return '';
		}
	}

	/**
	 * getMemberMobileArr 获取会员手机数组
	 * @return [type] [description]
	 */
	public function getMemberMobileArr() {
		$arr = array();
		$arr['0'] = "系统";
		$list = $this->getAllMemberList('member_id,member_mobile');
		foreach ($list as $v) {
			$arr[$v['member_id']] = $v['member_mobile'];
		}
		return $arr;
	}

	/**
	 * getMemberNameArr 获取会员姓名数组
	 * @return [type] [description]
	 */
	public function getMemberNameArr() {
		$arr = array();
		$list = $this->getAllMemberList('member_id,member_name');
		foreach ($list as $v) {
			$arr[$v['member_id']] = $v['member_name'];
		}
		return $arr;
	}

	/**
	 * buildBinaryTreeArr 生成二叉树数组
	 * @param  [type]  $member_id [description]
	 * @param  boolean $is_main   [description]
	 * @return [type]             [description]
	 */
	public function buildBinaryTreeArr($member_id, $is_master = true) {
		$binary_tree = array();
		$info = $this->getMemberExtendInfo(array('member_id' => $member_id), 'member_id,member_mobile,access_id,left_right_area', 'union');
		if (!empty($info)) {
			if ($is_master) {
				$binary_tree['type'] = 'master';
				$binary_tree['member_id'] = $info['member_id'];
				$binary_tree['member_mobile'] = $info['member_mobile'];
				$binary_tree['access_id'] = $info['access_id'];
				$access_list = $this->getMemberExtendList(array('access_id' => $member_id), '*', 'union', null, 'left_right_area desc');

				if (!empty($access_list[0])) {
					$binary_tree['performance1'] = floatFormat($access_list[0]['total_performance']);
					$binary_tree['surplus1'] = floatFormat($access_list[0]['surplus_performance'] + $access_list[0]['new_performance']);
				} else {
					$binary_tree['performance1'] = floatFormat(0);
					$binary_tree['surplus1'] = floatFormat(0);
				}

				if (!empty($access_list[1])) {
					$binary_tree['performance2'] = ncPriceFormat($access_list[1]['total_performance']);
					$binary_tree['surplus2'] = ncPriceFormat($access_list[1]['surplus_performance'] + $access_list[1]['new_performance']);
				} else {
					$binary_tree['performance2'] = ncPriceFormat(0);
					$binary_tree['surplus2'] = ncPriceFormat(0);
				}
			} else {
				$binary_tree['type'] = 'slave';
				$binary_tree['member_id'] = $info['member_id'];
				$binary_tree['member_mobile'] = $info['member_mobile'];
				$binary_tree['access_id'] = $info['access_id'];
				$binary_tree['left_right_area'] = $info['left_right_area'];
			}
		}
		$binary_tree['child'] = $this->getChildNode($member_id, 1);
		return $binary_tree;
	}

	/**
	 * getChildNode 获取child节点
	 * @param  [type] $member_id [description]
	 * @return [type]            [description]
	 */
	public function getChildNode($member_id, $cur_layer, $max_layer = 2) {
		$child_node = array();
		$s_node = array(
			'type' => 'slave',
			'member_id' => 0,
			'member_mobile' => 0,
			'access_id' => $member_id,
			'child' => array(),
			'left_right_area' => 0,
		);
		if ($cur_layer > $max_layer) {
			return $child_node;
		}
		$child_list = $this->getMemberExtendList(array('access_id' => $member_id), 'member_id,member_mobile,access_id,left_right_area', 'union', null, 'left_right_area desc');
		if (empty($child_list)) {
			$child_node[0] = $s_node;
			$child_node[1] = $s_node;
		} else {
			foreach ($child_list as $key => $child) {
				$child_list[$key]['type'] = 'slave';
				// if (count($child_list) == 2) {
				// 	if ($child_list[0]['left_right_area'] == 2) {
				// 		$temp = $child_list[1];
				// 		$child_list[1] = $child_list[0];
				// 		$child_list[0] = $temp;
				// 	}
				// }
				// if (count($child_list) == 1) {
				// 	if ($child_list[0]['left_right_area'] == 2) {
				// 		$temp = $child_list[0];
				// 		$child_list[0] = $s_node;
				// 		$child_list[1] = $temp;
				// 	}
				// 	if ($child_list[0]['left_right_area'] == 1) {
				// 		$child_list[1] = $s_node;
				// 	}
				// }
				// if (empty($child_list[$key]['member_id'])) {
				// 	$key = 1;
				// }
				$child_list[$key]['child'] = $this->getChildNode($child['member_id'], $cur_layer + 1, $max_layer);
			}
			$child_node[] = $child_list[0] ? $child_list[0] : $s_node;
			$child_node[] = $child_list[1] ? $child_list[1] : $s_node;
		}
		return $child_node;
	}

	/**
	 * buildFamiltree 生成族谱html
	 * @param  [type] $tree [description]
	 * @return [type]       [description]
	 */
	public function buildFamiltree($tree) {
		$html = '<ul>';
		$left_right_area = '';
		foreach ($tree as $v) {
			$html .= '<li>';
			if ($v['type'] == 'master') {
				$html .= '<a href="" style="width:280px;height:60px;">' . $v['member_mobile'] . '<br/>' . $v['performance1'] . '  ' . $v['performance2'] . '<br/>' . $v['surplus1'] . '  ' . $v['surplus2'] . '</a>';
			} else {
				if ($v['member_mobile']) {
					$left_right_area = $v['left_right_area'];
					$html .= '<a href="index.php?act=' . $_GET['act'] . '&op=access_family_tree&mobile=' . $v['member_mobile'] . '&master=0" style="width:120px;height:40px;padding-top:25px;">' . $v['member_mobile'] . '</a>';
				} else {
					// if (empty($left_right_area)) {
					// 	$left_right_area = 1;
					// 	$html .= '<a href="index.php?act=' . $_GET['act'] . '&op=access_register&area=left&access_id=' . $v['access_id'] . ' " style="width:120px;height:40px;padding-top:25px;"><u>注册</u></a>';
					// } elseif ($left_right_area == 1) {
					$html .= '<a href="index.php?act=' . $_GET['act'] . '&op=access_register&area=right&access_id=' . $v['access_id'] . ' " style="width:120px;height:40px;padding-top:25px;"><u>注册</u></a>';
					// }
				}
			}
			if (isset($v['child']) && !empty($v['child'])) {
				$html .= $this->buildFamiltree($v['child']);
			}
			$html .= '</li>';
		}
		$html .= '</ul>';
		return $html;
	}

	/**
	 * buildTreeHtml 生成树状图
	 * @param  [type] $infoArr   [description]
	 * @param  string $parent_id [description]
	 * @return [type]            [description]
	 */
	public function buildTreeHtml($infoArr, $parent_id = '') {
		$html = '';
		foreach ($infoArr as $value) {
			$html .= '<tr data-tt-id="' . $value['member_mobile'] . '" data-tt-parent-id="' . $parent_id . '">';
			$html .= '<td class="tree">' . $value['member_mobile'] . '【'.str_replace(array(0,1,2,3,4,5),array("体验用户","VIP","店主","合伙人","高级合伙人","战略合伙人"),$value['member_level']).'】业绩：'. $value['total_bd_product_consume'] .'</td>';
			$html .= '</tr>';
			if (!empty($value['child'])) {
				$html .= $this->buildTreeHtml($value['child'], $value['member_mobile']);
			}
		}
		return $html;
	}

	/**
	 * checkAccessInfoById 检测接点人
	 * @param  [type] $member_id [description]
	 * @return [type]            [description]
	 */
	public function checkAccessInfoById($member_id) {
		$info = $this->getMemberExtendInfo(array('member_id' => $member_id), '*', 'union');
		if (empty($info)) {
			return array('error' => "接点人不存在");
		}
		$count = $this->getMemberAccessCount($member_id);
		if ($count >= 2) {
			return array('error' => "此接点人已接满2人");
		}
		return $info;
	}

	/**
	 * checkInviteInfoByMobile 检测邀请人
	 * @param  [type] $member_mobile [description]
	 * @return [type]                [description]
	 */
	public function checkInviteInfoByMobile($member_mobile) {
		$info = $this->getMemberExtendInfo(array('member_mobile' => $member_mobile), '*', 'union');
		if (empty($info)) {
			return array('error' => "邀请人不存在");
		}
		return $info;
	}

	/**
	 * getNewMemberNum 获取新注册用户数量
	 * @param  [type] $condition [description]
	 * @return [type]            [description]
	 */
	public function getNewMemberNum($condition) {
		return $this->table('member_extend')->where($condition)->count();
	}

	/**
	 * _parseChildFieldInfo 解析下级族谱数组
	 * @param  [type] $info [description]
	 * @param  [type] &$arr [description]
	 * @return [type]       [description]
	 */
	private function _parseChildFieldInfo($info, &$arr, $field) {
		foreach ($info as $v) {
			if (empty($v['child'])) {
				$arr[] = $v[$field];
			} else {
				$arr[] = $v[$field];
				$this->_parseChildFieldInfo($v['child'], $arr, $field);
			}
		}
	}

	/**
	 * _parseParentFieldInfo 解析上级族谱数组
	 * @param  [type] $info  [description]
	 * @param  [type] &$arr  [description]
	 * @param  [type] $field [description]
	 * @return [type]        [description]
	 */
	private function _parseParentFieldInfo($info, &$arr, $field) {
		foreach ($info as $v) {
			if (empty($v['parent'])) {
				$arr[] = $v[$field];
			} else {
				$arr[] = $v[$field];
				$this->_parseParentFieldInfo($v['parent'], $arr, $field);
			}
		}
	}

	public function setMemberExtendFieldValue($condition, $field, $value) {
		return $this->table('member_extend')->where($condition)->set_field($field, $value);
	}

    /**
     * 修改会员等级
     * @param $member_id int
     * @param $level int
     * @return mixed
     */
	public function updateMemberLevel($member_id,$level){
        try{
            $res = $this->table('member_extend')->where(array('me_member_id'=>$member_id))->update(['member_level'=>$level]);
        }catch (Exception $e){
            return callback(false,$e->getMessage());
        }
        return $res;
	}

    /**
     * 获取上级个人信息(及扩展信息)
     * @param $member_id
     * @return mixed
     */
	public function getParentInfo($member_id,$fields ='*'){
	    $parent_id = $this->table('member_extend')->where(['me_member_id'=>$member_id])->field('invite_id')->find();
	    $res = $this->getMemberExtendInfo(['member_id'=>$parent_id['invite_id']],$fields,$type = 'union');
	    return $res;
    }

    /**
     * 获取直推下级信息
     * @param $member_id
     * @param string $fields
     * @return mixed
     */
    public function getChildInfoArr($member_id,$fields='*'){
	    $res = $this->table('member_extend')->where(['invite_id'=>$member_id])->field($fields)->select();
	    return $res;
    }

    /**
     * getInviteChildMobileArr 获取所有推荐下级会员等级数组
     * @param  [type] $member_id [description]
     * @return [type]            [description]
     */
    public function getAllChildLevelArr($member_id) {
        $level_arr = array();
        $invite[] = $this->getInviteFamilyTreeArr($member_id, 'member_level');
        $this->_parseChildFieldInfo($invite, $level_arr, 'member_level');
        return $level_arr;
    }

    /**
     * getInviteParentMemberIdArr 获取推荐上级会员Level数组
     * @param  [type] $member_id [description]
     * @return [type]            [description]
     */
    public function getInviteParentMemberLevelArr($member_id) {
        $member_id_arr = array();
        $invite_arr = $this->getInviteParentArr($member_id, 'member_level');
        $this->_parseParentFieldInfo($invite_arr, $member_id_arr, 'member_level');
        return $member_id_arr;
    }

    /**
     * 获取N级推荐上级等级数组
     * @param $id_arr
     * @param string $field
     * @return mixed
     */
    public function getTeamMemberLevelArr($id_arr){
        $res = $this->table('member_extend')->where(array('me_member_id'=>array('in',$id_arr)))->field('member_level')->order('me_member_id desc')->select();
        foreach ($res as $k=>$v){
            static $result = array();
            $result[$k] = $v['member_level'];
        }
      return $result;
    }

    /**
     * 获取N级推荐上级库存数组
     * @param $id_arr
     * @param string $field
     * @return mixed
     */
    public function getTeamMemberInventoryArr($id_arr){
        $res = $this->table('member,member_extend')->where(array('member_id'=>array('in',$id_arr)))->field('member.member_id,member_extend.member_level,member.inventory')->join('union')
            ->on('member.member_id=member_extend.me_member_id')->order('member.member_id desc')->select();
        foreach ($res as $k=>$v){
            if ($v['member_level'] == 0){
                $fictitious_inventory[$v['member_id']] = $v['inventory'];
            }else{
                $fictitious_inventory[$v['member_id']] = $v['inventory']/$this->level_ratio[$v['member_level']];
            }
        }
        return $fictitious_inventory;
    }

    /**
     * 修改团队中各等级的人数
     * @param $arr  array 会员ID数组
     * @param $level  int 修改等级
     * @return  multitype
     */
    public function updateChildLevelNum($arr,$level){
        $updateField = str_replace([1,2,3,4,5],["child_vip_num","child_shopkeeper_num","child_partner_num","child_senior_partner_num","child_strategic_partner_num"],$level);
        try{
            $condition['member_id'] = array('in',$arr);
            $res = $this->table('child_level_num')->where($condition)->setInc($updateField,1);
        }catch (Exception $e){
            return callback(false,$e->getMessage());
        }
        return $res;
    }

    /**
     * 修改团队中各等级的人数
     * @param $arr  array 会员ID数组
     * @param $level  int 修改等级
     * @return  multitype
     */
    public function DecChildLevelNum($arr,$level){
        $updateField = str_replace([1,2,3,4,5],["child_vip_num","child_shopkeeper_num","child_partner_num","child_senior_partner_num","child_strategic_partner_num"],$level);
        try{
            $condition['member_id'] = array('in',$arr);
            $res = $this->table('child_level_num')->where($condition)->setDec($updateField,1);
        }catch (Exception $e){
            return callback(false,$e->getMessage());
        }
        return $res;
    }

    /**
     * 团队各等级人数的数组
     * @param $id_arr array 须获取该信息的会员ID数组
     * @param string $field 获取字段
     * @return mixed
     */
    public function getTeamAllLevelNum($id_arr){
//      $this->table('child_level_num')->where(array('member_id'=>array('in',$id_arr)))->field($field)->order('member_id desc')->select();
        $res = $this->table('child_level_num,member_extend')
            ->field('child_level_num.*,member_extend.member_level')
            ->where(array('child_level_num.member_id'=>array('in',$id_arr)))
            ->join('union')
            ->on('child_level_num.member_id=member_extend.me_member_id')
            ->order('child_level_num.member_id desc')
            ->select();
       return $res;
    }

    public function getChildLevelNum($member_id,$level){
        $res = $this->table('member_extend')->where(['invite_id'=>$member_id,'member_level'=>['egt',$level]])->field('me_member_id')->count();
        return $res;
    }

    /**
     * admin更改会员账户余额
     * @param $type
     * @param $data
     * @return array
     */
    public function changeBalance($type,$data){
        if (!in_array($type, array('consume', 'bonus', 'recharge', 'withdraw', 'admin'))) {
            return array('error' => '参数错误');
        }
        $member_id = $data['member_id'];
        $operate_type = $data['operate_type'];
        $amount = floatFormat($data['amount']);
        $info = $this->getMemberExtendInfo(array('me_member_id' => $member_id), "*", 'union');
        if (empty($info)) {
            return array('error' => '会员信息错误');
        }
        Model::beginTransaction();
        if ($operate_type == 'add'){
            try{
                Model('member')->where(array('member_id' => $member_id))->setInc('balance',$amount);
            }catch (Exception $e){
                return array('error'=>$e->getMessage());
            }
        }else{
            try{
                Model('member')->where(array('member_id' => $member_id))->setDec('balance',$amount);
            }catch (Exception $e){
                return array('error'=>$e->getMessage());
            }
        }
        //添加余额调整记录
        $insert['member_id'] = $member_id;
        $insert['member_name'] = $data['member_name'];
        $insert['member_mobile'] = $info['member_mobile'];
        $insert['operate_amount'] = $operate_type == 'add'?$amount:-$amount;
        $insert['surplus_amount'] = $info['balance']+$insert['operate_amount'];
        $insert['add_time'] = TIMESTAMP;
        $insert['type'] = "admin";
        $insert['content'] = "管理员为会员调整";
        try{
            Model('member_balance_record')->insert($insert);
            Model::commit();
        }catch (Exception $e){
            Model::rollback();
            return array('error'=>$e->getMessage());
        }
        return array('success'=>'调整成功');
    }

    /**
     * changeBean 更改会员余额
     * @param  [type] $type [description]
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function changeMemberBalance($type, $data) {
        if (!in_array($type, array('consume', 'bonus', 'recharge', 'withdraw', 'admin','equity','inventory','city',))) {
            return array('error' => '参数错误');
        }
        $member_id = $data['member_id'];
        $info = $this->getMemberExtendInfo(array('me_member_id' => $member_id), "*", 'union');
        $amount = floatFormat($data['amount']);
        $member_name = $data['member_name'] = $info['member_name'];
        $addtime = TIMESTAMP;
        $sn = makeSN($type, $member_id);
        if (empty($info)) {
            return array('error' => '会员信息错误');
        }

        try {
            $model_balance = Model('balance');
            $log_data = array();
            $log_data['member_id'] = $member_id;
            $log_data['member_name'] = $member_name;
            $log_data['member_mobile'] = $info['member_mobile'];
            $log_data['add_time'] = $addtime;
            $log_data['type'] = $type;
            $log_data['sn'] = $sn;
            $condition = array('me_member_id' => $member_id);
            Model()->beginTransaction();
            switch ($type) {
                //余额消费
                case 'consume':
                    $log_data['surplus_amount'] = floatFormat($info['balance'] - $amount);
                    // 余额记录
                    $log_data['operate_amount'] = 0 - $amount;
                    $log_data['content'] = "会员" . $info['member_mobile'] . " 消费:" . $amount;
                    $insert_id = $model_balance->addBalanceLog($log_data);

                    // 余额消费记录
                    $cl_log_data = array();
                    $cl_log_data['cl_member_id'] = $member_id;
                    $cl_log_data['cl_amount'] = $amount;
                    $cl_log_data['cl_addtime'] = $addtime;
                    $cl_log_data['cl_sn'] = $sn;
                    $cl_log_data['cl_desc'] = '会员：'.$info['member_mobile'].'消费'.$amount;
                    $model_balance->addBalanceConsumeLog($cl_log_data);
                    Log::memberRecord($log_data['cl_desc'], $member_id);
                    //更改会员账户余额
                    Model('member')->addBalance($member_id,-$amount);
                    break;
                //提现申请
                case 'withdraw':
                    //余额记录
                    $model_bank = Model('bank');
                    $bank_info = $model_bank->getMemberBankInfo(array('mb_id' => $data['mb_id']));
                    $log_data['surplus_amount'] = floatFormat($info['balance'] - $amount);
                    $log_data['operate_amount'] = 0 - $amount;
                    $log_data['content'] = "会员" . $info['member_mobile'] . "申请提现,金额:" . $amount;
                    $insert_id = $model_balance->addBalanceLog($log_data);
                    Log::memberRecord($log_data['lg_desc'], $member_id);
                    //提现记录
                    $wl_log_data = array();
                    $wl_log_data['wl_member_id'] = $member_id;
                    $wl_log_data['wl_sn'] = $sn;
                    $wl_log_data['wl_amount'] = $amount;
                    $wl_log_data['wl_addtime'] = $addtime;
                    $wl_log_data['wl_desc'] = '会员' . $info['member_mobile'] . ' 申请提现'. $amount.",手续费:".$data['poundage'];
                    $wl_log_data['wl_account_name'] = $bank_info['mb_account_name'];
                    $wl_log_data['wl_account_bank_name'] = $bank_info['mb_account_bank_name'];
                    $wl_log_data['wl_account_branch'] = $bank_info['mb_account_branch'];
                    $wl_log_data['wl_account_num'] = $bank_info['mb_account_num'];
                    $wl_log_data['wl_fee'] = floatFormat($amount * C('withdraw_charge_percentage') / 100.00);
                    $model_balance->addBalanceWithdrawLog($wl_log_data);
                    Log::memberRecord($wl_log_data['wl_desc'], $member_id);
                    //更改会员账户余额
                    Model('member')->addBalance($member_id,-$amount);
                    break;
                //兑换股权
                case 'equity':
                    // 余额记录
                    $log_data['surplus_amount'] = floatFormat($info['balance'] - $data['balance_amount']);
                    $log_data['operate_amount'] = 0 - $data['balance_amount'];
                    if ($data['balance_amount'] <0){
                        $log_data['content'] = "会员" . $info['member_mobile'] . " 兑换股权审核未通过，退还金额".$log_data['operate_amount'];
                        //更改会员账户余额
                        Model('member')->addBalance($member_id,-$data['balance_amount']);
                        $insert_id = $model_balance->addBalanceLog($log_data);
                        break;
                    }
                    $log_data['content'] = "会员" . $info['member_mobile'] . " 兑换股权:" . $amount."余额:".$log_data['surplus_amount'];
                    $insert_id = $model_balance->addBalanceLog($log_data);
                    //股权记录
                    $eq_log_data['elg_member_id'] = $member_id;
                    $eq_log_data['elg_member_name'] = $member_name;
                    $eq_log_data['elg_sn'] = $sn;
                    $eq_log_data['elg_amount'] = $amount;
                    $eq_log_data['elg_balance_amount'] = $data['balance_amount'];
                    $eq_log_data['elg_equity_amount'] = $info['member_equity'] + $amount;
                    $eq_log_data['elg_type'] = 'exchange';
                    $eq_log_data['elg_desc'] = "会员" . $info['member_mobile'] . " 兑换股权:" . $amount;
                    $eq_log_data['elg_addtime'] = $addtime;
                    Model('equity')->addBalanceEquityLog($eq_log_data);
                    Log::memberRecord($eq_log_data['elg_desc'], $member_id);
                    //更改会员账户余额
                    Model('member')->addBalance($member_id,-$data['balance_amount']);
                    break;
                //会员分润
                case 'bonus':
                    //余额记录
                    $log_data['surplus_amount'] = floatFormat($info['balance'] + $data['amount']);
                    $log_data['operate_amount'] =  $data['amount'];
                    $log_data['content'] = $data['content'];
                    $insert_id = $model_balance->addBalanceLog($log_data);
                    //更改会员账户余额
                    Model('member')->addBalance($member_id,$data['amount']);
                    break;
                //库存转移
                case 'inventory':
                    $log_data['surplus_amount'] = floatFormat($info['balance'] + $data['amount']);
                    $log_data['operate_amount'] =  $data['amount'];
                    $log_data['content'] = "会员" . $info['member_mobile'] . "库存转移:" . $amount."余额:".$log_data['surplus_amount'];
                    $insert_id = $model_balance->addBalanceLog($log_data);
                    //更改会员账户余额
                    $res = Model('member')->addBalance($member_id,$data['amount']);
//                    var_dump($res);
                    break;
                //管理员调整
                case 'admin':
                    $operate_type = $data['operate_type'];
                    $admin_name = $data['admin_name'];
                    $al_log_data = array();
                    if ($operate_type == 'add') {
                        $log_data['surplus_amount'] = floatFormat($info['balance'] + $data['amount']);
                        $log_data['operate_amount'] = $amount;
                        $log_data['content'] = '管理员增加会员' . $info['member_mobile'] . '余额:' . $amount;
                        $res = Model('member')->addBalance($member_id,$data['amount']);
                        $insert_id = $model_balance->addBalanceLog($log_data);
                        Log::memberRecord('会员' . $info['member_mobile'] . '余额增加' . $amount, $member_id);
                    } elseif ($operate_type == 'sub') {
                        $log_data['surplus_amount'] = floatFormat(($this->getAvailBeanByInfo($info)) - $amount);
                        $log_data['operate_amount'] = 0 - $amount;
                        $lg_desc =  '管理员增加会员' . $info['member_mobile'] . '余额:' . $amount;
                        $insert_id = $model_balance->addBalanceLog($log_data);
                        $res = Model('member')->addBalance($member_id,-$data['amount']);
                        Log::memberRecord('会员' . $info['member_mobile'] . '余额减少' . $amount, $member_id);
                    }
                    Log::memberRecord($lg_desc, $member_id);
                    break;
                case 'city':
                    // 余额记录
                    if ($data['get_city'] == 'get' ){
                        $log_data['surplus_amount'] = floatFormat($info['balance'] - $amount);
                        $log_data['operate_amount'] = 0 - $amount;
                        $log_data['content'] = "会员" . $info['member_mobile'] . " 抢占城主消费余额".$amount;
                        //更改会员账户余额
                        $res = Model('member')->addBalance($member_id,-$amount);
                        $insert_id = $model_balance->addBalanceLog($log_data);
                        Log::memberRecord($log_data['content'], $member_id);
                    }else{
                        $log_data['surplus_amount'] = floatFormat($info['balance'] + $amount);
                        $log_data['operate_amount'] = $amount;
                        $log_data['content'] = "会员" . $info['member_mobile'] . " 城主被抢占，返还余额".$amount;
                        //更改会员账户余额
                        Model('member')->addBalance($member_id,$amount);
                        $insert_id = $model_balance->addBalanceLog($log_data);
                        Log::memberRecord($log_data['content'], $member_id);
                    }
                    break;
            }
            Model()->commit();
            return $log_data;
        } catch (Exception $e) {
            Model()->rollback();
            return array('error' => $e->getMessage());
        }
    }

    /**
     * 初次兑换股权成功的会员，更改其初次兑换状态
     * @param $member_id
     * @return array
     */
    public function changeEquityBuyStatus($member_id){
        try{
            $update['is_first_buy_equity'] = 0;
            return $this->table('member_extend')->where(['me_member_id'=>$member_id])->update($update);
        }catch (Exception $e){
            return array(false,'error'=>$e->getMessage());
        }
    }

    public function getMemberLevel($member_id){
       $res = $this->table('member_extend')->field('member_level')->where(array('me_member_id'=>$member_id))->find();
        return $res['member_level'];
    }

	/**
	 * 查询会员统计
	 * @param $member_id
	 * @return array
	 */
	public function getStatistics($condition,$field = '*'){
		 return $res = $this->table('member_extend')->field($field)->where($condition)->select();
	}

	/**
	 * 查询会员统计
	 * @param $member_id
	 * @return array
	 */
	public function getDonateAmountSun($field){
		return $res = $this->table('balance_withdraw_record')->field($field)->find();
	}


    /**
     * order_pay 订单支付
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function order_pay($data) {
       $member_info = $this->getMemberExtendInfo(['member_id'=>$data['member_id']],'*','union');
       // $discount = Model('level')->getMemberDiscount($member_info['member_level']);
        try {
            $this->beginTransaction();
            $log_data = array();
            $member_id = $data['member_id'];
            $amount = $data['amount'] ;
            $log_data['member_id'] = $member_id;
            $log_data['member_name'] = $data['member_name'];
            $log_data['sn'] = $data['order_sn'];
            $log_data['type'] = $data['type']?$data['type']:'consume';
            $log_data['add_time'] = TIMESTAMP;
            $log_data['surplus_amount'] = floatFormat(($this->getMemberBalanceById($member_id)) - $amount);
            $log_data['operate_amount'] = 0 - $amount;
            $log_data['content'] = '订单支付，支付金额为' . $amount;
            $this->setIncMemberExtendField(array('me_member_id' => $member_id), array('total_consume' => $amount));
            Log::memberRecord('会员' . $member_id . '消费' . $amount, $member_id);
            Log::memberRecord($log_data['content'], $member_id);
            $this->changeMemberBalance($log_data['type'], $data);
            //判断是否为报单产品
            if ($data['product_num']){
                for ($i=1;$i<=(int)$data['product_num'];$i++){
                    $diff_profit = Logic('member_level')->buyReportProduct($member_info['member_id'],$data['pay_sn']);
                    if (!$diff_profit['state']){
                        $data['member_id'] = $member_info['member_id'];
                        $data['msg'] = $diff_profit['msg'];
                        $data['time'] = date("Y-m-d H:i:s",time());
                        $data['time_level'] = $member_info['member_level'];
                        //日志
                    }
                }
            }
            $this->commit();
            return array('succ' => "支付成功");
        } catch (Exception $exception) {
            $this->rollback();
            return array('error' => "支付失败2");
        }
    }


    /**
     * getInviteChildMobileArr 获取推荐下级数组
     * @param  [type] $member_id [description]
     * @return [type]            [description]
     */
    public function getInviteChildInfoArr($member_id) {
        $member_info = array();
        $invite[] = $this->getInviteFamilyTreeArr($member_id,'*');
        $this->_parseChildFieldInfo($invite, $member_info, 'member_id');
        return $member_info;
    }


    /**
     * getMemberExtendList 获取会员以及扩展列表
     * @param  [type] $condition [description]
     * @param  string $fields     [description]
     * @return [type]            [description]
     */
    public function getMemberExtendList2($condition = array(), $fields = '*'){
        $res = $this
            ->table('member,member_extend')
            ->field($fields)
            ->where($condition)
            ->join('inner')
            ->on('member.member_id=member_extend.me_member_id')
            ->select();
        return $res;
    }



    public function qwer($data){
        try{
            return $this->table('child_level_num')->insert($data);
        }catch (Exception $e){
            var_dump($e);
        }
    }

	//获取VIP套餐金额总数
	  public function getVIPAmount($condition){
        return $this->table('member_extend')->where($condition)->sum('set_meal_amount');
    }

    /**
	 *  修改融云token信息
	 * @param unknown $condition
	 * @param string $fields
	 */
	public function rctokenupdate($condition,$data) {
		return $this->table('member')->where($condition)->update($data);
	}
    /**
     * order_pay 抢城主订单支付
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function city_order_pay($data) {
        $member_info = $this->getMemberExtendInfo(['member_id'=>$data['member_id']],'*','union');
        // $discount = Model('level')->getMemberDiscount($member_info['member_level']);
        try {
            $this->beginTransaction();
            $log_data = array();
            $member_id = $data['member_id'];
            $amount = $data['amount'] ;
            $log_data['member_id'] = $member_id;
            $log_data['member_name'] = $data['member_name'];
            $log_data['sn'] = $data['order_sn'];
            $log_data['type'] = 'consume';
            if (!empty($data['type'])){
                $log_data['type'] =$data['type'];
            }
            $log_data['add_time'] = TIMESTAMP;
            $log_data['amount'] = floatFormat($amount);
            $log_data['content'] = '订单支付，支付金额为' . $amount;
            $log_data['get_city'] = 'get';
            $this->setIncMemberExtendField(array('me_member_id' => $member_id), array('total_consume' => $amount));
            Log::memberRecord('会员' . $member_id . '消费' . $amount, $member_id);
            Log::memberRecord($log_data['content'], $member_id);
            $res = $this->changeMemberBalance($log_data['type'], $log_data);
            //判断是否为报单产品
            if ($data['product_num']){
                for ($i=1;$i<=(int)$data['product_num'];$i++){
                    $diff_profit = Logic('member_level')->buyReportProduct($member_info['member_id'],$data['pay_sn']);
                    if (!$diff_profit['state']){
                        $data['member_id'] = $member_info['member_id'];
                        $data['msg'] = $diff_profit['msg'];
                        $data['time'] = date("Y-m-d H:i:s",time());
                        $data['time_level'] = $member_info['member_level'];
                        //日志
                    }
                }
            }
            $this->commit();
            return array('succ' => "支付成功");
        } catch (Exception $exception) {
            $this->rollback();
            return array('error' => "支付失败2");
        }
    }

    /**
     * order_pay 被抢城主金额返还
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function city_lord_reward($data) {
        $member_info = $this->getMemberExtendInfo(['member_id'=>$data['member_id']],'*','union');
        // $discount = Model('level')->getMemberDiscount($member_info['member_level']);
        try {
            $this->beginTransaction();
            $log_data = array();
            $member_id = $data['member_id'];
            $amount = $data['amount'] ;
            $log_data['member_id'] = $member_id;
            $log_data['member_name'] = $data['member_name'];
            $log_data['sn'] = $data['order_sn'];
            $log_data['type'] = 'consume';
            if (!empty($data['type'])){
                $log_data['type'] =$data['type'];
            }
            $log_data['add_time'] = TIMESTAMP;
            $log_data['amount'] = floatFormat($amount);
            $log_data['content'] = '订单支付，支付金额为' . $amount;
            $log_data['get_city'] = 'get';
            $this->setIncMemberExtendField(array('me_member_id' => $member_id), array('total_consume' => $amount));
            Log::memberRecord('会员' . $member_id . '消费' . $amount, $member_id);
            Log::memberRecord($log_data['content'], $member_id);
            $res = $this->changeMemberBalance($log_data['type'], $log_data);
            //判断是否为报单产品
            if ($data['product_num']){
                for ($i=1;$i<=(int)$data['product_num'];$i++){
                    $diff_profit = Logic('member_level')->buyReportProduct($member_info['member_id'],$data['pay_sn']);
                    if (!$diff_profit['state']){
                        $data['member_id'] = $member_info['member_id'];
                        $data['msg'] = $diff_profit['msg'];
                        $data['time'] = date("Y-m-d H:i:s",time());
                        $data['time_level'] = $member_info['member_level'];
                        //日志
                    }
                }
            }
            $this->commit();
            return array('succ' => "支付成功");
        } catch (Exception $exception) {
            $this->rollback();
            return array('error' => "支付失败2");
        }
    }

}
