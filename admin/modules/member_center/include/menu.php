<?php
/**
 * 菜单
 *
 */
defined('In33hao') or exit('Access Invalid!');

$_menu['member_center'] = array(
    'name' => "会员系统",
    'child' => array(
        array(
            'name' => "会员",
            'child' => array(
                'member' => "会员管理",
//                'bank' => "积分互赠",
            ),
        ),
        array(
            'name' => "财务",
            'child' => array(
                'account' => "账户管理",
				 'account_adjust' => "账户调整",
                'bonus' => "积分明细",
                'recharge' => "积分挂卖管理",
                'withdraw' => "积分回购明细",
                //'bean' => "积分明细",
				//'silver_bean' => "提成管理",
                'equity' => "积分互赠管理",
                //'float_dividend' => "分红管理",
                'financial_statement' => '财务日报表',
                //'bank' => '银行卡管理',
				'member_system_edit' => '高级设置',
			),
		),
	),
);