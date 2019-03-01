<?php
/**
 * 个人中心
 *
 *
 *  (c) ()
 * @license
 * @link
 * @since
 */
header("Access-Control-Allow-Origin: *");

defined('In33hao') or exit('Access Invalid!');
class redbay_personalControl extends apiEncryptCommonControl {
	public function __construct() {
		parent::__construct();
	}

	public function indexOp() {
		$this->subordinateOp();
	}
	/*
	* 直推下级
	*/
	public function subordinateOp(){
		$token=Model('mb_user_token');
		$where['token']=$_POST['token'];
		//用token得到用户id号
		$rowtoken=$token->getMbUserTokenInfo($where);
		$where_token['member_id']=$rowtoken['member_id'];
		$manage=Model('red_city_owner_manage');
		$memberslist=$manage->getmemberslist($where_token);
		if($memberslist){
			$i=1;
			foreach ($memberslist as $key => $value) {
				if($value['member_level']==0){
					$data['fans']= (int)$i;
				}else{
					$data['member']= (int)$i;
				}
				$i++;
			}
			if($data['fans']==false){
				$data['fans']=0;
			}
			if($data['member']==false){
				$data['member']=0;
			}
			responseApiJson(1,'',$data);
		}else{
			$data['fans']=0;
			$data['member']=0;
			responseApiJson(1,'',$data);
		}
		
	}
	/*
	* 直推下级
	*/
	public function memberOp(){
        $token=Model('mb_user_token');
		$member_find=Model('member');
		$where['token']=$_POST['token'];
        $fans=$_POST['fans'];
		//用token得到用户id号
		$rowtoken=$token->getMbUserTokenInfo($where);
		$where_token['member_id']=$rowtoken['member_id'];
		$manage=Model('red_city_owner_manage');
		$memberslist=$manage->getmemberslist($where_token);
		foreach ($memberslist as $key => $value) {
			if($fans=='fans'){
				$where_fans['member_id']=$value['member_id'];
				$member=$manage->getmemberfanslist($where_fans);
			}elseif($fans=='member'){
                $where_member['me_member_id']=$value['me_member_id'];
				$member=$manage->getmembermelist($where_member);
			}
		}
		foreach ($member as $k=>$v){
            $data[$k]['member_avatar']=UPLOAD_SITE_URL . '/' . ATTACH_AVATAR . '/'.$v['member_avatar'];
            $data[$k]['member_name']=$v['member_me_name'];
            $data[$k]['member_me_mobile']=$v['member_me_mobile'];
        }
		responseApiJson(1,'',$data);
	}
	/*
	 * 商家状态
	 */
	public function businessfindOp(){
        $token=Model('red_member_user');
        $where['token']=$_POST['token'];
        //用token得到用户id号
        $rowtoken=$token->memberuserfind($where);
        $business=Model('red_business');
        $wheremobile['member_mobile'] =$rowtoken['member_mobile'];
        $data_business=$business->get_businesss_find($wheremobile);
        if($data_business['state']=='0'){
            $business='1';
        }elseif($data_business['state']=='1'){
            $business='2';
        }elseif($data_business['state']=='2'){
            $business='3';
        }else{
            $business='0';
        }
        responseApiJson(1,'',$business);
    }
	/*
	 * 门店信息添加
	 */
	public function businessaddOp(){
        $validate = new Validate();
        $validate->validateparam = array(
            array('input' => $_POST['title'], 'require' => "true", 'message' => "请输入店名"),
            array('input' => $_POST['mobile'], 'require' => "true", 'message' => "请输入手机号码"),
            array('input' => $_POST['mobile'], 'validator' => "mobile", 'message' => "手机号码格式错误"),
        );
	    $business=Model('red_business');
        $token=Model('mb_user_token');
        $where['token']=$_POST['token'];
        $row=$token->getMbUserTokenInfo($where);
        $data['member_id']=$row['member_id'];
        if($data){
	        $businessfind=$business->get_businesss_find($data);
	        if($businessfind==false || $businessfind['state']==1){
	            $data['title']=$_POST['title'];
	            $data['address']=$_POST['address'];
	            $data['mobile']=$_POST['mobile'];
	            $data['classify']=$_POST['classify'];
	            $data['content']=$_POST['content'];
	            $data['licence']=$_POST['licence'];
	            $data['store']=$_POST['store'];
	            $data['state']='0';
	            $data['addtime']=time();
	            $businesssadd=$business->businesssadd($data);
	            if($businesssadd){
	                responseApiJson(1,'',$businesssadd);
	            }else{
	                responseApiJson(0);
	            }
	        }else{
	            responseApiJson(0,'你已申请过商家');
	        }
	}else{
		responseApiJson(0,'没有识别码');
	}

    }

