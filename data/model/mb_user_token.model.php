<?php
/**
 * 手机端令牌模型
 *
 * @
 * @license
 * @link
 */

defined('In33hao') or exit('Access Invalid!');

class mb_user_tokenModel extends Model {
	public function __construct() {
		parent::__construct('mb_user_token');
	}

	/**
	 * getAppMemberTokenByInfo 获取app端TOKEN
	 * @param  [type] $info [description]
	 * @return [type]       [description]
	 */
	public function getAppMemberTokenByInfo($info) {
		$token = '';
		$nowTime = TIMESTAMP;
		$token_info = $this->getMbUserTokenInfo(array('member_id' => $info['member_id']));

		$flag = 0;
		if (empty($token_info)) {
			// 生成token
			$token = $this->_generateToken($info);
			if (!$token) {
				$flag = $this->addMbUserToken(array('member_id' => $info['member_id'], 'member_name' => $info['member_name'], 'token' => $token, 'login_time' => $nowTime, 'client_type' => "APP", 'expire_time' => $nowTime + TOKEN_EXPIRE));
			} else {
				return '';
			}
		} else {
			$old_token = $token_info['token'];
			if (C('rongcloud.open')) {
				$rongCloud = new RongCloud(C('rongcloud.appKey'), C('rongcloud.appSecret'));
				$result = $rongCloud->user()->checkOnline($info['member_id']);
				$result = json_decode($result, true);
				if (!$result['status']) {
					return $old_token;
				} else {
					$token = $this->_generateToken($info);
					if (!$token) {
						$flag = $this->updateUserTokenInfo(array('member_id' => $info['member_id']), array('token' => $token, 'login_time' => $nowTime, 'client_type' => "APP", 'expire_time' => $nowTime + TOKEN_EXPIRE));
					} else {
						return '';
					}
				}
			}
		}

		if ($flag) {
			return $token;
		} else {
			return '';
		}

	}

	/**
	 * 查询
	 *
	 * @param array $condition 查询条件
	 * @return array
	 */
	public function getMbUserTokenInfo($condition) {
		return $this->where($condition)->find();
	}

	public function updateUserTokenInfo($condition, $data) {
		return $this->where($condition)->update($data);
	}

	public function getMbUserTokenInfoByToken($token) {
		if (empty($token)) {
			return null;
		}
		return $this->getMbUserTokenInfo(array('token' => $token));
	}

	public function updateMemberOpenId($token, $openId) {
		return $this->where(array(
			'token' => $token,
		))->update(array(
			'openid' => $openId,
		));
	}

	/**
	 * 新增
	 *
	 * @param array $param 参数内容
	 * @return bool 布尔类型的返回结果
	 */
	public function addMbUserToken($param) {
		return $this->insert($param);
	}

	/**
	 * 删除
	 *
	 * @param int $condition 条件
	 * @return bool 布尔类型的返回结果
	 */
	public function delMbUserToken($condition) {
		return $this->where($condition)->delete();
	}

	/**
	 * _generateToken 生成Token
	 * @param  [type] $info [description]
	 * @return [type]       [description]
	 */
	private function _generateToken($info) {
		if (C('rongcloud.open')) {
			$rongCloud = new RongCloud(C('rongcloud.appKey'), C('rongcloud.appSecret'));
			$result = $rongCloud->user()->getToken($info['member_id'], $info['member_name'], getMemberAvatarForID($info['member_id']));
			$result = json_decode($result, true);
			if (isset($result['code']) && $result['code'] == 200 && isset($result['token'])) {
				return $result['token'];
			} else {
				return array();
			}
		} else {
			$token = md5($info['member_name'] . strval(TIMESTAMP) . strval(rand(0, 999999)));
			return $token;
		}
	}
}
