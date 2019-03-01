<?php
/**
 * 收益分配
 *
 */
defined('In33hao') or exit('Access Invild!');
class income_settingControl extends SystemControl {
	public function __construct() {
		parent::__construct();
	}

	public function indexOp() {
		$model_setting = Model('setting');
		if (chksubmit()) {
			$data = array();
			$data['bean_equity_raito'] = floatval(trim($_POST['bean_equity_raito']));
			$data['static_income_rebate_percentage'] = intval(trim($_POST['static_income_rebate_percentage']));
			$data['bean_max_consume_amount'] = intval(trim($_POST['bean_max_consume_amount']));
			$data['bean_max_consume_num'] = intval(trim($_POST['bean_max_consume_num']));
			$data['service_bean_max_donate_num'] = intval(trim($_POST['service_bean_max_donate_num']));
			$data['one_rebate_percentage'] = intval(trim($_POST['one_rebate_percentage']));
			$data['two_rebate_percentage'] = intval(trim($_POST['two_rebate_percentage']));
			$data['three_rebate_percentage'] = intval(trim($_POST['three_rebate_percentage']));
			$data['collision_rebate_percentage'] = intval(trim($_POST['collision_rebate_percentage']));
			$data['withdraw_charge_percentage'] = intval(trim($_POST['withdraw_charge_percentage']));
			$data['withdraw_charge_limit'] = intval(trim($_POST['withdraw_charge_limit']));
			$data['equity_max_amount'] = trim($_POST['equity_max_amount']);
			$data['equity_limit'] = intval(trim($_POST['equity_limit']));
			$data['bean_lt_recieve_limit'] = intval(trim($_POST['bean_lt_recieve_limit']));
			$data['bean_egt_recieve_limit'] = intval(trim($_POST['bean_egt_recieve_limit']));
			$data['bean_lt_recieve_accumulate_limit'] = intval(trim($_POST['bean_lt_recieve_accumulate_limit']));
			$data['bean_egt_recieve_accumulate_limit'] = intval(trim($_POST['bean_egt_recieve_accumulate_limit']));
			$data['equity_dividend_min_limit'] = intval(trim($_POST['equity_dividend_min_limit']));
			$data['equity_dividend_max_limit'] = intval(trim($_POST['equity_dividend_max_limit']));
			$data['dividend_status'] = $_POST['dividend_status'];
			$data['dividend_consume_multiple'] = intval(trim($_POST['dividend_consume_multiple']));
			$data['honor_status'] = $_POST['honor_status'];
			$data['honor_days'] = intval($_POST['honor_days']);
			$data['honor_equity_num_limit'] = floatval(trim($_POST['honor_equity_num_limit']));
			$task_start_time = $_POST['task_start_time'];
			if (strcmp(C('task_start_time'), $task_start_time) != 0) {
				if (checkIsLinuxOs()) {
					if ($this->taskSetting($task_start_time)) {
						$data['task_start_time'] = $task_start_time;
					} else {
						$data['task_start_time'] = C('task_start_time');
					}
				} else {
					$data['task_start_time'] = $task_start_time;
				}
			}
			$result = $model_setting->updateSetting($data);
			if ($result) {
				manualDelCacheFile('setting');
				showMessage('参数设置成功', '', '', 'succ');
			} else {
				showMessage('参数设置失败', '', '', 'error');
			}
		}
		$setting_list = $model_setting->getListSetting();
		Tpl::output('s_info', $setting_list);
		Tpl::setDirquna('member_center');
		Tpl::showpage('income_setting.index');
	}

	/**
	 * taskSetting 计划任务设置
	 * @return [type] [description]
	 */
	private function taskSetting($time) {
		$shell = "sudo crontab -l > /tmp/crontab.bak";
		exec($shell, $result, $status);
		$arr[] = $status;
		list($hour, $second) = explode(':', $time);
		$shell = "sudo echo '" . $second . " " . $hour . " * * * /usr/bin/curl http://www.szbeilaid.com/member_system/index.php?act=crontab' > /tmp/crontab.bak";
		exec($shell, $result, $status);
//        $shell = "<font color='red'>$shell</font>";
		//        echo "<pre>";
		//        if ($status) {
		//            echo "shell命令{$shell}执行失败";
		//        } else {
		//            echo "shell命令{$shell}成功执行, 结果如下<hr>";
		//            print_r($result);
		//        }
		//        die;
		$arr[] = $status;
		$shell = "sudo crontab /tmp/crontab.bak";
		exec($shell, $result, $status);
		$arr[] = $status;
		// $shell = "sudo systemctl reload crond.service";
		// exec($shell, $result, $status);
		// $arr[] = $status;
		// $shell = "sudo systemctl restart crond.service";
		// exec($shell, $result, $status);
		// $arr[] = $status;
		if (!in_array(1, $arr)) {
			return true;
		} else {
			return false;
		}
	}
}