    /*
     * 自已的商家
     */
    public function advertisementdetailsOp(){
        $token=Model('mb_user_token');
        $where_data['token']=$_GET['token'];
        //用token得到用户id号
        $rowtoken=$token->getMbUserTokenInfo($where_data);
        $where['business_id']=$_GET['business_id'];
        $where['state']='2';
        $business=Model('red_business');
        $businesssadd=$business->get_businesss_find($where);
        if($businesssadd){
            $data['store']=UPLOAD_SITE_URL.DS.ATTACH_MOBILE.DS.$businesssadd['store'];
            $data['member_avatar']=UPLOAD_SITE_URL . '/' . ATTACH_AVATAR . '/' .$businesssadd['member_avatar'];
            $data['title']=$businesssadd['title'];
            $data['address']=$businesssadd['address'];
            $data['mobile']=$businesssadd['mobile'];
            $data['classify']=$businesssadd['classify'];
            $data['content']=$businesssadd['content'];
            responseApiJson(1,'',$data);
        }else{
            responseApiJson(0,'商家正在审核中');
        }
    }

    /*
     * 查询商家是否有发布优恵劵
     */
    public function excellentsecuritiesOp(){
        $issue=Model('red_redbag_issue');
        $token=Model('mb_user_token');
        $token_where['token']=$_GET['token'];
        $row=$token->getMbUserTokenInfo($token_where);
        $where['member_id']=$row['member_id'];
        $where['state']='2';
        $where['payment_state']='1';
        $where['red_examine']='2';
        $return_list = $issue->getPdRechargeList($where);
        if($return_list){
            foreach ($return_list as $key=>$value){
                $return[$key]['title']=$value['red_title'];
                $return[$key]['address']=$value['red_address'];
                $return[$key]['discount']=$value['red_discount'];
                $return[$key]['already_issued']=$value['red_envelopes_already_issued'];//已抢
                $return[$key]['unissued']=$value['red_envelopes_unissued'];
                $return[$key]['time']=date('Y-m-d',$value['ter_of_validity']);
                $redbag['red_envelopes_order_number']=$value['red_envelopes_order_number'];
                $redbag['receivestate']='1';
                $excellentsecurities=$issue->getexcellentsecurities($redbag);
                $count=count($excellentsecurities);
                $return[$key]['swap']=$count;
            }
            responseApiJson(1,'',$return);
        }else{
            responseApiJson(0,'商家没有发布优恵劵');
        }
    }

    /*
     * 立即兑换
     */
    public function redeem_nowOp(){
        $issue=Model('red_redbag_issue');
        $token=Model('mb_user_token');
        $token_where['token']=$_POST['token'];
        $row=$token->getMbUserTokenInfo($token_where);
        $where_array['member_id']=$row['member_id'];
        //$where_array['member_id']=$this->member_info['member_id'];
        $where['order_number']=$_POST['order_number'];
        $order_number=explode("-",$where['order_number']);
        $where_array['red_envelopes_order_number']=$order_number[0];
        $time=strtotime(date("Y-m-d",time()));
        $envelopes=$issue->envelopesget($where_array);
        $validity=(int)$envelopes['ter_of_validity'];
        if($time<$validity){
            $where['receivestate']='0';
            $excellentsecurities=$issue->getexcel($where);
            if($excellentsecurities){
                $data['receivestate']='1';
                $id['id']=$excellentsecurities['id'];
                $return=$issue->miniupdate($id,$data);
                if($return){
                    responseApiJson(1,'',$return);
                }else{
                    responseApiJson(0,'兑换失败');
                }
            }else{
                responseApiJson(0,'无效的优恵劵');
            }
        }else{
            responseApiJson(0,'优恵劵已过期');
        }
    }

