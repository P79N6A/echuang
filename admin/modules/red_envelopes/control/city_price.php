<?php
/**
 * 年龄
 *
 */
defined('In33hao') or exit('Access Invild!');
class city_priceControl extends SystemControl {

    public function __construct()
    {
        parent::__construct();
    }

    public function indexOp()
    {
        $this->citypriceOp();
    }

    /**
     * 城主竞价
     */
    public function citypriceOp(){
        Tpl::setDirquna('red_envelopes');
        Tpl::showpage('redbag_cityprice');
    }

    public function get_xmlOp()
    {
        $page = $_POST['rp'];
        $order = $_POST['sortname'] . ' ' . $_POST['sortorder'];
        $price=Model('red_city_price');
        if ($_POST['query'] != '') {
            $condition[$_POST['qtype']] = array('like', '%' . $_POST['query'] . '%');
        }
        $return_list = $price->getPdRechargeList($condition, $page, $fields = '*', $order = '', $limit = '');
        $data = array();
        $data['now_page'] = $price->shownowpage();
        $data['total_num'] = $price->gettotalnum();
        foreach ($return_list as $v) {
            $param = array();
            $param['operation'] = "<a class='btn blue' href='index.php?act=city_price&op=citypriceupdates&city_price_id=" . $v['city_price_id'] . "'><i class='fa fa-pencil-square-o'></i>编辑</a><a class='btn red' onclick='fg_delete(".$v['city_price_id'].")' href='index.php?act=city_price&op=citypricedel&city_price_id=" . $v['city_price_id'] . "'><i class='fa fa-trash-o'></i>删除</a>";
            $param['province'] = $v['province_name'];
            $param['city'] = $v['city_name'];
            $param['area'] = $v['area_name'];
            $param['price'] = $v['price'];
            $data['list'][$v['city_price_id']] = $param;
        }
        Tpl::flexigridXML($data);
    }

    /*
    * 删除单条城主竞价
    */
    public function citypricedelOp(){
        $price = Model('red_city_price');
        $city_price_id=$_GET['city_price_id'];
        $find=$price->del($city_price_id);
        if($find){
            showMessage('删除成功','index.php?act=city_price&op=cityprice');
        }else{
            showMessage('删除失败','index.php?act=city_price&op=cityprice');
        }
    }


    /**
     * 城主竞价添加
     */
    public function cityprice_pushOp(){
        $price=Model('red_city_price');
        if($_POST){
            if ($_POST['province'] && $_POST['city'] && $_POST['area']) {
                $where['province']=$_POST['province'];
                $where['city']=$_POST['city'];
                $where['area']=$_POST['area'];
                $data=$price->envelopesget($where);
                if($data==false){
                    $data['price']=$_POST['price'];
                    $data['province']=$_POST['province'];
                    $data['city']=$_POST['city'];
                    $data['area']=$_POST['area'];
                    $data['present_price']=$_POST['price'];
                    $data['addtime']=time();
                    $price_data=$price->redbayadd($data);
                    if($price_data){
                        showMessage('添加成功','index.php?act=city_price');
                    }
                }else{
                   showMessage('这个区以有数据！'); 
                }
            }else{
                showMessage('请选择省市区');
            }
        }
        Tpl::setDirquna('red_envelopes');
        Tpl::showpage('redbag_cityrice.push');
    }

    public function citypriceupdatesOp(){
        $price=Model('red_city_price');
        $condition['city_price_id'] = $_GET['city_price_id'];
        $list_setting = $price->envelopesget($condition);
        if($_POST){
            $data['price']=$_POST['price'];
            $price_data=$price->stateupdate($condition,$data);
            if($price_data){
                showMessage('更改成功','index.php?act=city_price');
            }else{
                showMessage('更改失败','index.php?act=city_price');
            }
        }
        Tpl::output('list_setting',$list_setting);
        Tpl::setDirquna('red_envelopes');
        Tpl::showpage('redbag_cityprice.updates');
    }
    /*
    * 城主竞价三级联动
    */
     public function sjldOp()
    {
        //连接数据库
        $db = Model('red_expand_the_scope');
        //接受数据
        $id = 0;
        if (!empty($_GET)) {//如果得到的数不是空
            $id = $_GET['id'];//获取省的id
        }
        //查表父id=0
        $sql = $db->sel($id);

        //返回数据 字符串
        $this->arrTostr($sql);
    }
    /*
    * 返回联动值
    */
    function arrTostr($sql)
    {//$a是结果集返回的是二维数组
        $brr = array();//定义一个数组用来存数组转字符串
        foreach ($sql as $v) {//遍历数组里面的数
            $temp = implode(",", $v);//遍历数组里面的数
            $brr[] = $temp;//转成一维数组
        }
        echo implode("^", $brr);//转成字符串
    }

}

