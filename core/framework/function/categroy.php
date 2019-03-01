<?php
/**
 * 分类统一调用函数
 */

defined('In33hao') or exit('Access Invild!');

function g_cat_cthumb($file = '', $type = '') {

	$type_array = explode(',_', ltrim(CATEGROY_IMAGES_EXT, '_'));
	if (!in_array($type, $type_array)) {
		$type = '240';
	}

	if (empty($file)) {
		return UPLOAD_SITE_URL . '/' . defaultCategroyImage($type);
	}

	$search_array = explode(',', CATEGROY_IMAGES_EXT);
	$file = str_ireplace($search_array, '', $file);

	if (!C('oss.open')) {

		// 本地存储时，增加判断文件是否存在，用默认图代替
		if (!file_exists(BASE_UPLOAD_PATH . DS . ATTACH_MOBILE . DS . 'category' . DS . $file)) {
			return UPLOAD_SITE_URL . DS . defaultCategroyImage($type);
		} else {
			return UPLOAD_SITE_URL . DS . ATTACH_MOBILE . DS . 'category' . DS . $file;
		}
	} else {
		return C('oss.img_url') . '/' . ATTACH_GOODS . '/' . $store_id . '/' . $file . '@!product-' . $type;
	}
}
