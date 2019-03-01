<?php defined('In33hao') or exit('Access Invalid!');?>

<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <div class="subject">
                <h3>收益分配</h3>
                <h5></h5>
            </div>
        </div>
    </div>
    <div class="fixed-bar">
        <div class="item-title">
            <div class="subject">
                <h3>高级设置</h3>
                <h5>会员系统高级设置</h5>
            </div><?php echo $output['top_link']; ?>
        </div>
    </div>
    <form method="post" enctype="multipart/form-data" name="form1">
        <input type="hidden" name="form_submit" value="ok" />
        <div style="margin-left: 10%;margin-right: 10%" class="ncap-form-default system-setting">
            <b style="font-size: small;">销售提成</b>
            <dl class="row">
                <dt class="tit">
                    <label for="vip_commission">VIP：</label>
                </dt>
                <dd class="opt">
                    <input class="w65" id="vip_commission" name="vip_commission" value="<?php echo $output['s_info'][0]['ml_commission'] ? $output['s_info'][0]['ml_commission'] : 100; ?>" class="input-txt" type="text">　元
                    <p class="notic">VIP会员报单,VIP可获得的奖励，设置为0则没有奖励</p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label for="shopkeeper_commission ">店主：</label>
                </dt>
                <dd class="opt">
                    <input class="w100" id="shopkeeper_commission" name="shopkeeper_commission" value="<?php echo $output['s_info'][1]['ml_commission'] ? $output['s_info'][1]['ml_commission'] : 80; ?>" class="input-txt" type="text">&nbsp;元
                    <p class="notic">VIP会员报单，店主可获得的销售提成，设置为0则没有奖励</p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label for="partner_commission">合伙人：</label>
                </dt>
                <dd class="opt">
                    <input class="w100" id="partner_commission" name="partner_commission" value="<?php echo $output['s_info'][2]['ml_commission'] ? $output['s_info'][2]['ml_commission'] : 40; ?>" class="input-txt" type="text">&nbsp;元
                    <p class="notic">VIP会员报单，合伙人可获得的销售提成，设置为0则没有奖励</p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label for="senior_partner_commission">高级合伙人：</label>
                </dt>
                <dd class="opt">
                    <input class="w100" id="senior_partner_commission" name="senior_partner_commission" value="<?php echo $output['s_info'][3]['ml_commission'] ? $output['s_info'][3]['ml_commission'] : 20; ?>" class="input-txt" type="text">&nbsp;元
                    <p class="notic">VIP会员报单，高级合伙人可获得的销售提成，设置为0则没有奖励</p>
                </dd>
            </dl>
<hr>
            <b style="font-size: small;">销售折扣</b>
            <dl class="row">
                <dt class="tit">
                    <label for="vip_ratio">VIP：</label>
                </dt>
                <dd class="opt">
                    <input class="w100" id="vip_ratio" name="vip_ratio" value="<?php echo $output['s_info'][0]['ml_discount_ratio'] ? $output['s_info'][0]['ml_discount_ratio']*100 : 1; ?>" class="input-txt" type="text">&nbsp;%
                    <p class="notic">VIP会员重销后，可获得的重销折扣，设置为0则没有折扣</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="shopkeeper_ratio">店主：</label>
                </dt>
                <dd class="opt">
                    <input class="w100" id="shopkeeper_ratio" name="shopkeeper_ratio" value="<?php echo $output['s_info'][1]['ml_discount_ratio'] ? $output['s_info'][1]['ml_discount_ratio'] *100: 1; ?>" class="input-txt" type="text">&nbsp;%
                    <p class="notic">店主会员重销后，可获得的重销折扣，设置为0则没有折扣</p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label for="partner_ratio">合伙人：</label>
                </dt>
                <dd class="opt">
                    <input class="w100" id="partner_ratio" name="partner_ratio" value="<?php echo $output['s_info'][2]['ml_discount_ratio'] ? $output['s_info'][2]['ml_discount_ratio']*100 : 1; ?>" class="input-txt" type="text">&nbsp;%
                    <p class="notic">合伙人会员重销后，可获得的重销折扣，设置为0则没有折扣</p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label for="senior_partner_ratio">高级合伙人：</label>
                </dt>
                <dd class="opt">
                    <input class="w100" id="senior_partner_ratio" name="senior_partner_ratio" value="<?php echo $output['s_info'][3]['ml_discount_ratio'] ? $output['s_info'][3]['ml_discount_ratio']*100 : 1; ?>" class="input-txt" type="text">&nbsp;%
                    <p class="notic">高级合伙人会员重销后，可获得的重销折扣，设置为0则没有折扣</p>
                </dd>
            </dl>
