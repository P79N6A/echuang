<?php
/**
 *  设置范围
 *
 */
defined('In33hao') or exit('Access Invild!');
class envelopesControl extends SystemControl {
	private $links = array(
		array('url' => 'act=envelopes&op=envelopes','text'=>'设置范围'),
        array('url' => 'act=envelopes&op=the_scope','text'=>'扩大范围设置'),
        array('url' => 'act=envelopes&op=push_time','text'=>'红包推送时间设置'),
        array('url' => 'act=envelopes&op=auction_of_city_leaders','text'=>'设置城主竞拍'),
		//array('url' => 'act=account&op=account_adjust', 'lang' => 'member_account_adjust'),
	);

    public function __construct()
    {
        parent::__construct();

    }

    public function indexOp()
    {
        $this->envelopesOp();
    }

    /**
     * 设置范围查看
     */
    public function envelopesOp(){
        $location=Model('red_envelopes_location');
        $result=$location->red_getRow('1');
        if(chksubmit()){
            $data['red_envelopes_location_range']=$_POST['red_envelopes_location_range'];
            $data['red_envelopes_location_num']=$_POST['red_envelopes_location_num'];
            $data['time_setting']=$_POST['time_setting'];
            if($result){
                $string=$location->updates($data,'1');
            }
            if($string){
                showMessage('设置成功','index.php?act=envelopes&op=envelopes');
            }else{
                showMessage('设置失败','index.php?act=envelopes&op=envelopes');
            } 
        }
        Tpl::output('result',$result);
        Tpl::setDirquna('red_envelopes');
        Tpl::output('top_link', $this->sublink($this->links));
        Tpl::showpage('redbag_setting.location');
    }

    /**
     * 扩大范围设置查看
     */
    public function the_scopeOp(){
        $the_scope=Model('red_expand_the_scope');
        $result=$the_scope->red_getRow('1');
        if(chksubmit()){
            $data['recommended_registration']=$_POST['recommended_registration'];
            $data['add_red_envelopes_num']=$_POST['add_red_envelopes_num'];
            $data['add_red_envelopes_range']=$_POST['add_red_envelopes_range'];
            $data['state']=$_POST['state'];
            if($result){
                $string=$the_scope->updates($data,'1');
                if($string){
                    showMessage('设置成功','index.php?act=envelopes&op=the_scope');
                }else{
                    showMessage('设置失败','index.php?act=envelopes&op=the_scope');
                } 
            }
        }
        Tpl::output('result',$result);
        Tpl::setDirquna('red_envelopes');
        Tpl::output('top_link', $this->sublink($this->links, 'the_scope'));
        Tpl::showpage('redbag_setting.extended');
    }

    /*
     * 红包推送时间
     */
    public function push_timeOp(){
        $location=Model('red_envelopes_location');
        $result=$location->red_getRow('1');
        if($_POST){
            $data['time_setting']=$_POST['time_setting'];
            $rereturn=$location->updates($data,$result['red_envelopes_location_id']);
            if($rereturn){
                showMessage('设置成功','index.php?act=envelopes&op=push_time');
            }else{
                showMessage('设置失败','index.php?act=envelopes&op=push_time');
            }
        }
        Tpl::output('result',$result);
        Tpl::setDirquna('red_envelopes');
        Tpl::output('top_link', $this->sublink($this->links, 'push_time'));
        Tpl::showpage('redbag_setting.push_time');
    }
    public function auction_of_city_leadersOp(){
        $location=Model('red_envelopes_location');
        $result=$location->red_getRow('1');
        if($_POST){
            $data['auction']=$_POST['auction'];
            $rereturn=$location->updates($data,$result['red_envelopes_location_id']);
            if($rereturn){
                showMessage('设置成功','index.php?act=envelopes&op=auction_of_city_leaders');
            }else{
                showMessage('设置失败','index.php?act=envelopes&op=auction_of_city_leaders');
            }
        }
        Tpl::output('result',$result);
        Tpl::setDirquna('red_envelopes');
        Tpl::output('top_link', $this->sublink($this->links, 'auction_of_city_leaders'));
        Tpl::showpage('redbag_setting.auction');
    }
}