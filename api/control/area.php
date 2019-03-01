<?php
/**
 * 地区
 *
 *
 *  (c) ()
 * @license
 * @link
 * @since
 */
header("Access-Control-Allow-Origin: *");

defined('In33hao') or exit('Access Invalid!');
class areaControl extends apiEncryptCommonControl {
	public function __construct() {
		parent::__construct();
	}

	public function indexOp() {

	    $distribution_logic = Logic('distribution');
	    $distribution_logic->getInviteId(10000224);

		$this->area_listOp();
	}

	/**
	 * 地区列表
	 */
	public function area_listOp() {
		$area_id = intval($_GET['area_id']);

		$model_area = Model('area');

		$condition = array();
		if ($area_id > 0) {
			$condition['area_parent_id'] = $area_id;
		} else {
			$condition['area_deep'] = 1;
		}
		$area_list = $model_area->getAreaList($condition, 'area_id,area_name');
		responseApiJson(1, '', array('area_list' => $area_list));
	}

}
