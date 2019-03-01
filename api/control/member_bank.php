<?php
/**
 * 我的银行卡
 *
 *  (c) ()
 * @license
 * @link
 * @since
 */
header("Access-Control-Allow-Origin: *");
defined('In33hao') or exit('Access Invalid!');

class member_bankControl extends apiEncryptMemberControl {

	public function __construct() {
		parent::__construct();
		$this->model_bank = Model('bank');
	}

	/**
	 * 银行卡列表
	 */
	public function pri_bankListOp() {
		$member_info['mb_member_id'] = $this->member_info['member_id'];
		$bank_list = Model('bank')->getMemberBankList($member_info);
		if (empty($bank_list)) {
			responseApiJson(1, '', array('bank_list' => array()));
		} else {
			responseApiJson(1, '', array('bank_list' => $bank_list));
		}
	}

	/**
	 * 银行卡详细信息
	 */
	public function pri_bankInfoOp() {
		$bank_id = intval($_POST['bank_id']);

		$condition = array();
		$condition['mb_id'] = $bank_id;
		$bank_info = $this->model_bank->getMemberBankInfo($condition);
		if (!empty($bank_id) && $bank_info['mb_member_id'] == $this->member_info['member_id']) {
			responseApiJson(1, '', $bank_info);
		} else {
			responseApiJson(0, '银行卡信息不存在', '');
		}
	}

	/**
	 * 删除银行卡
	 */
	public function pri_bankDelOp() {
		$bank_id = intval($_GET['bank_id']);

		$condition = array();
		$condition['mb_id'] = $bank_id;
		$result = Model('bank')->delMemberBank($condition);
		if ($result) {
			responseApiJson(1, '删除成功', '');
		} else {
			responseApiJson(0, '删除失败', '');
		}
	}

	/**
	 * 新增银行卡
	 */
	public function pri_bankAddOp() {
		$bank_info = $this->_bank_valid();
		$is_have_bank = $this->model_bank->getMemberBankInfo(array('mb_member_id' => $this->member_info['member_id']));
		if ($is_have_bank) {
			responseApiJson(0, '只能绑定一张银行卡', '');
		} else {
			$result = $this->model_bank->addMemberBank($bank_info);
			if ($result) {
				responseApiJson(1, '添加成功', '');
			} else {
				responseApiJson(0, '添加失败', '');
			}
		}
	}

	/**
	 * 编辑银行卡
	 */
	public function pri_bankEditOp() {
		$bank_id = intval($_POST['bank_id']);

		//验证银行卡是否为本人
		$bank_info = $this->model_bank->getOnebank($bank_id);
		if ($bank_info['member_id'] != $this->member_info['member_id']) {
			responseApiJson(0, '参数错误', '');
		}

		$bank_info = $this->_bank_valid();
		if ($_POST['is_default']) {
			$this->model_bank->editbank(array('is_default' => 0), array('member_id' => $this->member_info['member_id'], 'is_default' => 1));
		}
		$result = $this->model_bank->editbank($bank_info, array('bank_id' => $bank_id, 'member_id' => $this->member_info['member_id']));
		if ($result) {
			responseApiJson(1, '保存成功', '');
		} else {
			responseApiJson(0, '保存失败', '');
		}
	}

	/**
	 * 验证银行卡数据
	 */
	private function _bank_valid() {
		$obj_validate = new Validate();
		$obj_validate->validateparam = array(
			array("input" => $_POST["account_name"], "require" => "true", "message" => '开户人姓名不能为空'),
			array("input" => $_POST["account_bank_name"], "require" => "true", "message" => '开户行不能为空'),
			array("input" => $_POST["account_branch"], "require" => "true", "message" => '支行不能为空'),
			array("input" => $_POST['account_num'], 'require' => 'true', 'message' => '银行卡账号不能为空'),
		);
		$error = $obj_validate->validate();
		if ($error != '') {
			responseApiJson(0, $error, '');
		}

		$data = array();
		$data['mb_member_id'] = $this->member_info['member_id'];
		$data['mb_account_name'] = $_POST["account_name"];
		$data['mb_account_bank_name'] = $_POST["account_bank_name"];
		$data['mb_account_branch'] = $_POST["account_branch"];
		$data['mb_account_num'] = $_POST['account_num'];
		$data['mb_addtime'] = time();
		return $data;
	}
}
