<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/30 0030
 * Time: 下午 15:35
 */

class crontabControl
{
    public function StrategicPartnerBonusOp()
    {
        //获得当日凌晨的时间戳
        date_default_timezone_set('PRC');
        $start_time = strtotime(date("Y-m-d"), time());
        $end_time = $start_time + 60 * 60 * 24;
        $strategic_partner_bonus = (Model('member_profit')->getStrategicPartnerBonusAmount(['cl_addtime' => ['between', [$start_time, $end_time]]])) * 0.05;
        $strategic_partner_member_arr = Model()->table('member_extend,member')->where(array('member_extend.member_level' => 5))->join('union')->on('member.member_id = member_extend.me_member_id')->select();
        //var_dump($strategic_partner_member_arr);
        $average_bonus_amount = $strategic_partner_bonus / count($strategic_partner_member_arr);
        foreach ($strategic_partner_member_arr as $k => $v) {
            $data['member_id'] = $v['member_id'];
            $data['member_name'] = $v['member_name'];
            $data['amount'] = $average_bonus_amount;
            $data['content'] = "战略合伙人" . $v['member_mobile'] . "获得平台分红" . $average_bonus_amount;
            $result = Model('member_extend')->changeMemberBalance('bonus', $data);
        }
        if ($result){
            echo 111;
            echo "<br>";
        }
    }
}