<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/28 0028
 * Time: 上午 9:08
 */
defined('In33hao') or exit('Access Invalid!');

class city_lordModel extends Model {
    public function __construct(){
        parent::__construct("rob_city_lord");
    }

    public function getRobCityLord($order_id){
        return $this->table('rob_city_lord')->where(['rob_city_lord_id'=>$order_id])->find();
    }

    public function addCityExchangeLog($data){
        return $this->table('rob_city_lord')->insert($data);
    }
}