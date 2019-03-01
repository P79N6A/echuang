<?php
/**
 * 兴趣
 *
 */
defined('In33hao') or exit('Access Invild!');
class interestControl extends SystemControl {


    public function __construct()
    {
        parent::__construct();
    }

    public function indexOp()
    {
        $this->interestOp();
    }

    /**
     * 兴趣例
     */
    public function interestOp(){
        Tpl::output('result',$result);
        Tpl::setDirquna('red_envelopes');
        Tpl::showpage('redbag_interest.index');
    }

    public function get_xmlOp()
    {
        $interest=Model('red_interest');
        $interest_list=$interest->get_array();
        $data = array();
        $data['now_page'] = 1;
        $data['total_num'] = count($interest_list);
        foreach ($interest_list as $v) {
            $param = array();
            $param['operation'] = "<a class='btn blue' href='index.php?act=interest&op=interestupdates&interest_id=" . $v['interest_id'] . "'><i class='fa fa-pencil-square-o'></i>编辑</a><a class='btn red' onclick='fg_delete(".$v['interest_id'].")'><i class='fa fa-trash-o'></i>删除</a>";
            $param['name'] = $v['name'];
            $data['list'][$v['interest_id']] = $param;
        }
        Tpl::flexigridXML($data);
    }

    /**
     * 兴趣添加设置
     */
    public function interest_addOp(){
        $interest = Model('red_interest');
        if(chksubmit()){
            $data['name']=$_POST['interest_name'];
            $find=$interest->getfind($data);
            if($find==false){
                $data['addtime']=time();
                $string=$interest->insertadd($data);
                if($string){
                    showMessage('添加成功','index.php?act=interest&op=interest');
                }else{
                    showMessage('添加失败','index.php?act=interest&op=interest');
                }
            }else{
                showMessage('爱好已存在','index.php?act=interest&op=interest');
            }
        }
        Tpl::output('result',$result);
        Tpl::setDirquna('red_envelopes');
        Tpl::showpage('redbag_interest.index_add');
    }

    /*
    * 删除单个兴趣
    */
    public function interestdelOp(){
        $interest = Model('red_interest');
        $interest_id=$_GET['interest_id'];
        $find=$interest->del($interest_id);
        if($find){
            showMessage('删除成功','index.php?act=interest&op=interest');
        }else{
            showMessage('删除失败','index.php?act=interest&op=interest');
        }
    }

    /*
    * 更改兴趣
    */

    public function interestupdatesOp(){
        $interest = Model('red_interest');
        $interest_id=$_GET['interest_id'];
        $result=$interest->red_getRow($interest_id);
        if(chksubmit()){
            $data['name']=$_POST['name'];
            $string=$interest->updates($data,$interest_id);
            if($string){
                showMessage('更新成功','index.php?act=interest&op=interest');
            }else{
                showMessage('更新失败','index.php?act=interest&op=interest');
            }
        }
        Tpl::output('result',$result);
        Tpl::setDirquna('red_envelopes');
        Tpl::showpage('redbag_interest.index_updates');
    }

}