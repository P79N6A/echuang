<?php
/**
 * 金豆模型
 *
 */
defined('In33hao') or exit('Access Invild!');

class beanModel extends Model {
	public function __construct() {
		parent::__construct();
	}

	/**
	 * getBeanLogList 获取金豆日志列表
	 * @param  array  $condition [description]
	 * @param  string $field     [description]
	 * @param  [type] $page      [description]
	 * @param  string $order     [description]
	 * @param  string $limit     [description]
	 * @return [type]            [description]
	 */
	public function getBeanLogList($condition = array(), $field = '*', $page = null, $order = 'lg_addtime desc', $limit = '') {
		return $this->table('bean_log')->field($field)->where($condition)->page($page)->order($order)->limit($limit)->select();
	}

	/**
	 * getAllBeanLogList 获取所有金豆日志信息
	 * @param  string $fields [description]
	 * @param  string $order  [description]
	 * @return [type]         [description]
	 */
	public function getAllBeanLogList($fields = '*', $order = 'lg_addtime desc') {
		$sql = "SELECT " . $fields . " FROM " . DBPRE . "bean_log ORDER BY " . $order;
		return Model()->query($sql);
	}

	/**
	 * getBeanLogInfo 获取金豆日志信息
	 * @param  [type]  $condition [description]
	 * @param  string  $field     [description]
	 * @param  boolean $master    [description]
	 * @return [type]             [description]
	 */
	public function getBeanLogInfo($condition, $field = '*', $master = false) {
		return $this->table('bean_log')->field($field)->where($condition)->master($master)->find();
	}

	/**
	 * addBeanLog 添加金豆日志
	 * @param [type] $data [description]
	 */
	public function addBeanLog($data) {
		$insert = $this->table('bean_log')->insert($data);
		if (!$insert) {
			throw new Exception('添加金豆日志失败');
		} else {
			return $insert;
		}
	}

	/**
	 * editBeanLog 更新金豆日志
	 * @param  [type] $condition [description]
	 * @param  [type] $data      [description]
	 * @return [type]            [description]
	 */
	public function editBeanLog($condition, $data) {
		$update = $this->table('bean_log')->where($condition)->update($data);
		if (!$update) {
			throw new Exception('更新金豆日志失败');
		}
	}

	/**
	 * getBeanConsumeLogList 获取金豆消费记录
	 * @param  array  $condition [description]
	 * @param  string $field     [description]
	 * @param  [type] $page      [description]
	 * @param  string $order     [description]
	 * @param  string $limit     [description]
	 * @return [type]            [description]
	 */
	public function getBeanConsumeLogList($condition = array(), $field = '*', $page = null, $order = 'cl_addtime desc', $limit = '') {
		return $this->table('bean_consume_log')->field($field)->where($condition)->page($page)->order($order)->limit($limit)->select();
	}

	/**
	 * getBeanConsumeLogInfo 获取金豆消费信息
	 * @param  [type]  $condition [description]
	 * @param  string  $field     [description]
	 * @param  boolean $master    [description]
	 * @return [type]             [description]
	 */
	public function getBeanConsumeLogInfo($condition, $field = '*', $master = false) {
		return $this->table('bean_consume_log')->field($field)->where($condition)->master($master)->find();
	}

	/**
	 * addBeanConsumeLog 添加金豆消费日志
	 * @param [type] $data [description]
	 */
	public function addBeanConsumeLog($data) {
		$insert = $this->table('bean_consume_log')->insert($data);
		if (!$insert) {
			throw new Exception('添加金豆消费日志失败');
		} else {
			return $insert;
		}
	}

	/**
	 * editBeanConsumeLog 更新金豆消费日志
	 * @param  [type] $condition [description]
	 * @param  [type] $data      [description]
	 * @return [type]            [description]
	 */
	public function editBeanConsumeLog($condition, $data) {
		$update = $this->table('bean_consume_log')->where($condition)->update($data);
		if (!$update) {
			throw new Exception('更新金豆消费日志失败');
		}
	}

    /**
     * getBeanConsumeSum 获取金豆消费总额
     * @param  [type] $condition [description]
     * @return [type]            [description]
     */
    public function getBeanConsumeSum($condition) {
        return $this->table('bean_consume_log')->where($condition)->sum('cl_amount');
    }


