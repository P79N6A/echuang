$(function() {
    var key = getCookie('key');
    if (!key) {
        window.location.href = WapSiteUrl + '/tmpl/member_system/login.html';
        return;
    }

    $.ajax({
        type: 'post',
        url: ApiUrl + "/index.php?act=member_bank&op=memberBankList",
        data: { key: key },
        dataType: 'json',
        success: function(result) {
            checkLogin(result.login);
            var list = result.datas.list;
            var html = '';
            if (list.length > 0) {
                for (var i = 0; i <= list.length - 1; i++) {
                    html += '<option value=' + list[i].mb_id + '>' + list[i].bank + '</option>';
                }
                $('#bank').append(html);
            }
        }
    });

    // 金豆提现
    $('#withdrawbtn').click(function() {
        var amount = $('#amount').val();
        var bank = getSelectComponentValue('bank');
        var passwd = $('#passwd').val();
        
        $.ajax({
            type: 'post',
            url: ApiUrl + "/index.php?act=member_property&op=goldWithdraw",
            data: { key: key, amount: amount, bank: bank, passwd: passwd },
            dataType: 'json',
            success: function(result) {
                checkLogin(result.login);
                if (result.datas.error) {
                    errorTipsShow(result.datas.error);
                    return;
                }
                // errorTipsShow('提现成功');
                location.href = WapSiteUrl + '/tmpl/member_system/gold_withdraw_success.html';
            }
        });
    });
    // // 判断银行卡
    // $.ajax({
    //     type: 'post',
    //     url: ApiUrl + "/index.php?act=member_bank&op=memberBankList",
    //     data: { key: key },
    //     dataType: 'json',
    //     success: function(result) {
    //         checkLogin(result.login);
    //         var list = result.datas.list;
    //         var html = '';
    //         if (list.length > 0) {
    //             html += '<form action="" method="">';
    //             html +='<ul class="form-box">';
    //             html +='<li><img src="../../images/member_system/bean/gold_withdraw.png"></li>';
    //             html +='<li class="form-item">';
    //             html +='<p>提现金额:</p>';
    //             html +='<input type="text" id="amount" name="amount" placeholder="请输入提现金额" oninput="writeClear($(this));"/>';
    //             html +='</li>';
    //             html +='<li class="form-item">';
    //             html +='<p>提现银行账号:</p>';
    //             html +='<select name="bank" id="bank">';
    //             html +='<option value="">—请选择银行卡—</option>';
    //             for (var i = 0; i <= list.length - 1; i++) {
    //                 html += '<option value=' + list[i].mb_id + '>' + list[i].bank + '</option>';
    //             }
    //             html += '</select>';
    //             html += '</li>';
    //             html += '<li class="form-item">';
    //             html += '<p>支付密码:</p>';
    //             html += '<input type="password" id="passwd" placeholder="请输入支付密码" oninput="writeClear($(this));"/>';
    //             html += '</li>';
    //             html += '</ul>';
    //             html += '<div class="error-tips"></div>';
    //             html += '<div class="form-btn">';
    //             html += '<button class="btn" id="withdrawbtn">提交</button>';
    //             html += '</div>';

    //             // html += '<li>';
    //             // html += '<div class="submit">';
    //             // html += '<span>';
    //             // html += '<input type="button" class="submit-btn" id="submit-btn" value="提交">';
    //             // html += '</span>';
    //             // html += '</div>';
    //             // html += '</li>';
    //             // 


    //             //     <li>
    //             //         <img src="../../images/member_system/bean/gold_withdraw.png">
    //             //     </li>

    //             //         <p>手机号</p>
    //             //         <div class="input-box">
    //             //             <input type="text" placeholder="请输入手机号" class="txt" name="mobile" id="mobile" oninput="writeClear($(this));">
    //             //         </div>
    //             //     </li>
    //             //     <li class="form-item">
    //             //         <p class="bean-p">密&nbsp;码</p>
    //             //         <div class="input-box">
    //             //             <input type="password" placeholder="请输入登录密码" class="txt" name="userpwd" id="userpwd" oninput="writeClear($(this));">
    //             //         </div>
    //             //     </li>
    //             // </ul>





    //         } else {
    //             html += '<div class="nctouch-norecord bank">';
    //             html += '<div class="norecord-ico"><i></i></div>';
    //             html += '<dl>';
    //             html += '<dt>你还没添加银行卡哦</dt>';
    //             html += '<dd>赶快去添加吧</dd>';
    //             html += '</dl>';
    //             html += '<a href="bank_add.html?ref=gold_bean" class="btn" style="color:#00D1B0 !important;border: solid 1px #00D1B0; border-radius: 0.2rem;">去添加</a>';
    //             html += '</div>';   
    //         }
    //         $('.bean-content').append(html);
    //     }
    // });
});

