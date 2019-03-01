<?php
/**
 * 我的商城
 *
 */

defined('In33hao') or exit('Access Invalid!');

class member_indexControl extends apiEncryptMemberControl {

	public function __construct() {
		parent::__construct();
	}
	/**
	 * forgetPasswordOp 修改密码(登录和支付)
	 * @return [type] [description]
	 */
	public function modify_passwordOp() {
		if (empty($_POST['mobile'])) {
			responseApiJson(0, "请输入手机号");
		}
		if (empty($_POST['captcha'])) {
			responseApiJson(0, "请输入验证码");
		}

		if (empty($_POST['password'])) {
			responseApiJson(0, "请输入新密码");
		}
		if (empty($_POST['confirm_password'])) {
			responseApiJson(0, "请输入确认密码");
		}
		if ($_POST['confirm_password'] != $_POST['password']) {
			responseApiJson(0, "两次输入密码不正确");
		}
		$mobile = trim($_POST['mobile']);
		$captcha = trim($_POST['captcha']);
		$password = trim($_POST['password']);
		$confirm_password = trim($_POST['confirm_password']);
		$type = $_POST['type'];
		$logic_connect_api = Logic('connect_api');
		// 检查手机号是否已被注册
		$model_member = Model('member');
		$info = $model_member->getMemberInfo(array('member_mobile' => $mobile));
		if (intval($type) == 3) {
			$result = $logic_connect_api->checkSmsCaptcha($mobile, $captcha, 3);
			if ($result['state'] == false) {
				responseApiJson(0, "验证码错误或已过期");
			}
			if (!empty($info)) {
				$model_member->editMember(array('member_id' => $info['member_id']), array('member_passwd' => md5($password)));
				responseApiJson(1, "密码修改成功");
			} else {
				responseApiJson(0, "手机号未注册");
			}
		}
		if (intval($type) == 4) {
			$result = $logic_connect_api->checkSmsCaptcha($mobile, $captcha, 4);
			if ($result['state'] == false) {
				responseApiJson(0, "验证码错误或已过期");
			}
			if (!empty($info)) {
				if ($password == $confirm_password) {
					$model_member->editMember(array('member_id' => $info['member_id']), array('member_paypwd' => md5($confirm_password)));
					responseApiJson(1, "支付密码保存成功");
				}
			} else {
				responseApiJson(0, "手机号未注册");
			}
		}
	}

