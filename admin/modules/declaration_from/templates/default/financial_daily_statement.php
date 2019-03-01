<?php defined('In33hao') or exit('Access Invild!');?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <div class="subject">
                <h3>财务日报表</h3>
                <h5>财务日报表</h5>
            </div>
            <?php echo $output['top_link']; ?> </div>
    </div>
    <div class="explanation" id="explanation">
        <div class="title" id="checkZoom"><i class="fa fa-lightbulb-o"></i>
            <h4 title=""><?php echo $lang['nc_prompts']; ?></h4>
            <span id="explanationZoom" title="<?php echo $lang['nc_prompts_span']; ?>"></span> </div>
        <ul>
            <li>通过日报表，可以查看会员系统每天的会员注册、消费、奖金情况</li>
        </ul>
        <ul>
            <li>日报表显示的为当天的实时数据，静态分红总额、推广奖金总额、拓展奖等奖金只有当天晚上日结算后才会显示，搜索前一 天的日报表则会显示所有的数据。</li>
        </ul>
    </div>
    <div class="ncap-form-all ncap-stat-general">
        <div class="title">
            <form method="post" action="index.php?act=financial_statement&op=financial_daily_statement" name="formSearch" id="formSearch">
                <h3><?php echo @date('Y-m-d', $output['stat_time']); ?>最新报表</h3>
                <div class="sDiv">
                    <span>搜索日期</span>
                    <label class="sDiv2">
                        <input id="search_time" class="s-input-txt hasDatapicker" value="<?php echo @date('Y-m-d', $output['stat_time']); ?>" name="search_time" readonly="readonly" type="text" />
                    <input type="hidden" id="hei" value="<?php print_r($output['statnew_arr']); ?>">
                        <input class="btn" value="搜索" type="button" />
                    </label>
                </div>
            </form>
        </div>
        <dl class="row">
            <dd class="opt">
                <ul class="nc-row">
                    <li title="会员注册人数：<?php echo $output['statnew_arr']['member_regnum']; ?>人">
                        <h4>会员注册人数</h4>
                        <h2 class="timer" id="count-number" data-to="<?php echo $output['statnew_arr']['member_regnum']; ?>" data-speed="1500"></h2>
                    </li>
                    <li title="VIP人数：<?php echo $output['statnew_arr']['vip_num']; ?>人">
                        <h4>VIP人数</h4>
                        <h2 class="timer" id="count-number" data-to="<?php echo $output['statnew_arr']['vip_num']; ?>" data-speed="1500"></h2>
                    </li>
                    <li title="店主人数：<?php echo $output['statnew_arr']['shopkeeper_num']; ?>人">
                        <h4>店主人数</h4>
                        <h2 class="timer" id="count-number" data-to="<?php echo $output['statnew_arr']['shopkeeper_num']; ?>" data-speed="1500"></h2>
                    </li>
                    <li title="合伙人人数：<?php echo $output['statnew_arr']['partner_num']; ?>人">
                        <h4>合伙人人数</h4>
                        <h2 class="timer" id="count-number" data-to="<?php echo $output['statnew_arr']['partner_num']; ?>" data-speed="1500"></h2>
                    </li>
                    <li title="高级合伙人人数：<?php echo $output['statnew_arr']['senior_partner_num']; ?>人">
                        <h4>高级合伙人人数</h4>
                        <h2 class="timer" id="count-number" data-to="<?php echo $output['statnew_arr']['senior_partner_num']; ?>" data-speed="1500"></h2>
                    </li>
                    <li title="战略合伙人人数：<?php echo $output['statnew_arr']['strategic_partner_num']; ?>人">
                        <h4>战略合伙人人数</h4>
                        <h2 class="timer" id="count-number" data-to="<?php echo $output['statnew_arr']['strategic_partner_num']; ?>" data-speed="1500"></h2>
                    </li>
                    <li title="已提现金额：<?php echo $output['statnew_arr']['have_withdraw_amount']; ?>元">
                        <h4>已提现金额</h4>
                        <h2 class="timer" id="count-number" data-to="<?php echo $output['statnew_arr']['have_withdraw_amount']; ?>" data-speed="1500"></h2>
                    </li>
                    <li title="已提现手续费：<?php echo $output['statnew_arr']['behave_withdraw_feean_donate_num']; ?>元">
                        <h4>已提现手续费</h4>
                        <h2 class="timer" id="count-number" data-to="<?php echo $output['statnew_arr']['have_withdraw_fee']; ?>" data-speed="1500"></h2>
                    </li>
                    <li title="VIP推荐津贴(直推)：<?php echo $output['statnew_arr']['direct_prize']; ?>元">
                        <h4>VIP推荐津贴(直推)</h4>
                        <h2 class="timer" id="count-number" data-to="<?php echo $output['statnew_arr']['direct_prize']; ?>" data-speed="1500"></h2>
                    </li>
                    <li title="战略合伙人分红：<?php echo $output['statnew_arr']['strategic_partner_bonus']; ?>元">
                        <h4>战略合伙人分红</h4>
                        <h2 class="timer" id="count-number" data-to="<?php echo $output['statnew_arr']['strategic_partner_bonus']; ?>" data-speed="1500"></h2>
                    </li>
                    <li title="股权兑换数：<?php echo $output['statnew_arr']['equity_exchange_amount']; ?>">
                        <h4>股权兑换数</h4>
                        <h2 class="timer" id="count-number" data-to="<?php echo $output['statnew_arr']['equity_exchange_amount']; ?>" data-speed="1500"></h2>
                    </li>
