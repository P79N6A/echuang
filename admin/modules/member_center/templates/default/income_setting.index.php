<?php defined('In33hao') or exit('Access Invalid!');?>

<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <div class="subject">
                <h3>高级设置</h3>
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
            <!--            <b style="font-size: small;">关系</b>-->


            <dl class="row">
                <dt class="tit">
                    <label for="f_estimate_integral">预期收益：</label>
                </dt>
                <dd class="opt">
                    <input class="w65" id="f_estimate_integral" name="f_estimate_integral" value="<?php echo $output['s_info']['f_estimate_integral'] ? $output['s_info']['f_estimate_integral'] : 65600; ?>" class="input-txt" type="text">　积分
                    <p class="notic">此设置数量为用户报单后将获得的预期收益积分。</p>
                </dd>
            </dl>

<!--            <dl class="row">-->
<!--                <dt class="tit">-->
<!--                    <label for="f_declaration_time">报单收益：</label>-->
<!--                </dt>-->
<!--                <dd class="opt">-->
<!--                    <input class="w65" id="f_declaration_time" name="f_declaration_time" value="--><?php //echo $output['s_info']['f_declaration_time'] ? $output['s_info']['f_declaration_time'] : 48; ?><!--" class="input-txt" type="text">　积分-->
<!--                    <p class="notic">此设置数量为用户报单后， 将会会预期收益积分中释放的收益积分。</p>-->
<!--                </dd>-->
<!--            </dl>-->

            <dl class="row">
                <dt class="tit">
                    <label for="f_reward_integral">直推收益：</label>
                </dt>
                <dd class="opt">
                    <input class="w65" id="f_reward_integral" name="f_reward_integral" value="<?php echo $output['s_info']['f_reward_integral'] ? $output['s_info']['f_reward_integral'] : 20; ?>" class="input-txt" type="text">　积分
                    <p class="notic">此设置数量为用户报单后， 上面直推7级每级可从预期收益积分中释放的直推收益积分。</p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label for="f_deduction_integral">本人考核：</label>
                </dt>
                <dd class="opt">
                    <input class="w65" id="f_deduction_integral" name="f_deduction_integral" value="<?php echo $output['s_info']['f_deduction_integral'] ? $output['s_info']['f_deduction_integral'] : 1000; ?>" class="input-txt" type="text">　积分
                    <p class="notic">此设置数量为会员激活第二天开始6天内未直推满3个人，从第7天开始每天需扣除的积分数。</p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label for="f_declaration_time">直推考核：</label>
                </dt>
                <dd class="opt">
                    <input class="w65" id="f_declaration_time" name="f_declaration_time" value="<?php echo $output['s_info']['f_declaration_time'] ? $output['s_info']['f_declaration_time'] : 48; ?>" class="input-txt" type="text">　积分
                    <p class="notic">此设置数量为会员激活第二天开始6天内未直推满3个人，从第7天开始上面直推7级每天需扣除的积分数。</p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label for="f_service_standard">会员挂卖最低标准：</label>
                </dt>
                <dd class="opt">
                    <input class="w65" id="f_service_standard" name="f_service_standard" value="<?php echo $output['s_info']['f_service_standard'] ? $output['s_info']['f_service_standard'] : 100; ?>" class="input-txt" type="text">　分
                    <p class="notic">会员挂卖积分的最低标准，如最低挂卖100积分，每次只能按100的整数挂卖。</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="f_service_charge">会员挂卖手续费：</label>
                </dt>
                <dd class="opt">
                    <input class="w65" id="f_service_charge" name="f_service_charge" value="<?php echo $output['s_info']['f_service_charge'] ? $output['s_info']['f_service_charge'] : 20; ?>" class="input-txt" type="text">　%
                    <p class="notic">会员挂卖积分据需燃烧的积分数，如挂卖100积分，扣除20积分后，在市场上挂卖只剩余80积分。</p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label for="f_vip_service_standard">创客VIP最低标准：</label>
                </dt>
                <dd class="opt">
                    <input class="w65" id="f_vip_service_standard" name="f_vip_service_standard" value="<?php echo $output['s_info']['f_vip_service_standard'] ? $output['s_info']['f_vip_service_standard'] : 1000; ?>" class="input-txt" type="text">　分
                    <p class="notic">创客VIP挂卖积分的最低标准，如最低挂卖1000积分，每次只能按1000的整数挂卖。</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="f_vip_service_charge">创客VIP挂卖手续费：</label>
                </dt>
                <dd class="opt">
                    <input class="w65" id="f_vip_service_charge" name="f_vip_service_charge" value="<?php echo $output['s_info']['f_vip_service_charge'] ? $output['s_info']['f_vip_service_charge'] : 10; ?>" class="input-txt" type="text">　%
                    <p class="notic">创客VIP挂卖积分据需燃烧的积分数，如挂卖100积分，扣除10积分后，在市场上挂卖只剩余90积分。</p>
                </dd>
            </dl>