	/**
	 * getBeanConsumeSum 获取余额消费总额
	 * @param  [type] $condition [description]
	 * @return [type]            [description]
	 */
	public function getBalanceConsumeSum($condition) {
		return $this->table('balance_consume_record')->where($condition)->sum('cl_amount');
	}

	/**
	 * setBeanConsumeFieldValue 设置金豆消费字段值
	 * @param [type] $condition [description]
	 * @param [type] $field     [description]
	 * @param [type] $value     [description]
	 */
	public function setBeanConsumeFieldValue($condition, $field, $value) {
		return $this->table('bean_consume_log')->where($condition)->set_field($field, $value);
	}
	/**
	 * getBeanRebateLogList 获取金豆返利记录
	 * @param  array  $condition [description]
	 * @param  string $field     [description]
	 * @param  [type] $page      [description]
	 * @param  string $order     [description]
	 * @param  string $limit     [description]
	 * @return [type]            [description]
	 */
	public function getBeanRebateLogList($condition = array(), $field = '*', $page = null, $order = 'rl_addtime desc', $limit = '', $group = '') {
		return $this->table('bean_rebate_log')->field($field)->where($condition)->page($page)->order($order)->limit($limit)->select();
	}

	/**
	 * getBeanRebateLogListGroup 分组获取数据
	 * @param  [type] $group     [description]
	 * @param  array  $condition [description]
	 * @param  string $field     [description]
	 * @return [type]            [description]
	 */
	public function getBeanRebateLogGroupList($group, $condition = array(), $field = '*') {
		return $this->table('bean_rebate_log')->field($field)->where($condition)->group($group)->select();
	}

	/**
	 * getBeanRebateSumAmount 获取返利总金额
	 * @param  array  $condition [description]
	 * @return [type]            [description]
	 */
	public function getBeanRebateSumAmount($condition = array()) {
		return $this->table('bean_rebate_log')->where($condition)->sum('rl_amount');
	}

	/**
	 * getBeanRebateLogInfo 获取金豆返利信息
	 * @param  [type]  $condition [description]
	 * @param  string  $field     [description]
	 * @param  boolean $master    [description]
	 * @return [type]             [description]
	 */
	public function getBeanRebateLogInfo($condition, $field = '*', $master = false) {
		return $this->table('bean_rebate_log')->field($field)->where($condition)->master($master)->find();
	}

	/**
	 * addBeanRebateLog 添加金豆返利日志
	 * @param [type] $data [description]
	 */
	public function addBeanRebateLog($data) {
		$insert = $this->table('bean_rebate_log')->insert($data);
		if (!$insert) {
			throw new Exception('添加金豆返利日志失败');
		} else {
			return $insert;
		}
	}

	/**
	 * editBeanRebateLog 更新金豆返利日志
	 * @param  [type] $condition [description]
	 * @param  [type] $data      [description]
	 * @return [type]            [description]
	 */
	public function editBeanRebateLog($condition, $data) {
		$update = $this->table('bean_rebate_log')->where($condition)->update($data);
		if (!$update) {
			throw new Exception('更新金豆返利日志失败');
		}
	}

	/**
	 * getBeanRechargeLogList 获取金豆充值列表
	 * @param  array  $condition [description]
	 * @param  string $field     [description]
	 * @param  [type] $page      [description]
	 * @param  string $order     [description]
	 * @param  string $limit     [description]
	 * @return [type]            [description]
	 */
	public function getBeanRechargeLogList($condition = array(), $field = '*', $page = null, $order = 'rl_addtime desc', $limit = '') {
		return $this->table('bean_recharge_log')->field($field)->where($condition)->page($page)->order($order)->limit($limit)->select();
	}

	public function getAllBeanRechargeLogList($fields = '*', $order = 'rl_addtime desc') {
		$sql = "SELECT " . $fields . " FROM " . DBPRE . "bean_recharge_log ORDER BY " . $order;
		return Model()->query($sql);
	}

