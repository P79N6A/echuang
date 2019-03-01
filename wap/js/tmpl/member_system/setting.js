$(function() {
	var key = getCookie('key');
	if (!key) {
		window.location.href = WapSiteUrl + '/tmpl/member_system/login.html';
		return;
	}

	$('#logout').click(function() {
		delCookie('username');
        delCookie('userid');
        delCookie('key');
        
        window.location.href = WapSiteUrl + '/tmpl/member_system/center.html';
        return;
	});
});