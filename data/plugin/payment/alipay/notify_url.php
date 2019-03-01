<?php
/**
 * 支付宝服务器异步通知页面
 *
 */
error_reporting(7);
$_GET['act'] = 'callback';
$_GET['op'] = 'asynNotify';
$_GET['payment_code'] = 'alipay';
require_once dirname(__FILE__) . '/../../../../callback/index.php';
