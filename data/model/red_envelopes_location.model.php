<?php
/**
 * 一条范围
 *
 *
 *
 * *  (c) ()
 * @license
 * @link
 * @since
 */
defined('In33hao') or exit('Access Invalid!');
class red_envelopes_locationModel extends Model {
    /**
     * 根据id查询一条范围
     *
     * @param int $id 范围id
     * @return array 一维数组
     */
    public function red_getRow($id){
        return Db::getRow(array('table'=>'red_envelopes_location','field'=>'red_envelopes_location_id','value'=>$id));
    }

    /**
     * 添加范围
     *
     * @param array $input
     * @return bool
     */
    public function add($input){
        return Db::insert('red_envelopes_location',$input);
    }
    
    /**
     * 更新范围
     *
     * @param array $input
     * @param int $id
     * @return bool
     */
    public function updates($input,$id){
        return Db::update('red_envelopes_location',$input," red_envelopes_location_id='$id' ");
    }

    /**
     * 删除范围
     *
     * @param string $id
     * @return bool
     */
    public function del($id='1'){
        return Db::delete('red_envelopes_location','red_envelopes_location_id in('.$id.')');
    }
}
