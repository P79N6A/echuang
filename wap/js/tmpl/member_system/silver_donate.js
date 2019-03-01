$(function() {
    var key = getCookie('key');
    if (!key) {
        window.location.href = WapSiteUrl + '/tmpl/member_system/login.html';
        return;
    }
    $.ajax({
        type: 'post',
        url: ApiUrl + "/index.php?act=member_property&op=checkServer",
        data: { key: key },
        dataType: 'json',

        success: function(result) {
            checkLogin(result.login);

            if (!result.data.server) {
                errorTipsShow('不是服务中心不能转赠银豆');
            }
        }
    });

    $('#donatebtn').click(function() {
        var amount = $('#amount').val();
        var to_mobile = $('#to_mobile').val();
        var passwd = $('#passwd').val();

        $.ajax({
            type: 'post',
            url: ApiUrl + '/index.php?act=member_property&op=silverDonate',
            data: { key: key, amount: amount, to_mobile: to_mobile, passwd: passwd },
            dataType: 'json',

            success: function(result) {
            	checkLogin(result.login);
            	if (result.datas.error) {
                    errorTipsShow(result.datas.error);
                    return;
                }
                window.location.href = WapSiteUrl + '/tmpl/member_system/silver_donate_success.html';
            }
        })
    });
});