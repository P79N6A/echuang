function getQueryString(name) {
    var reg = new RegExp('(^|&)' + name + '=([^&]*)(&|$)', 'i');
    var r = window.location.search.substr(1).match(reg);
    if (r != null) {
        return unescape(r[2]);
    }
    return null;
}
$(function() {
   // var key = getCookie('key');
    //console.log(key)

    $.ajax({
        type: 'post',
        // url: ApiUrl + "/index.php?act=index&op=newsBulletin",
        url: "http://shop.divona.xin/mobile/index.php?act=index&op=newsBulletin",
        data: { },
        dataType: 'json',
        success: function(result) {
            console.log(result)
            var html = template.render('adv_list', result.data);
            var html1 = template.render('adv_li', result.data);
            $("#adv_na").append(html);
            $("#adv_na1").append(html1);
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
        }
    });

});