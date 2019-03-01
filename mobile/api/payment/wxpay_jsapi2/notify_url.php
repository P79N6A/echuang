<?php
/**
 * 微信支付通知地址
 *
 *  (c) ()
 * @license
 * @link
 * @since
 */

$_GET['act'] = 'payment';
$_GET['op'] = 'notify';
$_GET['payment_code'] = 'wxpay_jsapi';

require __DIR__ . '/../../../index.php';
