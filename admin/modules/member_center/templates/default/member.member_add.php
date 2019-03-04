<?php defined('In33hao') or exit('Access Invild!');?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title"><a class="back" href="index.php?act=member&op=member_manage" title="返回会员列表"><i class="fa fa-arrow-circle-o-left"></i></a>
            <div class="subject">
                <h3>添加会员</h3>
                <h5>会员系统添加新会员</h5>
            </div>
        </div>
    </div>
    <form id="user_form" enctype="multipart/form-data" method="post">
        <input type="hidden" name="form_submit" value="ok" />
        <?php if ($output['member_access']): ?>
        <input type="hidden" name="member_access" value="<?php echo $output['member_access']; ?>" />
        <input type="hidden" name="access_id" value="<?php echo $output['access_id']; ?>" />
        <?php endif?>

        <div class="ncap-form-default">
            <dl class="row">
                <dt class="tit">
                    <label for="member_mobile">会员手机号</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="member_mobile" id="member_mobile" class="input-txt" value="" />
                    <span class="err"></span>
                    <p class="notic">填写手机号码</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="member_name">会员姓名</label>
                </dt>
                <dd class="opt">
                    <input type="text" id="member_name" name="member_name" class="input-txt" value="" />
                    <span class="err"></span>
                    <p class="notic">填写会员姓名,仅限于中文名</p>
                </dd>
            </dl>
<!--            <dl class="row">-->
<!--                <dt class="tit">-->
<!--                    <label for="member_invite">推荐人</label>-->
<!--                </dt>-->
<!--                <dd class="opt">-->
<!--                    <input type="text" id="member_invite" name="member_invite" class="input-txt" value="" />-->
<!--                    <span class="err"></span>-->
<!--                    <p class="notic">填写推荐人手机号码</p>-->
<!--                </dd>-->
<!--            </dl>-->
            <dl class="row">
                <dt class="tit">会员等级</dt>
                <dd class="opt">
                    <select class="input-txt" name="customer_level" id="customer_level">
                        <option id="ordinary_user" value="0">普通用户</option>
                        <option id="ordinary_member" value="1">普通会员</option>
                        <option id="vip" value="2">创客VIP</option>
                    </select>
                </dd>
            </dl>
<!--            <dl class="row">-->
<!--                <dt class="tit">-->
<!--                    <label for="member_access">接点人</label>-->
<!--                </dt>-->
<!--                <dd class="opt">-->
<!--                    --><?php //if ($output['member_access']): ?>
<!--                    <span>--><?php //echo $output['member_access'] ?><!--</span>-->
<!--                    --><?php //else: ?>
<!--                    <input type="text" id="member_access" name="member_access" class="input-txt" value="" />-->
<!--                    <span class="err"></span>-->
<!--                    <p class="notic">填写接点人手机号码</p>-->
<!--                    --><?php //endif?>
<!--                </dd>-->
<!--            </dl>-->
            <dl class="row">
                <dt class="tit">
                    <label for="member_password">登录密码</label>
                </dt>
                <dd class="opt">
                    <input type="text" id="member_password" name="member_password" class="input-txt" value="000000" />
                    <span class="err"></span>
                    <p class="notic">默认密码为000000</p>
                    <input type="hidden" value="<?php echo $output['area']?>" name="area">
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="member_password_confirm">确认登录密码</label>
                </dt>
                <dd class="opt">
                    <input type="text" id="member_password_confirm" name="member_password_confirm" class="input-txt" value="000000" />
                    <span class="err"></span>
                    <p class="notic">默认确认密码为000000</p>
                </dd>
            </dl>
            <?php if (!$output['member_access']): ?>
            <dl class="row">
                <dt class="tit">
                    <label for="member_state">会员状态</label>
                </dt>
                <dd class="opt">
                    <label>
                        <input type="radio" checked value="1" name="member_state"> 正常
                    </label>
                    <label style="margin-left: 30px;">
                        <input type="radio" value="0" name="member_state"> 冻结
                    </label>
                    <p class="notic">会员状态默认正常</p>
                </dd>
            </dl>
            <?php endif?>

            <div class="bot">
                <a href="JavaScript:void(0);" class="ncap-btn-big ncap-btn-green" id="submitBtn">
                    确认提交
                </a>
            </div>
        </div>
    </form>
