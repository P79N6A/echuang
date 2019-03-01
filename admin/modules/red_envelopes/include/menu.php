<?php
/**
 * 菜单
 *
 */
defined('In33hao') or exit('Access Invalid!');

$_menu['red_envelopes'] = array(
    'name' => "抢红包",
    'child' => array(
        array(
            'name' => "设置",
            'child' => array(
                'envelopes' => "紅包设置",
                'distribution' => "分润设置",
                'interest' => "红包标签",
                'age' => "年龄阶段",
            ),

        ),
        array(
            'name' => "红包",
            'child' => array(
    //          'member_upgrade'        => "会员升级",
    //          'upgrade_return'        => "升级返现记录",
                'expand_area_record'    => "扩大范围记录",
                'city_owner_manage'     => "城主管理",
                'city_owner_record'     => "抢城主记录",
                'redbag_issue'          => "红包审核",
                'redbag_manage'         => "抢红包管理",
                'member_rebate'         => "会员分润管理",
                'city_owner_rebate'     => '城主分润管理',
                'city_price'            => '城主原价设置',
                'merchant_entry'        => '商家入驻',
                'securities'            => '用户优恵劵',
                'business_securities'   => '商家优恵劵',
                //'redbag_push'       => '红包推送',
			),
		),
        array(
            'name' => "广告",
            'child' => array(
                'advertisement'     => "广告记录",
                //'ceshi'           => "测试页",
                'redbag_task'              => '任务',
            ),
         ),
	),
);