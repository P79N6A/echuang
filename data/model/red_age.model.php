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
class red_ageModel extends Model {

    /**
     * 读取列表年龄
     * @param array $condition
     *
     */
    public function get_array() {
        return $this->table('age')->select();
    }

    /**
     * 读取单条年龄
     * @param array $condition
     *
     */
    public function getfind($where) {
        return $this->table('age')->where($where)->find();
    }
    /**
     * 根据id查询一条年龄
     *
     * @param int $id 扩大范围id
     * @return array 一维数组
     */
    public function red_getRow($id){
        return Db::getRow(array('table'=>'age','field'=>'age_id','value'=>$id));
    }

    /**
     * 添加年龄
     *
     * @param array $input
     * @return bool
     */
    public function insertadd($input){
        return DB::insert('age',$input);
    }
    /**
     * 更新年龄
     *
     * @param array $input
     * @param int $id
     * @return bool
     */
    public function updates($input,$id){
        return Db::update('age',$input,"age_id='$id' ");
    }

    /**
     * 删除年龄
     *
     * @param string $id
     * @return bool
     */
    public function del($id){
        return Db::delete('age','age_id in('.$id.')');
    }
}
