<?php
/**
 * 公告控制器
 *
 */
class noticeControl extends BaseMemberControl {
	public function indexOp() {
		$model_notice = Model('notice');
		if (empty($_GET['id'])) {
			showMessage('参数错误', '', '', 'error');
		}
		$notice_info = $model_notice->getNoticeInfo(array('notice_id' => $_GET['id']));
		if (empty($notice_info)) {
			showMessage('公告不存在', '', '', 'error');
		}
		Tpl::output('notice', $notice_info);
		Tpl::showpage('notice.index');
	}
}