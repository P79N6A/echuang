<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/21 0021
 * Time: 下午 18:09
 */
defined('In33hao') or exit('Access Invild!');
class member_system_editControl extends SystemControl
{
    public function __construct()
    {
        parent::__construct();
        Language::read('member_center');
    }

    private $links = array(
        array('url' => 'act=member_system_edit&op=income_setting', 'lang' => 'income_setting'),
        array('url' => 'act=member_system_edit&op=up_level_manage', 'lang' => 'up_level_manage'),
    );

    /**
     * indexOp 高级设置入口
     * @return [type] [description]
     */
    public function indexOp() {
        $this->income_settingOp();
    }

    public function income_settingOp() {
        $model_setting = Model('setting');
        if (chksubmit()) {
            $data = array();
            $data['f_estimate_integral'] = intval(trim($_POST['f_estimate_integral']));
            $data['f_reward_integral'] = intval(trim($_POST['f_reward_integral']));
            $data['f_deduction_integral'] = intval(trim($_POST['f_deduction_integral']));
            $data['f_superior_deduction_integral'] = intval(trim($_POST['f_superior_deduction_integral']));
            $data['f_service_charge'] = intval(trim($_POST['f_service_charge']));
            $data['f_vip_service_charge'] = intval(trim($_POST['f_vip_service_charge']));
            $result = $model_setting->updateSetting($data);
            if ($result) {
                manualDelCacheFile('setting');
                showMessage('参数设置成功', '', '', 'succ');
            } else {
                showMessage('参数设置失败', '', '', 'error');
            }
        }
        $same_senior_partner_commission = $model_setting->getListSetting();
        Tpl::output('s_info', $same_senior_partner_commission);
        Tpl::output('other_setting', $same_senior_partner_commission);
        Tpl::setDirquna('member_center');
        Tpl::showpage('income_setting.index');
    }

    public function up_level_manageOp(){
        $model_level = Model('level');
        $model_setting = Model('setting');
        if (chksubmit()) {
            $data = array();
            $data1['report_product_price'] = floatval(trim($_POST['report_product_price']));
            $data['shopkeeper_direct_vip_num'] = intval(trim($_POST['shopkeeper_direct_vip_num']));
            $data['shopkeeper_team_vip_num'] = intval(trim($_POST['shopkeeper_team_vip_num']));
            $data['partner_direct_vip_num'] = intval(trim($_POST['partner_direct_vip_num']));
            $data['partner_team_vip_num'] = intval(trim($_POST['partner_team_vip_num']));
            $data['senior_partner_direct_vip_num'] = intval(trim($_POST['senior_partner_direct_vip_num']));
            $data['senior_partner_team_vip_num'] = intval(trim($_POST['senior_partner_team_vip_num']));
            $data['strategic_partner_direct_vip_num'] = intval(trim($_POST['strategic_partner_direct_vip_num']));
            $result1 = $model_setting->updateSetting4($data);
            $result2 = $model_setting->updateSetting5($data1);
            if ($result1 && $result2) {
                manualDelCacheFile('setting');
                showMessage('参数设置成功', '', '', 'succ');
            } else {
                showMessage('参数设置失败', '', '', 'error');
            }
        }
        Tpl::output('top_link', $this->sublink($this->links, 'up_level_manage'));
//        $setting_list = $model_setting->getListSetting();
        $setting_list = $model_level->getMemberSettingList();
        $same_senior_partner_commission = $model_setting->getListSetting();
        $member_system_setting = $model_level->getMemberSystemSetting();
        Tpl::output('s_info', $setting_list);
        Tpl::output('other_setting', $same_senior_partner_commission);
        Tpl::output('member_system_setting', $member_system_setting);
        Tpl::setDirquna('member_center');
        Tpl::showpage('up_level_manage.index');
    }
}