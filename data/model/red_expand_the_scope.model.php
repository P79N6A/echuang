<?php
/**
 * 扩大范围
 *
 *
 *
 * *  (c) ()
 * @license
 * @link
 * @since
 */
defined('In33hao') or exit('Access Invalid!');
class red_expand_the_scopeModel extends Model {
    /**
     * 根据id查询一条扩大范围
     *
     * @param int $id 扩大范围id
     * @return array 一维数组
     */
    public function red_getRow($id){
        return Db::getRow(array('table'=>'expand_the_scope','field'=>'expand_the_scope_id','value'=>$id));
    }

    /**
     * 添加扩大范围
     *
     * @param array $input
     * @return bool
     */
    public function add($input){
        return Db::insert('expand_the_scope',$input);
    }
    
    /**
     * 更新扩大范围
     *
     * @param array $input
     * @param int $id
     * @return bool
     */
    public function updates($input,$id){
        return Db::update('expand_the_scope',$input,"expand_the_scope_id='$id' ");
    }

    /**
     * 删除扩大范围
     *
     * @param string $id
     * @return bool
     */
    public function del($id='1'){
        return Db::delete('expand_the_scope','expand_the_scope_id in('.$id.')');
    }

    /**
     * 删除扩大范围
     *
     * @param string $id
     * @return bool
     */
    public function sel($id){
        return $this->table('area')->where("area_parent_id = $id")->select();
    }

}
