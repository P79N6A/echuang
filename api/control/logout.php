<?php
/**
 * 注销登录
 *
 */
header("Access-Control-Allow-Origin: *");
defined('In33hao') or exit('Access Invalid!');

class logoutControl extends apiEncryptMemberControl {

	public function __construct() {
		parent::__construct();
	}

	public function login_outOp() {
		$conidition['token'] = $_GET['token'];
		$result = Model('mb_user_token')->delMbUserToken($conidition);
		if ($result) {
			responseApiJson(1, '退出成功', '');
		} else {
			responseApiJson(0, '退出失败', '');
		}
	}
}
