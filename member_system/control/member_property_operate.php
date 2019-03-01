<?php
/**
 * 财务操作
 *
 */
class member_property_operateControl extends MemberOperateControl {
	public function __construct() {
		parent::__construct();
	}

	/**
	 * withdraw_applyOp 申请提现
	 * @return [type] [description]
	 */
	public function withdraw_applyOp() {
		$model_bank = Model('bank');
		$member_id = $this->member_info['member_id'];
		$bank_list = $model_bank->getMemberBankList(array('mb_member_id' => $member_id));
		if (empty($bank_list)) {
			showMessage('请先添加银行卡', 'index.php?act=member_info&op=bank_account_add', '', 'error');
		}
		if (chksubmit()) {
			$validate = new Validate();
			$validate->validateparam = array(
				array('input' => $_POST['withdraw_amount'], 'require' => "true", "message" => "提现金额不能为空"),
				array('input' => $_POST['pay_password'], 'require' => "true", "message" => "支付密码不能为空"),
				array('input' => $_POST['account_id'], 'require' => "true", "message" => "请选择提现银行账号"),
			);
			$error = $validate->validate();
			if ($error != '') {
				showMessage($error, '', '', 'error');
			} else {
				$model_member_extend = Model('member_extend');
				$nameArr = $model_member_extend->getMemberNameArr();
				$withdraw_amount = trim($_POST['withdraw_amount']);
				$account_id = trim($_POST['account_id']);
				$pay_pwd = trim($_POST['pay_password']);
				// 支付密码判断
				if (md5($pay_pwd) != ($this->member_info['member_paypwd'])) {
					showMessage('支付密码不正确', '', '', 'error');
				}
				if (!is_numeric($withdraw_amount)) {
					showMessage('输入提现金额不是数字', '', '', 'error');
				}
				if ($withdraw_amount > 0) {
//					$avalible_bean = $model_member_extend->getAvailBeanById($member_id);
                    $balance = Model('member')-> getMemberBalanceById($member_id);
//					if ($withdraw_amount > $avalible_bean) {
//						showMessage('可用金豆不足', '', '', 'error');
//					}
					if($withdraw_amount > $balance){
                        showMessage('可用余额不足', '', '', 'error');
                    } else{
						if ($withdraw_amount < C('withdraw_charge_limit')) {
							showMessage('提现金额须大于' . C('withdraw_charge_limit') . '元', '', '', 'error');
						}
						if ($withdraw_amount % 100 != 0) {
							showMessage('提现金额不为整', '', '', 'error');
						}
						$withdraw_data = array();
						$withdraw_data['member_id'] = $member_id;
						$withdraw_data['amount'] = $withdraw_amount;
						$withdraw_data['member_name'] = $nameArr[$member_id];
						$withdraw_data['mb_id'] = $account_id;
						$result = $model_member_extend->changeMemberBalance('withdraw', $withdraw_data);
						if (!isset($result['error'])) {
							showMessage('提现提交成功，请等待审核。。。', 'index.php?act=member_property&op=withdraw_list', '', 'succ');
						} else {
							showMessage('提现提交失败', '', '', 'error');
						}
					}
				} else {
					showMessage('提现金额需大于0', '', '', 'error');
				}
			}
		}
		Tpl::output('bank_list', $bank_list);
		$this->_profile_menu('withdraw_apply', 'withdraw_apply');
		Tpl::showpage('apply_withdraw');
	}

