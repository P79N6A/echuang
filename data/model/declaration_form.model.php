<?php
/**
 * e创 报单
 *
 *
 *
 * *  (c) ()
 * @license
 * @link
 * @since
 */
defined('In33hao') or exit('Access Invalid!');

// 等级
define('LEVEL_ONE', 1);
define('LEVEL_TWO', 2);
define('LEVEL_THREE', 3);
define('LEVEL_FOUR', 4);
define('LEVEL_FIVE', 5);
define('LEVEL_SIX', 6);
define('LEVEL_SEVEN', 7);
define('DECLARATION_STATE_ZERO', 0);
define('DECLARATION_STATE_ONE', 1);
define('DECLARATION_STATE_TWO', 2);
define('DECLARATION_STATE_THREE', 3);
define('DECLARATION_STATE_FOUR', 4);

class declaration_formModel extends Model {

    private $level = array(
        LEVEL_ONE => 'invite_one',
        LEVEL_TWO => 'invite_two',
        LEVEL_THREE => 'invite_three',
        LEVEL_FOUR => 'invite_four',
        LEVEL_FIVE => 'invite_five',
        LEVEL_SIX => 'invite_six',
        LEVEL_SEVEN => 'invite_seven',
    );
    public $state = array(
        DECLARATION_STATE_ZERO => '未审核',
        DECLARATION_STATE_ONE => '已开始',
        DECLARATION_STATE_TWO => '已结束',
        DECLARATION_STATE_THREE => '审核未通过',
        DECLARATION_STATE_FOUR => '已出局',
    );

    public function __construct(){
        parent::__construct('declaration_form');
    }

    /**
     * 报单详细信息（查库）
     * @param array $condition
     * @param string $field
     * @return array
     */
    public function getDeclarationFromInfo($condition, $field = '*', $master = false) {
        return $this->table('declaration_form')->field($field)->where($condition)->master($master)->find();
    }

    /**
     * 报单列表
     * @param array $condition
     * @param string $field
     * @param number $page
     * @param string $order
     */
    public function getDeclarationFromList($condition = array(), $field = '*', $page = null, $order = 'id desc', $limit = '') {
        return $this->table('declaration_form')->field($field)->where($condition)->page($page)->order($order)->limit($limit)->select();
    }

    /**
     * 添加报单
     * @param array 报单信息
     * @return bool 返回结果
     */
    public function addDeclarationFrom($param){
        if (empty($param)) {
            return false;
        }
        $id = $this->table('declaration_form')->insert($param);
        if (empty($id)){
            return false;
        }else{
            return $id;
        }
    }

    /**
     * 编辑报单
     * @param array $condition
     * @param array $data
     */
    public function editDeclarationFrom($condition, $data) {
        $update = $this->table('declaration_form')->where($condition)->update($data);
        return $update;
    }

    /**
     * 删除报单
     *
     * @param int $id
     * @return bool 返回结果
     */
    public function delDeclarationFrom($id) {
        if (intval($id) > 0) {
            $where = " id = '" . intval($id) . "'";
            $result = Db::delete('declaration_form', $where);
            return $result;
        } else {
            return false;
        }
    }

    /**
     * 查询会员某个级别的下级个数
     * @param int $member_id 会员id
     * @param int $level 级别
     * @return int 数量
     */
    public function getSubordinateMemberNum($member_id,$level){
        $level_array = $this->level;
        if (empty($level_array[$level])){
            return false;
        }
        $where = array("$level_array[$level]"=>$member_id);
        $field = " count(member_id) ";
        $num = $this->getDeclarationFromInfo($where,$field);
        return empty($num)?0:$num;
    }

    /**
     * 获取需要绑定的上级id号
     * @param int $member_id 会员id
     * @param int $level 级别
     * @param int $recommend_num 直推限制人数
     * @return int 数量
     */
    public function getSubordinateMemberId($member_id,$level,$recommend_num){
        $level_array = $this->level;
        if (empty($level_array[$level])){
            return false;
        }
        $sql = "SELECT member_id FROM red_declaration_form AS d1 WHERE $level_array[$level] = $member_id AND (SELECT COUNT(member_id) FROM red_declaration_form AS d2 WHERE d2.invite_one = d1.member_id) < $recommend_num LIMIT 1";
        $data = Model()->query($sql);
        if (empty($data)){
            return false;
        }else{
            return $data['member_id'];
        }
    }