	/**
	 * getBeanRechargeLogInfo 获取金豆充值信息
	 * @param  [type]  $condition [description]
	 * @param  string  $field     [description]
	 * @param  boolean $master    [description]
	 * @return [type]             [description]
	 */
	public function getBeanRechargeLogInfo($condition, $field = '*', $master = false) {
		return $this->table('bean_recharge_log')->field($field)->where($condition)->master($master)->find();
	}

	/**
	 * addBeanRechargeLog 添加金豆充值日志
	 * @param [type] $data [description]
	 */
	public function addBeanRechargeLog($data) {
		$insert = $this->table('bean_recharge_log')->insert($data);
		if (!$insert) {
			throw new Exception('添加金豆充值日志失败');
		} else {
			return $insert;
		}
	}

	/**
	 * editBeanRechargeLog 更新金豆充值日志
	 * @param  [type] $condition [description]
	 * @param  [type] $data      [description]
	 * @return [type]            [description]
	 */
	public function editBeanRechargeLog($condition, $data) {
		$update = $this->table('bean_recharge_log')->where($condition)->update($data);
		if (!$update) {
			throw new Exception('更新金豆充值日志失败');
		}
	}

	/**
	 * getBeanWithdrawPageLogList 获取金豆提现列表
	 * @param  array  $condition [description]
	 * @param  string $field     [description]
	 * @param  [type] $page      [description]
	 * @param  string $order     [description]
	 * @param  string $limit     [description]
	 * @return [type]            [description]
	 */
	public function getBeanWithdrawPageLogList($condition = array(), $field = '*', $page = null, $order = 'wl_addtime desc', $limit = '') {
		return $this->table('bean_withdraw_log')->field($field)->where($condition)->page($page)->order($order)->limit($limit)->select();
	}

	/**
	 * getBeanWithdrawLogList获取金豆提现列表
	 * @param  [type] $condition [description]
	 * @param  string $fields    [description]
	 * @param  string $order     [description]
	 * @return [type]            [description]
	 */
	public function getBeanWithdrawLogList($condition, $fields = '*', $order = 'wl_addtime desc') {
		return $this->table('bean_withdraw_log')->field($fields)->where($condition)->order($order)->limit(false)->select();
	}
	/**
	 * getAllBeanWithdrawLogList 获取金豆所有提现记录
	 * @param  string $fields [description]
	 * @param  string $order  [description]
	 * @return [type]         [description]
	 */
	public function getAllBeanWithdrawLogList($fields = '*', $order = 'wl_addtime desc') {
		$sql = "SELECT " . $fields . " FROM " . DBPRE . "bean_withdraw_log ORDER BY " . $order;
		return Model()->query($sql);
	}

	/**
	 * getBeanWithdrawLogInfo 获取金豆提现信息
	 * @param  [type]  $condition [description]
	 * @param  string  $field     [description]
	 * @param  boolean $master    [description]
	 * @return [type]             [description]
	 */
	public function getBeanWithdrawLogInfo($condition, $field = '*', $master = false) {
		return $this->table('bean_withdraw_log')->field($field)->where($condition)->master($master)->find();
	}

	/**
	 * addBeanWithdrawLog 添加金豆提现日志
	 * @param [type] $data [description]
	 */
	public function addBeanWithdrawLog($data) {
		$insert = $this->table('bean_withdraw_log')->insert($data);
		if (!$insert) {
			throw new Exception('添加金豆提现日志失败');
		} else {
			return $insert;
		}
	}

	/**
	 * editBeanWithdrawLog 更新金豆提现日志
	 * @param  [type] $condition [description]
	 * @param  [type] $data      [description]
	 * @return [type]            [description]
	 */
	public function editBeanWithdrawLog($condition, $data) {
		// foreach ($condition as $key => $value) {
		// 	if (is_array($value) && $key == 'wl_sn') {
		// 		foreach ($value as $k => $v) {
		// 			$condition_edit[$key] = $v;
		// 			$update = $this->table('bean_withdraw_log')->where($condition_edit)->update($data);
		// 		}
		// 	}
		// }
		// if (!$update) {
		// 	throw new Exception('更新金豆提现日志失败');
		// } else {
		// 	return $update;
		// }
		$update = $this->table('bean_withdraw_log')->where($condition)->update($data);
		if (!$update) {
			throw new Exception('更新金豆兑换日志失败');
		}
	}

