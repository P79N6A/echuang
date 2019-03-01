$(function() {
    var myScroll;
    $("#header").on('click', '.header-inp', function() {
        location.href = WapSiteUrl + '/tmpl/search.html';
    });
    var dataso ;
    $.getJSON("http://shop.divona.xin/mobile/index.php?act=goods_class&op=getRootClass", function(result) {
        dataso = result.datas;
        console.log(dataso)
        dataso.WapSiteUrl = WapSiteUrl;
        var html = template.render('category-one', dataso);
        $("#categroy-cnt").html(html);
        var one = dataso.class_list[0].gc_id;
        var one_img = dataso.class_list[0].cn_adv1
        get_brand_recommend(one,one_img);
        myScroll = new IScroll('#categroy-cnt', { mouseWheel: true, click: true });
    });
    if(!dataso){
        console.log("暂时没出来")
    }else{
        console.log(dataso)
    }

    $('.pre-loading').hide();
    $('#categroy-cnt').on('click', '.category', function(index) {

        $('.pre-loading').show();
        $(this).parent().addClass('selected').siblings().removeClass("selected");
        var gc_id = $(this).attr('date-id');
        var gc_index = $(this).attr('date-index');
        $.getJSON(ApiUrl + '/index.php?act=goods_class&op=getClassList', { gc_id: gc_id }, function(result) {
            console.log(result)
            console.log(gc_id)
            var data = result.datas;
            data.sourl = dataso.class_list[gc_index].cn_adv1;
            data.WapSiteUrl = WapSiteUrl;
            console.log(data)
            var html = template.render('category-two', data);
            $("#categroy-rgt").html(html);
            $('.pre-loading').hide();
            // new IScroll('#categroy-rgt', { mouseWheel: true, click: true });
        });
        myScroll.scrollToElement(document.querySelector('.categroy-list li:nth-child(' + ($(this).parent().index() + 1) + ')'), 1000);
    });

    // $('#categroy-cnt').on('click', '.brand', function() {
    //     $('.pre-loading').show();
    //     get_brand_recommend();
    // });
});

function get_brand_recommend(one,one_img) {
    $('.category-item').removeClass('selected');
    $('.category-item').eq(0).addClass('selected');
    $('.pre-loading').show();
    $.getJSON(ApiUrl + '/index.php?act=goods_class&op=getClassList',{ gc_id: one }, function(result) {
        console.log(result)
        var data = result.datas;
        data.sourl = one_img;
        data.WapSiteUrl = WapSiteUrl;
        var html = template.render('category-two', data);
        $("#categroy-rgt").html(html);
        $('.pre-loading').hide();
        new IScroll('#categroy-rgt', { mouseWheel: true, click: true });
    });
}