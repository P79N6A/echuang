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
class red_member_upgradeModel extends Model {

    /**
     * 读取列表升级会员
     * @param array $condition
     *
     */
    public function getarray() {
        return $this->table('red_upgrade_member')->select();
    }
    public function getPdRechargeList($condition = array(), $pagesize = '', $fields = '*', $order = '', $limit = '') {
        return $this->table('red_upgrade_member')->where($condition)->field($fields)->order($order)->limit($limit)->page($pagesize)->select();
    }
}
