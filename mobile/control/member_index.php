<?php
/**
 * 我的商城
 *
 *  (c) ()
 * @license
 * @link
 * @since
 */

defined('In33hao') or exit('Access Invalid!');

class member_indexControl extends mobileMemberControl {

	public function __construct() {
		parent::__construct();
	}

	/**
	 * 我的商城
	 */
	public function indexOp() {
		$condition['buyer_id'] = $this->member_info['member_id'];
		$condition['order_type'] = array('in', array(1, 3));
		$condition['order_state'] = ORDER_STATE_NEW;
		$member_info = array();
		$member_info['user_name'] = $this->member_info['member_name'];
		$member_info['avatar'] = getMemberAvatarForID($this->member_info['member_id']);
		$member_gradeinfo = Model('member')->getOneMemberGrade(intval($this->member_info['member_exppoints']));
		$member_info['level_name'] = $member_gradeinfo['level_name'];
		$member_info['favorites_store'] = Model('favorites')->getStoreFavoritesCountByMemberId($this->member_info['member_id']);
		$member_info['favorites_goods'] = Model('favorites')->getGoodsFavoritesCountByMemberId($this->member_info['member_id']);
		// 交易提醒
		$model_order = Model('order');
		$member_info['order_nopay_count'] = $model_order->getOrderCountByID('buyer', $this->member_info['member_id'], 'NewCount');
		$member_info['order_noreceipt_count'] = $model_order->getOrderCountByID('buyer', $this->member_info['member_id'], 'SendCount');
		$member_info['order_notakes_count'] = $model_order->getOrderCountByID('buyer', $this->member_info['member_id'], 'TakesCount');
		$member_info['order_noeval_count'] = $model_order->getOrderCountByID('buyer', $this->member_info['member_id'], 'EvalCount');
		// 售前退款
		$condition = array();
		$condition['buyer_id'] = $this->member_info['member_id'];
		$condition['refund_state'] = array('lt', 3);
		$member_info['return'] = Model('refund_return')->getRefundReturnCount($condition);
		// 交易提醒
		$model_order = Model('order');
		$sql_order_nopay_count = "SELECT COUNT(*) as count from dfn_orders WHERE buyer_id='" . $this->member_info['member_id'] . "' and order_state=10";
		$sql_order_noreceipt_count = "SELECT COUNT(*) as count from dfn_orders WHERE buyer_id='" . $this->member_info['member_id'] . "' and order_state=20";
		$sql_order_notakes_count = "SELECT COUNT(*) as count from dfn_orders WHERE buyer_id='" . $this->member_info['member_id'] . "' and order_state=30";
		$sql_order_noeval_count = "SELECT COUNT(*) as count from dfn_orders WHERE buyer_id='" . $this->member_info['member_id'] . "' and order_state=40 and evaluation_state=0";
		$order_nopay_count = $model_order->query($sql_order_nopay_count);
		$order_noreceipt_count = $model_order->query($sql_order_noreceipt_count);
		$order_notakes_count = $model_order->query($sql_order_notakes_count);
		$order_order_noeval_count = $model_order->query($sql_order_noeval_count);
		$member_info['order_nopay_count'] = empty($order_nopay_count[0]['count'])?0:$order_nopay_count[0]['count'];
		$member_info['order_noreceipt_count'] = empty($order_noreceipt_count[0]['count'])?0:$order_noreceipt_count[0]['count'];
		$member_info['order_notakes_count'] = empty($order_notakes_count[0]['count'])?0:$order_notakes_count[0]['count'];
		$member_info['order_noeval_count'] = empty($order_order_noeval_count[0]['count'])?0:$order_order_noeval_count[0]['count'];
        $member_info['member_level'] = str_replace(array(0,1,2), array('普通用户','普通会员','创客VIP'),$this->member_info['member_level']);
        $member_info['integral'] = $this->member_integral['m_integral'];
        $member_info['estimate_integral'] = $this->member_integral['estimate_integral'];
        output_data(array('member_info' => $member_info));
	}

	/**
	 * 我的资产
	 */
	public function my_assetOp() {
		$param = $_GET;
		$fields_arr = array('point', 'predepoit', 'available_rc_balance', 'redpacket', 'voucher');
		$fields_str = trim($param['fields']);
		if ($fields_str) {
			$fields_arr = explode(',', $fields_str);
		}
		$member_info = array();
		if (in_array('point', $fields_arr)) {
			$member_info['point'] = $this->member_info['member_points'];
		}
		if (in_array('predepoit', $fields_arr)) {
			$member_info['predepoit'] = $this->member_info['available_predeposit'];
		}
		if (in_array('available_rc_balance', $fields_arr)) {
			$member_info['available_rc_balance'] = $this->member_info['available_rc_balance'];
		}
		if (in_array('redpacket', $fields_arr)) {
			$member_info['redpacket'] = Model('redpacket')->getCurrentAvailableRedpacketCount($this->member_info['member_id']);
		}
		if (in_array('voucher', $fields_arr)) {
			$member_info['voucher'] = Model('voucher')->getCurrentAvailableVoucherCount($this->member_info['member_id']);
		}
		output_data($member_info);
	}
}
