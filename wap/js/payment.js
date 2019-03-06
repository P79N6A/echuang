$(function() {
    var key = getCookie('key');
    if (!key) {
        location.href = WapSiteUrl + '/tmpl/member_system/login.html';
    }

    var pay_sn = getQueryString('pay_sn');
    if (!pay_sn) {
        errorTipsShow('参数错误');
    } else {
        $.ajax({
            type: 'post',
            url: ApiUrl + '/index.php?act=member_order&op=checkPaysn',
            data: { key: key, pay_sn: pay_sn },
            dataType: 'json',
            success: function(result) {
                console.log(result)
                checkLogin(result.login);
                if (result.datas.error) {
                    errorTipsShow(result.datas.error);
                } else {
                    $.ajax({
                        type: 'post',
                        url: ApiUrl + '/index.php?act=member_payment&op=getPaymentList',
                        data: { key: key },
                        dataType: 'json',
                        success: function(result) {
                            console.log(result)
                            var list = result.datas.list;
                            var html = '';
                            if (list.length == 0) {
                                errorTipsShow('暂无支持的支付方式');
                                location.href = history.back(-1);
                                return false;
                            } else {
                                for (var i = 0; i < list.length; i++) {
                                    html += '<dl class="mt5">';
                                    html += '<dt>';
                                    html += '<a id="pay-item" code="' + list[i].payment_code + '">';
                                    html += '<h3><i class="mcmc-0'+i+'"></i>' + list[i].payment_name + '</h3>';
                                    html += '<h5><i class="arrow-r"></i></h5>';
                                    html += '</a>';
                                    html += '</dt>';
                                    html += '</dl>';
                                }
                                $('.payment-list').append(html);

                                var payment_code = $('#payment_code').val();
                                if (payment_code != '') {
                                    $("a[code='" + payment_code + "'").parent().parent().addClass('selected');
                                }
                            }
                        }
                    });
                    $('.payment-list').on("click", "#pay-item", function() {

                        var code = $(this).attr('code');
                        console.log(code)
                        var payment_code = $('#payment_code').val();
                        // var payment_code=document.getElementById("payment_code").value;
                        console.log(payment_code)
                        errorTipsHide();
                        if (code === payment_code) {
                            return;
                        }
                        if ($("a[code='" + payment_code + "']").parent().parent().hasClass('selected')) {
                            $("a[code='" + payment_code + "']").parent().parent().removeClass('selected');
                        }
                        $('#payment_code').val(code);
                        $(this).parent().parent().addClass('selected');
                    });

                    $('#submitbtn').click(function() {
                        var payment_code = $('#payment_code').val();
                        if (payment_code == '') {
                            errorTipsShow("请选择支付方式");
                            return;
                        } else {
                            if (payment_code == 'balancepay'|| payment_code == 'goldpay' || payment_code == 'silverpay') {
                                $.dialog({
                                    titleText: '输入支付密码',
                                    showTitle: true,
                                    type: 'confirm',
                                    contentHtml: '<input type="password" name="pay_passwd" id="pay_passwd" placeholder="请输入支付密码" />',
                                    buttonText: {
                                        ok: '确认支付',
                                        cancel: '取消'
                                    },
                                    onClickOk: function() {
                                        var pay_passwd = $('#pay_passwd').val();
                                        if (pay_passwd == '') {
                                            errorTipsShow('请输入支付密码');
                                            return;
                                        } else {
                                            doPay(key, pay_sn, payment_code, pay_passwd);
                                        }
                                    },
                                });
                            } else if(payment_code == 'alipay_wap'){
                                location.href = ApiUrl + '/index.php?act=member_payment&op=wapPay&key=' + key + '&pay_sn=' + pay_sn + '&payment_code=' + payment_code;
                            }else{
                                location.href = ApiUrl + '/index.php?act=member_payment&op=pay_new&key=' + key + '&pay_sn=' + pay_sn + '&payment_code=' + 'wxpay_jsapi';
                            }
                        }

                    });

                }
            }
        });

    }

});

function doPay(key, pay_sn, payment_code, pay_passwd) {
    $.dialog({
        type: 'info',
        infoText: '加载中…',
        infoIcon: '../images/icon/loading.gif',
        autoClose: 2500
    });
    $.ajax({
        type: 'get',
        url: ApiUrl + '/index.php?act=member_payment&op=wapPay',
        data: {
            key: key,
            pay_sn: pay_sn,
            payment_code: payment_code,
            pay_passwd: pay_passwd
        },
        dataType: 'json',
        success: function(result) {
            checkLogin(result.login);

            if (result.datas.error) {
                errorTipsShow(result.datas.error);
                return;
            } 
            location.href = WapSiteUrl + '/tmpl/payment_success.html';
        }
    });
}