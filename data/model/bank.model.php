<?php
/**
 * 银行模块
 *
 */
defined('In33hao') or exit('Access Invild!');

class bankModel extends Model {
	public function __construct() {
		parent::__construct();
	}

	/**
	 * getBankList 获取银行列表
	 * @param  array  $condition [description]
	 * @param  string $fields    [description]
	 * @return [type]            [description]
	 */
	public function getBankList($condition = array(), $fields = '*') {
		$condition['bank_state'] = 1;
		return $this->table('bank')->where($condition)->field($fields)->select();
	}

	/**
	 * getAllBankList 获取所有银行列表
	 * @return [type] [description]
	 */
	public function getAllBankList() {
		$sql = "SELECT * FROM " . DBPRE . "bank";
		return $this->query($sql);
	}

	/**
	 * getBankInfo 获取银行信息
	 * @param  [type] $condition [description]
	 * @param  string $fields    [description]
	 * @return [type]            [description]
	 */
	public function getBankInfo($condition, $fields = '*') {
		return $this->table('bank')->where($condition)->field($fields)->find();
	}

	/**
	 * addBankInfo 添加银行信息
	 * @param [type] $data [description]
	 */
	public function addBankInfo($data) {
		return $this->table('bank')->insert($data);
	}

	/**
	 * deleteBankInfo 删除银行信息
	 * @param  [type] $condition [description]
	 * @return [type]            [description]
	 */
	public function deleteBankInfo($condition) {
		return $this->table('bank')->where($condition)->delete();
	}

	/**
	 * updateBankInfo 更新银行信息
	 * @param  [type] $condition [description]
	 * @param  [type] $data      [description]
	 * @return [type]            [description]
	 */
	public function updateBankInfo($condition, $data) {
		return $this->table('bank')->where($condition)->update($data);
	}

	/**
	 * getMemberBankList 获取会员银行列表
	 * @param  [type] $condition [description]
	 * @param  string $fields    [description]
	 * @return [type]            [description]
	 */
	public function getMemberBankList($condition = array(), $fields = '*', $page = null, $order = 'mb_addtime desc', $limit = '') {
		return $this->table('member_bank')->where($condition)->field($fields)->page($page)->order($order)->limit($limit)->select();
	}

	/**
	 * getMemberBankInfo 获取会员银行信息
	 * @param  [type] $condition [description]
	 * @param  string $fields    [description]
	 * @return [type]            [description]
	 */
	public function getMemberBankInfo($condition, $fields = '*') {
		return $this->table('member_bank')->where($condition)->field($fields)->find();
	}

	/**
	 * delMemberBank 删除银行卡信息
	 * @param  [type] $condition [description]
	 * @return [type]            [description]
	 */
	public function delMemberBank($condition) {
		return $this->table('member_bank')->where($condition)->delete();
	}

	/**
	 * addMemberBank 添加会员银行卡
	 * @param [type] $data [description]
	 */
	public function addMemberBank($data) {
		return $this->table('member_bank')->insert($data);

	}

	/**
	 * editMemberBank 编辑会员银行卡
	 * @param  [type] $condition [description]
	 * @param  [type] $data      [description]
	 * @return [type]            [description]
	 */
	public function editMemberBank($condition, $data) {
		return $this->table('member_bank')->where($condition)->update($data);

	}

	/**
	 * buildBankSelectHtml 生成银行下来菜单
	 * @param  [type] $bank_name [description]
	 * @return [type]            [description]
	 */
	public function buildBankSelectHtml($bank_name = '') {
		$html = '<option value ="">请选择开户银行</option>';
		$bank_list = $this->getBankList(array(), 'bank_name,bank_id');
		foreach ($bank_list as $v) {
			if (!empty($bank_name) && ($v['bank_name'] == $bank_name)) {
				$html .= '<option value ="' . $v['bank_id'] . '" selected>' . $v['bank_name'] . '</option>';
			} else {
				$html .= '<option value ="' . $v['bank_id'] . '">' . $v['bank_name'] . '</option>';
			}
		}
		return $html;
	}

	/**
	 * getBankListArr 获取银行列表数组
	 * @return [type] [description]
	 */
	public function getBankListArr() {
		$arr = array();
		$bank_list = $this->getBankList(array(), 'bank_name,bank_id');
		foreach ($bank_list as $v) {
			$arr[$v['bank_id']] = $v['bank_name'];
		}
		return $arr;
	}

}