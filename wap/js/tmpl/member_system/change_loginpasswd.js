$(function() {
    var key = getCookie('key');
    if (!key) {
        window.location.href = WapSiteUrl + '/tmpl/member_system/login.html';
        return;
    }
    var change_passwd = 0;
    $('#loginpasswdbtn').click(function() {
        if (change_passwd) {
            errorTipsShow("正在处理中，请勿重复点击！");
            return false;
        }
        var old_passwd = $('#old_passwd').val();
        var new_passwd = $('#new_passwd').val();
        var passwd_confirm = $('#passwd_confirm').val();
        change_passwd = 1;
        $.ajax({
            type: 'post',
            url: ApiUrl + '/index.php?act=setting&op=changeLoginPasswd',
            data: { key: key, old_passwd: old_passwd, new_passwd: new_passwd, passwd_confirm: passwd_confirm },
            dataType: 'json',
            success: function(result) {
                change_passwd = 0;
                checkLogin(result.login);
                if (result.datas.error) {
                    errorTipsShow(result.datas.error);
                    return;
                }

                delCookie('username');
                delCookie('userid');
                delCookie('key');
                location.href = WapSiteUrl + '/tmpl/member_system/login.html';
                return;
            }
        })
    });
})