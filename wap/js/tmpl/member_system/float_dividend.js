$(function() {
    var key = getCookie('key');

    if (key) {
        $.ajax({
            type: 'post',
            url: ApiUrl + "/index.php?act=member_property&op=memberFloatDividend",
            data: { key: key },
            dataType: 'json',

            success: function(result) {
                checkLogin(result.login);
                var html = '';
                var list = result.datas.list;
                if (list.length > 0) {
                    for (var i = 0; i < list.length; i++) {
                        html += '<li>';
                        html += '<dl>';
                        html += '<dt>' + list[i].title + '</dt>';
                        html += '<dd>' + list[i].time + '</dd>';
                        html += '</dl>';
                        if (list[i].amount >= 0) {
                            html += '<div class="money add">' + list[i].amount + '</div>';
                        } else {
                            html += '<div class="money reduce">' + list[i].amount + '</div>';
                        }
                        html += '<time class="date"></time>';
                        html += '</li>';
                    }
                } else {
                    html += '<div class="nctouch-norecord float-dividend">';
                    html += '<div class="norecord-ico"><i></i></div>';
                    html += '<dl>';
                    html += '<dt>很抱歉，您还没有分红哦</dt>';
                    html += '<dd>马上就会有的</dd>';
                    html += '</dl>';
                    html += '</div>';
                }

                $('#float-dividend-list').append(html);
            }
        });
    } else {
        window.location.href = WapSiteUrl + '/tmpl/member_system/login.html';
        return;
    }
});