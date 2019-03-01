<?php
/**
 * 会员账户
 */
class member_propertyControl extends mobileMemberControl {
    private $member_system_set;
    /**
     * 获取会员系统部分设置
     */
    private function getMemberSystemSet(){
        $this->member_system_set = Model()->table('member_system_set')->find();
    }

	/**
	 * __construct 构造函数
	 */
	public function __construct() {
		parent::__construct();
        $this->getMemberSystemSet();
	}

	/**
	 * memberGoldOp 会员金豆
	 * @return [type] [description]
	 */
	public function memberGoldOp() {
		output_data(array('amount' => ncPriceFormat(Model('member_extend')->getAvailBeanById($this->member_info['member_id']))));
	}

	/**
	 * goldListOp 金豆详情列表
	 * @return [type] [description]
	 */
	public function goldListOp() {
		$log_list = array();
		$bean_list = Model('bean')->getBeanLogList(array('lg_member_id' => $this->member_info['member_id']));
		$total_num = Model('bean')->gettotalpage();

		foreach ($bean_list as $v) {
			$param = array();
			$param['id'] = $v['lg_id'];
			$lg_sn = $v['lg_sn'];

			if ($v['lg_type'] == "donate") {
				$bean_donate_info = Model('bean')->getBeanDonateLogInfo(array('dl_sn' => $v['lg_sn']));
				if ($v['lg_amount'] < 0) {
					$member_id = $bean_donate_info['dl_to_member_id'];
					$receive = "接收人:";
				} else {
					$member_id = $bean_donate_info['dl_from_member_id'];
					$receive = "转赠人:";
				}
				$mobile = Model('member')->getMemberMobileById($member_id);
				$param['receive'] = $receive . $mobile;
				$param['title'] = replaceBeanType($v['lg_type']) . '[' . $lg_sn . ']';
			} elseif ($v['lg_type'] == "rebate" || $v['lg_type'] == "withdraw") {
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
		output_data(array('list' => $log_list, 'total_num' => $total_num));
	}

	/**
	 * goldConsumeOp 金豆消费
	 * @return [type] [description]
	 */
	public function goldConsumeOp() {
		if (!$this->member_info['member_state']) {
			output_error("会员已冻结，不能操作账户");
		}
		$validate = new Validate();
		$validate->validateparam = array(
			array('input' => $_POST['amount'], 'require' => "true", 'message' => "请输入消费金额"),
			array('input' => $_POST['amount'], 'validator' => "double", 'message' => "消费金额不是数字"),
			array('input' => $_POST['passwd'], 'require' => "true", 'message' => "请输入支付密码"),
		);
		$error = $validate->validate();
		if ($error != '') {
			output_error($error);
		}

		if (!$this->checkPaypwd(trim($_POST['passwd']))) {
			output_error("支付密码错误");
		}

		$member_id = $this->member_info['member_id'];
		$amount = $_POST['amount'];

		if (Model('member_extend')->getAvailBeanById($member_id) < $amount) {
			output_error("可用金豆不足");
		}

		// 判断可消费金豆和消费次数
		$consumed_amount = $this->member_info['total_consume'];
		if (($consumed_amount + $amount) > C('bean_max_consume_amount')) {
			output_error("超过金豆最大可消费额");
		}
		$consumed_num = Model('bean')->getConsumeNumById($member_id);
		if (($consumed_num + 1) > C('bean_max_consume_num')) {
			output_error("超过金豆最大可消费次数");
		}
		// 判断是否超过最大金券数量
		$member_gold_coupon = $this->member_info['member_equity'];
		$member_equity = $amount / C('bean_equity_raito');

		// 用户可获得的金券数量
		$total_equity = $member_gold_coupon + $member_equity;
		if ($total_equity > C('equity_max_amount')) {
			output_error("已超出个人拥有最大金券数量");
		}

		$consume_data['member_id'] = $member_id;
		$consume_data['amount'] = $amount;
		$consume_data['member_name'] = $this->member_info['member_name'];
		$result = Model('member_extend')->changeBean('consume', $consume_data);
		if (isset($result['error'])) {
			output_error("金豆消费失败");
		} else {
			output_data(1);
		}
	}

	/**
	 * goldWithdrawOp 金豆提现
	 * @return [type] [description]
	 */
	public function goldWithdrawOp() {
		if (!$this->member_info['member_state']) {
			output_error("会员已冻结，不能操作账户");
		}
		$validate = new Validate();
		$validate->validateparam = array(
			array('input' => $_POST['amount'], 'require' => "true", 'message' => "请输入金豆提现金额"),
			array('input' => $_POST['amount'], 'validator' => "double", 'message' => "金豆提现金额格式错误"),
			array('input' => $_POST['bank'], 'require' => "true", 'message' => "请选择提现银行账号"),
			array('input' => $_POST['passwd'], 'require' => "true", 'message' => "请输入支付密码"),
		);
		$error = $validate->validate();
		if ($error != '') {
			output_error($error);
		}

		if (!$this->checkPaypwd(trim($_POST['passwd']))) {
			output_error("支付密码错误");
		}

		$bank_info = Model('bank')->getMemberBankInfo(array('mb_id' => $_POST['bank'], 'mb_member_id' => $this->member_info['member_id']));
		if (empty($bank_info)) {
			output_error("选择的银行卡信息错误");
		}
		$amount = ncPriceFormat($_POST['amount']);
		if (Model('member_extend')->getAvailBeanById($this->member_info['member_id']) < $amount) {
			output_error("可提金额不足");
		}

		if ($amount % 100 != 0) {
			output_error("提现金额须整百");
		}

		if ($amount < C('withdraw_charge_limit')) {
			output_error("提现金额须大于" . C('withdraw_charge_limit'));
		}

		$withdraw_data['member_id'] = $this->member_info['member_id'];
		$withdraw_data['member_name'] = $this->member_info['member_name'];
		$withdraw_data['amount'] = $amount;
		$withdraw_data['mb_id'] = $bank_info['mb_id'];
		$result = Model('member_extend')->changeBean('withdraw', $withdraw_data);
		if (isset($result['error'])) {
			output_error("提现失败");
		} else {
			output_data(1);
		}
	}

	/**
	 * goldDonateOp 金豆转赠
	 * @return [type] [description]
	 */
	public function goldDonateOp() {
		if (!$this->member_info['member_state']) {
			output_error("会员已冻结，不能操作账户");
		}
		$validate = new Validate();
		$validate->validateparam = array(
			array('input' => $_POST['amount'], 'require' => "true", 'message' => "请输入金豆转赠金额"),
			array('input' => $_POST['amount'], 'validator' => "double", 'message' => "转赠金额格式错误"),
			array('input' => $_POST['to_mobile'], 'require' => "true", 'message' => "请输入接收人手机号"),
			array('input' => $_POST['to_mobile'], 'validator' => "mobile", 'message' => "接收人手机号格式错误"),
			array('input' => $_POST['passwd'], 'require' => "true", 'message' => "请输入支付密码"),
		);
		$error = $validate->validate();
		if ($error != '') {
			output_error($error);
		}
		if (!$this->checkPaypwd(trim($_POST['passwd']))) {
			output_error("支付密码错误");
		}

		$to_info = Model('member_extend')->getMemberExtendInfo(array('member_mobile' => $_POST['to_mobile']), '*', 'union');
		if (empty($to_info)) {
			output_error("接收人不存在");
		}

		$amount = $_POST['amount'];
		if ($this->member_info['member_id'] == $to_info['member_id']) {
			output_error("自己不能转赠金豆给自己");
		}
		if ($to_info['is_service']) {
			output_error("服务中心不能接受转赠金豆");
		}
		if (!$this->member_info['is_service']) {
			if ($to_info['member_equity'] < C('equity_limit')) {
				if (C('bean_lt_recieve_limit') != 0) {
					if (C('bean_lt_recieve_limit') != $amount) {
						output_error("金券少于" . C('equity_limit') . ",接收人只能接受" . C('bean_lt_recieve_limit') . "金豆");
					}
				}
				if (C('bean_lt_recieve_accumulate_limit') != 0) {
					if ($to_info['accumulate_receive_amount'] + $amount > C('bean_lt_recieve_accumulate_limit')) {
						output_error("金券少于" . C('equity_limit') . ",接收人累计接收金豆数不能超过" . C('bean_lt_recieve_accumulate_limit') . "金豆");
					}
				}

			} else {
				if (C('bean_egt_recieve_limit') != 0) {
					if (C('bean_egt_recieve_limit') != $amount) {
						output_error("金券超过" . C('equity_limit') . ",接收人只能接受" . C('bean_egt_recieve_limit') . "金豆");
					}
				}
				if (C('bean_egt_recieve_accumulate_limit') != 0) {
					if ($to_info['accumulate_receive_amount'] + $amount > C('bean_egt_recieve_accumulate_limit')) {
						output_error("金券超过" . C('equity_limit') . ",接收人累计接收金豆数不能超过" . C('bean_egt_recieve_accumulate_limit') . '金豆');
					}
				}

			}
		}

		if (Model('member_extend')->getAvailBeanById($this->member_info['member_id']) < $amount) {
			output_error("金豆不足，不能转赠");
		}

		$donate_data['member_id'] = $this->member_info['member_id'];
		$donate_data['amount'] = $amount;
		$donate_data['member_name'] = $this->member_info['member_name'];
		$donate_data['to_member_id'] = $to_info['member_id'];
		$donate_data['to_member_name'] = $to_info['member_name'];
		$result = Model('member_extend')->changeBean('donate', $donate_data);
		if (isset($result['error'])) {
			output_error("转赠失败");
		} else {
			output_data(1);
		}
	}

	/**
	 * memberSilverOp 会员银豆
	 * @return [type] [description]
	 */
	public function memberSilverOp() {
		output_data(array('amount' => ncPriceFormat(Model('member_extend')->getAvailSilverBeanById($this->member_info['member_id']))));
	}

	/**
	 * silverListOp 银豆列表
	 * @return [type] [description]
	 */
	public function silverListOp() {
		$log_list = array();
		$bean_list = Model('bean')->getSilverBeanLogList(array('slg_member_id' => $this->member_info['member_id']));
		$total_num = Model('bean')->gettotalpage();

		foreach ($bean_list as $v) {
			$param = array();
			$param['id'] = $v['slg_id'];
			$slg_sn = $v['slg_sn'];

			if ($v['slg_type'] == 'donate') {
				$param['title'] = replaceBeanType($v['slg_type']) . '[' . $slg_sn . ']';
				$bean_donate_info = Model('bean')->getSilverBeanLogInfo(array('slg_sn' => $v['slg_sn'], 'slg_amount' => 0 - $v['slg_amount']));

				if (!empty($bean_donate_info)) {
					$member_id = $bean_donate_info['slg_member_id'];

					if ($v['slg_amount'] < 0) {
						$receive = "接收人:";
					} else {
						$receive = "转赠人:";
					}

					$mobile = Model('member')->getMemberMobileById($member_id);
					$param['receive'] = $receive . $mobile;
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
		output_data(array('list' => $log_list, 'total_num' => $total_num));
	}

	/**
	 * silverDonateOp 银豆转赠
	 * @return [type] [description]
	 */
	public function silverDonateOp() {

		if (!$this->member_info['member_state']) {
			output_error("会员已冻结，不能操作账户");
		}

		$validate = new Validate();
		$validate->validateparam = array(
			array('input' => $_POST['amount'], 'require' => "true", 'message' => "请输入银豆转赠金额"),
			array('input' => $_POST['amount'], 'validator' => 'double', 'message' => "转赠金额格式错误"),
			array('input' => $_POST['to_mobile'], 'require' => "true", 'message' => "请输入接收人手机号"),
			array('input' => $_POST['to_mobile'], 'validator' => "mobile", 'message' => "接收人手机号格式错误"),
			array('input' => $_POST['passwd'], 'require' => "true", 'message' => "请输入支付密码"),
		);
		$error = $validate->validate();
		if ($error != '') {
			output_error($error);
		}

		if ($this->member_info['is_service'] == 0) {
			output_error("您还不是服务中心,不能转赠银豆");
		}
		if (!$this->checkPaypwd(trim($_POST['passwd']))) {
			output_error("支付密码错误");
		}

		$to_info = Model('member_extend')->getMemberExtendInfo(array('member_mobile' => $_POST['to_mobile']), '*', 'union');
		if (empty($to_info)) {
			output_error("接收人不存在");
		}

		$amount = ncPriceFormat($_POST['amount']);
		if ($this->member_info['member_id'] == $to_info['member_id']) {
			output_error("自己不能转赠银豆给自己");
		}
		if ($this->member_info['is_service'] == 1 && $to_info['is_service'] == 1) {
			output_error("转赠人和接收人不能同时是服务中心");
		}

		if (Model('member_extend')->getAvailSilverBeanById($this->member_info['member_id']) < $amount) {
			output_error("银豆不足，不能转赠");
		}
		$donate_data['member_id'] = $this->member_info['member_id'];
		$donate_data['amount'] = $amount;
		$donate_data['member_name'] = $this->member_info['member_name'];
		$donate_data['to_member_id'] = $to_info['member_id'];
		$donate_data['to_member_name'] = $to_info['member_name'];
		$result = Model('member_extend')->changeSilverBean('donate', $donate_data);
		if (isset($result['error'])) {
			output_error("转赠失败");
		} else {
			output_data(1);
		}
	}

	/**
	 * memberEquityOp 会员金券
	 * @return [type] [description]
	 */
	public function memberEquityOp() {
		$equity_list = Model('equity')->getEquityLogList(array('elg_member_id' => $this->member_info['member_id']));
		$log_list = array();
		foreach ($equity_list as $v) {
			$param = array();
			$param['id'] = $v['elg_id'];
			$param['title'] = replaceEquityType($v['elg_type']) . '[' . $v['elg_sn'] . ']';
			$param['amount'] = $v['elg_amount'] > 0 ? '+' . $v['elg_amount'] : $v['elg_amount'];
			$param['time'] = date('Y-m-d H:i:s', $v['elg_addtime']);
			$log_list[] = $param;
		}
		output_data(array('amount' => ncPriceFormat($this->member_info['member_equity']), 'list' => $log_list));
	}

	/**
	 * memberEquityNumOp 会员金券数量
	 * @return [type] [description]
	 */
	public function memberEquityNumOp() {
		output_data(array('amount' => convertToEquityNum($this->member_info['member_equity'], C('equity_dividend_min_limit'), C('equity_dividend_max_limit'))));
	}

	/**
	 * memberExpenditureOp 会员消费额
	 * @return [type] [description]
	 */
	public function memberExpenditureOp() {
		output_data(array('amount' => ncPriceFormat(Model('bean')->getBeanConsumeSum(array('cl_member_id' => $this->member_info['member_id'])))));
	}

	/**
	 * memberFloatDividend 浮动分红
	 * @return [type] [description]
	 */
	public function memberFloatDividendOp() {
		$log_list = array();
		$dividend_list = Model('float_dividend')->getFloatDividendLogList(array('flg_member_id' => $this->member_info['member_id']));
		foreach ($dividend_list as $v) {
			$param = array();
			$param['id'] = $v['flg_id'];
			$param['title'] = "分红编号:" . $v['flg_sn'];
			$param['amount'] = $v['flg_amount'] > 0 ? '+' . $v['flg_amount'] : $v['flg_amount'];
			$param['time'] = $v['flg_addtime'] ? date('Y-m-d H:i:s', $v['flg_addtime']) : '';
			$log_list[] = $param;
		}
		output_data(array('list' => $log_list));
	}

	/**
	 * checkPaypasswdOp 检测支付密码
	 * @return [type] [description]
	 */
	public function checkPaypasswdOp() {
		if (empty($this->member_info['member_paypwd'])) {
			output_data(array('paypasswd' => 0));
		} else {
			output_data(array('paypasswd' => 1));
		}
	}

	public function checkServerOp() {
		if ($this->member_info['is_service']) {
			output_data(array('server' => 1));
		} else {
			output_data(array('server' => 0));
		}
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
                    $param['title'] = '库存转入';
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
     * 股权兑换
     */
    public function equity_exchangeOp(){
        if (!$this->member_info['member_state']) {
            responseApiJson(2, '会员已冻结，不能操作账户');
        }
        $validate = new Validate();
        $validate->validateparam = array(
            array('input' => $_POST['amount'], 'require' => "true", 'message' => "请输入兑换数量"),
            array('input' => $_POST['amount'], 'validator' => "number", 'message' => "兑换数量不是数字"),
            array('input' => $_POST['paypwd'], 'require' => "true", 'message' => "请输入支付密码"),
        );
        $error = $validate->validate();
        if ($error != '') {
            responseApiJson(2, $error);
        }
        if (md5(trim($_POST['paypwd'])) != $this->member_info['member_paypwd']) {
            responseApiJson(2, "支付密码错误");
        }
        $amount = floatval($_POST['amount']);
        if ($this->member_info['is_first_buy_equity'] == 1){
            if ($amount < $this->member_system_set['equity_start_buy_limit']){
                responseApiJson(2, "起购数量".$this->member_system_set['equity_start_buy_limit']."起");
            }
        }
        if ($amount<$this->member_system_set['equity_add_buy_limit']){
            responseApiJson(2, "兑换数量".$this->member_system_set['equity_add_buy_limit']."起");
        }

        $model_member_extend = Model('member_extend');
        $member_id = $this->member_info['member_id'];
        $ratio = explode(":",$this->member_system_set['equity_exchange_ratio']);
        $balance_amount = ($ratio[0]/$ratio[1])*$amount;
        if ($this->member_info['balance'] < $balance_amount) {
            responseApiJson(2, "可用余额不足");
        }

        $exchange_data = array();
        $exchange_data['member_id'] = $member_id;
        $exchange_data['member_name'] = $this->member_info['member_name'];
        $exchange_data['amount'] = $amount;
        $exchange_data['balance_amount'] = $balance_amount;
        $result = $model_member_extend->changeMemberBalance('equity', $exchange_data);
        if (isset($result['error'])) {
            responseApiJson(2, "兑换失败");
        }
        if ($this->member_info['is_first_buy_equity'] == 1){
            $res = Model('member_extend')->changeEquityBuyStatus($member_id);
            if (isset($res['error'])){
                responseApiJson(2, "[兑换失败]");
            }
        }
        responseApiJson(1, "您的兑换申请已提交，请等待审核，谢谢");
    }


}