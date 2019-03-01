<?php
/**
 * 会员推荐关系
 *
 */
defined('In33hao') or exit('Access Invild!');

class member_inviteControl extends BaseMemberControl {
	public function indexOp() {
		parent::__construct();
	}

	/**
	 * directListOp 直推列表
	 * @return [type] [description]
	 */
	public function directListOp() {
		$model_member_extend = Model('member_extend');
		$codition = array();
	}
}