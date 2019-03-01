<?php
/**
 * 会员财产中心
 *
 */
defined('In33hao') or exit('Access Invild!');

class member_propertyControl extends BaseMemberControl
{
    public function __construct()
    {
        parent::__construct();
    }

    public function indexOp()
    {
        $this->bonus_listOp();
    }

    /**
     * bonusList 奖励明细
     * @return [type] [description]
     */
    public function bonus_listOp()
    {
        $model_member_profit = Model('member_profit');
        $condition = array();
        $condition['member_id'] = $_SESSION['member_id'];
        $type = 0;
        // 搜索
        if (chksubmit()) {
            if (!empty($_POST['query_start_date'])) {
                $query_start_date = $_POST['query_start_date'];
                $start_time = strtotime($query_start_date);
                Tpl::output('query_start_date', $query_start_date);
            }else{
                $start_time = 0;
            }
            if (!empty($_POST['query_end_date'])) {
                $query_end_date = $_POST['query_end_date'];
                $end_time = strtotime($query_end_date)+86400;
                Tpl::output('query_end_date', $query_end_date);
            }else{
            $end_time = time();
        }
            $condition['add_time'] = array('between', array($start_time,$end_time));
            if (!empty($_POST['dr_no'])){
                $condition['dr_no'] = $_POST['dr_no'];
            }
            if (!empty($_POST['type'])){
                $condition['type'] = str_replace(array("1","2","3"),array("diff","eql","direct"),$_POST['type']);
                $type = $_POST['type'];
            }
        }
        $list = $model_member_profit->getBonusList($condition);
        Tpl::output('bonus_type', $type);
        Tpl::output('list', $list);
        $this->_profile_menu('bonus_list', 'bonus_list');
        Tpl::showpage('bonus_log.list');
    }

    /**
     * consumeList 消费列表
     * @return [type] [description]
     */
    public function consume_listOp()
    {
        $model_bean = Model('bean');
        $condition = array();
        $condition['cl_member_id'] = $_SESSION['member_id'];
        $fields = 'cl_sn,cl_addtime,cl_amount,elg_amount';
        if (chksubmit()) {
            if (!empty($_POST['query_start_date'])) {
                $query_start_date = $_POST['query_start_date'];
                $start_time = strtotime($query_start_date);
                $condition['cl_addtime'] = array('egt', $start_time);
                Tpl::output('query_start_date', $query_start_date);
            }
            if (!empty($_POST['query_end_date'])) {
                $query_end_date = $_POST['query_end_date'];
                $end_time = strtotime($query_end_date);
                $condition['cl_addtime'] = array('elt', $end_time);
                Tpl::output('query_end_date', $query_end_date);
            }
        }
        $list = $model_bean->getBeanConsumeEquityList($condition, $fields);
        Tpl::output('list', $list);
        $this->_profile_menu('consume_list', 'consume_list');
        Tpl::showpage('consume_log.list');
    }

    /**
     * rechargeListOp 充值管理
     * @return [type] [description]
     */
    public function recharge_listOp()
    {
        $model_bean = Model('bean');
        $condition = array();
        $condition['rl_member_id'] = $_SESSION['member_id'];
        $condition['rl_recharge_type'] = 'alipay';

        if (chksubmit()) {
            if (!empty($_POST['query_start_date'])) {
                $query_start_date = $_POST['query_start_date'];
                $start_time = strtotime($query_start_date);
                $condition['rl_paytime'] = array('egt', $start_time);
                Tpl::output('query_start_date', $query_start_date);
            }
            if (!empty($_POST['query_end_date'])) {
                $query_end_date = $_POST['query_end_date'];
                $end_time = strtotime($query_end_date);
                $condition['rl_paytime'] = array('elt', $end_time);
                Tpl::output('query_end_date', $query_end_date);
            }
        }
        $list = $model_bean->getBeanRechargeLogList($condition);

        Tpl::output('list', $list);
        $this->_profile_menu('recharge_list', 'recharge_list');
        Tpl::showpage('recharge_log.list');
    }

