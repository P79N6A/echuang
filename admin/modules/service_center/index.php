<?php
/**
 * 服务中心板块初始化文件
 *
 */

define('BASE_PATH', str_replace('\\','/',dirname(dirname(dirname(__FILE__)))));
define('MODULES_BASE_PATH', str_replace('\\','/',dirname(__FILE__)));
require __DIR__ . '/../../../33hao.php';

define('APP_SITE_URL', ADMIN_SITE_URL.'/modules/member_system');
define('TPL_NAME', TPL_ADMIN_NAME);
define('ADMIN_TEMPLATES_URL', ADMIN_SITE_URL.'/templates/'.TPL_NAME);
define('ADMIN_RESOURCE_URL', ADMIN_SITE_URL.'/resource');

define('BASE_TPL_PATH', MODULES_BASE_PATH.'/templates/'.TPL_NAME);
define('MODULE_NAME', 'member_center');
if (!@include(BASE_PATH.'/control/control.php')) exit('control.php isn\'t exists!');
$system='service_center';

Base::runadmin($system);