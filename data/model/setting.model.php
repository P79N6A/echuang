<?php
/**
 * 系统设置内容
 *
 *
 *
 * *  (c) ()
 * @license
 * @link
 * @since
 */
defined('In33hao') or exit('Access Invalid!');
class settingModel extends Model {
    public function __construct() {
        parent::__construct('setting');
    }

    /**
     * 读取系统设置信息
     *
     * @param string $name 系统设置信息名称
     * @return array 数组格式的返回结果
     */
    public function getRowSetting($name) {
        $param  = array();
        $param['table'] = 'setting';
        $param['where'] = "name='".$name."'";
        $result = Db::select($param);
        if(is_array($result) and is_array($result[0])){
            return $result[0];
        }
        return false;
    }

    /**
     * 读取系统设置列表
     *
     * @param
     * @return array 数组格式的返回结果
     */
    public function getListSetting(){
        $param = array();
        $param['table'] = 'setting';
        $result = Db::select($param);
        /**
         * 整理
         */
        if (is_array($result)){
            $list_setting = array();
            foreach ($result as $k => $v){
                $list_setting[$v['name']] = $v['value'];
            }
        }
        return $list_setting;
    }

    /**
     * 更新信息
     *
     * @param array $param 更新数据
     * @return bool 布尔类型的返回结果
     */
    public function updateSetting($param) {
        if (empty($param)) {
            return false;
        }

        if (is_array($param)) {
            foreach ($param as $k => $v){
                $tmp = array();
				$tmp_k = array();
                $specialkeys_arr = array('statistics_code');
				$tmp_k['name'] = $k;
				$tmp['name'] = $k;
                $tmp['value'] = (in_array($k, $specialkeys_arr) ? $v : $v);
                $where = " name = '".$k."'";
				$setting_data = Db::select(['field'=>'name','table'=>'setting']);
				if(!in_array($tmp_k, $setting_data)){
					$result = Db::insert('setting', $tmp);
				} else {
					$result = Db::update('setting', $tmp, $where);
				}
                if ($result !== true){
                    return $result;
                }
            }
            dkcache('setting');
            // @unlink(BASE_DATA_PATH.DS.'cache'.DS.'setting.php');
            return true;
        } else {
            return false;
        }
    }

public function memberClear() {
		Db::query("delete from `".DBPRE."member`");
		Db::query("delete from `".DBPRE."store`");
		Db::query("delete from `".DBPRE."upload` where store_id>0");
		Db::query("TRUNCATE TABLE `".DBPRE."goods`");
		Db::query("TRUNCATE TABLE `".DBPRE."goods_spec`");
		Db::query("TRUNCATE TABLE `".DBPRE."goods_group`");
		Db::query("TRUNCATE TABLE `".DBPRE."activity_detail`");
		Db::query("TRUNCATE TABLE `".DBPRE."address`");
		Db::query("TRUNCATE TABLE `".DBPRE."cart`");
		Db::query("TRUNCATE TABLE `".DBPRE."consult`");
		Db::query("TRUNCATE TABLE `".DBPRE."favorites`");
		Db::query("TRUNCATE TABLE `".DBPRE."message`");
		Db::query("TRUNCATE TABLE `".DBPRE."order`");
		Db::query("TRUNCATE TABLE `".DBPRE."order_address`");
		Db::query("TRUNCATE TABLE `".DBPRE."order_goods`");
		Db::query("TRUNCATE TABLE `".DBPRE."order_log`");
		Db::query("TRUNCATE TABLE `".DBPRE."recommend_goods`");
		Db::query("TRUNCATE TABLE `".DBPRE."store_class_goods`");
		Db::query("TRUNCATE TABLE `".DBPRE."store_goods_class`");
		Db::query("TRUNCATE TABLE `".DBPRE."store_navigation`");
		Db::query("TRUNCATE TABLE `".DBPRE."store_partner`");
		Db::query("TRUNCATE TABLE `".DBPRE."store_gradelog`");
		Db::query("TRUNCATE TABLE `".DBPRE."store_watermark`");
		Db::query("TRUNCATE TABLE `".DBPRE."inform`");
		Db::query("TRUNCATE TABLE `".DBPRE."complain`");
		Db::query("TRUNCATE TABLE `".DBPRE."complain_goods`");
		Db::query("TRUNCATE TABLE `".DBPRE."complain_talk`");
		Db::query("TRUNCATE TABLE `".DBPRE."voucher`");
		Db::query("TRUNCATE TABLE `".DBPRE."voucher_template`");
		Db::query("TRUNCATE TABLE `".DBPRE."points_cart`");
		Db::query("TRUNCATE TABLE `".DBPRE."points_log`");
		Db::query("TRUNCATE TABLE `".DBPRE."points_order`");
		Db::query("TRUNCATE TABLE `".DBPRE."points_orderaddress`");
		Db::query("TRUNCATE TABLE `".DBPRE."points_ordergoods`");
		Db::query("TRUNCATE TABLE `".DBPRE."predeposit_cash`");
		Db::query("TRUNCATE TABLE `".DBPRE."predeposit_log`");
		Db::query("TRUNCATE TABLE `".DBPRE."predeposit_recharge`");
		Db::query("TRUNCATE TABLE `".DBPRE."album_class`");
		Db::query("TRUNCATE TABLE `".DBPRE."album_pic`");
		Db::query("TRUNCATE TABLE `".DBPRE."map`");
		Db::query("TRUNCATE TABLE `".DBPRE."refund_log`");
		Db::query("TRUNCATE TABLE `".DBPRE."return`");
		Db::query("TRUNCATE TABLE `".DBPRE."return_goods`");
		Db::query("TRUNCATE TABLE `".DBPRE."coupon`");
		Db::query("TRUNCATE TABLE `".DBPRE."gold_buy`");
		Db::query("TRUNCATE TABLE `".DBPRE."gold_log`");
		Db::query("TRUNCATE TABLE `".DBPRE."ztc_glodlog`");
		Db::query("TRUNCATE TABLE `".DBPRE."ztc_goods`");
		Db::query("TRUNCATE TABLE `".DBPRE."p_mansong`");
		Db::query("TRUNCATE TABLE `".DBPRE."p_mansong_apply`");
		Db::query("TRUNCATE TABLE `".DBPRE."p_mansong_quota`");
		Db::query("TRUNCATE TABLE `".DBPRE."p_mansong_rule`");
		Db::query("TRUNCATE TABLE `".DBPRE."p_xianshi`");
		Db::query("TRUNCATE TABLE `".DBPRE."p_xianshi_apply`");
		Db::query("TRUNCATE TABLE `".DBPRE."p_xianshi_goods`");
		Db::query("TRUNCATE TABLE `".DBPRE."p_xianshi_quota`");
		Db::query("TRUNCATE TABLE `".DBPRE."transport`");
		Db::query("TRUNCATE TABLE `".DBPRE."transport_extend`");
		Db::query("TRUNCATE TABLE `".DBPRE."sns_comment`");
		Db::query("TRUNCATE TABLE `".DBPRE."sns_friend`");
		Db::query("TRUNCATE TABLE `".DBPRE."sns_goods`");
		Db::query("TRUNCATE TABLE `".DBPRE."sns_sharegoods`");
		Db::query("TRUNCATE TABLE `".DBPRE."sns_sharestore`");
		Db::query("TRUNCATE TABLE `".DBPRE."sns_tracelog`");
		Db::query("TRUNCATE TABLE `".DBPRE."sns_visitor`");
		return true;
	}
	
