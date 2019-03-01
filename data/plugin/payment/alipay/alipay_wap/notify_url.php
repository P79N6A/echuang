<?php
/**
 * 支付宝异步回调
 *
 */

error_reporting(7);
$_GET['act'] = 'callback';
$_GET['op'] = 'asynNotify';
$_GET['payment_code'] = 'alipay_wap';
require_once dirname(__FILE__) . '/../../../../../callback/index.php';