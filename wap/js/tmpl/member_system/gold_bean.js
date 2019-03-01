var paypwd = 0;
var bank_list = null;
$(function() {
    var key = getCookie('key');
    if (!key) {
        window.location.href = WapSiteUrl + '/tmpl/member_system/login.html';
        return;
    }
    var ref = 'gold_bean';

    $.ajax({
        type: 'post',
        url: ApiUrl + "/index.php?act=member_property&op=member_balance",
        data: { key: key },
        dataType: 'json',

        success: function(result) {
            checkLogin(result);
            $('#gold-bean').html(result.data.amount);
        }
    });
    
    checkPaypasswd();
    checkMemberBank();

    $('#gold-withdraw').click(function() {
        if (paypwd == 0) {
            var r = confirm('暂无支付密码，点击确定去设置');
            if (r == true) {
                location.href = WapSiteUrl + '/tmpl/member_system/change_paypasswd.html?ref=' + ref;
            }
        } else {
            if (bank_list.length == 0) {
                location.href = WapSiteUrl + '/tmpl/member_system/gold_withdraw_no_bank.html';
            } else {
                location.href = WapSiteUrl + '/tmpl/member_system/gold_withdraw.html';

            }
        }
    });

    $('#gold-donate').click(function() {
        if (paypwd == 0) {
            var r = confirm('暂无支付密码，点击确定去设置');
            if (r == true) {
                location.href = WapSiteUrl + '/tmpl/member_system/change_paypasswd.html?ref=' + ref;
            }
        } else {
            location.href = WapSiteUrl + '/tmpl/member_system/gold_donate.html';
        }
    });

    $('#gold-consume').click(function() {
        if (paypwd == 0) {
            var r = confirm('暂无支付密码，点击确定去设置');
            if (r == true) {
                location.href = WapSiteUrl + '/tmpl/member_system/change_paypasswd.html?ref=' + ref;
            }
        } else {
            location.href = WapSiteUrl + '/tmpl/member_system/gold_consume.html';
        }
    });

    // 检测支付密码
    function checkPaypasswd() {
        $.ajax({
            type: 'post',
            url: ApiUrl + "/index.php?act=member_property&op=checkPaypasswd",
            data: { key: key },
            dataType: 'json',
            success: function(result) {
                checkLogin(result.login);
                if (result.datas.paypasswd == 1) {
                    // var r = confirm('暂无支付密码，点击确定去设置');
                    // if (r == true) {
                    //     location.href = WapSiteUrl + '/tmpl/member_system/change_paypasswd.html?ref=' + ref;
                    //     return;
                    // }
                    paypwd = 1;
                }
            }
        });
    }

    // 检测会员银行卡
    function checkMemberBank() {
        var key = getCookie('key');
        $.ajax({
            type: 'post',
            url: ApiUrl + "/index.php?act=member_bank&op=memberBankList",
            data: { key: key },
            dataType: 'json',
            success: function(result) {
                checkLogin(result.login);
                var list = result.datas.list;
                // if (list.length == 0) {
                //     location.href = WapSiteUrl + '/tmpl/member_system/bank_add.html?ref=' + ref;
                //     return;
                // } else {
                bank_list = list;
                // }
            }
        });
    }

});