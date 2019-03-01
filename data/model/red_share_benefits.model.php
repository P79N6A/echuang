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
class red_share_benefitsModel extends Model {
    /*
    * 下级给上级分润
    */
    public function sharebenefitsadd($data){
       return  $this->table('share_benefits')->insert($data);
    }

    /*
    * 下级给上级分润
    */
    public function sharebenefitsfind($where){
       return  $this->table('share_benefits_list')->where($where)->find();
    }

    /*
    * 下级给上级分润
    */
    public function getPdRechargeList($condition = array(), $pagesize = '', $fields = '*', $order = '', $limit = '') {
        return $this->table('share_benefits_list')->where($condition)->field($fields)->order($order)->limit($limit)->page($pagesize)->select();
    }


}
