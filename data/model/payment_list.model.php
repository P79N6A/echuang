<?php
/**
 * 支付列表
 *
 */

defined('In33hao') or exit('Access Invalid!');
class payment_listModel extends Model {

	// 开启状态开启标识
	const STATE_OPEN = 1;

	// 本地支付标识
	const TAG_LOCAL = 0;

	// 第三方支付标识
	const TAG_THIRD = 1;

	public function __construct() {
		parent::__construct('payment_list');
	}

	/**
	 * 读取单行信息
	 *
	 * @param
	 * @return array 数组格式的返回结果
	 */
	public function getPaymentInfo($condition = array()) {
		return $this->where($condition)->find();
	}

	/**
	 * 读开启中的取单行信息
	 *
	 * @param
	 * @return array 数组格式的返回结果
	 */
	public function getPaymentOpenInfo($condition = array()) {
		$condition['payment_state'] = self::STATE_OPEN;
		return $this->where($condition)->find();
	}

	/**
	 * 读取多行
	 *
	 * @param
	 * @return array 数组格式的返回结果
	 */
	public function getPaymentList($condition = array()) {
		return $this->where($condition)->select();
	}

	/**
	 * 读取开启中的支付方式
	 *
	 * @param
	 * @return array 数组格式的返回结果
	 */
	public function getPaymentOpenList($condition = array()) {
		$condition['payment_state'] = self::STATE_OPEN;
		return $this->where($condition)->key('payment_code')->select();
	}

	/**
	 * 更新信息
	 *
	 * @param array $param 更新数据
	 * @return bool 布尔类型的返回结果
	 */
	public function editPayment($data, $condition) {
		return $this->where($condition)->update($data);
	}

	/**
	 * 读取支付方式信息by Condition
	 *
	 * @param
	 * @return array 数组格式的返回结果
	 */
	public function getRowByCondition($conditionfield, $conditionvalue) {
		$param = array();
		$param['table'] = 'payment';
		$param['field'] = $conditionfield;
		$param['value'] = $conditionvalue;
		$result = Db::getRow($param);
		return $result;
	}

	/**
	 * is_online_pay 是否在线支付
	 * @param  [type]  $payment_code [description]
	 * @return boolean               [description]
	 */
	public function is_online_pay($payment_code) {
		if ($payment_code == 'beanpay') {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * 获取网页支付方式
	 */
	public function getWapPaymentList() {
		$condition['payment_state'] = self::STATE_OPEN;
		$condition['payment_tag'] = self::TAG_LOCAL;
		$local_paylist = $this->getPaymentList($condition);
		$condition['payment_tag'] = self::TAG_THIRD;
		$condition['payment_class'] = array('like', '%wap%');

		$third_paylist = $this->getPaymentList($condition);

		$paylist = array_merge($local_paylist, $third_paylist);
		return $paylist;
	}
}