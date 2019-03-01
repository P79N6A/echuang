$(function() {
    var key = getCookie('key');
    if (key) {
        $.ajax({
            type: 'post',
            url: ApiUrl + "/index.php?act=member_property&op=member_balance_list",
            data: { key: key },
            dataType: 'json',

            success: function(result) {
                console.log(result)
                checkLogin(result.login);
                var html = '';
                var list = result.data.list;
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
                            html += '<div class="money reduce">' + list[i].change_amount + '</div>';
                        }
                        html += '<time class="date"></time>';
                        html += '</li>';

                    }
                } else {
                    html += '<div class="nctouch-norecord gold">';
                    html += '<div class="norecord-ico"><i></i></div>';
                    html += '<dl>';
                    html += '<dt>很抱歉，你还没有明细哦</dt>';
                    html += '<dd>赶快去消费吧</dd>';
                    html += '</dl>';
                    html += '</div>';
                }

                $('#gold-list').append(html);
            }

        });
    }

});