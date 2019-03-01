<?php
/**
 * 财产中心
 *
 */
class member_propertyControl extends apiEncryptMemberControl {
    private $member_system_set;


	public function __construct() {
		parent::__construct();
		$this->getMemberSystemSet();
	}

    /**
     * 获取会员系统部分设置
     */
	private function getMemberSystemSet(){
	    $this->member_system_set = Model()->table('member_system_set')->find();
    }

	/**
	 * member_beanOp 我的金豆
	 * @return [type] [description]
	 */
	public function member_beanOp() {
		$model_member = Model('member');
		$member_bean_amount = $model_member->getAvailBalanceById($this->member_info['member_id']);
		responseApiJson(1, '', array('amount' => floatFormat($member_bean_amount)));
	}

	/**
	 * member_bean_listOp 金豆列表
	 * @return [type] [description]
	 */
	public function member_bean_listOp() {
		$model_bean = Model('bean');
		$bean_list = $model_bean->getBeanLogList(array('lg_member_id' => $this->member_info['member_id']));
		$total_num = $model_bean->gettotalpage();
		$log_list = array();
		foreach ($bean_list as $v) {
			$param = array();
			$param['id'] = $v['lg_id'];
			$lg_sn = $v['lg_sn'];

			if ($v['lg_type'] == 'donate') {
				$bean_donate_info = $model_bean->getBeanDonateLogInfo(array('dl_sn' => $v['lg_sn']));
				// if (!empty($bean_donate_info)) {
				// $member_id = $v['lg_member_id'];
				if ($v['lg_amount'] < 0) {
					$member_id = $bean_donate_info['dl_to_member_id'];
					$receive = "接收人:";
				} else {
					$member_id = $bean_donate_info['dl_from_member_id'];
					$receive = "转赠人:";
				}
				$info = Model('member_extend')->getMemberInfo(array('member_id' => $member_id), 'member_mobile');
				$param['receive'] = $receive . $info['member_mobile'];
				$param['title'] = replaceBeanType($v['lg_type']) . '[' . $lg_sn . ']';

			} elseif ($v['lg_type'] == 'rebate' || $v['lg_type'] == 'withdraw') {
				$param['title'] = replaceBeanType($v['lg_type']) . '[' . $lg_sn . ']';
				$param['receive'] = $v['lg_desc'] ? $v['lg_desc'] : '';
			} else {
				$param['title'] = replaceBeanType($v['lg_type']) . '[' . $lg_sn . ']';
				$param['receive'] = '';
			}
			$param['amount'] = $v['lg_amount'] > 0 ? '+' . $v['lg_amount'] : $v['lg_amount'];
			$param['time'] = date('Y-m-d H:i:s', $v['lg_addtime']);
			$log_list[] = $param;
		}
		responseApiJson(1, '', array('list' => $log_list, 'total_num' => $total_num));
	}

	/**
	 * member_bean_listOp 银豆列表
	 * @return [type] [description]
	 */
	public function silver_member_bean_listOp() {
		$model_bean = Model('bean');
		$bean_list = $model_bean->getSilverBeanLogList(array('slg_member_id' => $this->member_info['member_id']));
		$total_num = $model_bean->gettotalpage();
		$log_list = array();
		foreach ($bean_list as $v) {
			$param = array();
			$param['id'] = $v['slg_id'];
			$slg_sn = $v['slg_sn'];

			if ($v['slg_type'] == 'donate') {
				$param['title'] = replaceBeanType($v['slg_type']) . '[' . $slg_sn . ']';
				$bean_donate_info = $model_bean->getSilverBeanLogInfo(array('slg_sn' => $v['slg_sn'], 'slg_amount' => 0 - $v['slg_amount']));

				if (!empty($bean_donate_info)) {
					$member_id = $bean_donate_info['slg_member_id'];

					if ($v['slg_amount'] < 0) {
						$receive = "接收人:";
						// $bean_donate_info = $model_bean->getSilverBeanLogInfo(array('slg_sn' => $v['slg_sn']));
					} else {
						$receive = "转赠人:";
					}
					//Log::record($member_id, 'LOG');
					$info = Model('member_extend')->getMemberInfo(array('member_id' => $member_id), 'member_mobile');
					$param['receive'] = $receive . $info['member_mobile'];
				} else {
					$param['receive'] = "";
				}

			} elseif ($v['slg_type'] == 'rebate' || $v['slg_type'] == 'withdraw') {
				$param['title'] = replaceBeanType($v['slg_type']) . '[' . $slg_sn . ']';
				$param['receive'] = $v['slg_desc'] ? $v['slg_desc'] : '';
			} else {
				$param['title'] = replaceBeanType($v['slg_type']) . '[' . $slg_sn . ']';
				$param['receive'] = '';
			}
			$param['amount'] = $v['slg_amount'] > 0 ? '+' . $v['slg_amount'] : $v['slg_amount'];
			$param['time'] = date('Y-m-d H:i:s', $v['slg_addtime']);
			$log_list[] = $param;
		}
		responseApiJson(1, '', array('list' => $log_list, 'total_num' => $total_num));
	}

