<?php
/**
 * 金券封顶记录模块
 */
defined('In33hao') or exit('Access Invild!');
class equity_cappingModel extends Model {
	public function __construct() {
		parent::__construct('equity_capping_log');
	}

	/**
	 * addEquityCappingLog 添加金券封顶记录
	 * @param [type] $data [description]
	 */
	public function addEquityCappingLog($data) {
		return $this->insert($data);
	}
}