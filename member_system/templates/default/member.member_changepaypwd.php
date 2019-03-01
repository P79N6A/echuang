<?php defined('In33hao') or exit('Access Invalid!');?>
<div class="tabmenu">
    <?php include template('layout/submenu');?>
</div>
<div class="ncsc-form-default">
    <form id="passwd_form" action="" method="post" enctype="multipart/form-data">
        <input type="hidden" name="form_submit" value="ok">
        <input type="hidden" id="mobile" value="<?php echo $output['info']['member_mobile']; ?>">
        <dl>
            <dt>身份验证方式：</dt>
            <dd>
                <p>
                    <select name="auth_type" id="auth_type">
                        <option value="mobile">手机 [
                            <?php echo encryptShow($output['info']['member_mobile'], 4, 4); ?>]</option>
                    </select>
                    <a href="javascript:void(0);" id="send_captcha" class="ncbtn ml5"><span id="sending" style="display:none">正在</span><span class="send_success_tips"><strong id="show_times" class="red mr5"></strong>秒后再次</span>获取手机验证码</a></p>
                <p class="send_success_tips hint mt10">“手机验证码”已发出，请注意查收，请在<strong>“30分种”</strong>内完成验证。</p>
            </dd>
        </dl>
        <dl>
            <dt>验证码：</dt>
            <dd>
                <input class="w400 text" name="captcha" type="text" id="captcha" value="" maxlength="30" placeholder="填写收到的验证码" />
                <span></span>
                <p></p>
            </dd>
        </dl>
        <dl>
            <dt>支付密码：</dt>
            <dd>
                <input class="w400 text" name="paypwd" type="password" id="paypwd" value="" maxlength="30" placeholder="填写新支付密码" />
                <span></span>
                <p>6-20位字符，可由英文、数字及标点符号组成。</p>
            </dd>
        </dl>
        <dl>
            <dt>确认密码：</dt>
            <dd>
                <input class="w400 text" name="paypwd_confirm" type="password" id="paypwd_confirm" value="" maxlength="30" placeholder="填写确认密码" />
                <span></span>
                <p>6-20位字符，可由英文、数字及标点符号组成。</p>
            </dd>
        </dl>
        <div class="bottom">
            <label class="submit-border">
                <input type="submit" class="submit" value="确认修改">
            </label>
        </div>
    </form>
</div>
<script type="text/javascript">
$('.send_success_tips').hide();
var ALLOW_SEND = true;
$(function() {
    function StepTimes() {
        $num = parseInt($('#show_times').html());
        $num = $num - 1;
        $('#show_times').html($num);
        if ($num <= 0) {
            ALLOW_SEND = !ALLOW_SEND;
            $('.send_success_tips').hide();
        } else {
            setTimeout(StepTimes, 1000);
        }
    }
    $('#send_captcha').on('click', function() {
        if (!ALLOW_SEND) return;
        ALLOW_SEND = !ALLOW_SEND;
        $('#sending').show();
        $.getJSON('index.php?act=common&op=get_sms_captcha', { mobile: $('#mobile').val(), sms_type: 4 }, function(data) {
            if (data.state == 'true') {
                $('#sending').hide();
                $('#show_times').html(60);
                $('.send_success_tips').show();
                setTimeout(StepTimes, 1000);
            } else {
                ALLOW_SEND = !ALLOW_SEND;
                $('#sending').hide();
                showDialog(data.msg, 'error', '', '', '', '', '', '', '', '', 2);
            }
        });
    });
    $('#paypwd_form').validate({
        errorPlacement: function(error, element) {
            var error_td = element.parent('dd');
            error_td.find('span').append(error);
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
            captcha: {
                required: true,
            },
            paypwd: {
                required: true,
                minlength: 6,
                maxlength: 20
            },
            paypwd_confirm: {
                required: true,
                equalTo: '#paypwd'
            },
        },
        messages: {
            captcha: {
                required: '<i class="icon-exclamation-sign"></i>验证码不能为空',
            },
            paypwd: {
                required: '<i class="icon-exclamation-sign"></i>密码不能为空',
                minlength: '<i class="icon-exclamation-sign"></i>密码长度在6-20个字符之间',
                maxlength: '<i class="icon-exclamation-sign"></i>密码长度在6-20个字符之间'
            },
            paypwd_confirm: {
                required: '<i class="icon-exclamation-sign"></i>请输入确认密码',
                equalTo: '<i class="icon-exclamation-sign"></i>两次输入的密码不相同'
            },
        }
    })
})
</script>