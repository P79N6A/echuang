<?php
/**
 * 会员系统入口
 *
 */
define('APP_ID', 'member_system');
define('BASE_PATH', str_replace('\\', '/', dirname(__FILE__)));

require __DIR__ . '/../33hao.php';

define('APP_SITE_URL', MEMBER_SYSTEM_SITE_URL);
define('TPL_NAME', TPL_MEMBER_NAME);
define('MEMBER_SYSTEM_TEMPLATES_URL', MEMBER_SYSTEM_SITE_URL . '/templates/' . TPL_NAME);
define('MEMBER_SYSTEM_RESOURCE_SITE_URL', MEMBER_SYSTEM_SITE_URL . '/resource');
require_once BASE_PATH . '/function/function.php';

if (!@include (BASE_PATH . '/control/control.php')) {
	exit('control.php isn\'t exists!');
}
Base::run();