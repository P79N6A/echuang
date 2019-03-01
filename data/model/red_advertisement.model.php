<?php
/**
 * 广告
 *
 *
 *
 * *  (c) ()
 * @license
 * @link
 * @since
 */
defined('In33hao') or exit('Access Invalid!');
class red_advertisementModel extends Model {

    /**
     * 读取列表广告
     * @param array $condition
     *
     */
    public function getPdRechargeList($condition = array(), $pagesize = '', $fields = '*', $order = '', $limit = '') {
        return $this->table('advertisement_business')->where($condition)->field($fields)->order($order)->limit($limit)->page($pagesize)->select();
    }

    /**
     * 读取单条广告
     * @param array $condition
     *
     */
    public function getfind($where) {
        return $this->table('advertisement')->where($where)->find();
    }

    /**
     * 读取单条广告
     * @param array $condition
     *
     */
    public function advetisementgetfind($where) {
        return $this->table('advertisement_business')->where($where)->find();
    }
    /**
     * 根据id查询一条广告
     *
     * @param int $id 扩大范围id
     * @return array 一维数组
     */
    public function red_getRow($id){
        return Db::getRow(array('table'=>'advertisement','field'=>'red_advertisement_id','value'=>$id));
    }

    /**
     * 添加广告
     *
     * @param array $input
     * @return bool
     */
    public function insertadd($input){
        return DB::insert('advertisement',$input);
    }
    /**
     * 更新广告
     *
     * @param array $input
     * @param int $id
     * @return bool
     */
    public function updates($input,$id){
        return Db::update('advertisement',$input,"red_advertisement_id='$id' ");
    }

    /**
     * 删除广告
     *
     * @param string $id
     * @return bool
     */
    public function del($id){
        return Db::delete('advertisement','red_advertisement_id in('.$id.')');
    }

    /*
    * 使用会员ID查询会员
    */
    public function getMemberInfoByID($id){
        return $this->table('city_member')->where($id)->find();
    }
}
