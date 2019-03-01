<?php
/**
 * 平台公告
 *
 */
defined('In33hao') or exit('Access Invild!');
class notice_settingControl extends SystemControl {
	public function __construct() {
		parent::__construct();
	}

	public function indexOp() {
		$this->notice_manageOp();
	}

	/**
	 * notice_manageOp 公告管理
	 * @return [type] [description]
	 */
	public function notice_manageOp() {
		Tpl::setDirquna('system_center');
		Tpl::showpage('notice_setting.notice_manage');
	}

	/**
	 * notice_addOp 添加公告
	 * @return [type] [description]
	 */
	public function notice_addOp() {
		$model_notice = Model('notice');
		if (chksubmit()) {
			$validate = new Validate();
			$validate->validateparam = array(
				array('input' => $_POST['notice_title'], 'require' => "true", 'message' => "公告标题不能为空"),
				array('input' => $_POST['nc_id'], 'require' => "true", 'message' => '请选择公告类型'),
				array('input' => $_POST['notice_content'], 'require' => "true", 'message' => "公告内容不能为空"),
			);
			$error = $validate->validate();
			if ($error != '') {
				showMessage($error, '', '', 'error');
			}
			$data = array();
			$data['notice_ncid'] = trim($_POST['nc_id']);
			$data['notice_ifshow'] = trim($_POST['notice_show']);
			$data['notice_sort'] = trim($_POST['notice_sort']);
			$data['notice_title'] = trim($_POST['notice_title']);
			$data['notice_content'] = $_POST['notice_content'];
			$data['notice_addtime'] = TIMESTAMP;
			$result = $model_notice->addNotice($data);
			if (!isset($result['error'])) {
				showMessage('添加公告成功', 'index.php?act=notice_setting&op=notice_manage', '', 'succ');
			} else {
				showMessage('添加公告失败', '', '', 'error');
			}
		}
		$noticeClass_arr = $model_notice->getNoticeClassArr();
		Tpl::output('nc_arr', $noticeClass_arr);
		Tpl::setDirquna('system_center');
		Tpl::showpage('notice_setting.notice_add');
	}

	/**
	 * notice_deleteOp 删除公告
	 * @return [type] [description]
	 */
	public function notice_deleteOp() {
		$model_notice = Model('notice');
		$id = intval($_GET['del_id']);
		$state = 0;
		$msg = "删除公告失败";
		$result = $model_notice->delNotice(array('notice_id' => $id));
		if (!$result['error']) {
			$state = 1;
			$msg = '删除公告成功';
		}
		echo json_encode(array('state' => $state, 'msg' => $msg));
	}

	/**
	 * notice_editOp description]
	 * @return [type] [description]
	 */
	public function notice_editOp() {
		$model_notice = Model('notice');
		$notice_id = $_GET['id'] ? $_GET['id'] : $_POST['id'];
		$notice_info = $model_notice->getNoticeInfo(array('notice_id' => $notice_id));
		if (empty($notice_info)) {
			showMessage('公告信息错误', '', '', 'error');
		}
		if (chksubmit()) {
			$validate = new Validate();
			$validate->validateparam = array(
				array('input' => $_POST["notice_title"], 'require' => "true", 'message' => "公告标题不能为空"),
				array('input' => $_POST["nc_id"], 'require' => "true", 'message' => "公告类型不能为空"),
				array('input' => $_POST["notice_sort"], 'require' => "true", 'message' => "公告排序不能为空"),
				array('input' => $_POST["notice_content"], 'require' => "true", 'message' => "公告內容不能为空"),
			);
			$error = $validate->validate();
			if ($error != '') {
				showMessage($error, '', '', 'error');
			}

			$notice_data = array();
			$notice_data['notice_title'] = trim($_POST['notice_title']);
			$notice_data['notice_ncid'] = trim($_POST['nc_id']);
			$notice_data['notice_ifshow'] = trim($_POST['notice_show']);
			$notice_data['notice_sort'] = trim($_POST['notice_sort']);
			$notice_data['notice_content'] = trim($_POST['notice_content']);
			$notice_data['notice_addtime'] = TIMESTAMP;
			try {
				$model_notice = $model_notice->editNotice(array('notice_id' => $notice_id), $notice_data);
				showMessage('公告编辑成功', 'index.php?act=notice_setting&op=notice_manage', '', 'succ');
			} catch (Exception $exception) {
				showMessage('公告编辑失败', '', '', 'error');
			}

		} else {

			$noticeClass_arr = $model_notice->getNoticeClassArr();
			Tpl::setDirquna('system_center');
			Tpl::output('nc_arr', $noticeClass_arr);
			Tpl::output('notice', $notice_info);
			Tpl::showpage('notice_setting.notice_edit');
		}

	}

	/**
	 * get_xmlOp 获取公告数据
	 * @return [type] [description]
	 */
	public function get_xmlOp() {
		$model_notice = Model('notice');
		$condition = array();
		$noticeClass_arr = $model_notice->getNoticeClassArr();
		$page = $_POST['rp'];
		$order = 'notice_sort desc';
		$notice_list = $model_notice->getNoticeList($condition, '*', $page, $order);
		$data = array();
		$data['now_page'] = $model_notice->shownowpage();
		$data['total_num'] = $model_notice->gettotalnum();
		foreach ($notice_list as $v) {
			$param = array();
			$param['operation'] = "<a class='btn red' onclick=\"fg_delete({$v['notice_id']})\"><i class='fa fa-trash-o'></i>删除</a><a class='btn blue' href='index.php?act=notice_setting&op=notice_edit&id={$v['notice_id']}'><i class='fa fa-pencil-square-o'></i>编辑</a>";
			$param['notice_title'] = $v['notice_title'];
			$param['notice_type'] = $noticeClass_arr[$v['notice_ncid']];
			$param['notice_addtime'] = date('Y-m-d', $v['notice_addtime']);
			$param['notice_ifshow'] = str_replace(array(0, 1), array('否', '是'), $v['notice_ifshow']);
			$data['list'][$v['notice_id']] = $param;
		}
		echo Tpl::flexigridXML($data);
		exit();
	}
}