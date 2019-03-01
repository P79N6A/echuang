<?php
/**
 * 邀请返利
 *
 */

defined('In33hao') or exit('Access Invalid!');

class member_inviteControl extends apiEncryptMemberControl {

	public function __construct() {
		parent::__construct();
	}

	/**
	 * 我的邀请返利
	 */
	public function pri_inviteOp() {
		$member_id = $this->member_info['member_id'];
		$encode_member_id = base64_encode(intval($member_id) * 1);
		$myurl = BASE_SITE_URL . "/member_system/index.php?act=login&op=register";
		$str_member = "memberqr_" . $member_id;
		$myurl_src = UPLOAD_SITE_URL . DS . "shop" . DS . 'er_code.png';
		$imgfile = BASE_UPLOAD_PATH . DS . "shop" . DS . 'er_code.png';

		if (!file_exists($imgfile)) {
			require_once BASE_RESOURCE_PATH . DS . 'phpqrcode' . DS . 'index.php';
			$PhpQRCode = new PhpQRCode();
			$PhpQRCode->set('pngTempDir', BASE_UPLOAD_PATH . DS . "shop" . DS);
			$PhpQRCode->set('date', $myurl);
			$PhpQRCode->set('pngTempName', 'er_code.png');
			$PhpQRCode->init();
		}
		$info = array();
		$info['user_name'] = $this->member_info['member_name'];
		$info['avator'] = getMemberAvatarForID($this->member_info['member_id']);
		$info['myurl'] = $myurl;
		$info['myurl_src'] = $myurl_src;
		// 下载连接
		$mydownurl = BASE_SITE_URL . "/index.php?act=invite&op=downqrfile&id=" . $member_id;
		$info['mydownurl'] = $mydownurl;
		responseApiJson(1, "", $info);
	}
}
