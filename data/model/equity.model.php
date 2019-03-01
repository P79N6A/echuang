<?php
/**
 * 股权模块
 *
 */
defined('In33hao') or exit('Access Invild!');

class equityModel extends Model {

	/**
	 * getEquityLogList 获取股权列表
	 * @param  array  $condition [description]
	 * @param  string $fields     [description]
	 * @param  [type] $page      [description]
	 * @param  string $order     [description]
	 * @param  string $limit     [description]
	 * @return [type]            [description]
	 */
	public function getEquityLogList($condition = array(), $fields = '*', $page = null, $order = 'elg_addtime desc', $limit = '') {
		return $this->table('equity_log')->field($fields)->where($condition)->page($page)->order($order)->limit($limit)->select();
	}

	public function getAllEquityLogList($fields = '*', $order = 'elg_addtime desc') {
		$sql = "SELECT " . $fields . " FROM " . DBPRE . "equity_log ORDER BY " . $order;
		return Model()->query($sql);
	}

	/**
	 * getEquityLogInfo 获取股权信息
	 * @param  [type]  $condition [description]
	 * @param  string  $fields     [description]
	 * @param  boolean $master    [description]
	 * @return [type]             [description]
	 */
	public function getEquityLogInfo($condition, $fields = '*', $master = false) {
		return $this->table('equity_log')->field($fields)->where($condition)->master($master)->find();
	}

	/**
	 * addEquityLog 添加股权日志
	 * @param [type] $data [description]
	 */
	public function addEquityLog($data) {
	    $res = $this->table('equity_log')->insert($data);
        return $res;
	}

	/**
     * 添加管理员调整股权日志
     */
	public function addAdminEquityLog($data){
        $res = $this->table('equity_admin_log')->insert($data);
        return $res;
    }
	
	/**
	 * editEquityLog 更新股权日志
	 * @param  [type] $condition [description]
	 * @param  [type] $data      [description]
	 * @return [type]            [description]
	 */
	public function editEquityLog($condition, $data) {
		$update = $this->table('equity_log')->where($condition)->update($data);
		if (!$update) {
			throw new Exception('更新股权日志失败');
		}
		return $update;
	}

	/**
	 * getEquityAdminLogList 获取管理员股权日志列表
	 * @param  array  $condition [description]
	 * @param  string $fields     [description]
	 * @param  [type] $page      [description]
	 * @param  string $order     [description]
	 * @param  string $limit     [description]
	 * @return [type]            [description]
	 */
	public function getEquityAdminLogList($condition = array(), $fields = '*', $page = null, $order = 'eal_addtime desc', $limit = '') {
		return $this->table('equity_admin_log')->field($fields)->where($condition)->page($page)->order($order)->limit($limit)->select();
	}

	/**
	 * getEquityAdminLogInfo 获取管理员股权信息
	 * @param  [type]  $condition [description]
	 * @param  string  $fields     [description]
	 * @param  boolean $master    [description]
	 * @return [type]             [description]
	 */
	public function getEquityAdminLogInfo($condition, $fields = '*', $master = false) {
		return $this->table('equity_admin_log')->field($fields)->where($condition)->master($master)->find();
	}

	/**
	 * addEquityAdminLog 添加管理员股权日志
	 * @param [type] $data [description]
	 */
	public function addEquityAdminLog($data) {
		$insert = $this->table('equity_admin_log')->insert($data);
		if (!$insert) {
			throw new Exception('添加管理员股权日志失败');
		} else {
			return $insert;
		}
	}

	/**
	 * editEquityAdminLog 更新管理员股权日志
	 * @param  [type] $condition [description]
	 * @param  [type] $data      [description]
	 * @return [type]            [description]
	 */
	public function editEquityAdminLog($condition, $data) {
		$update = $this->table('equity_admin_log')->where($condition)->update($data);
		if (!$update) {
			throw new Exception('更新管理员股权日志失败');
		}
	}

	/**
	 * getEquityConsumeLogList 获取股权消费日志列表
	 * @param  array  $condition [description]
	 * @param  string $fields     [description]
	 * @param  [type] $page      [description]
	 * @param  string $order     [description]
	 * @param  string $limit     [description]
	 * @return [type]            [description]
	 */
	public function getEquityConsumeLogList($condition = array(), $fields = '*', $page = null, $order = 'eal_addtime desc', $limit = '') {
		return $this->table('equity_consume_log')->field($fields)->where($condition)->page($page)->order($order)->limit($limit)->select();
	}

	/**
	 * getEquityConsumeLogInfo 获取股权消费信息
	 * @param  [type]  $condition [description]
	 * @param  string  $fields     [description]
	 * @param  boolean $master    [description]
	 * @return [type]             [description]
	 */
	public function getEquityConsumeLogInfo($condition, $fields = '*', $master = false) {
		return $this->table('equity_consume_log')->field($fields)->where($condition)->master($master)->find();
	}

	/**
	 * addEquityConsumeLog 添加股权消费日志
	 * @param [type] $data [description]
	 */
	public function addEquityConsumeLog($data) {
		$insert = $this->table('equity_consume_log')->insert($data);
		if (!$insert) {
			throw new Exception('添加股权消费日志失败');
		} else {
			return $insert;
		}
	}

	/**
	 * editEquityConsumeLog 更新股权消费日志
	 * @param  [type] $condition [description]
	 * @param  [type] $data      [description]
	 * @return [type]            [description]
	 */
	public function editEquityConsumeLog($condition, $data) {
		$update = $this->table('equity_consume_log')->where($condition)->update($data);
		if (!$update) {
			throw new Exception('更新股权消费日志失败');
		}
	}

	/**
	 * joinEquityListArr 拼接股权列表数组
	 * @param  [type] $list [description]
	 * @return [type]       [description]
	 */
	public function joinEquityListArr($list) {
		foreach ($list as $k => $v) {
			$sn = $v['elg_sn'];
			$equity_desc = '';
			$equity_type = '';
			switch ($v['elg_type']) {
			case 'consume':
				$log_info = $this->getEquityConsumeLogInfo(array('ecl_sn' => $sn), 'ecl_desc');
				$equity_desc = $log_info['ecl_desc'];
				$equity_type = '消费';
				break;
			case 'admin':
				$log_info = $this->getEquityAdminLogInfo(array('eal_sn' => $sn), 'eal_desc');
				$equity_desc = $log_info['eal_desc'];
				$equity_type = 'redbag';
				break;
			case 'capping':
				$equity_desc = $v['elg_desc'];
				$equity_type = '封顶';
				break;
			}
			$list[$k]['equity_desc'] = $equity_desc;
			$list[$k]['equity_type'] = $equity_type;
		}
		return $list;
	}

	/**
	 * getConsumeEquityAmount 获取消费金券金额
	 * @param  [type] $condition [description]
	 * @return [type]            [description]
	 */
	public function getConsumeEquityAmount($condition) {
		$condition['elg_type'] = "consume";
		return $this->table('equity_log')->where($condition)->sum('elg_amount');
	}


	//添加股权日志
	public function addBalanceEquityLog($insert){
        $insert = $this->table('equity_log')->insert($insert);
        if (!$insert) {
            throw new Exception('添加股权日志失败');
        } else {
            return $insert;
        }
    }
	
	//获取股权交换数量
	public function getEquityExchangeAmount($condition,$field){
        return $this->table('equity_log')->where($condition)->sum($field);
    }

}