<?php
/**
 *
 *
 *
 */
header("Access-Control-Allow-Origin: *");
defined('In33hao') or exit('Access Invalid!');
class indexControl extends apiEncryptCommonControl {

	public function __construct() {
		parent::__construct();
	}

	/**
	 * 首页
	 */
	public function indexOp() {
		$model_mb_special = Model('mb_special');
        $data = $model_mb_special->getMbSpecialIndex($_GET['special_id']);
        //foreach ($data as $k=>$v){
            //$keys= array_keys($v);
            //if ($keys[0] == 'adv_list'){
               // unset($data[$k]);
            //}
       // }
        $data = array_values($data);
        $this->_output_special($data, $_GET['type']);
	}

	/**
	 * 专题
	 */
	public function specialOp() {
		$model_mb_special = Model('mb_special');
		$info = $model_mb_special->getMbSpecialInfoByID($_GET['special_id']);
		$list = $model_mb_special->getMbSpecialItemUsableListByID($_GET['special_id']);
		$data = array_merge($info, array('list' => $list));
		$this->_output_special($data, $_GET['type'], $_GET['special_id']);
	}

	/**
	 * 输出专题
	 */
	private function _output_special($data, $type = 'json', $special_id = 0) {
		$model_special = Model('mb_special');
		if ($_GET['type'] == 'html') {
			$html_path = $model_special->getMbSpecialHtmlPath($special_id);
			if (!is_file($html_path)) {
				ob_start();
				Tpl::output('list', $data);
				Tpl::showpage('mb_special');
				file_put_contents($html_path, ob_get_clean());
			}
			header('Location: ' . $model_special->getMbSpecialHtmlUrl($special_id));
			die;
		}
        foreach ($data as $k=>$v) {
            if (array_key_exists('home1',$v) || array_key_exists('home2',$v) || array_key_exists('home3',$v) || array_key_exists('home4',$v) || array_key_exists('goods1',$v) || array_key_exists('',$v)){
                if (isset( $data[$k][key($v)]['textfield'])){
                    $data[$k][key($v)]['textfield'] = BASE_SITE_URL.DS."data/upload/mobile/special/s0".$data[$k][key($v)]['textfield'];
                }
            }
        }
		responseApiJson(1, '', $data);
	}
	/**
	 * @brief 正则取 url 参数
	 * @param $url
	 * @return
	 */
	public function getUrlKeyValue($url) {
		$result = array();
		$mr = preg_match_all('/(\?|&)(.+?)=([^&?]*)/i', $url, $matchs);
		if ($mr !== false) {
			for ($i = 0; $i < $mr; $i++) {
				$result[$matchs[2][$i]] = $matchs[3][$i];
			}
		}
		return $result;
	}

	/**
	 * android客户端版本号
	 */
	public function apk_versionOp() {
		$version = C('mobile_apk_version');
		$url = C('mobile_apk');
		if (empty($version)) {
			$version = '';
		}
		if (empty($url)) {
			$url = '';
		}

		responseApiJson(1, '', array('version' => $version, 'url' => $url));
	}

	/**
	 * 热门搜索列表
	 */
	public function searchHotInfoOp() {
		$list = @explode(',', C('hot_search'));
		if (!$list || !is_array($list)) {
			$list = array();
		}
		if ($_COOKIE['hisSearch'] != '') {
			$his_search_list = explode('~', $_COOKIE['hisSearch']);
		}
		if (!$his_search_list || !is_array($his_search_list)) {
			$his_search_list = array();
		}
		$all_list['hot_list'] = $list;
		$all_list['his_list'] = $his_search_list;
		responseApiJson(1, '', $all_list);
	}

	/**
	 * 默认搜索关键词
	 */
	public function searchKeyWordOp() {
		if (C('rec_search') != '') {
			$rec_search_list = @unserialize(C('rec_search'));
		}
		$rec_search_list = is_array($rec_search_list) ? $rec_search_list : array();
		$result = $rec_search_list[array_rand($rec_search_list)];
		if ($result) {
			responseApiJson(1, '', $result['name']);
		} else {
			responseApiJson(1, '', '');
		}
	}

