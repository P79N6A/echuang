<?php
/**
 * 店门
 *
 *
 *
 * *  (c) ()
 * @license
 * @link
 * @since
 */
defined('In33hao') or exit('Access Invalid!');
class red_businessModel extends Model {


    /*
    *读取列表发红包
    *
    */
    public function getPdRechargeList($condition = array(), $pagesize = '', $fields = '*', $limit = ''){
        return $this->table('business_member')->where($condition)->field($fields)->limit($limit)->page($pagesize)->select();
    }
    /**
     * 读取列表店门
     * @param array $condition
     *
     */
    public function getbusinessselect() {
        return $this->table('business')->select();
    }

    /**
     * 读取单条店门
     * @param array $condition
     *
     */
    public function getbusinesssfind($where) {
        return $this->table('business')->where($where)->find();
    }

    /**
     * 读取单条店门详细信息
     * @param array $condition
     *
     */
    public function get_businesss_find($where) {
        return $this->table('business_member')->where($where)->find();
    }
    /**
     * 根据id查询一条店门
     *
     * @param int $id 店门id
     * @return array 一维数组
     */
    public function businesssgetRow($id){
        return Db::getRow(array('table'=>'business','field'=>'business_id','value'=>$id));
    }

    /**
     * 添加店门
     *
     * @param array $input
     * @return bool
     */
    public function businesssadd($input){
        return DB::insert('business',$input);
    }
    /**
     * 更新店门
     *
     * @param array $input
     * @param int $id
     * @return bool
     */
    public function businesssupdates($input,$id){
        return Db::update('business',$input,"business_id='$id' ");
    }

    /**
     * 删除店门
     *
     * @param string $id
     * @return bool
     */
    public function del($id){
        return Db::delete('business','business_id in('.$id.')');
    }
}