	/**
	 * member_beanOp 我的银豆
	 * @return [type] [description]
	 */
	public function silver_beanOp() {
		$model_member_extend = Model('member_extend');
		$member_silver_bean_amount = $model_member_extend->getAvailSilverBeanById($this->member_info['member_id']);
		responseApiJson(1, '', array('amount' => floatFormat($member_silver_bean_amount)));
	}
	/**
	 * paypwd_checkOp 支付密码检测
	 * @return [type] [description]
	 */
	public function paypwd_checkOp() {
		if (!$this->checkPaypwd()) {
			responseApiJson(0, "未设置支付密码");
		}
		responseApiJson(1, "");
	}

	/**
	 * bean_operate_withdrawOp 金豆提现
	 * @return [type] [description]
	 */
	public function bean_operate_withdrawOp() {
		if (!$this->member_info['member_state']) {
			responseApiJson(0, '会员已冻结，不能操作账户');
		}
		$validate = new Validate();
		$validate->validateparam = array(
			array('input' => $_POST['amount'], 'require' => "true", 'message' => "请输入提现金额"),
			array('input' => $_POST['amount'], 'validator' => 'number', 'message' => "提现金额必须是整数"),
			array('input' => $_POST['bank_id'], 'require' => "true", 'message' => "请选择银行账号"),
			array('input' => $_POST['paypwd'], 'require' => "true", 'message' => "请输入支付密码"),
		);
		$error = $validate->validate();
		if ($error != "") {
			responseApiJson(0, $error);
		}
		if (md5(trim($_POST['paypwd'])) != $this->member_info['member_paypwd']) {
			responseApiJson(0, "支付密码错误");
		}
		$member_id = $this->member_info['member_id'];

		$bank_info = Model('bank')->getMemberBankInfo(array('mb_account_num' => $_POST['bank_id'], 'mb_member_id' => $member_id));
		if (empty($bank_info)) {
			responseApiJson(0, "提现银行账号不存在");
		}
		$amount = floatval($_POST['amount']);
		$model_member_extend = Model('member_extend');
		if ($model_member_extend->getAvailBeanById($member_id) < $amount) {
			responseApiJson(0, "可提金额不足");
		}
		if ($amount % 100 != 0) {
			responseApiJson(0, "提现金额须整百");
		}
		if ($amount < C('withdraw_charge_limit')) {
			responseApiJson(0, "提现金额须大于" . C('withdraw_charge_limit'));
		}
		$withdraw_data = array();
		$withdraw_data['member_id'] = $member_id;
		$withdraw_data['member_name'] = $this->member_info['member_name'];
		$withdraw_data['amount'] = $amount;
		$withdraw_data['mb_id'] = $bank_info['mb_id'];
		$result = $model_member_extend->changeBean('withdraw', $withdraw_data);
		if (isset($result['error'])) {
			responseApiJson(0, "提现失败");
		} else {
			responseApiJson(1, "提现申请成功,请等待审核...");
		}
	}

