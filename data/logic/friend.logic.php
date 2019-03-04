<?php
/**
 * e创好友
 */
defined('In33hao') or exit('Access Invild!');

class friendLogic
{
    /**
     * 申请添加好友
     * @param int $member_id 会员id
     * @param int $friend_member_id 好友id
     * @param int $state 状态
     * @return bool
     */
    public function addFriend($member_id,$friend_member_id,$state=1)
    {
        $model_friend = Model('friend');
        $is_friend = $this->judgeFriend($member_id,$friend_member_id);
        if ($is_friend != 3){
            $param = array(
                'member_id'=>$member_id,
                'friend_member_id'=>$friend_member_id,
                'add_time'=>time(),
                'state'=>$state,
            );
            return $model_friend->addFriend($param);
        }else{
            return false;
        }
    }

    /**
     * 好友转赠
     * @param int $member_id 会员id
     * @param int $friend_member_id 好友id
     * @param float $integral 转赠积分
     * @return bool
     */
    public function giveFriendIngtegral($member_id,$friend_member_id,$integral)
    {
        $model_friend = Model('friend');
        $model_declaration_form = Model('declaration_form');
        $is_friend = $this->judgeFriend($member_id,$friend_member_id);
        if ($is_friend == 1){
            $filed_name = 'give_integral';
        }
        elseif($is_friend == 2){
            $filed_name = 'donate_integral';
        }
        else{
            return false;
        }
        $info1 = $model_declaration_form->getIntegralTotal($member_id,2);
        $result1 = $model_declaration_form->changeMemberIntegral($member_id,1,2,$integral,$info1,'',5);
        $info2 = $model_declaration_form->getIntegralTotal($friend_member_id,2);
        $result2 = $model_declaration_form->changeMemberIntegral($friend_member_id,1,1,$integral,$info2,'',6);

        if ($result1 && $result2){
            //修改好友
        }

    }

    /**
     * 判断好友关系
     * @param int $member_id 会员id
     * @param int $friend_member_id 好友id
     * @return int $result 1、1添加2为好友的；2、2添加1为好友的；3、未添加好友；
     */
    public function judgeFriend($member_id,$friend_member_id)
    {
        $model_friend = Model('friend');
        $where = ' (member_id = \''.$member_id.'\' AND friend_member_id = \''.$friend_member_id.'\')  ';
        $is_friend_one = $model_friend->getFriendInfo($where);
        if ($is_friend_one){
            $result = 1;
        }else{
            $where = ' (friend_member_id = \''.$member_id.'\' AND member_id = \''.$friend_member_id.'\')  ';
            $is_friend_two = $model_friend->getFriendInfo($where);
            if ($is_friend_two){
                $result = 2;
            }else{
                $result = 3;
            }
        }

        return $result;
    }
}