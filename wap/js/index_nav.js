$(function() {
    // 返利
    // var uid = window.location.href.split("#V5");
    // var fragment = uid[1];
    console.log(getQueryString("special_id"))
    var Special_id = getQueryString("special_id");
    $.ajax({
        type: 'post',
        // url: ApiUrl + "/index.php?act=index&op=newsBulletin",  "<?php echo BASE_SITE_URL; ?>
        url: "http://shop.divona.xin/api/index.php?act=index&special_id="+Special_id,
        data: { },
        dataType: 'json',
        success: function(result) {
            var data = result.data;
            var html = '';
            console.log(result.data[0])
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
                    html += template.render(kk, vv);
                    return false;
                });
            });
            $("#main-container2").html(html);
        }
    });
})

function getQueryString(name) {
    var reg = new RegExp('(^|&)' + name + '=([^&]*)(&|$)', 'i');
    var r = window.location.search.substr(1).match(reg);
    if (r != null) {
        return unescape(r[2]);
    }
    return null;
}