	/**
	 * getBeanWithdrawLogList 获取金豆转赠列表
	 * @param  array  $condition [description]
	 * @param  string $field     [description]
	 * @param  [type] $page      [description]
	 * @param  string $order     [description]
	 * @param  string $limit     [description]
	 * @return [type]            [description]
	 */
	public function getBeanDonateLogList($condition = array(), $field = '*', $page = null, $order = 'dl_addtime desc', $limit = '') {
		return $this->table('bean_donate_log')->field($field)->where($condition)->page($page)->order($order)->limit($limit)->select();
	}

	/**
	 * getBeanDonateLogInfo 获取金豆转赠信息
	 * @param  [type]  $condition [description]
	 * @param  string  $field     [description]
	 * @param  boolean $master    [description]
	 * @return [type]             [description]
	 */
	public function getBeanDonateLogInfo($condition, $field = '*', $master = false) {
		return $this->table('bean_donate_log')->field($field)->where($condition)->master($master)->find();
	}

	/**
	 * addBeanDonateLog 添加金豆转赠日志
	 * @param [type] $data [description]
	 */
	public function addBeanDonateLog($data) {
		$insert = $this->table('bean_donate_log')->insert($data);
		if (!$insert) {
			throw new Exception('添加金豆转赠日志失败');
		} else {
			return $insert;
		}
	}

	/**
	 * editBeanDonateLog 更新金豆转赠日志
	 * @param  [type] $condition [description]
	 * @param  [type] $data      [description]
	 * @return [type]            [description]
	 */
	public function editBeanDonateLog($condition, $data) {
		$update = $this->table('bean_donate_log')->where($condition)->update($data);
		if (!$update) {
			throw new Exception('更新金豆转赠日志失败');
		}
	}

	/**
	 * getBeanExchangeLogList 获取金豆兑换列表
	 * @param  array  $condition [description]
	 * @param  string $field     [description]
	 * @param  [type] $page      [description]
	 * @param  string $order     [description]
	 * @param  string $limit     [description]
	 * @return [type]            [description]
	 */
	public function getBeanExchangeLogList($condition = array(), $field = '*', $page = null, $order = 'lg_admin desc', $limit = '') {
		return $this->table('bean_exchange_log')->field($field)->where($condition)->page($page)->order($order)->limit($limit)->select();
	}

	/**
	 * getBeanExchangeLogInfo 获取金豆兑换信息
	 * @param  [type]  $condition [description]
	 * @param  string  $field     [description]
	 * @param  boolean $master    [description]
	 * @return [type]             [description]
	 */
	public function getBeanExchangeLogInfo($condition, $field = '*', $master = false) {
		return $this->table('bean_exchange_log')->field($field)->where($condition)->master($master)->find();
	}

	/**
	 * addBeanExchangeLog 添加金豆转赠日志
	 * @param [type] $data [description]
	 */
	public function addBeanExchangeLog($data) {
		$insert = $this->table('bean_exchange_log')->insert($data);
		if (!$insert) {
			throw new Exception('添加金豆兑换日志失败');
		} else {
			return $insert;
		}
	}

	/**
	 * editBeanExchangeLog 更新金豆转赠日志
	 * @param  [type] $condition [description]
	 * @param  [type] $data      [description]
	 * @return [type]            [description]
	 */
	public function editBeanExchangeLog($condition, $data) {
		$update = $this->table('bean_exchange_log')->where($condition)->update($data);
		if (!$update) {
			throw new Exception('更新金豆兑换日志失败');
		}
	}

	/**
	 * getBeanAdminLogList 获取金豆管理员列表
	 * @param  array  $condition [description]
	 * @param  string $field     [description]
	 * @param  [type] $page      [description]
	 * @param  string $order     [description]
	 * @param  string $limit     [description]
	 * @return [type]            [description]
	 */
	public function getBeanAdminLogList($condition = array(), $field = '*', $page = null, $order = 'al_addtime desc', $limit = '') {
		return $this->table('bean_admin_log')->field($field)->where($condition)->page($page)->order($order)->limit($limit)->select();
	}

