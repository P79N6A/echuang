<?php
/**
 * 文章
 * @
 * @license
 * @link
 * @since
 *
 **/

defined('In33hao') or exit('Access Invalid!');
class noticeControl extends mobileHomeControl {

	public function __construct() {
		parent::__construct();
	}

	/**
	 * 文章列表
	 */
	public function notice_listOp() {
		if (!empty($_GET['ac_id']) && intval($_GET['ac_id']) > 0) {
			$notice_class_model = Model('notice_class');
			$notice_model = Model('notice');
			$condition = array();

			$child_class_list = $notice_class_model->getChildClass(intval($_GET['ac_id']));
			$ac_ids = array();
			if (!empty($child_class_list) && is_array($child_class_list)) {
				foreach ($child_class_list as $v) {
					$ac_ids[] = $v['ac_id'];
				}
			}
			$ac_ids = implode(',', $ac_ids);
			$condition['ac_ids'] = $ac_ids;
			$condition['notice_show'] = '1';
			$notice_list = $notice_model->getnoticeList($condition);
			$notice_type_name = $this->notice_type_name($ac_ids);
			output_data(array('notice_list' => $notice_list, 'notice_type_name' => $notice_type_name));
		} else {
			output_error('缺少参数:文章类别编号');
		}
	}

	/**
	 * 根据类别编号获取文章类别信息
	 */
	private function notice_type_name() {
		if (!empty($_GET['ac_id']) && intval($_GET['ac_id']) > 0) {
			$notice_class_model = Model('notice_class');
			$notice_class = $notice_class_model->getOneClass(intval($_GET['ac_id']));
			return ($notice_class['ac_name']);
		} else {
			return ('缺少参数:文章类别编号');
		}
	}

	/**
	 * 单篇文章显示
	 */
	public function notice_showOp() {
		$notice_model = Model('notice');
		if (!empty($_GET['notice_id']) && intval($_GET['notice_id']) > 0) {
			$where['notice_id'] = $_GET['notice_id'];
			$notice = $notice_model->getNoticeInfo($where);
			$notice['notice_content'] = htmlspecialchars_decode($notice['notice_content']);
			if (empty($notice)) {
				output_error('文章不存在');
			} else {
				output_data($notice);
			}
		} else {
			output_error('缺少参数:文章编号');
		}
	}
}