<hr>
            <b style="font-size: small;">直推奖励</b>
            <dl class="row">
                <dt class="tit">
                    <label for="shopkeeper_direct_prize">VIP会员直推店主分红：</label>
                </dt>
                <dd class="opt">
                    <input class="w100" id="shopkeeper_direct_prize" name="shopkeeper_direct_prize" value="<?php echo $output['s_info'][1]['ml_direct_prize'] ? $output['s_info'][1]['ml_direct_prize'] : 1000; ?>" class="input-txt" type="text" />&nbsp;元
                    <p class="notic">此设置金额*VIP会员直推店主分红，即为直推店主市场分红</p>
                </dd>
            </dl>
<!--<hr>-->
            <dl class="row">
                <dt class="tit">
                    <label for="partner_direct_prize">VIP会员直推合伙人分红：</label>
                </dt>
                <dd class="opt">
                    <input class="w100" id="partner_direct_prize" name="partner_direct_prize" value="<?php echo $output['s_info'][2]['ml_direct_prize'] ? $output['s_info'][2]['ml_direct_prize'] : 2500; ?>" class="input-txt" type="text">&nbsp;元
                    <p class="notic">此设置金额*VIP会员直推合伙人分红，即为直推店主市场分红</p>
                </dd>
            </dl>
<!--<hr>-->
<!--            <b style="font-size: small;">VIP会员直推高级合伙人分红</b>-->
            <dl class="row">
                <dt class="tit">
                    <label for="senior_partner_direct_prize">VIP会员直推高级合伙人分红：</label>
                </dt>
                <dd class="opt">
                    <input class="w100" id="senior_partner_direct_prize" name="senior_partner_direct_prize" value="<?php echo $output['s_info'][3]['ml_direct_prize'] ? $output['s_info'][3]['ml_direct_prize'] : 5000; ?>" class="input-txt" type="text">&nbsp;元
                    <p class="notic">此设置金额*VIP会员直推高级合伙人分红，即为直推店主市场分红</p>
                </dd>
            </dl>
<hr>
            <b style="font-size: small;">平级奖励</b>
<!--            <b>同级高级合伙人下级VIP销售提成一级</b>-->
            <dl class="row">
                <dt class="tit">
                    <label for="senior_partner_commission_v1">同级高级合伙人下级VIP销售提成一级：</label>
                </dt>
                <dd class="opt">
                    <input class="w100" id="senior_partner_commission_v1" name="senior_partner_commission_v1" value="<?php echo $output['other_setting']['senior_partner_commission_v1'] ? $output['other_setting']['senior_partner_commission_v1'] : 10; ?>" class="input-txt" type="text">&nbsp;元
                    <p class="notic">推荐的高级合伙人培训津贴</p>
                </dd>
            </dl>
<!--<hr>-->
<!--            <b>同级高级合伙人下级VIP销售提成二级</b>-->
            <dl class="row">
                <dt class="tit">
                    <label for="senior_partner_commission_v2">同级高级合伙人下级VIP销售提成二级：</label>
                </dt>
                <dd class="opt">
                    <input class="w100" id="senior_partner_commission_v2" name="senior_partner_commission_v2" value="<?php echo $output['other_setting']['senior_partner_commission_v2'] ? $output['other_setting']['senior_partner_commission_v2'] : 5; ?>" class="input-txt" type="text">&nbsp;元
                    <p class="notic">推荐的高级合伙人培训津贴</p>
                </dd>
<!--                <dd class="opt">-->
<!--                    <div>金券分红最小限制值&nbsp;-->
<!--                        <input class="w30" id="equity_dividend_min_limit" name="equity_dividend_min_limit" value="--><?php //echo $output['s_info']['equity_dividend_min_limit'] ? $output['s_info']['equity_dividend_min_limit'] : 3500; ?><!--" class="input-txt" type="text" />，金券分红最大限制值&nbsp;-->
<!--                        <input class="w40" id="equity_dividend_max_limit" name="equity_dividend_max_limit" value="--><?php //echo $output['s_info']['equity_dividend_max_limit'] ? $output['s_info']['equity_dividend_max_limit'] : 35000; ?><!--" class="input-txt" type="text" />，当历史分红总额达到消费金豆数量的&nbsp;-->
<!--                        <input class="w40" id="dividend_consume_multiple" name="dividend_consume_multiple" value="--><?php //echo $output['s_info']['dividend_consume_multiple'] ? $output['s_info']['dividend_consume_multiple'] : 10; ?><!--" class="input-txt" type="text" />倍，需要复投-->
<!--                    </div>-->
<!--                </dd>-->
            </dl>
