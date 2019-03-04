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
                    <li title="激活会员人数：<?php echo $output['statnew_arr']['vip_num']; ?>人">
                        <h4>激活会员人数</h4>
                        <h2 class="timer" id="count-number" data-to="<?php echo $output['statnew_arr']['vip_num']; ?>" data-speed="1500"></h2>
                    </li>
                    <li title="积分释放数量：<?php echo $output['statnew_arr']['shopkeeper_num']; ?>分">
                        <h4>积分释放数量</h4>
                        <h2 class="timer" id="count-number" data-to="<?php echo $output['statnew_arr']['shopkeeper_num']; ?>" data-speed="1500"></h2>
                    </li>
                    <li title="积分扣除数量：<?php echo $output['statnew_arr']['partner_num']; ?>分">
                        <h4>积分扣除数量</h4>
                        <h2 class="timer" id="count-number" data-to="<?php echo $output['statnew_arr']['partner_num']; ?>" data-speed="1500"></h2>
                    </li>
                    <li title="积分兑换总额：<?php echo $output['statnew_arr']['senior_partner_num']; ?>分">
                        <h4>积分兑换总额</h4>
                        <h2 class="timer" id="count-number" data-to="<?php echo $output['statnew_arr']['senior_partner_num']; ?>" data-speed="1500"></h2>
                    </li>
                    <li title="积分转赠数量：<?php echo $output['statnew_arr']['strategic_partner_num']; ?>分">
                        <h4>积分转赠数量</h4>
                        <h2 class="timer" id="count-number" data-to="<?php echo $output['statnew_arr']['strategic_partner_num']; ?>" data-speed="1500"></h2>
                    </li>
                    <li title="积分转赠笔数：<?php echo $output['statnew_arr']['have_withdraw_amount']; ?>笔">
                        <h4>积分转赠笔数</h4>
                        <h2 class="timer" id="count-number" data-to="<?php echo $output['statnew_arr']['have_withdraw_amount']; ?>" data-speed="1500"></h2>
                    </li>
                    <li title="积分挂卖数量：<?php echo $output['statnew_arr']['behave_withdraw_feean_donate_num']; ?>分">
                        <h4>积分挂卖数量</h4>
                        <h2 class="timer" id="count-number" data-to="<?php echo $output['statnew_arr']['have_withdraw_fee']; ?>" data-speed="1500"></h2>
                    </li>
                    <li title="积分挂卖手续费：<?php echo $output['statnew_arr']['direct_prize']; ?>分">
                        <h4>积分挂卖手续费</h4>
                        <h2 class="timer" id="count-number" data-to="<?php echo $output['statnew_arr']['direct_prize']; ?>" data-speed="1500"></h2>
                    </li>
                    <li title="积分回购数量：<?php echo $output['statnew_arr']['strategic_partner_bonus']; ?>分">
                        <h4>积分回购数量</h4>
                        <h2 class="timer" id="count-number" data-to="<?php echo $output['statnew_arr']['strategic_partner_bonus']; ?>" data-speed="1500"></h2>
                    </li>
                    <li title="">
                    </li>
                    <li title="">
                    </li>
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