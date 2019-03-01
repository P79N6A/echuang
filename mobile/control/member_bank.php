<?php
/**
 * 会员银行卡
 */
class member_bankControl extends mobileMemberControl {

	// 构造函数
	public function __construct() {
		parent::__construct();
	}

	/**
	 * memberBankListOp 会员银行账号列表
	 * @return [type] [description]
	 */
	public function memberBankListOp() {
		$bank_list = Model('bank')->getMemberBankList(array('mb_member_id' => $this->member_info['member_id']));
		foreach ($bank_list as $key => $v) {
			$bank_list[$key]['bank'] = $v['mb_account_bank_name'] . '(' . formatBankCardNo($v['mb_account_num']) . ')';
			$bank_list[$key]['bank_account_num'] = formatBankCardNo($v['mb_account_num']);
		}
		output_data(array('list' => $bank_list));
	}

	/**
	 * memberBankInfoDetailOp 会员银行账号详情
	 * @return [type] [description]
	 */
	public function memberBankInfoDetailOp() {
		$validate = new Validate();
		$validate->validateparam = array(
			array('input' => $_POST['bank_id'], 'require' => "true", 'message' => "缺少bank_id参数"),
		);

		$error = $validate->validate();
		if ($error != '') {
			output_error($error);
		}

		$bank_id = intval($_POST['bank_id']);
		$condition['mb_id'] = $bank_id;
		$bank_info = Model('bank')->getMemberBankInfo(array('mb_id' => $bank_id, 'mb_member_id' => $this->member_info['member_id']));

		if (!empty($bank_info)) {
			$bank_info['bank_num'] = formatBankCardNo($bank_info['mb_account_num']);
			output_data(array('info' => $bank_info));
		} else {
			output_error("银行卡信息不存在");
		}
	}

	/**
	 * memberBankAddOp 会员银行卡添加
	 * @return [type] [description]
	 */
	public function memberBankAddOp() {
		$validate = new Validate();
		$validate->validateparam = array(
			array('input' => $_POST['account_name'], 'require' => "true", 'message' => "开户人姓名不能为空"),
			array('input' => $_POST['bank'], 'require' => "true", 'message' => "未选择开户行"),
			array('input' => $_POST['account_branch'], 'require' => "true", 'message' => "支行不能为空"),
			array('input' => $_POST['account_num'], 'require' => "true", 'message' => "银行卡账号不能为空"),
		);
		$error = $validate->validate();
		if ($error != '') {
			output_error($error);
		}
		$bank_info = Model('bank')->getMemberBankInfo(array('mb_member_id' => $this->member_info['member_id']));
		if (!empty($bank_info)) {
			output_error("只能绑定一张银行卡");
		}

		$bank = Model('bank')->getBankInfo(array('bank_id' => $_POST['bank'], 'bank_state' => 1));
		if (empty($bank)) {
			output_error("不支持当前选择的银行");
		}
		$data['mb_member_id'] = $this->member_info['member_id'];
		$data['mb_account_name'] = trim($_POST['account_name']);
		$data['mb_account_bank_name'] = $bank['bank_name'];
		$data['mb_account_branch'] = $_POST['account_branch'];
		$data['mb_account_num'] = $_POST['account_num'];
		$data['mb_addtime'] = TIMESTAMP;

		$result = Model('bank')->addMemberBank($data);
		if ($result) {
			output_data(1);
		} else {
			output_error("银行卡添加失败");
		}
	}

	/**
	 * memberBankDeleteOp 删除会员银行卡
	 * @return [type] [description]
	 */
	public function memberBankDeleteOp() {
		$validate = new Validate();
		$validate->validateparam = array(
			array('input' => $_POST['bank_id'], 'require' => "true", 'message' => "缺少bank_id参数"),
		);
		$error = $validate->validate();
		if ($error != '') {
			output_error($error);
		}

		$bank_id = intval($_POST['bank_id']);
		// 验证是否是本人
		$bank_info = Model('bank')->getMemberBankInfo(array('mb_id' => $bank_id, 'mb_member_id' => $this->member_info['member_id']));
		if (empty($bank_info)) {
			output_error("银行卡不存在");
		}
		$result = Model('bank')->delMemberBank(array('mb_id' => $bank_id));
		if ($result) {
			output_data(1);
		} else {
			output_error("银行卡删除失败");
		}
	}

	/**
	 * memberBankEditOp 会员银行卡编辑
	 * @return [type] [description]
	 */
	public function memberBankEditOp() {
		$validate = new Validate();
		$validate->validateparam = array(
			array('input' => $_POST['bank_id'], 'require' => "true", 'message' => "缺少bank_id参数"),
			array('input' => $_POST['account_name'], 'require' => "true", 'message' => "开户人姓名不能为空"),
			array('input' => $_POST['bank'], 'require' => "true", 'message' => "未选择开户行"),
			array('input' => $_POST['account_branch'], 'require' => "true", 'message' => "支行不能为空"),
			array('input' => $_POST['account_num'], 'require' => "true", 'message' => "银行卡账号不能为空"),
		);
		$error = $validate->validate();
		if ($error != '') {
			output_error($error);
		}
		$bank_id = intval($_POST['bank_id']);
		// 验证银行卡是否为本人
		$bank_info = Model('bank')->getMemberBankInfo(array('mb_id' => $bank_id, 'mb_member_id' => $this->member_info['member_id']));
		if (empty($bank_info)) {
			output_error("银行卡不存在");
		}
		$bank = Model('bank')->getBankInfo(array('bank_id' => $_POST['bank'], 'bank_state' => 1));
		if (empty($bank)) {
			output_error("选择的银行不支持");
		}
		$bank_data['mb_account_name'] = trim($_POST['account_name']);
		$bank_data['mb_account_bank_name'] = $bank['bank_name'];
		$bank_data['mb_account_branch'] = trim($_POST['account_branch']);
		$bank_data['mb_account_num'] = trim($_POST['account_num']);
		$bank_data['mb_addtime'] = TIMESTAMP;

		$result = Model('bank')->editMemberBank(array('mb_id' => $bank_id), $bank_data);
		if ($result) {
			output_data(1);
		} else {
			output_error("银行卡信息编辑失败");
		}
	}

	/**
	 * bankListOp 支持的银行列表
	 * @return [type] [description]
	 */
	public function bankListOp() {
		output_data(array('list' => Model('bank')->getBankList()));
	}
}