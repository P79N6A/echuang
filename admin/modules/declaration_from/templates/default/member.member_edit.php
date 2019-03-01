<?php defined('In33hao') or exit('Access Invild!');?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <div class="subject">
                <h3>会员编辑</h3>
                <h5>会员系统会员信息编辑</h5>
            </div>
        </div>
    </div>
    <form id="user_form" enctype="multipart/form-data" method="post">
        <input type="hidden" name="form_submit" value="ok" />
        <input type="hidden" name="member_id" value="<?php echo $output['info']['member_id']; ?>">
        <div class="ncap-form-default">
            <dl class="row">
                <dt class="tit">
                    <label for="member_mobile">会员手机号</label>
                </dt>
                <dd class="opt">
                    <label><?php echo $output['info']['member_mobile']; ?></label>
                    <!-- <input type="text" value="<?php echo $output['info']['member_mobile']; ?>" name="member_mobile" id="member_mobile" class="input-txt">
                    <span class="err"></span>
                    <p class="notic"></p> -->
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="member_name">会员姓名</label>
                </dt>
                <dd class="opt">
                    <input type="text" id="member_name" name="member_name" class="input-txt" value="<?php echo $output['info']['member_name']; ?>">
                    <span class="err"></span>
                    <p class="notic"></p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>推荐人</label>
                </dt>
                <dd class="opt">
                	<label><?php echo $output['mobilearr'][$output['info']['invite_id']]; ?></label>
                </dd>
            </dl>
<!--            <dl class="row">-->
<!--                <dt class="tit">-->
<!--                    <label>接点人</label>-->
<!--                </dt>-->
<!--                <dd class="opt">-->
<!--                	<label>--><?php //echo $output['mobilearr'][$output['info']['access_id']]; ?><!--</label>-->
<!--                </dd>-->
<!--            </dl>-->
            <dl class="row">
                <dt class="tit">会员等级</dt>
                <dd class="opt">
                   <select class="input-txt" name="member_level" id="member_level">
                        <option id="experience" value="0">体验用户</option>
                        <option id="vip" value="1">VIP</option>
                        <option id="shopkeeper" value="2">店主</option>
                        <option id="partner" value="3">合伙人</option>
                        <option id="senior_partner" value="4">高级合伙人</option>
                        <option id="strategic_partner" value="5">战略合伙人</option>
                    </select>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="member_password">登录密码</label>
                </dt>
                <dd class="opt">
                    <input type="password" id="member_password" name="member_password" class="input-txt" value="" />
                    <span class="err"></span>
                    <p class="notic">不填写默认不修改</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="member_password_confirm">确认登录密码</label>
                </dt>
                <dd class="opt">
                    <input type="password" id="member_password_confirm" name="member_password_confirm" class="input-txt" value="" />
                    <span class="err"></span>
                    <p class="notic">不填写默认不修改</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="member_paypwd">支付密码</label>
                </dt>
                <dd class="opt">
                    <input type="password" id="member_paypwd" name="member_paypwd" class="input-txt" value="" />
                    <span class="err"></span>
                    <p class="notic">不填写默认不修改</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="member_paypwd_confirm">确认支付密码</label>
                </dt>
                <dd class="opt">
                    <input type="password" id="member_paypwd_confirm" name="member_paypwd_confirm" class="input-txt" value="" />
                    <span class="err"></span>
                    <p class="notic">不填写默认不修改</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>状态</label>
                </dt>
                <dd class="opt">
                	<label>
                        <input type="radio" <?php echo ($output['info']['member_state'] == 1) ? 'checked' : ''; ?> value="1" name="member_state">
                       	正常
                    </label>
                    <label style="margin-left: 30px;">
                        <input type="radio" <?php echo ($output['info']['member_state'] == 0) ? 'checked' : ''; ?> value="0" name="member_state">
                        冻结
                    </label>
                    <span class="err"></span>
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
            member_name: {
                required: true,
                chinese_name: true
            },
            member_password: {
                minlength: 6,
                maxlength: 20
            },
            member_password_confirm: {
                equalTo: '#member_password'
            },
            member_paypwd: {
                digits:true,
                minlength: 6,
                maxlength: 6
            },
            member_paypwd_confirm: {
                equalTo: '#member_paypwd'
            },
        },
        messages: {
            member_name: {
                required: '<i class="icon-exclamation-sign"></i>会员姓名不能为空',
                chinese_name: '<i class="icon-exclamation-sign"></i>会员姓名只限于中文'
            },
            member_password: {
                minlength: '<i class="icon-exclamation-sign"></i>密码长度应在6-20个字符之间',
                maxlength: '<i class="icon-exclamation-sign"></i>密码长度应在6-20个字符之间'
            },
            member_password_confirm: {
                equalTo: '<i class="icon-exclamation-sign"></i>两次输入的密码不一致'
            },
            member_paypwd: {
                digits:'<i class="icon-exclamation-sign"></i>支付密码必须是整数',
                minlength: '<i class="icon-exclamation-sign"></i>支付密码长度应是6位',
                maxlength: '<i class="icon-exclamation-sign"></i>支付密码长度应是6位'
            },
            member_paypwd_confirm: {
                equalTo: '<i class="icon-exclamation-sign"></i>两次输入的支付密码不一致'
            },
        }
    });
});
</script>