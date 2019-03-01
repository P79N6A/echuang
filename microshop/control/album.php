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
class albumControl extends MircroShopControl{

    public function __construct() {
        parent::__construct();
        Tpl::output('index_sign','album');
    }

    //首页
    public function indexOp(){
        Tpl::showpage('album');
    }
}
