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
<!--            <b style="font-size: small;">关系</b>-->
            <dl class="row">
                <dt class="tit">
                    <label for="f_declaration_time">报单时间：</label>
                </dt>
                <dd class="opt">
                    <input class="w65" id="f_declaration_time" name="f_declaration_time" value="<?php echo $output['s_info']['f_declaration_time'] ? $output['s_info']['f_declaration_time'] : 48; ?>" class="input-txt" type="text">　天
                    <p class="notic">报单开始到结束的时间限制</p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label for="f_recommend_num">直推限制：</label>
                </dt>
                <dd class="opt">
                    <input class="w65" id="f_recommend_num" name="f_recommend_num" value="<?php echo $output['s_info']['f_recommend_num'] ? $output['s_info']['f_recommend_num'] : 3; ?>" class="input-txt" type="text">　人
                    <p class="notic">直推人数限制</p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label for="f_reward_integral">直推奖励：</label>
                </dt>
                <dd class="opt">
                    <input class="w65" id="f_reward_integral" name="f_reward_integral" value="<?php echo $output['s_info']['f_reward_integral'] ? $output['s_info']['f_reward_integral'] : 20; ?>" class="input-txt" type="text">　积分
                    <p class="notic">直推一人，本人和7个上级可从预期收益积分向会员积分转换的积分</p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">绩效考核：</dt>
                <dd class="opt">
                    <input class="w100" id="f_cycle_time" name="f_cycle_time" value="<?php echo $output['s_info']['f_cycle_time'] ? $output['s_info']['f_cycle_time'] : 6; ?>" class="input-txt" type="text">天，至少直推
                    <input class="w100" id="f_cycle_num" name="f_cycle_num" value="<?php echo $output['s_info']['f_cycle_num'] ? $output['s_info']['f_cycle_num'] : 3; ?>" class="input-txt" type="text">人
                    <p class="notic">绩效考核标准</p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">绩效惩罚：</dt>
                <dd class="opt">
                    本人扣除<input class="w100" id="f_deduction_integral" name="f_deduction_integral" value="<?php echo $output['s_info']['f_deduction_integral'] ? $output['s_info']['f_deduction_integral'] : 1000; ?>" class="input-txt" type="text">分，
                    上级扣除积分<input class="w100" id="f_superior_deduction_integral" name="f_superior_deduction_integral" value="<?php echo $output['s_info']['f_superior_deduction_integral'] ? $output['s_info']['f_superior_deduction_integral'] : 500; ?>" class="input-txt" type="text">分
                    <p class="notic">绩效未达标每天扣除积分</p>
                </dd>
            </dl>



            <div class="bot"><a href="JavaScript:void(0);" class="ncap-btn-big ncap-btn-green" onclick="document.form1.submit()">确认提交</a></div>
        </div>
    </form>
</div>