$(function() {
    var key = getCookie('key');

    if (!key) {
        window.location.href = WapSiteUrl + '/tmpl/member_system/login.html';
        return;

    }
    $.ajax({
        type: 'post',
        url: ApiUrl + "/index.php?act=member_property&op=memberEquity",
        data: { key: key },
        dataType: 'json',

        success: function(result) {
            checkLogin(result.login);
            $('#equity').html(result.datas.amount);
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
                html += '<div class="nctouch-norecord equity">';
                html += '<div class="norecord-ico"><i></i></div>';
                html += '<dl>';
                html += '<dt>你还没可看的明细</dt>';
                html += '<dd>赶紧去购买更多喜欢的</dd>';
                html += '</dl>';
                html += '</div>';
            }

            $('#equity-list').append(html);
        }
    });

    $('#equity-num').click(function() {
        $.ajax({
            type: 'post',
            url: ApiUrl + '/index.php?act=member_property&op=memberEquityNum',
            data: { key: key },
            dataType: 'json',

            success: function(result) {
                alert('金券数量:' + result.datas.amount);
            }
        });
    });
});