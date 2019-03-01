<?php
/**
 * 服务中心模块
 *
 */
defined('In33hao') or exit('Access Invild!');
class serviceControl extends BaseMemberControl {
	public function __construct() {
		parent::__construct();
	}

	public function indexOp() {
		$this->service_applyOp();
	}

	/**
	 * service_applyOp 服务中心申请
	 * @return [type] [description]
	 */
	public function service_applyOp() {
		$model_service = Model('service');
		$member_id = $this->member_info['member_id'];
		if (chksubmit()) {
			$slg_log_data = array();
			$slg_log_data['slg_member_id'] = $member_id;
			$slg_log_data['slg_member_name'] = $this->member_info['member_name'];
			$slg_log_data['slg_addtime'] = TIMESTAMP;
			try {
				$model_service->addServiceLog($slg_log_data);
				showMessage('服务中心申请成功,请等待审核。。。', '', '', 'succ');
			} catch (Exception $exception) {
				showMessage('申请失败', '', '', 'error');
			}
		} else {
			$s_info = $model_service->getServiceLogInfo(array('slg_member_id' => $member_id));

			if (empty($s_info)) {
				$this->_profile_menu('apply', 'service_apply');
				Tpl::showpage('service.service_apply');
			} else {
				$mobileArr = Model('member_extend')->getMemberMobileArr();
				Tpl::output('m_info', $mobileArr);
				Tpl::output('s_info', $s_info);
				$this->_profile_menu('list', 'service_apply');
				Tpl::showpage('service.service_list');
			}
		}

	}

	/**
	 * _profile_menu
	 * @param  string $menu_key [description]
	 * @return [type]           [description]
	 */
	private function _profile_menu($menu_type, $menu_key = '') {
		if ($menu_type == 'apply') {
			$menu_arr = array(
				1 => array('menu_key' => 'service_apply', 'menu_name' => '服务器中心申请', 'menu_url' => 'index.php?act=service&op=service_apply'),
			);
		} else {
			$menu_arr = array(
				1 => array('menu_key' => 'service_apply', 'menu_name' => "服务中心申请状态", 'menu_url' => 'index.php?act=service&op=service_apply'),
			);
		}

		Tpl::output('member_menu', $menu_arr);
		Tpl::output('menu_key', $menu_key);
	}
}