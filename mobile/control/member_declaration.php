<?php
/**
 * 我的商城
 *
 *  (c) ()
 * @license
 * @link
 * @since
 */

defined('In33hao') or exit('Access Invalid!');

class member_declarationControl extends mobileMemberControl
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 报单列表
     */
    public function indexOp()
    {
        $order = 'add_time desc';
        $page = empty($_REQUEST['rp'])?10:intval($_REQUEST['rp']);
        $model_declaration_form = Model('declaration_form');
        $data = array();
        $list = $model_declaration_form->getMemberDeclarationList(array('member.member_id'=>$this->member_info['member_id']),'declaration_form.*',$page,$order);
        $data['now_page'] = $model_declaration_form->shownowpage();
        $data['total_num'] = $model_declaration_form->gettotalnum();
        if (empty($list)){
            $data['list'] = array();
        }else{
            foreach($list as $k=>$v)
            {
                $v['integral_start_time'] = $v['integral_start_time'] ? date('Y-m-d H:i:s', $v['integral_start_time']) : '';
                $v['integral_end_time'] = $v['integral_end_time'] ? date('Y-m-d H:i:s', $v['integral_end_time']) : '';
                $v['add_time'] = $v['add_time'] ? date('Y-m-d H:i:s', $v['add_time']) : '';
                $v['audit_time'] = $v['audit_time'] ? date('Y-m-d H:i:s', $v['audit_time']) : '';
                $data['list'][$v['id']] = $v;
            }
        }
        output_data($data);
    }

    /**
     * 报单信息
     */
    public function infoOp()
    {
        $id = empty($_REQUEST['id'])?0:intval($_REQUEST['id']);
        $model_declaration_form = Model('declaration_form');
        $declaration_info = $model_declaration_form->getDeclarationFromInfo(array('id'=>$id));
        if (empty($declaration_info)){
            output_error('报单不存在');
        }
        $declaration_info['integral_start_time'] = $declaration_info['integral_start_time'] ? date('Y-m-d H:i:s', $declaration_info['integral_start_time']) : '';
        $declaration_info['integral_end_time'] = $declaration_info['integral_end_time'] ? date('Y-m-d H:i:s', $declaration_info['integral_end_time']) : '';
        $declaration_info['add_time'] = $declaration_info['add_time'] ? date('Y-m-d H:i:s', $declaration_info['add_time']) : '';
        $declaration_info['audit_time'] = $declaration_info['audit_time'] ? date('Y-m-d H:i:s', $declaration_info['audit_time']) : '';
        output_data(array('declaration_info' => $declaration_info));
    }

}