	/**
	 * 高级搜索
	 */
	// public function search_advOp() {
	// 	$area_list = Model('area')->getAreaList(array('area_deep' => 1), 'area_id,area_name');
	// 	if (C('contract_allow') == 1) {
	// 		$contract_list = Model('contract')->getContractItemByCache();
	// 		$_tmp = array();
	// 		$i = 0;
	// 		foreach ($contract_list as $k => $v) {
	// 			$_tmp[$i]['id'] = $v['cti_id'];
	// 			$_tmp[$i]['name'] = $v['cti_name'];
	// 			$i++;
	// 		}
	// 	}
	// 	output_data(array('area_list' => $area_list ? $area_list : array(), 'contract_list' => $_tmp));
	// }

	/**
	 * 首页公告详情
	 */
	public function get_article_infoOp() {
		$article_id = $_GET['article_id'];
		if (!empty($article_id)) {
			$url = "http://192.168.0.151/wap/tmpl/article_show.html?article_id=" . $article_id;
			responseApiJson(1, '', array('url' => $url));
		}
	}

	/**
	 * 首页公告列表
	 */
	public function get_index_article_listOp() {
		//公告ac_id类型
		$ac['ac_id'] = 1;
		$index_notice_list = Model('notice')->getNoticeList();
		foreach ($index_notice_list as $key => $value) {
			if (!empty($index_notice_list[$key]['notice_id'])) {
				$url = BASE_SITE_URL . "/wap/tmpl/notice_show.html?notice_id=" . $index_notice_list[$key]['notice_id'];
				$index_notice_list[$key]['notice_url'] = $url;
			}
			if (empty($index_notice_list[$key]['article_desc'])) {
				$index_notice_list[$key]['notice_content'] = "";
			}
		}
		if ($index_notice_list) {
			responseApiJson(1, '', $index_notice_list);
		} else {
			responseApiJson(1, '', '');
		}
	}

	//上传图片插件
	public function upload_fileOp() {
		$pic_name = '';
		$upload = new UploadFile();
		$file = current($_FILES);

		$upload->set('max_size', C('image_max_filesize'));
		$upload_type = $_POST['upload_type'];
		if ($upload_type == "avatar") {
			$uploaddir = ATTACH_AVATAR;
		}
		if ($upload_type == "truename") {
			$uploaddir = ATTACH_MEMBER . DS . 'member_verify' . DS;
		}
		$upload->set('default_dir', $uploaddir);
		$upload->set('allow_type', array('jpg', 'jpeg', 'gif', 'png'));
		if (!empty($file['tmp_name'])) {
			$result = $upload->upfile(key($_FILES));
			if ($result) {
				if ($upload_type == "avatar") {
					$pic_url = UPLOAD_SITE_URL . '/' . ATTACH_AVATAR . '/' . $upload->file_name;
				}
				if ($upload_type == "truename") {
					$pic_url = UPLOAD_SITE_URL . DS . ATTACH_MEMBER . DS . 'member_verify' . DS . $upload->file_name;
				}
				responseApiJson(1, '', array(
					'pic_name' => $upload->file_name,
					'pic_url' => $pic_url,
					'height' => $this->get_height($pic_url),
					'width' => $this->get_width($pic_url),
				));
			} else {
				responseApiJson(0, $upload->error, array('pic_name' => "", 'pic_url' => ""));
			}
		}
	}

	function get_height($image) {
		$size = getimagesize($image);
		$height = $size[1];
		return $height;
	}

	function get_width($image) {
		$size = getimagesize($image);
		$width = $size[0];
		return $width;
	}