	/**
	 * bean_operate_donateOp 金豆转赠
	 * @return [type] [description]
	 */
	public function bean_operate_donateOp() {
		if (!$this->member_info['member_state']) {
			responseApiJson(0, '会员已冻结，不能操作账户');
		}
		$validate = new Validate();
		$validate->validateparam = array(
			array('input' => $_POST['mobile'], 'require' => "true", 'message' => "请输入接收人手机号"),
			array('input' => $_POST['mobile'], 'validator' => "mobile", 'message' => "输入的手机号格式不正确"),
			array('input' => $_POST['amount'], 'require' => "true", 'message' => "请输入金豆转赠数量"),
			array('input' => $_POST['amount'], 'validator' => 'number', 'message' => "输入的转赠数量不是数字"),
			array('input' => $_POST['paypwd'], 'require' => "true", 'message' => "请输入支付密码"),
		);
		$error = $validate->validate();
		if ($error != '') {
			responseApiJson(0, $error);
		}
		if (md5(trim($_POST['paypwd'])) != $this->member_info['member_paypwd']) {
			responseApiJson(0, "支付密码错误");
		}
		$model_member_extend = Model('member_extend');
		$r_info = $model_member_extend->getMemberExtendInfo(array('member_mobile' => $_POST['mobile']), '*', 'union');
		if (empty($r_info)) {
			responseApiJson(0, "接收人不存在");
		}
		$member_id = $this->member_info['member_id'];
		$amount = $_POST['amount'];
		if ($member_id == $r_info['member_id']) {
			responseApiJson(0, "自己不能转赠金豆给自己");
		}
		if ($r_info['is_service']) {
			responseApiJson(0, "服务中心不能接受转赠金豆");
		}
		if (!$this->member_info['is_service']) {
			if ($r_info['member_equity'] < C('equity_limit')) {
				if (C('bean_lt_recieve_limit') != 0) {
					if (C('bean_lt_recieve_limit') != $amount) {
						responseApiJson(0, "金券少于" . C('equity_limit') . ",接收人只能接受" . C('bean_lt_recieve_limit') . "金豆");
					}
				}
				if (C('bean_lt_recieve_accumulate_limit') != 0) {
					if ($r_info['accumulate_receive_amount'] + $amount > C('bean_lt_recieve_accumulate_limit')) {
						responseApiJson(0, "金券少于" . C('equity_limit') . ",接收人累计接收金豆数不能超过" . C('bean_lt_recieve_accumulate_limit') . "金豆");
					}
				}

			} else {
				if (C('bean_egt_recieve_limit') != 0) {
					if (C('bean_egt_recieve_limit') != $amount) {
						responseApiJson(0, "金券超过" . C('equity_limit') . ",接收人只能接受" . C('bean_egt_recieve_limit') . "金豆");
					}
				}
				if (C('bean_egt_recieve_accumulate_limit') != 0) {
					if ($r_info['accumulate_receive_amount'] + $amount > C('bean_egt_recieve_accumulate_limit')) {
						responseApiJson(0, "金券超过" . C('equity_limit') . ",接收人累计接收金豆数不能超过" . C('bean_egt_recieve_accumulate_limit') . '金豆');
					}
				}

			}
		}
		$avalible_bean = $model_member_extend->getAvailBeanById($member_id);
		if ($avalible_bean < $amount) {
			responseApiJson(0, "金豆不足，不能转赠");
		}
		$donate_data = array();
		$donate_data['member_id'] = $member_id;
		$donate_data['amount'] = $amount;
		$donate_data['member_name'] = $this->member_info['member_name'];
		$donate_data['to_member_id'] = $r_info['member_id'];
		$donate_data['to_member_name'] = $r_info['member_name'];
		$result = $model_member_extend->changeBean('donate', $donate_data);
		if (isset($result['error'])) {
			responseApiJson(0, "转赠失败");
		} else {
			responseApiJson(1, "转赠成功");
		}

	}

