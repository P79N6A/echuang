<?php
/**
 * 前台品牌分类
 *
 *
 *  (c) ()
 * @license
 * @link
 * @since
 */


defined('In33hao') or exit('Access Invalid!');
class documentControl extends mobileHomeControl {
    public function __construct() {
        parent::__construct();
    }

    public function agreementOp() {
        $doc = Model('document')->getOneByCode('agreement');
        output_data($doc);
    }
}
