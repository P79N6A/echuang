<?php
/**
 * 会员升级逻辑
 * User: Dennis
 * Date: 2018/10/11
 * Time: 下午 13:56
 */
defined('In33hao') or exit('Access Invalid!');
class member_levelLogic{

    private  $up_level_need_arr;
    private  $inventory_info;
    private $level_config = [
        1=>"child_vip_num",
        2=>"child_shopkeeper_num",
        3=>"child_partner_num",
        4=>"child_senior_partner_num",
        5=>"child_strategic_partner_num",
    ];

    public function __construct(){
        $this->_getInventoryInfo();
    }

    private function _getInventoryInfo(){
        $this->inventory_info = Model('member_system_set')->getInventoryInfo();
    }

    /**
     * 购买报单产品
     * @return multitype
     */
    public function buyReportProduct($member_id,$pay_sn){
        $member_info = Model('member_extend')->getMemberExtendInfo(array('member_id'=>$member_id),'*','union');
        $member_level = $member_info['member_level'];
        if ($member_level == 0){
//            var_dump(123);
            //会员升级
            $up_level = $this->UpdateMemberLevel($member_id,1);
            if ($up_level['state'] == false){
                return callback(false,'会员升级失败');
            }
            //上级级差分润（报单）
            $diffProfit =Logic('profit')->diffProfit($member_id,$pay_sn);
            if (!$diffProfit){
                return callback(false,'上级分润失败');
            }
            //上级团队人数变更,并判断所有上级是否升级
            $change_team_num = $this->changeTeamNum($member_id,0,1);
            //var_dump($change_team_num);die();

            //库存（上级出货逻辑）
            Logic('inventory')->getInventory($member_id);

            //更改会员套餐金额字段
            Model('member_extend')->setFieldMemberExtend(array('me_member_id'=>$member_id), 'set_meal_amount',399);
            //更改会员加盟状态
            Model('member_extend')->setFieldMemberExtend(array('me_member_id'=>$member_id), 'is_first_joinin',0);

            //添加会员业绩
            Model('member_extend')->setIncMemberExtendField(array('me_member_id' => $member_id), array('total_bd_product_consume' => 399));


            return callback(true,'报单成功');
        }else{
            //上级级差分润(复销)
            $diffProfit =Logic('profit')->diffProfit_repay($member_id,$member_level,$pay_sn);
            if (!$diffProfit){
                return callback(false,'上级分润失败');
            }
            //库存
            Logic('inventory')->getInventory($member_id);

            //添加会员业绩
            $discount =Model('level')->getMemberDiscount($member_info['member_level']);
            Model('member_extend')->setIncMemberExtendField(array('me_member_id' => $member_id), array('total_bd_product_consume' => 399*$discount));
            return callback(true,'复销成功');
        }
    }


    /**
     * 获取升级所需条件数组
     */
    public function getUpNeedArr(){
        $res = $this->up_level_need_arr = Model('level')->getUpLevelNeedArr();
//        var_dump($res);
    }

    /**
     * 直接更改会员等级
     * @param $member_id
     * @param $member_level
     * @return multitype
     */
    public function UpdateMemberLevel($member_id,$member_level){
        if (empty($member_level) || empty($member_id)){
            return callback(false,'数据接收失败');
        }
        $res = Model()->table('member_extend')->where(array('me_member_id'=>$member_id))->field('member_level')->find();
        // var_dump($res,$data);
        $now_level = $res['member_level'];
        $res = Model('member_extend')->updateMemberLevel($member_id,$member_level);
        $up_log_data['member_id'] = $member_id;
        $member_info = Model('member_extend')->getMemberExtendInfo(array('member_id'=>$up_log_data['member_id']),'*','union');
        $up_log_data['member_name'] = $member_info['member_name'];
        $up_log_data['member_level'] = $member_info['member_level'];
        $up_log_data['before_level'] = $now_level;
        $up_log_data['up_time'] = time();
        Model('level')->addUpLevelRecord($up_log_data);
        if (!$res){
            return callback(false,$res['msg']);
        }
        return callback(true,'会员升级成功',$member_level);
    }

