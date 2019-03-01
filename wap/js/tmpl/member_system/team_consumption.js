$(function() {
    var key = getCookie('key');
    console.log(key)
    if(key){
        $.ajax({
            type: 'post',
            url: ApiUrl + "/index.php?act=member_refund&op=member_team_achievement",
            //url: "http://192.168.0.180:8080/mobile/index.php?act=member_family&op=member_team",
            data: { key: key },
            dataType: 'json',
            success: function(result) {
                console.log(result)
                var mun = 0;
                for(var i=0; i<result.data.achievement_list.length; i++){
                    mun+=parseFloat(result.data.achievement_list[i].order_amount);
                }
                console.log(result.data.total_achievement)
                $("#title_num").append(result.data.total_achievement);
                var html = template.render('tpl', result.data);
                $("#wp").append(html);
            }
        });
	}

});