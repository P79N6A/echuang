<?php
/**
 * 我的商城
 *
 *  (c) ()
 * @license
 * @link
 * @since
 */

defined('In33hao') or exit('Access Invalid!');

class member_friendControl extends mobileMemberControl
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 好友列表
     */
    public function indexOp()
    {
        $order = 'add_time desc';
        $page = empty($_REQUEST['rp'])?10:intval($_REQUEST['rp']);
        $model_member_friend = Model('friend');
        $member_id = $this->member_info['member_id'];
        $data = array();
        $where = ' member_id = \''.$member_id.'\' OR friend_member_id = \''.$member_id.'\' ';
        $flied = ' *,IF(member_id = '.$member_id.',friend_member_id,member_id) as friend_id ';
        $list = $model_member_friend->getFriendList($where,$flied,$page,$order);
        $data['now_page'] = $model_member_friend->shownowpage();
        $data['total_num'] = $model_member_friend->gettotalnum();
        if (empty($list)){
            $data['list'] = array();
        }
        else{
            foreach($list as $k=>$v)
            {
                $v['friend_avatar'] = getMemberAvatarForID($v['friend_id']);
                $v['friend_name'] = Model('member')->getMemberInfo(array('member_id'=>$v['friend_id']),'member_name')['member_name'];
                $data['list'][$v['id']] = $v;
            }
        }
        output_data($data);
    }

    /**
     * 好友申请列表
     */
    public function friend_applicationOp()
    {
        $order = 'add_time desc';
        $page = empty($_REQUEST['rp'])?10:intval($_REQUEST['rp']);
        $model_member_friend = Model('friend');
        $member_id = $this->member_info['member_id'];
        $data = array();
        $where = ' friend_member_id = \''.$member_id.'\' AND state in (0,1) ';
        $flied = ' *,IF(member_id = '.$member_id.',friend_member_id,member_id) as friend_id ';
        $list = $model_member_friend->getFriendList($where,$flied,$page,$order);
        $data['now_page'] = $model_member_friend->shownowpage();
        $data['total_num'] = $model_member_friend->gettotalnum();
        if (empty($list)){
            $data['list'] = array();
        }
        else{
            foreach($list as $k=>$v)
            {
                $v['friend_avatar'] = getMemberAvatarForID($v['member_id']);
                $v['friend_name'] = Model('member')->getMemberInfo(array('member_id'=>$v['member_id']),'member_name')['member_name'];
                $v['state'] = str_replace(array(0,1), array('未同意', '已同意'), $v['state']);
                $data['list'][$v['id']] = $v;
            }
        }
        output_data($data);
    }

    /**
     * 申请添加好友
     */
    public function add_friendOp()
    {
        $id = empty($_REQUEST['id'])?0:intval($_REQUEST['id']);
        $result = Logic('friend')->addFriend($this->member_info['member_id'],$id);
        if (empty($result['error'])){
            output_data('添加成功');
        }else{
            output_error($result['error']);
        }
    }

    /**
     * 同意好友申请
     */
    public function agree_friendOp()
    {
        $id = empty($_REQUEST['id'])?0:intval($_REQUEST['id']);
        $result = Model('friend')->editFriend(array('id'=>$id),array('state'=>1));
        if ($result){
            output_data('同意成功');
        }else{
            output_error('同意失败');
        }
    }

    /**
     * 好友转赠积分
     */
    public function give_friend_integralOp()
    {
        $friend_id = empty($_REQUEST['friend_id'])?0:intval($_REQUEST['friend_id']);
        $integral = empty($_REQUEST['integral'])?0:intval($_REQUEST['integral']);
        $remarks = empty($_REQUEST['remarks'])?'':trim($_REQUEST['remarks']);
        $result = Logic('friend')->giveFriendIngtegral($this->member_info['member_id'],$friend_id,$integral,$remarks);
        if (empty($result['error'])){
            output_data('赠送成功');
        }else{
            output_data($result['error']);
        }
    }

}
