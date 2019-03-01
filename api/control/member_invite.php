<?php
/**
 * 邀请返利
 *
 */
header("Access-Control-Allow-Origin: *");
defined('In33hao') or exit('Access Invalid!');

class member_inviteControl extends apiEncryptMemberControl {

	public function __construct() {
		parent::__construct();
	}

	/**
	 * 我的邀请返利
	 */
	public function pri_inviteOp() {
        $mobile = $this->member_info['member_mobile'];
        $member_name = $this->member_info['member_name'];
        $return = [
            'mobile'=>$mobile,
            'member_name'=>$member_name
        ];
        $encode_mobile = base64_encode($mobile);
        $myurl = WAP_SITE_URL . "/tmpl/member_system/register.html?invite=" .$encode_mobile;
        $str_member = "memberqr_" . $this->member_info['member_id'];
        $myurl_src = UPLOAD_SITE_URL . DS . "shop" . DS . "member" . DS . $str_member . '.png';
        $imgfile = BASE_UPLOAD_PATH . DS . "shop" . DS . "member" . DS . $str_member . '.png';
        if (!file_exists($imgfile)) {
            require_once BASE_RESOURCE_PATH . DS . 'phpqrcode' . DS . 'index.php';
            $PhpQRCode = new PhpQRCode();

            $PhpQRCode->set('pngTempDir', BASE_UPLOAD_PATH . DS . "shop" . DS . "member" . DS);
            $PhpQRCode->set('date', $myurl);
            $PhpQRCode->set('pngTempName', $str_member . '.png');
            $PhpQRCode->init();
        }
        responseApiJson(1, "",['myurl_src'=>$myurl_src,'return'=>$return]);
	}
}
