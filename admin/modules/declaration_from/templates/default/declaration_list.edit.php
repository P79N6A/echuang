<?php defined('In33hao') or exit('Access Invild!');?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <div class="subject">
                <h3>报单会员编辑</h3>
                <h5>报单系统报单信息编辑</h5>
            </div>
        </div>
    </div>
    <form id="user_form" enctype="multipart/form-data" method="post">
        <input type="hidden" name="form_submit" value="ok" />
        <input type="hidden" name="id" value="<?php echo $output['info']['id']; ?>">
        <div class="ncap-form-default">
            <dl class="row">
                <dt class="tit">
                    <label for="member_mobile">单号</label>
                </dt>
                <dd class="opt">
                    <label><?php echo $output['info']['order_sn']; ?></label>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="member_mobile">会员ID</label>
                </dt>
                <dd class="opt">
                    <label><?php echo $output['info']['member_id']; ?></label>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="declaration_time">报单时间</label>
                </dt>
                <dd class="opt">
                    <input type="text" id="declaration_time" name="declaration_time" class="input-txt" value="<?php echo $output['info']['declaration_time']; ?>">天
                    <span class="err"></span>
                    <p class="notic"></p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="cycle_time">绩效考核时间</label>
                </dt>
                <dd class="opt">
                    <input type="text" id="cycle_time" name="cycle_time" class="input-txt" value="<?php echo $output['info']['cycle_time']; ?>">天
                    <span class="err"></span>
                    <p class="notic"></p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="estimate_integral">预期收益积分</label>
                </dt>
                <dd class="opt">
                    <input type="text" id="estimate_integral" name="estimate_integral" class="input-txt" value="<?php echo $output['info']['estimate_integral']; ?>">分
                    <span class="err"></span>
                    <p class="notic"></p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="deduction_integral">绩效扣除本人分数</label>
                </dt>
                <dd class="opt">
                    <input type="text" id="deduction_integral" name="deduction_integral" class="input-txt" value="<?php echo $output['info']['deduction_integral']; ?>">分
                    <span class="err"></span>
                    <p class="notic"></p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="superior_deduction_integral">绩效扣除上级分数</label>
                </dt>
                <dd class="opt">
                    <input type="text" id="superior_deduction_integral" name="superior_deduction_integral" class="input-txt" value="<?php echo $output['info']['superior_deduction_integral']; ?>">分
                    <span class="err"></span>
                    <p class="notic"></p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="inviter_id">用户填写的推荐人ID</label>
                </dt>
                <dd class="opt">
                    <input type="text" id="inviter_id" name="inviter_id" class="input-txt" value="<?php echo $output['info']['inviter_id']; ?>">
                    <span class="err"></span>
                    <p class="notic"></p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="invite_one">实际绑定的推荐人ID</label>
                </dt>
                <dd class="opt">
                    <input type="text" id="invite_one" name="invite_one" class="input-txt" value="<?php echo $output['info']['invite_one']; ?>">
                    <span class="err"></span>
                    <p class="notic"></p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="invite_two">上二级的推荐人ID</label>
                </dt>
                <dd class="opt">
                    <input type="text" id="invite_two" name="invite_two" class="input-txt" value="<?php echo $output['info']['invite_two']; ?>">
                    <span class="err"></span>
                    <p class="notic"></p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="invite_three">上三级的推荐人ID</label>
                </dt>
                <dd class="opt">
                    <input type="text" id="invite_three" name="invite_three" class="input-txt" value="<?php echo $output['info']['invite_three']; ?>">
                    <span class="err"></span>
                    <p class="notic"></p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="invite_four">上四级的推荐人ID</label>
                </dt>
                <dd class="opt">
                    <input type="text" id="invite_four" name="invite_four" class="input-txt" value="<?php echo $output['info']['invite_four']; ?>">
                    <span class="err"></span>
                    <p class="notic"></p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="invite_five">上五级的推荐人ID</label>
                </dt>
                <dd class="opt">
                    <input type="text" id="invite_five" name="invite_five" class="input-txt" value="<?php echo $output['info']['invite_five']; ?>">
                    <span class="err"></span>
                    <p class="notic"></p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="invite_six">上六级的推荐人ID</label>
                </dt>
                <dd class="opt">
                    <input type="text" id="invite_six" name="invite_six" class="input-txt" value="<?php echo $output['info']['invite_six']; ?>">
                    <span class="err"></span>
                    <p class="notic"></p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="invite_seven">上七级的推荐人ID</label>
                </dt>
                <dd class="opt">
                    <input type="text" id="invite_seven" name="invite_seven" class="input-txt" value="<?php echo $output['info']['invite_seven']; ?>">
                    <span class="err"></span>
                    <p class="notic"></p>
                </dd>
            </dl>
            <dl class="row" style="display: none;">
                <dt class="tit">
                    <label>状态</label>
                </dt>
                <dd class="opt">
                    <label>
                        <input type="radio" <?php echo ($output['info']['state'] == 0) ? 'checked' : ''; ?> value="0" name="member_state">
                        未审核
                    </label>
                    <label style="margin-left: 30px;">
                        <input type="radio" <?php echo ($output['info']['state'] == 1) ? 'checked' : ''; ?> value="1" name="member_state">
                        已开始
                    </label>
                    <label style="margin-left: 30px;">
                        <input type="radio" <?php echo ($output['info']['state'] == 2) ? 'checked' : ''; ?> value="2" name="member_state">
                        已完成
                    </label>
                    <label style="margin-left: 30px;">
                        <input type="radio" <?php echo ($output['info']['state'] == 3) ? 'checked' : ''; ?> value="3" name="member_state">
                        审核未通过
                    </label>
                    <span class="err"></span>
                    <p class="notic">报单如已完成将不能再修改状态</p>
                </dd>
            </dl>
            <div class="bot">
                <a href="JavaScript:void(0);" class="ncap-btn-big ncap-btn-green" id="submitBtn">
                    <?php echo $lang['nc_submit']; ?>
                </a>
            </div>
        </div>
    </form>
</div>
<script type="text/javascript">
    //获取会员等级
    $("#member_level option[value='<?php echo $output['info']['member_level']?>']").attr("selected", true);

    $("#submitBtn").click(function() {
        $('#user_form').submit();
    })
    $(function() {
        jQuery.validator.addMethod("chinese_name", function(value, element) {
            return this.optional(element) || (/^[\u2E80-\u9FFF]/.test(value))
        }, "Chinese only please");

        // 用户表单验证
        $("#user_form").validate({
            errorPlacement: function(error, element) {
                var error_dd = element.parent('dd:first');
                error_dd.children('span').append(error);
                element.parents('dd:first').addClass('error');
            },
            success: function(label) {
                label.parents('dd').removeClass('error').find('label').remove();
            },
            onkeyup: false,
            rules: {
            },
            messages: {
            }
        });
    });
</script>