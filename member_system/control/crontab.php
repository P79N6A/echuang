<?php
/**
 * 计划任务
 *
 */
defined('In33hao') or exit('Access Invild!');
class crontabControl extends BaseCommonControl {
	public function __construct() {
		parent::__construct();
	}

	/**
	 * indexOp 入口
	 * @return [type] 计划任务入口
	 */
	public function indexOp() {
		$model_crontab = Model('crontab');
		Model()->table('task_log')->insert(array('add_time' => date('Y-m-d H:i:s')));
		$model_member_extend = Model('member_extend');
		$crontab_log_data = array();
		$time_start = getMicrotime();

		// 双轨碰撞
		$logic_collision = Logic('collision');
		$result = $logic_collision->collisionStart();
		if (isset($result['error'])) {
			Log::record($result['error'], 'ERR');
			$crontab_log_data['crontab_collision'] = 0;
		} else {
			$time_end = getMicrotime();
			Log::record('碰撞执行时间' . (float) ($time_end - $time_start) . "\r\n", 'LOG');
		}

		$logic_dividend = Logic('dividend');

		// 股权转静态分红
		if (C('dividend_status') == '1') {
			$time_start = getMicrotime();
			$logic_dividend->staticDividends();
			$time_end = getMicrotime();
			Log::record('静态分红执行时间' . (float) ($time_end - $time_start) . "\r\n", 'LOG');
		}

		// 奖金日结
		$time_start = getMicrotime();
		$result = $logic_dividend->bonusDailyKnot();
		$time_end = getMicrotime();
		Log::record('奖金日结执行时间' . (float) ($time_end - $time_start) . "\r\n", 'LOG');

		// 奖金统计
		$nowTime = TIMESTAMP;
		$startTime = strtotime(date('Y-m-d', TIMESTAMP));
		$endTime = $startTime + 86400 - 1;
		$condition = array('between', array($startTime, $endTime));
		$statement_info = Model('finacial_statement')->getFinacialStatementInfo(array('fs_addtime' => $condition));
		$arr = Model('finacial_statement')->getFinacialStatementArr($condition);

		if (empty($statement_info)) {
			$arr['fs_addtime'] = $startTime;
			$arr['fs_updatetime'] = $nowTime;
			Model('finacial_statement')->addFinacialStatement($arr);
		} else {
			$arr['fs_updatetime'] = $nowTime;
			Model('finacial_statement')->editFinacialStatement(array('id' => $statement_info['id']), $arr);
		}

		// 封号操作
		Logic('honor')->honor();
		dump('计划任务执行成功');
	}

	public function testOp() {
		// if (isset($_POST['key']) && $_POST['key']) {
		// 	$key = $_POST['key'];
		// 	unset($_POST['key']);
		// 	foreach ($_POST as $k => $v) {
		// $_POST[$k] = EncryptUtil::decrypt(trim($v), $key);
		// 	}

		// }
		// foreach ($_POST as $k => $v) {
		// 	Log::record("key:" . $k . " value:" . $v . "\r\n");
		// }
		// echo json_encode(array('status' => 1, 'msg' => '', 'data' => $_POST));
		$key = 'yDJZey35ZiJ2DJnC';
		$iv = 'y3ZieJ5nnDBR7Q2C';
		$en_data = base64_encode(openssl_encrypt("1111111111111111", "aes-128-cbc", $key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $iv));
		dump($en_data);
		// ("openssl_encrypt:");
		// var_dump(bin2hex(base64_decode($en_data)));
		// var_dump($en_data);
		$de_data = openssl_decrypt(base64_decode($en_data), "aes-128-cbc", $key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $iv);
		// var_dump("openssl_decrypt:");
		// var_dump($de_data);
		// var_dump(openssl_error_string());
		dump($de_data);
	}
}
