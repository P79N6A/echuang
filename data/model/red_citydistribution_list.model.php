<?php
/**
 * 发红包
 *
 *
 *
 * *  (c) ()
 * @license
 * @link
 * @since
 */
defined('In33hao') or exit('Access Invalid!');
class red_citydistribution_listModel extends Model {
    /*
    * 城主分润添加
    */
    public function citydistriadd($data){
      return  $this->table('city_lord_share_benefits_administration')->insert($data);
    }
    /*
    * 城主分润查询
    */
    public function cityfind($where){
      return $this->table('city_list')->where($where)->find();
    }

    /*
     * 查询城主
     */
    public function citymemberfind($where){
        return $this->table('city_member')->where($where)->find();
    }
}
