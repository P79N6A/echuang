$(function() {
    var key = getCookie('key');
    if (!key) {
        location.href = WapSiteUrl + '/tmpl/member_system/login.html';
    }

    // var getQueryString('');
    var member_id = getQueryString('member_id');
    $.ajax({
        type: 'post',
        url: ApiUrl + '/index.php?act=member_family&op=accessFamilyTree',
        data: { key: key, member_id: member_id },
        dataType: 'json',
        success: function(result) {
            checkLogin(result.login);
            if (result.datas.error) {
                errorTipsShow(result.datas.error);
                return;
            }
            var performance = result.datas.performance;
            $('#left_total_performance').html(performance.performance1);
            $('#right_total_performance').html(performance.performance2);
            $('#left_surplus_performance').html(performance.surplus1);
            $('#right_surplus_performance').html(performance.surplus2);
            var html = '';
            var map_arr = result.datas.map_arr;
            html += '<ul>';
            html += '<li>';
            html += '<a href="access_tree.html?member_id=' + map_arr[0][0].member_id + '">' + map_arr[0][0].member_mobile + '</a>';
            html += '<ul>';
            html += '<li>';
            if (map_arr[1][0].member_id == '') {
                if (map_arr[1][0].access_mobile == '') {
                    html += '<a href="#"></a>';
                } else {
                    html += '<a href="register.html?access=' + map_arr[1][0].access_mobile + '">注册</a>'
                }
            } else {
                html += '<a href="access_tree.html?member_id=' + map_arr[1][0].member_id + '">' + map_arr[1][0].member_mobile + '</a>';
            }

            html += '<ul>';
            html += '<li>';
            if (map_arr[2][0].member_id == '') {
                if (map_arr[2][0].access_mobile == '') {
                    html += '<a href="#"></a>';
                } else {
                    html += '<a href="register.html?access=' + map_arr[2][0].access_mobile + '">注册</a>'
                }
            } else {
                html += '<a href="access_tree.html?member_id=' + map_arr[2][0].member_id + '">' + map_arr[2][0].member_mobile + '</a>';
            }
            html += '</li>';
            html += '<li>';
            if (map_arr[2][1].member_id == '') {
                if (map_arr[2][1].access_mobile == '') {
                    html += '<a href="#"></a>';
                } else {
                    html += '<a href="register.html?access=' + map_arr[2][1].access_mobile + '">注册</a>'
                }
            } else {
                html += '<a href="access_tree.html?member_id=' + map_arr[2][1].member_id + '">' + map_arr[2][1].member_mobile + '</a>';
            }
            html += '</li>';
            html += '</ul>';
            html += '</li>';
            html += '<li>';
            if (map_arr[1][1].member_id == '') {
                if (map_arr[1][1].access_mobile == '') {
                    html += '<a href="#"></a>';
                } else {
                    html += '<a href="register.html?access=' + map_arr[1][1].access_mobile + '">注册</a>'
                }
            } else {
                html += '<a href="access_tree.html?member_id=' + map_arr[1][1].member_id + '">' + map_arr[1][1].member_mobile + '</a>';
            }
            html += '<ul>';
            html += '<li>';
            if (map_arr[2][2].member_id == '') {
                if (map_arr[2][2].access_mobile == '') {
                    html += '<a href="#"></a>';
                } else {
                    html += '<a href="register.html?access=' + map_arr[2][2].access_mobile + '">注册</a>'
                }
            } else {
                html += '<a href="access_tree.html?member_id=' + map_arr[2][2].member_id + '">' + map_arr[2][2].member_mobile + '</a>';
            }
            html += '</li>';
            html += '<li>';
            if (map_arr[2][3].member_id == '') {
                if (map_arr[2][3].access_mobile == '') {
                    html += '<a href="#"></a>';
                } else {
                    html += '<a href="register.html?access=' + map_arr[2][3].access_mobile + '">注册</a>'
                }
            } else {
                html += '<a href="access_tree.html?member_id=' + map_arr[2][3].member_id + '">' + map_arr[2][3].member_mobile + '</a>';
            }
            html += '</li>';
            html += '</ul>';
            html += '</li>';
            html += '</ul>';
            html += '</li>';
            html += '</ul>';
            $('.tree').append(html);
        }
    })
})