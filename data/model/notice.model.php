<?php
/**
 * 公告模块
 * 
 */
defined('In33hao') or exit('Access Invild!');

class noticeModel extends Model {
	public function __construct() {
		parent::__construct();
	}

	/**
	 * getNoticeList 获取公告列表
	 * @param  array  $condition [description]
	 * @param  string $field     [description]
	 * @param  [type] $page      [description]
	 * @param  string $order     [description]
	 * @param  string $limit     [description]
	 * @return [type]            [description]
	 */
	public function getNoticeList($condition=array(), $field='*', $page=null, $order='notice_sort desc', $limit='') {
		return $this->table('notice')->field($field)->where($condition)->page($page)->order($order)->limit($limit)->select();
	}

	/**
	 * getNoticeInfo 获取公告信息
	 * @param  [type]  $condition [description]
	 * @param  string  $field     [description]
	 * @param  boolean $master    [description]
	 * @return [type]             [description]
	 */
	public function getNoticeInfo($condition, $field='*', $master=false) {
        return $this->table('notice')->field($field)->where($condition)->master($master)->find();
    }

    /**
     * addNotice 添加公告
     * @param [type] $data [description]
     */
	public function addNotice($data) {
		$insert = $this->table('notice')->insert($data);
		if (!$insert) {
			// throw new Exception('添加公告失败');
			return array('error'=>"添加公告失败");
		} else {
			return $insert;
		}
	}

	/**
	 * editNotice 更新公告
	 * @param  [type] $condition [description]
	 * @param  [type] $data      [description]
	 * @return [type]            [description]
	 */
	public function editNotice($condition, $data) {
		$update = $this->table('notice')->where($condition)->update($data);
		if (!$update) {
			throw new Exception('更新公告失败');
		} else {
			return $update;
		}
	}

	public function delNotice($condition) {
		$delete = $this->table('notice')->where($condition)->delete();
		if (!$delete) {
			return array('error'=>"删除公告失败");
		} else {
			return $delete;
		}
	}
	/**
	 * getNoticeClassList 获取公告索引列表
	 * @param  array  $condition [description]
	 * @param  string $field     [description]
	 * @param  [type] $page      [description]
	 * @param  string $order     [description]
	 * @param  string $limit     [description]
	 * @return [type]            [description]
	 */
	public function getNoticeClassList($condition=array(), $field='*', $page=null, $order='nc_sort desc', $limit='') {
		return $this->table('notice_class')->field($field)->where($condition)->page($page)->order($order)->limit($limit)->select();
	}

	/**
	 * getNoticeClassInfo 获取公告索引信息
	 * @param  [type]  $condition [description]
	 * @param  string  $field     [description]
	 * @param  boolean $master    [description]
	 * @return [type]             [description]
	 */
	public function getNoticeClassInfo($condition, $field='*', $master=false) {
        return $this->table('notice_class')->field($field)->where($condition)->master($master)->find();
    }

    /**
     * addNoticeClass 添加公告索引
     * @param [type] $data [description]
     */
	public function addNoticeClass($data) {
		$insert = $this->table('notice_class')->insert($data);
		if (!$insert) {
			throw new Exception('添加公告索引失败');
		} else {
			return $insert;
		}
	}

	/**
	 * editNoticeClass 更新公告索引
	 * @param  [type] $condition [description]
	 * @param  [type] $data      [description]
	 * @return [type]            [description]
	 */
	public function editNoticeClass($condition, $data) {
		$update = $this->table('notice_class')->where($condition)->update($data);
		if (!$update) {
			throw new Exception('更新公告索引失败');
		}
	}

	/**
	 * getNoticeClassArr 获取公告类型数组
	 * @return [type] [description]
	 */
	public function getNoticeClassArr() {
		$arr = array();
		$noticeClass_list = $this->getNoticeClassList(array(), 'nc_id,nc_name');
		foreach ($noticeClass_list as $v) {
			$arr[$v['nc_id']] = $v['nc_name'];
		}
		return $arr;
	}
}