<?php
/**
 * api基类
 *
 */

defined('In33hao') or exit('Access Invalid!');

class apiBaseControl {
	// 客户端类型
	protected $client_type_array = array('android', 'wap', 'wechat', 'ios', 'windows');
	// 列表默认分页数
	protected $page = 5;

	public function __construct() {
		Language::read('api');
		// 分页数处理
		$page = intval($_GET['page']);
		if ($page > 0) {
			$this->page = $page;
		}
	}
}

class apiCommonControl extends apiBaseControl {
	public function __construct() {
		parent::__construct();
	}

	protected function getMemberIdIfExists() {
		$token = $_GET['token'];

		if (empty($token)) {
			$token = $_POST['token'];
		}

		$model_mb_user_token = Model('mb_user_token');
		$mb_user_token_info = $model_mb_user_token->getMbUserTokenInfoByToken($token);
		if (empty($mb_user_token_info)) {
//		    var_dump(111);die();
			return 0;
		}

		return $mb_user_token_info['member_id'];
	}
}

class apiMemberControl extends apiBaseControl {

	protected $member_info = array();

	public function __construct() {
		parent::__construct();
		$token = $_GET['token'];
		if (empty($token)) {
			$token = $_POST['token'];
		}

		$mb_user_token_info = Model('mb_user_token')->getMbUserTokenInfoByToken($token);
		if (empty($mb_user_token_info)) {
			responseApiJson(2, "请登录");
		}
		if (time() > $mb_user_token_info['expire_time']) {
			responseApiJson(2, "请重新登录");
		}
		$model_member_extend = Model('member_extend');
		$this->member_info = $model_member_extend->getMemberExtendInfo(array('member_id' => $mb_user_token_info['member_id']), '*', 'union');

		if (empty($this->member_info)) {
			responseApiJson(2, "请登录");
		} else {
			if ($this->member_info['member_state'] == 0) {
				responseApiJson(3, "该账号已被冻结");
			} else {
				// 更新token过期时间
				Model('mb_user_token')->updateUserTokenInfo(array('token' => $mb_user_token['token']), array('expire_time' => TIMESTAMP + TOKEN_EXPIRE));
				$this->member_info['client_type'] = 'app';
				$this->member_info['openid'] = $mb_user_token_info['openid'];
				$this->member_info['token'] = $mb_user_token_info['token'];
			}
		}
	}

	public function getOpenId() {
		return $this->member_info['openid'];
	}

	public function setOpenId($openId) {
		$this->member_info['openid'] = $openId;
		Model('mb_user_token')->updateMemberOpenId($this->member_info['token'], $openId);
	}

	protected function getMemberEquityNum($equity) {
		$equity_num = 0;
		if ($equity >= C('equity_dividend_max_limit')) {
			$equity_num = 10;
		} elseif ($equity >= C('equity_dividend_min_limit')) {
			$equity_num = 1;
		}
		return $equity_num;
	}

	protected function checkPaypwd() {
		if (empty($this->member_info['member_paypwd'])) {
			return false;
		} else {
			return true;
		}
	}
}

/**
 * 接口加密通用基类
 */
class apiEncryptCommonControl extends apiCommonControl {
	public function __construct() {
		parent::__construct();
		if (isset($_POST['key']) && $_POST['key']) {
        $key = trim($_POST['key']);
        unset($_POST['key']);
        foreach ($_POST as $k => $v) {
            $_POST[$k] = EncryptUtil::decrypt(trim($v), $key);
        }
    }
	}
}

/**
 * 接口加密会员基类
 */
class apiEncryptMemberControl extends apiMemberControl {
	public function __construct() {
		parent::__construct();
		if (isset($_POST['key']) && $_POST['key']) {
			$key = trim($_POST['key']);
			unset($_POST['key']);
			foreach ($_POST as $k => $v) {
				$_POST[$k] = EncryptUtil::decrypt(trim($v), $key);
			}

		}
	}
}