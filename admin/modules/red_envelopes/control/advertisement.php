<?php
/**
 * 年龄
 *
 */
defined('In33hao') or exit('Access Invild!');
class advertisementControl extends SystemControl {

    public function __construct()
    {
        parent::__construct();
    }

    public function indexOp()
    {
        $this->advertisementOp();
    }

    /**
     * 广告例
     */
    public function advertisementOp(){
        Tpl::setDirquna('red_envelopes');
        Tpl::showpage('redbag_push.list');
    }

    public function get_xmlOp()
    {
        $return=Model('red_advertisement');
        if ($_POST['query'] != '') {
            $condition[$_POST['qtype']] = array('like', '%' . $_POST['query'] . '%');
        }
        $return_list = $return->getPdRechargeList($condition, $pagesize = '', $fields = '*', $order = '', $limit = '');
        $data = array();
        $data['now_page'] = 1;
        $data['total_num'] = count($return_list);
        foreach ($return_list as $v) {
            $param = array();
            $param['operation'] = "<a class='btn blue' href='index.php?act=advertisement&op=advertising_updates&red_advertisement_id=" . $v['red_advertisement_id'] . "'><i class='fa fa-pencil-square-o'></i>编辑</a><a class='btn red' onclick='fg_delete(".$v['red_advertisement_id'].")' href='index.php?act=advertisement&op=advertisingdel&red_advertisement_id=" . $v['red_advertisement_id'] . "'><i class='fa fa-trash-o'></i>删除</a>";
            $param['province'] = $v['province_name'];
            $param['city'] = $v['city_name'];
            $param['area'] = $v['area_name'];
            $param['title'] = $v['title'];
            $param['address'] = $v['address'];
            $param['mobile'] = $v['mobile'];
            switch ($v['classify']) {
                case '1':
                    $param['classify'] = '衣';
                    break;
                case '2':
                    $param['classify'] = '吃';
                    break;
                case '3':
                    $param['classify'] = '住';
                    break;
                case '4':
                    $param['classify'] = '行';
                    break;
                default:
                    $param['classify'] = '';
            }
            $param['store'] = "<a href='javascript:void(0);' class='pic-thumb-tip' onMouseOut='toolTip()' onMouseOver='toolTip(\"<img src=".UPLOAD_SITE_URL.DS.ATTACH_MOBILE.DS.$v['store'].">\")'><i class='fa fa-picture-o'></i></a>";
            $data['list'][$v['red_advertisement_id']] = $param;
        }
        Tpl::flexigridXML($data);
    }

    /*
    * 删除单条广告
    */
    public function advertisingdelOp(){
        $advertisement = Model('red_advertisement');
        $red_advertisement_id=$_GET['red_advertisement_id'];
        $find=$advertisement->del($red_advertisement_id);
        if($find){
            showMessage('删除成功','index.php?act=advertisement&op=interest');
        }else{
            showMessage('删除失败','index.php?act=advertisement&op=interest');
        }
    }


    /**
     * 广告图片推送
     */
    public function advertising_pushOp(){
        $advertisement=Model('red_advertisement');
        $business=Model('red_business');
        if($_POST){
            if ($_POST['province'] && $_POST['city'] && $_POST['area']) {
                $data['red_advertisement_province']=$_POST['province'];
                $data['red_advertisement_city']=$_POST['city'];
                $data['red_advertisement_area']=$_POST['area'];
                $return=$advertisement->getPdRechargeList($data);
                $state=0;
                foreach ($return as $k=>$v){
                    $where['member_mobile']=$_POST['member_mobile'];
                    $where['state']='2';
                    $data_business=$business->get_businesss_find($where);
                    if($v['classify']==$data_business['classify']){
                        showMessage('已有推送这类广告','index.php?act=advertisement&op=index');
                    }
                }
                if($state==0 || $return==false){
                    $where['member_mobile']=$_POST['member_mobile'];
                    $where['state']='2';
                    $data_business=$business->get_businesss_find($where);
                    $data['business_id']=$data_business['business_id'];
                    $advertisementfind=$advertisement->getfind($data);
                    if($advertisementfind){
                        showMessage('已推送广告','index.php?act=advertisement&op=index');
                    }else{
                        $advertisementadd=$advertisement->insertadd($data);
                        if($advertisementadd){
                            showMessage('推送广告成功','index.php?act=advertisement&op=index');
                        }else{
                            showMessage('推送广告失败','index.php?act=advertisement&op=index');
                        }
                    }
                }

            }else{
                showMessage('请选择省市区');
            }
        }
        Tpl::setDirquna('red_envelopes');
        Tpl::output('top_link', $this->sublink($this->links, 'advertising_push'));
        Tpl::showpage('redbag_setting.push');
    }
    /*
     * 查询是否是商家
     */
    public function check_memberOp(){
        $where['member_mobile'] = trim($_GET['mobile']);
        if ($where['member_mobile']) {
            $business=Model('red_business');
            $model_member= Model('member');
            $info = $model_member->getMemberInfo($where);
            $where['state']='2';
            $data_business=$business->get_businesss_find($where);
            if ($data_business) {
                $data['name']=$info['member_name'];
                $data['id']=$info['member_id'];
                $data['member_mobile']=$info['member_mobile'];
                $data['title']=$data_business['title'];
                $data['mobile']=$data_business['mobile'];
                echo json_encode($data);
            } else {
                echo "";
            }
        } else {
            echo "0";
        }
    }

    public function advertising_updatesOp(){
        $advertisement=Model('red_advertisement');
        $condition['red_advertisement_id'] = $_GET['red_advertisement_id'];
        $list_setting = $advertisement->getPdRechargeList($condition);
        if($_POST){
            var_dump($_POST);die;
        }
        switch ($list_setting[0]['classify']) {
            case '1':
                $list_setting[0]['classify'] = '衣';
                break;
            case '2':
                $list_setting[0]['classify'] = '吃';
                break;
            case '3':
                $list_setting[0]['classify'] = '住';
                break;
            case '4':
                $list_setting[0]['classify'] = '行';
                break;
            default:
                $list_setting[0]['classify'] = '';
        }
        $list_setting[0]['store']=UPLOAD_SITE_URL.DS.ATTACH_MOBILE.DS.$list_setting[0]['store'];
        Tpl::output('list_setting',$list_setting[0]);
        Tpl::setDirquna('red_envelopes');
        Tpl::output('top_link', $this->sublink($this->links, 'advertising_push'));
        Tpl::showpage('redbag_setting.updates');
    }
    /*
    * 广告三级联动
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

