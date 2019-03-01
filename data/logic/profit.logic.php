<?php
/**
 * 级差分润逻辑
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/19 0019
 * Time: 下午 14:11
 */
defined('In33hao') or exit('Access Invalid!');
class profitLogic {

    private $profit_total;
    private $profit_set_arr;
    private $eql_profit_set_arr;
    public function __construct(){
        $this->_getProfitSet();
    }

    private function _getProfitSet(){
        $this->profit_set_arr = array_column(Model()->table('member_level')->field('ml_commission')->select(),'ml_commission');
        $res = $this->profit_set_arr;
        array_pop($res);
        $this->profit_total = array_sum($res);
        $condition['name'] = "senior_partner_commission_v1";
        $this->eql_profit_set_arr[] = Model()->table('setting')->field('name,value')->where($condition)->find();
        $condition['name'] = "senior_partner_commission_v2";
        $this->eql_profit_set_arr[] = Model()->table('setting')->field('name,value')->where($condition)->find();
        $condition['name'] = "senior_partner_commission_v3";
        $this->eql_profit_set_arr[] = Model()->table('setting')->field('name,value')->where($condition)->find();
        $this->eql_profit_set_arr = array_column($this->eql_profit_set_arr,'value');
    }

    /**
     * 给直系上级添加直推奖励，记录插入分红表。
     * @param $member_id
     * @param $member_level
     * @return multitype
     */
    public function parentAddDirectProfit($member_id,$member_level){
        $member_model = Model('member');
        $parent_id = $member_model->getDirectParentID(array('member_id'=>$member_id),$fields = 'inviter_id');
        if (!$parent_id){
            return callback(false,'没有上级信息');
        }
        $direct_prize = Model('level')->getDirectPrize($member_level);
        if (!$direct_prize){
            return callback(false,'获取奖励金额失败');
        }
        Model::beginTransaction();
        try{
            //增加上级余额
            $res = $member_model->addBalance($parent_id['inviter_id'],$direct_prize['ml_direct_prize']);
            //插入直推记录
            if ($res){
                $parent_name = $member_model->getInfoMember(array('member_id'=>$parent_id['inviter_id']), $field = 'member_name');
                $insert['member_name'] = $parent_name['member_name'];
                $insert['member_id'] = $parent_id['inviter_id'];
                $insert['type'] = 'direct';
                $insert['prize'] = $direct_prize['ml_direct_prize'];
                $insert['dr_no'] = date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
                $insert['add_time'] = time();
                $level = str_replace(array(1,2,3,4),array("Vip","店主","合伙人","高级合伙人"),$member_level);
                $insert['content'] = "直推".$level.$member_id."奖励";
                Model('member_profit')->addProfitRecord($insert);
            }
            Model::commit();
        }catch(Exception $e){
            Model::rollback();
            return callback(false,'直推奖励失败');
        }
        return callback(true,'上级获取直推奖成功');
    }

    /**
     * 用户首次购买399套餐，所有上级级差分润，记录插入分润表
     * @param $member_id
     */
    public function diffProfit($member_id,$pay_sn)
    {
        if (!$member_id || !$pay_sn){
            return callback(false,"参数错误");
        }
        //获取所有上级Id=>Level数组
        $parentMembetIdArr = Model('member_extend')->getInviteParentMemberIdArr($member_id);
        $parentMembetLevelArr = Model('member_extend')->getInviteParentMemberLevelArr($member_id);
        array_shift($parentMembetIdArr);
        array_shift($parentMembetLevelArr);
        $parentArr = array_combine($parentMembetIdArr, $parentMembetLevelArr);
        if (!$parentArr){
            return callback(false,"获取上级信息错误");
        }
//        var_dump($parentArr);
        //所有上级级差分润
        $diff = 0; //已被分润金额
        $flag = null;  //高级or战略合伙人出现的标记
        $up_num = 0;  //上级中获得分润的人数
        $up_times = 0;//上级次序
        foreach ($parentArr as $k => $v) { //ID=>Level
            $up_times++;
            if ($up_times == 1 && $v == 0){
                $diff = $this->profit_set_arr[0];
            }
            $insert['type'] = 'diff';
            switch ($v) {
                case 1:
                    $prize[$k] = $this->profit_set_arr[0] - $diff;
                    break;
                case 2:
                    $prize[$k] = $this->profit_set_arr[0] + $this->profit_set_arr[1] - $diff;
                    break;
                case 3:
                    $prize[$k] = $this->profit_total - $this->profit_set_arr[3] - $diff;
                    break;
                case 4:
                    $prize[$k] = $this->profit_total - $diff;
                    break;
                case 5:
                    $prize[$k] = $this->profit_total - $diff;
                    break;
                default:
                    $prize[$k] = 0;
            }
            if ($prize[$k]<0){
                $prize[$k] = 0;
            }
            $diff += $prize[$k];
            if ($v==5 || $v==4){
                switch ($flag){
                    case 1:
                        $insert['type'] = 'eql';
                        $prize[$k] = $this->eql_profit_set_arr[0];
                        break;
                    case 2:
                        $insert['type'] = 'eql';
                        $prize[$k] = $this->eql_profit_set_arr[1];
                        break;
                    case 3:
                        $insert['type'] = 'eql';
                        $prize[$k] = $this->eql_profit_set_arr[2];
                        break;
                }
                $flag+=1;
            }
            //添加记录,更改余额。
            if ($prize[$k] != 0){
                $up_num ++;
                $condition['member_id'] = $k;
                $fields = 'member_name,member_id';
                $member_info = Model('member')->getMemberInfo($condition,$fields);
                $level_name = str_replace(array(1,2,3,4,5),array("VIP","店主","合伙人","高级合伙人","战略合伙人"),$v);
                $insert['member_id'] = $member_info['member_id'];
                $insert['member_name'] = $member_info['member_name'];
                $insert['add_time'] = time();
                $insert['pay_sn'] = $pay_sn;
                $insert['prize'] = $prize[$k];
                $insert['dr_no'] = makeSN('bonus',$member_info['member_id']);
                $insert['content'] = $level_name."(".$insert['member_id'].")获得报单级差奖励".$prize[$k];
                try{
                    //分润记录
                    Model('member_profit')->addProfitRecord($insert);
                    //更改余额
                    $exchange_data['member_id'] = $member_info['member_id'];
                    $exchange_data['member_name'] = $member_info['member_name'];
                    $exchange_data['amount'] = $prize[$k];
                    $exchange_data['content'] = $level_name."(".$insert['member_id'].")获得报单级差奖励".$prize[$k];;
                    $result = Model('member_extend')->changeMemberBalance('bonus', $exchange_data);
                    Model('member')->addMemberBonus($member_info['member_id'],$prize[$k]);
                }catch (Exception $e){
                   $e->getMessage();
                }
            }
            if ($flag>=4){
                break;
            }
//            var_dump($prize[$k]);
        }
        $msg = $up_num."位上级获得分润";
        return callback(true,$msg,'');
    }

