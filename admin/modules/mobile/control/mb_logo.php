<?php
/**
 * 手机端LOGO图片设置
 *
 *
 *
 *
 */



defined('In33hao') or exit('Access Invalid!');
class mb_logoControl extends SystemControl{
    public function __construct(){
        parent::__construct();
//         Language::read('mobile');
    }
	
	
    public function indexOp(){
        $model_setting = Model('setting');
        if (chksubmit()){
            if ($_FILES['mobile_logo']['tmp_name'] != ''){
                $upload = new UploadFile();
                $upload->set('default_dir',ATTACH_COMMON);
				$upload->file_name='home_logo.png';
                $result = $upload->upfile('mobile_logo');
                if ($result){
                    $_POST['mobile_logo'] = $upload->file_name;
                }else {
                    showMessage($upload->error);
                }
            }
            if ($_FILES['mobile_index_logo']['tmp_name'] != ''){
                $upload = new UploadFile();
                $upload->set('default_dir',ATTACH_COMMON);
                $upload->file_name='home_index_logo.png';
                $result = $upload->upfile('mobile_index_logo');
                if ($result){
                    $_POST['mobile_index_logo'] = $upload->file_name;
                }else {
                    showMessage($upload->error);
                }
            }
            if ($_FILES['mobile_av_logo']['tmp_name'] != ''){
                $upload = new UploadFile();
                $upload->set('default_dir',ATTACH_COMMON);
                $upload->file_name='home_av_logo.png';
                $result = $upload->upfile('mobile_av_logo');
                if ($result){
                    $_POST['mobile_av_logo'] = $upload->file_name;
                }else {
                    showMessage($upload->error);
                }
            }
            $update_array = array();
            if (!empty($_POST['mobile_logo'])){
				$update_array['mobile_logo'] = $_POST['mobile_logo'];
			}
            if (!empty($_POST['mobile_index_logo'])){
                $update_array['mobile_index_logo'] = $_POST['mobile_index_logo'];
            }
            if (!empty($_POST['mobile_av_logo'])){
                $update_array['mobile_av_logo'] = $_POST['mobile_av_logo'];
            }
            $result = $model_setting->updateSetting($update_array);
            if ($result){
                if (!empty($mobile_logo)){
                    @unlink(BASE_ROOT_PATH.DS.DIR_UPLOAD.DS.ATTACH_COMMON.'/'.'home_logo.png');
                }
                if (!empty($mobile_index_logo)){
                    @unlink(BASE_ROOT_PATH.DS.DIR_UPLOAD.DS.ATTACH_COMMON.'/'.'home_index_logo.png');
                }
                if (!empty($mobile_index_logo)){
                    @unlink(BASE_ROOT_PATH.DS.DIR_UPLOAD.DS.ATTACH_COMMON.'/'.'home_av_logo.png');
                }
                showMessage(Language::get('nc_common_save_succ'));
            }else {
                showMessage(Language::get('nc_common_save_fail'));
            }
        }
        Tpl::output('mobile_logo',C('mobile_logo'));
        Tpl::output('mobile_index_logo',C('mobile_index_logo'));
        Tpl::setDirquna('mobile');
        Tpl::showpage('mb_logo.index');
    }
}
