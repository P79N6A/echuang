<?php
/**
 * 订单管理
 *
 *
 *
 *
 * @
 * @license  
 * @link      
 */
defined('In33hao') or exit('Access Invalid!');
class red_money_logoModel extends Model {
	/*
	* 红包订单记录
	*/
	public function logoselect($where)
	{
		return $this->table('money_logo')->where($where)->order('money_id DESC')->select();
	}

	/*
	* 红包订单记录添加
	*/
	public function logoadd($data)
	{
		return $this->table('member_balance_record')->insert($data);
	}
}
