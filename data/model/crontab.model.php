<?php
/**
 * 计划任务模块
 *
 */
defined('In33hao') or exit('Access Invild!');
class crontabModel extends Model {
	public function __construct() {
		parent::__construct();
	}

	/**
	 * getCrontabLogList 获取计划任务日志列表
	 * @param  array  $condition [description]
	 * @param  string $field     [description]
	 * @param  [type] $page      [description]
	 * @param  string $order     [description]
	 * @param  string $limit     [description]
	 * @return [type]            [description]
	 */
	public function getCrontabLogList($condition = array(), $field = '*', $page = null, $order = 'crontab_addtime desc', $limit = '') {
		return $this->table('crontab_log')->field($field)->where($condition)->page($page)->order($order)->limit($limit)->select();
	}

	/**
	 * getCrontabLogInfo 获取计划任务信息
	 * @param  [type]  $condition [description]
	 * @param  string  $field     [description]
	 * @param  boolean $master    [description]
	 * @return [type]             [description]
	 */
	public function getCrontabLogInfo($condition, $field = '*', $master = false) {
		return $this->table('crontab_log')->field($field)->where($condition)->master($master)->find();
	}

	/**
	 * addBeanLog 添加计划任务日志
	 * @param [type] $data [description]
	 */
	public function addCrontabLog($data) {
		$insert = $this->table('crontab_log')->insert($data);
		if (!$insert) {
			throw new Exception('添加计划任务日志失败');
		} else {
			return $insert;
		}
	}

	/**
	 * editBeanLog 更新计划任务日志
	 * @param  [type] $condition [description]
	 * @param  [type] $data      [description]
	 * @return [type]            [description]
	 */
	public function editCrontabLog($condition, $data) {
		$update = $this->table('crontab_log')->where($condition)->update($data);
		if (!$update) {
			throw new Exception('更新计划任务日志失败');
		}
	}

}