<!--                    <li title="提现手续费：--><?php //echo $output['statnew_arr']['withdraw_fee_amount']; ?><!--元">-->
<!--                        <h4>提现手续费</h4>-->
<!--                        <h2 class="timer" id="count-number" data-to="--><?php //echo $output['statnew_arr']['withdraw_fee_amount']; ?><!--" data-speed="1500"></h2>-->
<!--                    </li>-->
<!--                    <li title="静态分红总额：--><?php //echo $output['statnew_arr']['static_dividend_amount']; ?><!--元">-->
<!--                        <h4>静态分红总额</h4>-->
<!--                        <h2 class="timer" id="count-number" data-to="--><?php //echo $output['statnew_arr']['static_dividend_amount']; ?><!--" data-speed="1500"></h2>-->
<!--                    </li>-->
<!--                    <li title="推广奖金总额：--><?php //echo $output['statnew_arr']['promotion_bonus_amount']; ?><!--元">-->
<!--                        <h4>推广奖金总额</h4>-->
<!--                        <h2 class="timer" id="count-number" data-to="--><?php //echo $output['statnew_arr']['promotion_bonus_amount']; ?><!--" data-speed="1500"></h2>-->
<!--                    </li>-->
<!--                    <li title="拓展奖总额：--><?php //echo $output['statnew_arr']['extend_bonus_amount']; ?><!--" 元>-->
<!--                        <h4>拓展奖总额</h4>-->
<!--                        <h2 class="timer" id="count-number" data-to="--><?php //echo $output['statnew_arr']['extend_bonus_amount']; ?><!--" data-speed="1500"></h2>-->
<!--                    </li>-->
<!--                    <li title="奖金总额：--><?php //echo $output['statnew_arr']['bonus_amount']; ?><!--元">-->
<!--                        <h4>奖金总额</h4>-->
<!--                        <h2 class="timer" id="count-number" data-to="--><?php //echo $output['statnew_arr']['bonus_amount']; ?><!--" data-speed="1500"></h2>-->
<!--                    </li>-->
<!--                    <li title="拔出比率：--><?php //echo $output['statnew_arr']['dail_ratio']; ?><!--%">-->
<!--                        <h4>拔出比率</h4>-->
<!--                        <h2 class="timer" id="count-number" data-to="--><?php //echo $output['statnew_arr']['dail_ratio']; ?><!--" data-speed="1500"></h2>-->
<!--                    </li>-->
<!--                    <li title="浮动分红总额：--><?php //echo $output['statnew_arr']['float_dividend_amount']; ?><!--元">-->
<!--                        <h4>浮动分红总额</h4>-->
<!--                        <h2 class="timer" id="count-number" data-to="--><?php //echo $output['statnew_arr']['float_dividend_amount']; ?><!--" data-speed="1500"></h2>-->
<!--                    </li>-->
                </ul>
            </dd>
        </dl>
    </div>
