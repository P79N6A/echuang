<?php
/**
 * 积分交易管理
 *
 */
defined('In33hao') or exit('Access Invild!');
class rechargeControl extends SystemControl
{
    public function __construct()
    {
        parent::__construct();
    }

    public function indexOp()
    {
        $this->recharge_manageOp();
    }

    /**
     * recharge_manageOp 积分交易管理
     * @return [type] [description]
     */
    public function recharge_manageOp()
    {
        Tpl::setDirquna('member_center');
        Tpl::showpage('recharge.recharge_manage');
    }

	/**
	 * get_xmlOp
	 * @return [type] [description]
	 */
	public function get_xmlOp() {
		$model_integral_selling = Model('integral_selling');
		$condition = array();
		if ($_POST['query'] != '') {
			if ($_POST['qtype'] == 'member_mobile') {
                $list = Model('member')->getMemberList(array('member_mobile' => array('like', '%' . $_POST['query'] . '%')));
                if (!empty($list)) {
                    $arr = array();
                    foreach ($list as $v) {
                        $arr[] = $v['member_id'];
                    }
                    $condition['member_id'] = array('in', $arr);
                } else {
                    $condition['member_id'] = null;
                }
			} elseif ($_POST['qtype'] == 'rl_member_name') {
                $list = Model('member')->getMemberList(array('member_name' => array('like', '%' . $_POST['query'] . '%')));
                if (!empty($list)) {
                    $arr = array();
                    foreach ($list as $v) {
                        $arr[] = $v['member_id'];
                    }
                    $condition['member_id'] = array('in', $arr);
                } else {
                    $condition['member_id'] = null;
                }
			} else {
				$condition[$_POST['qtype']] = array('like', '%' . $_POST['query'] . '%');
			}
		}

		$order = 'add_time desc';
		$page = $_POST['rp'];
		$list = $model_integral_selling->getIntegralSellingList($condition, '*', $page, $order);
		$mobileArr = Model('member_extend')->getMemberMobileArr();
		$nameArr = Model('member_extend')->getMemberNameArr();
		$state = $model_integral_selling->state;
		$data = array();
		$data['now_page'] = $model_integral_selling->shownowpage();
		$data['total_num'] = $model_integral_selling->gettotalnum();
		foreach ($list as $v) {
			$param = array();
            if ($v['state'] == 1) {
                $param['operation'] = "<a class='btn blue' href='index.php?act=recharge&op=buy_integral&type=one&id=" . $v['id'] . "'><i class='fa fa-pencil-square-o'></i>回购</a><a class='btn blue' href='index.php?act=recharge&op=refuse_integral&type=one&id=" . $v['id'] . "'><i class='fa fa-pencil-square-o'></i>拒绝</a>";
            }else{
                $param['operation'] = '';
            }
            $param['sell_sn'] = $v['sell_sn'];
			$param['member_mobile'] = $mobileArr[$v['member_id']];
			$param['member_name'] = $nameArr[$v['member_id']];
			$param['state'] = $state[$v['state']];
			$param['sell_integral'] = $v['sell_integral'];
			$param['actual_integral'] = $v['actual_integral'];
			$param['add_time'] = $v['add_time'] ? date('Y-m-d', $v['add_time']) : '';
			$param['buy_time'] = $v['buy_time'] ? date('Y-m-d', $v['buy_time']) : '';
			$param['refuse_time'] = $v['refuse_time'] ? date('Y-m-d', $v['refuse_time']) : '';
			$data['list'][$v['id']] = $param;
		}
		echo Tpl::flexigridXML($data);
		exit();
	}

    /**
     * buy_integralOp 积分回购
     * @return [type] [description]
     */
    public function buy_integralOp() {
        if (empty($_GET['id'])){
            showMessage('请选择要操作的数据项！', '', '', 'error');
        }
        $ids = explode(',', $_GET['id']);
        if (count($ids) == 0) {
            if (isset($_GET['type'])) {
                showMessage(L('wrong_argument'), '', '', 'error');
            } else {
                exit(json_encode(array('state' => false, 'msg' => L('wrong_argument'))));
            }
        }
        foreach ($ids as $id) {
            $result = Logic('integral_selling')->buyIntegral($id);
            if (!$result) {
                if (isset($_GET['type'])) {
                    showMessage('积分回购失败', '', '', 'error');
                } else {
                    exit(json_encode(array('state' => false, 'msg' => '积分回购失败')));
                }
            }
        }
        if (isset($_GET['type'])) {
            showMessage('积分回购成功', '', '', 'error');
        } else {
            exit(json_encode(array('state' => true, 'msg' => "积分回购成功")));
        }
    }

