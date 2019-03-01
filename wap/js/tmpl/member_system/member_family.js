$(function() {
    var key = getCookie('key');
    if (!key) {
        window.location.href = WapSiteUrl + '/tmpl/member_system/login.html';
    }

    $.ajax({
        type: 'post',
        url: ApiUrl + "/index.php?act=member_family&op=memberDirectList",
        data: { key: key },
        dataType: 'json',

        success: function(result) {
            checkLogin(result.login);
            var list = result.datas.list;
            var html = '';
            if (list.length > 0) {

                for (var i = 0; i < list.length; i++) {
                    html += '<li>';
                    html += '<img src="' + list[i].avatar + '" alt="" style="width: 64px;height: 64px">';
                    html += '<div style="display: inline-grid;margin-left: 5px">';
                    html += '<span>' + list[i].name + '</span>';
                    html += '<span>' + list[i].mobile + '</span>';
                    html += '</div>';
                    html += '<time class="date">' + list[i].time + '</time>';
                    html += '</li>';
                }
            } else {
                html += '<div class="nctouch-norecord family">';
                html += '<div class="norecord-ico"><i></i></div>';
                html += '<dl>';
                html += '<dt>暂时没有直推的会员</dt>';
                html += '<dd>赶快去寻找直推吧</dd>';
                html += '</dl>';
                html += '</div>';
            }
            $('#direct-list').html(html);
        }
    });
});