	/**
	 * getBeanAdminLogInfo 获取金豆管理员信息
	 * @param  [type]  $condition [description]
	 * @param  string  $field     [description]
	 * @param  boolean $master    [description]
	 * @return [type]             [description]
	 */
	public function getBeanAdminLogInfo($condition, $field = '*', $master = false) {
		return $this->table('bean_admin_log')->field($field)->where($condition)->master($master)->find();
	}

	/**
	 * addBeanAdminLog 添加金豆管理员日志
	 * @param [type] $data [description]
	 */
	public function addBeanAdminLog($data) {
		$insert = $this->table('bean_admin_log')->insert($data);
		if (!$insert) {
			throw new Exception('添加金豆管理员日志失败');
		} else {
			return $insert;
		}
	}

	/**
	 * editBeanAdminLog 更新金豆管理员日志
	 * @param  [type] $condition [description]
	 * @param  [type] $data      [description]
	 * @return [type]            [description]
	 */
	public function editBeanAdminLog($condition, $data) {
		$update = $this->table('bean_admin_log')->where($condition)->update($data);
		if (!$update) {
			throw new Exception('更新金豆管理员日志失败');
		}
	}

	/**
	 * getBeanDayLogList 获取金豆列表
	 * @param  array  $condition [description]
	 * @param  string $field     [description]
	 * @param  [type] $page      [description]
	 * @param  string $order     [description]
	 * @param  string $limit     [description]
	 * @return [type]            [description]
	 */
	public function getBeanDayLogList($condition = array(), $field = '*', $page = null, $order = 'bdl_addtime desc', $limit = '') {
		return $this->table('bean_day_log')->field($field)->where($condition)->page($page)->order($order)->limit($limit)->select();
	}

	public function getAllBeanDayLogList($fields = '*', $order = 'bdl_addtime desc') {
		$sql = "SELECT " . $fields . " FROM " . DBPRE . "bean_day_log ORDER BY " . $order;
		return Model()->query($sql);
	}

	/**
	 * getBeanDayLogInfo 获取金豆信息
	 * @param  [type]  $condition [description]
	 * @param  string  $field     [description]
	 * @param  boolean $master    [description]
	 * @return [type]             [description]
	 */
	public function getBeanDayLogInfo($condition, $field = '*', $master = false) {
		return $this->table('bean_day_log')->field($field)->where($condition)->master($master)->find();
	}

	/**
	 * addBeanDayLog 添加金豆日志
	 * @param [type] $data [description]
	 */
	public function addBeanDayLog($data) {
		$insert = $this->table('bean_day_log')->insert($data);
		if (!$insert) {
			throw new Exception('添加金豆管理员日志失败');
		} else {
			return $insert;
		}
	}

	/**
	 * editBeanDayLog 更新金豆日志
	 * @param  [type] $condition [description]
	 * @param  [type] $data      [description]
	 * @return [type]            [description]
	 */
	public function editBeanDayLog($condition, $data) {
		$update = $this->table('bean_day_log')->where($condition)->update($data);
		if (!$update) {
			throw new Exception('更新金豆管理员日志失败');
		}
	}

	/**
	 * getBeanConsumeEquityInfo 获取金豆消费股权信息
	 * @param  array  $condition [description]
	 * @param  string $fields    [description]
	 * @return [type]            [description]
	 */
	public function getBeanConsumeEquityList($condition = array(), $fields = '*') {
		return $this
			->table('bean_consume_log,equity_log')
			->field($fields)
			->where($condition)
			->join('inner')
			->on('bean_consume_log.cl_sn=equity_log.elg_sn')
			->order('cl_addtime desc')
			->select();
	}

	/**
	 * getBeanExchangeEquityList 获取金豆兑换股权列表
	 * @param  [type] $condition [description]
	 * @param  string $fields    [description]
	 * @param  [type] $page      [description]
	 * @param  string $order     [description]
	 * @param  string $limit     [description]
	 * @return [type]            [description]
	 */
	public function getBeanExchangeEquityList($condition, $fields = '*', $page = null, $order = 'el_addtime desc', $limit = '') {
		return $this
			->table('bean_exchange_log,equity_log')
			->field($fields)
			->where($condition)
			->join('inner')
			->on('bean_exchange_log.el_sn=equity_log.elg_sn')
			->page($page)
			->order($order)
			->limit($limit)
			->select();
	}

