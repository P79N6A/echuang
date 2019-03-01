<?php
/**
 * 查询所有増加过的范围条件
 *
 *
 *
 * *  (c) ()
 * @license
 * @link
 * @since
 */
defined('In33hao') or exit('Access Invalid!');
class red_expand_area_recordModel extends Model {
    /*
    *  查询所有増加过的范围条件
    */
    public function getPdRechargeList($condition = array(), $pagesize = '', $fields = '*', $order = '', $limit = '') {
    return $this->table('expand_list')->where($condition)->field($fields)->order($order)->limit($limit)->page($pagesize)->select();
    }
    /*
    * 查询所有増加过的范围
    */
    public function getselect(){
       return  $this->table('expand_list')->select();
    }

    /*
    * 増加范围
    */
    public function getexpandadd($data){
        return  $this->table('extended_range_record')->insert($data);
    }
}
