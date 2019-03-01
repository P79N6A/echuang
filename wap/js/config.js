var BaseUrl = "http://" + window.location.host;
var SiteUrl = "http://" + window.location.host + "/shop";
// var ApiUrl = "http://121.196.201.195";
var ApiUrl = "http://" + window.location.host + "/mobile";
// var ApiUrl = "http://" + document.domain + "/mobile";
//121.196.201.195
var pagesize = 10;
var WapSiteUrl = "http://" + window.location.host + "/wap";
var IOSSiteUrl = "http://" + window.location.host + "/app.ipa";
var AndroidSiteUrl = "http://" + window.location.host + "/app.apk";

// var BaseUrl = "<?php echo DATE?>";
// var SiteUrl = "http://192.168.0.99:8080/shop";
// var ApiUrl = "http://192.168.0.99:8080";
// //var ApiUrl = "http://" + window.location.host + "/mobile";
// // var ApiUrl = "http://" + document.domain + "/mobile";
// //121.196.201.195
// var pagesize = 10;
// var WapSiteUrl = "http://192.168.0.99:8080/wap";
// var IOSSiteUrl = "http://192.168.0.99:8080/app.ipa";
// var AndroidSiteUrl = "http://192.168.0.99:8080/app.apk";

// auto url detection
(function() {
    // var m = /^(https?:\/\/.+)\/wap/i.exec(location.href);
    var m = /^(https?:\/\/.+)\/wap/i.exec(location.href);
    if (m && m.length > 1) {
        SiteUrl = m[1] + '/shop';
        ApiUrl = m[1] + '/mobile';
        WapSiteUrl = m[1] + '/wap';
    }
})();