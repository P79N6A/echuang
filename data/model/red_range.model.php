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
class red_rangeModel extends Model {
    /*
    * 用户收到红包add
    */
    public function add($data){
        return $this->table('range')->insert($data);
    }

    /*
    * 用户收到红包find
    */
    public function rangefind($where){
        return $this->table('range')->where($where)->order('addtime desc')->find();
    }

    /*
    * 用户收到红包更新
    */
    public function rangeupdate($where,$data){
        return $this->table('range')->where($where)->update($data);
    }

}
