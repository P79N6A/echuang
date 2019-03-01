<?php
/**
 * 年龄
 *
 *
 *
 * *  (c) ()
 * @license
 * @link
 * @since
 */
defined('In33hao') or exit('Access Invalid!');
class red_interestModel extends Model {

    /**
     * 读取列表年龄
     * @param array $condition
     *
     */
    public function get_array() {
        return $this->table('interest')->select();
    }

    /**
     * 读取单条年龄
     * @param array $condition
     *
     */
    public function getfind($where) {
        return $this->table('interest')->where($where)->find();
    }
    /**
     * 根据id查询一条年龄
     *
     * @param int $id 扩大范围id
     * @return array 一维数组
     */
    public function red_getRow($id){
        return Db::getRow(array('table'=>'interest','field'=>'interest_id','value'=>$id));
    }

    /**
     * 添加年龄
     *
     * @param array $input
     * @return bool
     */
    public function insertadd($input){
        return DB::insert('interest',$input);
    }
    /**
     * 更新年龄
     *
     * @param array $input
     * @param int $id
     * @return bool
     */
    public function updates($input,$id){
        return Db::update('interest',$input,"interest_id='$id' ");
    }

    /**
     * 删除年龄
     *
     * @param string $id
     * @return bool
     */
    public function del($id){
        return Db::delete('interest','interest_id in('.$id.')');
    }
}
