<?php defined('In33hao') or exit('Access Invalid!');?>
<div class="nc-register-bg">
    <div class="nc-register-box">
        <div class="nc-register-layout">
            <div class="left">
                <div class="nc-register-mode">
                    <ul class="tabs-nav">
                        <li><a href="">账号注册</a></li>
                    </ul>
                    <div id="tabs_container" class="tabs-container">
                        <div id="default" class="tabs-content">
                            <form id="register_form" method="post" class="nc-login-form" action="<?php echo urlMemberSystem('login', 'member_save'); ?>">
                                <?php Security::getToken();?>
                                <input type="hidden" name="ref_url" value="<?php echo $output['ref_url'] ?>">
                                <input type="hidden" name="nchash" value="<?php echo getNchash(); ?>" />
                                <input type="hidden" name="form_submit" value="ok" />
                                <dl>
                                    <dt>姓名：</dt>
                                    <dd>
                                        <input type="text" id="member_name" name="member_name" class="text" tipMsg="请填写用户姓名" />
                                    </dd>
                                </dl>
                                <dl>
                                    <dt>手机号：</dt>
                                    <dd>
                                        <input type="text" id="member_mobile" name="member_mobile" class="text" tipMsg="请填写手机号" />
                                    </dd>
                                </dl>
                                <div class="code-div mt15" style="margin-top: 0;">
                                    <dl>
                                        <dt>验证码：</dt>
                                        <dd>
                                            <input type="text" name="sms_captcha" tipMsg="请输入短信验证码" class="text w120" id="sms_captcha" size="10" />
                                        </dd>
                                    </dl>
                                    <span><a href="javascript:send_sms(1);" style="display: block;" id="send">发送验证码</a></span>
                                </div>
                                <dl>
                                    <dt>设置密码：</dt>
                                    <dd>
                                        <input type="password" id="password" name="password" class="text" tipMsg="<?php echo $lang['login_register_password_to_login']; ?>" />
                                    </dd>
                                </dl>
                                <dl>
                                    <dt>确认密码：</dt>
                                    <dd>
                                        <input type="password" id="password_confirm" name="password_confirm" class="text" tipMsg="<?php echo $lang['login_register_input_password_again']; ?>" />
                                    </dd>
                                </dl>
                                <dl>
                                    <dt>推荐人：</dt>
                                    <dd>
                                        <?php if (empty($output['invite'])): ?>
                                        <input type="text" id="member_invite" name="member_invite" class="text" tipMsg="请填写手机号" value="" />
                                        <?php else: ?>
                                        <label class="text">
                                            <?php echo $output['invite']; ?>
                                        </label>
                                        <?php endif?>
                                    </dd>
                                </dl>
 <!--<dl>-->
