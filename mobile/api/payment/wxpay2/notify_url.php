<?php
/**
 * 微信支付通知地址
 *
 *  (c) ()
 * @license
 * @link
 * @since
 */
$_GET['act']	= 'payment2';
$_GET['op']		= 'notify';
$_GET['payment_code'] = 'wxpay_redbag';
function writeLog($content){
    $log_path ='../../'.date('y-m-d') . '_log.txt';
    file_put_contents($log_path, $content . "\n", FILE_APPEND);
}
require_once(dirname(__FILE__).'/../../../index.php');
