<?php
/**
 * 会员分润
 *
 *
 *
 * *  (c) ()
 * @license
 * @link
 * @since
 */
defined('In33hao') or exit('Access Invalid!');
class red_membership_distributionModel extends Model {
    /**
     * 根据id查询一条会员分润
     *
     * @param int $id 会员分润id
     * @return array 一维数组
     */
    public function red_getRow($id){
        return Db::getRow(array('table'=>'membership_distribution','field'=>'membership_distribution_id','value'=>$id));
    }

    /**
     * 添加会员分润
     *
     * @param array $input
     * @return bool
     */
    public function add($input){
        return Db::insert('membership_distribution',$input);
    }
    
    /**
     * 更新会员分润
     *
     * @param array $input
     * @param int $id
     * @return bool
     */
    public function updates($input,$id){
        return Db::update('membership_distribution',$input," membership_distribution_id='$id' ");
    }

    /**
     * 删除会员分润
     *
     * @param string $id
     * @return bool
     */
    public function del($id='1'){
        return Db::delete('membership_distribution','membership_distribution_id in('.$id.')');
    }
}