	/**
	 * getConsumeNumById 获取会员消费次数
	 * @param  [type] $member_id [description]
	 * @return [type]            [description]
	 */
	public function getConsumeNumById($member_id) {
		return $this->table('bean_consume_log')->where(array('cl_member_id' => $member_id, 'cl_capping_state' => 0))->count();
	}

	/**
	 * getMemberDonateNum 获取转赠次数
	 * @param  [type] $member_id [description]
	 * @return [type]            [description]
	 */
	public function getMemberDonateNum($member_id) {
		return $this->table('bean_donate_log')->where(array('dl_from_member_id' => $member_id))->count();
	}

	/**
	 * getMemberToDonateNum 获取被转增次数
	 * @param  [type] $member_id [description]
	 * @return [type]            [description]
	 */
	public function getMemberToDonateNum($member_id) {
		return $this->table('bean_donate_log')->where(array('dl_to_member_id' => $member_id))->count();
	}

	/**
	 * joinBeanListArr 拼接金豆列表数组
	 * @param  [type] $list [description]
	 * @return [type]       [description]
	 */
	public function joinBeanListArr($list) {
		foreach ($list as $k => $v) {
			$sn = $v['lg_sn'];
			$bean_desc = '';
			$bean_type = '';
			switch ($v['lg_type']) {
			case 'rebate':
				$log_info = $this->getBeanRebateLogInfo(array('rl_sn' => $sn), 'rl_desc');
				$bean_desc = $v['lg_desc'];
				$bean_type = '分享奖励';
				break;
			case 'consume':
				$log_info = $this->getBeanConsumeLogInfo(array('cl_sn' => $sn), 'cl_desc');
				$bean_desc = $v['lg_desc'] ? $v['lg_desc'] : $log_info['cl_desc'];
				$bean_type = '消费';
				break;
			case 'withdraw':
				$log_info = $this->getBeanWithdrawLogInfo(array('wl_sn' => $sn), 'wl_desc');
				$bean_desc = $log_info['wl_desc'];
				$bean_type = '提现';
				break;
			case 'admin':
				$log_info = $this->getBeanAdminLogInfo(array('al_sn' => $sn), 'al_desc');
				$bean_desc = $log_info['al_desc'];
				$bean_type = 'redbag';
				break;
			case 'recharge':
				$log_info = $this->getBeanRechargeLogInfo(array('rl_sn' => $sn), 'rl_desc');
				$bean_desc = $log_info['rl_desc'];
				$bean_type = '充值';
				break;
			case 'exchange':
				$log_info = $this->getBeanExchangeLogInfo(array('el_sn' => $sn), 'el_desc');
				$bean_desc = $log_info['el_desc'];
				$bean_type = '兑换';
				break;
			case 'donate':
				$log_info = $this->getBeanDonateLogInfo(array('dl_sn' => $sn), 'dl_desc');
				$bean_desc = $log_info['dl_desc'];
				$bean_type = '转赠';
				break;
			case 'order_pay':
				$bean_desc = $v['lg_desc'];
				$bean_type = "订单支付";
				break;
			}
			$list[$k]['bean_desc'] = $bean_desc;
			$list[$k]['bean_type'] = $bean_type;
		}
		return $list;
	}

	/**
	 * getSilverBeanLogList 获取银豆日志列表
	 * @param  array  $condition [description]
	 * @param  string $field     [description]
	 * @param  [type] $page      [description]
	 * @param  string $order     [description]
	 * @param  string $limit     [description]
	 * @return [type]            [description]
	 */
	public function getSilverBeanLogList($condition = array(), $field = '*', $page = null, $order = 'slg_addtime desc', $limit = '') {
		return $this->table('silver_bean_log')->field($field)->where($condition)->page($page)->order($order)->limit($limit)->select();
	}

	/**
	 * getAllSilverBeanLogList 获取所有银豆记录列表
	 * @param  string $fields [description]
	 * @param  string $order  [description]
	 * @return [type]         [description]
	 */
	public function getAllSilverBeanLogList($fields = '*', $order = 'slg_addtime desc') {
		$sql = "SELECT " . $fields . " FROM " . DBPRE . "silver_bean_log ORDER BY " . $order;
		return Model()->query($sql);
	}

