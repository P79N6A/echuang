<?php
/**
 * e创 好友关系
 *
 *
 *
 * *  (c) ()
 * @license
 * @link
 * @since
 */
defined('In33hao') or exit('Access Invalid!');

class friendModel extends Model {
    public function __construct(){
        parent::__construct('friend');
    }

    /**
     *  好友关系信息（查库）
     * @param array $condition
     * @param string $field
     * @return array
     */
    public function getFriendInfo($condition, $field = '*', $master = false) {
        return $this->table('friend')->field($field)->where($condition)->master($master)->find();
    }

    /**
     *  好友列表
     * @param array $condition
     * @param string $field
     * @param number $page
     * @param string $order
     */
    public function getFriendList($condition = array(), $field = '*', $page = null, $order = 'id desc', $limit = '') {
        return $this->table('friend')->field($field)->where($condition)->page($page)->order($order)->limit($limit)->select();
    }

    /**
     * 添加好友
     * @param array 好友信息
     * @return bool 返回结果
     */
    public function addFriend($param){
        if (empty($param)) {
            return false;
        }
        $id = $this->table('friend')->insert($param);
        if (empty($id)){
            return false;
        }else{
            return $id;
        }
    }

    /**
     * 编辑好友
     * @param array $condition
     * @param array $data
     */
    public function editFriend($condition, $data) {
        $update = $this->table('friend')->where($condition)->update($data);
        return $update;
    }

    /**
     * 删除好友
     *
     * @param int $id
     * @return bool 返回结果
     */
    public function delFriend($id) {
        if (intval($id) > 0) {
            $where = " id = '" . intval($id) . "'";
            $result = Db::delete('friend', $where);
            return $result;
        } else {
            return false;
        }
    }

    /**
     * 字段自增
     * @param string $field 字段名
     * @param string $where 条件
     * @param string $num   增加数量
     * @return bool
     */
    public function incrementField($field,$where,$num)
    {
        $sql = 'UPDATE red_friend SET '.$field.' = '.$field.' + '.$num.' WHERE '.$where;
        return $this->query($sql);
    }
}