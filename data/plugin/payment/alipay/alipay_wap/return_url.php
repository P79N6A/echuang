<?php
/**
 * 支付宝服务器同步通知
 *
 */
error_reporting(7);
$_GET['act'] = 'callback';
$_GET['op'] = 'syncCallback';
$_GET['payment_code'] = 'alipay_wap';
require_once dirname(__FILE__) . '/../../../../../mobile/index.php';