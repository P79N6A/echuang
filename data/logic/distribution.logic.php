<?php
/**
 * e创分销
 */
defined('In33hao') or exit('Access Invild!');

class distributionLogic
{
    /*
     * 获取上七级推荐人id
     *
     * @param int $member_id
     * @param int $type
     * @return array
     */
    public function getInviteId($member_id,$type=1)
    {
        $declaration_from_model = Model('declaration_from');
        $where = array('member_id'=>$member_id);
        $filed = 'invite_one,invite_two,invite_three,invite_four,invite_five,invite_six,invite_seven';
        $member_intergral = $declaration_from_model->getDeclarationFromInfo($where,$filed);
        if ($type == 1){
            $data = $member_intergral;
        }
        elseif ($type == 2){
            $data = array(
                $member_intergral['invite_one'],
                $member_intergral['invite_two'],
                $member_intergral['invite_three'],
                $member_intergral['invite_four'],
                $member_intergral['invite_five'],
                $member_intergral['invite_six'],
                $member_intergral['invite_seven']
                );
        }
        return $data;
    }

    /*
     * 关系绑定(添加报单)
     *
     * @param int $member_id 会员id
     * @param int $inviteid 推荐人id
     * @param int $level 推荐人所在级别
     * @param int $pay_id 支付表ID
     * @return bool
     */
    public function setInviteId($member_id,$inviteid,$level,$pay_id)
    {
        $declaration_from_model = Model('declaration_from');
        $setting_model = Model('setting');
        $superior_info = $declaration_from_model->getDeclarationFromInfo(' member_id = '.$inviteid.' and integral_start_time > '.time());
        if (empty($superior_info)){
            //推荐人没购买报单或以过期
            return false;
        }
        //获取后台设置
        $setting = $setting_model->getListSetting();

        //获取绑定的上级id
        $inviteid_one = $declaration_from_model->getSubordinateMemberId($member_id,$level,$setting['f_recommend_num']);
        $inviteid_array = $this->getInviteId($inviteid_one);
        $time = time();
        $data = array(
            'member_id'=>$member_id,
            'inviter_id'=>$inviteid,
            'invite_one'=>$inviteid_one,
            'invite_two'=>$inviteid_array['invite_one'],
            'invite_three'=>$inviteid_array['invite_two'],
            'invite_four'=>$inviteid_array['invite_three'],
            'invite_five'=>$inviteid_array['invite_four'],
            'invite_six'=>$inviteid_array['invite_five'],
            'invite_seven'=>$inviteid_array['invite_six'],
            'integral'=>0,
            'estimate_integral'=>$this->getEstimateIntegral(),
            //'integral_start_time'=>$time,
            //'integral_end_time'=>$time+$setting['f_declaration_time']*86400,
            'cycle_time'=>$setting['f_cycle_time'],
            'deduction_integral'=>$setting['f_deduction_integral'],
            'superior_deduction_integral'=>$setting['f_superior_deduction_integral'],
            'declaration_time'=>$setting['f_declaration_time'],
            'state'=>0,
            'order_sn'=>$this->makeOrderSn($pay_id),
            'add_time'=>$time,
        );
        return $declaration_from_model->addDeclarationFrom($data);
    }

    /**
     * 分润
     * 添加本人和上级总共8人的会员积分，扣除报单的预收益积分，添加会员积分日志
     * @param array $declaration_info 报单信息
     *
     */
    public function implementDistribution($declaration_info){
        $model_declaration_form = Model('declaration_form');
        $setting = Model('setting')->getListSetting();
        $distribution_integral = $setting['f_reward_integral'];
        $level = $model_declaration_form->level;
        for($i=0;$i<8;$i++)
        {
            $type = 0;
            if ($i == 0){
                $level[$i] = 'member_id';
                $type = 8;
            }
            if (!empty($declaration_info[$level[$i]])){
                $sup_menber_declaration_info = $model_declaration_form->getMenberDeclarationFromInfo($declaration_info[$level[$i]]);
                if (!empty($sup_menber_declaration_info) && $sup_menber_declaration_info['estimate_integral'] > $distribution_integral){
                    $m_increase_integral = $distribution_integral+$sup_menber_declaration_info['m_integral'];
                    $d_increase_integral = $distribution_integral+$sup_menber_declaration_info['d_integral'];
                    $estimate_integral = $sup_menber_declaration_info['estimate_integral']-$distribution_integral;
                    Model('member')->editMember(array('member_id'=>$sup_menber_declaration_info['member_id']), array('integral'=>$m_increase_integral));
                    Model('declaration_form')->editDeclarationFrom(array('id'=>$sup_menber_declaration_info['id']),array('integral'=>$d_increase_integral,'estimate_integral'=>$estimate_integral));
                    $log = array(
                        'member_id'=>$sup_menber_declaration_info['member_id'],
                        'variable_integral'=>$distribution_integral,
                        'stable_integral'=>$m_increase_integral,
                        'variable_estimate_integral'=>-$distribution_integral,
                        'stable_estimate_integral'=>$estimate_integral,
                        'type'=>$type,
                        'add_time'=>time(),
                        'invite_id'=>$declaration_info['member_id']
                    );
                    Model('member_integral_log')->addMemberIntegralLog($log);
                }
            }
        }
    }