<!--                                    <dt>共享资源：</dt>-->
<!--                                    <dd>-->
<!--                                        <input type="text" id="member_access" name="member_access" class="text" tipMsg="请填写手机号">-->
<!--                                    </dd>-->
<!--                                </dl>-->
                                <?php if($output['condition']=='1'){?>
                                <div class="code-div mt15">
                                    <dl>
                                        <dt>
                                            <?php echo $lang['login_register_code']; ?>：</dt>
                                        <dd>
                                            <input type="text" id="captcha" name="captcha" class="text w80" size="10" tipMsg="<?php echo $lang['login_register_input_code']; ?>" />
                                        </dd>
                                    </dl>
                                    <span><img src="index.php?act=seccode&op=makecode&type=50,120&nchash=<?php echo getNchash(); ?>" name="codeimage" id="codeimage"/> <a class="makecode" href="javascript:void(0)" onclick="javascript:document.getElementById('codeimage').src='index.php?act=seccode&op=makecode&type=50,120&nchash=<?php echo getNchash(); ?>&t=' + Math.random();"><?php echo $lang['login_password_change_code']; ?></a></span>
                                </div>
                                <?php } ?>
                                <dl class="clause-div">
                                    <dd>
                                        <input name="agree" type="checkbox" class="checkbox" id="clause" value="1" checked="checked" />
                                        <?php echo $lang['login_register_agreed']; ?>
                                        <a href="<?php echo urlShop('document', 'index', array('code' => 'agreement')); ?>" target="_blank" class="agreement" title="<?php echo $lang['login_register_agreed']; ?>">
                                            <?php echo $lang['login_register_agreement']; ?>
                                        </a>
                                    </dd>
                                </dl>
                                <div class="submit-div">
                                    <input type="submit" id="Submit" value="<?php echo $lang['login_register_regist_now']; ?>" class="submit" />
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
$(function() {
    jQuery.validator.addMethod("letters_name", function(value, element) {
        return this.optional(element) || (/^[A-Za-z0-9\u4e00-\u9fa5_-]+$/i.test(value) && !/^\d+$/.test(value));
    }, "Letters only please");

    jQuery.validator.addMethod("chinese_name", function(value, element) {
        return this.optional(element) || (/^[\u2E80-\u9FFF]/.test(value))
    }, "Chinese only please");

    // 初始化Input的灰色提示信息
    $('input[tipMsg]').inputTipText({ pwd: 'password,password_confirm' });
    // 注册方式切换
    $('.nc-register-mode').tabulous({
        // 动画缩放渐变效果effect: 'scale'
        effect: 'slideLeft' //动画左侧滑入效果
        // 动画下方滑入效果 effect: 'scaleUp'
        // 动画反转效果 effect: 'flip'
    });
    var div_form = '#default';
    $(".nc-register-mode .tabs-nav li a").click(function() {
        if ($(this).attr("href") !== div_form) {
            div_form = $(this).attr('href');
            $("" + div_form).find(".makecode").trigger("click");
        }
    });
    var _register_member = 0;
    // 注册表单验证
    $("#register_form").validate({
        errorPlacement: function(error, element) {
            var error_td = element.parent('dd');
            error_td.append(error);
            element.parents('dl:first').addClass('error');
        },
        success: function(label) {
            label.parents('dl:first').removeClass('error').find('label').remove();
        },
        submitHandler: function(form) {
            if (_register_member) return false;
            _register_member = 1;
            ajaxpost('register_form', '', '', 'onerror');
        },
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
            password: {
                required: true,
                minlength: 6,
                maxlength: 20
            },
            password_confirm: {
                required: true,
                equalTo: '#password'
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
            member_access: {
                required: true,
                minlength: 11,
                remote: {
                    url: 'index.php?act=login&op=ajax_check_member_access',
                    type: 'get',
                    data: {
                        member_access: function() {
                            return $('#member_access').val();
                        }
                    }
                }
            },

   //         captcha: {
   //             required: true,
   //             remote: {
   ////                 url: 'index.php?act=seccode&op=check&nchash=<?php echo getNchash(); ?>',
   //                 type: 'get',
   //                 data: {
   //                     captcha: function() {
   //                         return $('#captcha').val();
   //                     }
   //                 },
   //                 complete: function(data) {
   //                     if (data.responseText == 'false') {
   //                         document.getElementById('codeimage').src = 'index.php?act=seccode&op=makecode&type=50,120&nchash=<?php echo getNchash(); ?>&t=' + Math.random();
   //                     }
   //                 }
   //             }
   //         },

            agree: {
                required: true
            }
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
            password: {
                required: '<i class="icon-exclamation-sign"></i>密码不能为空',
                minlength: '<i class="icon-exclamation-sign"></i>密码长度应在6-20个字符之间',
                maxlength: '<i class="icon-exclamation-sign"></i>密码长度应在6-20个字符之间'
            },
            password_confirm: {
                required: '<i class="icon-exclamation-sign"></i>请再次输入密码',
                equalTo: '<i class="icon-exclamation-sign"></i>两次输入的密码不一致'
            },
                    // member_access: {
            //     required: '<i class="icon-exclamation-sign"></i>接点人不能为空',
            //     minlength: '<i class="icon-exclamation-sign"></i>接点人手机号格式不正确',
            //     remote: '<i class="icon-exclamation-sign"></i>接点人未注册或接点人错误'
            // },

           // captcha: {
           //     required: '<i class="icon-remove-circle" title="请输入验证码"></i>',
           //     remote: '<i class="icon-remove-circle" title="验证码不正确"></i>'
          //  },
            agree: {
                required: '<i class="icon-exclamation-sign"></i>请勾选服务协议'
            },
        }
    });
});

function send_sms(type) {
    var mobile = $('#member_mobile').val();

    if (mobile == '') {
        alert('请输入手机号');
        return;
    }
    if (!/^1\d{10}$/.test(mobile)) {
        alert('手机号格式错误');
        return;
    }
    $.ajax({
        type: 'GET',
        url: 'index.php?act=common&op=get_sms_captcha&mobile=' + mobile + '&sms_type=' + type,
        async: false,
        dataType: 'json',
        success: function(result) {
            if (result.state == "false") {
                alert(result.msg);
                return;
            } else {
                var second = 60;
                var send_obj = document.getElementById('send');
                var interval = setInterval(function() {
                    second -= 1;
                    if (second == 0) {
                        send_obj.href = 'javascript:send_sms(1);';
                        $('#send').html('发送验证码');
                        clearInterval(interval);
                    } else {
                        send_obj.href = 'javascript:void(0);'
                        $('#send').html(second + 's后重新发送');
                    }
                }, 1000);
            }
        }
    });
}
</script>