    /**
     * withdrawList 提现管理
     * @return [type] [description]
     */
    public function withdraw_listOp()
    {
        $model_balance = Model('balance');
        $condition = array();
        $condition['wl_member_id'] = $_SESSION['member_id'];
        $wl_state = 0;
        if (chksubmit()) {
            if (!empty($_POST['query_start_date'])) {
                $query_start_date = $_POST['query_start_date'];
                $start_time = strtotime($query_start_date);
                Tpl::output('query_start_date', $query_start_date);
            }else{
                $start_time = 0;
            }
            if (!empty($_POST['query_end_date'])) {
                $query_end_date = $_POST['query_end_date'];
                $end_time = strtotime($query_end_date)+86400;
                Tpl::output('query_end_date', $query_end_date);
            }else{
                $end_time = time();
            }
            $condition['wl_addtime'] = array('between', array($start_time,$end_time));
            if ($_POST['wl_state']) {
                $wl_state = $_POST['wl_state'];
                $condition['wl_state'] = $wl_state-1;
            }
            if (!empty($_POST['wl_sn'])){
                $condition['wl_sn'] = $_POST['wl_sn'];
            }
        }
        $list = $model_balance->getBalanceWithdraw($condition);
        Tpl::output('list', $list);
        Tpl::output('wl_state', $wl_state);
        $this->_profile_menu('withdraw_list', 'withdraw_list');
        Tpl::showpage('withdraw_log.list');
    }

    public function equity_listOp(){
        $model_equity = Model('equity');
        $condition = array();
        $condition['elg_member_id'] = $_SESSION['member_id'];
        $state = 0;
        // 搜索
        if (chksubmit()) {
            if (!empty($_POST['query_start_date'])) {
                $query_start_date = $_POST['query_start_date'];
                $start_time = strtotime($query_start_date);
                Tpl::output('query_start_date', $query_start_date);
            }else{
                $start_time = 0;
            }
            if (!empty($_POST['query_end_date'])) {
                $query_end_date = $_POST['query_end_date'];
                $end_time = strtotime($query_end_date)+86400;
                Tpl::output('query_end_date', $query_end_date);
            }else{
                $end_time = time();
            }
            $condition['elg_addtime'] = array('between', array($start_time,$end_time));
            if (!empty($_POST['elg_sn'])){
                $condition['elg_sn'] = $_POST['elg_sn'];
            }
            if (!empty($_POST['elg_state'])){
                $condition['elg_state'] = $_POST['elg_state']-1;
                $state = $_POST['elg_state'];
            }
        }
        $list = $model_equity->getEquityLogList($condition);
        Tpl::output('elg_state', $state);
        Tpl::output('list', $list);
        $this->_profile_menu('equity_list', 'equity_list');
        Tpl::showpage('equity_log.list');
    }

    /**
     * donateList 转赠列表
     * @return [type] [description]
     */
    public function donate_listOp()
    {
        $model_bean = Model('bean');
        $condition = array();
        $condition['dl_from_member_id'] = $_SESSION['member_id'];
        if (chksubmit()) {
            if (!empty($_POST['query_start_date'])) {
                $query_start_date = $_POST['query_start_date'];
                $start_time = strtotime($query_start_date);
                $condition['dl_addtime'] = array('egt', $start_time);
                Tpl::output('query_start_date', $query_start_date);
            }
            if (!empty($_POST['query_end_date'])) {
                $query_end_date = $_POST['query_end_date'];
                $end_time = strtotime($query_end_date);
                $condition['dl_addtime'] = array('elt', $end_time);
                Tpl::output('query_end_date', $query_end_date);
            }
        }
        $list = $model_bean->getBeanDonateLogList($condition);
        $arr = array();
        $member_list = Model('member')->getMemberList();
        foreach ($member_list as $key => $v) {
            $arr[$v['member_id']] = $v['member_mobile'];
        }
        Tpl::output('members', $arr);
        Tpl::output('list', $list);
        $this->_profile_menu('donate_list', 'donate_list');
        Tpl::showpage('donate_log.list');
    }

    /**
     * finaneList 财务对账
     * @return [type] [description]
     */
    public function finane_listOp()
    {

    }

    /**
     * exchangeListOp 兑换管理
     * @return [type] [description]
     */
    public function exchange_listOp()
    {

        Tpl::showpage('exchange_log.list');
    }

    /**
     * recharge_applyOp 申请充值
     * @return [type] [description]
     */
    public function recharge_applyOp()
    {
        if (chksubmit()) {
            $model_member_extend = Model('member_extend');
            $recharge_amount = trim($_POST['recharge_amount']);
            $recharge_type = trim($_POST['recharge_type']);
            if (!is_numeric($recharge_amount)) {
                showMessage('充值金额输入不是数字', '', '', 'error');
            }
            if ($recharge_amount > 0) {
                $recharge_data = array();
                $recharge_data['member_id'] = $this->member_info['member_id'];
                $recharge_data['amount'] = $recharge_amount;
                $recharge_data['recharge_type'] = 'alipay';
                $recharge_data['account_type'] = 'bean';
                $result = $model_member_extend->recharge_apply($recharge_data);
                if (isset($result['error'])) {
                    showMessage('充值提交失败', '', '', 'error');
                } else {
                    showMessage('充值提交成功', 'index.php?act=member_property&op=recharge_list', '', 'succ');
                }
            } else {
                showMessage('充值金额需大于0', '', '', 'error');
            }

        }
        $this->_profile_menu('recharge_apply', 'recharge_apply');
        Tpl::showpage('apply_recharge');
    }