		public function updateSetting1($param){
        if (empty($param)) {
            return false;
        }
        if (is_array($param)) {
            $this->table('member_level')->where(['ml_id'=>1])->update(['ml_commission'=>$param['vip_commission'],'ml_discount_ratio'=>$param['vip_ratio']/100]);
            $this->table('member_level')->where(['ml_id'=>2])->update(['ml_commission'=>$param['shopkeeper_commission'],'ml_discount_ratio'=>$param['shopkeeper_ratio']/100,'ml_direct_prize'=>$param['shopkeeper_direct_prize']]);
            $this->table('member_level')->where(['ml_id'=>3])->update(['ml_commission'=>$param['partner_commission'],'ml_discount_ratio'=>$param['partner_ratio']/100,'ml_direct_prize'=>$param['partner_direct_prize']]);
            $this->table('member_level')->where(['ml_id'=>4])->update(['ml_commission'=>$param['senior_partner_commission'],'ml_discount_ratio'=>$param['senior_partner_ratio']/100,'ml_direct_prize'=>$param['senior_partner_direct_prize']]);
            $this->table('member_level')->where(['ml_id'=>5])->update(['ml_commission'=>$param['senior_partner_commission'],'ml_discount_ratio'=>$param['senior_partner_ratio']/100]);
            dkcache('member_level');
            // @unlink(BASE_DATA_PATH.DS.'cache'.DS.'setting.php');
            return true;
        } else {
            return false;
        }
    }
	public function updateSetting2($param){
        if (empty($param)) {
            return false;
        }
        if (is_array($param)) {
            foreach ($param as $k => $v){
                $tmp = array();
                $tmp_k = array();
                $specialkeys_arr = array('statistics_code');
                $tmp_k['name'] = $k;
                $tmp['name'] = $k;
                $tmp['value'] = (in_array($k, $specialkeys_arr) ? $v : $v);
                $where = " name = '".$k."'";
                $setting_data = Db::select(['field'=>'name','table'=>'setting']);
                if(!in_array($tmp_k, $setting_data)){
                    $result = Db::insert('setting', $tmp);
                } else {
                    $result = Db::update('setting', $tmp, $where);
                }
                if ($result !== true){
                    return $result;
                }
            }
            dkcache('setting');
            // @unlink(BASE_DATA_PATH.DS.'cache'.DS.'setting.php');
            return true;
        } else {
            return false;
        }
    }
	public function updateSetting3($param){
        if (empty($param)) {
            return false;
        }
        if (is_array($param)) {
            $update['withdraw_poundage'] =$param['withdraw_poundage'];
            $update['withdraw_limit'] =$param['withdraw_limit'];
            $update['equity_start_buy_limit'] =$param['equity_start_buy_limit'];
            $update['equity_add_buy_limit'] =$param['equity_add_buy_limit'];
            $update['equity_exchange_ratio'] =$param['equity_exchange_ratio'];
            $this->table('member_system_set')->where(['id'=>1])->update($update);
            dkcache('member_system_set');
            // @unlink(BASE_DATA_PATH.DS.'cache'.DS.'setting.php');
            return true;
        } else {
            return false;
        }
    }