    /**
     * 变更所有上级团队等级人数
     * @param $member_id      int   会员ID
     * @param $before_level   int   该会员升级前等级
     * @param $member_level   int   该会员升级后等级
     * @return multitype
     */
    public function changeTeamNum($member_id,$before_level,$member_level){
        //获取N级推荐关系
//        var_dump($before_level,$member_level);
        $genealogy = Model('member')->getMemberInfo(['member_id'=>$member_id], $field = 'genealogy', $master = false);
        if (!$genealogy['genealogy']){
            $genealogy = Model('member_extend')->getInviteParentMemberIdArr($member_id);
            array_shift($genealogy);
        }
        if ($before_level ==0){
            $res = Model('member_extend')->updateChildLevelNum($genealogy,$member_level);
//            var_dump($res);
        }else{
            Model('member_extend')->DecChildLevelNum($genealogy,$before_level);
            $res = Model('member_extend')->updateChildLevelNum($genealogy,$member_level);
//            var_dump($res);
        }
        if (!$res){
            return callback(false,'团队人数修改失败');
        }
        //die();
        $this->TeamToUpLevel($genealogy,$member_level);
        return callback(true,'',$genealogy);
    }

    /**
     * 所有上级升级逻辑
     * @param $genealogy_id_arr array 所有上级id数组
     * @param $member_level int 所有上级首次要升的级别（升级$member_level失败则需判断$member_level-1，直到上级的等级与$member_level相等）
     * @return multitype
     */
    private function TeamToUpLevel($genealogy_id_arr,$member_level){
//        var_dump($member_level);
//        var_dump($genealogy_id_arr);
        $member_extend = Model('member_extend');
        $level_num = $member_extend->getTeamAllLevelNum($genealogy_id_arr);//
//        var_dump($level_num);
        if (!$level_num){
            return callback(false,'');
        }
        $this->getUpNeedArr();
        $up_arr = array();
        foreach ($level_num as $k=>$v){
//            echo 111;
            if ($v['member_level'] > $member_level){
                continue;
            }
            $times = $member_level;
            if ($times+1 >5){
                continue;
            }
            for ($i=0;$i<=($member_level-$v['member_level']);$i++){
                $res = $this->_checkUp($v, $times+1);
//                var_dump($res);
                if ($res['state'] == true) {
                    $up_arr[$times+1][] = $v['member_id'];
                    break;
                }
                $times--;
                if ($times+1 < $v['member_level']) {
                    break;
                }
            }
        }
//        var_dump($up_arr);
        //得到满足条件的上级LEVEL=>ID数组： $up_arr; 进行升级操作
        $i = 0;
        foreach ($up_arr as $k=>$v){
            if ($k >=5){
                $k=5;
            }
            $condition['me_member_id'] = $v[$i];
            $data['member_level'] = $k;
            $res = Model('member_extend')->editMemberLevel($condition, $data);
            $i++;
            //var_dump($res);die();
            if (!$res){
                return callback(false,'上级升级失败');
            }
        }
    }