    /**
     * float_dividend_listOp 浮动分红
     * @return [type] [description]
     */
    public function float_dividend_listOp()
    {
        $model_float_dividend = Model('float_dividend');
        $dividend_list = $model_float_dividend->getFloatDividendLogList(array('flg_member_id' => $this->member_info['member_id']));
        $this->_profile_menu('float_dividend_list', 'float_dividend_list');
        Tpl::output('list', $dividend_list);
        Tpl::showpage('float_dividend_log.list');
    }

    /**
     * silver_bean_listOp 银豆记录
     * @return [type] [description]
     */
    public function silver_bean_listOp()
    {
        $model_equity = Model('equity');
        $condition = array();
        $condition['elg_member_id'] = $_SESSION['member_id'];
        $state = 0;
        // 搜索
        if (chksubmit()) {
            if (!empty($_POST['query_start_date'])) {
                $query_start_date = $_POST['query_start_date'];
                $start_time = strtotime($query_start_date);
                Tpl::output('query_start_date', $query_start_date);
            }else{
                $start_time = 0;
            }
            if (!empty($_POST['query_end_date'])) {
                $query_end_date = $_POST['query_end_date'];
                $end_time = strtotime($query_end_date)+86400;
                Tpl::output('query_end_date', $query_end_date);
            }else{
                $end_time = time();
            }
            $condition['elg_addtime'] = array('between', array($start_time,$end_time));
            if (!empty($_POST['elg_sn'])){
                $condition['elg_sn'] = $_POST['elg_sn'];
            }
            if (!empty($_POST['elg_state'])){
                $condition['elg_state'] = $_POST['elg_state']-1;
                $state = $_POST['elg_state'];
            }
        }
        $list = $model_equity->getEquityLogList($condition);
        Tpl::output('elg_state', $state);
        Tpl::output('list', $list);
        $this->_profile_menu('equity_list', 'equity_list');
        Tpl::showpage('equity_log.list');
    }

    /**
     * _profile_menu 导航栏
     * @param  [type] $menu_type [description]
     * @param  string $menu_key  [description]
     * @return [type]            [description]
     */
    private function _profile_menu($menu_type, $menu_key = '')
    {
        $menu_arr = array();
        switch ($menu_type) {
            case 'bonus_list':
                $menu_arr = array(
                    1 => array('menu_key' => 'bonus_list', 'menu_name' => '分红明细', 'menu_url' => 'index.php?act=member_property&op=bonus_list'),
                );
                break;
            case 'consume_list':
                $menu_arr = array(
                    1 => array('menu_key' => 'consume_list', 'menu_name' => '消费记录', 'menu_url' => 'index.php?act=member_property&op=consume_list'),
                );
                break;
            case 'recharge_list':
                $menu_arr = array(
                    1 => array('menu_key' => 'recharge_list', 'menu_name' => '充值记录', 'menu_url' => 'index.php?act=member_property&op=recharge_list'),

                );
                break;

            case 'withdraw_list':
                $menu_arr = array(
                    1 => array('menu_key' => 'withdraw_list', 'menu_name' => "提现管理", 'menu_url' => 'index.php?act=member_property&op=withdraw_list'),
                );
                break;
            case 'donate_list':
                $menu_arr = array(
                    1 => array('menu_key' => 'donate_list', 'menu_name' => "转赠列表", 'menu_url' => 'index.php?act=member_property&op=donate_list'),
                );
                break;
            case 'recharge_apply':
                $menu_arr = array(
                    1 => array('menu_key' => 'recharge_apply', 'menu_name' => "申请充值", 'menu_url' => 'index.php?act=member_property&op=recharge_apply'),
                );
                break;
            case 'float_dividend_list':
                $menu_arr = array(
                    1 => array('menu_key' => 'float_dividend_list', 'menu_name' => "浮动分红", 'menu_url' => 'index.php?act=member_property&op=float_dividend_list'),
                );
                break;
            case 'silver_bean_list':
                $menu_arr = array(
                    array('menu_key' => "equity_list", 'menu_name' => "股权管理", 'menu_url' => "index.php?act=member_property&op=equity_list"),
                );
                break;
            case 'equity_list':
                $menu_arr = array(
                    array('menu_key' => "equity_list", 'menu_name' => "股权管理", 'menu_url' => "index.php?act=member_property&op=equity_list"),
                );
                break;
        }
        Tpl::output('member_menu', $menu_arr);
        Tpl::output('menu_key', $menu_key);
    }
}