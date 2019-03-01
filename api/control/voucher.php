<?php
/**
 * 代金券
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/4 0004
 * Time: 下午 15:07
 */
defined('In33hao') or exit('Access Invalid!');
class voucherControl extends apiEncryptMemberControl
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 我的代金券列表
     */
    public function voucher_listOp() {
        $param = $_POST;

        $model_voucher = Model('voucher');
        $voucher_list = $model_voucher->getMemberVoucherList($this->member_info['member_id'], $param['voucher_state'], $this->page, 'voucher_state asc,voucher_id desc');
        $page_count = $model_voucher->gettotalpage();
        responseApiJson(1,array('voucher_list' => $voucher_list), mobile_page($page_count));
    }

    /**
     * 免费领取代金券
     */
    public function voucher_freeexOp() {
        $param = $_POST;

        $t_id = intval($param['tid']);
        if($t_id <= 0){
            responseApiJson(0,'代金券信息错误');
        }
        $model_voucher = Model('voucher');
        //验证是否可领取代金券
        $data = $model_voucher->getCanChangeTemplateInfo($t_id, $this->member_info['member_id'], $this->member_info['store_id']);
        if ($data['state'] == false){
            responseApiJson(0,$data['msg']);
        }
        try {
            $model_voucher->beginTransaction();
            //添加代金券信息
            $data = $model_voucher->exchangeVoucher($data['info'], $this->member_info['member_id'], $this->member_info['member_name']);
            if ($data['state'] == false) {
                throw new Exception($data['msg']);
            }
            $model_voucher->commit();
            responseApiJson(1,'领取成功');
        } catch (Exception $e) {
            $model_voucher->rollback();
            responseApiJson(0,$e->getMessage());
        }
    }
}