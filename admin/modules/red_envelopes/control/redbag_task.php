<?php
/**
 * 任务
 *
 */
defined('In33hao') or exit('Access Invild!');
class redbag_taskControl extends SystemControl {


    public function __construct()
    {
        parent::__construct();
    }

    public function indexOp()
    {
        $this->redbag_taskOp();
    }

    /**
     * 任务例
     */
    public function redbag_taskOp(){
        Tpl::setDirquna('red_envelopes');
        Tpl::showpage('redbag_task.list');
    }

    public function get_xmlOp()
    {
        $page = $_POST['rp'];
        $task=Model('red_redbag_task');
        //获取条件$condition
        $condition = array();
        if ($_POST['query'] != '') {
            $condition[$_POST['qtype']] = array('like', '%' . $_POST['query'] . '%');
        }
        if ($_REQUEST['keyword_type'] && $_REQUEST['keyword']) {
            $condition[$_REQUEST['keyword_type']] = array('like', '%' . $_REQUEST['keyword'] . '%');
        }
        if ($_REQUEST['query_start_date'] && $_REQUEST['query_end_date'] && strtotime($_REQUEST['query_start_date']) <= strtotime($_REQUEST['query_end_date'])) {
            $condition['addtime'] = array('between', array(strtotime($_REQUEST['query_start_date']), strtotime("+1 day", strtotime($_REQUEST['query_end_date'])) - 1));
        } elseif ($_REQUEST['query_start_date']) {
            $condition['addtime'] = array('egt', strtotime($_REQUEST['query_start_date']));
        } elseif ($_REQUEST['query_end_date']) {
            $condition['addtime'] = array('elt', strtotime("+1 day", strtotime($_REQUEST['query_end_date'])) - 1);
        }
        $age_list= Model()->table('redbag_task')->where($condition)->page($page)->select();
        $data = array();
        $data['now_page'] = $task->shownowpage();
        $data['total_num'] = $task->gettotalnum();
        foreach ($age_list as $v) {
            $param = array();
            $addtime=date("Y-m-d H:i:s",time());
            $param['operation'] = "<a class='btn blue' href='index.php?act=redbag_task&op=updates&task_id=" . $v['task_id'] . "'><i class='fa fa-pencil-square-o'></i>编辑</a><a class='btn blue' href='index.php?act=redbag_task&op=see&task_id=" . $v['task_id'] . "'><i class='fa fa-pencil-square-o'></i>查看</a><a class='btn red' href='index.php?act=redbag_task&op=taskdel&task_id=" . $v['task_id'] . "' onclick='fg_delete(".$v['task_id'].")'><i class='fa fa-trash-o'></i>删除</a>";
            $param['title'] = $v['task_title'];
            $param['number'] = $v['task_number'];
            $param['quantity_received'] = $v['task_quantity_received'];
            $param['surplus_quantity'] = $v['task_surplus_quantity'];
            $param['content'] = $v['task_content'];
            $picture = $picture=unserialize($v['task_picture']);
            if($picture[0]){
                $param['picture']="<a href='javascript:void(0);' class='pic-thumb-tip' onMouseOut='toolTip()' onMouseOver='toolTip(\"<img src=".UPLOAD_SITE_URL.DS.ATTACH_MOBILE.DS.$picture[0].">\")'><i class='fa fa-picture-o'></i></a>";
            }else{
                $param['picture']="<a href='javascript:void(0);' class='pic-thumb-tip' onMouseOut='toolTip()' onMouseOver='toolTip(\"<img src=".UPLOAD_SITE_URL.DS.ATTACH_MOBILE.DS.$picture[0].">\")'><i class='fa fa-picture-o'></i></a>";
            }
            $param['contrast_picture'] = "<a href='javascript:void(0);' class='pic-thumb-tip' onMouseOut='toolTip()' onMouseOver='toolTip(\"<img src=".UPLOAD_SITE_URL.DS.ATTACH_MOBILE.DS.$v['task_contrast_picture'].">\")'><i class='fa fa-picture-o'></i></a>";;
            $param['total_sum'] = $v['task_total_sum'];
            $param['task_order'] = $v['task_order'];
            $param['addtime'] = $addtime;
            $data['list'][$v['task_id']] = $param;
        }
        Tpl::flexigridXML($data);
    }

    /**
     * 任务添加设置
     */
    public function redbag_task_addOp(){
        $task = Model('red_redbag_task');
        if(chksubmit()){
            $data['task_title']=$_POST['title'];
            $data['task_number']=$_POST['number'];
            $data['task_surplus_quantity']=$_POST['number'];
            $data['task_quantity_received']='0';
            $data['task_content']=$_POST['content'];
            if($_FILES['picture']){
                $_POST['task_picture']=$this->buildInfo();
            }
            if (!empty($_FILES['contrast_picture']['name'])) {
                $upload = new UploadFile();
                $upload->set('default_dir', ATTACH_MOBILE);
                $result = $upload->upfile('contrast_picture');
                if ($result) {
                    $_POST['contrast_picture'] = $upload->file_name;
                } else {
                    showMessage($upload->error, '', '', 'error');
                }
            }
            $data['task_picture']=serialize($_POST['task_picture']);
            $data['task_contrast_picture']=$_POST['contrast_picture'];
            $data['task_total_sum']=$_POST['total_sum'];
            $data['task_order']='TA'.time().rand('0000','9999');
            $data['addtime']=time();
            $string=Model()->table('redbag_task')->insert($data);
            if($string){
                $this->red_task_miniOp($string);
                showMessage('添加成功','index.php?act=redbag_task&op=index');
            }else{
                showMessage('添加失败','index.php?act=redbag_task&op=index');
            }
        }
        Tpl::setDirquna('red_envelopes');
        Tpl::showpage('redbag_task.add');
    }

