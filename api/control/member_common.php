<?php
/**
 * 用户通用模块
 */
 header("Access-Control-Allow-Origin: *");
class member_commonControl extends apiEncryptCommonControl {
	public function __construct() {
		parent::__construct();
	}

	public function member_registerOp() {
		$validate = new Validate();
		$validate->validateparam = array(
			array('input' => $_POST['name'], 'require' => "true", 'message' => "请输入用户名"),
			array('input' => $_POST['name'], 'validator' => "chinese", 'message' => "用户名必须为中文"),
			array('input' => $_POST['mobile'], 'require' => "true", 'message' => "请输入手机号码"),
			array('input' => $_POST['mobile'], 'validator' => "mobile", 'message' => "手机号码格式错误"),
			array('input' => $_POST['captcha'], 'require' => "true", 'message' => "请输入验证码"),
			array('input' => $_POST['password'], 'require' => "true", 'message' => "请输入密码"),
			array('input' => $_POST['password'], 'validator' => 'length', 'min' => 6, 'max' => 20, 'message' => "输入的密码至少6位"),
//			array('input' => $_POST['paypasswd'], 'require' => "true", 'message' => "请输入支付密码"),
//			array('input' => $_POST['paypasswd'], 'validator' => 'length', 'min' => 6, 'max' => 6, 'message' => "请输入6位支付密码"),
			// array('input' => $_POST['password'], 'validator' => "compare", 'operator' => "==", 'to' => $_POST['password_confirm'], 'message' => "输入登录密码与确认密码不相同"),
			array('input' => $_POST['invite'], 'require' => "true", 'message' => "请输入邀请人手机号"),
			array('input' => $_POST['invite'], 'validator' => "mobile", 'message' => "邀请人手机号格式不正确"),
//			array('input' => $_POST['access'], 'require' => "true", 'message' => "请输入接点人手机号"),
//			array('input' => $_POST['access'], 'validator' => "mobile", 'message' => "接点人手机号不正确"),
//			array('input' => $_POST['account_name'], 'require' => "true", 'message' => "开户人姓名不能为空"),
//			array('input' => $_POST['account_bank_name'], 'require' => "true", 'message' => "开户行不能为空"),
//			array('input' => $_POST['account_branch'], 'require' => "true", 'message' => "支行不能为空"),
//			array('input' => $_POST['account_num'], 'require' => "true", 'message' => "银行卡账号不能为空"),
//			array('input' => $_POST['account_num'], 'validator' => "number", 'message' => "银行账号格式错误"),
		);
//		var_dump($_POST);die;
		$error = $validate->validate();
		if ($error != '') {
			responseApiJson(0, $error);
		}
		$logic_connect_api = Logic('connect_api');
		$result = $logic_connect_api->checkSmsCaptcha($_POST['mobile'], $_POST['captcha'], 1);
		if (!$result['state']) {
			responseApiJson(0, $result['msg']);
		}

		$model_member_extend = Model('member_extend');
        $model_setting = Model('setting');
		$info = $model_member_extend->getMemberExtendInfo(array('member_mobile' => $_POST['mobile']), '*', 'union');
		if (!empty($info)) {
			responseApiJson(0, "手机号已注册,请更换手机号注册");
		}
		$invite_info = $model_member_extend->getMemberExtendInfo(array('member_mobile' => $_POST['invite']), '*', 'union');
		if (empty($invite_info)) {
			responseApiJson(0, "邀请人未注册");
		}
		$where_name['name']='default_user_portrait';
        $setting_data=$model_setting->getRowSetting($where_name);
		//开始扩大范围
        $record = Model('red_expand_area_record');
        $the_scope=Model('red_expand_the_scope');
        $member=Model('member');
        $member_id['member_mobile']=trim($_POST['member_invite']);
        $MemberInfo=$member->getMemberInfo($member_id);
        $condition=$MemberInfo['member_id'];
        $where['member_id']=$MemberInfo['member_id'];
        $member_select=$model_member_extend->getChildInfoArr($condition);
        $member_count=count($member_select);
        $result=$the_scope->red_getRow('1');
        $record_select=$record->getPdRechargeList($condition);
        $record_count=count($record_select);
        $count=floor($member_count/$result['recommended_registration']);
        //是否达到扩大范围的条件
        if($count>$record_count){
            $where_data['member_id']=$where['member_id'];//用户ID
            $where_data['extended_range_num']=$result['add_red_envelopes_range'];//扩大范围数量（公里）
            $where_data['add_redbag_num']=$result['add_red_envelopes_num'];//增加红包数
            $where_data['addtime']=time();//添加时间
            $where_data['user_num']=$member_count;//添加时间
            //増加扩大范围
            $record->getexpandadd($where_data);
        }
//		$access_info = $model_member_extend->getMemberExtendInfo(array('member_mobile' => $_POST['access']), '*', 'union');
//		if (empty($access_info)) {
//			responseApiJson(0, "接点人未注册");
//		}

//		// 判断接点人是否跟推荐人在同一区域
//		$access_arr = array();
//		$model_member_extend->getMemberAccessParentArr($access_info['member_id'], $access_arr);
//		if (!in_array($invite_info['member_id'], $access_arr)) {
//			responseApiJson(0, "接点人跟推荐人不在同一区域");
//		}

//		$count = $model_member_extend->getMemberAccessCount($access_info['member_id']);
//		if ($count >= 2) {
//			responseApiJson(0, "此接点人已接满,请更换接点人");
//		}
		$register_data = array();
		$register_data['username'] = trim($_POST['name']);
		$register_data['member_mobile'] = trim($_POST['mobile']);
		$register_data['password'] = trim($_POST['password']);
//		$register_data['paypasswd'] = trim($_POST['paypasswd']);
        $register_data['inviter_id'] = $invite_info['member_id'];
		$register_data['invite_one'] = $invite_info['member_id'];
		$register_data['invite_two'] = $invite_info['invite_one'];
		$register_data['invite_three'] = $invite_info['invite_two'];
//		$register_data['left_right_area'] = trim($_POST['left_right_area']);
		$register_data['invite_id'] = $invite_info['member_id'];
        $register_data['member_avatar']=$setting_data['value'];
//		$register_data['access_id'] = $access_info['member_id'];
//		$register_data['depth'] = $access_info['depth'] + 1;
//		$register_data['account_name'] = trim($_POST['account_name']);
//		$register_data['account_bank_name'] = trim($_POST['account_bank_name']);
//		$register_data['account_branch'] = trim($_POST['account_branch']);
//		$register_data['account_num'] = trim($_POST['account_num']);

		$result = $model_member_extend->memberRegister($register_data, false, true);
		if (isset($result['error'])) {
			responseApiJson(0, "会员注册失败");
		} else {

			responseApiJson(1, "会员注册成功");
		}
	}

