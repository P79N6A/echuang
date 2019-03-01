<?php
/**
 * 服务中心
 *
 */
defined('In33hao') or exit('Access Invild!');
class serviceControl extends SystemControl
{
    public function __construct()
    {
        parent::__construct();
    }

    public function indexOp()
    {
        $this->service_manageOp();
    }

    /**
     * service_manageOp 服务中心管理
     * @return [type] [description]
     */
    public function service_manageOp()
    {
        Tpl::setDirquna('service_center');
        Tpl::showpage('service.service_manage');
    }

    /**
     * service_viewOp 查看信息
     * @return [type] [description]
     */
    public function service_viewOp()
    {
        $id = intval(trim($_GET['id']));
        $model_service = Model('service');
        $info = $model_service->getServiceLogInfo(array('slg_id' => $id));
        $mobile_arr = Model('member_extend')->getMemberMobileArr();
        $name_arr = Model('member_extend')->getMemberNameArr();
        if (empty($info)) {
            showMessage('服务中心信息错误', '', '', 'error');
        } else {
            Tpl::output('namearr', $name_arr);
            Tpl::output('mobilearr', $mobile_arr);
            Tpl::output('s_info', $info);
            Tpl::setDirquna('service_center');
            Tpl::showpage('service.service_view', 'null_layout');
        }
    }

    /**
     * service_auditOp 通过审核
     * @return [type] [description]
     */
    public function service_auditOp()
    {
        $id = intval(trim($_GET['id']));
        $model_service = Model('service');
        $info = $model_service->getServiceLogInfo(array('slg_id' => $id));
        if (!empty($info)) {
            $result = Model('member_extend')->auditService($id);

            if (!isset($result['error'])) {
                showMessage('审核成功', '', '', 'succ');
            } else {
                showMessage('审核失败', '', '', 'error');
            }
        } else {
            showMessage('服务中心信息错误', '', '', 'error');
        }
    }
    /**
     * get_xmlOp 获取服务中心
     * @return [type] [description]
     */
    public function get_xmlOp()
    {
        $model_service = Model('service');
        $condition = array();
        if ($_POST['query'] != '') {
            $list = Model('member_extend')->getMemberList(array('member_mobile' => array('like', '%' . $_POST['query'] . '%')));
            if (!empty($list)) {
                $arr = array();
                foreach ($list as $info) {
                    $arr[] = $info['member_id'];
                }
                $condition['slg_member_id'] = array('in', $arr);
            } else {
                $condition[$_POST['qtype']] = array('like', '%' . $_POST['query'] . '%');
            }
            // $condition[$_POST['qtype']] = array('like', '%' . $_POST['query'] . '%');
        }
        $order = 'slg_addtime desc';
        $page = $_POST['rp'];
        $service_list = $model_service->getServiceLogList($condition, '*', $page, $order);
        $mobile_arr = Model('member_extend')->getMemberMobileArr();
        $data = array();
        $data['now_page'] = $model_service->shownowpage();
        $data['total_num'] = $model_service->gettotalnum();
        foreach ($service_list as $v) {
            $param = array();
            if ($v['slg_state'] == 0) {
                $param['operation'] = "<a class='btn green' href='javascript:void(0)' onclick=\"ajax_form('service_info', '查看服务中心申请的明细', 'index.php?act=service&op=service_view&id=" . $v['slg_id'] . "', 640)\" ><i class='fa fa-list-alt'></i>查看</a><a class='btn blue' href='index.php?act=service&op=service_audit&id=" . $v['slg_id'] . "'><i class='fa fa-pencil-square-o'></i>审核</a>";
            } else {
                $param['operation'] = "<a class='btn green' href='javascript:void(0)' onclick=\"ajax_form('service_info', '查看服务中心申请的明细', 'index.php?act=service&op=service_view&id=" . $v['slg_id'] . "', 640)\" ><i class='fa fa-list-alt'></i>查看</a>";
            }
            $param['slg_id'] = $v['slg_id'];
            $param['member_mobile'] = $mobile_arr[$v['slg_member_id']];
            $param['slg_member_name'] = $v['slg_member_name'];
            $param['slg_addtime'] = $v['slg_addtime'] ? date('Y-m-d H:i:s', $v['slg_addtime']) : '';
            $param['slg_state'] = str_replace(array(0, 1, 2), array('待审核', '审核成功', '拒绝'), $v['slg_state']);
            $param['slg_audittime'] = $v['slg_audittime'] ? date('Y-m-d H:i:s', $v['slg_audittime']) : '';
            $data['list'][$v['slg_id']] = $param;
        }
        echo Tpl::flexigridXML($data);
        exit();
    }
    /**
     * 删除
     */
    public function delOp()
    {
        $ids = explode(',', $_GET['id']);
        if (count($ids) == 0) {
            //showMessage(L('wrong_argument'), '', '', 'error');
            exit(json_encode(array('state' => false, 'msg' => L('wrong_argument'))));
        }
        $conidition = array('slg_id' => array('in', $ids));
        $model_service = Model('service');
        $result = $model_service->deleteServiceLog($conidition);
        if ($result) {
            //showMessage('删除成功', '', '', 'succ');
            exit(json_encode(array('state' => true, 'msg' => '删除成功')));
        } else {
            //showMessage('审核失败', '', '', 'error');
            exit(json_encode(array('state' => false, 'msg' => '删除失败')));
        }
    }
}