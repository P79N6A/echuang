<?php
/**
 * 抢红包
 *
 *
 *
 * *  (c) ()
 * @license
 * @link
 * @since
 */
defined('In33hao') or exit('Access Invalid!');
class red_redbag_manageModel extends Model {

    /**
     * 读取列表抢红包
     * @param array $condition
     *
     */
    public function getPdRechargeList($condition = array(), $pagesize = '', $fields = '*', $limit = ''){
        return $this->table('management_list')->where($condition)->field($fields)->limit($limit)->page($pagesize)->select();
    }
    /**
    * 抢红包add
    */
    public function redbayadd($data){
       return  $this->table('red_envelopes')->insert($data);
        
    }

    /**
     * 读取抢红包
     * @param array $condition
     *
     */
    public function getfind($where){
        return $this->table('red_envelopes')->where($where)->find();
    }
}