    public function updateSetting4($param){
        if (empty($param)) {
            return false;
        }
        if (is_array($param)) {
            $this->table('member_level')->where(['ml_id'=>1])->update(['ml_direct_vip_num'=>$param['shopkeeper_direct_vip_num'],'ml_team_vip_num'=>$param['shopkeeper_team_vip_num']]);
            $this->table('member_level')->where(['ml_id'=>2])->update(['ml_direct_vip_num'=>$param['partner_direct_vip_num'],'ml_team_vip_num'=>$param['partner_team_vip_num']]);
            $this->table('member_level')->where(['ml_id'=>3])->update(['ml_direct_vip_num'=>$param['senior_partner_direct_vip_num'],'ml_team_vip_num'=>$param['senior_partner_team_vip_num']]);
            $this->table('member_level')->where(['ml_id'=>4])->update(['ml_direct_vip_num'=>$param['strategic_partner_direct_vip_num']]);
            dkcache('member_level');
            // @unlink(BASE_DATA_PATH.DS.'cache'.DS.'setting.php');
            return true;
        } else {
            return false;
        }
    }

    public function updateSetting5($param){
        if (empty($param)) {
            return false;
        }
        if (is_array($param)) {
            $update['report_product_price'] =$param['report_product_price'];
            $this->table('member_system_set')->where(['id'=>1])->update($update);
            dkcache('member_system_set');
            // @unlink(BASE_DATA_PATH.DS.'cache'.DS.'setting.php');
            return true;
        } else {
            return false;
        }
    }
	
}
