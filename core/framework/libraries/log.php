<?php
/**
 * 记录日志
 *
 * @
 * @license
 * @link
 */
defined('In33hao') or exit('Access Invalid!');
class Log
{
    const SQL = 'SQL';
    const ERR = 'ERR';
    const LOG = 'LOG';
    private static $log = array();

    /**
     * record 日志记录
     * @param  [type] $message [description]
     * @param  [type] $level   [description]
     * @return [type]          [description]
     */
    public static function record($message, $level = self::ERR)
    {
        $now = @date('Y-m-d H:i:s', time());
        switch ($level) {
            case self::SQL:
                self::$log[] = "[{$now}] {$level}: {$message}\r\n";
                break;
            case self::ERR:
                $log_file = BASE_DATA_PATH . '/log/' . date('Ymd', TIMESTAMP) . '.log';
                $url = $_SERVER['REQUEST_URI'] ? $_SERVER['REQUEST_URI'] : $_SERVER['PHP_SELF'];
                $url .= " ( act={$_GET['act']}&op={$_GET['op']} ) ";
                $content = "[{$now}] {$url}\r\n{$level}: {$message}\r\n";
                @file_put_contents($log_file, $content, FILE_APPEND);
                break;
            case 'LOG':
                $log_file = BASE_DATA_PATH . '/log/' . date('Ymd', TIMESTAMP) . '.log';
                file_put_contents($log_file, $message, FILE_APPEND);
                break;
        }
    }

    /**
     * read 读取日志
     * @return [type] [description]
     */
    public static function read()
    {
        return self::$log;
    }

    /**
     * memberRecord 会员日志记录
     * @param  [type] $message   [description]
     * @param  [type] $member_id [description]
     * @return [type]            [description]
     */
    public static function memberRecord($message, $member_id)
    {
        $log_file = BASE_DATA_PATH . DS . 'log' . DS . 'member' . DS . date('Ymd', TIMESTAMP) . '_' . $member_id . '.log';
        $message = date('Y-m-d H:i:s', TIMESTAMP) . ' ' . $message . "\r\n";
        @file_put_contents($log_file, $message, FILE_APPEND);
    }

    /**
     * adminRecord 管理员日志记录
     * @param  [type] $message    [description]
     * @param  [type] $admin_name [description]
     * @return [type]             [description]
     */
    public static function adminRecord($message, $admin_name)
    {
        $log_file = BASE_DATA_PATH . DS . 'log' . DS . 'admin' . DS . date('Ymd', TIMESTAMP) . '_' . $admin_name . '.log';
        file_put_contents($log_file, $message, FILE_APPEND);
    }
}