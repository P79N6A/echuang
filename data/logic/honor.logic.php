<?php
defined('In33hao') or exit('Access Invild!');
/**
 * 封号逻辑
 */
class honorLogic {
	/**
	 * honor 封号操作
	 * @return [type] [description]
	 */
	public function honor() {
		if (C('honor_status') == '1') {
			$honor_time = strtotime("-" . C('honor_days') . " day");
			$equity_limit = C('honor_equity_num_limit') * C('equity_dividend_min_limit');
			$list = Model('member_extend')->getMemberExtendList(array('reg_time' => array('elt', $honor_time), 'member_equity' => array('elt', $equity_limit), 'member_state' => 1), '*', 'union');
			if (!empty($list)) {
				foreach ($list as $key => $info) {
					Model('member')->editMember(array('member_id' => $info['member_id']), array('member_state' => 0));
				}
			}
		}
	}
}