	/**
	 * bean_operate_donateOp 银豆转赠
	 * @return [type] [description]
	 */
	public function silver_bean_operate_donateOp() {
		if (!$this->member_info['member_state']) {
			responseApiJson(0, '会员已冻结，不能操作账户');
		}
		if ($this->member_info['is_service'] == 0) {
			responseApiJson(0, "您还不是服务中心");
		}

		$validate = new Validate();
		$validate->validateparam = array(
			array('input' => $_POST['mobile'], 'require' => "true", 'message' => "请输入接收人手机号"),
			array('input' => $_POST['mobile'], 'validator' => "mobile", 'message' => "输入的手机号格式不正确"),
			array('input' => $_POST['amount'], 'require' => "true", 'message' => "请输入银豆转赠数量"),
			array('input' => $_POST['amount'], 'validator' => 'number', 'message' => "输入的转赠数量不是数字"),
			array('input' => $_POST['paypwd'], 'require' => "true", 'message' => "请输入支付密码"),
		);
		$error = $validate->validate();
		if ($error != '') {
			responseApiJson(0, $error);
		}
		if (md5(trim($_POST['paypwd'])) != $this->member_info['member_paypwd']) {
			responseApiJson(0, "支付密码错误");
		}
		$model_member_extend = Model('member_extend');
		$r_info = $model_member_extend->getMemberExtendInfo(array('member_mobile' => $_POST['mobile']), '*', 'union');
		if (empty($r_info)) {
			responseApiJson(0, "接收人不存在");
		}
		$member_id = $this->member_info['member_id'];
		$amount = $_POST['amount'];
		if ($member_id == $r_info['member_id']) {
			responseApiJson(0, "自己不能转赠银豆给自己");
		}
		if ($this->member_info['is_service'] == 1 && $r_info['is_service'] == 1) {
			responseApiJson(0, "转赠人和被转赠人不能同时为服务中心");
		}
		$avalible_bean = $model_member_extend->getAvailSilverBeanById($member_id);
		if ($avalible_bean < $amount) {
			responseApiJson(0, "银豆不足，不能转赠");
		}
		$donate_data = array();
		$donate_data['member_id'] = $member_id;
		$donate_data['amount'] = $amount;
		$donate_data['member_name'] = $this->member_info['member_name'];
		$donate_data['to_member_id'] = $r_info['member_id'];
		$donate_data['to_member_name'] = $r_info['member_name'];
		$result = $model_member_extend->changeSilverBean('donate', $donate_data);
		if (isset($result['error'])) {
			responseApiJson(0, "转赠失败");
		} else {
			responseApiJson(1, "转赠成功");
		}

	}

	/**
	 * bean_operate_consumeOp 金豆消费
	 * @return [type] [description]
	 */
	public function bean_operate_consumeOp() {
		if (!$this->member_info['member_state']) {
			responseApiJson(0, '会员已冻结，不能操作账户');
		}
		$validate = new Validate();
		$validate->validateparam = array(
			array('input' => $_POST['amount'], 'require' => "true", 'message' => "请输入消费金额"),
			array('input' => $_POST['amount'], 'validator' => "number", 'message' => "消费金额不是数字"),
			array('input' => $_POST['paypwd'], 'require' => "true", 'message' => "请输入支付密码"),
		);
		$error = $validate->validate();
		if ($error != '') {
			responseApiJson(0, $error);
		}
		if (md5(trim($_POST['paypwd'])) != $this->member_info['member_paypwd']) {
			responseApiJson(0, "支付密码错误");
		}
		$model_member_extend = Model('member_extend');
		$member_id = $this->member_info['member_id'];
		$amount = $_POST['amount'];

		if ($model_member_extend->getAvailBeanById($member_id) < $amount) {
			responseApiJson(0, "可用金豆不足");
		}

		// 增加封顶复投操作
		// $result = Logic('dividend')->capping($member_id);
		// if (isset($result['error'])) {
		// 	responseApiJson(0, $result['error']);
		// }

		// 判断可消费金豆和消费次数
		$consumed_amount = $this->member_info['total_consume'];
		if (($consumed_amount + $amount) > C('bean_max_consume_amount')) {
			responseApiJson(0, "超过金豆最大可消费额");
		}
		$consumed_num = Model('bean')->getConsumeNumById($member_id);
		if (($consumed_num + 1) > C('bean_max_consume_num')) {
			responseApiJson(0, "超过金豆最大可消费次数");
		}
		//判断是否超过最大金券数量
		$member_gold_coupon = $this->member_info['member_equity'];
		$member_equity = $amount / C('bean_equity_raito');
		//用户可获得的金券数量
		$total_equity = $member_gold_coupon + $member_equity;
		if ($total_equity > C('equity_max_amount')) {
			responseApiJson(0, "已超出个人拥有最大金券数量");
		}

		$consume_data = array();
		$consume_data['member_id'] = $member_id;
		$consume_data['amount'] = $amount;
		$consume_data['member_name'] = $this->member_info['member_name'];
		$result = $model_member_extend->changeBean('consume', $consume_data);
		if (isset($result['error'])) {
			responseApiJson(0, "金豆消费失败");
		} else {
			responseApiJson(1, "金豆消费成功");
		}
	}

