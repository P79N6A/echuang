<?php
/**
 * 会员系统纪录
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/19 0019
 * Time: 下午 16:59
 */
defined('In33hao') or exit('Access Invalid!');
class member_profitModel extends Model{
    public function __construct() {
        parent::__construct('member_profit_record');
    }

    /**获取单个会员余额记录
     * @return mixed
     */
    public function getMemberBalanceRecord($member_id){
        return $this->table('member_balance_record')->where(['member_id'=>$member_id])->order('add_time desc')->select();
    }

    /**
     * 添加分红记录
     * @param $insert
     * @return mixed
     */
    public function addProfitRecord($insert){
        return $this->insert($insert);
    }

    /**
     * 获取分红明细
     * @param $condition
     * @return mixed
     */
    public function getBonusList($condition){
       return $this->where($condition)->select();
    }

    /**
     * 获取分润记录
     * @param $condition
     */
    public function getOrderProfitRecord($condition){
        return $this->table('member_profit_record')->where($condition)->select();
    }

    /**
     * 获取奖励种类所对应数量
     * @param $condition
     * @return mixed
     */
    public function getRewardNumArr($condition){
        return $this->table('member_profit_record')->field('count(type) as num,type')->where($condition)->group('type')->select();
    }


    /**
     * 获取各奖励总额
     * @param $condition
     * @param $field
     * @return mixed
     */
    public function getPrizeAmount($condition,$field){
        return $this->table('member_profit_record')->where($condition)->sum($field);
    }


    public function getStrategicPartnerBonusAmount($condition){
        return $this->table('balance_consume_record')->where($condition)->sum('cl_amount');
    }

}