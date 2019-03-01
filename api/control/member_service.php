<?php
/**
 * 服务中心模块
 *
 */
 header("Access-Control-Allow-Origin: *");
class member_serviceControl extends apiEncryptMemberControl {
	public function __construct() {
		parent::__construct();
	}

	/**
	 * service_checkOp 服务中心检测
	 * @return [type] [description]
	 */
	public function service_checkOp() {
		if ($this->member_info['is_service']) {
			responseApiJson(0, "您已是服务中心");
		} else {
			$model_service = Model('service');
			$member_id = $this->member_info['member_id'];
			$s_info = $model_service->getServiceLogInfo(array('slg_member_id' => $member_id));
			if (!empty($s_info)) {
				responseApiJson(0, "你的申请正在审核中...");
			}
			responseApiJson(1, "确定申请服务中心");
		}
	}

	/**
	 * service_applyOp 服务中心申请
	 * @return [type] [description]
	 */
	public function service_applyOp() {
		$model_service = Model('service');
		$service_data = array();
		$service_data['slg_member_id'] = $this->member_info['member_id'];
		$service_data['slg_member_name'] = $this->member_info['member_name'];
		$service_data['slg_addtime'] = TIMESTAMP;
		try {
			$model_service->addServiceLog($service_data);
			responseApiJson(1, "服务中心申请成功,请等待审核...");
		} catch (Exception $exception) {
			responseApiJson(0, "服务中心申请失败");
		}
	}

}