	/**
	 * consume_applyOp 申请消费
	 * @return [type] [description]
	 */
	public function consume_applyOp() {
		if (chksubmit()) {
			$validate = new Validate();
			$validate->validateparam = array(
				array('input' => $_POST['consume_amount'], 'require' => "true", "message" => "消费金豆数不能为空"),
				array('input' => $_POST['pay_password'], 'require' => "true", "message" => "支付密码不能为空"),
			);
			$error = $validate->validate();
			if ($error != '') {
				showMessage($error, '', '', 'error');
			} else {
				$member_id = $this->member_info['member_id'];
				$model_member_extend = Model('member_extend');
				$consume_amount = trim($_POST['consume_amount']);
				$pay_pwd = trim($_POST['pay_password']);
				// 支付密码判断
				if (md5($pay_pwd) != ($this->member_info['member_paypwd'])) {
					showMessage('支付密码不正确', '', '', 'error');
				}
				// 判断可用金豆
				if (!is_numeric($consume_amount)) {
					showMessage('输入消费金豆数不是数字', '', '', 'error');

				}
				if ($consume_amount > 0) {
					$avalible_bean = $model_member_extend->getAvailBeanById($member_id);
					if ($avalible_bean < $consume_amount) {
						showMessage('可用金豆不足', '', '', 'error');
					}

					// 增加封顶复投操作
					// $result = Logic('dividend')->capping($member_id);
					// if (isset($result['error'])) {
					// 	showMessage($result['error'], '', '', 'error');
					// }

					// 判断可消费金豆和消费次数
					$bean_consumed_amount = $this->member_info['total_consume'];
					if (($bean_consumed_amount + $consume_amount) > C('bean_max_consume_amount')) {
						showMessage('超过金豆最大可消费额', '', '', 'error');
					}

					$bean_consumed_num = Model('bean')->getConsumeNumById($member_id);
					if (($bean_consumed_num + 1) > C('bean_max_consume_num')) {
						showMessage('超过金豆最大可消费次数', '', '', 'error');
					}
					//判断是否超过最大金券数量
					$member_gold_coupon = $this->member_info['member_equity'];
					$member_equity = floatval($consume_amount / C('bean_equity_raito'));
					//用户可获得的金券数量
					$total_equity = $member_gold_coupon + $member_equity;
					if ($total_equity > C('equity_max_amount')) {
						showMessage('已超出个人拥有最大金券数量', '', '', 'error');
					}

					$consume_data = array();
					$consume_data['member_id'] = $member_id;
					$consume_data['amount'] = $consume_amount;
					$consume_data['member_name'] = $this->member_info['member_name'];
					$result = $model_member_extend->changeBean('consume', $consume_data);
					if (isset($result['error'])) {
						showMessage('消费失败', '', '', 'error');
					} else {
						showMessage('消费成功', 'index.php?act=member_property&op=consume_list', '', 'succ');
					}
				} else {
					showMessage('输入的消费金豆数须大于0', '', '', 'error');
				}
			}

		}
		$this->_profile_menu('consume_apply', 'consume_apply');
		Tpl::showpage('apply_consume');
	}

	/**
	 * bean_donateOp 金豆转赠
	 * @return [type] [description]
	 */
	public function bean_donateOp() {
		if (chksubmit()) {
			$validate = new Validate();
			$validate->validateparam = array(
				array('input' => $_POST['donate_amount'], 'require' => "true", "message" => "转赠金豆数不能为空"),
				array('input' => $_POST['receive_mobile'], 'require' => "true", "message" => "接收人手机号不能为空"),
				array('input' => $_POST['pay_password'], 'require' => "true", "message" => "支付密码不能为空"),
			);
			$error = $validate->validate();
			if ($error != '') {
				showMessage($error, '', '', 'error');
			} else {
				$model_member_extend = Model('member_extend');
				$donate_amount = trim($_POST['donate_amount']);
				$receive_mobile = trim($_POST['receive_mobile']);
				$pay_pwd = trim($_POST['pay_password']);
				if (md5($pay_pwd) != ($this->member_info['member_paypwd'])) {
					showMessage('支付密码不正确', '', '', 'error');
				}
				if (!is_numeric($donate_amount)) {
					showMessage('输入转赠金豆数不是数字', '', '', 'error');
				}
				if ($donate_amount > 0) {
					$info = $model_member_extend->getMemberExtendInfo(array('member_mobile' => $receive_mobile), '*', 'union');
					// 判断接收人服务中心奖, 接收数量 累计数据量
					if (!empty($info)) {
						$member_id = $this->member_info['member_id'];
						if ($member_id == $info['member_id']) {
							showMessage('不能自己转赠金豆给自己', '', '', 'error');
						}
						if ($info['is_service']) {
							showMessage('服务中心不能接受转赠金豆', '', '', 'error');
						}
						if (!$this->member_info['is_service']) {
							if ($info['member_equity'] < C('equity_limit')) {
								if (C('bean_lt_recieve_limit') != 0) {
									if (C('bean_lt_recieve_limit') != $donate_amount) {
										showMessage('金券少于' . C('equity_limit') . ',接收人只能接受' . C('bean_lt_recieve_limit') . '金豆', '', '', 'error');
									}
								}
								if (C('bean_lt_recieve_accumulate_limit') != 0) {
									if ($info['accumulate_receive_amount'] + $donate_amount > C('bean_lt_recieve_accumulate_limit')) {
										showMessage('金券少于' . C('equity_limit') . ',接收人累计接收金豆数不能超过' . C('bean_lt_recieve_accumulate_limit') . '金豆', '', '', 'error');
									}
								}

							} else {
								if (C('bean_egt_recieve_limit') != 0) {
									if (C('bean_egt_recieve_limit') != $donate_amount) {
										showMessage('金券超过' . C('equity_limit') . ',接收人只能接受' . C('bean_egt_recieve_limit') . '金豆', '', '', 'error');
									}
								}
								if (C('bean_egt_recieve_accumulate_limit') != 0) {
									if ($info['accumulate_receive_amount'] + $donate_amount > C('bean_egt_recieve_accumulate_limit')) {
										showMessage('金券超过' . C('equity_limit') . ',接收人累计接收金豆数不能超过' . C('bean_egt_recieve_accumulate_limit') . '金豆', '', '', 'error');
									}
								}

							}
						}

						$avalible_bean = $model_member_extend->getAvailBeanById($this->member_info['member_id']);
						if ($avalible_bean < $donate_amount) {
							showMessage('金豆不足，不能转赠', '', '', 'error');
						}
						$donate_data = array();
						$donate_data['member_id'] = $member_id;
						$donate_data['amount'] = $donate_amount;
						$donate_data['member_name'] = $this->member_info['member_name'];
						$donate_data['to_member_id'] = $info['member_id'];
						$donate_data['to_member_name'] = $info['member_name'];
						$result = $model_member_extend->changeBean('donate', $donate_data);
						if (isset($result['error'])) {
							showMessage('转赠失败', '', '', 'error');
						} else {
							showMessage('转赠成功', 'index.php?act=member_property&op=donate_list', '', 'succ');
						}
					} else {
						showMessage('接收人不存在', '', '', 'error');
					}
				} else {
					showMessage('输入转赠金豆数须大于0', '', '', 'error');
				}

			}
		}
		$this->_profile_menu('bean_donate', 'bean_donate');
		Tpl::showpage('donate_golden_bean');
	}

