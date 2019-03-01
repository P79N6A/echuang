$(function() {
    var key = getCookie('key');
    console.log(key)
    if(key){
        $.ajax({
            type: 'post',
            url: ApiUrl + "/index.php?act=member_family&op=direct_level_num",
            //url: "http://192.168.0.180:8080/mobile/index.php?act=member_family&op=member_team",
            data: { key: key },
            dataType: 'json',
            success: function(result) {
                console.log(result.data)
                var html = template.render('tpl', result.data);
                $("#wp").append(html);
            }
        });
	}

});