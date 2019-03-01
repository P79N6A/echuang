<?php
/**
 * 家族中心
 */
defined('In33hao') or exit('Access Invalid!');
class member_familyControl extends apiEncryptMemberControl {
    public function __construct() {
        parent::__construct();
    }



    /**
     * 我的直推、间推人数
     */
    public function member_teamOp(){
        //直推人数
        $model_member_extend = Model('member_extend');
        $d_invite_list = $model_member_extend->getMemberExtendList(array('invite_id' => $this->member_info['member_id']), 'member_id', 'union');
        $data['d_invite_num'] = (string) count($d_invite_list);
        //间接推荐人数
        $all_child_level_arr = $model_member_extend->getAllChildLevelArr($this->member_info['member_id']);
        array_shift($all_child_level_arr);
        for ($i=0;$i<=count($all_child_level_arr);$i++) {
            if (!isset($all_child_level_arr[$i])) {
                unset($all_child_level_arr[$i]);
            }
        }
//        var_dump($all_child_level_arr,count($all_child_level_arr));
        $data['ind_invite_num'] = (string) (count($all_child_level_arr) - $data['d_invite_num']);
        responseApiJson(1,'',$data);
    }

    /**
     * 我的直推各等级人数
     */
    public function direct_level_numOp()
    {
        //直推各等级人数
        $model_member_extend = Model('member_extend');
        $d_child_level_arr = $model_member_extend->getMemberExtendList(array('invite_id' => $this->member_info['member_id']), 'member_level', 'union');
        $direct_level_num = array_count_values(array_column($d_child_level_arr, 'member_level'));
        ksort($direct_level_num);
        $direct_level_num2 = array();
        for ($i=0;$i<=5;$i++) {
            if (!isset($direct_level_num[$i])){
                $direct_level_num[$i] = 0;
            }
            $direct_level_num2[str_replace(array(0, 1, 2, 3, 4, 5), array("体验用户", "VIP", "店主", "合伙人", "高级合伙人", "战略合伙人"), $i)] = (string)$direct_level_num[$i];
        }
        responseApiJson(1,'',$direct_level_num2);
    }


    /**
     * 我的间推各等级人数
     */
    public function in_direct_level_numOp(){
        $model_member_extend = Model('member_extend');
        //直推
        $d_child_level_arr = $model_member_extend->getMemberExtendList(array('invite_id' => $this->member_info['member_id']), 'member_level', 'union');
        $direct_level_num = array_count_values(array_column($d_child_level_arr, 'member_level'));
        for ($i=0;$i<=5;$i++) {
            if (!isset($direct_level_num[$i])) {
                $direct_level_num[$i] = 0;
            }
        }
        ksort($direct_level_num);
        //全部
        $child_level_arr = $model_member_extend->getAllChildLevelArr($this->member_info['member_id']);
        array_shift($child_level_arr);
        $all_direct_level_num = @array_count_values($child_level_arr);
        for ($i=0;$i<=5;$i++) {
            if (!isset($all_direct_level_num[$i])) {
                unset($all_direct_level_num[$i]);
            }
        }
        ksort($all_direct_level_num);
        //间推各等级人数
        for ($i=0;$i<=5;$i++){
            $in_direct_level_num[$i] = $all_direct_level_num[$i] - $direct_level_num[$i];
        }
        ksort($in_direct_level_num);
        $in_direct_level_num2 = array();
        foreach ($in_direct_level_num as $k=>$v){
            $in_direct_level_num2[str_replace(array(0,1,2,3,4,5),array("体验用户","VIP","店主","合伙人","高级合伙人","战略合伙人"),$k)] = (string)$v;
        }
        responseApiJson(1,'',$in_direct_level_num2);
    }

    /**
     * member_direct_listOp 我的直推列表
     * @return [type] [description]
     */
    public function member_direct_listOp() {
        $level = $_GET['level'];
        $direct_list = array();
        $member_id = $this->member_info['member_id'];
        $model_member_extend = Model('member_extend');
        $fields = 'member_id,member_name,member_mobile,member_time,member_avatar';
        $invite_list = $model_member_extend->getMemberExtendList(array('invite_id' => $member_id,'member_level'=>$level), $fields, 'union');
        $total_num = $model_member_extend->gettotalpage();
        foreach ($invite_list as $v) {
            $param = array();
            $param['id'] = $v['member_id'];
            $param['name'] = $v['member_name'];
            $param['mobile'] = $v['member_mobile'];
            $param['time'] = $v['member_time'] ? date('Y-m-d H:i:s', $v['member_time']) : '';
            $param['avatar'] = getMemberAvatar($v['member_avatar']);
            $param['level'] = str_replace(array(0,1,2,3,4,5),array("体验用户","VIP","店主","合伙人","高级合伙人","战略合伙人"),$level);
            $direct_list[] = $param;
        }
        responseApiJson(1, '', array('list' => $direct_list, 'total_num' => $total_num));
    }

    /**
     * 我的间推列表
     */
    public function member_in_direct_listOp(){
        $level = $_GET['level'];
        $model_member_extend = Model('member_extend');
        $member_id = $this->member_info['member_id'];
        $all_child_id = $model_member_extend->getInviteChildInfoArr($member_id);
        array_shift($all_child_id);
        $invite_list = array_column($model_member_extend->getMemberExtendList(array('invite_id' => $member_id,'member_level'=>$level), 'member_id', 'union'),'member_id');
        $in_direct_id = array_diff($all_child_id,$invite_list);
        $child_info = $model_member_extend->getMemberExtendList2(array('member_id'=>['in',$in_direct_id],'member_level'=>$level),'member_name,member_mobile,from_unixtime(member_time) as add_time,member_avatar');

        $param = array();
        foreach ($child_info as $k=>$v){
            $param[$k]['name'] = $v['member_name'];
            $param[$k]['mobile'] = $v['member_mobile'];
            $param[$k]['time'] = $v['add_time'];
            $param[$k]['avatar'] = BASE_SITE_URL.'/data/upload/shop/avatar/'.$v['member_avatar'];
            $param[$k]['level'] = str_replace(array(0,1,2,3,4,5),array("体验用户","VIP","店主","合伙人","高级合伙人","战略合伙人"),$level);
        }
        $total_num = $model_member_extend->gettotalpage();
        responseApiJson(1,'',array('list' => $param, 'total_num' => $total_num));
    }

}