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
class red_member_robModel extends Model {

    /**
     * 哪个用户抢红包所有数据
     */
    public function selectmemberrob($data) {
        return $this->table('member_rob')->where($data)->select();
    }
}
