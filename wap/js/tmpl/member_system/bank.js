$(function() {
    var key = getCookie('key');
    if (!key) {
        window.location.href = WapSiteUrl + '/tmpl/member_system/login.html';
        return;
    }
    var ref = getQueryString('ref');
    $.ajax({
        type: 'post',
        url: ApiUrl + '/index.php?act=member_bank&op=memberBankList',
        data: { key: key },
        dataType: 'json',

        success: function(result) {
            var list = result.datas.list;
            var html = '';
            if (list.length == 0) {
                html += '<ul class="form-box" style="text-align: center">';
                html += '<div>';
                html += '<img src="../../images/member_system/nobank.png" width="100" height="100">';
                html += '</div>';
                html += '<p>你还没有添加银行</p><p>添加银行卡方便提现</p>';
                html += '</ul>';
                html += '<div class="form-btn">';
                html += '<a class="btn" href="bank_add.html">添加银行卡</a>';
                html += '</div>';
            } else {
                for (var i = 0; i < list.length; i++) {
                    html += '<a href="bank_info.html?bank_id='+list[i].mb_id+'">';
                    html += '<div class="bank_list">';
                    html += '<div class="bank_list_info">';
                    html += '<p>';
                    html += '<span>' + list[i].mb_account_bank_name + '</span>';
                    html += '<span>' + list[i].mb_account_branch + '</span>';
                    html += '</p>';
                    html += '<p>';
                    html += '<span>' + list[i].mb_account_name + '</span>';
                    html += '</p>';
                    html += '<p>';
                    html += '<span>' + list[i].bank_account_num + '</span>';
                    html += '</p>';
                    html += '</div>';
                    html += '</div>';
                    html += '</a>';
                }

            }

            $("#bank").append(html);
        }
    })
    $('#addbankbtn').click(function() {
        location.href = WapSiteUrl + '/tmpl/member_system/bank_add.html';
    });
    $('#back').click(function() {
        if (ref) {
            location.href = WapSiteUrl + '/tmpl/member_system/' + ref + '.html';
        } else {
            location.href = WapSiteUrl + '/tmpl/member_system/setting.html';
        }
    });
    $('#toaddbtn').click(function() {
        if (ref) {
            location.href = WapSiteUrl + '/tmpl/member_system/bank_add.html?ref=' + ref;
        } else {
            location.href = WapSiteUrl + '/tmpl/member_system/bank_add.html';
        }
    });
})