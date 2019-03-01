<?php
/**
 * 拨比模块
 */
defined('In33hao') or exit('Access Invild!');

class dial_statisticsModel extends Model {
	/**
	 * getBeanLogList 获取拨比列表
	 * @param  array  $condition [description]
	 * @param  string $field     [description]
	 * @param  [type] $page      [description]
	 * @param  string $order     [description]
	 * @param  string $limit     [description]
	 * @return [type]            [description]
	 */
	public function getDialStatisticsList($condition = array(), $field = '*', $page = null, $order = 'lg_addtime desc', $limit = '') {
		return $this->table('dial_statistics')->field($field)->where($condition)->page($page)->order($order)->limit($limit)->select();
	}

	/**
	 * getAllBeanLogList 获取所有拨比信息
	 * @param  string $fields [description]
	 * @param  string $order  [description]
	 * @return [type]         [description]
	 */
	public function getAllDialStatisticsList($fields = '*', $order = 'lg_addtime desc') {
		$sql = "SELECT " . $fields . " FROM " . DBPRE . "dial_statistics ORDER BY " . $order;
		return Model()->query($sql);
	}

	/**
	 * getBeanLogInfo 获取拨比信息
	 * @param  [type]  $condition [description]
	 * @param  string  $field     [description]
	 * @param  boolean $master    [description]
	 * @return [type]             [description]
	 */
	public function getDialStatisticsInfo($condition, $field = '*') {
		return $this->table('dial_statistics')->field($field)->where($condition)->find();
	}

	/**
	 * addBeanLog 添加拨比日志
	 * @param [type] $data [description]
	 */
	public function addDialStatistics($data) {
		return $this->table('dial_statistics')->insert($data);
	}

	/**
	 * editBeanLog 更新拨比日志
	 * @param  [type] $condition [description]
	 * @param  [type] $data      [description]
	 * @return [type]            [description]
	 */
	public function editDialStatistics($condition, $data) {
		return $this->table('dial_statistics')->where($condition)->update($data);

	}
}