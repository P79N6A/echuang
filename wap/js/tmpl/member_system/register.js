$(function() {
    var invite = getQueryString('invite');

    if (invite != '') {
        var base = new Base64();
        var invite = base.decode(invite);
        $('#invite').val(invite);
        $('#invite').attr('readonly', 'readonly');
    }
    var access = getQueryString('access');
    if (access != '') {
        $('#invite').val(access);
        $('#invite').attr('readonly', 'readonly');
    }
    $.ajax({
        'type': "post",
        'url': ApiUrl + '/index.php?act=login&op=bankList',
        'data': {},
        'dataType': "json",
        success: function(result) {
            var list = result.datas.list;
            if (list.length == 0) {
                errorTipsShow("<p>银行信息获取失败，暂时不能注册</p>");
            }
            var html = '';
            html += '<option value="">-请选择银行-</option>';
            for (var i = 0; i < list.length; i++) {
                html += '<option value="' + list[i].bank_id + '">' + list[i].bank_name + '</option>';
            }
            $('#bank_name').append(html);
        }
    })

    $.sValid.init({
        rules: {
            username: {
                required: true
            },
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
            paypasswd: {
                required: true
            },
            invite: {
                required: true,
                mobile: true
            },
            access: {
                required: true,
                mobile: true
            },
            bank_account_name: {
                required: true,
            },
            bank_branch_name: {
                required: true,
            },
            bank_account_num: {
                required: true,
                number: true
            }
        },
        messages: {
            username: {
                required: "请输入用户名"
            },
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
            paypasswd: {
                required: "请输入支付密码"
            },
            invite: {
                required: "请输入邀请人手机",
                mobile: "邀请人手机格式错误"
            },
            access: {
                required: "请输入接点人手机",
                mobile: "接点人手机格式错误"
            },
            bank_acount_name: {
                required: "请输入银行账户"
            },
            bank_branch_name: {
                required: "请输入支行名称",
            },
            bank_account_num: {
                required: "请输入银行卡号",
                number: "银行卡号格式错误"
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

    var register_member = 0;
    $('#registerbtn').click(function() {
        if (!$(this).parent().hasClass('ok')) {
            return false;
        }

        if (register_member) {
            errorTipsShow("<p>正在处理中，请勿重复点击！</p>");
            return false;
        }
        if ($.sValid()) {
            register_member = 1;
            var username = $.trim($("#username").val());
            var mobile = $.trim($("#mobile").val());
            var captcha = $.trim($("#captcha").val());
            var passwd = $.trim($("#passwd").val());
            var paypasswd = $.trim($("#paypasswd").val());
            var invite = $.trim($("#invite").val());
            var access = $.trim($("#access").val());
            var area = $.trim($("#area").val());
            var client = 'wap';
            var bank_name = $('#bank_name').val();
            var bank_account_name = $.trim($('#bank_account_name').val());
            var bank_branch_name = $.trim($('#bank_branch_name').val());
            var bank_account_num = $.trim($('#bank_account_num').val());
            $.ajax({
                type: 'post',
                url: ApiUrl + "/index.php?act=login&op=register",
                data: { username: username, mobile: mobile, captcha: captcha, passwd: passwd, paypasswd: paypasswd, invite: invite, access: access, area: area, client: client, bank_account_name: bank_account_name, bank_name: bank_name, bank_branch_name: bank_branch_name, bank_account_num: bank_account_num },
                dataType: 'json',
                success: function(result) {
                    if (!result.datas.error) {
                        if (typeof(result.datas.key) == 'undefined') {
                            return false;
                        } else {
                            // 更新cookie购物车
                            updateCookieCart(result.datas.key);
                            addCookie('username', result.datas.username);
                            addCookie('key', result.datas.key);
                            location.href = WapSiteUrl + '/tmpl/member_system/center.html';
                        }
                        errorTipsHide();
                    } else {
                        errorTipsShow("<p>" + result.datas.error + "</p>");
                        register_member = 0;
                    }
                }
            });
        }

    });
});

// 发送手机验证码
function send_sms(mobile) {
    $.getJSON(ApiUrl + '/index.php?act=connect&op=get_sms_captcha', { type: 1, phone: escape(mobile) }, function(result) {
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
            errorTipsShow('<p>' + result.datas.error + '<p>');
        }
    });
}

function check_sms_captcha(mobile, captcha) {
    $.getJSON(ApiUrl + '/index.php?act=connect&op=check_sms_captcha', { type: 1, phone: mobile, captcha: captcha }, function(result) {
        if (!result.datas.error) {
            window.location.href = 'register_mobile_password.html?mobile=' + mobile + '&captcha=' + captcha;
        } else {
            //loadSeccode();
            errorTipsShow('<p>' + result.datas.error + '<p>');
        }
    });
}