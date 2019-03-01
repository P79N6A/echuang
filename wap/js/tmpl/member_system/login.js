$(function() {
    var key = getCookie('key');
    // console.log(key);
    // return;
    if (key) {
        // delCookie('username');
        // delCookie('userid');
        // delCookie('key');

        window.location.href = WapSiteUrl + '/tmpl/member_system/center.html';
        return;
    }
    $.getJSON(ApiUrl + '/index.php?act=connect&op=get_state', function(result) {
        var ua = navigator.userAgent.toLowerCase();
        var allow_login = 0;

    });
    // 上级网址
    var referurl = document.referrer;

    if (!referurl) {
        try {
            if (window.opener) {
                referrer = window.opener.location.href;
            }

        } catch (e) {}
    }

    $.sValid.init({
        rules: {
            mobile: {
                required: true,
                mobile: true
            },
            userpwd: "required"
        },
        messages: {
            mobile: {
                required: "手机号必填！",
                mobile: "手机号格式错误"
            },
            userpwd: "密码必填!"
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

    // 会员登录
    var allow_submit = true;
    $('#loginbtn').click(function() {
        console.log("这回没找错吧")
        console.log(allow_submit)
        if (!$(this).parent().hasClass('ok')) {
            return false;
        }

        if (allow_submit) {
            allow_submit = false;
        } else {
            return false;
        }
        console.log(allow_submit)
        var username = $('#mobile').val();
        var pwd = $('#userpwd').val();
        var client = 'wap';
        if ($.sValid()) {
            $.ajax({
                type: 'post',
                url: ApiUrl + "/index.php?act=login",
                data: { username: username, password: pwd, client: client },
                dataType: 'json',
                success: function(result) {
                    allow_submit = true;
                    if (!result.datas.error) {
                        if (typeof(result.datas.key) == 'undefined') {
                            return false;
                        } else {
                            var expireHours = 0;
                            if ($('#checkbox').prop('checked')) {
                                expireHours = 188;
                            }
                            // 更新cookie购物车
                            updateCookieCart(result.datas.key);
                            addCookie('username', result.datas.username, expireHours);
                            addCookie('key', result.datas.key, expireHours);
                            if (result.datas.sell) {
                                if (result.datas.sell.seller_name && result.datas.sell.key) {
                                    addCookie('seller_name', result.datas.sell.seller_name, expireHours);
                                    addCookie('store_name', result.datas.sell.store_name, expireHours);
                                    addCookie('seller_key', result.datas.sell.key, expireHours);
                                }
                            }
                            var loginUrl = WapSiteUrl + '/tmpl/member_system/change_loginpasswd.html';
                            var payUrl = WapSiteUrl + '/tmpl/member_system/change_paypasswd.html';
                            if (referurl.localeCompare(loginUrl) == 0 || referurl.localeCompare(payUrl) == 0) {
                                referurl = WapSiteUrl + '/tmpl/member_system/center.html';
                            }
                            location.href = referurl;
                        }
                        errorTipsHide();
                    } else {
                        errorTipsShow(result.datas.error);
                        allow_submit = true;
                    }
                }
            });
        }
    });
});