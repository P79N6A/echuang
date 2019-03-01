$(function() {
    var key = getCookie('key');
    if (!key) {
        window.location.href = WapSiteUrl + '/tmpl/member_system/login.html';
        return;
    }
    // $.ajax({
    //     type: 'post',
    //     url: ApiUrl + "/index.php?act=member_property&op=checkPaypasswd",
    //     data: { key: key },
    //     dataType: 'json',
    //     success: function(result) {
    //         checkLogin(result.login);
    //         checkPaypasswd(result.datas.paypasswd);
    //     }
    // });
    $('#donatebtn').click(function() {
    	var amount = $('#amount').val();
        var to_mobile = $('#to_mobile').val();
        var passwd = $('#passwd').val();
        // 判断银行卡
        $.ajax({
            type: 'post',
            url: ApiUrl + "/index.php?act=member_property&op=goldDonate",
            data: { key: key, amount: amount, to_mobile: to_mobile, passwd: passwd },
            dataType: 'json',
            success: function(result) {
                checkLogin(result.login);
                if (result.datas.error) {
                    errorTipsShow(result.datas.error);
                    return;
                }
                window.location.href = WapSiteUrl + '/tmpl/member_system/gold_donate_success.html';
            }
        });
    })
});