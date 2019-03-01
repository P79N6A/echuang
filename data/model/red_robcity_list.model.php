<?php
/**
 * 城主原价
 *
 *
 *
 * *  (c) ()
 * @license
 * @link
 * @since
 */
defined('In33hao') or exit('Access Invalid!');
class red_robcity_listModel extends Model {

    /**
     * 读取列表城主原价
     * @param array $condition
     *
     */
    public function getPdRechargeList($condition = array(), $pagesize = '', $fields = '*', $order = '', $limit = '') {
        return $this->table('robcity_list')->where($condition)->field($fields)->order($order)->limit($limit)->page($pagesize)->select();
    }

    /*
     * 查询发红包的订单号
     * 返回订单信息
     */
    public function robcitylistfind($where){
        return $this->table('city_price')->where($where)->find();
    }

   /*
    * 更改状态
    */
   public function robcitylistupdate($where,$data){
       return $this->table('city_price')->where($where)->update($data);
   }
    /*
    * 抢城主
    */
    public function robcitylistadd($data){
      return  $this->table('city_price')->insert($data);
    }

    /**
     * 抢城主记录
     * @param $condition
     */
    public function getCityExchangeRecord($condition = array(), $pagesize = '', $fields = '*', $order = '', $limit = ''){
        return $this->table('city_exchange')->where($condition)->field($fields)->order($order)->limit($limit)->page($pagesize)->select();
    }
}
