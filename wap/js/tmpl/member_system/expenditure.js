$(function() {
    var key = getCookie('key');

    if (key) {
        $.ajax({
            type: 'post',
            url: ApiUrl + "/index.php?act=member_property&op=memberExpenditure",
            data: { key: key },
            dataType: 'json',

            success: function(result) {
                checkLogin(result.login);
                $('#expenditure').html(result.datas.amount);
            }
        });
    } else {
        window.location.href = WapSiteUrl + '/tmpl/member_system/login.html';
        return;
    }
});