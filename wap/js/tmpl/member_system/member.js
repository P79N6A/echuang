$(function() {
    if (getQueryString('key') != '') {
        var key = getQueryString('key');
        var username = getQueryString('username');
        addCookie('key', key);
        addCookie('username', username);
    } else {
        var key = getCookie('key');
    }
    if (!key) {
        location.href = WapSiteUrl + '/tmpl/member_system/login.html';
    }
    var redirect_uri = getCookie('redirect_uri');
    if (redirect_uri && getQueryString('info') == 'hao') {
        window.location.href = WapSiteUrl + redirect_uri;
    }
    console.log(key)
    if (key) {
        $.ajax({
            type: 'post',
            url: ApiUrl + "/index.php?act=member_index",
            //url: ApiUrl + "/index.php?act=member_index",
            data: { key: key },
            dataType: 'json',

            success: function(result) {
                console.log(result)
                checkLogin(result.login);

                // var html = '<div class="member-info">' +
                //     '<div class="user-avatar"> <img src="' + result.datas.member_info.avatar + '"/> </div>' +
                //     '<div class="user-name"> <span>' + result.datas.member_info.user_name + '</span> </div>' +
                //     '</div>' +
                //     '<div class="member-collect"><span><a href="favorites.html"><em>' + result.datas.member_info.favorites_goods + '</em>' +
                //     '<p>商品收藏</p>' +
                //     '</a> </span><span><a href="favorites_store.html"><em>' + result.datas.member_info.favorites_store + '</em>' +
                //     '<p>店铺收藏</p>' +
                //     '</a> </span><span><a href="views_list.html"><i class="goods-browse"></i>' +
                //     '<p>我的足迹</p>' +
                //     '</a> </span></div>';
                // $(".member-top").html(html);

                // 会员图标
                var html = '<div class="member-info">' +
                    '<div class="user-avatar"> <img src="' + result.datas.member_info.avatar + '"/> </div>' +
                    '<div class="user-name"> <span>' + result.datas.member_info.user_name + '</span> </div>' +
                    '<div class="user-name-in"> <span>级别：' + result.datas.member_info.member_level + '</span> </div>' +
                    '</div>';
                $(".member-top").html(html);

                // 我的订单
                var html = '';
                if (result.datas.member_info.order_nopay_count > 0) {
                    html += '<li><a href="order_list.html?data-state=state_new"><i class="cc-01"></i><p>待付款</p><sup>'+result.datas.member_info.order_nopay_count+'</sup></a></li>';
                } else {
                    html += '<li><a href="order_list.html?data-state=state_new"><i class="cc-01"></i><p>待付款</p></a></li>';
                }

                if (result.datas.member_info.order_noreceipt_count > 0) {
                    html += '<li><a href="order_list.html?data-state=state_pay"><i class="cc-03"></i><p>待发货</p><sup>'+result.datas.member_info.order_noreceipt_count+'</sup></a></li>';
                } else {
                    html += '<li><a href="order_list.html?data-state=state_pay"><i class="cc-03"></i><p>待发货</p></a></li>';
                }

                if (result.datas.member_info.order_notakes_count > 0) {
                    html += '<li><a href="order_list.html?data-state=state_send"><i class="cc-04"></i><p>待收货</p><sup>'+result.datas.member_info.order_notakes_count+'</sup></a></li>';
                } else {
                    html += '<li><a href="order_list.html?data-state=state_send"><i class="cc-04"></i><p>待收货</p></a></li>';
                }
                if (result.datas.member_info.order_noeval_count > 0) {
                    html += '<li><a href="order_list.html?data-state=state_notakes"><i class="cc-05"></i><p>待评价</p><sup>'+result.datas.member_info.order_noeval_count+'</sup></a></li>';
                } else {
                    html += '<li><a href="order_list.html?data-state=state_notakes"><i class="cc-05"></i><p>待评价</p></a></li>';
                }
                
                $("#order_ul").html(html);

                // 财产中心
                var html = '<li><a href="gold_bean.html"><i class="cc-06"></i><p>余额</p></a></li>' +
                    '<li><a href="my_team.html"><i class="cc-10"></i><p>我的团队</p></a></li>' +
                    '<li><a href="gold_bean1.html"><i class="cc-14"></i><p>股权兑换</p></a></li>' +
                    '<li><a href="'+WapSiteUrl+'/jquerytree/index1.html?token='+key+'"><i class="cc-11"></i><p>推荐图</p></a></li>';
                $('#property_ul').html(html);

                // 会员中心
                // var html = '<li><a href="direct_invite.html"><i class="cc-10"></i><p>我的推广</p></a></li>' +
                //     '<li><a href="'+WapSiteUrl+'/jquerytree/index.html"><i class="cc-11"></i><p>来源图</p></a></li>' +
                //     '<li><a href="access_tree.html"><i class="cc-12"></i><p>资源图</p></a></li>' +
                //     '<li><a href="silver_bean.html"><i class="cc-13"></i><p>银豆</p></a></li>';
                // $('#family_ul').html(html);

                return false;
            }
        });
    } else {
        // var html = '<div class="member-info">' +
        //     '<a href="login.html" class="default-avatar" style="display:block;"></a>' +
        //     '<a href="login.html" class="to-login">点击登录</a>' +
        //     '</div>' +
        //     '<div class="member-collect"><span><a href="login.html"><i class="favorite-goods"></i>' +
        //     '<p>商品收藏</p>' +
        //     '</a> </span><span><a href="login.html"><i class="favorite-store"></i>' +
        //     '<p>店铺收藏</p>' +
        //     '</a> </span><span><a href="login.html"><i class="goods-browse"></i>' +
        //     '<p>我的足迹</p>' +
        //     '</a> </span></div>';
        // $(".member-top").html(html);

        // 会员图标
        var html = '<div class="member-info">' +
            '<a href="login.html" class="default-avatar" style="display:block;"></a>' +
            '<a href="login.html" class="to-login">点击登录</a>' +
            '</div>';
        $(".member-top").html(html);

        // 我的订单
        var html = '<li><a href="order_list.html?data-state=state_new"><i class="cc-01"></i><p>待付款</p></a></li>' +
            '<li><a href="order_list.html?data-state=state_pay"><i class="cc-03"></i><p>待发货</p></a></li>' +
            '<li><a href="order_list.html?data-state=state_send"><i class="cc-04"></i><p>待收货</p></a></li>' +
            '<li><a href="order_list.html?data-state=state_notakes"><i class="cc-05"></i><p>待评价</p></a></li>';
        $("#order_ul").html(html);

        // 财产中心
        var html = '<li><a href="gold_bean.html"><i class="cc-06"></i><p>金豆</p></a></li>' +
            '<li><a href="equity.html"><i class="cc-07"></i><p>金券</p></a></li>' +
            '<li><a href="float_dividend.html"><i class="cc-08"></i><p>浮动分红</p></a></li>' +
            '<li><a href="voucher_list.html"><i class="cc-09"></i><p>消费额</p></a></li>';
        $('#property_ul').html(html);

        // 会员中心
        var html = '<li><a href="direct_invite.html"><i class="cc-10"></i><p>我的推广</p></a></li>' +
            '<li><a href="'+WapSiteUrl+'/jquerytree/index.html"><i class="cc-11"></i><p>来源图</p></a></li>' +
            '<li><a href="access_tree.html"><i class="cc-12"></i><p>资源图</p></a></li>' +
            '<li><a href="silver_bean.html"><i class="cc-13"></i><p>银豆</p></a></li>';
        $('#family_ul').html(html);


        return false;
    }

    //滚动header固定到顶部
    $.scrollTransparent();

    // 服务中心申请
    $('#service').click(function() {
        $.ajax({
            type: 'post',
            url: ApiUrl + '/index.php?act=member_service&op=serviceCheck',
            data: { key: key },
            dataType: 'json',

            success: function(result) {
                checkLogin(result.login);
                if (result.datas.service == '0') {
                    var r = confirm(result.datas.msg);
                    if (r == true) {
                        $.ajax({
                            type: 'post',
                            url: ApiUrl + '/index.php?act=member_service&op=serviceApply',
                            data: { key: key },
                            dataType: 'json',

                            success: function(re) {
                                checkLogin(re.login);
                                alert(re.datas.msg);
                            }
                        });
                    }
                } else if (result.datas.service == '1') {
                    alert(result.datas.msg);
                } else if (result.datas.service == '2') {
                    alert(result.datas.msg);
                }
                // console.log(result);
                // return false;
            }
        });
        // if (!key) {
        //     location.href = WapSiteUrl + '/tmpl/member_system/login.html';
        // }
        // var r = confirm("确定申请服务中心");
        // if (r == true) {
        //     alert("You pressed OK!");
        // } else {
        //     alert("You pressed Cancel!");
        // }

    });
});