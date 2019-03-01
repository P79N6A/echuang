<?php

/**
 * 会员系统共用funciton
 *
 */
defined('In33hao') or exit('Access Invild!');

/**
 * getMicrotime 获取当前微秒
 * @return [type] [description]
 */
function getMicrotime()
{
    list($usec, $sec) = explode(' ', microtime());
    return (float) ($usec + $sec);
}
