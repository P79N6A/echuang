$(function() {
    var key = getCookie('key');
    if (!key) {
        location.href = WapSiteUrl + '/tmpl/member_system/login.html'
    }

    var bank_id = getQueryString('bank_id');
    $.ajax({
        type: 'post',
        url: ApiUrl + "/index.php?act=member_bank&op=memberBankInfoDetail",
        data: { key: key, bank_id: bank_id },
        dataType: 'json',
        success: function(result) {
            checkLogin(result.login);
            if (result.datas.error) {
                errorTipsShow(result.datas.error);
                return;
            }
            var info = result.datas.info;
            var html = '';

            html += '<div class="bank_list" style="margin-top:10px;">';
            html += '<div class="bank_list_info">';
            html += '<p>';
            html += '<span>' + info.mb_account_bank_name + '</span>';
            html += '<span>' + info.mb_account_branch + '</span>';
            html += '</p>';
            html += '<p>';
            html += '<span>' + info.mb_account_name + '</span>';
            html += '</p>';
            html += '<p>';
            html += '<span>' + info.bank_num + '</span>';
            html += '</p>';
            html += '</div>';
            html += '</div>';

            $('#bank_info').append(html);
        }
    });

    $('#bank_relieve').click(function() {
    	$.ajax({
        type: 'post',
        url: ApiUrl + "/index.php?act=member_bank&op=memberBankDelete",
        data: { key: key, bank_id: bank_id },
        dataType: 'json',
        success: function(result) {
            checkLogin(result.login);
            if (result.datas.error) {
                errorTipsShow(result.datas.error);
                location.href = WapSiteUrl + '/tmpl/member_system/bank.html';
                return;
            }
            alert('银行卡解除绑定成功');
            location.href = WapSiteUrl + '/tmpl/member_system/bank.html';
        }
    });
    })
})