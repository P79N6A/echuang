<?php
/**
 * 碰撞逻辑
 *
 */

defined('In33hao') or exit('Access Invild!');
class collisionLogic {
	// 碰撞会员信息
	private $_col_member_list = array();

	public function collisionStart() {
		$this->_traverseMemberExtend($this->_col_member_list);
		$this->_collisionRebate();
		$this->_clearNewPerformance();
	}

	/**
	 * _traverseMemberExtend 遍历会员接点信息
	 * @param  [type] $col_member_list [description]
	 * @return [type]                  [description]
	 */
	private function _traverseMemberExtend(&$col_member_list) {
		$condition = array();
		$model_member_extend = Model('member_extend');
		// 获取最大深度
		$max_depth = $model_member_extend->getMaxField('depth');
		for ($i = $max_depth - 1; $i >= 1; $i--) {
			$condition['depth'] = $i;
			$info = $model_member_extend->getMemberExtendList($condition, 'me_member_id');
			foreach ($info as $v1) {
				$arr = array();
				$member_id = $v1['me_member_id'];
				$access_info = $model_member_extend->getMemberExtendList(array('access_id' => $member_id), 'me_member_id,new_performance,surplus_performance', '', null, 'reg_time asc');
				if (!empty($access_info) && is_array($access_info)) {
					$arr['main_id'] = $member_id;
					if (count($access_info) == 1) {
						$access = $access_info[0];
						$arr['area0_id'] = $access['me_member_id'];
						$arr['area0_amount'] = floatFormat($access['new_performance'] + $access['surplus_performance']);
						$arr['area1_id'] = 0;
						$arr['area1_amount'] = 0;
					} elseif (count($access_info) == 2) {
						foreach ($access_info as $key => $v2) {
							$arr['area' . $key . '_id'] = $v2['me_member_id'];
							$arr['area' . $key . '_amount'] = floatFormat($v2['new_performance'] + $v2['surplus_performance']);
						}
					}
					$max_amount = max($arr['area0_amount'], $arr['area1_amount']);
					if ($max_amount > 0) {
						$col_member_list[] = $arr;
					}
				}
			}
		}
	}

	/**
	 * _collisionRebate 碰撞返利
	 * @return [type] [description]
	 */
	private function _collisionRebate() {
		try {
			Model()->beginTransaction();
			$list = $this->_col_member_list;
			$model_member_extend = Model('member_extend');
			// 双轨碰撞
			foreach ($list as $key => $v) {
				$area0_member_id = $v['area0_id'];
				$area1_member_id = $v['area1_id'];
				$member_id = $v['main_id'];
				// 碰撞金额
				$collision_amount = min($v['area0_amount'], $v['area1_amount']);
				// 返利金额
				$amount = floatFormat($collision_amount * C('collision_rebate_percentage') / 100);

				// 双轨碰撞
				if ($area0_member_id && $area1_member_id) {
					// 碰撞剩余金额
					$surplus_amount = abs($v['area0_amount'] - $v['area1_amount']);
					$info = Model('member_extend')->getMemberExtendInfo(array('me_member_id' => $member_id), 'member_name,member_state', 'union');
					// 主会员状态正常
					if ($amount > 0 && $info['member_state']) {
						$data = array();
						$data['member_id'] = $member_id;
						$data['amount'] = $amount;
						$data['member_name'] = $info['member_name'];
						$data['bean_type'] = 'extend';
						// 增加左右碰撞金额描述
						// $data['desc'] = "";
						// 碰撞返利
						$return = $model_member_extend->changeBean('rebate', $data);
						// 碰撞编号
						$sn = $return['lg_sn'];
						$cl_log_data = array();
						$cl_log_data['cl_main_id'] = $member_id;
						$cl_log_data['cl_area0_id'] = $v['area0_id'];
						$cl_log_data['cl_area1_id'] = $v['area1_id'];
						$cl_log_data['cl_area0_amount'] = $v['area0_amount'];
						$cl_log_data['cl_area1_amount'] = $v['area1_amount'];
						$cl_log_data['cl_amount'] = $collision_amount;
						$cl_log_data['cl_sn'] = $sn;
						$cl_log_data['cl_addtime'] = TIMESTAMP;

						// 插入碰撞记录
						$model_member_extend->addCollisionLog($cl_log_data);
					}

					// 清空双轨新增业绩
					$model_member_extend->setFieldMemberExtend(array('me_member_id' => $area0_member_id), 'new_performance', 0);
					$model_member_extend->setFieldMemberExtend(array('me_member_id' => $area1_member_id), 'new_performance', 0);

					// 双轨剩余业绩处理
					if ($v['area0_amount'] >= $v['area1_amount']) {
						$model_member_extend->setFieldMemberExtend(array('me_member_id' => $area0_member_id), 'surplus_performance', $surplus_amount);
						$model_member_extend->setFieldMemberExtend(array('me_member_id' => $area1_member_id), 'surplus_performance', 0);
					} else {
						$model_member_extend->setFieldMemberExtend(array('me_member_id' => $area0_member_id), 'surplus_performance', 0);
						$model_member_extend->setFieldMemberExtend(array('me_member_id' => $area1_member_id), 'surplus_performance', $surplus_amount);
					}

				} else {
					if ($area0_member_id) {
						$model_member_extend->setFieldMemberExtend(array('me_member_id' => $area0_member_id), 'new_performance', 0);
						$model_member_extend->setFieldMemberExtend(array('me_member_id' => $area0_member_id), 'surplus_performance', $v['area0_amount']);
					}
					if ($area1_member_id) {
						$model_member_extend->setFieldMemberExtend(array('me_member_id' => $area1_member_id), 'new_performance', 0);
						$model_member_extend->setFieldMemberExtend(array('me_member_id' => $area1_member_id), 'surplus_performance', $v['area1_amount']);
					}
				}
			}
			Model()->commit();
			return true;
		} catch (Exception $exception) {
			Model()->rollback();
			return array('error' => $exception->getMessage());
		}

	}

	/**
	 * _clearNewPerformance 清空新增业绩
	 * @return [type] [description]
	 */
	private function _clearNewPerformance() {
		Model('member_extend')->clearAllMemberPerformance();
	}

}