<?php

defined('In33hao') or exit('Access Invild!');

class member_levelControl extends BaseMemberControl {

    private $inventory_info = array();//库存信息（各等级配送数量数组,报单产品价格）
    public function __construct(){
        parent::__construct();
        $this->_getInventoryInfo();
    }

    private function _getInventoryInfo(){
        $this->inventory_info = Model('member_system_set')->getInventoryInfo();
    }


    /***测试方法*****/
    public function testOp(){
    }

   

}