    /**
     * 用户复购399套餐，所有上级级差分润，记录插入分润表
     * @param $member_id
     * @param $member_level
     * @return multitype
     */
    public function diffProfit_repay($member_id,$member_level,$pay_sn){
        //        var_dump($member_id);
        //获取所有上级Id=>Level数组
        $parentMembetIdArr = Model('member_extend')->getInviteParentMemberIdArr($member_id);
        $parentMembetLevelArr = Model('member_extend')->getInviteParentMemberLevelArr($member_id);
        array_shift($parentMembetIdArr);
        array_shift($parentMembetLevelArr);
        $parentArr = array_combine($parentMembetIdArr, $parentMembetLevelArr);
        //所有上级级差分润
        switch ($member_level){
            case 2:
                $diff = $this->profit_set_arr[1];
                break;
            case 3:
                $diff = $this->profit_set_arr[2] + $this->profit_set_arr[1];
                break;
            default:
                $diff =0;
        }
        $flag = 1;
        $up_num = 0;
        foreach ($parentArr as $k => $v) { //ID=>Level
            if ($member_level <4){
                $insert['type'] = 'diff';
                if ($member_level>=$v){
                    continue;
                }
                switch ($v) {
                    case 2:
                        $prize[$k] = $this->profit_set_arr[1] - $diff;
                        break;
                    case 3:
                        $prize[$k] = $this->profit_set_arr[2] + $this->profit_set_arr[1] - $diff;
                        break;
                    case 4:
                        $prize[$k] = $this->profit_total - $this->profit_set_arr[0] - $diff;
                        break;
                    case 5:
                        $prize[$k] = $this->profit_total - $this->profit_set_arr[0] - $diff;
                        break;
                    default:
                        $prize[$k] = 0;
                }
                if ($prize[$k]<0){
                    $prize[$k] = 0;
                }
                $diff += $prize[$k];
                if ($v==5 || $v==4){
                    if ($flag == 1){
                        $insert['type'] = 'diff';
                    }else{
                        $insert['type'] = 'eql';
                    }
                    switch ($flag){
                        case 2:
                            $prize[$k] = $this->eql_profit_set_arr[0];
                            break;
                        case 3:
                            $prize[$k] = $this->eql_profit_set_arr[1];
                            break;
                        case 4:
                            $prize[$k] = $this->eql_profit_set_arr[2];
                            break;
                    }
                    $flag+=1;
                }
            }else {
                if ($v == 5 || $v == 4) {
                    $insert['type'] = 'eql';
                    switch ($flag) {
                        case 1:
                            $prize[$k] = $this->eql_profit_set_arr[0];
                            break;
                        case 2:
                            $prize[$k] = $this->eql_profit_set_arr[1];
                            break;
                        case 3:
                            $prize[$k] = $this->eql_profit_set_arr[2];
                            break;
                    }
                    $flag += 1;
                }
            }

            //添加记录,更改余额。
            if ($prize[$k] != 0){
                $up_num ++;
                $condition['member_id'] = $k;
                $fields = 'member_name,member_id';
                $member_info = Model('member')->getMemberInfo($condition,$fields);
                $level_name = str_replace(array(1,2,3,4,5),array("VIP","店主","合伙人","高级合伙人","战略合伙人"),$v);
                $insert['member_id'] = $member_info['member_id'];
                $insert['member_name'] = $member_info['member_name'];
                $insert['add_time'] = time();
                $insert['prize'] = $prize[$k];
                $insert['pay_sn'] = $pay_sn;
                $insert['dr_no'] = makeSN('bonus',$member_info['member_id']);
                $insert['content'] = $level_name."(".$insert['member_id'].")获得复销级差奖励".$prize[$k];
                try{
                    //分润记录
                    Model('member_profit')->addProfitRecord($insert);
                    //更改余额，添加余额记录
                    $exchange_data['member_id'] = $member_info['member_id'];
                    $exchange_data['member_name'] = $member_info['member_name'];
                    $exchange_data['amount'] = $prize[$k];
                    $exchange_data['content'] = $level_name."(".$insert['member_id'].")获得复销级差奖励".$prize[$k];;
                    $result = Model('member_extend')->changeMemberBalance('bonus', $exchange_data);
                    Model('member')->addMemberBonus($member_info['member_id'],$prize[$k]);
                }catch (Exception $e){
                    $e->getMessage();
                }
            }
            if ($flag>=5){
                break;
            }
        }
        $msg = $up_num."位上级获得分润";
        return callback(true,$msg,'');
    }

}