    /**
     * 获取会员报单信息
     * @param int $member_id 会员id
     * @param int $type  类型 1、获取会员信息；2、获取下级会员信息
     * @return array
     */
    public function getMenberDeclarationFromInfo($member_id,$type = 1){
        if ($type == 1){
            $where = ' member_id = \''.$member_id.'\' and integral_end_time > \''.time().'\' and state = 1 ';
            $data =  $this->table('member_declaration')->where($where)->find();
        }else{
            $where = ' inviter_id = \''.$member_id.'\' and integral_end_time > \''.time().'\' and state = 1 ';
            $data =  $this->table('member_declaration')->field('*,member_name as member_truename')->where($where)->select();
        }
        return $data;
    }

    /**
     * 获取会员列表
     * @return array
     */
    public function getMemberDeclarationList($condition = array(), $fields = '*', $page = null, $order = 'member.member_id desc', $limit = '')
    {
        return $this
            ->table('member,declaration_form')
            ->field($fields)
            ->where($condition)
            ->join('left')
            ->on('member.member_id = declaration_form.member_id')
            ->page($page)
            ->order($order)
            ->limit($limit)
            ->select();
    }

    /**
     * 获取会员报单次数和出局次数
     * @param int $member_id
     * @return array
     */
    public function getOutgoTimes($member_id)
    {
        $times = $this->table('declaration_form')->field('COUNT(id) as declaration_times , COUNT(DISTINCT id,IF(state=4,TRUE,NULL)) as outgo_times')->where(array('member_id'=>$member_id))->find();
        if (empty($times)){
            $times['declaration_times'] = 0;
            $times['outgo_times'] = 0;
        }
        return $times;
    }

    /**
     * 获取会员总积分和预收益积分
     * @param int $type 类型
     * @param int $member_id 类型
     * @return array
     */
    public function getIntegralTotal($member_id = 0,$type = 1)
    {
        if ($type==1){
            $data =  $this->table('member_declaration')->field('SUM(m_integral) as integral_total,SUM(estimate_integral) as estimate_integral_total')->find();
        }else{
            $where = ' member_id = \''.$member_id.'\' ';
            $data =  $this->table('member_declaration')->field('*')->where($where)->find();
        }
        return $data;
    }

    /**
     * 会员积分调整
     * @param int $member_id    用户id
     * @param int $account_type 调整类型  1、积分 2、预期积分
     * @param int $operate_type 增减类型  1、增加 2、减少
     * @param int $amount       调整数量
     * @param int $info         会员信息
     * @param int $remarks      备注
     * @param int $type         类型
     * @param int $invite_id    上下级ID/转赠人ID
     * @return bool
     */
    public function changeMemberIntegral($member_id,$account_type,$operate_type,$amount,$info,$remarks,$type,$invite_id = 0)
    {
        if ($operate_type == 2){
            if ($account_type == 1 && $info['m_integral']<$amount){
                //会员积分不足
                return false;
            }
            elseif($account_type == 2 && $info['estimate_integral']<$amount){
                //会员预期积分不足
                return false;
            }
            $amount = -$amount;
        }
        $info['estimate_integral'] = $info['estimate_integral']?$info['estimate_integral']:0;
        if ($account_type == 1){
            $sql = 'UPDATE red_member set integral = integral + '.$amount.' WHERE member_id = '.$member_id;
            $result = $this->execute($sql);
            $variable_integral = $amount;
            $stable_integral = $info['m_integral']+$amount;
            $variable_estimate_integral = 0;
            $stable_estimate_integral = $info['estimate_integral'];
        }
        elseif ($account_type == 2){
            $sql = 'UPDATE red_declaration_form set estimate_integral = estimate_integral + '.$amount.' WHERE state = 1 AND member_id = '.$member_id;
            $result = $this->execute($sql);
            $variable_integral = 0;
            $stable_integral = $info['m_integral'];
            $variable_estimate_integral = $amount;
            $stable_estimate_integral = $info['estimate_integral']+$amount;
        }
        if ($result){
            $log = array(
                'member_id'=>$member_id,
                'variable_integral'=>$variable_integral,
                'stable_integral'=>$stable_integral,
                'variable_estimate_integral'=>$variable_estimate_integral,
                'stable_estimate_integral'=>$stable_estimate_integral,
                'type'=>$type,
                'add_time'=>time(),
                'invite_id'=>$invite_id,
                'remarks'=>$remarks,
            );
            Model('member_integral_log')->addMemberIntegralLog($log);
        }
        return $result;
    }

}