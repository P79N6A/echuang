<?php
/**
 * 物流自提服务站首页
 *
 * *  (c) ()
 * @license
 * @link
 * @since
 */



defined('In33hao') or exit('Access Invalid!');

class indexControl extends BaseDeliveryControl{
    public function __construct(){
        parent::__construct();
        @header('location: index.php?act=login');die;
    }
}