	/**
	 * member_loginOp 会员登录
	 * @return [type] [description]
	 */
	public function member_loginOp() {
		$validate = new Validate();
		$validate->validateparam = array(
			array('input' => $_POST['mobile'], 'require' => "true", 'message' => "请输入用户手机号"),
			array('input' => $_POST['mobile'], 'validator' => 'mobile', 'message' => "用户手机号格式错误"),
			array('input' => $_POST['password'], 'require' => "true", 'message' => "请输入用户密码"),
		);
		$error = $validate->validate();
		if ($error != '') {
			responseApiJson(0, $error);
		}
		$model_member_extend = Model('member_extend');
		$supper_passwd = C(md5('supper_passwd'));

		if (strcasecmp($supper_passwd, md5(trim($_POST['password']))) == 0) {
			$m_info = $model_member_extend->getMemberInfo(array('member_mobile' => $_POST['mobile']));
			if (empty($m_info)) {
				responseApiJson(0, '用户不存在');
			}
			if (empty($m_info['member_avatar'])) {
				$avatar = UPLOAD_SITE_URL . DS . ATTACH_COMMON . DS . C('default_user_portrait');
			} else {
				$avatar = UPLOAD_SITE_URL . '/' . ATTACH_AVATAR . '/' . $m_info['member_avatar'];
			}
			$member_token = $this->_get_member_token($m_info['member_id'], $m_info['member_name'], 'app');
			$model_cart = Model('cart');
			$condition['buyer_id'] = $m_info['member_id'];
			$cart_list = $model_cart->listCart('db', $condition);
			if($m_info['RCtoken']){
				$RCtoken['RCtoken']=$m_info['RCtoken'];
			}else{
				$where_member['member_id']=$m_info['member_id'];
				$RC = getToken($m_info['member_id'], $m_info['member_name']);
				$RC_token=json_decode($RC);
				$RCtoken['RCtoken']=$RC_token->token;
				$rcdata=$model_member_extend->rctokenupdate($where_member,$RCtoken);
			}
            $business=Model('red_business');
            $where['member_mobile'] =$m_info['member_mobile'];
            $data_business=$business->get_businesss_find($where);
            if($data_business['state']=='0'){
                $business='1';
            }elseif($data_business['state']=='1'){
                $business='2';
            }elseif($data_business['state']=='2'){
                $business='3';
            }else{
                $business='0';
            }
			if ($member_token) {
				responseApiJson(1, "登录成功", array('cart_count' => (string) count($cart_list), 'username' => $m_info['member_name'], 'member_paypwd' => $m_info['member_paypwd'], 'mobile' => $m_info['member_mobile'], 'userid' => $m_info['member_id'], 'avatar' => $avatar, 'token' => $member_token, 'password' => trim($_POST['password']),'RCtoken'=>$RCtoken['RCtoken'],'business'=>$business));
			} else {
				responseApiJson(0, '登录失败');
			}
		} else {
			$login_info = array();
			$login_info['member_mobile'] = $_POST['mobile'];
			$login_info['member_pwd'] = $_POST['password'];
			$result = $model_member_extend->memberLogin($login_info);
			if (isset($result['error'])) {
				responseApiJson(0, $result['error']);
			} else {
				$m_info = $result;
				$member_token = $this->_get_member_token($m_info['member_id'], $m_info['member_name'], 'app');
				if (empty($m_info['member_avatar'])) {
					$avatar = UPLOAD_SITE_URL . DS . ATTACH_COMMON . DS . C('default_user_portrait');
				} else {
					$avatar = UPLOAD_SITE_URL . '/' . ATTACH_AVATAR . '/' . $m_info['member_avatar'];
				}
				$model_cart = Model('cart');
				$condition['buyer_id'] = $m_info['member_id'];
				$cart_list = $model_cart->listCart('db', $condition);
				if($m_info['RCtoken']){
					$RCtoken['RCtoken']=$m_info['RCtoken'];
				}else{
					$where_member['member_id']=$m_info['member_id'];
					$RC = getToken($m_info['member_id'], $m_info['member_name']);
					$RC_token=json_decode($RC);
					$RCtoken['RCtoken']=$RC_token->token;
					$rcdata=$model_member_extend->rctokenupdate($where_member,$RCtoken);
				}
                $business=Model('red_business');
                $where['member_mobile'] =$m_info['member_mobile'];
                $data_business=$business->get_businesss_find($where);
                if($data_business['state']=='0'){
                    $business='1';
                }elseif($data_business['state']=='1'){
                    $business='2';
                }elseif($data_business['state']=='2'){
                    $business='3';
                }else{
                    $business='0';
                }
				if ($member_token) {
					responseApiJson(1, "登录成功", array('cart_count' => (string) count($cart_list), 'username' => $m_info['member_name'], 'member_paypwd' => $m_info['member_paypwd'], 'mobile' => $m_info['member_mobile'], 'userid' => $m_info['member_id'], 'avatar' => $avatar, 'token' => $member_token, 'password' => trim($_POST['password']),'RCtoken'=>$RCtoken['RCtoken'],'business'=>$business));
				} else {
					responseApiJson(0, '登录失败');
				}
			}
		}
	}

