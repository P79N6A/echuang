$(function() {
    var key = getCookie('key');
    if (!key) {
        window.location.href = WapSiteUrl + '/tmpl/member_system/login.html';
        return;
    }

    $.ajax({
        type: 'post',
        url: ApiUrl + "/index.php?act=member_property&op=memberSilver",
        data: { key: key },
        dataType: 'json',

        success: function(result) {
            checkLogin(result.login);
            $('#silver-bean').html(result.datas.amount);
        }
    });

});