	/**
	 *
	 * 拼接签名字符串
	 * @param array $urlObj
	 *
	 * @return 返回已经拼接好的字符串
	 */
	function toUrlParams($urlObj) {
		$buff = "";
		foreach ($urlObj as $k => $v) {
			if ($k != "sign" && $v !== '' && $k != "service") {
				$buff .= $k . "=" . $v . "&";
			}
		}

		$buff = trim($buff, "&");
		return $buff;
	}

	/**
	 *  生成签名
	 */
	function getSignOp($data) {
		$key = $_GET['key'];
		//第一步：对参数按照key=value的格式，并按照参数名ASCII字典序排序如下：
		ksort($data);
		$string = $this->toUrlParams($data);

		//第二步：拼接API密钥
		$string = $string . "&key=" . $key;

		//MD5加密
		$string = md5($string);

		//将得到的字符串全部大写并返回
		return strtoupper($string);
	}

	/**
	 *  产生随机字符串，不长于32位
	 */
	function createNoncestr($length = 32) {
		$chars = "abcdefghijklmnopqrstuvwxyz0123456789";
		$str = "";
		for ($i = 0; $i < $length; $i++) {
			$str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
		}
		return $str;
	}

	public function update_appOp() {
		if (!empty(C('mobile_apk'))) {
			$app['mobile_apk'] = C('mobile_apk');
		} else {
			$app['mobile_apk'] = "";
		}
		if (!empty(C('mobile_ios'))) {
			$app['mobile_ios'] = C('mobile_ios');
		} else {
			$app['mobile_ios'] = "";
		}
		if (!empty(C('mobile_apk_version'))) {
			$app['mobile_apk_version'] = C('mobile_apk_version');
		} else {
			$app['mobile_apk_version'] = "";
		}
		responseApiJson(1, '', $app);
	}

	/**
	 * 读取第三方银行卡列表
	 */
	public function public_bankListOp() {
		$bank_list['bank_list'] = Model('bank')->getBankList();
		responseApiJson(1, '', $bank_list);
	}

	//用户协议
	public function user_documentOp() {
		$url = BASE_SITE_URL . '/wap/tmpl/member/document.html';
		responseApiJson(1, '', array('url' => $url));
	}

	public function member_tokenOp() {
		$token = $_GET['token'];
		$model_mb_user_token = Model('mb_user_token');
		$mb_user_token_info = $model_mb_user_token->getMbUserTokenInfoByToken($token);
		$member_info = Model('member')->getMemberInfoByID($mb_user_token_info['member_id']);
		echo json_encode(array('member_avatar'=>$member_info['member_avatar'],'member_id' => $member_info['member_id'], 'member_mobile' => $member_info['member_mobile'], 'member_name' => $member_info['member_name']));
	}

	public function invite_dianOp() {
		$member_id = trim($_GET['member_id']);
		$invite_list = Model('member_extend')->getMemberExtendList(array('invite_id' => $member_id), '*', 'union', null, 'reg_time asc');
		foreach ($invite_list as $key=>$value){
            $invite_list[$key]['member_level'] = str_replace(array('0','1','2','3','4','5'),array("体验用户","VIP","店主","合伙人","高级合伙人","战略合伙人"),$value['member_level']);
        }
		echo json_encode($invite_list);
	}

	//个人中心公告列表
	public function notice_listOp() {
		$article_list = Model('notice')->getNoticeList('', 'notice_id,notice_addtime,notice_title');
		foreach ($article_list as $key => $value) {
			if (!empty($article_list[$key]['notice_id'])) {
				$url = BASE_SITE_URL . "/wap/tmpl/notice_show.html?notice_id=" . $article_list[$key]['notice_id'];
				$article_list[$key]['notice_url'] = $url;
			}
			$article_list[$key]['notice_addtime'] = date('Y-m-d H:i:s', $value['notice_addtime']);
		}
		if ($article_list) {
			responseApiJson(1, '', $article_list);
		} else {
			responseApiJson(1, '', '');
		}
	}
}