	/**
	 * equity_numOp 我的金券数量
	 * @return [type] [description]
	 */
	public function equity_numOp() {
		$equity_num = $this->getMemberEquityNum($this->member_info['member_equity']);
		responseApiJson(1, '', array('amount' => $equity_num . ""));
	}

	/**
	 * member_consumeOp 我的消费额
	 * @return [type] [description]
	 */
	public function member_consumeOp() {
		responseApiJson(1, '', array('amount' => ncPriceFormat(Model('bean')->getBalanceConsumeSum(array('cl_member_id' => $this->member_info['member_id'])))));
	}

	/**
	 * member_float_dividend 浮动分红
	 * @return [type] [description]
	 */
	public function member_float_dividendOp() {
		$model_float_dividend = Model('float_dividend');
		$log_list = array();
		$dividend_list = $model_float_dividend->getFloatDividendLogList(array('flg_member_id' => $this->member_info['member_id']));
		foreach ($dividend_list as $v) {
			$param = array();
			$param['id'] = $v['flg_id'];
			$param['title'] = "分红编号:" . $v['flg_sn'];
			$param['amount'] = $v['flg_amount'] > 0 ? '+' . $v['flg_amount'] : $v['flg_amount'];
			$param['time'] = $v['flg_addtime'] ? date('Y-m-d H:i:s', $v['flg_addtime']) : '';
			$log_list[] = $param;
		}
		responseApiJson(1, '', array('list' => $log_list));

	}



    /**
     * 我的余额
     */
    public function member_balanceOp() {
        $model_member = Model('member');
        $member_balance_amount = $model_member->getAvailBalanceById($this->member_info['member_id']);
        responseApiJson(1, '', array('amount' => floatFormat($member_balance_amount)));
    }

    /**
     * 余额明细
     */
    public function member_balance_listOp(){
        //会员余额记录
        $member_balance_list =Model('member_profit')->getMemberBalanceRecord($this->member_info['member_id']);
        $record_list = array();
        $total_num = Model()->gettotalpage();
        foreach($member_balance_list as $k=>$v){
            $param = array();
           switch ($v['type']){
               case 'admin':
                   $param['title'] = '管理员调整';
                   break;
               case 'withdraw':
                   $param['title'] = '提现';
                   break;
               case 'recharge':
                   $param['title'] = '充值';
                   break;
               case 'consume':
                   $param['title'] = '消费';
                   break;
               case 'bonus';
                   $param['title'] = '分润奖励';
                   break;
               case 'equity';
                   $param['title'] = '股权兑换';
                   break;
               case 'inventory';
                   $param['title'] = '库存解冻';
                   break;
               default:
                   $param['title'] = '';
           }
            $param['id'] = $v['id'];
            $param['change_amount'] = $v['operate_amount']>0?"+".$v['operate_amount']:$v['operate_amount'];
            $param['surplus_amount'] = $v['surplus_amount'];
            $param['time'] = date("Y-m-d H:i:s",$v['add_time']);
            $record_list[] = $param;
        }
        responseApiJson(1, '', array('list' => $record_list,'total_num'=>$total_num));
    }