    /**
     * refuse_integralOp 拒绝回购
     * @return [type] [description]
     */
    public function refuse_integralOp() {
        if (empty($_GET['id'])){
            showMessage('请选择要操作的数据项！', '', '', 'error');
        }
        $ids = explode(',', $_GET['id']);
        if (count($ids) == 0) {
            if (isset($_GET['type'])) {
                showMessage(L('wrong_argument'), '', '', 'error');
            } else {
                exit(json_encode(array('state' => false, 'msg' => L('wrong_argument'))));
            }
        }
        foreach ($ids as $id) {
            $result = Logic('integral_selling')->refuseIntegralSelling($id);
            if (!$result) {
                if (isset($_GET['type'])) {
                    showMessage('操作失败', '', '', 'error');
                } else {
                    exit(json_encode(array('state' => false, 'msg' => '操作失败')));
                }
            }
        }
        if (isset($_GET['type'])) {
            showMessage('操作成功', '', '', 'error');
        } else {
            exit(json_encode(array('state' => true, 'msg' => "操作成功")));
        }
    }

	/**
	 * export_xlsOp 导出execl文件
	 * @return [type] [description]
	 */
	public function export_xlsOp() {
		import('libraries.excel');
        $model_integral_selling = Model('integral_selling');
		$model_member_extend = Model('member_extend');

        $state = $model_integral_selling->state;
        $data = $model_integral_selling->getIntegralSellingList();

		$mobile_arr = $model_member_extend->getMemberMobileArr();
		$name_arr = $model_member_extend->getMemberNameArr();
		foreach ((array)$data as $key => $value) {
			$data[$key]['member_mobile'] = $mobile_arr[$value['member_id']];
			$data[$key]['member_name'] = $name_arr[$value['member_id']];
		}
		$excel_obj = new Excel();
		$excel_data = array();
		// 设置样式
		$excel_obj->setStyle(array('id' => 's_title', 'Font' => array('FontName' => '宋体', 'Size' => '12', 'Bold' => '1')));
		// header
		$excel_data[0][] = array('styleid' => 's_title', 'data' => '单号');
		$excel_data[0][] = array('styleid' => 's_title', 'data' => '会员手机');
		$excel_data[0][] = array('styleid' => 's_title', 'data' => '真实姓名');
		$excel_data[0][] = array('styleid' => 's_title', 'data' => '状态');
		$excel_data[0][] = array('styleid' => 's_title', 'data' => '挂卖积分');
		$excel_data[0][] = array('styleid' => 's_title', 'data' => '实际到市场积分');
		$excel_data[0][] = array('styleid' => 's_title', 'data' => '挂卖时间');
		$excel_data[0][] = array('styleid' => 's_title', 'data' => '回购时间');
		$excel_data[0][] = array('styleid' => 's_title', 'data' => '拒绝时间');

		foreach ((array) $data as $k => $v) {
			$tmp = array();
			$tmp[] = array('data' => $v['sell_sn']);
			$tmp[] = array('data' => $v['member_mobile']);
			$tmp[] = array('data' => $v['member_name']);
			$tmp[] = array('data' => $state[$v['state']]);
			$tmp[] = array('data' => $v['sell_integral']);
			$tmp[] = array('data' => $v['actual_integral']);
			$tmp[] = array('data' => $v['add_time'] ? date('Y-m-d', $v['add_time']) : '');
			$tmp[] = array('data' => $v['buy_time'] ? date('Y-m-d', $v['buy_time']) : '');
			$tmp[] = array('data' => $v['refuse_time'] ? date('Y-m-d', $v['refuse_time']) : '');
			$excel_data[] = $tmp;
		}
		$excel_data = $excel_obj->charset($excel_data, CHARSET);
		$excel_obj->addArray($excel_data);
		$excel_obj->addWorksheet($excel_obj->charset('积分交易管理', CHARSET));
		$excel_obj->generateXML($excel_obj->charset('积分交易管理', CHARSET) . $_GET['curpage'] . '-' . date('Y-m-d-H', time()));
	}
}