<?php defined('In33hao') or exit('Access Invalid!');?>
<div class="tabmenu">
    <?php include template('layout/submenu');?>
</div>
<div class="ncsc-form-default">
    <form id="add_form" action="" method="post" enctype="multipart/form-data">
        <input type="hidden" name="form_submit" value="ok" />
        <input type="hidden" name="member_access" value="<?php echo $output['member_access'] ?>" />
        <div style="margin-top: 20px;">
            <dl>
                <dt><i class="required"></i>会员名：</dt>
                <dd>
                    <input class="w400 text" name="member_name" type="text" id="member_name" value="" maxlength="30" placeholder="请填写会员名" />
                    <span></span>
                </dd>
            </dl>
            <dl>
                <dt><i class="required"></i>手机号：</dt>
                <dd>
                    <input class="w400 text" name="member_mobile" type="text" id="member_mobile" value="" maxlength="30" placeholder="请填写手机号" />
                    <span></span>
                </dd>
            </dl>
            <dl>
                <dt><i class="required"></i>信息来源：</dt>
                <dd>
                    <input class="w400 text" name="member_invite" type="text" id="member_invite" value="" maxlength="30" placeholder="请填写手机号" />
                    <span></span>
                </dd>
            </dl>
            <dl>
                <dt><i class="required"></i>共享资源：</dt>
                <dd>
                    <label>
                        <?php echo $output['a_mobile']; ?>
                    </label>
                </dd>
            </dl>
            <dl>
                <dt><i class="required"></i>设置密码：</dt>
                <dd>
                    <input class="w400 text" name="member_pwd" type="text" id="member_pwd" value="000000" maxlength="30" placeholder="请填写密码" />
                    <span></span>
                    <p class="notic">默认密码为000000</p>

                </dd>
            </dl>
            <dl>
                <dt><i class="required"></i>确认密码：</dt>
                <dd>
                    <input class="w400 text" name="member_pwd_confirm" type="text" id="member_pwd_confirm" value="000000" maxlength="30" placeholder="请填写确认密码" />
                    <span></span>
                    <p class="notic">默认确认密码为000000</p>
                    <input type="hidden" name="area" value="<?php echo $output['area']?>">
                </dd>
            </dl>
            <div class="bottom">
                <label class="submit-border">
                    <input type="submit" class="submit" value="确认注册">
                </label>
            </div>
        </div>
    </form>
</div>
<script type="text/javascript">
$(function() {
    jQuery.validator.addMethod("chinese_name", function(value, element) {
        return this.optional(element) || (/^[\u2E80-\u9FFF]/.test(value))
    }, "Chinese only please");

    jQuery.validator.addMethod("mobile_avail", function(value, element) {
        return this.optional(element) || (/^[1][3,4,5,7,8][0-9]{9}$/.test(value))
    }, "mobile is invild");

    // 注册表单验证
    $("#add_form").validate({
        errorPlacement: function(error, element) {
            var error_td = element.parent('dd');
            error_td.append(error);
            element.parents('dl:first').addClass('error');
        },
        success: function(label) {
            label.parents('dl:first').removeClass('error').find('label').remove();
        },
        // submitHandler: function(form) {
        //     if (_register_member) return false;
        //     _register_member = 1;
        //     ajaxpost('add_form', '', '', 'onerror');
        // },
        onkeyup: false,
        rules: {
            member_name: {
                required: true,
                chinese_name: true
            },
            member_mobile: {
                required: true,
                minlength: 11,
                remote: {
                    url: 'index.php?act=login&op=ajax_check_member_mobile',
                    type: 'get',
                    data: {
                        member_mobile: function() {
                            return $('#member_mobile').val();
                        }
                    }
                }
            },
            member_pwd: {
                required: true,
                minlength: 6,
                maxlength: 20
            },
            member_pwd_confirm: {
                required: true,
                equalTo: '#member_pwd'
            },
            member_invite: {
                required: true,
                minlength: 11,
                remote: {
                    url: 'index.php?act=login&op=ajax_check_member_invite',
                    type: 'get',
                    data: {
                        member_invite: function() {
                            return $('#member_invite').val();
                        }
                    }
                }
            },
        },
        messages: {
            member_name: {
                required: '<i class="icon-exclamation-sign"></i>用户名不能为空',
                chinese_name: '<i class="icon-exclamation-sign"></i>用户名只能是中文',
            },
            member_mobile: {
                required: '<i class="icon-exclamation-sign"></i>手机号不能为空',
                minlength: '<i class="icon-exclamation-sign"></i>手机号格式不正确',
                remote: '<i class="icon-exclamation-sign"></i>该手机号已注册'
            },
            member_invite: {
                required: '<i class="icon-exclamation-sign"></i>邀请人不能为空',
                minlength: '<i class="icon-exclamation-sign"></i>邀请人手机号格式不正确',
                remote: '<i class="icon-exclamation-sign"></i>邀请人未注册'
            },
            member_pwd: {
                required: '<i class="icon-exclamation-sign"></i>密码不能为空',
                minlength: '<i class="icon-exclamation-sign"></i>密码长度应在6-20个字符之间',
                maxlength: '<i class="icon-exclamation-sign"></i>密码长度应在6-20个字符之间'
            },
            member_pwd_confirm: {
                required: '<i class="icon-exclamation-sign"></i>请再次输入密码',
                equalTo: '<i class="icon-exclamation-sign"></i>两次输入的密码不一致'
            },
        }
    });
});
</script>