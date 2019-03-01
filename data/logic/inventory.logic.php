<?php
/**
 * 库存逻辑
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/30 0030
 * Time: 上午 10:13
 */
defined('In33hao') or exit('Access Invalid!');
class inventoryLogic{
    private $level_ratio;

    public function __construct() {
        $this->_getLevelRatio();
    }

    private function _getLevelRatio(){
        $level_ratio = Model('member_level')->field('ml_id,ml_discount_ratio')->select();
        foreach ($level_ratio as $v){
            $this->level_ratio[$v['ml_id']] = $v['ml_discount_ratio'];
        }
    }

    /**
     * 上级出货逻辑（库存）
     * @param $member_id
     * @return multitype
     */
    public function getInventory($member_id){
//        var_dump($member_id);
        //获取N级推荐关系
        $genealogy = Model('member')->getMemberInfo(['member_id'=>$member_id], $field = 'genealogy', $master = false);
        if (!$genealogy['genealogy']){
            $genealogy = Model('member_extend')->getInviteParentMemberIdArr($member_id);
            array_shift($genealogy);
        }
       //所有上级库存（按比例转换后）->出货得上级
        $all_parten_inventory_arr = Model('member_extend')->getTeamMemberInventoryArr($genealogy);
        foreach ((array)$all_parten_inventory_arr as $k=>$v){
            if ($v>=399){
                $give_product_member = $k;
                break;
            }
        }
       if (!$give_product_member){
           return callback(false,'所有上级库存不足');
       }
//       var_dump($give_product_member);
       //更改出货上级库存
        $change = $this->_changeInventory($give_product_member);
       if (!$change){
           return callback(false,'上级出货失败');
       }
        //更改出货上级余额
        $member_info = Model('member_extend')->getMemberExtendInfo(['member_id'=>$give_product_member],"*",'union');
        $exchange_data['member_id'] = $give_product_member;
        $exchange_data['member_name'] = $member_info['member_name'];
        $exchange_data['amount'] = 399*$this->level_ratio[$member_info['member_level']];
        $result = Model('member_extend')->changeMemberBalance('inventory', $exchange_data);
//        var_dump($result);
    }

    private function _changeInventory($member_id){
        $member_model = Model('member');
        $member_info = Model('member_extend')->getMemberExtendInfo(['member_id'=>$member_id],"*",'union');
        $inventory = 399*$this->level_ratio[$member_info['member_level']];
        $condition['member_id'] = $member_id;
        return $member_model->changeMemberInventory($condition,$inventory);
    }
}