function getQueryString(name) {
    var reg = new RegExp('(^|&)' + name + '=([^&]*)(&|$)', 'i');
    var r = window.location.search.substr(1).match(reg);
    if (r != null) {
        return unescape(r[2]);
    }
    return null;
}
$(function() {
    console.log(getQueryString("level"))
    var Level = getQueryString("level");
    var key = getCookie('key');
    console.log(key)
    if(key){
        $.ajax({
            type: 'post',
            url: ApiUrl + "/index.php?act=member_family&op=member_in_direct_list&level="+Level,
            //url: "http://192.168.0.180:8080/mobile/index.php?act=member_family&op=member_team",
            data: { key: key,level:Level},
            dataType: 'json',
            success: function(result) {
                console.log(result.data)
                var html = template.render('tpl', result.data);
                $("#wwp").append(html);
            }
        });
	}

});