</div>
<script type="text/javascript">
$("#submitBtn").click(function() {
    if ($('#user_form').valid()) {
        $('#user_form').submit();
    }
});

$(function() {
    jQuery.validator.addMethod("chinese_name", function(value, element) {
        return this.optional(element) || (/^[\u2E80-\u9FFF]/.test(value))
    }, "Chinese only please");

    jQuery.validator.addMethod("mobile_avail", function(value, element) {
        return this.optional(element) || (/^[1][3,4,5,7,8][0-9]{9}$/.test(value))
    }, "mobile is invild");

    // 注册表单验证
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
                chinese_name: true,
            },
            member_mobile: {
                required: true,
                mobile_avail: true,
                remote: {
                    url: 'index.php?act=member&op=ajax_check_member_mobile',
                    type: 'get',
                    data: {
                        member_mobile: function() {
                            return $('#member_mobile').val();
                        }
                    }
                }
            },
            member_password: {
                required: true,
                minlength: 6,
                maxlength: 20
            },
            member_password_confirm: {
                required: true,
                equalTo: '#member_password'
            },
            member_invite: {
                required: true,
                mobile_avail: true,
                remote: {
                    url: 'index.php?act=member&op=ajax_check_member_invite',
                    type: 'get',
                    data: {
                        member_invite: function() {
                            return $('#member_invite').val();
                        }
                    }
                }
            },
            <?php if (!$output['member_access']): ?>
            member_access: {
                required: true,
                mobile_avail: true,
                remote: {
                    url: 'index.php?act=member&op=ajax_check_member_access',
                    type: 'get',
                    data: {
                        member_access: function() {
                            return $('#member_access').val();
                        }
                    }
                }
            },
            <?php endif?>

        },
        messages: {
            member_name: {
                required: '<i class="icon-exclamation-sign"></i>会员姓名不能为空',
                chinese_name: '<i class="icon-exclamation-sign"></i>会员姓名只限于中文',
            },
            member_mobile: {
                required: '<i class="icon-exclamation-sign"></i>手机号不能为空',
                mobile_avail: '<i class="icon-exclamation-sign"></i>手机号格式不正确',
                remote: '<i class="icon-exclamation-sign"></i>该手机号已注册,请更换手机号'
            },
            member_invite: {
                required: '<i class="icon-exclamation-sign"></i>邀请人不能为空',
                mobile_avail: '<i class="icon-exclamation-sign"></i>邀请人手机号格式不正确',
                remote: '<i class="icon-exclamation-sign"></i>邀请人未注册'
            },
            <?php if (!$output['member_access']): ?>
            member_access: {
                required: '<i class="icon-exclamation-sign"></i>接点人不能为空',
                mobile_avail: '<i class="icon-exclamation-sign"></i>接点人手机号格式不正确',
                remote: '<i class="icon-exclamation-sign"></i>接点人未注册或此接点人已接满',
            },
            <?php endif?>
            member_password: {
                required: '<i class="icon-exclamation-sign"></i>密码不能为空',
                minlength: '<i class="icon-exclamation-sign"></i>密码长度应在6-20个字符之间',
                maxlength: '<i class="icon-exclamation-sign"></i>密码长度应在6-20个字符之间'
            },
            member_password_confirm: {
                required: '<i class="icon-exclamation-sign"></i>请再次输入密码',
                equalTo: '<i class="icon-exclamation-sign"></i>两次输入的密码不一致'
            },

        }
    });
});
</script>