<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>会员系统</title>
    <link href="<?php echo MEMBER_SYSTEM_TEMPLATES_URL ?>/css/base.css" rel="stylesheet" type="text/css">
    <link href="<?php echo MEMBER_SYSTEM_TEMPLATES_URL ?>/css/seller_center.css" rel="stylesheet" type="text/css">
    <link href="<?php echo MEMBER_SYSTEM_TEMPLATES_URL ?>/css/familytree.css" rel="stylesheet" type="text/css">
    <link href="<?php echo MEMBER_SYSTEM_RESOURCE_SITE_URL; ?>/font/font-awesome/css/font-awesome.min.css" rel="stylesheet" />
    <!--[if IE 7]>
        <link rel="stylesheet" href="<?php echo SHOP_RESOURCE_SITE_URL; ?>/font/font-awesome/css/font-awesome-ie7.min.css">
    <![endif]-->
    <script type="text/javascript" src="<?php echo RESOURCE_SITE_URL; ?>/js/jquery.js"></script>
    <script type="text/javascript" src="<?php echo MEMBER_SYSTEM_RESOURCE_SITE_URL; ?>/js/seller.js"></script>
    <script type="text/javascript" src="<?php echo RESOURCE_SITE_URL; ?>/js/waypoints.js"></script>
    <script type="text/javascript" src="<?php echo RESOURCE_SITE_URL; ?>/js/jquery-ui/jquery.ui.js"></script>
    <script type="text/javascript" src="<?php echo RESOURCE_SITE_URL; ?>/js/jquery.validation.min.js"></script>
    <script type="text/javascript" src="<?php echo RESOURCE_SITE_URL; ?>/js/common.js"></script>
    <script type="text/javascript" src="<?php echo RESOURCE_SITE_URL; ?>/js/member.js" charset="utf-8"></script>
    <script type="text/javascript" src="<?php echo RESOURCE_SITE_URL; ?>/js/dialog/dialog.js" id="dialog_js" charset="utf-8"></script>
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="<?php echo RESOURCE_SITE_URL; ?>/js/html5shiv.js"></script>
      <script src="<?php echo RESOURCE_SITE_URL; ?>/js/respond.min.js"></script>
<![endif]-->
</head>

<body>
    <script type="text/javascript" src="<?php echo RESOURCE_SITE_URL; ?>/js/ToolTip.js"></script>
    <div id="append_parent"></div>
    <div id="ajaxwaitid"></div>
    <header class="ncsc-head-layout w">
        <div class="wrapper">
            <div class="ncsc-admin">
                <dl class="ncsc-admin-info">
                    <dt class="admin-avatar"><img src="<?php echo getMemberAvatarForID($_SESSION['member_id']); ?>" width="32" class="pngFix" alt="" /></dt>
                    <dd class="admin-permission">当前用户</dd>
                    <dd class="admin-name">
                        <?php echo $_SESSION['member_name']; ?>
                    </dd>
                </dl>
                <div class="ncsc-admin-function">
                    <a href="<?php echo SHOP_SITE_URL; ?>" title="前往商城"><i class="icon-home"></i></a><a href="<?php echo urlMemberSystem('member_info', 'member_account'); ?>" title="修改密码"><i class="icon-wrench"></i></a><a href="<?php echo urlMemberSystem('login', 'logout'); ?>" title="安全退出"><i class="icon-signout"></i></a></div>
            </div>
            <div class="center-logo"> <a href="<?php echo MEMBER_SYSTEM_SITE_URL; ?>"><img src="<?php echo UPLOAD_SITE_URL . '/' . MEMBER_ATTACH_COMMON . DS . C('member_system_logo'); ?>" class="pngFix" alt=""/></a>
                <h1>会员系统中心</h1>
            </div>
            <div class="index-search-container">
                <div class="index-sitemap"><a href="javascript:void(0);">导航管理 <i class="icon-angle-down"></i></a>
                    <div class="sitemap-menu-arrow"></div>
                    <div class="sitemap-menu">
                        <div class="title-bar">
                            <h2> <i class="icon-sitemap"></i>管理导航<em>小提示：添加您经常使用的功能到首页侧边栏，方便操作。</em> </h2>
                            <span id="closeSitemap" class="close">X</span></div>
                        <div id="quicklink_list" class="content">
                            <?php if (!empty($output['menu']) && is_array($output['menu'])) {?>
                            <?php foreach ($output['menu'] as $key => $menu_value) {?>
                            <dl <?php echo ($key == 'promotion' ? 'class="double"' : ''); ?>>
                                <dt>
                                    <?php echo $menu_value['name']; ?>
                                </dt>
                                <?php if (!empty($menu_value['child']) && is_array($menu_value['child'])) {?>
                                <?php foreach ($menu_value['child'] as $submenu_value) {?>
                                <dd <?php if (!empty($output['seller_quicklink'])) {echo in_array($submenu_value['act'], $output['seller_quicklink']) ? 'class="selected"' : '';}?>><i nctype="btn_add_quicklink" data-quicklink-act="<?php echo $submenu_value['act']; ?>" class="icon-check" title="添加为常用功能菜单"></i>
                                    <a href="index.php?act=<?php echo $submenu_value['act']; ?>&op=<?php echo $submenu_value['op']; ?>">
                                        <?php echo $submenu_value['name']; ?> </a>
                                </dd>
                                <?php }?>
                                <?php }?>
                            </dl>
                            <?php }?>
                            <?php }?>
                        </div>
                    </div>
                </div>
                <div class="search-bar">
                    <form method="get" target="_blank">
                        <input type="hidden" name="act" value="search">
                        <input type="text" nctype="search_text" name="keyword" placeholder="商城商品搜索" class="search-input-text">
                        <input type="submit" nctype="search_submit" class="search-input-btn pngFix" value="">
                    </form>
                </div>
            </div>
            <nav class="ncsc-nav">
                <dl class="<?php echo $output['current_menu']['model'] == 'index' ? 'current' : ''; ?>">
                    <dt><a href="index.php?act=member_info&op=index">首页</a></dt>
                    <dd class="arrow"></dd>
                </dl>
                <?php if (!empty($output['menu']) && is_array($output['menu'])) {?>
                <?php foreach ($output['menu'] as $key => $menu_value) {?>
                <dl class="<?php echo $output['current_menu']['model'] == $key ? 'current' : ''; ?>">
                    <dt>
                        <a href="index.php?act=<?php echo $menu_value['child'][key($menu_value['child'])]['act']; ?>&op=<?php echo $menu_value['child'][key($menu_value['child'])]['op']; ?>">
                            <?php echo $menu_value['name']; ?>
                        </a>
                    </dt>
                    <dd>
                        <ul>
                            <?php if (!empty($menu_value['child']) && is_array($menu_value['child'])) {?>
                            <?php foreach ($menu_value['child'] as $submenu_value) {?>
                            <li>
                                <a href="index.php?act=<?php echo $submenu_value['act']; ?>&op=<?php echo $submenu_value['op']; ?>">
                                    <?php echo $submenu_value['name']; ?> </a>
                            </li>
                            <?php }?>
                            <?php }?>
                        </ul>
                    </dd>
                    <dd class="arrow"></dd>
                </dl>
                <?php }?>
                <?php }?>
            </nav>