<?php
/**
 * e创 会员积分日志
 *
 *
 *
 * *  (c) ()
 * @license
 * @link
 * @since
 */
defined('In33hao') or exit('Access Invalid!');

define('MEMBER_INTEGRAL_LOG_TYPE_ZERO', 0);
define('MEMBER_INTEGRAL_LOG_TYPE_ONE', 1);
define('MEMBER_INTEGRAL_LOG_TYPE_TWO', 2);
define('MEMBER_INTEGRAL_LOG_TYPE_THREE', 3);
define('MEMBER_INTEGRAL_LOG_TYPE_FOUR', 4);
define('MEMBER_INTEGRAL_LOG_TYPE_FIVE', 5);
define('MEMBER_INTEGRAL_LOG_TYPE_SIX', 6);

class member_integral_logModel extends Model {

    public $state = array(
        MEMBER_INTEGRAL_LOG_TYPE_ZERO => '下级分润',
        MEMBER_INTEGRAL_LOG_TYPE_ONE => '绩效扣除',
        MEMBER_INTEGRAL_LOG_TYPE_TWO => '兑换商品',
        MEMBER_INTEGRAL_LOG_TYPE_THREE => '市场挂卖',
        MEMBER_INTEGRAL_LOG_TYPE_FOUR => '市场购入',
        MEMBER_INTEGRAL_LOG_TYPE_FIVE => '赠送好友',
        MEMBER_INTEGRAL_LOG_TYPE_SIX => '好友赠送',
    );

    public function __construct(){
        parent::__construct('member_integral_log');
    }

    /**
     * 日志详情
     * @param array $condition
     * @param string $field
     * @return array
     */
    public function getMemberIntegralLogInfo($condition, $field = '*', $master = false) {
        return $this->table('member_integral_log')->field($field)->where($condition)->master($master)->find();
    }

    /**
     * 日志列表
     * @param array $condition
     * @param string $field
     * @param number $page
     * @param string $order
     */
    public function getMemberIntegralLogList($condition = array(), $field = '*', $page = null, $order = 'id desc', $limit = '') {
        return $this->table('member_integral_log')->field($field)->where($condition)->page($page)->order($order)->limit($limit)->select();
    }

    /**
     * 添加日志
     * @param array 日志信息
     * @return bool 返回结果
     */
    public function addMemberIntegralLog($param){
        if (empty($param)) {
            return false;
        }
        $id = $this->table('member_integral_log')->insert($param);
        if (empty($id)){
            return false;
        }else{
            return $id;
        }
    }

    /**
     * 编辑日志
     * @param array $condition
     * @param array $data
     */
    public function editMemberIntegralLog($condition, $data) {
        $update = $this->table('member_integral_log')->where($condition)->update($data);
        return $update;
    }

    /**
     * 删除日志
     *
     * @param int $id
     * @return bool 返回结果
     */
    public function delMemberIntegralLog($id) {
        if (intval($id) > 0) {
            $where = " id = '" . intval($id) . "'";
            $result = Db::delete('member_integral_log', $where);
            return $result;
        } else {
            return false;
        }
    }
}