<?php
/**
 * 我的地址
 *
 *  (c) ()
 * @license    
 * @link       
 * @since      
 */

defined('In33hao') or exit('Access Invalid!');

class member_addressControl extends apiEncryptMemberControl {

	public function __construct() {
		parent::__construct();
		$this->model_address = Model('address');
	}

	/**
	 * 地址列表
	 */
	public function pri_addressListOp() {
		$address_list['address_list'] = Model('address')->getAddressList(array('member_id' => $this->member_info['member_id']));
		responseApiJson(1, '', $address_list);
	}

	/**
	 * 地址详细信息
	 */
	public function pri_addressInfoOp() {
		$address_id = intval($_POST['address_id']);

		$condition = array();
		$condition['address_id'] = $address_id;
		$address_info = $this->model_address->getAddressInfo($condition);
		if (!empty($address_id) && $address_info['member_id'] == $this->member_info['member_id']) {
			responseApiJson(1, '', $address_info);
		} else {
			responseApiJson(0, '地址信息不存在', '');
		}
	}

	/**
	 * 删除地址
	 */
	public function pri_addressDelOp() {
		$address_id = intval($_GET['address_id']);

		$condition = array();
		$condition['address_id'] = $address_id;
		$condition['member_id'] = $this->member_info['member_id'];
		$this->model_address->delAddress($condition);
		responseApiJson(1, '删除成功', '');
	}

	/**
	 * 新增地址
	 */
	public function pri_addressAddOp() {
		$address_info = $this->_address_valid();

		$result = $this->model_address->addAddress($address_info);
		if ($result) {
			responseApiJson(1, '添加成功', '');
		} else {
			responseApiJson(0, '添加失败', '');
		}
	}

	/**
	 * 编辑地址
	 */
	public function pri_addressEditOp() {
		$address_id = intval($_POST['address_id']);

		//验证地址是否为本人
		$address_info = $this->model_address->getOneAddress($address_id);
		if ($address_info['member_id'] != $this->member_info['member_id']) {
			responseApiJson(0, '参数错误', '');
		}

		$address_info = $this->_address_valid();
		if ($_POST['is_default']) {
			$this->model_address->editAddress(array('is_default' => 0), array('member_id' => $this->member_info['member_id'], 'is_default' => 1));
		}
		$result = $this->model_address->editAddress($address_info, array('address_id' => $address_id, 'member_id' => $this->member_info['member_id']));
		if ($result) {
			responseApiJson(1, '保存成功', '');
		} else {
			responseApiJson(0, '保存失败', '');
		}
	}

	/**
	 * 验证地址数据
	 */
	private function _address_valid() {
		$obj_validate = new Validate();
		$obj_validate->validateparam = array(
			array("input" => $_POST["true_name"], "require" => "true", "message" => '姓名不能为空'),
			array("input" => $_POST["area_info"], "require" => "true", "message" => '地区不能为空'),
			array("input" => $_POST["address"], "require" => "true", "message" => '地址不能为空'),
			array("input" => $_POST['mob_phone'], 'require' => 'true', 'message' => '联系方式不能为空'),
		);
		$error = $obj_validate->validate();
		if ($error != '') {
			responseApiJson(0, $error, '');
		}

		$data = array();
		$data['member_id'] = $this->member_info['member_id'];
		$data['true_name'] = $_POST['true_name'];
		$data['area_id'] = intval($_POST['area_id']);
		$data['city_id'] = intval($_POST['city_id']);
		$data['area_info'] = $_POST['area_info'];
		$data['address'] = $_POST['address'];
		$data['tel_phone'] = $_POST['tel_phone'];
		$data['mob_phone'] = $_POST['mob_phone'];
		$data['is_default'] = $_POST['is_default'] ? 1 : 0;
		if ($_POST['is_default']) {
			$this->model_address->editAddress(array('is_default' => 0), array('member_id' => $this->member_info['member_id'], 'is_default' => 1));
		}
		return $data;
	}

}