	/**
	 * 我的商城
	 */
	public function indexOp() {
        $member_info = array();
        $member_info['user_name'] = $this->member_info['member_name'];
        if (empty($this->member_info['member_avatar'])) {
            $member_info['avatar'] = UPLOAD_SITE_URL . DS . ATTACH_COMMON . DS . C('default_user_portrait');
        } else {
            $member_info['avatar'] = UPLOAD_SITE_URL . '/' . ATTACH_AVATAR . '/' . $this->member_info['member_avatar'];
        }
        $member_level = Model('level')->getOneMemberLevelName(intval($this->member_info['member_level']));
        $member_info['level_name'] = $member_level;
        $member_info['favorites_store'] = Model('favorites')->getStoreFavoritesCountByMemberId($this->member_info['member_id']);
		$member_info['favorites_goods'] = Model('favorites')->getGoodsFavoritesCountByMemberId($this->member_info['member_id']);
		// 交易提醒
		$model_order = Model('order');

		$sql_order_nopay_count = "SELECT COUNT(*) as count from dfn_orders WHERE buyer_id='" . $this->member_info['member_id'] . "' and order_state=10";
		$sql_order_noreceipt_count = "SELECT COUNT(*) as count from dfn_orders WHERE buyer_id='" . $this->member_info['member_id'] . "' and order_state=20";
		$sql_order_notakes_count = "SELECT COUNT(*) as count from dfn_orders WHERE buyer_id='" . $this->member_info['member_id'] . "' and order_state=30";
		$sql_order_noeval_count = "SELECT COUNT(*) as count from dfn_orders WHERE buyer_id='" . $this->member_info['member_id'] . "' and order_state=40 and evaluation_state=0";
		$order_nopay_count = $model_order->query($sql_order_nopay_count);
		$order_noreceipt_count = $model_order->query($sql_order_noreceipt_count);
		$order_notakes_count = $model_order->query($sql_order_notakes_count);
		$order_order_noeval_count = $model_order->query($sql_order_noeval_count);
		$member_info['order_nopay_count'] = $order_nopay_count[0]['count'];
		$member_info['order_noreceipt_count'] = $order_noreceipt_count[0]['count'];
		$member_info['order_notakes_count'] = $order_notakes_count[0]['count'];
		$member_info['order_noeval_count'] = $order_order_noeval_count[0]['count'];

//        $member_info['order_nopay_count'] = $model_order->getOrderCountByID('buyer', $this->member_info['member_id'], 'NewCount');
		//        $member_info['order_noreceipt_count'] = $model_order->getOrderCountByID('buyer', $this->member_info['member_id'], 'SendCount');
		//        $member_info['order_notakes_count'] = $model_order->getOrderCountByID('buyer', $this->member_info['member_id'], 'TakesCount');
		//        $member_info['order_noeval_count'] = $model_order->getOrderCountByID('buyer', $this->member_info['member_id'], 'EvalCount');

		// 售前退款
		$condition = array();
		$condition['buyer_id'] = $this->member_info['member_id'];
		$condition['refund_state'] = array('lt', 3);
		$member_info['return'] = Model('refund_return')->getRefundReturnCount($condition);

		responseApiJson(1, '', array('member_info' => $member_info));
	}
	//实名认证
	public function authen_truenameOp() {
		if ($_POST) {
			$verify_arr = array();
			$verify_arr['member_id'] = $this->member_info['member_id'];
			$verify_arr['member_truename'] = trim($_POST['true_name']);
			$verify_arr['member_id_num'] = trim($_POST['id_card_num']);
			$verify_arr['id_card_front'] = trim($_POST['id_card_front1']);
			$verify_arr['id_card_back'] = trim($_POST['id_card_back1']);
			$verify_arr['id_card_hand'] = trim($_POST['id_card_hand1']);
			$verify_arr['verify_state'] = 20;
			$verify_arr['add_time'] = TIMESTAMP;
			$result = Model('member_verify')->insert($verify_arr);

			if ($result) {
				Model('member')->editMember(array('member_id' => $this->member_info['member_id']), array('member_verify' => 20));
				responseApiJson(1, '资料已提交，等待审核', '');
			} else {
				responseApiJson(0, '资料提交失败', '');
			}
		} else {
			$member_id = $this->member_info['member_id'];
			$model_member_verify = Model('member_verify');
			$verify_info = $model_member_verify->where(array('member_id' => $member_id))->find();
			if (!empty($verify_info)) {
				if ($verify_info['verify_state'] == 20) {
					$verify_state = "未审核";
				}
				if ($verify_info['verify_state'] == 30) {
					$verify_state = "认证失败";
				}
				if ($verify_info['verify_state'] == 40) {
					$verify_state = "认证成功";
				}
				responseApiJson(1, '', array('verify_state' => $verify_state));
			} else {
				responseApiJson(1, '', array('verify_state' => '未认证'));
			}
		}
	}
	//上传用户头像信息
	public function upload_user_imageOp() {
		if ($_POST) {
			$user_avatar = $_POST['user_avatar'];
			if (!empty($user_avatar)) {
				$conidition['member_id'] = $this->member_info['member_id'];
				$param['member_avatar'] = $user_avatar;
				$result = Model('member')->editMember($conidition, $param);
				if ($result) {
					responseApiJson(1, '更改头像成功', '');
				} else {
					responseApiJson(0, '更改头像失败', '');
				}
			} else {
				responseApiJson(0, '请先上传图片', '');
			}
		} else {
			if (empty($this->member_info['member_avatar'])) {
				$user_info['user_avatar'] = UPLOAD_SITE_URL . DS . ATTACH_COMMON . DS . C('default_user_portrait');
			} else {
				$user_info['user_avatar'] = UPLOAD_SITE_URL . '/' . ATTACH_AVATAR . '/' . $this->member_info['member_avatar'];
			}
			$user_info['member_truename'] = $this->member_info['member_name'];
			if (empty($this->member_info['member_mobile'])) {
				$user_info['member_id_num'] = "";
			} else {
				$user_info['member_id_num'] = $this->member_info['member_mobile'];
			}
			responseApiJson(1, '', $user_info);
		}
	}
	//接点人族谱图
	public function buildBinaryOp() {
		$binary_tree = Model('member_extend')->buildBinaryTreeArr($this->member_info['member_id']);
		$performance['performance1'] = $binary_tree['performance1'];
		$performance['performance2'] = $binary_tree['performance2'];
		$performance['surplus1'] = $binary_tree['surplus1'];
		$performance['surplus2'] = $binary_tree['surplus2'];
		if ($_POST) {
			$member_id = $_POST['member_id'];
			$new_arr = $this->_build_arr($member_id);
		} else {
			$new_arr = $this->_build_arr();
		}
		responseApiJson(1, '', array('map_arr' => $new_arr, 'performance' => $performance));
	}

