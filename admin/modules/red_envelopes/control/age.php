<?php
/**
 * 年龄
 *
 */
defined('In33hao') or exit('Access Invild!');
class ageControl extends SystemControl {


    public function __construct()
    {
        parent::__construct();
    }

    public function indexOp()
    {
        $this->ageOp();
    }

    /**
     * 年龄例
     */
    public function ageOp(){
        Tpl::setDirquna('red_envelopes');
        Tpl::showpage('redbag_age.index');
    }

    public function get_xmlOp()
    {
        $age=Model('red_age');
        $age_list=$age->get_array();
        $data = array();
        $data['now_page'] = 1;
        $data['total_num'] = count($age_list);
        foreach ($age_list as $v) {
            $param = array();
            $param['operation'] = "<a class='btn blue' href='index.php?act=age&op=ageupdates&age_id=" . $v['age_id'] . "'><i class='fa fa-pencil-square-o'></i>编辑</a><a class='btn red' onclick='fg_delete(".$v['age_id'].")'><i class='fa fa-trash-o'></i>删除</a>";
            $param['age_name'] = $v['left_age'].'-'.$v['right_age'];
            $data['list'][$v['age_id']] = $param;
        }
        Tpl::flexigridXML($data);
    }

    /**
     * 年龄添加设置
     */
    public function age_addOp(){
        $age = Model('red_age');
        if(chksubmit()){
            $data['left_age']=(int)$_POST['left_age'];
            $data['right_age']=(int)$_POST['right_age'];
            $find=$age->getfind($data);
            if($find==false){
                $data['addtime']=time();
                $string=$age->insertadd($data);
                if($string){
                    showMessage('添加成功','index.php?act=age&op=age');
                }else{
                    showMessage('添加失败','index.php?act=age&op=age');
                }
            }else{
                showMessage('年龄已存在','index.php?act=age&op=age');
            }
        }
        Tpl::output('result',$result);
        Tpl::setDirquna('red_envelopes');
        Tpl::showpage('redbag_age.index_add');
    }

    /*
    * 删除单个年龄阶段
    */
    public function agedelOp(){
        $age = Model('red_age');
        $age_id=$_GET['age_id'];
        $find=$age->del($age_id);
        if($find){
            showMessage('删除成功','index.php?act=age&op=age');
        }else{
            showMessage('删除失败','index.php?act=age&op=age');
        }
    }

    /*
    * 更改年龄阶段
    */

    public function ageupdatesOp(){
        $age = Model('red_age');
        $age_id=$_GET['age_id'];
        $result=$age->red_getRow($age_id);
        if(chksubmit()){
            $data['left_age']=(int)$_POST['left_age'];
            $data['right_age']=(int)$_POST['right_age'];
            $string=$age->updates($data,$age_id);
            if($string){
                showMessage('更新成功','index.php?act=age&op=age');
            }else{
                showMessage('更新失败','index.php?act=age&op=age');
            }
        }
        Tpl::output('result',$result);
        Tpl::setDirquna('red_envelopes');
        Tpl::showpage('redbag_age.index_updates');
    }

}

