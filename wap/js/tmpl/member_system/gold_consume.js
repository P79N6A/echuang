$(function() {
    var key = getCookie('key');
    if (!key) {
        window.location.href = WapSiteUrl + '/tmpl/member_system/login.html';
        return;
    }

    // 金豆消费
    $('#consumebtn').click(function() {
        var amount = $('#amount').val();
        var passwd = $('#passwd').val();
        // 金豆消费
        $.ajax({
            type: 'post',
            url: ApiUrl + "/index.php?act=member_property&op=equity_exchange",
            data: { key: key, amount: amount, paypwd: passwd },
            dataType: 'json',
            success: function(result) {
                console.log(result)
                checkLogin(result.msg);
                console.log(result.error)
                if (result.status == 2) {
                    console.log(result.msg)
                    errorTipsShow(result.msg);
                    return false;
                }
                 window.location.href = WapSiteUrl + '/tmpl/member_system/gold_consume_success.html';

            }
        });
    });

});