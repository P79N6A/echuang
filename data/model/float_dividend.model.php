<?php
/**
 * 浮动分红记录模块
 *
 */
defined('In33hao') or exit('Access Invild!');

class float_dividendModel extends Model {
	public function __construct() {
		parent::__construct('float_dividend_log');
	}

	/**
	 * getFloatDividendLogList 获取浮动分红日志列表
	 * @param  array  $condition [description]
	 * @param  string $field     [description]
	 * @param  [type] $page      [description]
	 * @param  string $order     [description]
	 * @param  string $limit     [description]
	 * @return [type]            [description]
	 */
	public function getFloatDividendLogList($condition = array(), $field = '*', $page = null, $order = 'flg_addtime desc', $limit = '') {
		return $this->field($field)->where($condition)->page($page)->order($order)->limit($limit)->select();
	}

	/**
	 * groupExportData 分组导出数据
	 * @param  [type] $condition [description]
	 * @param  [type] $fields    [description]
	 * @param  [type] $group     [description]
	 * @return [type]            [description]
	 */
	public function groupExportData($condition, $fields, $group) {
		return $this->field($fields)->where($condition)->group($group)->select();
	}

	public function getAllFloatDividendLogList($fields = '*', $order = 'flg_addtime desc') {
		$sql = "SELECT " . $fields . " FROM " . DBPRE . "float_dividend_log ORDER BY " . $order;
		return Model()->query($sql);
	}

	/**
	 * getFloatDividendLogInfo 获取浮动分红日志信息
	 * @param  [type]  $condition [description]
	 * @param  string  $field     [description]
	 * @param  boolean $master    [description]
	 * @return [type]             [description]
	 */
	public function getFloatDividendLogInfo($condition, $field = '*', $master = false) {
		return $this->field($field)->where($condition)->master($master)->find();
	}

	/**
	 * addFloatDividendLog 添加浮动分红日志
	 * @param [type] $data [description]
	 */
	public function addFloatDividendLog($data) {
		$insert = $this->insert($data);
		if (!$insert) {
			throw new Exception('添加浮动分红日志失败');
		} else {
			return $insert;
		}
	}

	/**
	 * editFloatDividendLog 更新浮动分红日志
	 * @param  [type] $condition [description]
	 * @param  [type] $data      [description]
	 * @return [type]            [description]
	 */
	public function editFloatDividendLog($condition, $data) {
		foreach ($condition as $key => $value) {
			if (is_array($value) && $key == 'flg_id') {
				foreach ($value as $k => $v) {
					$condition_edit[$key] = $v;
					$update = $this->where($condition_edit)->update($data);
				}
			} else {
				$update = $this->where($condition)->update($data);
			}
		}
		if (!$update) {
			throw new Exception('更新浮动分红日志失败');
		} else {
			return $update;
		}
	}

	/**
	 * getFloatDividendAmount 获取浮动分红总额
	 * @param  [type] $condition [description]
	 * @return [type]            [description]
	 */
	public function getFloatDividendAmount($condition) {
		return $this->table('float_dividend_log')->where($condition)->sum('flg_amount');
	}

	/**
	 * setFieldValue 设置字段值
	 * @param [type] $condition [description]
	 * @param [type] $field     [description]
	 * @param [type] $value     [description]
	 */
	public function setDividendFieldValue($condition, $field, $value) {
		return $this->table('float_dividend_log')
			->where($condition)
			->set_field($field, $value);
	}


    public function getMemberProfitRecordList($condition = array(), $field = '*', $page = null, $order = 'add_time desc', $limit = '') {
        return $this->table('member_profit_record')->field($field)->where($condition)->page($page)->order($order)->limit($limit)->select();
    }

}