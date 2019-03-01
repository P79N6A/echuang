$(function() {
    //  返利
    var uid = window.location.href.split("#V5");
    var fragment = uid[1];
    if (fragment) {
        if (fragment.indexOf("V5") == 0) {
            addCookie("uid", "0");
        } else {
            addCookie("uid", fragment);
        }
    }
    var headerClone = $('#header').clone();
    $(window).scroll(function() {
        if ($(window).scrollTop() <= $('#main-container1').height()) {
            headerClone = $('#header').clone();
            $('#header').remove();
            headerClone.addClass('transparent').removeClass('');
            headerClone.prependTo('.nctouch-home-top');
        } else {
            headerClone = $('#header').clone();
            $('#header').remove();
            headerClone.addClass('').removeClass('transparent');
            headerClone.prependTo('body');
        }
    });

    $.ajax({
        url: BaseUrl+"/api/index.php?act=index",
        type: 'get',
        dataType: 'json',
        success: function(result) {
            var data = result.data;
            var html = '';
            var carNav = "";
            console.log(result)
            $.each(data, function(k, v) {
                $.each(v, function(kk, vv) {
                    switch (kk) {
                        case 'adv_list':
                        case 'nav1':
                            break;
                        case 'home3':
                            $.each(vv.item, function(k3, v3) {
                                vv.item[k3].url = buildUrl(v3.type, v3.data);
                            });

                            break;

                        case 'home1':
                            vv.url = buildUrl(vv.type, vv.data);
                            break;

                        case 'home2':
                        case 'home4':
                            vv.square_url = buildUrl(vv.square_type, vv.square_data);
                            vv.rectangle1_url = buildUrl(vv.rectangle1_type, vv.rectangle1_data);
                            vv.rectangle2_url = buildUrl(vv.rectangle2_type, vv.rectangle2_data);
                            break;
                        case 'home5':
                            vv.square_url = buildUrl(vv.square_type, vv.square_data);
                            vv.rectangle1_url = buildUrl(vv.rectangle1_type, vv.rectangle1_data);
                            vv.rectangle2_url = buildUrl(vv.rectangle2_type, vv.rectangle2_data);
                            vv.rectangle3_url = buildUrl(vv.rectangle3_type, vv.rectangle3_data);
                            break;
                    }
                    if (k == 0) {
                        $("#main-container1").html(template.render(kk, vv));
                        console.log(vv)
                    } else if(k == 1){
                        $("#so_nav").html(template.render(kk, vv));
                        console.log(vv)
                    }else{
                        html += template.render(kk, vv);
                    }
                    return false;
                });
            });

            $("#main-container2").html(html);
            // $(".nctouch-home-nav ul li").each(function(){
            //     console.log("11")
            // })
            console.log(data[1].nav1.square_title)
            $.each(data[1].nav1.square_title,function(i,item){   //遍历ul中的li
                // carNewsList += "<li  style='font-size:14px;color:#5d5d5d;'>"+item.columnName+"</li>";
                //"+data[1].nav1.square_data[i]+"
                if(!item==""){
                    console.log(item);
                    carNav += "<li>" + "<a href='index_nav.html?special_id="+data[1].nav1.square_data[i]+"'>" + "<span><i style='background-image: url("+data[1].nav1.square_image[i]+")'></i></span>" + "<p>"+data[1].nav1.square_title[i]+"</p>" + "</a>" + "</li>";
                }

            });
            //console.log(carNav)
            $(".nctouch-home-nav ul").html(carNav)
            $('.adv_list').each(function() {
                if ($(this).find('.item').length < 2) {
                    return;
                }

                Swipe(this, {
                    startSlide: 2,
                    speed: 400,
                    auto: 3000,
                    continuous: true,
                    disableScroll: false,
                    stopPropagation: false,
                    callback: function(index, elem) {},
                    transitionEnd: function(index, elem) {}
                });
            });

            $('.xianshi-list').each(function() {
                if ($(this).find('.item').length < 2) {
                    return;
                }

                Swipe(this, {
                    startSlide: 2,
                    speed: 400,
                    auto: 3000,
                    continuous: true,
                    disableScroll: false,
                    stopPropagation: false,
                    callback: function(index, elem) {},
                    transitionEnd: function(index, elem) {}
                });
            });

        }
    });


    $.ajax({
        url: ApiUrl + "/index.php?act=index&op=getgg",
        type: 'get',
        data: { ac_id: 1 },
        jsonp: 'callback',
        dataType: 'jsonp',
        success: function(result) {
            var data = result.datas;
            data.WapSiteUrl = WapSiteUrl;
            var html = template.render('getgg_tpl', data);
            $("#getgg").html(html);
        }
    });
    $.getJSON(ApiUrl + '/index.php?act=brand&op=recommend_list', function(result){
        if(!result) {
            result = [];
            result.datas = [];
        }
        console.log(result);
        $('.loading').remove();
        // curpage++;
        var html = template.render('home_body', result);

        $("#product_list .goods-secrch-list").append(html);
        // hasmore = result.hasmore;
    });
    // $.getJSON(ApiUrl + '/index.php?act=brand&op=recommend_list', function(result) {
    //     var data = result.datas;
    //     data.WapSiteUrl = WapSiteUrl;
    //     var html = template.render('brand-one', data);
    //     $("#categroy-rgt").html(html);
    //     $('.pre-loading').hide();
    //     new IScroll('#categroy-rgt', { mouseWheel: true, click: true });
    // });
    // http://192.168.0.55/mobile/index.php?act=brand&op=recommend_list

});

function takeCount() {
    setTimeout("takeCount()", 1E3);
    $(".time-remain").each(function() {
        var b = $(this),
            a = b.attr("count_down");
        if (0 < a) {
            var a = parseInt(a) - 1,
                e = Math.floor(a / 86400),
                c = Math.floor(a / 3600) % 24,
                g = Math.floor(a / 60) % 60,
                f = Math.floor(a / 1) % 60;
            0 > e && (e = 0);
            0 > c && (c = 0);
            0 > g && (g = 0);
            0 > f && (f = 0);
            b.find("[time_id='d']").html(e);
            b.find("[time_id='h']").html(c);
            b.find("[time_id='m']").html(g);
            b.find("[time_id='s']").html(f);
            b.attr("count_down", a)
        }
    })
}

$(function() {
    setTimeout("takeCount()", 1E3);
    $('.xianshi-list').each(function() {
        if ($(this).find('.item').length < 2) {
            $(".xianshi").jfocus({
                time: 8E3
            });
        }
    });
});

//首页公告滚动
var rollText_k = 5; //循环公告总数
var rollText_i = 1; //循环公告默认值
rollText_tt = setInterval("rollText(1)", 8000);

function rollText(a) {
    clearInterval(rollText_tt);
    rollText_tt = setInterval("rollText(1)", 8000);
    rollText_i += a;
    if (rollText_i > rollText_k) {
        rollText_i = 1;
    }
    if (rollText_i == 0) {
        rollText_i = rollText_k;
    }
    // for (var j = 1; j <= rollText_k; j++) {
    //     document.getElementById("rollTextMenu" + j).style.display = "none";
    // }
    // document.getElementById("rollTextMenu" + rollText_i).style.display = "block";
}