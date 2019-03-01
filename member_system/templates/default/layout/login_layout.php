<?php defined('In33hao') or exit('Access Invalid!');
$wapurl = WAP_SITE_URL;
	$agent = $_SERVER['HTTP_USER_AGENT'];
//	if(strpos($agent,"comFront") || strpos($agent,"iPhone") || strpos($agent,"MIDP-2.0") || strpos($agent,"Opera Mini") || strpos($agent,"UCWEB") || strpos($agent,"Android") || strpos($agent,"Windows CE") || strpos($agent,"SymbianOS")){
if(0){			global $config;
        if(!empty($config['wap_site_url'])){
            $url = $config['wap_site_url'];
            switch ($_GET['act']){
			case 'goods':
			  $url .= '/tmpl/product_detail.html?goods_id=' . $_GET['goods_id'];
			  break;
			case 'store_list':
			  $url .= '/shop.html';
			  break;
			case 'show_store':
			  $url .= '/tmpl/store.html?store_id=' . $_GET['store_id'];
			  break;
			}
        } else {
            header('Location:'.$wapurl.$_SERVER['QUERY_STRING']);
        }
        header('Location:' . $url);
        exit();	
	}
?>
<!doctype html>
<html lang="zh">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET;?>">
<title><?php echo $output['html_title'];?></title>
<meta name="keywords" content="<?php echo $output['seo_keywords']; ?>" />
<meta name="description" content="<?php echo $output['seo_description']; ?>" />
<meta name="author" content="33Hao">
<meta name="copyright" content="33Hao Inc. All Rights Reserved">
<?php echo html_entity_decode($output['setting_config']['qq_appcode'],ENT_QUOTES); ?><?php echo html_entity_decode($output['setting_config']['sina_appcode'],ENT_QUOTES); ?><?php echo html_entity_decode($output['setting_config']['share_qqzone_appcode'],ENT_QUOTES); ?><?php echo html_entity_decode($output['setting_config']['share_sinaweibo_appcode'],ENT_QUOTES); ?>
<style type="text/css">
body { _behavior: url(<?php echo MEMBER_SYSTEM_TEMPLATES_URL;
?>/css/csshover.htc);
}
</style>
<link href="<?php echo MEMBER_SYSTEM_TEMPLATES_URL;?>/css/base.css" rel="stylesheet" type="text/css">
<link href="<?php echo MEMBER_SYSTEM_TEMPLATES_URL;?>/css/home_header.css" rel="stylesheet" type="text/css">
<link href="<?php echo MEMBER_SYSTEM_TEMPLATES_URL;?>/css/home_login.css" rel="stylesheet" type="text/css">
<link href="<?php echo MEMBER_SYSTEM_RESOURCE_SITE_URL;?>/font/font-awesome/css/font-awesome.min.css" rel="stylesheet" />
<!--[if IE 7]>
  <link rel="stylesheet" href="<?php echo LOGIN_RESOURCE_SITE_URL;?>/font/font-awesome/css/font-awesome-ie7.min.css">
<![endif]-->
<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!--[if lt IE 9]>
      <script src="<?php echo RESOURCE_SITE_URL_HTTPS;?>/js/html5shiv.js"></script>
      <script src="<?php echo RESOURCE_SITE_URL_HTTPS;?>/js/respond.min.js"></script>
<![endif]-->
<script>
var COOKIE_PRE = '<?php echo COOKIE_PRE;?>';var _CHARSET = '<?php echo strtolower(CHARSET);?>';var SITEURL = '<?php echo SHOP_SITE_URL;?>';var SHOP_SITE_URL = '<?php echo SHOP_SITE_URL;?>';var RESOURCE_SITE_URL = '<?php echo RESOURCE_SITE_URL;?>';var RESOURCE_SITE_URL = '<?php echo RESOURCE_SITE_URL;?>';var SHOP_TEMPLATES_URL = '<?php echo SHOP_TEMPLATES_URL;?>';
</script>
<script src="<?php echo RESOURCE_SITE_URL_HTTPS;?>/js/jquery.js"></script>
<script src="<?php echo RESOURCE_SITE_URL_HTTPS;?>/js/jquery-ui/jquery.ui.js"></script>
<script src="<?php echo RESOURCE_SITE_URL_HTTPS;?>/js/common.js"></script>
<script src="<?php echo RESOURCE_SITE_URL_HTTPS;?>/js/jquery.validation.min.js"></script>
<script src="<?php echo RESOURCE_SITE_URL_HTTPS;?>/js/dialog/dialog.js" id="dialog_js"></script>
<script src="<?php echo MEMBER_SYSTEM_RESOURCE_SITE_URL?>/js/taglibs.js"></script>
<script src="<?php echo MEMBER_SYSTEM_RESOURCE_SITE_URL?>/js/tabulous.js"></script>
</head>
<body>
  
<!-- PublicHeadLayout End -->
<div id="append_parent"></div>
<div id="ajaxwaitid"></div>
<?php require_once($tpl_file);?>
<?php //require_once template('layout/footer_https');?>
</body>
</html>