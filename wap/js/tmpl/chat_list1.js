$(function() {
    var key = getCookie('key');
    if (!key) {
        location.href = 'member_system/login.html';
    }
    var goods_id = getQueryString("goods_id")
    console.log(goods_id)
    if(key) {
        $.ajax({
            type: 'post',
            url: ApiUrl + '/index.php?act=goods&op=consulting_list&key='+key+'&goods_id=' + goods_id,
            data: {},
            dataType: 'json',
            success: function (result) {
                var html = "";
                for(var i=0; i<result.data.length; i++){
                    if(result.data[i].content_type=="consult"){
                        html+="<div class='right'><div class='right_li'><p>" + result.data[i].consult_content + "</p></div></div>"
                    }else if(result.data[i].content_type=="reply"){
                        html+="<div class='left'><div class='left_li'><p>" + result.data[i].consult_content + "</p></div></div>"
                    }

                }
                console.log(html)
                $(".chat_d").html(html)
                console.log(result)
            }
        });
        $("#btn").click(function () {
            console.log($("#t_text").val())
            var val = $("#t_text").val();
            html = "<div class='right'><div class='right_li'><p>" + val + "</p></div></div>"
            $(".chat").append(html)
            $.ajax({
                type: 'post',
                url: ApiUrl + '/index.php?act=goods&op=save_consult&key=' + key,
                data: {goods_id: goods_id, goods_content: val},
                dataType: 'json',
                success: function (result) {
                    console.log(result)
                }
            });
            $("#t_text").val("")
        })
    }
});
function getQueryString(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
    var r = window.location.search.substr(1).match(reg);
    if (r != null) return unescape(r[2]); return null;
}