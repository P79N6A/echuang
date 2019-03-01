<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/27 0027
 * Time: 下午 14:08
 */
defined('In33hao') or exit('Access Invalid!');

class city_lordControl extends apiEncryptMemberControl
{
    public function __construct()
    {
        parent::__construct();
    }


    /**
     *
     *城主竞拍支付
     */
    public function city_lord_payOp()
    {
        @header("Content-type: text/html; charset=" . CHARSET);
        $order_id = $_POST['order_id'];
        if (in_array($_POST['payment_code'], array('alipay', 'wxpay', 'beanpay', 'sliver_beanpay','balancepay'))) {
            if ($_POST['payment_code'] == "alipay") {
                $pay_info = $this->alipay_native_payOp($order_id);
            }
            if ($_POST['payment_code'] == "wxpay") {
                $pay_info = $this->wx_app_payOp($order_id);
            }
            if ($_POST['payment_code'] == "balancepay") {
                $pay_info = $this->_get_real_order_info($order_id, $_POST);
            }
            if (isset($pay_info['error'])) {
                responseApiJson(0, $pay_info['error'], '');
            }
        } else {
            responseApiJson(0, '暂无该支付方式11', '');
        }
    }


    /*
 * 获取订单支付信息
 */
    private function _get_real_order_info($order_id, $rcb_pd_pay = array())
    {
        $city_lord_where['member_id'] = $this->member_info['member_id'];
        //取订单信息
        $city_lord_where['rob_city_lord_id'] = $order_id;
        $order_list = Model()->table('rob_city_lord')->where($city_lord_where)->find();
        if ($order_list['state'] == 1) {
            responseApiJson(0, '订单已经支付，不能重复支付', '');
        }
        //余额支付
        if ($rcb_pd_pay['payment_code'] == 'balancepay') {
            $result['data']['order_list'] = $this->_balance_pay($order_list, $rcb_pd_pay, $city_lord_where['member_id']);
        }
        if($rcb_pd_pay['payment_code'] == ''){
            $model_member = Model('red_advertisement');
            $where['member_id'] = $city_lord_where['member_id'];
            $buyer_info = $model_member->getMemberInfoByID($where);
            if ($order_list['last_lord_id'] == $buyer_info['member_id']){
                responseApiJson(0, "您已经是城主啦", '');
            }else{
                $result = $order_list;
            }
        }
        return $result;
    }