<!--<hr>-->
<!--            <b>同级高级合伙人下级VIP销售提成三级</b>-->
            <dl class="row">
                <dt class="tit">
                    <label for="senior_partner_commission_v3">同级高级合伙人下级VIP销售提成三级：</label>
                </dt>
                <dd class="opt">
                    <input class="w100" id="senior_partner_commission_v3" name="senior_partner_commission_v3" value="<?php echo $output['other_setting']['senior_partner_commission_v3'] ? $output['other_setting']['senior_partner_commission_v3'] : 5; ?>" class="input-txt" type="text">&nbsp;元
                    <p class="notic">推荐的高级合伙人培训津贴</p>
                </dd>
<!--                <dd class="opt">-->
<!--                    <div>金券限制值&nbsp;-->
<!--                        <input class="w30" id="equity_limit" name="equity_limit" value="--><?php //echo $output['s_info']['equity_limit'] ? $output['s_info']['equity_limit'] : 3500; ?><!--" class="input-txt" type="text" />，少于金券限制值单笔只可接收金豆数&nbsp;-->
<!--                        <input class="w30" type="text" name="bean_lt_recieve_limit" id="bean_lt_recieve_limit" value="--><?php //echo $output['s_info']['bean_lt_recieve_limit']; ?><!--" />，累计接收金豆数不能超过&nbsp;-->
<!--                        <input class="w30" type="text" name="bean_lt_recieve_accumulate_limit" id="bean_lt_recieve_accumulate_limit" value="--><?php //echo $output['s_info']['bean_lt_recieve_accumulate_limit']; ?><!--">；-->
<!--                        <br/>超过金券限制值单笔只可接收金豆数&nbsp;-->
<!--                        <input class="w40" id="bean_egt_recieve_limit" name="bean_egt_recieve_limit" value="--><?php //echo $output['s_info']['bean_egt_recieve_limit']; ?><!--" class="input-txt" type="text" />，累计接收金豆数不能超过&nbsp;-->
<!--                        <input class="w40" type="text" name="bean_egt_recieve_accumulate_limit" id="bean_egt_recieve_accumulate_limit" value="--><?php //echo $output['s_info']['bean_egt_recieve_accumulate_limit']; ?><!--">-->
<!--                    </div>-->
<!--                </dd>-->
            </dl>
<hr>
<!--            <b>提现手续费</b>-->
            <dl class="row">
                <dt class="tit">提现手续费：</dt>
                <dd class="opt">
                    <input class="w100" id="withdraw_poundage" name="withdraw_poundage" value="<?php echo $output['member_system_setting'][0]['withdraw_poundage'] ? $output['member_system_setting'][0]['withdraw_poundage'] : 0.5; ?>" class="input-txt" type="text">&nbsp;%
                    <p class="notic">每笔提现扣除的手续费</p>
                </dd>
<!--                <dd class="opt">-->
<!--                    <div class="onoff">-->
<!--                        <label for="honor_status1" class="cb-enable --><?php //if ($output['s_info']['honor_status'] == '1') {?><!--selected--><?php //}?><!--">-->
<!--                            开启-->
<!--                        </label>-->
<!--                        <label for="honor_status0" class="cb-disable --><?php //if ($output['s_info']['honor_status'] == '0') {?><!--selected--><?php //}?><!--">-->
<!--                            关闭-->
<!--                        </label>-->
<!--                        <input id="honor_status1" name="honor_status" --><?php //if ($output['s_info']['honor_status'] == '1') {?><!--checked="checked"-->
<!--                        --><?php //}?><!-- value="1" type="radio" />-->
<!--                        <input id="honor_status0" name="honor_status" --><?php //if ($output['s_info']['honorstatus'] == '0') {?><!--checked="checked"-->
<!--                        --><?php //}?><!-- value="0" type="radio" />-->
<!--                    </div>-->
<!--                    <p class="notic"></p>-->
<!--                </dd>-->
            </dl>
<hr>
<!--            <b>提现限制</b>-->
<!--            <dl class="row">-->
<!--                <dt class="tit">自动封号条件：</dt>-->
<!--                <dd class="opt">-->
<!--                    <div>注册&nbsp;-->
<!--                        <input class="w30" id="honor_days" name="honor_days" value="--><?php //echo $output['s_info']['honor_days'] ? $output['s_info']['honor_days'] : 60; ?><!--" class="input-txt" type="text" />天内没有达到&nbsp;-->
<!--                        <input class="w30" type="text" name="honor_equity_num_limit" id="honor_equity_num_limit" value="--><?php //echo $output['s_info']['honor_equity_num_limit'] ? $output['s_info']['honor_equity_num_limit'] : 1; ?><!--" />个金券数量自动封号-->
<!--                    </div>-->
<!--                </dd>-->
<!--            </dl>-->
            <dl class="row">
                <dt class="tit">提现限制：</dt>
                <dd class="opt">
                    <input class="w100" id="withdraw_limit" name="withdraw_limit" value="<?php echo $output['member_system_setting'][0]['withdraw_limit'] ? $output['member_system_setting'][0]['withdraw_limit'] : 100; ?>" class="input-txt" type="text">&nbsp;整数倍
                    <p class="notic">提现须按此处设置的整数倍提现</p>
                </dd>
            </dl>
