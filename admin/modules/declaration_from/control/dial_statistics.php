<?php
/**
 * 拨比统计模块
 */
defined('In33hao') or exit('Access Invild!');

class dial_statisticsControl extends SystemControl {

	/**
	 * __construct 构造函数
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * indexOp 拨比统计模块入口
	 * @return [type] [description]
	 */
	public function indexOp() {
		$this->dial_statisticsOp();
	}

	/**
	 * dial_statisticsOp 拨比统计
	 * @return [type] [description]
	 */
	public function dial_statisticsOp() {
		$model_finacial_statement = Model('finacial_statement');
		$condition = array();
		$total_consume = $model_finacial_statement->getTotalConsumeAmount($condition);
		$total_bonus = $model_finacial_statement->getTotalBonusAmount($condition);

		$total_radio = ncPriceFormat($total_bonus / $total_consume * 100);
		Tpl::output('total_consume_amount', $total_consume);
		Tpl::output('total_bonus_amount', $total_bonus);
		Tpl::output('total_dail_ratio', $total_radio);
		Tpl::setDirquna('member_center');
		Tpl::showpage('dial_statistics');
	}

	/**
	 * get_xmlOp 获取xml数据
	 * @return [type] [description]
	 */
	public function get_xmlOp() {
		$condition = array();
		$model_finacial_statement = Model('finacial_statement');
		$fields = 'id,bean_consume_amount,bonus_amount,dail_ratio,fs_addtime';
		$page = $_POST['rp'];
		$list = $model_finacial_statement->getFinacialStatementList($condition, $fields, $page);

		$data = array();
		$data['now_page'] = $model_finacial_statement->shownowpage();
		$data['total_num'] = $model_finacial_statement->gettotalnum();
		foreach ($list as $v) {
			$param = array();
			$param['operation'] = "-----";
			$param['bean_consume_amount'] = ncPriceFormat($v['bean_consume_amount']);
			$param['bonus_amount'] = ncPriceFormat($v['bonus_amount']);
			$param['dail_ratio'] = $v['dail_ratio'] . '%';
			$param['fs_addtime'] = date('Y-m-d', $v['fs_addtime']);
			$data['list'][$v['id']] = $param;
		}
		Tpl::flexigridXML($data);
	}

	/**
	 * export_xlsOp 导出xls文件
	 * @return [type] [description]
	 */
	public function export_xlsOp() {
		import('libraries.excel');
		$model_finacial_statement = Model('finacial_statement');
		$id = $_GET['id'];
		if ($id) {
			$condition['id'] = array('in', $id);
			$data = $model_finacial_statement->getFinacialStatementList($condition, '*', 'union');
		} else {
			$data = $model_finacial_statement->getFinacialStatementList();
		}

		$excel_obj = new Excel();
		$excel_data = array();
		// 设置样式
		$excel_obj->setStyle(array('id' => 's_title', 'Font' => array('FontName' => '宋体', 'Size' => '12', 'Bold' => '1')));
		// header
		$excel_data[0][] = array('styleid' => 's_title', 'data' => '消费总额');
		$excel_data[0][] = array('styleid' => 's_title', 'data' => '奖金总额');
		$excel_data[0][] = array('styleid' => 's_title', 'data' => '拨比比率');
		$excel_data[0][] = array('styleid' => 's_title', 'data' => '时间');
		foreach ((array) $data as $k => $v) {
			$tmp = array();
			$tmp[] = array('data' => ncPriceFormat($v['bean_consume_amount']));
			$tmp[] = array('data' => ncPriceFormat($v['bonus_amount']));
			$tmp[] = array('data' => $v['dail_ratio'] . '%');
			$tmp[] = array('data' => date('Y-m-d', $v['fs_addtime']));
			$excel_data[] = $tmp;
		}
		$excel_data = $excel_obj->charset($excel_data, CHARSET);
		$excel_obj->addArray($excel_data);
		$excel_obj->addWorksheet($excel_obj->charset('拨比统计', CHARSET));
		$excel_obj->generateXML($excel_obj->charset('拨比统计', CHARSET) . $_GET['curpage'] . '-' . date('Y-m-d-H', time()));
	}
}