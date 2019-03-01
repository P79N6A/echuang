<?php
/**
 * Created by PhpStorm.
 * User: 白战国
 * Date: 2018/10/30
 * Time: 0:29
 */
defined('In33hao') or exit('Access Invalid!');
class member_system_setModel extends Model{
    public function __construct() {
        parent::__construct('member_system_set');
    }

    /**
     * 获取库存相关设置信息
     */
    public function getInventoryInfo(){
        $product_num_arr = $this->table('member_level')->field('ml_id,ml_give_product')->select();
        $return = array();
        foreach ($product_num_arr as $v){
            $return['product_num_arr'][$v['ml_id']] = $v['ml_give_product'];
        }
        $report_product_price = $this->table('member_system_set')->field('report_product_price')->find();
        $return['report_product_price'] = $report_product_price['report_product_price'];
        return $return;
    }
}