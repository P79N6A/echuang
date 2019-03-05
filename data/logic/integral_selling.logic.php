<?php
/**
 * e创 积分挂卖订单
 */
defined('In33hao') or exit('Access Invild!');

class integral_sellingLogic
{
    /**
     * 创建订单
     * @param int $member_id 会员id
     * @param float $integral 挂卖积分
     * @param int $state 订单状态
     * @return bool
     */
    public function makeIntegralSellingOrder($member_id,$integral,$state=1)
    {
        $model_integral_selling = Model('integral_selling');
        $setting = Model('setting')->getListSetting();
        $service_charge = empty($setting['f_service_charge'])?20:$setting['f_service_charge'];
        $vip_service_charge = empty($setting['f_vip_service_charge'])?10:$setting['f_vip_service_charge'];
        $service_standard = empty($setting['f_service_standard'])?100:$setting['f_service_standard'];
        $vip_service_standard = empty($setting['f_vip_service_standard'])?1000:$setting['f_vip_service_standard'];

        $member_info = Model('member')->getMemberInfoByID($member_id);
        if ($member_info['member_level'] == 1){
            if ($integral<$service_standard){
                //挂卖积分小于标准
                return false;
            }
            $actual_integral = $integral - ($integral*$service_charge/100);
            $type = 0;
        }
        elseif($member_info['member_level'] == 2){
            if ($integral<$vip_service_standard){
                //挂卖积分小于标准
                return false;
            }
            $actual_integral = $integral - ($integral*$vip_service_charge/100);
            $type = 1;
        }
        else{
            return false;
        }

        $data = array(
            'member_id'=>$member_id,
            'sell_sn'=>$this->makeOrderSn($member_id),
            'sell_integral'=>$integral,
            'actual_integral'=>$actual_integral,
            'type'=>$type,
            'state'=>$state,
            'add_time'=>time(),
        );

        if ($model_integral_selling->addIntegralSelling($data)){
            return $data['sell_sn'];
        }else{
            return false;
        }
    }

    /**
     * 积分挂卖
     * @param int $member_id  会员id
     * @param float $integral 挂卖积分
     */
    public function sellIntegral($member_id,$integral)
    {
        Db::beginTransaction();
        $sell_sn = $this->makeIntegralSellingOrder($member_id,$integral);
        if ($sell_sn){
            $model_declaration_from = Model('declaration_form');
            $member_info = $model_declaration_from->getIntegralTotal($member_id,2);
            $remakes = '挂卖积分，订单号：'.$sell_sn;
            if ($model_declaration_from->changeMemberIntegral($member_id,1,2,$integral,$member_info,$remakes,3)){
                Db::commit();
                return true;
            }else{
                Db::rollback();
                return false;
            }
        }else{
            return false;
        }
    }

    /**
     * 积分回购
     * @param int $id           积分挂卖订单id
     * @param int $type         类型：1、公司回购；2、创客VIP回购；
     * @param int $member_id    创客VIP会员id
     * @retunr bool
     */
    public function buyIntegral($id, $type = 1, $member_id = 0)
    {
        $model_integral_selling = Model('integral_selling');
        if ($type == 1){
            $where = array('id'=>$id);
            $data = array('state'=>2);
            return $model_integral_selling->editIntegralSelling($where,$data);
        }
        elseif($type == 2){
            Db::beginTransaction();
            $model_declaration_from = Model('declaration_form');
            $integral_selling_info = $model_integral_selling->getIntegralSellingInfoByID($id);
            $member_info = $model_declaration_from->getIntegralTotal($member_id,2);
            $integral = $integral_selling_info['actual_integral'];
            $sell_sn = $integral_selling_info['sell_sn'];
            if ($model_declaration_from->changeMemberIntegral($member_id,1,1,$integral,$member_info,'回购积分，订单号：'.$sell_sn,4)){
                $where = array('id'=>$id);
                $data = array('state'=>2,'sell_member_id'=>$member_id,'buy_time'=>time());
                if ($model_integral_selling->editIntegralSelling($where,$data)){
                    Db::commit();
                    return true;
                }else{
                    Db::rollback();
                    return false;
                }
            }
        }
        else{
            return false;
        }
    }

    /**
     * 拒绝挂卖
     * @param int $id 订单id
     * @return bool
     */
    public function refuseIntegralSelling($id)
    {
        $model_integral_selling = Model('integral_selling');
        $model_declaration_from = Model('declaration_form');
        $integral_selling_info = $model_integral_selling->getIntegralSellingInfoByID($id);
        $member_id = $integral_selling_info['member_id'];
        $integral = $integral_selling_info['sell_integral'];
        $sell_sn = $integral_selling_info['sell_sn'];
        $member_info = $model_declaration_from->getIntegralTotal($member_id,2);
        if ($model_declaration_from->changeMemberIntegral($member_id,1,1,$integral,$member_info,'挂卖积分订单被拒，订单号：'.$sell_sn,10)){
            $where = array('id'=>$id);
            $data = array('state'=>3,'refuse_time'=>time());
            return $model_integral_selling->editIntegralSelling($where,$data);
        }
    }

    /**
     * 生成积分挂卖订单编号(两位随机 + 从2000-01-01 00:00:00 到现在的秒数+微秒+会员ID%1000)，该值会传给第三方支付接口
     * 长度 =2位 + 10位 + 3位 + 3位  = 18位
     * 1000个会员同一微秒提订单，重复机率为1/100
     * @return string
     */
    public function makeOrderSn($member_id)
    {
        return $this->product_sn = mt_rand(10, 99)
            . sprintf('%010d', time() - 946656000)
            . sprintf('%03d', (float) microtime() * 1000)
            . sprintf('%03d', (int) $member_id % 1000);
    }
}