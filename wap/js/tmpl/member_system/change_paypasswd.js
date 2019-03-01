$(function() {
    var key = getCookie('key');
    if (!key) {
        window.location.href = WapSiteUrl + '/tmpl/member_system/login.html';
        return;
    }
    var ref = getQueryString('ref');

    $.ajax({
        type: 'post',
        url: ApiUrl + "/index.php?act=setting&op=getMemberMobile",
        data: { key: key },
        dataType: 'json',

        success: function(result) {
            checkLogin(result.login);
            $("#mobile_show").val(result.datas.mobile_show);
            $("#mobile_show").attr("readonly", "readonly");
            $("#mobile").val(result.datas.mobile);
        }
    });

    $.sValid.init({
        rules: {

            mobile: {
                required: true,
                mobile: true
            },
            captcha: {
                required: true
            },
            passwd: {
                required: true
            },
            passwd_confirm: {
                required: true
            }
        },
        messages: {

            mobile: {
                required: "请填写手机号",
                mobile: "手机号格式错误"
            },
            captcha: {
                required: "请输入验证码"
            },
            passwd: {
                required: "请输入密码"
            },
            passwd_confirm: {
                required: "请输入确认密码"
            }
        },
        callback: function(eId, eMsg, eRules) {
            if (eId.length > 0) {
                var errorHtml = "";
                $.map(eMsg, function(idx, item) {
                    errorHtml += "<p>" + idx + "</p>";
                });
                errorTipsShow(errorHtml);
            } else {
                errorTipsHide();
            }
        }
    });

    var change_passwd = 0;
    $('#changebtn').click(function() {
        if (!$(this).parent().hasClass('ok')) {
            return false;
        }
        if (change_passwd) {
            errorTipsShow("正在处理中，请勿重复点击！");
            return false;
        }
        var mobile = $.trim($('#mobile').val());
        var captcha = $.trim($('#captcha').val());
        var passwd = $.trim($('#passwd').val());
        var passwd_confirm = $.trim($('#passwd_confirm').val());
        change_passwd = 1;
        $.ajax({
            type: 'post',
            url: ApiUrl + "/index.php?act=setting&op=changePayPasswd",
            data: { key: key, mobile: mobile, captcha: captcha, passwd: passwd, passwd_confirm: passwd_confirm },
            dataType: 'json',
            success: function(result) {
                checkLogin(result.login);
                if (result.datas.ereror) {
                    errorTipsShow(result.datas.error);
                    change_passwd = 0;
                }
                if (ref) {
                    location.href = WapSiteUrl + '/tmpl/member_system/' + ref + '.html';
                } else {
                    location.href = WapSiteUrl + '/tmpl/member_system/center.html';
                }
                return;
            }
        });
    });

    // 发送手机验证码
    $('#send').click(function() {
        var mobile = $.trim($("#mobile").val());
        if (mobile == '') {
            errorTipsShow("请输入手机号");
            return false;
        }
        var reg = /^[1][3,4,5,7,8][0-9]{9}$/;
        if (!reg.test(mobile)) {
            errorTipsShow("手机号格式错误");
            return false;
        }
        send_sms(mobile);
    });

    // 返回
    $('#back').click(function() {
        if (ref) {
            location.href = WapSiteUrl + '/tmpl/member_system/' + ref + '.html';
        } else {
            window.history.go(-1);

        }
    })
});

// 发送手机验证码
function send_sms(mobile) {
    $.getJSON(ApiUrl + '/index.php?act=connect&op=get_sms_captcha', { type: 4, phone: escape(mobile) }, function(result) {
        if (!result.datas.error) {
            $.sDialog({
                skin: "green",
                content: '发送成功',
                okBtn: false,
                cancelBtn: false
            });
            $('.code-again').hide();
            $('.code-countdown').show().find('em').html(result.datas.sms_time);
            var times_Countdown = setInterval(function() {
                var em = $('.code-countdown').find('em');
                var t = parseInt(em.html() - 1);
                if (t == 0) {
                    $('.code-again').show();
                    $('.code-countdown').hide();
                    clearInterval(times_Countdown);
                } else {
                    em.html(t);
                }
            }, 1000);
        } else {
            errorTipsShow(result.datas.error);
        }
    });
}