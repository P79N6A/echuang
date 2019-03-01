$(function() {
    if (getQueryString('key') != '') {
        var key = getQueryString('key');
        var username = getQueryString('username');
        addCookie('key', key);
        addCookie('username', username);
    } else {
        var key = getCookie('key');
    }

    var redirect_uri = getCookie('redirect_uri');
    if (redirect_uri && getQueryString('info') == 'hao') {
        window.location.href = WapSiteUrl + redirect_uri;
    }

    if (key) {
        $.ajax({
            type: 'post',
            url: ApiUrl + "/index.php?act=member_index",
            data: { key: key },
            dataType: 'json',

            success: function(result) {
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
                // //渲染页面

                // $(".member-top").html(html);
                var html = '<div class="member-info">' +
                    '<div class="user-avatar"> <img src="' + result.datas.member_info.avatar + '"/> </div>' +
                    '<div class="user-name"> <span>' + result.datas.member_info.user_name + '</span> </div>' +
                    '</div>';
                $(".member-top").html(html);

                var html = '<li><a href="order_list.html"><i class="cc-01"></i><p>待付款</p><sup>1</sup></a></li>' +
                    '<li><a href="order_list.html"><i class="cc-02"></i><p>待收货</p></a></li>' +
                    '<li><a href="order_list.html"><i class="cc-03"></i><p>待自提</p></a></li>' +
                    '<li><a href="order_list.html"><i class="cc-04"></i><p>待评价</p></a></li>';
                // 渲染页面
                $("#order_ul").html(html);

                var html = '<li><a href="gold_bean.html"><i class="cc-06"></i><p>金豆</p></a></li>' +
                    '<li><a href="equity.html"><i class="cc-07"></i><p>金券</p></a></li>' +
                    '<li><a href="float_dividend.html"><i class="cc-07"></i><p>浮动分红</p></a></li>' +
                    '<li><a href="expenditure.html"><i class="cc-08"></i><p>消费额</p></a></li>';
                $('#property_ul').html(html);

                var html = '<li><a href="direct_invite.html"><i class="cc-06"></i><p>我的推广</p></a></li>' +
                    '<li><a href="rechargecardlog_list.html"><i class="cc-07"></i><p>来源图</p></a></li>' +
                    '<li><a href="rechargecardlog_list.html"><i class="cc-07"></i><p>资源图</p></a></li>' +
                    '<li><a href="silver_bean.html"><i class="cc-10"></i><p>银豆</p></a></li>';
                $('#family_ul').html(html);

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
        // //渲染页面
        // $(".member-top").html(html);
        var html = '<div class="member-info">' +
            '<a href="login.html" class="default-avatar" style="display:block;"></a>' +
            '<a href="login.html" class="to-login">点击登录</a>' +
            '</div>';
        // 渲染页面
        $(".member-top").html(html);
        var html = '<li><a href="order_list.html"><i class="cc-01"></i><p>待付款</p></a></li>' +
            '<li><a href="order_list.html"><i class="cc-02"></i><p>待收货</p></a></li>' +
            '<li><a href="order_list.html"><i class="cc-03"></i><p>待自提</p></a></li>' +
            '<li><a href="order_list.html"><i class="cc-04"></i><p>待评价</p></a></li>';


        //渲染页面
        $("#order_ul").html(html);

        var html = '<li><a href="gold_bean.html"><i class="cc-06"></i><p>金豆</p></a></li>' +
            '<li><a href="rechargecardlog_list.html"><i class="cc-07"></i><p>金券</p></a></li>' +
            '<li><a href="float_dividend.html"><i class="cc-07"></i><p>浮动分红</p></a></li>' +
            '<li><a href="voucher_list.html"><i class="cc-08"></i><p>消费额</p></a></li>';
        $('#property_ul').html(html);

        var html = '<li><a href="direct_invite.html"><i class="cc-06"></i><p>我的推广</p></a></li>' +
            '<li><a href="rechargecardlog_list.html"><i class="cc-07"></i><p>来源图</p></a></li>' +
            '<li><a href="rechargecardlog_list.html"><i class="cc-07"></i><p>资源图</p></a></li>' +
            '<li><a href="silver_bean.html"><i class="cc-10"></i><p>银豆</p></a></li>';
        $('#family_ul').html(html);

        
        return false;
    }

    //滚动header固定到顶部
    $.scrollTransparent();
});