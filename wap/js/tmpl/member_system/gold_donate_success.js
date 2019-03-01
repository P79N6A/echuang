$(function() {
	var key = getCookie('key');
	if (!key) {
		location.href = WapSiteUrl + '/tmpl/member_system/login.html';
	}
})