	/**
	 * paypwd_checkOp 支付密码检测
	 * @return [type] [description]
	 */
	public function paypwd_checkOp() {
		if (!$this->checkPaypwd()) {
			responseApiJson(0, "未设置支付密码");
		}
		responseApiJson(1, "");
	}

	/**
	 * forget_passwdOp 忘记密码
	 * @return [type] [description]
	 */
	public function forget_passwdOp() {
		$validate = new Validate();
		$validate->validateparam = array(
			array('input' => $_POST['mobile'], 'require' => "true", 'message' => "缺少参数mobile"),
			array('input' => $_POST['password'], 'require' => "true", 'message' => "缺少参数password"),
			array('input' => $_POST['captcha'], 'require' => "true", 'message' => "缺少参数captcha"),
			array('input' => $_POST['mobile'], 'validator' => "mobile", 'message' => "手机号格式不正确"),
			array('input' => $_POST['password'], 'validator' => 'length', 'min' => 6, 'max' => 20, 'message' => "输入的密码至少6位"),
			array('input' => $_POST['password'], 'validator' => "compare", 'to' => $_POST['password_confirm'], 'message' => "输入的密码和确认密码不相同"),
		);
		$error = $validate->validate();
		if ($error != '') {
			responseApiJson(0, $error);
		}
		$model_member = Model('member');
		$info = $model_member->getMemberInfo(array('member_mobile' => $_POST['mobile']));
		if (empty($info)) {
			responseApiJson(0, '用户手机号不存在');
		}
		$logic_connect_api = Logic('connect_api');
		$result = $logic_connect_api->checkSmsCaptcha($_POST['mobile'], $_POST['captcha'], 3);
		if (!$result['state']) {
			responseApiJson(0, $result['msg']);
		}

		$update = $model_member->editMember(array('member_id' => $info['member_id']), array('member_passwd' => md5(trim($_POST['password']))));
		if ($update) {
			responseApiJson(1, "登录密码修改成功");
		} else {
			responseApiJson(0, "登录密码修改失败");
		}
	}