<!--            <dl class="row">-->
<!--                <dt class="tit">-->
<!--                    <label for="f_declaration_time">报单时间：</label>-->
<!--                </dt>-->
<!--                <dd class="opt">-->
<!--                    <input class="w65" id="f_declaration_time" name="f_declaration_time" value="--><?php //echo $output['s_info']['f_declaration_time'] ? $output['s_info']['f_declaration_time'] : 48; ?><!--" class="input-txt" type="text">　天-->
<!--                    <p class="notic">报单开始到结束的时间限制</p>-->
<!--                </dd>-->
<!--            </dl>-->
<!---->
<!--            <dl class="row">-->
<!--                <dt class="tit">-->
<!--                    <label for="f_recommend_num">直推限制：</label>-->
<!--                </dt>-->
<!--                <dd class="opt">-->
<!--                    <input class="w65" id="f_recommend_num" name="f_recommend_num" value="--><?php //echo $output['s_info']['f_recommend_num'] ? $output['s_info']['f_recommend_num'] : 3; ?><!--" class="input-txt" type="text">　人-->
<!--                    <p class="notic">直推人数限制</p>-->
<!--                </dd>-->
<!--            </dl>-->
<!---->
<!--            <dl class="row">-->
<!--                <dt class="tit">-->
<!--                    <label for="f_reward_integral">直推奖励：</label>-->
<!--                </dt>-->
<!--                <dd class="opt">-->
<!--                    <input class="w65" id="f_reward_integral" name="f_reward_integral" value="--><?php //echo $output['s_info']['f_reward_integral'] ? $output['s_info']['f_reward_integral'] : 20; ?><!--" class="input-txt" type="text">　积分-->
<!--                    <p class="notic">直推一人，本人和7个上级可从预期收益积分向会员积分转换的积分</p>-->
<!--                </dd>-->
<!--            </dl>-->
<!---->
<!--            <dl class="row">-->
<!--                <dt class="tit">绩效考核：</dt>-->
<!--                <dd class="opt">-->
<!--                    <input class="w100" id="f_cycle_time" name="f_cycle_time" value="--><?php //echo $output['s_info']['f_cycle_time'] ? $output['s_info']['f_cycle_time'] : 6; ?><!--" class="input-txt" type="text">天，至少直推-->
<!--                    <input class="w100" id="f_cycle_num" name="f_cycle_num" value="--><?php //echo $output['s_info']['f_cycle_num'] ? $output['s_info']['f_cycle_num'] : 3; ?><!--" class="input-txt" type="text">人-->
<!--                    <p class="notic">绩效考核标准</p>-->
<!--                </dd>-->
<!--            </dl>-->
<!---->
<!--            <dl class="row">-->
<!--                <dt class="tit">绩效惩罚：</dt>-->
<!--                <dd class="opt">-->
<!--                    本人扣除<input class="w100" id="f_deduction_integral" name="f_deduction_integral" value="--><?php //echo $output['s_info']['f_deduction_integral'] ? $output['s_info']['f_deduction_integral'] : 1000; ?><!--" class="input-txt" type="text">分，-->
<!--                    上级扣除积分<input class="w100" id="f_superior_deduction_integral" name="f_superior_deduction_integral" value="--><?php //echo $output['s_info']['f_superior_deduction_integral'] ? $output['s_info']['f_superior_deduction_integral'] : 500; ?><!--" class="input-txt" type="text">分-->
<!--                    <p class="notic">绩效未达标每天扣除积分</p>-->
<!--                </dd>-->
<!--            </dl>-->



            <div class="bot"><a href="JavaScript:void(0);" class="ncap-btn-big ncap-btn-green" onclick="document.form1.submit()">确认提交</a></div>
        </div>
    </form>
</div>