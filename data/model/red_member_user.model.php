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
class red_member_userModel extends Model {
    /*
    * 用户收到红包find
    */
    public function memberuserfind($where){
        return $this->table('member_user')->where($where)->find();
    }
    /*
    * 用户等级
    */
    public function memberextendfind($where){
        return $this->table('member_extend')->where($where)->find();
    }
    /*
    * 
    */
    public function memberuserupdate($where,$data){
        return $this->table('member_extend')->where($where)->update($data);
    }
}
