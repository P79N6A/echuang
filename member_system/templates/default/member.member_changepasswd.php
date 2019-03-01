<?php defined('In33hao') or exit('Access Invalid!');?>

<div class="tabmenu">
    <?php include template('layout/submenu');?>
</div>
<div class="ncsc-form-default">
    <form id="passwd_form" action="" method="post" enctype="multipart/form-data">
        <input type="hidden" name="form_submit" value="ok">
        <dl>
            <dt>旧密码：</dt>
            <dd>
                <input class="w400 text" name="password_old" type="password" id="password_old" value="" maxlength="30" placeholder="填写当前旧密码"/>
                <span></span>
                <p></p>
            </dd>
        </dl>
        <dl>
            <dt>登录密码：</dt>
            <dd>
                <input class="w400 text" name="password" type="password" id="password" value="" maxlength="30" placeholder="填写新密码"/>
                <span></span>
                <p>6-20位字符，可由英文、数字及标点符号组成。</p>
            </dd>
        </dl>
        <dl>
            <dt>确认密码：</dt>
            <dd>
                <input class="w400 text" name="password_confirm" type="password" id="password_confirm" value="" maxlength="30" placeholder="填写确认密码"/>
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
$(function(){
    $('#passwd_form').validate({
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
            password_old: {
                required: true,
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
        },
        messages: {
            password_old: {
                required: '<i class="icon-exclamation-sign"></i>用户名不能为空',
            },
            password: {
                required: '<i class="icon-exclamation-sign"></i>密码不能为空',
                minlength: '<i class="icon-exclamation-sign"></i>密码长度在6-20个字符之间',
                maxlength: '<i class="icon-exclamation-sign"></i>密码长度在6-20个字符之间'
            },
            password_confirm: {
                required: '<i class="icon-exclamation-sign"></i>请输入确认密码',
                equalTo: '<i class="icon-exclamation-sign"></i>两次输入的密码不相同'
            },
        }
    })
})
</script>