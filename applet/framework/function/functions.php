<?php
/**
 * 框架扩展文件
 */

if (!function_exists('responseAppletJson')) {
	function responseAppletJson($status = 0, $msg = '', $data = null) {
		exit(json_encode(array('status' => $status, 'msg' => $msg, 'data' => $data)));
	}
}