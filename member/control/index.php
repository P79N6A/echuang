<?php
/**
 * 默认展示页面
 *
 *
 * *  (c) ()
 * @license
 * @link
 * @since
 */



defined('In33hao') or exit('Access Invalid!');
class indexControl extends BaseLoginControl{
    public function __construct() {
        @header("location: " . urlMember('member_information'));
    }
}
