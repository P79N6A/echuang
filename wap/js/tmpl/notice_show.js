//
$(function(){
	var article_id = getQueryString('notice_id');
	if (article_id=='') {
    	window.location.href = WapSiteUrl + '/index.html';
    	return;
	}
	else {
		$.ajax({
			url:ApiUrl+"/index.php?act=notice&op=notice_show",
			type:'get',
			data:{notice_id:article_id},
			jsonp:'callback',
			dataType:'jsonp',
			success:function(result){
				var data = result.datas;
				var html = template.render('article', data);
				$("#article-content").html(html);
				$("#art_title").html(data.notice_title);
				$(".article-content").html(data.notice_content);
			}
		});
	}	
});