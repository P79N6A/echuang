<?php
/**
 * 红包用户推送数据
 *
 *
 *
 * *  (c) ()
 * @license
 * @link
 * @since
 */
defined('In33hao') or exit('Access Invalid!');
class red_redbag_pushModel extends Model {
    /*
    *  查询所有红包用户推送数据
    */
    public function getPdRechargeList($condition = array(), $pagesize = '', $fields = '*', $order = '', $limit = '') {
    return $this->table('redbag_push_lish')->where($condition)->field($fields)->order($order)->limit($limit)->page($pagesize)->select();
    }
}