    /**
     *判断是否可以升级
     * @param $member_team_info_arr
     * @param $up_level
     * @return multitype
     */
    private function _checkUp($member_team_info_arr,$up_level){
//        var_dump($up_level);
        $team_num = 0;
        for ($i=$member_team_info_arr['member_level'];$i<=$up_level-1;$i++){
            $team_num += $member_team_info_arr[$this->level_config[$i]];
        }
//        var_dump($team_num);
        //判断团队
        if ($team_num < $this->up_level_need_arr[$up_level-2]['ml_team_vip_num']){
//            var_dump($team_num,$this->up_level_need_arr[$up_level-2]['ml_team_vip_num']);
            return callback(false,'会员'.$member_team_info_arr['member_id'].'升级'.$up_level.'条件(团队)不足');
        }
//        var_dump($this->up_level_need_arr[$up_level-2]['ml_team_vip_num']);
        //判断直推
        $child_num = Model('member_extend')->getChildLevelNum($member_team_info_arr['member_id'],$up_level-1);
//        var_dump($child_num);
        if ($child_num < $this->up_level_need_arr[$up_level-2]['ml_direct_vip_num']){
//            var_dump($child_num,$this->up_level_need_arr[$up_level-2]['ml_direct_vip_num']);
            return callback(false,'会员'.$member_team_info_arr['member_id'].'升级'.$up_level.'条件(直推)不足');
        }
//        var_dump($this->up_level_need_arr[$up_level-2]['ml_direct_vip_num']);
        return callback(true,'会员'.$member_team_info_arr['member_id'].'升级'.$up_level.'条件满足');
    }


    /**
     * 根据获取的加盟费  判断会员所升等级
     * @param $member_info
     * @param $franchise_fee
     * @return int
     */
    public function getUpLevelByFranchisefee($member_info,$franchise_fee){
        $franchise_fee_arr = Model('level')->franchiseFeeArr();
        $franchise_fee_arr = array_combine(range(1, count($franchise_fee_arr)), $franchise_fee_arr);
        if (in_array(['ml_franchise_fee' =>$franchise_fee],$franchise_fee_arr)){
            $franchiseFee = $franchise_fee;
        }else{
            $franchiseFee = $franchise_fee_arr[$member_info['member_level']]['ml_franchise_fee'] + $franchise_fee;
        }
        switch ($franchiseFee){
            case 399:
                $up_level = 1;
                break;
            case 3990:
                $up_level = 2;
                break;
            case 12000:
                $up_level = 3;
                break;
            case 36000:
                $up_level = 4;
                break;
            default:
        }
        return $up_level;
    }


    /**
     * 会员加盟
     * @return multitype
     */
    public function joinIn($member_id,$franchise_fee){

        $member_info = Model('member_extend')->getMemberExtendInfo(array('member_id' => $member_id), '*', 'union');
//        var_dump($member_info);die();
        if (empty($franchise_fee)){
            return callback(false,'参数无效');
        }
        if ($franchise_fee > $member_info['balance']){
            return callback(false,'余额不足');
        }


        //根据加盟费 获取会员所升等级
        $before_level = $member_info['member_level'];
        $up_level = Logic('member_level')->getUpLevelByFranchisefee($member_info,$franchise_fee);

        //会员升级
        if ($up_level > $before_level){
            $member_up_level = Logic('member_level')->UpdateMemberLevel($member_id,$up_level);
            if (!$member_up_level){
                return callback(false,'会员升级失败');
            }
        }

        //直系上级获取直推奖
        if ($member_info['is_first_joinin'] == 1){
            $parent_direct_profit = Logic('profit')->parentAddDirectProfit($member_id,$up_level);
            if (!$parent_direct_profit){
                return callback(false,'直系上级获取直推奖失败');
            }
            //修改加盟状态
            $condition['me_member_id'] = $member_info['member_id'];
            $res = Model('member_extend')->setFieldMemberExtend($condition,'is_first_joinin',0);
        }

        //加盟费存入库存，不可提现
        $condition = ['member_id'=>$member_id];
        $inventory = $this->inventory_info['product_num_arr'][$up_level]*$this->inventory_info['report_product_price'];//库存
        try{
            Model('member')->addInventory($condition,$inventory);
        }catch (Exception $e){
            return callback(false,'产品(库存)配送失败');
        }

        //变更所有上级团队等级人数 +所有上级升级
        if ($up_level > $member_info['member_level']){
            $all_parent_up_level = $this->changeTeamNum($member_id,$before_level,$up_level);
            if ($all_parent_up_level['state'] == false){
                return callback(false,'上级升级失败',$all_parent_up_level['msg']);
            }
        }
        return callback(true,'');
    }

}