<!--    <div class="ncap-form-all ncap-stat-general">-->
<!--        <div class="title">-->
<!--            <h3>服务中心统计</h3>-->
<!--        </div>-->
<!--        <dl class="row">-->
<!--            <dd class="opt">-->
<!--                <ul class="nc-row">-->
<!--                    <li title="新增服务中心数量：--><?php //echo $output['statnew_arr']['new_service_center_num']; ?><!--人">-->
<!--                        <h4>新增服务中心数量</h4>-->
<!--                        <h2 class="timer" id="count-number" data-to="--><?php //echo $output['statnew_arr']['new_service_center_num']; ?><!--" data-speed="1500"></h2>-->
<!--                    </li>-->
<!--                    <li title="银豆充值总额：--><?php //echo $output['statnew_arr']['silver_recharge_amount']; ?><!--元">-->
<!--                        <h4>银豆充值总额</h4>-->
<!--                        <h2 class="timer" id="count-number" data-to="--><?php //echo $output['statnew_arr']['silver_recharge_amount']; ?><!--" data-speed="1500"></h2>-->
<!--                    </li>-->
<!--                    <li title="银豆转赠数量：--><?php //echo $output['statnew_arr']['silver_donate_amount']; ?><!--">-->
<!--                        <h4>银豆转赠数量</h4>-->
<!--                        <h2 class="timer" id="count-number" data-to="--><?php //echo $output['statnew_arr']['silver_donate_amount']; ?><!--" data-speed="1500"></h2>-->
<!--                    </li>-->
<!--                    <li title="银豆转赠笔数：--><?php //echo $output['statnew_arr']['silver_donate_num']; ?><!--笔">-->
<!--                        <h4>银豆转赠笔数</h4>-->
<!--                        <h2 class="timer" id="count-number" data-to="--><?php //echo $output['statnew_arr']['silver_donate_num']; ?><!--" data-speed="1500"></h2>-->
<!--                    </li>-->
<!--                </ul>-->
<!--            </dd>-->
<!--        </dl>-->
<!--    </div>-->
    <div class="ncap-form-all ncap-stat-general">
        <div class="title">
            <h3>商城统计</h3>
        </div>
        <dl class="row">
            <dd class="opt">
                <ul class="nc-row">
                    <li title="商城消费总额：<?php echo $output['statnew_arr']['shop_consume_amount']; ?>元">
                        <h4>商城消费总额</h4>
                        <h2 class="timer" id="count-number" data-to="<?php echo $output['statnew_arr']['shop_consume_amount']; ?>" data-speed="1500"></h2>
                    </li>
                    <li title="商城VIP总额：<?php echo $output['statnew_arr']['vip_amount']; ?>元">
                        <h4>商城VIP总额</h4>
                        <h2 class="timer" id="count-number" data-to="<?php echo $output['statnew_arr']['vip_amount']; ?>" data-speed="1500"></h2>
                    </li>
                    <li title="微信支付总额：<?php echo $output['statnew_arr']['wx_pay_amount']; ?>元">
                        <h4>微信支付总额</h4>
                        <h2 class="timer" id="count-number" data-to="<?php echo $output['statnew_arr']['wx_pay_amount']; ?>" data-speed="1500"></h2>
                    </li>
                    <li title="支付宝支付总额：<?php echo $output['statnew_arr']['ali_pay_amount']; ?>元">
                        <h4>支付宝支付总额</h4>
                        <h2 class="timer" id="count-number" data-to="<?php echo $output['statnew_arr']['ali_pay_amount']; ?>" data-speed="1500"></h2>
                    </li>
