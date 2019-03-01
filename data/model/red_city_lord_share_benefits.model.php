<?php
/**
 * 城主分润
 *
 *
 *
 * *  (c) ()
 * @license
 * @link
 * @since
 */
defined('In33hao') or exit('Access Invalid!');
class red_city_lord_share_benefitsModel extends Model {
    /**
     * 根据id查询一条城主分润
     *
     * @param int $id 扩大范围id
     * @return array 一维数组
     */
    public function red_getRow($id){
        return Db::getRow(array('table'=>'city_lord_share_benefits','field'=>'city_lord_share_benefits_id','value'=>$id));
    }

    /**
     * 根据id查询一条城主分润
     *
     * @param int $id 扩大范围id
     * @return array 一维数组
     */
    public function listgetRow($where){
        return $this->table('city_list')->where($where)->find();
    }

    /**
     * 添加城主分润
     *
     * @param array $input
     * @return bool
     */
    public function add($input){
        return Db::insert('city_lord_share_benefits',$input);
    }
    
    /**
     * 更新城主分润
     *
     * @param array $input
     * @param int $id
     * @return bool
     */
    public function updates($input,$id){
        return Db::update('city_lord_share_benefits',$input,"city_lord_share_benefits_id='$id' ");
    }

    /**
     * 删除城主分润
     *
     * @param string $id
     * @return bool
     */
    public function del($id='1'){
        return Db::delete('city_lord_share_benefits','city_lord_share_benefits_id in('.$id.')');
    }
}
