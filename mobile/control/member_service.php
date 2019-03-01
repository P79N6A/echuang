<?php
/**
 * 服务中心模块
 *
 */
class member_serviceControl extends mobileMemberControl {
	public function __construct() {
		parent::__construct();
	}

	/**
	 * serviceCheckOp 服务中心检测
	 * @return [type] [description]
	 */
	public function serviceCheckOp() {
		if ($this->member_info['is_service']) {
			output_data(array("service" => 1, 'msg' => "您已是服务中心"));
		} else {
			$s_info = Model('service')->getServiceLogInfo(array('slg_member_id' => $this->member_info['member_id']));
			if (!empty($s_info)) {
				output_data(array('service' => 2, 'msg' => "你的申请正在审核中..."));
			} else {
				output_data(array('service' => 0, 'msg' => "确定申请服务中心"));
			}
		}
	}

	/**
	 * serviceApplyOp 服务中心申请
	 * @return [type] [description]
	 */
	public function serviceApplyOp() {
		$service_data['slg_member_id'] = $this->member_info['member_id'];
		$service_data['slg_member_name'] = $this->member_info['member_name'];
		$service_data['slg_addtime'] = TIMESTAMP;
		try {
			Model('service')->addServiceLog($service_data);
			output_data(array('msg' => "服务中心申请成功,请等待审核..."));
		} catch (Exception $exception) {
			output_data(array('msg' => "服务中心申请失败"));
		}
	}

}