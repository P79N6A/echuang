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
     * @return array
     */
    public function getMenberDeclarationFromInfo($member_id){
        $where = ' member_id = \''.$member_id.'\' and integral_end_time > \''.time().'\' and state = 0 ';
        $data =  $this->table('member_declaration')->where($where)->find();

        return $data;
    }

}