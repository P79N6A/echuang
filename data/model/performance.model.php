<?php
/**
 * 业绩模块
 *
 */
defined('In33hao') or exit('Access Invild!');
class performanceModel extends Model {
	public function __construct() {
		parent::__construct();
	}

	/**
	 * getPerformanceIncLogList 获取业绩新增日志列表
	 * @param  array  $condition [description]
	 * @param  string $field     [description]
	 * @param  [type] $page      [description]
	 * @param  string $order     [description]
	 * @param  string $limit     [description]
	 * @return [type]            [description]
	 */
	public function getPerformanceIncLogList($condition = array(), $field = '*', $page = null, $order = 'pil_addtime desc', $limit = '') {
		return $this->table('performance_inc_log')->field($field)->where($condition)->page($page)->order($order)->limit($limit)->select();
	}

	/**
	 * getPerformanceIncLogInfo 获取业绩新增日志信息
	 * @param  [type]  $condition [description]
	 * @param  string  $field     [description]
	 * @param  boolean $master    [description]
	 * @return [type]             [description]
	 */
	public function getPerformanceIncLogInfo($condition, $field = '*', $master = false) {
		return $this->table('performance_inc_log')->field($field)->where($condition)->master($master)->find();
	}

	/**
	 * addPerformanceIncLog 添加业绩新增日志
	 * @param [type] $data [description]
	 */
	public function addPerformanceIncLog($data) {
		$insert = $this->table('performance_inc_log')->insert($data);
		if (!$insert) {
			throw new Exception('添加业绩新增日志失败');
		} else {
			return $insert;
		}
	}

	/**
	 * editPerformanceIncLog 更新业绩新增日志
	 * @param  [type] $condition [description]
	 * @param  [type] $data      [description]
	 * @return [type]            [description]
	 */
	public function editPerformanceIncLog($condition, $data) {
		$update = $this->table('performance_inc_log')->where($condition)->update($data);
		if (!$update) {
			throw new Exception('更新业绩新增日志失败');
		}
	}

	/**
	 * getPerformanceClearLogList 获取业绩清空日志列表
	 * @param  array  $condition [description]
	 * @param  string $field     [description]
	 * @param  [type] $page      [description]
	 * @param  string $order     [description]
	 * @param  string $limit     [description]
	 * @return [type]            [description]
	 */
	public function getPerformanceClearLogList($condition = array(), $field = '*', $page = null, $order = 'pcl_addtime desc', $limit = '') {
		return $this->table('performance_clear_log')->field($field)->where($condition)->page($page)->order($order)->limit($limit)->select();
	}

	/**
	 * getPerformanceClearLogInfo 获取业绩清空日志信息
	 * @param  [type]  $condition [description]
	 * @param  string  $field     [description]
	 * @param  boolean $master    [description]
	 * @return [type]             [description]
	 */
	public function getPerformanceClearLogInfo($condition, $field = '*', $master = false) {
		return $this->table('performance_clear_log')->field($field)->where($condition)->master($master)->find();
	}

	/**
	 * addPerformanceClearLog 添加业绩清空日志
	 * @param [type] $data [description]
	 */
	public function addPerformanceClearLog($data) {
		$insert = $this->table('performance_clear_log')->insert($data);
		if (!$insert) {
			throw new Exception('添加业绩清空日志失败');
		} else {
			return $insert;
		}
	}

	/**
	 * editPerformanceClearLog 更新业绩清空日志
	 * @param  [type] $condition [description]
	 * @param  [type] $data      [description]
	 * @return [type]            [description]
	 */
	public function editPerformanceClearLog($condition, $data) {
		$update = $this->table('performance_clear_log')->where($condition)->update($data);
		if (!$update) {
			throw new Exception('更新业绩清空日志失败');
		}
	}

	/**
	 * addMemberPerformanceIncLog 添加会员业绩日志(新增业绩、总业绩)
	 * @param  [type] $member_id [description]
	 * @return [type]            [description]
	 */
	public function addMemberPerformanceIncLog($member_id, $amount) {
		$pil_log_data = array();
		$pil_log_data['pil_member_id'] = $member_id;
		$pil_log_data['pil_type'] = 'new';
		$pil_log_data['pil_amount'] = $amount;
		$pil_log_data['pil_addtime'] = TIMESTAMP;
		$pil_log_data['pil_desc'] = '会员新增业绩增加' . $amount;
		$this->addPerformanceIncLog($pil_log_data);
		$pil_log_data['pil_type'] = 'total';
		$pil_log_data['pil_desc'] = '会员总业绩增加' . $amount;
		$this->addPerformanceIncLog($pil_log_data);
	}

	public function addMmeberPerformanceClearLog($member_id) {
		$pcl_log_data = array();
		$pcl_log_data['pcl_member_id'] = $member_id;
		$pcl_log_data['pcl_addtime'] = TIMESTAMP;
		$pcl_log_data['pcl_desc'] = '会员清空新增业绩';
		$this->addPerformanceClearLog($pcl_log_data);
	}
}