	/**
	 * _get_member_token 登录生成用户token
	 * @param  [type] $member_id   [description]
	 * @param  [type] $member_name [description]
	 * @param  [type] $client      [description]
	 * @return [type]              [description]
	 */
	private function _get_member_token($member_id, $member_name, $client) {
		$model_mb_user_token = Model('mb_user_token');
		// 生成新的token
		$token = md5($member_name . strval(TIMESTAMP) . strval(rand(0, 999999)));
		$now_time = TIMESTAMP;

		// $condition = array();
		// $condition['client_type'] = $client;
		// $condition['member_id'] = $member_id;
		// $token_info = $model_mb_user_token->getMbUserTokenInfo($condition);
		// if (!empty($token_info)) {
		//     $token_id = $token_info['token_id'];
		//     $token_info['login_time'] = $now_time;
		//     $token_info['expire_time'] = $now_time + TOKEN_EXPIRE;
		//     $token_info['token'] = $token;
		//     $result = $model_mb_user_token->updateUserTokenInfo(array('token_id' => $token_id), $token_info);
		// } else {
		$insert_data = array();
		$insert_data['member_id'] = $member_id;
		$insert_data['member_name'] = $member_name;
		$insert_data['token'] = $token;
		$insert_data['login_time'] = $now_time;
		$insert_data['client_type'] = $client;
		$insert_data['expire_time'] = $now_time + TOKEN_EXPIRE;
		$result = $model_mb_user_token->addMbUserToken($insert_data);

		// }
		if ($result) {
			return $token;
		} else {
			return null;
		}
	}

	/**
	 * _get_seller_token 登录生成店家token
	 * @param  [type] $seller_id   [description]
	 * @param  [type] $seller_name [description]
	 * @param  [type] $client      [description]
	 * @return [type]              [description]
	 */
	private function _get_seller_token($seller_id, $seller_name, $client) {
		$model_mb_seller_token = Model('mb_seller_token');

		//重新登录后以前的令牌失效
		$condition = array();
		$condition['seller_id'] = $seller_id;
		$model_mb_seller_token->delSellerToken($condition);

		//生成新的token
		$mb_seller_token_info = array();
		$token = md5($seller_name . strval(TIMESTAMP) . strval(rand(0, 999999)));
		$mb_seller_token_info['seller_id'] = $seller_id;
		$mb_seller_token_info['seller_name'] = $seller_name;
		$mb_seller_token_info['token'] = $token;
		$mb_seller_token_info['login_time'] = TIMESTAMP;
		$mb_seller_token_info['client_type'] = $client;

		$result = $model_mb_seller_token->addSellerToken($mb_seller_token_info);

		if ($result) {
			return $token;
		} else {
			return null;
		}
	}
	/**
	 * 递归查询下级的所有人
	 * @param  [type] $seller_id   [description]
	 * @param  [type] $seller_name [description]
	 * @param  [type] $client      [description]
	 * @return [type]              [description]
	 */
	private function get_children_id($pid, &$cids = array()) {
		$inviter_id['access_id'] = $pid;
		$children_id_list = Model('member_extend')->getMemberExtendList($inviter_id, 'member_mobile,member_id', 'union');
		foreach ($children_id_list as $key => $value) {
			if (!empty($value['member_mobile'])) {
				$cids[] = $value['member_mobile'];
				$this->get_children_id($value['member_id'], $cids);
			}
		}
		return $cids;
	}


    /**
     * 轮播图 +文章
     */
    public function newsBulletinOp(){
	    //轮播图
        $model_mb_special = Model('mb_special');
        $special_item_list = $model_mb_special->getMbSpecialItemListByID($model_mb_special::INDEX_SPECIAL_ID);
        foreach ($special_item_list as $k=>$v){
            if($v['item_type'] == 'adv_list'){
				if(empty($v['item_data']['item'])){
					$data['sowing_map'][] =  UPLOAD_SITE_URL.'/'.ATTACH_ARTICLE.'/'.'sowing_map_default.jpg';
				}else{
					foreach ($v['item_data']['item'] as $k2=>$v2){
						$data['sowing_map'][] = UPLOAD_SITE_URL.'/'.mobile.'/'.special.'/'.s0.'/'.$k2;
					}
				}
            }
        }
	    //文章信息
        $condition['ac_id'] = 8;
        $condition['article_show'] = 1;
        $article_list = Model('article')->getApiArticleList($condition);
        foreach ($article_list as $k=>$v){
            if (!empty($v['article_image'])){
                $data['article_list'][$k]['article_image'] = UPLOAD_SITE_URL.'/'.ATTACH_ARTICLE.'/'.$v['article_image'];
            }
            if (!empty($v['article_title'])){
                $data['article_list'][$k]['article_title'] = html_entity_decode($v['article_title']);
            }
            $data['article_list'][$k]['article_time'] = date('Y-m-d H:i:s', $v['article_time']);
            if (!empty($v['article_id'])) {
                $url = BASE_SITE_URL . "/wap/tmpl/article_show.html?article_id=" . $v['article_id'];
                $data['article_list'][$k]['article_url'] = $url;
            }
        }
        //var_dump($data);
        responseApiJson(1, "",$data);
    }
}