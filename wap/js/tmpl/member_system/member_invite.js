$(function() {
    var key = getCookie('key');
    if (key == '') {
        window.location.href = WebSiteUrl + '/tmp/member_system/login.html';
        return;
    }
    
    $.ajax({
        type: 'post',
        url: ApiUrl + "/index.php?act=member_family&op=memberInvite",
        data: { key: key },
        dataType: 'json',

        success: function(result) {
            checkLogin(result.login);
            $('#myurl_src').attr("src", result.datas.invite.myurl_src);
            return false;
        }
    });
});