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
class red_rob_red_envelopesModel extends Model {

    /**
     * 用户抢红包add
     */
    public function addredenvelopes($data) {
        return $this->table('rob_red_envelopes')->insert($data);
    }

    /**
     * 修改用户优恵劵是否用
     */
    public function del($data,$where) {
        return $this->table('rob_red_envelopes')->where($where)->update($data);
    }
    /*
     * 谁已抢到的优恵劵
     */
    public function getmanagementList($condition){
        return $this->table('management_list')->where($condition)->find();
    }

    /**
     * 用户抢红包find
     */
    public function findredenvelopes($data) {
        return $this->table('rob_red_envelopes')->where($data)->find();
    }

    /**
     * 哪个用户抢红包所有数据
     */
    public function selectredenvelopes($data) {
        return $this->table('rob_red_envelopes')->where($data)->select();
    }

    /**
     * 用户存在余额中
     */
    public function updateredenvelopes($where,$data) {
        return $this->table('member')->where($where)->update($data);
    }
}