	public function _build_arr($member_id = "") {
		if (empty($member_id)) {
			$binary_tree = Model('member_extend')->buildBinaryTreeArr($this->member_info['member_id']);
		} else {
			$binary_tree = Model('member_extend')->buildBinaryTreeArr($member_id, false);
		}
		if (!empty($binary_tree)) {
			$level1_arr['member_id'] = $binary_tree['member_id'];
			$level1_arr['member_mobile'] = $binary_tree['member_mobile'];
			$new_arr[0][] = $level1_arr;
			if (!empty($binary_tree['child'])) {
				foreach ($binary_tree['child'] as $key => $value) {
					if ($value['member_id'] == 0) {
						$level2_arr['member_id'] = "";
						$level2_arr['member_mobile'] = "";
						$member_info = Model('member')->getMemberInfoByID($value['access_id']);
						$level2_arr['access_mobile'] = $member_info['member_mobile'];
						$new_arr[1][] = $level2_arr;
					} else {
						$level2_arr['member_id'] = $value['member_id'];
						$level2_arr['member_mobile'] = $value['member_mobile'];
						$new_arr[1][] = $level2_arr;
					}
					if (!empty($value['child'])) {
						foreach ($value['child'] as $k => $v) {
							if ($v['member_id'] == 0) {
								$level3_arr['member_id'] = "";
								$level3_arr['member_mobile'] = "";
								$member_info1 = Model('member')->getMemberInfoByID($v['access_id']);
								$level3_arr['access_mobile'] = $member_info1['member_mobile'];
								$new_arr[2][] = $level3_arr;
							} else {
								$level3_arr['member_id'] = $v['member_id'];
								$level3_arr['member_mobile'] = $v['member_mobile'];
								$new_arr[2][] = $level3_arr;
							}
						}
					} else {
						$level3_arr['access_mobile'] = "";
						$level3_arr['member_id'] = "";
						$level3_arr['member_mobile'] = "";
						$new_arr[2][] = $level3_arr;
						$new_arr[2][] = $level3_arr;
					}
				}
			}
		}
		return $new_arr;
	}

	//会员库存信息
	public function member_inventoryOp(){
//	    var_dump($this->member_info);
        $member_id = $this->member_info['member_id'];
        $member_model = Model('member');
        $condition['member_id'] = $member_id;
        $member_inventory = $member_model->getMemberInventory($condition);
        responseApiJson(1,'',$member_inventory);
    }

   //会员等级计算加盟费
    public function franchiseFeeOp(){
	    $member_level = $this->member_info['member_level'];
	    $franchise_fee_arr = Model('member')->getFranchiseFee($member_level);
//	    var_dump($franchise_fee_arr);die();
        responseApiJson(1,'',$franchise_fee_arr);
    }


    public function memberJoinOp(){
        $member_id = $_POST['member_id'];
        $franchise_fee = $_POST['franchise_fee'];
        $res = Logic('member_level')->joinIn($member_id,$franchise_fee);
        if ($res){
            Model('member_extend')->changeMemberBalance('');
        }
        var_dump($res);
	}
}
