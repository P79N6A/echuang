<?php
/**
 * 会员分润
 *
 */
defined('In33hao') or exit('Access Invild!');
class distributionControl extends SystemControl {
	private $links = array(
		array('url' => 'act=distribution&op=membership_distribution','text'=>'会员分润'),
        array('url' => 'act=distribution&op=share_benefits','text'=>'城主分润'),
		//array('url' => 'act=account&op=account_adjust', 'lang' => 'member_account_adjust'),
	);

    public function __construct()
    {
        parent::__construct();

    }

    public function indexOp()
    {
        $this->membership_distributionOp();
    }

    /**
     * 设置会员分润查看
     */
    public function membership_distributionOp(){
        $distribution=Model('red_membership_distribution');
        $result=$distribution->red_getRow('1');
        if(chksubmit()){
            $data['state']=$_POST['state'];
            $data['recommender_giving_red_packets']=$_POST['recommender_giving_red_packets'];
            $data['grab_a_red_envelope']=$_POST['grab_a_red_envelope'];
            if($result){
                $string=$distribution->updates($data,'1');
            }
            if($string){
                showMessage('设置成功','index.php?act=distribution&op=membership_distribution');
            }else{
                showMessage('设置失败','index.php?act=distribution&op=membership_distribution');
            } 
        }
        Tpl::output('result',$result);
        Tpl::setDirquna('red_envelopes');
        Tpl::output('top_link', $this->sublink($this->links));
        Tpl::showpage('redbag_distribution.membership');
    }

    /**
     * 城主分润设置查看
     */
    public function share_benefitsOp(){
        $share_benefits=Model('red_city_lord_share_benefits');
        $result=$share_benefits->red_getRow('1');
        if(chksubmit()){
            $data['member_rob']=$_POST['member_rob'];
            $data['member_hair']=$_POST['member_hair'];
            $data['city_rebate']=$_POST['city_rebate'];
            $data['state']=$_POST['state'];
            if($result){
                $string=$share_benefits->updates($data,'1');
            }
            if($string){
                showMessage('设置成功','index.php?act=distribution&op=share_benefits');
            }else{
                showMessage('设置失败','index.php?act=distribution&op=share_benefits');
            } 
        }
        Tpl::output('result',$result);
        Tpl::setDirquna('red_envelopes');
        Tpl::output('top_link', $this->sublink($this->links, 'the_scope'));
        Tpl::showpage('redbag_benefits.city');
    }
}