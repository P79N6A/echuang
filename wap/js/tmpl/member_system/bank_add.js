$(function() {
    var key = getCookie('key');
    if (!key) {
        window.location.href = WapSiteUrl + '/tmpl/member_system/login.html';
        return;
    }
    var ref = getQueryString('ref');
    $.ajax({
        type: 'post',
        url: ApiUrl + "/index.php?act=member_bank&op=bankList",
        data: { key: key },
        dataType: 'json',

        success: function(result) {
            checkLogin(result.login);
            var list = result.datas.list;
            var html = '<option value="">-请选择银行-</option>';
            for (var i = 0; i <= list.length - 1; i++) {
                var bank = list[i];
                html += '<option value=' + bank.bank_id + '>' + bank.bank_name + '</option>';
            }
            $('#bank').append(html);
        }
    });


    $('#bankaddbtn').click(function() {
        var account_name = $('#account_name').val();
        var bank = getSelectComponentValue('bank');
        var account_branch = $('#account_branch').val();
        var account_num = $('#account_num').val();
        $.ajax({
            type: 'post',
            url: ApiUrl + '/index.php?act=member_bank&op=memberBankAdd',
            data: { key: key, account_name: account_name, bank: bank, account_branch: account_branch, account_num: account_num },
            dataType: 'json',

            success: function(result) {
                checkLogin(result.login);
                if (result.datas.error) {
                    errorTipsShow(result.datas.error);
                    return;
                }
                if (ref) {
                    location.href = WapSiteUrl + '/tmpl/member_system/bank.html?ref=' + ref;
                } else {
                    location.href = WapSiteUrl + '/tmpl/member_system/bank.html';
                }
            }
        });
    })
});