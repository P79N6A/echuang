$(function() {
    var key = getCookie('key');
    console.log(key)
    if(key){
        $.ajax({
            type: 'post',
            url: ApiUrl + "/index.php?act=member_refund&op=member_diff_reward",
            //url: "http://192.168.0.180:8080/mobile/index.php?act=member_family&op=member_team",
            data: { key: key },
            dataType: 'json',
            success: function(result) {
                console.log(result)
                var mun = 0;
                for(var i=0; i<result.data.profit_reward_list.length; i++){
                    mun+=parseFloat(result.data.profit_reward_list[i].order_amount);
                }
                console.log(mun)
                var numhtml = +mun;
                $("#title_num").append(numhtml);
                var html = template.render('tpl', result.data);
                $("#wp").append(html);
            }
        });
	}

});