    /*
    * 任务分成小包
    */
    public function red_task_miniOp($string){
        $where['task_id'] = $string;
        $task =Model()->table('redbag_task')->where($where)->find();
        $data['task_id']=$task['task_id'];
        $data['task_order']=$task['task_order'];
        $data['state']='0';
        $data['addtime']=time();
        $data['money']=$task['task_total_sum']/$task['task_number'];
        $i=1;
        for ($i; $i <= $task['task_number']; $i++) {
            $data['task_order_number']=$task['task_order'].'-'.$i;
            Model()->table('redbag_task_mini')->insert($data);
        }
    }

    function buildInfo(){
        $dest_folder   = BASE_UPLOAD_PATH . DS .ATTACH_MOBILE . DS ;//上传图片保存的路径 图片放在跟你upload.php同级的picture文件夹里
        $arr=array();   //定义一个数组存放上传图片的名称方便你以后会用的。
        $count=0;
        $tp = array("image/gif","image/pjpeg","image/jpeg","image/png","image/jpg");    //检查上传文件是否在允许上传的类型
        foreach ($_FILES["picture"]["error"] as $key => $error) {
            if(!in_array($_FILES["picture"]["type"][$key],$tp)){
                echo "<script language='javascript'>";
                echo "alert(\"文件类型错误!\");";
                echo "</script>";
                exit;
            }
            $tmp_name = $_FILES["picture"]["tmp_name"][$key];
            $a = explode(".", $_FILES["picture"]["name"][$key]);  //截取文件名跟后缀
            if($a[2]){
                $name = date('YmdHis') . mt_rand(100, 999) . "." . $a[2];  // 文件的重命名 （日期+随机数+后缀）
            }else{
                $name = date('YmdHis') . mt_rand(100, 999) . "." . $a[1];  // 文件的重命名 （日期+随机数+后缀）
            }
            $uploadfile = $dest_folder . $name;     // 文件的路径
            move_uploaded_file($tmp_name, $uploadfile);
            $arr[]=$name;
        }
        return $arr;
    }

    /*
    * 删除单个任务
    */
    public function taskdelOp(){
        $task_id=$_GET['task_id'];
        $find=Model()->table('redbag_task')->where('task_id='.$task_id)->delete();
        Model()->table('redbag_task_mini')->where('task_id='.$task_id)->delete();
        if($find){
            showMessage('删除成功','index.php?act=redbag_task&op=index');
        }else{
            showMessage('删除失败','index.php?act=redbag_task&op=index');
        }
    }

    /*
    * 更改任务
    */
    public function updatesOp(){
        $task_id=$_GET['task_id'];
        $find=Model()->table('redbag_task')->where('task_id='.$task_id)->find();
        if(chksubmit()){
            $data['task_title']=$_POST['title'];
            $data['task_number']=$_POST['number'];
            $data['task_content']=$_POST['content'];
            if($_FILES['picture']){
                $_POST['task_picture']=$this->buildInfo();
            }
            if (!empty($_FILES['contrast_picture']['name'])) {
                $upload = new UploadFile();
                $upload->set('default_dir', ATTACH_MOBILE);
                $result = $upload->upfile('contrast_picture');
                if ($result) {
                    $_POST['contrast_picture'] = $upload->file_name;
                } else {
                    showMessage($upload->error, '', '', 'error');
                }
            }
            $data['task_picture']=serialize($_POST['task_picture']);
            $data['task_contrast_picture']=$_POST['contrast_picture'];
            $data['task_total_sum']=$_POST['total_sum'];
            $data['addtime']=time();
            $string=Model()->table('redbag_task')->where('task_id='.$task_id)->updates($data);
            if($string){
                showMessage('更新成功','index.php?act=redbag_task&op=index');
            }else{
                showMessage('更新失败','index.php?act=redbag_task&op=index');
            }
        }
        Tpl::output('result',$find);
        Tpl::setDirquna('red_envelopes');
        Tpl::showpage('redbag_task.updatas');
    }
    /*
     * 任务查看
     */
    public function seeOp(){
        $task_id=$_GET['task_id'];
        $find=Model()->table('redbag_task')->where('task_id='.$task_id)->find();
        if(count(unserialize($find['task_picture']))>0){
            $picture=unserialize($find['task_picture']);
            foreach ($picture as $k=>$v){
                $task_picture[]=UPLOAD_SITE_URL.DS.ATTACH_MOBILE.DS.$v;
            }
            $find['task_picture']=$task_picture;
        }else{
            $find['task_picture']='';
        }
        if($find['task_contrast_picture']){
            $find['task_contrast_picture']=UPLOAD_SITE_URL.DS.ATTACH_MOBILE.DS.$find['task_contrast_picture'];
        }else{
            $find['task_contrast_picture']='';
        }
        Tpl::output('result',$find);
        Tpl::setDirquna('red_envelopes');
        Tpl::showpage('redbag_task.see');
    }
}