	/**
	 * silver_bean_donateOp 银豆转赠
	 * @return [type] [description]
	 */
	public function silver_bean_donateOp() {
		$info = $this->member_info;
		$avail_bean = Model('member_extend')->getAvailSilverBeanById($info['member_id']);
		if (!$info['is_service']) {
			showMessage('您还不是服务中心，不能转赠银豆', 'index.php?act=service&op=service_apply', '', 'error');
		}
		if (chksubmit()) {
			$validate = new Validate();
			$validate->validateparam = array(
				array('input' => $_POST['donate_amount'], 'require' => "true", "message" => "转赠银豆数不能为空"),
				array('input' => $_POST['receive_mobile'], 'require' => "true", "message" => "接收人手机号不能为空"),
				array('input' => $_POST['pay_password'], 'require' => "true", "message" => "支付密码不能为空"),
				array('input' => $_POST['donate_amount'], 'validator' => 'double', "message" => "转赠银豆数须为数字"),
				array('input' => $_POST['donate_amount'], 'validator' => "compare", 'operator' => ">=", 'to' => "0.01", 'message' => "输入转赠银豆数须大于0"),
			);
			$error = $validate->validate();
			if ($error != '') {
				showMessage($error, '', '', 'error');
			}
			$model_member_extend = Model('member_extend');
			$donate_amount = trim($_POST['donate_amount']);
			$receive_mobile = trim($_POST['receive_mobile']);
			$pay_pwd = trim($_POST['pay_password']);
			if (md5($pay_pwd) != ($info['member_paypwd'])) {
				showMessage('支付密码不正确', '', '', 'error');
			}
			if (!is_numeric($donate_amount)) {
				showMessage('输入转赠银豆数不是数字', '', '', 'error');
			}
			if ($donate_amount <= 0) {
				showMessage('输入转赠银豆数须大于0', '', '', 'error');
			}
			if ($donate_amount > $avail_bean) {
				showMessage('银豆不足，不能转赠', '', '', 'error');
			}

			if ($receive_mobile == $info['member_mobile']) {
				showMessage('不能自己转赠金豆给自己', '', '', 'error');
			}

			$receive_info = $model_member_extend->getMemberExtendInfo(array('member_mobile' => $receive_mobile), '*', 'union');

			if (empty($receive_info)) {
				showMessage('接收人不存在', '', '', 'error');
			}

			if ($receive_info['is_service']) {
				showMessage('服务中心不能接受转赠银豆', '', '', 'error');
			}

			$donate_data = array();
			$donate_data['member_id'] = $info['member_id'];
			$donate_data['amount'] = $donate_amount;
			$donate_data['member_name'] = $info['member_name'];
			$donate_data['to_member_id'] = $receive_info['member_id'];
			$donate_data['to_member_name'] = $receive_info['member_name'];
			$result = $model_member_extend->changeSilverBean('donate', $donate_data);
			if (isset($result['error'])) {
				showMessage('银豆转赠失败', '', '', 'error');
			} else {
				showMessage('银豆转赠成功', 'index.php?act=member_property&op=donate_list', '', 'succ');
			}
		} else {
			Tpl::output("avail_num", $avail_bean);
			$this->_profile_menu("silver_bean_donate", "silver_bean_donate");
			Tpl::showpage('silver_bean_donate');
		}

	}