    /*
     * 查询用户是否有优恵劵
     */
    public function excellentOp(){
        $issue=Model('red_redbag_issue');
        $manage=Model('red_redbag_manage');
        $token=Model('mb_user_token');
        $token_where['token']=$_GET['token'];
        $row=$token->getMbUserTokenInfo($token_where);
        $where['wo_member']=$row['member_id'];
        $where['red_examine']='2';
        $where['rob_state']='0';
        $manage_return=$manage->getPdRechargeList($where);
        if($manage_return){
            foreach ($manage_return as $k=>$v){
                $where_mini['order_number']=$v['order_number'];
                $getexcel=$issue->getexcel($where_mini);
                if($getexcel){
                    $return[$k]['red_redeem_code']=$v['red_redeem_code'];
                    $return[$k]['title']=$v['red_title'];
                    $return[$k]['address']=$v['red_address'];
                    $return[$k]['discount']=$v['red_discount'];
                    $return[$k]['order_number']=$v['order_number'];
                    $return[$k]['ter_of_validity']=date('Y-m-d',$v['ter_of_validity']);
                    if($getexcel['receivestate']=='1'){
                        $return[$k]['receivestate']='已领取';
                    }else{
                        $return[$k]['receivestate']='可兑换';
                    }
                }
            }
            responseApiJson(1,'',$return);
        }else{
            responseApiJson(0,'没有优恵劵');
        }
    }

    /*
     * 二维码
     */
    public function qrcodeOp(){
        $order_number=$_POST['order_number'];//$_POST['order_number'];
        $url='http://qr.liantu.com/api.php?text='.$order_number;
        responseApiJson(1,'',$url);
    }

    /*
     * 任务
     */
    public function taskOp(){
        $task=Model()->table('redbag_task')->select();
        foreach ($task as $k=>$v){
            $data[$k]['money']=$v['task_total_sum']/$v['task_number'];
            $data[$k]['title']=$v['task_title'];
            $data[$k]['number']=$v['task_number'];
            $data[$k]['quantity_received']=$v['task_quantity_received'];
            $data[$k]['surplus_quantity']=$v['task_surplus_quantity'];
        }
        if($data){
            responseApiJson(1,'',$data);
        }else{
            responseApiJson(0,'还没任务可做');
        }
    }
    /*
     * 任务详情
     */

    public function taskminiOp(){
        $where['task_id']=$_GET['task_id'];
        $task=Model()->table('redbag_task')->where($where)->find();
        $picture=unserialize($task['task_picture']);
        foreach ($picture as $k=>$v){
            $picture_lsit[]=UPLOAD_SITE_URL.DS.ATTACH_MOBILE.DS.$v;
        }
        $data['contrast_picture']=UPLOAD_SITE_URL.DS.ATTACH_MOBILE.DS.$task['task_contrast_picture'];
        $data['picture']=$picture_lsit;
        $data['content']=$task['task_content'];
        responseApiJson(1,'',$data);
    }

    /*
     * 提交任务
     */
    public function taskpostOp(){
        if(chksubmit()){
            $token=Model('mb_user_token');
            $token_where['token']=$_POST['token'];
            $row=$token->getMbUserTokenInfo($token_where);
            $data['member_id']=$row['member_id'];
            $data['mobile']=$_POST['mobile'];
            $data['picture']=$_POST['picture'];
            $data['task_id']=$_POST['task_id'];
            $data['picture']=$_POST['picture'];
            $data['state']=0;
            $data['addtime']=time();
            $where['task_id']=$_POST['task_id'];
            $task=Model()->table('redbag_task_mini')->where($where)->limit(1)->order('RAND()')->find();
            if($task){
                $member_task=Model()->table('member_task')->insert($data);
                if($member_task){
                    responseApiJson(1,'正在提交审核');
                }
            }else{
                responseApiJson(0,'没有此任务');
            }
        }
    }

    /*
     * 图片对比
     */
    public function picturecontrast(){
        
    }
}
