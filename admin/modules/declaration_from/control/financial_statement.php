<?php
/**
 * 财务报表模块
 */
class financial_statementControl extends SystemControl {
	private $links = array(
		array('url' => 'act=financial_statement&op=financial_daily_statement', 'lang' => 'financial_daily_statement'),
		array('url' => 'act=financial_statement&op=financial_history_statement', 'lang' => 'financial_history_statement'),
	);
	public function __construct() {
		parent::__construct();
		Language::read('member_center');
	}

	/**
	 * indexOp 财务报表入口
	 * @return [type] [description]
	 */
	public function indexOp() {
		$this->financial_daily_statementOp();
	}

	/**
	 * financial_daily_statementOp 财务日报表
	 * @return [type] [description]
	 */
	public function financial_daily_statementOp() {
		if (isset($_POST['search_time'])) {
			$startTime = strtotime($_POST['search_time']);
			$endTime = $startTime + 86400 - 1;
		} else {
			$startTime = strtotime(date('Y-m-d', TIMESTAMP));
			$endTime = TIMESTAMP;
		}
		// 下一天时间
		$nextTime = $startTime + 86400;
		$nowTime = TIMESTAMP;
		$condition = array('between', array($startTime, $endTime));
//		$condition = array('elt', TIMESTAMP);
        $statement_arr = array();
        $statement_arr['member_regnum'] = Model('member_extend')->getNewMemberNum(['reg_time'=>$condition]);
        $statement_arr['vip_num'] = Model('member_extend')->getNewMemberNum(['member_level'=>1,'reg_time'=>$condition]);
        $statement_arr['shopkeeper_num'] = Model('member_extend')->getNewMemberNum(['member_level'=>2,'reg_time'=>$condition]);
        $statement_arr['partner_num'] = Model('member_extend')->getNewMemberNum(['member_level'=>3,'reg_time'=>$condition]);
        $statement_arr['senior_partner_num'] = Model('member_extend')->getNewMemberNum(['member_level'=>4,'reg_time'=>$condition]);
        $statement_arr['strategic_partner_num'] = Model('member_extend')->getNewMemberNum(['member_level'=>5,'reg_time'=>$condition]);
        $statement_arr['have_withdraw_amount'] = Model('balance')->getBalanceConsumeAmount(array('wl_state'=>2,'wl_paytime'=>$condition),'wl_amount');
        $statement_arr['have_withdraw_fee'] = Model('balance')->getBalanceConsumeAmount(array('wl_state'=>2,'wl_paytime'=>$condition),'wl_fee');
        $statement_arr['direct_prize'] = Model('member_profit')->getPrizeAmount(array('type' =>'direct','add_time'=>$condition),'prize');
        $statement_arr['equity_exchange_amount'] = Model('equity')->getEquityExchangeAmount(array('elg_type' =>'exchange','elg_state'=>2,'elg_addtime'=>$condition),'elg_amount');
        $statement_arr['strategic_partner_bonus'] = (Model('member_profit')->getStrategicPartnerBonusAmount(['cl_addtime'=>$condition])) * 0.05;
        $statement_arr['shop_consume_amount'] = Model('member_profit')->getStrategicPartnerBonusAmount(['cl_addtime'=>$condition]);
        $statement_arr['vip_amount'] = Model('member_extend')->getVIPAmount(['reg_time'=>$condition]);
        $statement_arr['wx_pay_amount'] =  ncPriceFormat(Model('order')->getWxPayAmount(['payment_time'=>$condition]));
        $statement_arr['ali_pay_amount'] = ncPriceFormat(Model('order')->getAliPayAmount(['payment_time'=>$condition]));

//		var_dump($statement_arr);die();
		Tpl::output('statnew_arr', $statement_arr);
		Tpl::output('stat_time', $startTime);
		Tpl::output('top_link', $this->sublink($this->links, 'financial_daily_statement'));
		Tpl::setDirquna('member_center');
		Tpl::showpage('financial_daily_statement');
	}

	/**
	 * financial_history_statementOp 财务历史报表
	 * @return [type] [description]
	 */
	public function financial_history_statementOp() {
		$condition = array();
//		$condition = array('elt', TIMESTAMP);
		$statement_arr = array();
		$statement_arr['member_regnum'] = Model('member_extend')->getNewMemberNum($condition);
        $condition['member_level'] = 1;
        $statement_arr['vip_num'] = Model('member_extend')->getNewMemberNum($condition);
		$condition['member_level'] = 2;
        $statement_arr['shopkeeper_num'] = Model('member_extend')->getNewMemberNum($condition);
        $condition['member_level'] = 3;
        $statement_arr['partner_num'] = Model('member_extend')->getNewMemberNum($condition);
        $condition['member_level'] = 4;
        $statement_arr['senior_partner_num'] = Model('member_extend')->getNewMemberNum($condition);
        $condition['member_level'] = 5;
        $statement_arr['strategic_partner_num'] = Model('member_extend')->getNewMemberNum($condition);
        $statement_arr['have_withdraw_amount'] = Model('balance')->getBalanceConsumeAmount(array('wl_state'=>2),'wl_amount');
        $statement_arr['have_withdraw_fee'] = Model('balance')->getBalanceConsumeAmount(array('wl_state'=>2),'wl_fee');
        $statement_arr['direct_prize'] = Model('member_profit')->getPrizeAmount(array('type' =>'direct'),'prize');
        $statement_arr['equity_exchange_amount'] = Model('equity')->getEquityExchangeAmount(array('elg_type' =>'exchange','elg_state'=>2),'elg_amount');
        $statement_arr['strategic_partner_bonus'] = (Model('member_profit')->getStrategicPartnerBonusAmount(array())) * 0.05;
        $statement_arr['shop_consume_amount'] = Model('member_profit')->getStrategicPartnerBonusAmount(array());
        $statement_arr['vip_amount'] = Model('member_extend')->getVIPAmount(array());
        $statement_arr['wx_pay_amount'] =  ncPriceFormat(Model('order')->getWxPayAmount(array()));
        $statement_arr['ali_pay_amount'] = ncPriceFormat(Model('order')->getAliPayAmount(array()));
//        var_dump($statement_arr);die();
		Tpl::output('statnew_arr', $statement_arr);
		Tpl::output('top_link', $this->sublink($this->links, 'financial_history_statement'));
		Tpl::setDirquna('member_center');
		Tpl::showpage('financial_history_statement');
	}
}