    /**
     * 绩效未达标扣除积分
     * 扣除本人和上级总共8人的预收益积分，添加积分日志
     * @param array $declaration_info 报单信息
     *
     */
    public function implementAchievementsAppraisal($declaration_info){
        $model_declaration_form = Model('declaration_form');
        $setting = Model('setting')->getListSetting();
        $deduction_integral = $setting['f_deduction_integral']?1000:$setting['f_deduction_integral'];
        $superior_deduction_integral = $setting['f_superior_deduction_integral']?500:$setting['f_superior_deduction_integral'];
        $level = $model_declaration_form->level;
        for($i=0;$i<8;$i++)
        {
            if ($i == 0){
                $level[$i] = 'member_id';
                $integral = $deduction_integral;
                $type = 1;
            }else{
                $integral = $superior_deduction_integral;
                $type = 7;
            }
            if (!empty($declaration_info[$level[$i]])){
                $sup_menber_declaration_info = $model_declaration_form->getMenberDeclarationFromInfo($declaration_info[$level[$i]]);
                if (!empty($sup_menber_declaration_info) && $sup_menber_declaration_info['estimate_integral'] > $integral){
                    $estimate_integral = $sup_menber_declaration_info['estimate_integral']-$integral;
                    Model('declaration_form')->editDeclarationFrom(array('id'=>$sup_menber_declaration_info['id']),array('estimate_integral'=>$estimate_integral));
                    $log = array(
                        'member_id'=>$sup_menber_declaration_info['member_id'],
                        'variable_integral'=>0,
                        'stable_integral'=>$sup_menber_declaration_info['m_integral'],
                        'variable_estimate_integral'=>-$integral,
                        'stable_estimate_integral'=>$estimate_integral,
                        'type'=>$type,
                        'add_time'=>time(),
                        'invite_id'=>$declaration_info['member_id']
                    );
                    Model('member_integral_log')->addMemberIntegralLog($log);
                }
            }
        }
    }

    /**
     * 会员绩效考核
     * 判断会员绩效是否达标，未达标将扣除预收益积分
     *
     * @return bool
     */
    public function achievementsAppraisal($member_id){
        $model_declaration_from = Model('declaration_from');
        $member_declaration_from_info = $model_declaration_from->getMenberDeclarationFromInfo($member_id);
        if (!empty($member_declaration_from_info)){
            $member_achievements = $this->judgeMemberAchievements($member_declaration_from_info);
            if (!$member_achievements){
                $declaration_info = $model_declaration_from->getDeclarationFromInfo($member_id);
                Model::beginTransaction();
                try {
                    return $this->implementAchievementsAppraisal($declaration_info);
                    Model::commit();
                }catch (Exception $e){
                    Model::rollback();
                }
            }
        }
    }

    /**
     * 判断会员绩效是否达标
     * @param array $member_declaration_from_info 会员报单信息
     * @return bool
     */
    public function judgeMemberAchievements($member_declaration_from_info){
        $level = $this->getMemberLevel($member_declaration_from_info['member_id']);
        $time = time();
        $start_time = $member_declaration_from_info['integral_start_time'];
        $cycle_time = $member_declaration_from_info['cycle_time']*86400;
        $achievements_level = ceil(($time-$start_time)/$cycle_time);

        if ($level<$achievements_level){
            return false;
        }else{
            return true;
        }
    }

    /**
     * 判断会员目前下级所处的阶段
     * @param int $member_id
     * @return int
     */
    public function getMemberLevel($member_id)
    {
        $declaration_from_model = Model('declaration_from');
        $AchievementsSetting = $this->getAchievementsSetting();

        $level = 0;
        foreach($AchievementsSetting as $key=>$val)
        {
            $subordinate_num = $declaration_from_model->getSubordinateMemberNum($member_id,$key);
            if ($subordinate_num<$val){
                $level = $val;
            }
            if ($level == 0 && $key == 7){
                $level = 8;
            }
        }

        return $level;
    }

    /**
     * 获取绩效标准
     *
     * @return array
     */
    public function getAchievementsSetting(){
        $setting_model = Model('setting');
        $setting = $setting_model->getListSetting();
        $recommend_num = empty($setting['f_recommend_num'])?3:$setting['f_recommend_num'];//直推限制人数
        $data = array('1'=>$recommend_num);
        for($i=1;$i<7;$i++)
        {
            $data[$i+1] = $data[$i]*$recommend_num;
        }
        return $data;
    }

    /**
     * 获取预期收益积分
     *
     * @return int
     */
    public function getEstimateIntegral(){
        $setting_model = Model('setting');
        $setting = $setting_model->getListSetting();
//        $recommend_num = empty($setting['f_recommend_num'])?3:$setting['f_recommend_num'];//直推限制人数
//        $reward_integral = empty($setting['f_reward_integral'])?20:$setting['f_reward_integral'];//直推限制人数
//        $data = array('1'=>$recommend_num);
//        $num = $recommend_num;
//        for($i=1;$i<7;$i++)
//        {
//            $data[$i+1] = $data[$i]*$recommend_num;
//            $num += $data[$i+1];
//        }
//        $estimate_integral = ($num+1) * $reward_integral;
        $estimate_integral = empty($setting['f_estimate_integral'])?65600:$setting['f_estimate_integral'];
        return $estimate_integral;
    }

    /**
     * 订单编号生成规则，n(n>=1)个订单表对应一个支付表，
     * 生成订单编号(年取1位 + $pay_id取13位 + 第N个子订单取2位)
     * 1000个会员同一微秒提订单，重复机率为1/100
     * @param int $pay_id 支付表自增ID
     * @return string
     */
    public function makeOrderSn($pay_id)
    {
        //记录生成子订单的个数，如果生成多个子订单，该值会累加
        static $num;
        if (empty($num)) {
            $num = 1;
        } else {
            $num++;
        }
        return (date('y', time()) % 9 + 1) . sprintf('%013d', $pay_id) . sprintf('%02d', $num);
    }
}