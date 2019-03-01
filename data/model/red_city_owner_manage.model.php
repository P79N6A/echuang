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
class red_city_owner_manageModel extends Model {

    /**
     * 读取列表升级会员
     * @param array $condition
     *
     */
    public function getarray() {
        return $this->table('upgrade_return_list')->select();
    }
    public function getPdRechargeList($condition = array(), $pagesize = '', $fields = '*', $order = '', $limit = '') {
        return $this->table('city_list')->where($condition)->field($fields)->order($order)->limit($limit)->page($pagesize)->select();
    }

    public function getwhere($where,$fields='*'){
       return $this->table('member')->where($where)->field($fields)->select();
    }
    /*
    * 下级查上级
    */
    public function getfind($where){
       return $this->table('share_members_list')->where($where)->find();
    }

    /*
    * 下级查上级
    */
    public function getmemberslist($where){
       return $this->table('share_members_list')->where($where)->select();
    }

    /*
    * 只查会员是会员的信息level不是0
    */
    public function getmembermelist($where){
       return $this->table('member_me')->where($where)->select();
    }
    /*
    * 只查会员是会员的信息level是0
    */
    public function getmemberfanslist($where){
       return $this->table('member_fans')->where($where)->select();
    }
}