    /**
     * balance_operate_withdrawOp 余额提现
     * @return [type] [description]
     */
    public function balance_operate_withdrawOp() {
        if (!$this->member_info['member_state']) {
            responseApiJson(0, '会员已冻结，不能操作账户');
        }
        $validate = new Validate();
        $validate->validateparam = array(
            array('input' => $_POST['amount'], 'require' => "true", 'message' => "请输入提现金额"),
            array('input' => $_POST['amount'], 'validator' => 'number', 'message' => "提现金额必须是整数"),
            array('input' => $_POST['bank_id'], 'require' => "true", 'message' => "请选择银行账号"),
            array('input' => $_POST['paypwd'], 'require' => "true", 'message' => "请输入支付密码"),
        );
        $error = $validate->validate();
        if ($error != "") {
            responseApiJson(0, $error);
        }
        if (md5(trim($_POST['paypwd'])) != $this->member_info['member_paypwd']) {
            responseApiJson(0, "支付密码错误");
        }
        $member_id = $this->member_info['member_id'];

        $bank_info = Model('bank')->getMemberBankInfo(array('mb_account_num' => $_POST['bank_id'], 'mb_member_id' => $member_id));
        if (empty($bank_info)) {
            responseApiJson(0, "提现银行账号不存在");
        }
        $amount = floatval($_POST['amount']);
        $model_member_extend = Model('member_extend');
        if ($this->member_info['balance'] < $amount) {
            responseApiJson(0, "可提金额不足");
        }
        if ($amount % $this->member_system_set['withdraw_limit'] != 0) {
            responseApiJson(0, "提现金额须是$this->member_system_set['withdraw_limit']整数倍");
        }
        $withdraw_data = array();
        //扣除手续费
        $withdraw_data['poundage'] = $amount*($this->member_system_set['withdraw_poundage']/100);
        $withdraw_data['amount'] = $amount-$withdraw_data['poundage'];
        $withdraw_data['member_id'] = $member_id;
        $withdraw_data['member_name'] = $this->member_info['member_name'];
        $withdraw_data['mb_id'] = $bank_info['mb_id'];
        $result = $model_member_extend->changeMemberBalance('withdraw', $withdraw_data);
        if (isset($result['error'])) {
            responseApiJson(0, "提现失败");
        } else {
            responseApiJson(1, "提现申请成功,手续费".$this->member_system_set['withdraw_poundage']."%,请等待审核...");
        }
    }

    /**
     * bean_operate_consumeOp 余额消费
     * @return [type] [description]
     */
    public function balance_operate_consumeOp() {
        if (!$this->member_info['member_state']) {
            responseApiJson(0, '会员已冻结，不能操作账户');
        }
        $validate = new Validate();
        $validate->validateparam = array(
            array('input' => $_POST['amount'], 'require' => "true", 'message' => "请输入消费金额"),
            array('input' => $_POST['amount'], 'validator' => "number", 'message' => "消费金额不是数字"),
            array('input' => $_POST['paypwd'], 'require' => "true", 'message' => "请输入支付密码"),
        );
        $error = $validate->validate();
        if ($error != '') {
            responseApiJson(0, $error);
        }
        if (md5(trim($_POST['paypwd'])) != $this->member_info['member_paypwd']) {
            responseApiJson(0, "支付密码错误");
        }
        $model_member_extend = Model('member_extend');
        $member_id = $this->member_info['member_id'];
        $amount = $_POST['amount'];

        if ($this->member_info['balance'] < $amount) {
            responseApiJson(0, "可用余额不足");
        }

        $consume_data = array();
        $consume_data['member_id'] = $member_id;
        $consume_data['amount'] = $amount;
        $consume_data['member_name'] = $this->member_info['member_name'];
        $result = $model_member_extend->changeMemberBalance('consume', $consume_data);
        if (isset($result['error'])) {
            responseApiJson(0, "消费失败");
        } else {
            responseApiJson(1, "消费成功");
        }
    }