	/**
	 * getSilverBeanLogInfo 获取银豆日志信息
	 * @param  [type]  $condition [description]
	 * @param  string  $field     [description]
	 * @param  boolean $master    [description]
	 * @return [type]             [description]
	 */
	public function getSilverBeanLogInfo($condition, $field = '*', $master = false) {
		return $this->table('silver_bean_log')->field($field)->where($condition)->master($master)->find();
	}

	/**
	 * addSilverBeanLog 添加银豆日志
	 * @param [type] $data [description]
	 */
	public function addSilverBeanLog($data) {
		return $this->table('silver_bean_log')->insert($data);
	}

	/**
	 * editSilverBeanLog 更新银豆日志
	 * @param  [type] $condition [description]
	 * @param  [type] $data      [description]
	 * @return [type]            [description]
	 */
	public function editSilverBeanLog($condition, $data) {
		return $this->table('silver_bean_log')->where($condition)->update($data);
	}

	/**
	 * getAllNoClearBeanRebateLogList 获取所有未金豆未清返利记录
	 * @param  array  $condition [description]
	 * @return [type]            [description]
	 */
	public function getAllNoClearBeanRebateLogList() {
		$sql = "SELECT * FROM " . DBPRE . "bean_rebate_log WHERE rl_isclear = 0 ORDER BY rl_addtime desc";
		return $this->query($sql);
	}

	/**
	 * getBeanConsumeNum 获取金豆消费人数
	 * @param  [type] $condition [description]
	 * @return [type]            [description]
	 */
	public function getBeanConsumeNum($condition) {
		$info = $this->table('bean_consume_log')->field('cl_member_id')->where($condition)->group('cl_member_id')->select();
		return count($info);
	}

	/**
	 * getBeanConsumeAmount 获取金豆消费总额
	 * @param  [type] $condition [description]
	 * @return [type]            [description]
	 */
	public function getBeanConsumeAmount($condition) {
		return $this->table('bean_consume_log')->where($condition)->sum('cl_amount');
	}

	/**
	 * getBeanConsumeItemNum 获取金豆消费笔数
	 * @param  [type] $condition [description]
	 * @return [type]            [description]
	 */
	public function getBeanConsumeItemNum($condition) {
		return $this->table('bean_consume_log')->where($condition)->count();
	}

	/**
	 * getBeanRechargeAmount 获取金豆充值金额
	 * @param  [type] $condition [description]
	 * @return [type]            [description]
	 */
	public function getBeanRechargeAmount($condition) {
		$condition['lg_type'] = "admin";
		return $this->table('bean_log')->where($condition)->sum('lg_amount');
	}

	/**
	 * getBeanDonateAmount 获取金豆赠送金额
	 * @param  [type] $condition [description]
	 * @return [type]            [description]
	 */
	public function getBeanDonateAmount($condition) {
		return $this->table('bean_donate_log')->where($condition)->sum('dl_amount');
	}

	/**
	 * getBeanDonateCountNum 获取金豆转赠笔数
	 * @param  [type] $condition [description]
	 * @return [type]            [description]
	 */
	public function getBeanDonateCountNum($condition) {
		return $this->table('bean_donate_log')->where($condition)->count();
	}

	/**
	 * getBeanWithdrawAmount 获取金豆提现金额
	 * @param  [type] $condition [description]
	 * @return [type]            [description]
	 */
	public function getBeanWithdrawAmount($condition) {
		$condition['wl_state'] = 1;
		return $this->table('bean_withdraw_log')->where($condition)->sum('wl_amount');
	}

	/**
	 * getBeanWithdrawStayAmount 获取金豆待支付提现金额
	 * @param  [type] $condition [description]
	 * @return [type]            [description]
	 */
	public function getBeanWithdrawStayAmount($condition) {
		$condition['wl_state'] = 0;
		return $this->table('bean_withdraw_log')->where($condition)->sum('wl_amount');
	}
	/**
	 * getBeanWithdrawFeeAmount 获取金豆提现手续费金额
	 * @param  [type] $condition [description]
	 * @return [type]            [description]
	 */
	public function getBeanWithdrawFeeAmount($condition) {
		$condition['wl_state'] = 1;
		return $this->table('bean_withdraw_log')->where($condition)->sum('wl_fee');
	}

