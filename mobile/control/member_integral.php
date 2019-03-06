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

class member_integralControl extends mobileMemberControl
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 积分明细
     */
    public function indexOp()
    {
        $order = 'add_time desc';
        $page = empty($_REQUEST['rp'])?10:intval($_REQUEST['rp']);
        $model_member_integral_log = Model('member_integral_log');
        $data = array();
        $list = $model_member_integral_log->getMemberIntegralLogList(array('member_id'=>$this->member_info['member_id']),'*',$page,$order);
        $data['now_page'] = $model_member_integral_log->shownowpage();
        $data['total_num'] = $model_member_integral_log->gettotalnum();
        if (empty($list)){
            $data['list'] = array();
        }
        else{
            foreach($list as $k=>$v)
            {
                $v['type'] = $model_member_integral_log->state[$v['type']];
                $data['list'][$v['id']] = $v;
            }
        }
        output_data($data);
    }

    /**
     * 我的挂卖订单
     */
    public function my_selling_orderOp()
    {
        $order = 'add_time desc';
        $page = empty($_REQUEST['rp'])?10:intval($_REQUEST['rp']);
        $model_integral_selling = Model('integral_selling');
        $data = array();
        $list = $model_integral_selling->getIntegralSellingList(array('member_id'=>$this->member_info['member_id']),'*',$page,$order);
        $data['now_page'] = $model_integral_selling->shownowpage();
        $data['total_num'] = $model_integral_selling->gettotalnum();
        if(empty($list)){
            $data['list'] = array();
        }
        else{
            foreach($list as $k=>$v)
            {
                $v['state'] = $model_integral_selling->state[$v['state']];
                $data['list'][$v['id']] = $v;
            }
        }
        output_data($data);
    }


    /**
     * 我的挂卖订单
     */
    public function market_selling_orderOp()
    {
        $order = 'add_time desc';
        $page = empty($_REQUEST['rp'])?10:intval($_REQUEST['rp']);
        $model_integral_selling = Model('integral_selling');
        $data = array();
        $list = $model_integral_selling->getIntegralSellingList(array(),'*',$page,$order);
        $data['now_page'] = $model_integral_selling->shownowpage();
        $data['total_num'] = $model_integral_selling->gettotalnum();
        if(empty($list)){
            $data['list'] = array();
        }
        else{
            foreach($list as $k=>$v)
            {
                $v['state'] = $model_integral_selling->state[$v['state']];
                $v['member_name'] = Model('member')->getMemberInfo(array('member_id'=>$v['member_id']),'member_name')['member_name'];
                $data['list'][$v['id']] = $v;
            }
        }
        output_data($data);
    }

    /**
     * 我的回购订单
     */
    public function my_buy_orderOp()
    {
        $order = 'add_time desc';
        $page = empty($_REQUEST['rp'])?10:intval($_REQUEST['rp']);
        $model_integral_selling = Model('integral_selling');
        $data = array();
        $list = $model_integral_selling->getIntegralSellingList(array('sell_member_id'=>$this->member_info['member_id']),'*',$page,$order);
        $data['now_page'] = $model_integral_selling->shownowpage();
        $data['total_num'] = $model_integral_selling->gettotalnum();
        if(empty($list)){
            $data['list'] = array();
        }
        else{
            foreach($list as $k=>$v)
            {
                $v['state'] = $model_integral_selling->state[$v['state']];
                $data['list'][$v['id']] = $v;
            }
        }
        output_data($data);
    }

    /**
     * 积分挂卖
     */
    public function sell_integralOp()
    {
        $integral = empty($_REQUEST['integral'])?0:intval($_REQUEST['integral']);
        $result = Logic('integral_selling')->sellIntegral($this->member_info['member_id'],$integral);
        if (empty($result['error'])){
            output_data('挂卖成功');
        }else{
            output_error($result['error']);
        }
    }

    /**
     * 积分回购
     */
    public function buy_integralOp()
    {
        $id = empty($_REQUEST['id'])?0:intval($_REQUEST['id']);
        $result = Logic('integral_selling')->buyIntegral($id,2,$this->member_info['member_id']);
        if (empty($result['error'])){
            output_data('回购成功');
        }else{
            output_error($result['error']);
        }
    }

    /**
     * 挂卖标准
     */
    public function integral_selling_settingOp()
    {
        $setting = Model('setting')->getListSetting();
        $data['service_charge'] = empty($setting['f_service_charge'])?20:$setting['f_service_charge'];
        $data['vip_service_charge'] = empty($setting['f_vip_service_charge'])?10:$setting['f_vip_service_charge'];
        $data['service_standard'] = empty($setting['f_service_standard'])?100:$setting['f_service_standard'];
        $data['vip_service_standard'] = empty($setting['f_vip_service_standard'])?1000:$setting['f_vip_service_standard'];
        output_error($data);
    }
}