    /**
     * 股权兑换
     */
    public function equity_exchangeOp(){
        $member_id = $_SESSION['member_id'];
        $member_info = Model('member_extend')->getMemberExtendInfo(array('me_member_id' => $member_id), "*", 'union');
        $member_system_set = Model()->table('member_system_set')->find();
        if (chksubmit()){
            if (!$member_info['member_state']) {
                showMessage('会员已冻结，不能操作账户', '', '', 'error');
            }
            $validate = new Validate();
            $validate->validateparam = array(
                array('input' => $_POST['exchange_amount'], 'require' => "true", 'message' => "请输入兑换数量"),
                array('input' => $_POST['exchange_amount'], 'validator' => "number", 'message' => "兑换数量不是数字"),
                array('input' => $_POST['pay_password'], 'require' => "true", 'message' => "请输入支付密码"),
            );
            $error = $validate->validate();
            if ($error != '') {
                showMessage($error, '', '', 'error');
            }
            if (md5(trim($_POST['pay_password'])) != $member_info['member_paypwd']) {
                showMessage("支付密码错误", '', '', 'error');
            }
            $amount = floatval($_POST['exchange_amount']);
            if ($member_info['is_first_buy_equity'] == 1){
                if ($amount < $member_system_set['equity_start_buy_limit']){
                    showMessage("起购数量".$member_system_set['equity_start_buy_limit']."起", '', '', 'error');
                }
            }
            if ($amount<$member_system_set['equity_add_buy_limit']){
                showMessage("兑换数量".$member_system_set['equity_add_buy_limit']."起", '', '', 'error');
            }

            $model_member_extend = Model('member_extend');
            $ratio = explode(":",$member_system_set['equity_exchange_ratio']);
            $balance_amount = ($ratio[0]/$ratio[1])*$amount;
            if ($member_info['balance'] < $balance_amount) {
                showMessage('可用余额不足', '', '', 'error');
            }

            $exchange_data = array();
            $exchange_data['member_id'] = $member_id;
            $exchange_data['member_name'] = $member_info['member_name'];
            $exchange_data['amount'] = $amount;
            $exchange_data['balance_amount'] = $balance_amount;
            $result = $model_member_extend->changeMemberBalance('equity', $exchange_data);
            if (isset($result['error'])) {
                showMessage('兑换失败', '', '', 'error');
            }
            if ($member_info['is_first_buy_equity'] == 1){
                $res = Model('member_extend')->changeEquityBuyStatus($member_id);
                if (isset($res['error'])){
                    showMessage('兑换失败', '', '', 'error');
                }
            }
            showMessage('您的兑换申请已提交，请等待审核，谢谢', 'index.php?act=member_property&op=equity_list', '', 'succ');
        }
        $this->_profile_menu('equity_exchange', 'equity_exchange');
        Tpl::showpage('apply_equity_exchange');
    }

	/**
	 * _profile_menu 导航栏
	 * @param  [type] $menu_type [description]
	 * @param  string $menu_key  [description]
	 * @return [type]            [description]
	 */
	private function _profile_menu($menu_type, $menu_key = '') {
		$menu_arr = array();
		switch ($menu_type) {
		case 'withdraw_apply':
			$menu_arr = array(
				1 => array('menu_key' => 'withdraw_apply', 'menu_name' => '申请提现', 'menu_url' => "index.php?act=member_property_operate&op=withdraw_apply"),
			);
			break;
		case 'consume_apply':
			$menu_arr = array(
				1 => array('menu_key' => 'consume_apply', 'menu_name' => '申请消费', 'menu_url' => "index.php?act=member_property_operate&op=consume_apply"),
			);
			break;
		case 'bean_donate':
			$menu_arr = array(
				1 => array('menu_key' => 'bean_donate', 'menu_name' => '转赠金豆', 'menu_url' => "index.php?act=member_property_operate&op=bean_donate"),
			);
			break;
		case 'silver_bean_donate':
			$menu_arr = array(
				1 => array('menu_key' => "silver_bean_donate", 'menu_name' => "转增银豆", 'menu_url' => "index.php?act=member_property_operate&op=silver_bean_donate"),
			);
			break;
        case 'equity_exchange':
            $menu_arr = array(
                1 => array('menu_key' => "equity_exchange", 'menu_name' => "股权兑换", 'menu_url' => "index.php?act=member_property_operate&op=equity_exchange"),
            );
            break;
		}
		Tpl::output('member_menu', $menu_arr);
		Tpl::output('menu_key', $menu_key);
	}

}