    private function _balance_pay($order_list, $post, $memberid){
        if (empty($post['password'])) {
            responseApiJson(0, '参数错误', '');
        }
        $model_member = Model('red_advertisement');
        $where['member_id'] = $memberid;
        $buyer_info = $model_member->getMemberInfoByID($where);
        if ($buyer_info['member_paypwd'] == '' || $buyer_info['member_paypwd'] != $post['password']) {
            responseApiJson(0, "用户支付密码不正确", '');
        }
        if ($order_list['last_lord_id'] == $buyer_info['member_id']){
            responseApiJson(0, "您已经是城主啦", '');
        }
        Db::beginTransaction();
        try {
            //当前城主订单支付
            $member_id = $buyer_info['member_id'];
            $member_name = $buyer_info['member_name'];
            $model_member_extend = Model('member_extend');
            $balance = $model_member_extend->getMemberExtendInfo(array('member_id' => $member_id), 'balance', 'union');
            $balance = floatval($balance['balance']);
            $money = floatval($order_list['money']);
            $data_pay = array();
            $data_pay['member_id'] = $member_id;
            $data_pay['member_name'] = $member_name;
            $data_pay['order_sn'] = $order_list['order_number'];
            $data_pay['amount'] = $order_list['money'];
            $data_pay['type'] = 'city';
            if ($balance >= $money) {
                // 立即支付，订单支付完成
                $result = $model_member_extend->city_order_pay($data_pay);
                if (isset($result['error'])) {
                    throw new Exception('支付失败3' . $result['error']);
                }
                //修改该区域城主现价
                $city_now_price =  Model()->table('city_price')->where(['area'=>$order_list['area_id']])->find();
                if (!empty($city_now_price)){
                    Model()->table('city_price')->where(['area'=>$order_list['area_id']])->update(['present_price'=>$order_list['money']]);
                }else{
                    throw new Exception( $result['error'].'没有该地区数据' );
                }
                //订单支付完成，修改支付状态
                Model()->table('rob_city_lord')->where(['rob_city_lord_id'=>$order_list['rob_city_lord_id']])->update(['state'=>1,'mode'=>'balancepay']);
                //上个城主订单
                $last_order = Model()->table('rob_city_lord')->where(['member_id'=>$order_list['last_lord_id'],'area_id'=>$order_list['area_id'],'addtime'=>['lt',time()]])->order('addtime desc')->find();
                if (!empty($last_order)){
                    //修改上个城主结束时间
                    Model()->table('rob_city_lord')->where(['rob_city_lord_id'=>$last_order['rob_city_lord_id']])->update(['endtime'=>time()]);
                }
                //上个城主
                $city_lord =  Model()->table('city_lord')->where(['area'=>$last_order['area_id']])->order('addtime desc')->find();
                if (!empty($city_lord)){
                    Model()->table('city_lord')->where(['city_lord_id'=>$city_lord['city_lord_id']])->update(['endtime'=>time()]);
                }
                //城主表添加新城主数据
                $insert['order_number'] = 'RDCZ'.time().rand('0000','9999');
                $insert['area'] = $order_list['area_id'];
                $insert['member_id'] = $member_id;
                $insert['addtime'] = time();
                Model()->table('city_lord')->insert($insert);
            } else {
                throw new Exception('可用余额不足');
            }
            Db::commit();
            //上次城主金额返还
            if (!empty($order_list['last_lord_id'])){
                $data_raward = array();
                $data_raward['member_id'] = $order_list['last_lord_id'];
                $data_raward['member_name'] = Model('member')->getMemberNameById($order_list['rob_city_lord_id']);
                $data_raward['order_sn'] = $order_list['order_number'];
                $primary_money = $this-> _get_subsidy($order_list['last_lord_money'],$order_list['money']);
                $data_raward['amount'] = $primary_money;
                $data_raward['type'] = 'city';
                $raward = Model('member_extend')->changeMemberBalance('city',$data_raward);
                if (isset($raward['error'])) {
                    throw new Exception('城主被抢占，金额返还失败' . $result['error']);
                }
            }
            responseApiJson(1, "支付成功", '');
        } catch (Exception $exception) {
            Db::rollback();
            responseApiJson(0, $exception->getMessage(), '');
        }
    }

    private  function  _get_subsidy($last_lord_money,$money)
    {
        $benefits=Model('red_city_lord_share_benefits');
        $databenefits=$benefits->red_getRow('1');
        $primary_money = $last_lord_money+($money*$databenefits['city_rebate'])/100;
        return $primary_money;
    }

    /**
     * 微信APP订单支付
     */
    public function wx_app_payOp($order_id) {
        $pay_info = $this->_get_real_order_info($order_id);
        if ($pay_info['rob_city_lord_id']!=$order_id) {
            responseApiJson(0, '订单号错误', '');
        }
        $param = array();
        $param['pay_sn'] = $pay_info['order_number'];
        $param['subject'] = '微信APP订单支付:'.$pay_info['order_number'];
        $param['amount'] =  0.01* 100;//$pay_info['money']
        $data = $this->aaaOp($param);
        responseApiJson(1, '', $data);
    }
    /*
     * 微信API接口
     */
    public function aaaOp($param) {
        $inc_file = '../mobile' . DS . 'api' . DS . 'payment' . DS . 'wxpay2.php';
        require_once $inc_file;
        $wxpay = new Wxpay2();
        $pre_param = $wxpay->getPrePayOrder($param['subject'], $param['pay_sn'], $param['amount']);
        $pay_param = $wxpay->getPayParam($pre_param);
        return $pay_param;
    }
}