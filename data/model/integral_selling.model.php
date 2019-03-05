<?php
/**
 * e创 积分挂卖订单
 *
 *
 *
 * *  (c) ()
 * @license
 * @link
 * @since
 */
defined('In33hao') or exit('Access Invalid!');
define('SELL_STATE_ZERO', 0);
define('SELL_STATE_ONE', 1);
define('SELL_STATE_TWO', 2);
define('SELL_STATE_THREE', 3);

class integral_sellingModel extends Model {
    public $state = array(
        SELL_STATE_ZERO => '待审核',
        SELL_STATE_ONE => '挂卖中',
        SELL_STATE_TWO => '已回购',
        SELL_STATE_THREE => '已拒绝',
    );
    public function __construct(){
        parent::__construct('integral_selling');
    }

    /**
     * 积分挂卖订单详细信息（查库）
     * @param array $condition
     * @param string $field
     * @return array
     */
    public function getIntegralSellingInfo($condition, $field = '*', $master = false) {
        return $this->table('integral_selling')->field($field)->where($condition)->master($master)->find();
    }

    /**
     * 取得积分挂卖订单详细信息（优先查询缓存）
     * 如果未找到，则缓存所有字段
     * @param int $id
     * @return array
     */
    public function getIntegralSellingInfoByID($id) {
        $integral_selling_info = rcache($id, 'integral_selling');
        if (empty($integral_selling_info)) {
            $integral_selling_info = $this->getIntegralSellingInfo(array('id' => $id), '*', true);
            wcache($id, $integral_selling_info, 'integral_selling');
        }
        return $integral_selling_info;
    }

    /**
     * 积分挂卖订单列表
     * @param array $condition
     * @param string $field
     * @param number $page
     * @param string $order
     */
    public function getIntegralSellingList($condition = array(), $field = '*', $page = null, $order = 'id desc', $limit = '') {
        return $this->table('integral_selling')->field($field)->where($condition)->page($page)->order($order)->limit($limit)->select();
    }

    /**
     * 添加积分挂卖订单
     * @param array 积分挂卖订单信息
     * @return bool 返回结果
     */
    public function addIntegralSelling($param){
        if (empty($param)) {
            return false;
        }
        $id = $this->table('integral_selling')->insert($param);
        if (empty($id)){
            return false;
        }else{
            return $id;
        }
    }

    /**
     * 编辑积分挂卖订单
     * @param array $condition
     * @param array $data
     */
    public function editIntegralSelling($condition, $data) {
        $update = $this->table('integral_selling')->where($condition)->update($data);
        return $update;
    }

    /**
     * 删除积分挂卖订单
     *
     * @param int $id
     * @return bool 返回结果
     */
    public function delIntegralSelling($id) {
        if (intval($id) > 0) {
            $where = " id = '" . intval($id) . "'";
            $result = Db::delete('integral_selling', $where);
            return $result;
        } else {
            return false;
        }
    }
}