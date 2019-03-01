<?php
/**
 * 余额模型
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/26 0026
 * Time: 上午 10:58
 */

 defined('In33hao') or exit('Access Invild!');

 class balanceModel extends Model{

     public function __construct() {
         parent::__construct();
     }

     /**
      * addBalanceLog 添加余额日志
      * @param [type] $data [description]
      */
     public function addBalanceLog($data) {
         $insert = $this->table('member_balance_record')->insert($data);
         if (!$insert) {
             throw new Exception('添加余额日志失败');
         } else {
             return $insert;
         }
     }

     /**
      * addBalanceConsumeLog 添加余额消费日志
      * @param [type] $data [description]
      */
     public function addBalanceConsumeLog($data) {
         $insert = $this->table('balance_consume_record')->insert($data);
         if (!$insert) {
             throw new Exception('添加余额消费日志失败');
         } else {
             return $insert;
         }
     }

     /**
      * addBalanceWithdrawLog 添加余额提现记录
      * @param [type] $data [description]
      */
     public function addBalanceWithdrawLog($data) {
         $insert = $this->table('balance_withdraw_record')->insert($data);
         if (!$insert) {
             throw new Exception('添加余额提现记录失败');
         } else {
             return $insert;
         }
     }

     public function getBalanceRecord($condition, $fields = '*',$page = null, $order ='bdl_addtime desc', $master = false){
         return $this->table('member_balance_record')->where($condition)->field($fields)->page($page)->order($order)->master($master)->select();
     }


     /**
      * 获取提现信息
      * @param $condition
      * @param $page
      * @param string $order
      * @param string $fields
      * @return mixed
      */
     public function getBalanceWithdraw($condition,$page=null,$order='wl_addtime desc',$fields="*",$master = false){
         $res = $this->table('balance_withdraw_record')->page($page)->where($condition)->field($fields)->master($master)->order($order)->select();
        return $res;
     }


     /**
      * 更新提现记录
      * @param $condition
      * @param $data
      * @throws Exception
      */
     public function editBalanceWithdrawRecord($condition, $data) {
         $update = $this->table('balance_withdraw_record')->where($condition)->update($data);
         if (!$update) {
             throw new Exception('更新余额提现日志失败');
         }
     }
	 
	 
	 //获取提现数据总数
	  public function getBalanceConsumeAmount($condition,$field){
         return $this->table('balance_withdraw_record')->where($condition)->sum($field);
     }
 }