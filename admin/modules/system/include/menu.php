<?php
/**
 * 菜单
 *
 * @
 * @license
 * @link
 */
defined('In33hao') or exit('Access Invalid!');
$_menu['system'] = array(
	'name' => '商城设置',
	'child' => array(
		array(
			'name' => $lang['nc_config'],
			'child' => array(
				'setting' => $lang['nc_web_set'],
				'upload' => $lang['nc_upload_set'],
				'mb_wx' => '微信二维码',
//				'message' => '邮件设置',
//				'taobao_api' => '淘宝接口',
				'admin' => '权限设置',
//				'admin_log' => $lang['nc_admin_log'],
//				'area' => '地区设置',
//				'task' => '计划任务',
				'cache' => $lang['nc_admin_clear_cache'],

			),
		),
		// array(
		// 	'name' => $lang['nc_member'],
		// 	'child' => array(
		// 		'member' => $lang['nc_member_manage'],
		// 		'account' => $lang['nc_web_account_syn'],
		// 	),
		// ),
		array(
			'name' => $lang['nc_website'],
			'child' => array(
				'article_class' => $lang['nc_article_class'],
				'article' => $lang['nc_article_manage'],
				'document' => $lang['nc_document'],
				'navigation' => $lang['nc_navigation'],
				'adv' => $lang['nc_adv_manage'],
//				'rec_position' => $lang['nc_admin_res_position'],
			),
		),
		array(
			'name' => '应用',
			'child' => array(
				'hao' => '基本设置',
//				'link' => '友情连接',
//				'goods' => '商品评价',
//				'db' => '数据库管理',
//				'store' => '分销管理',
//				'red_packet' => '红包抽奖管理',
			),
		),
	),
);
