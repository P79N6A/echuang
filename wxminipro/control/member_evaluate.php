<?php
/**
 * 会员评价
 *
 *  (c) ()
 * @license    
 * @link       
 * @since      
 */

defined('In33hao') or exit('Access Invalid!');

class member_evaluateControl extends apiEncryptMemberControl {

	public function __construct() {
		parent::__construct();
	}

	/**
	 * 评论
	 */
	public function indexOp() {
		$order_id = intval($_GET['order_id']);
		$return = Logic('member_evaluate')->validation($order_id, $this->member_info['member_id']);
		if (!$return['state']) {
			responseApiJson(0, $return['msg'], '');
		}
		//extract($return['data']);
		$store = array();
		$store['store_id'] = $return['data']['store_info']['store_id'];
		$store['store_name'] = $return['data']['store_info']['store_name'];
		$store['is_own_shop'] = $return['data']['store_info']['is_own_shop'];

		responseApiJson(1, '', array('store_info' => $store, 'order_goods' => $return['data']['order_goods']));
	}

	/**
	 * 评论保存
	 */
	public function saveOp() {
		$order_id = intval($_POST['order_id']);
		$return = Logic('member_evaluate')->validation($order_id, $this->member_info['member_id']);
		if (!$return['state']) {
			responseApiJson(0, $return['msg'], '');
		}
		extract($return['data']);
		$return = Logic('member_evaluate')->save($_POST, $order_info, $store_info, $order_goods, $this->member_info['member_id'], $this->member_info['member_name']);

		if (!$return['state']) {
			responseApiJson(0, $return['msg'], '');
		} else {
			responseApiJson(1, '评价成功', '');
		}
	}
}
