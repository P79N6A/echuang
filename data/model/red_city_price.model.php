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
class red_city_priceModel extends Model {

    /**
     * 读取列表发红包
     * @param array $condition
     *
     */
    public function getPdRechargeList($condition = array(), $pagesize = '', $fields = '*', $order = '', $limit = '') {
        return $this->table('price_area')->where($condition)->field($fields)->order($order)->limit($limit)->page($pagesize)->select();
    }

    /*
     * 查询发红包的订单号
     * 返回订单信息
     */
    public function envelopesget($where=''){
        return $this->table('price_area')->where($where)->find();
    }

    /*
     * 删除单条原价城主
     */
    public function del($id){
        return Db::delete('city_price','city_price_id in('.$id.')');
    }

   /*
    * 更改状态
    */
   public function stateupdate($where,$data){
       return $this->table('city_price')->where($where)->update($data);
   }
    /*
    * 原价城主add
    */
    public function redbayadd($data){
      return  $this->table('city_price')->insert($data);
    }

    /*
    * 城主竞拍订单添加
    */
    public function lordadd($data){
      return  $this->table('rob_city_lord')->insert($data);
    }

    /*
    * 抢城主记录
    */
    public function citylog($where){
      return  $this->table('city_logOp')->where($where)->select();
    }
}
