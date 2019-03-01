<?php
/**
 * 服务器中心模块
 *
 */
defined('In33hao') or exit('Access Invild!');

class serviceModel extends Model {
	public function __construct() {
		parent::__construct();
	}

	/**
	 * getServiceLogList 获取服务中心日志列表
	 * @param  array  $condition [description]
	 * @param  string $field     [description]
	 * @param  [type] $page      [description]
	 * @param  string $order     [description]
	 * @param  string $limit     [description]
	 * @return [type]            [description]
	 */
	public function getServiceLogList($condition = array(), $field = '*', $page = null, $order = 'slg_addtime desc', $limit = '') {
		return $this->table('service_log')->field($field)->where($condition)->page($page)->order($order)->limit($limit)->select();
	}

	/**
	 * getServiceLogInfo 获取服务中心日志信息
	 * @param  [type]  $condition [description]
	 * @param  string  $field     [description]
	 * @param  boolean $master    [description]
	 * @return [type]             [description]
	 */
	public function getServiceLogInfo($condition, $field = '*', $master = false) {
		return $this->table('service_log')->field($field)->where($condition)->master($master)->find();
	}

	/**
	 * addServiceLog 添加服务中心日志
	 * @param [type] $data [description]
	 */
	public function addServiceLog($data) {
		$insert = $this->table('service_log')->insert($data);
		if (!$insert) {
			throw new Exception('添加服务中心日志失败');
		} else {
			return $insert;
		}
	}

	/**
	 * editServiceLog 更新服务中心日志
	 * @param  [type] $condition [description]
	 * @param  [type] $data      [description]
	 * @return [type]            [description]
	 */
	public function editServiceLog($condition, $data) {
		$update = $this->table('service_log')->where($condition)->update($data);
		if (!$update) {
			throw new Exception('更新服务中心日志失败');
		}
	}
	/**
	 * editServiceLog 删除服务中心申请记录
	 * @param  [type] $condition [description]
	 * @param  [type] $data      [description]
	 * @return [type]            [description]
	 */
	public function deleteServiceLog($condition) {

		$delete = $this->table('service_log')->where($condition)->delete();
		if (!$delete) {
			throw new Exception('删除记录失败');
		} else {
			return $delete;
		}
	}

	/**
	 * getNewServiceNum 获取新增服务中心数量
	 * @param  [type] $condition [description]
	 * @return [type]            [description]
	 */
	public function getNewServiceNum($condition) {
		$condition['slg_state'] = 1;
		return $this->table('service_log')->where($condition)->count();
	}
}