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
    <!-- 操作说明 -->
    <div class="explanation" id="explanation">
        <div class="title" id="checkZoom"><i class="fa fa-lightbulb-o"></i>
            <h4 title="<?php echo $lang['nc_prompts_title']; ?>">收益分配</h4>
            <span id="explanationZoom" title="<?php echo $lang['nc_prompts_span']; ?>"></span> </div>
        <ul>
            <li>收益分配参数设置</li>
        </ul>
    </div>
    <form method="post" enctype="multipart/form-data" name="form1">
        <input type="hidden" name="form_submit" value="ok" />
        <div class="ncap-form-default system-setting">
            <dl class="row">
                <dt class="tit">
                    <label for="bean_equity_raito">金券与消费金豆比率：</label>
                </dt>
                <dd class="opt">
                    1&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;
                    <input class="w65" id="bean_equity_raito" name="bean_equity_raito" value="<?php echo $output['s_info']['bean_equity_raito'] ? $output['s_info']['bean_equity_raito'] : 1; ?>" class="input-txt" type="text">
                    <p class="notic">金豆消费股权时的比例</p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label for="static_income_rebate_percentage ">静态收益返利百分比：</label>
                </dt>
                <dd class="opt">
                    <input class="w100" id="static_income_rebate_percentage" name="static_income_rebate_percentage" value="<?php echo $output['s_info']['static_income_rebate_percentage'] ? $output['s_info']['static_income_rebate_percentage'] : 1; ?>" class="input-txt" type="text">&nbsp;%
                    <p class="notic">静态分红的百分比</p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label for="bean_max_consume_amount">金豆最大消费额：</label>
                </dt>
                <dd class="opt">
                    <input class="w100" id="bean_max_consume_amount" name="bean_max_consume_amount" value="<?php echo $output['s_info']['bean_max_consume_amount'] ? $output['s_info']['bean_max_consume_amount'] : 60000; ?>" class="input-txt" type="text">&nbsp;元
                    <p class="notic">金豆最大的累计消费额</p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label for="bean_max_consume_num">金豆最大可消费次数：</label>
                </dt>
                <dd class="opt">
                    <input class="w100" id="bean_max_consume_num" name="bean_max_consume_num" value="<?php echo $output['s_info']['bean_max_consume_num'] ? $output['s_info']['bean_max_consume_num'] : 2; ?>" class="input-txt" type="text">&nbsp;次
                    <p class="notic">金豆最大可消费次数</p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label for="service_bean_max_donate_num">服务中心金豆最大转赠次数：</label>
                </dt>
                <dd class="opt">
                    <input class="w100" id="service_bean_max_donate_num" name="service_bean_max_donate_num" value="<?php echo $output['s_info']['service_bean_max_donate_num'] ? $output['s_info']['service_bean_max_donate_num'] : 0; ?>" class="input-txt" type="text">&nbsp;次
                    <p class="notic">服务中心金豆最大转赠次数(0表示不限制)</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="one_rebate_percentage">1级推广奖比率：</label>
                </dt>
                <dd class="opt">
                    <input class="w100" id="one_rebate_percentage" name="one_rebate_percentage" value="<?php echo $output['s_info']['one_rebate_percentage'] ? $output['s_info']['one_rebate_percentage'] : 3; ?>" class="input-txt" type="text">&nbsp;%
                    <p class="notic">推荐1级会员消费获得的推广奖比率</p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label for="two_rebate_percentage">2级推广奖比率：</label>
                </dt>
                <dd class="opt">
                    <input class="w100" id="two_rebate_percentage" name="two_rebate_percentage" value="<?php echo $output['s_info']['two_rebate_percentage'] ? $output['s_info']['two_rebate_percentage'] : 5; ?>" class="input-txt" type="text">&nbsp;%
                    <p class="notic">推荐2级会员消费获得的推广奖比率</p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label for="three_rebate_percentage">3级推广奖比率：</label>
                </dt>
                <dd class="opt">
                    <input class="w100" id="three_rebate_percentage" name="three_rebate_percentage" value="<?php echo $output['s_info']['three_rebate_percentage'] ? $output['s_info']['three_rebate_percentage'] : 8; ?>" class="input-txt" type="text">&nbsp;%
                    <p class="notic">推荐3级会员消费获得的推广奖比率</p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label for="collision_rebate_percentage">双轨碰撞拓展奖比率：</label>
                </dt>
                <dd class="opt">
                    <input class="w100" id="collision_rebate_percentage" name="collision_rebate_percentage" value="<?php echo $output['s_info']['collision_rebate_percentage'] ? $output['s_info']['collision_rebate_percentage'] : 12; ?>" class="input-txt" type="text" />&nbsp;%
                    <p class="notic">双轨碰撞拓展奖比率</p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label for="withdraw_charge_percentage">提现手续费：</label>
                </dt>
                <dd class="opt">
                    <input class="w100" id="withdraw_charge_percentage" name="withdraw_charge_percentage" value="<?php echo $output['s_info']['withdraw_charge_percentage'] ? $output['s_info']['withdraw_charge_percentage'] : 10; ?>" class="input-txt" type="text">&nbsp;%
                    <p class="notic">每笔提现扣除的手续费</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="withdraw_charge_limit">金豆提现限制：</label>
                </dt>
                <dd class="opt">
                    <input class="w100" id="withdraw_charge_limit" name="withdraw_charge_limit" value="<?php echo $output['s_info']['withdraw_charge_limit'] ? $output['s_info']['withdraw_charge_limit'] : 500; ?>" class="input-txt" type="text">&nbsp;元
                    <p class="notic">金豆余额须超过此处设置数量方可提现</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="equity_max_amount">会员最大拥有金券数：</label>
                </dt>
                <dd class="opt">
                    <input class="w100" id="equity_max_amount" name="equity_max_amount" value="<?php echo $output['s_info']['equity_max_amount'] ? $output['s_info']['equity_max_amount'] : 50000; ?>" class="input-txt" type="text">
                    <p class="notic">每个会员最多拥有的金券数量</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="equity_dividend_limit">分红金券限制设置：</label>
                </dt>
                <dd class="opt">
                    <div>金券分红最小限制值&nbsp;
                        <input class="w30" id="equity_dividend_min_limit" name="equity_dividend_min_limit" value="<?php echo $output['s_info']['equity_dividend_min_limit'] ? $output['s_info']['equity_dividend_min_limit'] : 3500; ?>" class="input-txt" type="text" />，金券分红最大限制值&nbsp;
                        <input class="w40" id="equity_dividend_max_limit" name="equity_dividend_max_limit" value="<?php echo $output['s_info']['equity_dividend_max_limit'] ? $output['s_info']['equity_dividend_max_limit'] : 35000; ?>" class="input-txt" type="text" />，当历史分红总额达到消费金豆数量的&nbsp;
                        <input class="w40" id="dividend_consume_multiple" name="dividend_consume_multiple" value="<?php echo $output['s_info']['dividend_consume_multiple'] ? $output['s_info']['dividend_consume_multiple'] : 10; ?>" class="input-txt" type="text" />倍，需要复投
                    </div>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="bean_recieve_limit">接收金豆限制：</label>
                </dt>
                <dd class="opt">
                    <div>金券限制值&nbsp;
                        <input class="w30" id="equity_limit" name="equity_limit" value="<?php echo $output['s_info']['equity_limit'] ? $output['s_info']['equity_limit'] : 3500; ?>" class="input-txt" type="text" />，少于金券限制值单笔只可接收金豆数&nbsp;
                        <input class="w30" type="text" name="bean_lt_recieve_limit" id="bean_lt_recieve_limit" value="<?php echo $output['s_info']['bean_lt_recieve_limit']; ?>" />，累计接收金豆数不能超过&nbsp;
                        <input class="w30" type="text" name="bean_lt_recieve_accumulate_limit" id="bean_lt_recieve_accumulate_limit" value="<?php echo $output['s_info']['bean_lt_recieve_accumulate_limit']; ?>">；
                        <br/>超过金券限制值单笔只可接收金豆数&nbsp;
                        <input class="w40" id="bean_egt_recieve_limit" name="bean_egt_recieve_limit" value="<?php echo $output['s_info']['bean_egt_recieve_limit']; ?>" class="input-txt" type="text" />，累计接收金豆数不能超过&nbsp;
                        <input class="w40" type="text" name="bean_egt_recieve_accumulate_limit" id="bean_egt_recieve_accumulate_limit" value="<?php echo $output['s_info']['bean_egt_recieve_accumulate_limit']; ?>">
                    </div>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">是否开启自动封号：</dt>
                <dd class="opt">
                    <div class="onoff">
                        <label for="honor_status1" class="cb-enable <?php if ($output['s_info']['honor_status'] == '1') {?>selected<?php }?>">
                            开启
                        </label>
                        <label for="honor_status0" class="cb-disable <?php if ($output['s_info']['honor_status'] == '0') {?>selected<?php }?>">
                            关闭
                        </label>
                        <input id="honor_status1" name="honor_status" <?php if ($output['s_info']['honor_status'] == '1') {?>checked="checked"
                        <?php }?> value="1" type="radio" />
                        <input id="honor_status0" name="honor_status" <?php if ($output['s_info']['honorstatus'] == '0') {?>checked="checked"
                        <?php }?> value="0" type="radio" />
                    </div>
                    <p class="notic"></p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">自动封号条件：</dt>
                <dd class="opt">
                    <div>注册&nbsp;
                        <input class="w30" id="honor_days" name="honor_days" value="<?php echo $output['s_info']['honor_days'] ? $output['s_info']['honor_days'] : 60; ?>" class="input-txt" type="text" />天内没有达到&nbsp;
                        <input class="w30" type="text" name="honor_equity_num_limit" id="honor_equity_num_limit" value="<?php echo $output['s_info']['honor_equity_num_limit'] ? $output['s_info']['honor_equity_num_limit'] : 1; ?>" />个金券数量自动封号
                    </div>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    静态分红是否开启：
                </dt>
                <dd class="opt">
                    <div class="onoff">
                        <label for="dividend_status1" class="cb-enable <?php if ($output['s_info']['dividend_status'] == '1') {?>selected<?php }?>">
                            开启
                        </label>
                        <label for="dividend_status0" class="cb-disable <?php if ($output['s_info']['dividend_status'] == '0') {?>selected<?php }?>">
                            关闭
                        </label>
                        <input id="dividend_status1" name="dividend_status" <?php if ($output['s_info']['dividend_status'] == '1') {?>checked="checked"
                        <?php }?> value="1" type="radio">
                        <input id="dividend_status0" name="dividend_status" <?php if ($output['s_info']['dividend_status'] == '0') {?>checked="checked"
                        <?php }?> value="0" type="radio">
                    </div>
                    <p class="notic">
                        <?php echo $lang['site_state_notice']; ?>
                    </p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">计划任务开启时间：</dt>
                <dd class="opt">
                    <input id="task_start_time" name="task_start_time" type="text" class="text w130" value="<?php echo $output['s_info']['task_start_time'] ? $output['s_info']['task_start_time'] : '23:00'; ?>" />
                    <em class="add-on">
                        <i class="icon-calendar"></i>
                    </em>
                    <span></span>
                </dd>
            </dl>
            <div class="bot"><a href="JavaScript:void(0);" class="ncap-btn-big ncap-btn-green" onclick="document.form1.submit()">确认提交</a></div>
        </div>
    </form>
</div>