    /**
     * member_equityOp 我的股权
     * @return [type] [description]
     */
    public function member_equityOp() {
        $model_equity = Model('equity');
        $log_list = array();
        $equity_list = $model_equity->getEquityLogList(array('elg_member_id' => $this->member_info['member_id']));
        $total_num = $model_equity->gettotalpage();
        foreach ($equity_list as $v) {
            $param = array();
            $param['id'] = $v['elg_id'];
            $param['title'] = replaceEquityType($v['elg_type']) . '[' . $v['elg_sn'] . ']';
            $param['amount'] = $v['elg_amount'] > 0 ? '+' . $v['elg_amount'] : $v['elg_amount'];
            $param['time'] = date('Y-m-d H:i:s', $v['elg_addtime']);
            $log_list[] = $param;
        }
        responseApiJson(1, '', array('amount' => $this->member_info['member_equity'],'total_num' => $total_num));
    }

    /**
     * 股权兑换
     */
    public function equity_exchangeOp(){
        if (!$this->member_info['member_state']) {
            responseApiJson(0, '会员已冻结，不能操作账户');
        }
        $validate = new Validate();
        $validate->validateparam = array(
            array('input' => $_POST['amount'], 'require' => "true", 'message' => "请输入兑换数量"),
            array('input' => $_POST['amount'], 'validator' => "number", 'message' => "兑换数量不是数字"),
            array('input' => $_POST['paypwd'], 'require' => "true", 'message' => "请输入支付密码"),
        );
        $error = $validate->validate();
        if ($error != '') {
            responseApiJson(0, $error);
        }
        if (md5(trim($_POST['paypwd'])) != $this->member_info['member_paypwd']) {
            responseApiJson(0, "支付密码错误");
        }
        $amount = floatval($_POST['amount']);
        if ($this->member_info['is_first_buy_equity'] == 1){
            if ($amount < $this->member_system_set['equity_start_buy_limit']){
                responseApiJson(0, "起购数量".$this->member_system_set['equity_start_buy_limit']."起");
            }
        }
        if ($amount<$this->member_system_set['equity_add_buy_limit']){
            responseApiJson(0, "兑换数量".$this->member_system_set['equity_add_buy_limit']."起");
        }

        $model_member_extend = Model('member_extend');
        $member_id = $this->member_info['member_id'];
        $ratio = explode(":",$this->member_system_set['equity_exchange_ratio']);
        $balance_amount = ($ratio[0]/$ratio[1])*$amount;
        if ($this->member_info['balance'] < $balance_amount) {
            responseApiJson(0, "可用余额不足");
        }

        $exchange_data = array();
        $exchange_data['member_id'] = $member_id;
        $exchange_data['member_name'] = $this->member_info['member_name'];
        $exchange_data['amount'] = $amount;
        $exchange_data['balance_amount'] = $balance_amount;
        $result = $model_member_extend->changeMemberBalance('equity', $exchange_data);
        if (isset($result['error'])) {
            responseApiJson(0, "兑换失败");
        }
        if ($this->member_info['is_first_buy_equity'] == 1){
         $res = Model('member_extend')->changeEquityBuyStatus($member_id);
         if (isset($res['error'])){
             responseApiJson(0, "[兑换失败]");
         }
        }
        responseApiJson(1, "您的兑换申请已提交，请等待审核，谢谢");
    }


    /**
     * 我的分润 + 分润明细
     */
    public function member_bonusOp(){
        //我的分红
        $response_data = array();
        $condition['member_id'] = $this->member_info['member_id'];
        $fields = 'bonus';
        $response_data['my_bonus'] = Model('member')->getMemberBonus($condition,$fields);
        //分红明细
        $condition['member_id'] = $this->member_info['member_id'];
        $member_profit = Model('member_profit')->getBonusList($condition);
        foreach ($member_profit as $k=>$v){
            $data[$k]['add_time'] = date("Y-m-d H:i:s",$v['add_time']);
            $data[$k]['content'] = $v['content'];
            $data[$k]['prize'] = $v['prize'];
            $data[$k]['dr_no'] =$v['dr_no'] ;
        }
        $response_data['bonus_detail'] =$data;
        responseApiJson(1,'',$response_data);
    }
}