<hr>
            <dl class="row">
                <dt class="tit">原始股兑换限制：</dt>
                <dd class="opt">
                    <input class="w100" id="equity_start_buy_limit" name="equity_start_buy_limit" value="<?php echo $output['member_system_setting'][0]['equity_start_buy_limit'] ? $output['member_system_setting'][0]['equity_start_buy_limit'] : 5000; ?>" class="input-txt" type="text">起购，
                    <input class="w100" id="equity_add_buy_limit" name="equity_add_buy_limit" value="<?php echo $output['member_system_setting'][0]['equity_add_buy_limit'] ? $output['member_system_setting'][0]['equity_add_buy_limit'] : 100; ?>" class="input-txt" type="text">整倍数加购
                    <p class="notic">会员换购原始股按此设置积分数兑换</p>
                </dd>
            </dl>
<hr>
            <dl class="row">
                <dt class="tit">原始股兑换比例：</dt>
                <dd class="opt">
                    余额 : 股权 =
                    <input class="w100" id="equity_exchange_ratio" name="equity_exchange_ratio" value="<?php echo $output['member_system_setting'][0]['equity_exchange_ratio'] ? $output['member_system_setting'][0]['equity_exchange_ratio'] : "1:1"; ?>" class="input-txt" type="text">
                    <b>
<!--                        :</b>-->
<!--                    <input class="w100" id="equity_exchange_ratio" name="equity_exchange_ratio" value="--><?php //echo $output['member_system_setting'][0]['equity_exchange_ratio'] ? $output['member_system_setting'][0]['equity_exchange_ratio'] : 1; ?><!--" class="input-txt" type="text">-->
                    <p class="notic">会员换购原始股按此设置余额股权比例兑换</p>
                </dd>
            </dl>
<!--            <b>原始股兑换限制</b>-->
<!--            <dl class="row">-->
<!--                <dt class="tit">-->
<!--                    静态分红是否开启：-->
<!--                </dt>-->
<!--                <dd class="opt">-->
<!--                    <div class="onoff">-->
<!--                        <label for="dividend_status1" class="cb-enable --><?php //if ($output['s_info']['dividend_status'] == '1') {?><!--selected--><?php //}?><!--">-->
<!--                            开启-->
<!--                        </label>-->
<!--                        <label for="dividend_status0" class="cb-disable --><?php //if ($output['s_info']['dividend_status'] == '0') {?><!--selected--><?php //}?><!--">-->
<!--                            关闭-->
<!--                        </label>-->
<!--                        <input id="dividend_status1" name="dividend_status" --><?php //if ($output['s_info']['dividend_status'] == '1') {?><!--checked="checked"-->
<!--                        --><?php //}?><!-- value="1" type="radio">-->
<!--                        <input id="dividend_status0" name="dividend_status" --><?php //if ($output['s_info']['dividend_status'] == '0') {?><!--checked="checked"-->
<!--                        --><?php //}?><!-- value="0" type="radio">-->
<!--                    </div>-->
<!--                    <p class="notic">-->
<!--                        --><?php //echo $lang['site_state_notice']; ?>
<!--                    </p>-->
<!--                </dd>-->
<!--            </dl>-->
<!--            <dl class="row">-->
<!--                <dt class="tit">计划任务开启时间：</dt>-->
<!--                <dd class="opt">-->
<!--                    <input id="task_start_time" name="task_start_time" type="text" class="text w130" value="--><?php //echo $output['s_info']['task_start_time'] ? $output['s_info']['task_start_time'] : '23:00'; ?><!--" />-->
<!--                    <em class="add-on">-->
<!--                        <i class="icon-calendar"></i>-->
<!--                    </em>-->
<!--                    <span></span>-->
<!--                </dd>-->
<!--            </dl>-->
            <div class="bot"><a href="JavaScript:void(0);" class="ncap-btn-big ncap-btn-green" onclick="document.form1.submit()">确认提交</a></div>
        </div>
    </form>
</div>