	/**
	 * getBonusAmount 获取奖金金额
	 * @param  [type] $condition [description]
	 * @param  [type] $type      [description]
	 * @return [type]            [description]
	 */
	public function getBonusAmount($condition, $type) {
		$condition['rl_type'] = $type;
		return $this->table('bean_rebate_log')->where($condition)->sum('rl_amount');
	}

	/**
	 * getBonusTotalAmount 获取奖金总金额
	 * @param  [type] $condition [description]
	 * @return [type]            [description]
	 */
	public function getBonusTotalAmount($condition) {
		return $this->getBonusAmount($condition, 'dividend') + $this->getBonusAmount($condition, 'promotion') + $this->getBonusAmount($condition, 'extend');
	}

	/**
	 * getSilverDonateAmount 获取银豆转赠金额
	 * @param  [type] $condition [description]
	 * @return [type]            [description]
	 */
	public function getSilverDonateAmount($condition) {
		$condition['slg_type'] = "donate";
		$condition['slg_amount'] = array('gt', 0);
		return $this->table('silver_bean_log')->where($condition)->sum('slg_amount');
	}

	/**
	 * getSilverDonateCountNum 获取银豆转赠次数
	 * @param  [type] $condition [description]
	 * @return [type]            [description]
	 */
	public function getSilverDonateCountNum($condition) {
		$condition['slg_type'] = "donate";
		$condition['slg_amount'] = array('gt', 0);
		return $this->table('silver_bean_log')->where($condition)->count();
	}

	/**
	 * getSilverAmount 获取银豆金额
	 * @param  [type] $condition [description]
	 * @return [type]            [description]
	 */
	public function getSilverAmount($condition, $type) {
		$condition['slg_type'] = $type;
		return $this->table('silver_bean_log')->where($condition)->sum('slg_amount');
	}

	/**
	 * getSilverCountNum 获取银豆笔数
	 * @param  [type] $condition [description]
	 * @param  [type] $type      [description]
	 * @return [type]            [description]
	 */
	public function getSilverCountNum($condition, $type) {
		$condition['slg_type'] = $type;
		return $this->table('silver_bean_log')->where($condition)->count();
	}

	/**
	 * getBeanPresentAmount 获取金豆金额
	 * @param  [type] $condition [description]
	 * @param  [type] $type      [description]
	 * @return [type]            [description]
	 */
	public function getBeanAmount($condition, $type) {
		$condition['lg_type'] = $type;
		return $this->table('bean_log')->where($condition)->sum('lg_amount');
	}

	/**
	 * getBeanCountNum 获取金豆笔数
	 * @param  [type] $condition [description]
	 * @param  [type] $type      [description]
	 * @return [type]            [description]
	 */
	public function getBeanCountNum($condition, $type) {
		$condition['lg_type'] = $type;
		return $this->table('bean_log')->where($condition)->count();
	}

	/**
	 * getStaticToatalAmount 获取静态分红总额
	 * @param  [type] $condition [description]
	 * @return [type]            [description]
	 */
	public function getStaticToatalAmount($condition) {
		return $this->table('bean_day_log')->where($condition)->sum('bdl_dividend_amount');
	}

	/**
	 * getPromotionTotalAmount 获取拓展奖总额
	 * @param  [type] $condition [description]
	 * @return [type]            [description]
	 */
	public function getPromotionTotalAmount($condition) {
		return $this->table('bean_day_log')->where($condition)->sum('bdl_promotion_amount');
	}

	/**
	 * getExtendTotalAmount 获取推广奖总额
	 * @param  [type] $condition [description]
	 * @return [type]            [description]
	 */
	public function getExtendTotalAmount($condition) {
		return $this->table('bean_day_log')->where($condition)->sum('bdl_extend_amount');
	}

	public function getMemberBalanceRecord($condition = array(), $field = '*', $page = null, $order = 'add_time desc', $limit = ''){
        return $this->table('member_balance_record')->field($field)->where($condition)->page($page)->order($order)->limit($limit)->select();

    }

}