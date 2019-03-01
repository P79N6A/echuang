<?php
/**
 * 相册
 *
 *  (c) ()
 * @license    
 * @link       
 * @since      
 */

defined('In33hao') or exit('Access Invalid!');

class sns_albumControl extends apiEncryptMemberControl {

	public function __construct() {
		parent::__construct();
	}

	/**
	 * 上传图片
	 *
	 * @param
	 * @return
	 */
	public function file_uploadOp() {
		/**
		 * 读取语言包
		 */
		Language::read('sns_home');
		$lang = Language::getLangContent();
		$member_id = $this->member_info['member_id'];

		$model = Model();
		// 验证图片数量
		$count = $model->table('sns_albumpic')->where(array('member_id' => $member_id))->count();
		if (C('malbum_max_sum') != 0 && $count >= C('malbum_max_sum')) {
			responseApiJson(0, '已经超出允许上传图片数量，不能在上传图片！', '');
		}

		/**
		 * 上传图片
		 */
		$file_key = array_keys($_FILES);
		if (count($_FILES) > 1) {
			for ($i = 0; $i < count($_FILES); $i++) {
				$upload = new UploadFile();
				$upload_dir = ATTACH_MALBUM . DS . $member_id . DS;
				$upload->set('default_dir', $upload_dir . $upload->getSysSetPath());
				$thumb_width = '240,1024';
				$thumb_height = '2048,1024';

				$upload->set('max_size', C('image_max_filesize'));
				$upload->set('thumb_width', $thumb_width);
				$upload->set('thumb_height', $thumb_height);
				$upload->set('fprefix', $member_id);
				$upload->set('thumb_ext', '_240,_1024');
				$result = $upload->upfile($file_key[$i]);
				if (!$result) {
					responseApiJson(0, $upload->error, '1');
				}
				$img_path = $upload->getSysSetPath() . $upload->file_name;
				list($width, $height, $type, $attr) = getimagesize(BASE_UPLOAD_PATH . DS . ATTACH_MALBUM . DS . $member_id . DS . $img_path);
				$image = explode('.', $img_path);
				$model_sns_alumb = Model('sns_album');
				$ac_id = $model_sns_alumb->getSnsAlbumClassDefault($member_id);
				$insert = array();
				$insert['ap_name'] = $image['0'];
				$insert['ac_id'] = $ac_id;
				$insert['ap_cover'] = $img_path;
				$insert['ap_size'] = intval($_FILES[$i]['size']);
				$insert['ap_spec'] = $width . 'x' . $height;
				$insert['upload_time'] = time();
				$insert['member_id'] = $member_id;
				$model->table('sns_albumpic')->insert($insert);
				$data[] = $img_path;
			}
			responseApiJson(1, '', $data);
		} else {
			$upload = new UploadFile();
			$upload_dir = ATTACH_MALBUM . DS . $member_id . DS;
			$upload->set('default_dir', $upload_dir . $upload->getSysSetPath());
			$thumb_width = '240,1024';
			$thumb_height = '2048,1024';

			$upload->set('max_size', C('image_max_filesize'));
			$upload->set('thumb_width', $thumb_width);
			$upload->set('thumb_height', $thumb_height);
			$upload->set('fprefix', $member_id);
			$upload->set('thumb_ext', '_240,_1024');
			$result = $upload->upfile(key($_FILES));
			$img_path = $upload->getSysSetPath() . $upload->file_name;
			list($width, $height, $type, $attr) = getimagesize(BASE_UPLOAD_PATH . DS . ATTACH_MALBUM . DS . $member_id . DS . $img_path);

			$image = explode('.', $_FILES["file"]["name"]);
			$model_sns_alumb = Model('sns_album');
			$ac_id = $model_sns_alumb->getSnsAlbumClassDefault($member_id);
			$insert = array();
			$insert['ap_name'] = $image['0'];
			$insert['ac_id'] = $ac_id;
			$insert['ap_cover'] = $img_path;
			$insert['ap_size'] = intval($_FILES['file']['size']);
			$insert['ap_spec'] = $width . 'x' . $height;
			$insert['upload_time'] = time();
			$insert['member_id'] = $member_id;
			$model->table('sns_albumpic')->insert($insert);
			$data[] = $img_path;
		}
		responseApiJson(1, '', $data);
	}

}