<!--                    <li title="银豆支付笔数：--><?php //echo $output['statnew_arr']['silver_pay_num']; ?><!--笔">-->
<!--                        <h4>银豆支付笔数</h4>-->
<!--                        <h2 class="timer" id="count-number" data-to="--><?php //echo $output['statnew_arr']['silver_pay_num']; ?><!--" data-speed="1500"></h2>-->
<!--                    </li>-->
<!--                    <li title="微信支付总额：--><?php //echo $output['statnew_arr']['wx_pay_amount']; ?><!--元">-->
<!--                        <h4>微信支付总额</h4>-->
<!--                        <h2 class="timer" id="count-number" data-to="--><?php //echo $output['statnew_arr']['wx_pay_amount']; ?><!--" data-speed="1500"></h2>-->
<!--                    </li>-->
<!--                    <li title="微信支付笔数：--><?php //echo $output['statnew_arr']['wx_pay_num']; ?><!--笔">-->
<!--                        <h4>微信支付笔数</h4>-->
<!--                        <h2 class="timer" id="count-number" data-to="--><?php //echo $output['statnew_arr']['wx_pay_num']; ?><!--" data-speed="1500"></h2>-->
<!--                    </li>-->
<!--                    <li title="支付宝支付总额：--><?php //echo $output['statnew_arr']['ali_pay_amount']; ?><!--元">-->
<!--                        <h4>支付宝支付总额</h4>-->
<!--                        <h2 class="timer" id="count-number" data-to="--><?php //echo $output['statnew_arr']['ali_pay_amount']; ?><!--" data-speed="1500"></h2>-->
<!--                    </li>-->
<!--                    <li title="支付宝支付笔数：--><?php //echo $output['statnew_arr']['ali_pay_num']; ?><!--笔">-->
<!--                        <h4>支付宝支付笔数</h4>-->
<!--                        <h2 class="timer" id="count-number" data-to="--><?php //echo $output['statnew_arr']['ali_pay_num']; ?><!--" data-speed="1500"></h2>-->
<!--                    </li>-->
                </ul>
            </dd>
        </dl>
    </div>
</div>
<script type="text/javascript" src="<?php echo ADMIN_RESOURCE_URL ?>/js/jquery.numberAnimation.js"></script>
<script type="text/javascript" src="<?php echo ADMIN_RESOURCE_URL ?>/js/highcharts.js"></script>
<script type="text/javascript" src="<?php echo ADMIN_RESOURCE_URL ?>/js/statistics.js"></script>
<script type="text/javascript">
$(function() {
    //同步加载flexigrid表格
    $('.flex-table').flexigrid({
        height: 'auto', // 高度自动
        usepager: false, // 不翻页
        striped: false, // 不使用斑马线
        resizable: false, // 不调节大小
        reload: false, // 不使用刷新
        columnControl: false, // 不使用列控制
        title: '7日内店铺销售TOP30'
    });
    $('.flex-table2').flexigrid({
        height: 'auto', // 高度自动
        usepager: false, // 不翻页
        striped: false, // 不使用斑马线
        resizable: false, // 不调节大小
        reload: false, // 不使用刷新
        columnControl: false, // 不使用列控制
        title: '7日内商品销售TOP30'
    });

    $('#container').highcharts(<?php echo $output['stattoday_json']; ?>);
    $('#search_time').datepicker({ dateFormat: 'yy-mm-dd' });
    $('.btn').click(function() {
        $('#formSearch').submit();
    });
    console.log($("#hei").val())
});
</script>