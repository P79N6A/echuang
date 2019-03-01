<?php
/**
 * 升级会员
 *
 *
 *
 * *  (c) ()
 * @license
 * @link
 * @since
 */
defined('In33hao') or exit('Access Invalid!');
class red_upgrade_returnModel extends Model {

    /**
     * 读取列表升级会员
     * @param array $condition
     *
     */
    public function getarray() {
        return $this->table('upgrade_return_list')->select();
    }
    public function getPdRechargeList($condition = array(), $pagesize = '', $fields = '*', $order = '', $limit = '') {
        return $this->table('upgrade_return_list')->where($condition)->field($fields)->order($order)->limit($limit)->page($pagesize)->select();
    }

    public function getwhere($where,$fields='*'){
       return $this->table('member')->where($where)->field($fields)->select();
    }
}
