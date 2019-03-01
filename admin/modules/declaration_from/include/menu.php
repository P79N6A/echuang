<?php
/**
 * 菜单
 *
 */
defined('In33hao') or exit('Access Invalid!');

$_menu['declaration_from'] = array(
    'name' => "报单系统",
    'child' => array(
        array(
            'name' => "报单",
            'child' => array(
				'form_system_edit' => '设置',
                'declaration_list' => "报单列表",
			),
		),
	),
);