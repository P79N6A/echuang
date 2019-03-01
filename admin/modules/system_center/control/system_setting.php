<?php
/**
 * 平台设置
 *
 */
defined('In33hao') or exit('Access Invild!');

class system_settingControl extends SystemControl {
	public function __construct() {
		parent::__construct();
	}

	public function indexOp() {
		$model_setting = Model('setting');
		if (chksubmit()) {
			$data = array();
			$data['site_status'] = $_POST['site_status'];
			if ($_POST['site_status'] == 0) {
				$data['closed_reason'] = trim($_POST['closed_reason']);
			}
			$result = $model_setting->updateSetting($data);
			if ($result) {
				showMessage('参数设置成功', '', '', 'succ');
			} else {
				showMessage('参数设置失败', '', '', 'error');
			}
		}
		$setting_list = $model_setting->getListSetting();
		Tpl::output('s_info', $setting_list);
		Tpl::setDirquna('member_center');
		Tpl::showpage('system_setting.index');
	}
}