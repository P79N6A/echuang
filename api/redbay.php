<?php
/**
 * 地区
 *
 *
 *  (c) ()
 * @license
 * @link
 * @since
 */
header("Access-Control-Allow-Origin: *");

defined('In33hao') or exit('Access Invalid!');
class redbayControl extends apiEncryptCommonControl {
	public function __construct() {
		parent::__construct();
	}

	public function indexOp() {
		$this->age_listOp();
	}

	/**
	 * 年龄列表
	 */
	public function age_listOp() {
		$age=Model('red_age');
        $age_list=$age->get_array();
		responseApiJson(1, '', array('age_list' => $age_list));
	}

	/**
	 * 兴趣列表
	 */
	public function interest_listOp() {
		$interest=Model('red_interest');
        $interest_list=$interest->get_array();
		responseApiJson(1, '', array('interest_list' => $interest_list));
	}

	/*
	* 发红包列表
	*/
	public function redbag_issueOp(){
		$issue=Model('red_redbag_issue');
		$token=Model('mb_user_token');
		$where['token']=$_POST['token'];
		$row=$token->getMbUserTokenInfo($where);
		if($_POST){
			if($_POST['num']>=1 || $_POST['money']>=0.01){
	            $data['member_id']=$row['member_id'];
	            $data['red_envelopes_num']=$_POST['num'];
	            $data['red_envelopes_contentpicture']=$_POST['content'];
	            $data['red_envelopes_order_number']='RD'.time().rand('0000','9999');
	            $data['red_envelopes_type']=$_POST['type'];
	            $data['red_envelopes_age_id']=$_POST['age_id'];
	            $data['red_envelopes_interest_id']=$_POST['interest_id'];
	            $data['red_envelopes_gender']=$_POST['gender'];
	            $data['link']=$_POST['link'];
	            $data['red_envelopes_province']=$_POST['province'];
	            $data['red_envelopes_city']=$_POST['city'];
	            $data['red_envelopes_area']=$_POST['area'];
	            $data['red_envelopes_start_time']=time();
	            $data['red_envelopes_end_time']=time();
	            $data['state']='0';
	            $data['money']=$_POST['money'];
	            $data['red_envelopes_unissued']=$_POST['num'];
	            $data['red_envelopes_already_issued']='0';
	            $data['payment_state']='0';
	            if($_POST['img_path']){
	               $data['red_envelopes_picture']=serialize($_POST['img_path']); 
	            }
	            if($_POST['red_examine']=='2'){
	                $data['red_redeem_code']=$_POST['redeem_code'];
	                $data['red_title']=$_POST['title'];
	                $data['red_discount']=$_POST['discount'];
	                $data['ter_of_validity']=$_POST['ter_of_validity'];
	            }
	            $rmb=$_POST['money']/$_POST['num'];
	            if($rmb>=0.1){
	                $redbay=$issue->redbayadd($data);
	            }else{
	                responseApiJson(0, '每个红包的金额小于0.1');
	            }
	        }
	    }else{
	    	responseApiJson(0, '金额和红包个数不对');
	    }
        if($redbay==false){
            responseApiJson(0, '发送红包失败');
        }else{
        	$datalist['money']=$data['money'];
        	$datalist['red_envelopes_order_number']=$data['red_envelopes_order_number'];
            responseApiJson(1,'',$datalist);
        }
	}
	/*
	* 图片上传接囗 
	*/
	function filesOp(){
		$file_key = array_keys($_FILES);
		/*
		* 多图片上传接囗 返回array
		*/
        if (count($_FILES) >= 1) {
            for ($i = 0; $i < count($_FILES); $i++) {
                $upload = new UploadFile();
                $upload->set('default_dir', ATTACH_MOBILE);
                $thumb_width = '240,1024';
                $thumb_height = '2048,1024';
                $upload->set('thumb_width', $thumb_width);
                $upload->set('thumb_height', $thumb_height);
                $upload->set('fprefix', '10000010');
                $upload->set('thumb_ext', '_240,_1024');
                $result = $upload->upfile($file_key[$i]);
                if (!$result) {
                    responseApiJson(0, $upload->error, '1');
                }
                $img_path[] = $upload->getSysSetPath() . $upload->file_name;
            }
        }
        responseApiJson(1,'',$img_path);
	}

	/*
	*  范围
	*/
	public function envelopes_locationOp(){
		$range=Model('red_range');
		$token=Model('mb_user_token');
		$where['token']=$_GET['token'];
		$row=$token->getMbUserTokenInfo($where);
		$record=Model('red_expand_area_record');
        $location=Model('red_envelopes_location');
        $advertis=Model('red_advertisement');
        $where_token['member_id']=$row['member_id'];
        $rangelist=$range->rangefind($where_token);
        $memberuser=Model('red_member_user');
		$memberrow=$memberuser->memberuserfind($where_token);
		if($memberrow['age']==false && $memberrow['interest']==false){
			$data_list['information']='false';
		}else{
			$data_list['information']='ture';
			$data['age']		 	 =$memberrow['age'];
			$data['interest']	 	 =$memberrow['interest'];
			$data['gender']		 	 =$memberrow['gender'];
		}
        $timeas=time();
        if((int)$rangelist['overduetime']<$timeas || $rangelist==false){
	        $record_list=$record->getPdRechargeList();
	        $coope=$location->red_getRow('1');
            $num='';
            $addnum='';
	        foreach ($record_list as $key => $value) {
	            $num+=$value['extended_range_num'];
	            $addnum+=$value['add_redbag_num'];
	        }
	        $data_list['location_range']=$num+$coope['red_envelopes_location_range'];
	        $addnum_list=$addnum+$coope['red_envelopes_location_num'];
	        if($_GET['province']){
		        $data['province'] 		 =$_GET['province'];
				$data['city']		 	 =$_GET['city'];
				$data['area']		 	 =$_GET['area'];
				$where_array['city_name']=$_GET['city'];
				$where_array['area_name']=$_GET['area'];
				$advetisement=$advertis->advetisementgetfind($where_array);
				$data_list['advetisement']['clothing']=UPLOAD_SITE_URL.DS.ATTACH_MOBILE.DS.$advetisement['clothing'];
				$data_list['advetisement']['food']=UPLOAD_SITE_URL.DS.ATTACH_MOBILE.DS.$advetisement['food'];
				$data_list['advetisement']['live']=UPLOAD_SITE_URL.DS.ATTACH_MOBILE.DS.$advetisement['live'];
				$data_list['advetisement']['scenic']=UPLOAD_SITE_URL.DS.ATTACH_MOBILE.DS.$advetisement['scenic'];
				$data['location_num']=$addnum_list;
			}
			if((int)$rangelist['overduetime']<$timeas || $rangelist==false){
				$redbag_list=$this->redbag_listOp($data,$row['member_id']);
				if($redbag_list){
					foreach ($redbag_list as $k=> $v) {
						$redbay[]=$v['order_number'];
					}
				}
				$datalist['member_id']=$row['member_id'];
				$datalist['num']=count($redbay);
				$datalist['data']=serialize($redbay);
				$datalist['addtime']=time();
				$datalist['overduetime']=strtotime(date("Y-m-d H",time()).":59:59");
				$rangelistlist=$range->add($datalist);
				if($rangelistlist){
					$data_list['redbay']=$redbay;
					$data_list['location_num']=count($redbay);
		        	$city_lord = Model()->table('city_member')->where(['province_name'=>$_GET['province'],'city_name'=>$_GET['city'],'area_name'=>$_GET['area'],'endtime'=>0])->find();
		//	        var_dump($city_lord);
			        $area_id = Model()->table('area')->where(['area_name'=>$_GET['area']])->field('area_id')->find();
		            $city_lord_info = Model('member')->getMemberInfo(['member_id'=>$city_lord['member_id']]);
		//            var_dump($city_lord_info);
					$data_list['city_lord_info']['avatar'] = BASE_SITE_URL."/data/uploadop/avatar/".$city_lord_info['member_avatar'];
		            $data_list['city_lord_info']['profit'] = Model()->table('city_lord_benefits')->where(['member_id'=>$this->member_info['member_id'],'area'=>$area_id['area_id'],'benefits_time'=>['between',[$city_lord['addtime'],time()]]])->sum('money');
			    	if (!$data_list['city_lord_info']['profit']){
		                $data_list['city_lord_info']['profit'] = 0.00;
		            }
		            responseApiJson(1, '', $data_list);
				}
			}else{
				$data_list['redbay']=$redbay;
				$data_list['location_num']=count($redbay);
	        	$city_lord = Model()->table('city_member')->where(['province_name'=>$_GET['province'],'city_name'=>$_GET['city'],'area_name'=>$_GET['area'],'endtime'=>0])->find();
		//	        var_dump($city_lord);
			        $area_id = Model()->table('area')->where(['area_name'=>$_GET['area']])->field('area_id')->find();
		            $city_lord_info = Model('member')->getMemberInfo(['member_id'=>$city_lord['member_id']]);
		//            var_dump($city_lord_info);
					$data_list['city_lord_info']['avatar'] = BASE_SITE_URL."/data/uploadop/avatar/".$city_lord_info['member_avatar'];
		            $data_list['city_lord_info']['profit'] = Model()->table('city_lord_benefits')->where(['member_id'=>$this->member_info['member_id'],'area'=>$area_id['area_id'],'benefits_time'=>['between',[$city_lord['addtime'],time()]]])->sum('money');
			    	if (!$data_list['city_lord_info']['profit']){
		                $data_list['city_lord_info']['profit'] = 0.00;
		            }
		            responseApiJson(1, '', $data_list);
			}
	    }else{
	    	$record_list=$record->getPdRechargeList();
	    	$num='';
            $addnum='';
	        $coope=$location->red_getRow('1');
	        foreach ($record_list as $key => $value) {
	            $num+=$value['extended_range_num'];
	            $addnum+=$value['add_redbag_num'];
	        }
	        $data_list['location_range']=$num+$coope['red_envelopes_location_range'];
	        if($_GET['province']){
				$where_array['city_name']		 =$_GET['city'];
				$where_array['area_name']		 =$_GET['area'];
				$advetisement=$advertis->advetisementgetfind($where_array);
				$data_list['advetisement']['clothing']=UPLOAD_SITE_URL.DS.ATTACH_MOBILE.DS.$advetisement['clothing'];
				$data_list['advetisement']['food']=UPLOAD_SITE_URL.DS.ATTACH_MOBILE.DS.$advetisement['food'];
				$data_list['advetisement']['live']=UPLOAD_SITE_URL.DS.ATTACH_MOBILE.DS.$advetisement['live'];
				$data_list['advetisement']['scenic']=UPLOAD_SITE_URL.DS.ATTACH_MOBILE.DS.$advetisement['scenic'];
				$i=0;
				$datalist=unserialize($rangelist['data']);
				for($i;$i<count($datalist);$i++){
		            $sdasgdh[]=$datalist[$i];
		        }
				$data_list['redbay']=$sdasgdh;
				$data_list['location_num']=count($sdasgdh);
			}
	    	$city_lord = Model()->table('city_member')->where(['province_name'=>$_GET['province'],'city_name'=>$_GET['city'],'area_name'=>$_GET['area'],'endtime'=>0])->find();
//	        var_dump($city_lord);
	        $area_id = Model()->table('area')->where(['area_name'=>$_GET['area']])->field('area_id')->find();
            $city_lord_info = Model('member')->getMemberInfo(['member_id'=>$city_lord['member_id']]);
//            var_dump($city_lord_info);
			$data_list['city_lord_info']['avatar'] = BASE_SITE_URL."/data/uploadop/avatar/".$city_lord_info['member_avatar'];
            $data_list['city_lord_info']['profit'] = Model()->table('city_lord_benefits')->where(['member_id'=>$this->member_info['member_id'],'area'=>$area_id['area_id'],'benefits_time'=>['between',[$city_lord['addtime'],time()]]])->sum('money');
	    	if (!$data_list['city_lord_info']['profit']){
                $data_list['city_lord_info']['profit'] = 0.00;
            }
            responseApiJson(1, '', $data_list);
	    }
	}

	/*
	* 用地区查询红包订单号
	*/
	public function redbag_listOp($data,$member){
		$where['member_id']=$member;
		$issue=Model('red_redbag_issue');
		$red_envelopes=Model('red_rob_red_envelopes');
        $limit=$data['location_num'];
        $envelopes_list=$issue->envelopesList($data['city'],$data['area'],$limit);
        foreach ($envelopes_list as $key => $value) {
			$data['red_envelopes_order_number']=$value['red_envelopes_order_number'];
        	$issue_list=$issue->getList($data['red_envelopes_order_number'],$data['city'],$data['area']);
        	if($issue_list){
    			$datanumber[$key]['red_envelopes_order_number']=$issue_list['red_envelopes_order_number'];
	        	$datanumber[$key]['order_number']=$issue_list['order_number'];
	        	$datanumber[$key]['id']=$issue_list['id'];
        	}
		}
        return $datanumber;
	}

	/*
	 * 抢红包个人信息
	*/
	public function redbayOp(){
	    $issue=Model('red_redbag_issue');
		$data['order_number']=$_POST['red_envelopes_order_number'];
		$return_list = $issue->listgetfind($data);
		if($return_list){
			$datalist['name']=$return_list['member_name'];
			$datalist['avatar']=UPLOAD_SITE_URL . '/' . ATTACH_AVATAR . '/' .$return_list['member_avatar'];
			responseApiJson(1,'',$datalist);
		}else{
			responseApiJson(0,'这是个错误单号');
		}
	}

	/*
	* 抢红包
	*/
	public function redbaglistOp(){
		$range=Model('red_range');
		$token=Model('mb_user_token');
		$where['token']=$_POST['token'];
		//用token得到用户id号
		$rowtoken=$token->getMbUserTokenInfo($where);
		$memberuser=Model('red_member_user');
		$where_token['member_id']=$rowtoken['member_id'];
		//用member_id得到用户所有信息
		$memberrow=$memberuser->memberuserfind($where_token);
		//个人信息
		if($memberrow['age']==false && $memberrow['interest']==false && $memberrow['gender']==false ){
			responseApiJson(0, '请先完善个人信息');
		}else{
	        $issues=Model('red_redbag_manage');
	        $member=Model('member');
	        $issue=Model('red_redbag_issue');
	        $distribution=Model('red_membership_distribution');
	        $sharebenefits=Model('red_city_lord_share_benefits');
	        $share_benefits=Model('red_share_benefits');
	        $red_envelopes=Model('red_rob_red_envelopes');
	        //传过来的订单信息
	        $data['order_number']=$_POST['red_envelopes_order_number'];
	        $data_number['red_envelopes_order_number']=$_POST['red_envelopes_order_number'];
	        $envelopesdata['order_number']=$data['order_number'];
	        $envelopesdata['member_id']=$rowtoken['member_id'];
	        $where_array['member_id']=$rowtoken['member_id'];
	        //查询是否存已下发的单号
	        $rangelist=$range->rangefind($where_array);
	        //查询此订单是否存在
	        $minifind=$issue->envelopesgetfind($data);
	        //查询订单是否已抢过
	        $findredenvelopes=$red_envelopes->findredenvelopes($envelopesdata);
	        if($findredenvelopes==false){
		        $getrow=$issue->envelopesgetfind($data);
		        if($getrow['red_envelopes_unissued']!=0){
		        	$rowlist=$share_benefits->sharebenefitsfind($data);
			        if($rowlist){
			        	$row=$rowlist['money'];
			        }else{
			        	$row=0;
			        }
			        $rowdistribution=$distribution->red_getRow(1);
			        $rowsharebenefits=$sharebenefits->red_getRow(1);
			        $memberrow=$member->getMemberInfo($where_array);
			        $money=substr((($getrow['money'] - $row)/$getrow['red_envelopes_unissued']),0,5);
			        $roedistribution['money']=$money*($rowdistribution['grab_a_red_envelope']/100);//上级分润
			        $roesharebenefits['money']=$money*($rowsharebenefits['member_rob']/100);//城主分润
			        $roelove['money']=$money*(10/100);//爱心分润
			        $row_bution=$money-$roedistribution['money']-$roesharebenefits['money']-$roelove['money'];//$row_bution用户抢到的红包
			        if($row_bution>=0.001){
			        	$rowbution['money']=$row_bution;
			            $rowbution['red_envelopes_id']=$getrow['red_envelopes_id'];
			            $rowbution['order_number']=$_POST['red_envelopes_order_number'];
			            $rowbution['member_id']=$rowtoken['member_id'];
			            $rowbution['addtime']=time();
                        $rowbution['unissued'] = $getrow['red_envelopes_unissued']-1;
			            //更新用户抢到的红包
			            $addredenvelopes=$red_envelopes->addredenvelopes($rowbution);
			            if($addredenvelopes){
			            	$whererow['member_id']=$rowtoken['member_id'];
			            	$data_envelopes['balance']=$memberrow['balance']+$row_bution;
			            	$updateredenvelopes=$red_envelopes->updateredenvelopes($whererow,$data_envelopes);
			            	if($updateredenvelopes){
			            		$money_logo = Model('red_money_logo');
                                $dataarray['member_id']=$rowtoken['member_id'];
                                $dataarray['member_mobile']=$getrow['member_mobile'];
                                $dataarray['member_name']=$getrow['member_name'];
                                $dataarray['sn']=$data['order_number'];
                                $dataarray['type']='redbag_rob';
                                $dataarray['operate_amount']=$getrow['balance'];
                                $dataarray['surplus_amount']=$data_envelopes['balance'];
                                $dataarray['add_time']=time();
                                $dataarray['content']='会员'.$dataarray['member_mobile']. '抢到的:'.$row_bution;
                                //更新余额明细
                                $money_logo->logoadd($dataarray);
			            	}
			            	$data_list['red_envelopes_already_issued']=$getrow['red_envelopes_already_issued']+1;
				            $data_list['red_envelopes_unissued']=$getrow['red_envelopes_unissued']-1;
				            $mini['state']='1';
				            $data_issue['red_envelopes_id']=$getrow['red_envelopes_id'];
				            //更改红包己领取数量
				            $modifyredenvelopes=$issue->modify($data_issue,$data_list);
				            //更改小红包状态
				            $mini=$issue->miniupdate($data,$mini);
				            if($modifyredenvelopes && $_POST['red_envelopes_order_number']=$minifind['order_number']){
				            	$rangeupdate['range_id']=$rangelist['range_id'];
				                $unserialize=unserialize($rangelist['data']);
								for($i=0;$i<=count($unserialize);$i++){
						            if($unserialize[$i]==$_POST['red_envelopes_order_number']){
						                unset($unserialize[$i]);
						            }else{
						                $redbay[]=$unserialize[$i];
						            }
						        }
						        if(empty($redbay)==false){
						        	$redbayrow=array_filter($redbay);
						        	$dataupdate['data']=serialize($redbayrow);
							        $dataupdate['num']=count($redbayrow);
							        //更改个人红包的个数
							        $range->rangeupdate($rangeupdate,$dataupdate);
						        }else{
						        	$dataupdate['data']=serialize($redbay);
							        $dataupdate['num']=count($redbay);
							        //更改个人红包的个数
							        $range->rangeupdate($rangeupdate,$dataupdate);
						        }
				            }
				            if($getrow['red_examine']==1){
			                	$picture=unserialize($getrow['red_envelopes_picture']);
			                	if($picture==false){
			                		$picture_data[]='';
			                	}else{
			                		$rowpicture=array_filter($picture);
							        for ($i=0; $i <count($rowpicture) ; $i++) { 
							            $picture_data[]=UPLOAD_SITE_URL.DS.ATTACH_MOBILE.DS.$picture[$i];
							        }
			                	}
						        $datalist['red_envelopes_picture']=$picture_data;
				                $datalist['red_envelopes_contentpicture']=$getrow['red_envelopes_contentpicture'];
				                $datalist['link']=$getrow['link'];
				                $datalist['money']=substr($row_bution,0,5);
				                $datalist['name']=$memberrow['member_name'];
				                $datalist['avatar']=UPLOAD_SITE_URL . '/' . ATTACH_AVATAR . '/' .$memberrow['member_avatar'];
				            }else{
				            	$datalist['money']=substr($row_bution,0,5);
				                $picture=unserialize($getrow['red_envelopes_picture']);
			                	if($picture==false){
			                		$picture_data[]='';
			                	}else{
			                		$rowpicture=array_filter($picture);
							        for ($i=0; $i <count($rowpicture) ; $i++) { 
							            $picture_data[]=UPLOAD_SITE_URL.DS.ATTACH_MOBILE.DS.$picture[$i];
							        }
			                	}
						        $datalist['red_envelopes_picture']=$picture_data;
				                $datalist['red_envelopes_contentpicture']=$getrow['red_envelopes_contentpicture'];
				                $datalist['link']=$getrow['link'];
				                $datalist['red_redeem_code']=$getrow['red_redeem_code'];
				                $datalist['red_title']=$getrow['red_title'];
				                $datalist['red_discount']=$getrow['red_discount'];
				                $datalist['ter_of_validity']=date('Y-m-d H:i:s',$getrow['ter_of_validity']);
				                $datalist['member_id']=$rowtoken['member_id'];
				                $datalist['name']=$memberrow['member_name'];
				                $datalist['avatar']=UPLOAD_SITE_URL . '/' . ATTACH_AVATAR . '/' .$memberrow['member_avatar'];
				            }
				            $this->roeloveOp($roelove['money'],$rowtoken['member_id']);//爱心分润
				            $ashdgashgdj=$this->roedOp($roedistribution['money'],$getrow,$rowtoken['member_id']);//上级分润
				            responseApiJson(1,'',$datalist);
				            //$this->roesOp($roesharebenefits['money'],$getrow,$rowtoken['member_id']);//城主分润
			        	}else{
			            	responseApiJson(0, '数据添加错误');
			            }
			        }else{
			            responseApiJson(0, '金额小于0.001');
			        }
		        }else{
		        	$rangeupdate['range_id']=$rangelist['range_id'];
	                $unserialize=unserialize($rangelist['data']);
					for($i=0;$i<=count($unserialize);$i++){
			            if($unserialize[$i]==$_POST['red_envelopes_order_number']){
			                unset($unserialize[$i]);
			            }else{
			                $redbay[]=$unserialize[$i];
			            }
			        }
			        $dataupdate['data']=serialize(array_filter($redbay));
			        $dataupdate['num']=count(array_filter($redbay));
			        $sgdh=$range->rangeupdate($rangeupdate,$dataupdate);
		        	responseApiJson(0, '此红包已发完');
		        }
		    }else{
		    	$rangeupdate['range_id']=$rangelist['range_id'];
                $unserialize=unserialize($rangelist['data']);
				for($i=0;$i<=count($unserialize);$i++){
		            if($unserialize[$i]==$_POST['red_envelopes_order_number']){
		                unset($unserialize[$i]);
		            }else{
		                $redbay[]=$unserialize[$i];
		            }
		        }
		        $dataupdate['data']=serialize($redbay);
		        $dataupdate['num']=count($redbay);
		        $sgdh=$range->rangeupdate($rangeupdate,$dataupdate);
		    	responseApiJson(0, '您已领取过此红包');
		    }
		}
	}
	/*
	* 爱心基金add
	*/
	public function roeloveOp($roelove,$member_id){
		$member=Model('member');
		$where['member_id']=$member_id;
		$data_list=$member->getMemberInfo($where);
		$data['lovefund']=$data_list['lovefund']+$roelove;
		$data_array=$member->editMember($where,$data);
	}

	/*
	* 爱心查询
	*/
	public function lovefundOp(){
		$token=Model('mb_user_token');
		$where['token']=$_POST['token'];
		$rowtoken=$token->getMbUserTokenInfo($where);
		$member=Model('member');
		$where_array['member_id']=$rowtoken['member_id'];
		$memberrow=$member->getMemberInfo($where_array);
		if($memberrow){
			$data['lovefund']=$memberrow['lovefund'];
			responseApiJson(1,'',$data);
		}else{
			responseApiJson(0, '请先登录');
		}
	}
	/*
	* 用户抢红包给上级分润
	*/
	public function roedOp($money,$number,$rowtoken){
		$manage=Model('red_city_owner_manage');
		$member=Model('member');
		$benefits=Model('red_share_benefits');
		$where['member_id']=$number['member_id'];
		$data_array=$member->getMemberInfo($where);
		$find=$manage->getfind($rowtoken);
		$where_array=$find['me_member_id'];
        $find_va = $manage->getfind($where_array);
		if($find_va['member_level'] != 0){
			$data['money']=$money;
	        $data['order_number']='MERE'.substr($number['red_envelopes_order_number'],2);
	        $data['me_member_id']=$number['member_id'];
	        $data['envelopes_id']=$number['red_envelopes_id'];
	        $data['member_id']=$find['me_member_id'];
	        $data['state']=1;
	        $data['addtime']=time();
	        $row_data=$benefits->sharebenefitsadd($data);
			$rowdata['balance']=$data_array['balance']+$money;
			$data_array=$member->editMember($where_array,$rowdata);
			$money_logo = Model('red_money_logo');
            $dataarray['member_id']=$where['member_id'];
            $dataarray['member_mobile']=$data_array['member_mobile'];
            $dataarray['member_name']=$data_array['member_name'];
            $dataarray['sn']=$data['order_number'];
            $dataarray['type']='redbag_benefits';
            $dataarray['operate_amount']=$data_array['balance'];
            $dataarray['surplus_amount']=$rowdata['balance'];
            $dataarray['add_time']=time();
            $dataarray['content']='会员'.$dataarray['member_mobile']. '您的下级抢到的红包分润:'.$money;
            $money_logo->logoadd($dataarray);
	    }
	}
	/*
	* 余额查询
	*/
	public function memberOp(){
		$token=Model('mb_user_token');
		$where['token']=$_POST['token'];
		$rowtoken=$token->getMbUserTokenInfo($where);
		$member=Model('member');
		$where_array['member_id']=$rowtoken['member_id'];
		$memberrow=$member->getMemberInfo($where_array);
		if($memberrow){
			$data['balance']=$memberrow['balance'];
			responseApiJson(1,'',$data);
		}else{
			responseApiJson(0, '请先登录');
		}
	}

	/*
	* 个人信息设置
	*/
	public function personal_informationaddOp(){
		$memberuser=Model('red_member_user');
		$token=Model('mb_user_token');
		$where['token']=$_POST['token'];
		$rowtoken=$token->getMbUserTokenInfo($where);
		$where_array['member_id']=$rowtoken['member_id'];
		$wherefind['me_member_id']=$rowtoken['member_id'];
		$memberrow=$memberuser->memberuserfind($where_array);
		if($memberrow){
			$timeas=date("Y-m-d",time());
				if(($memberrow['age']==false && $_POST['age']) || ($memberrow['interest']==false && $_POST['interest']) || ($memberrow['gender']==false && $_POST['gender'])){
					if($memberrow['age']==false AND $_POST['age']){
						$data['age']=$timeas-$_POST['age'];
					}
					if($memberrow['interest']==false AND $_POST['interest']){
						$data['interest']=$_POST['interest'];
					}
					if($memberrow['gender']==false AND $_POST['gender']){
							$data['gender']=$_POST['gender'];
					}
				$row=$memberuser->memberuserupdate($wherefind,$data);
				if($row){
					responseApiJson(1,'您的个人信息设置成功');
				}
			}else{
				responseApiJson(0, '您的个人信息设置有误');
			}
		}else{
			responseApiJson(0, '系统内部错误');
		}
	}

	/*
	* 城主
	*/
	public function city_listOp(){
		$price=Model('red_city_price');
		$data=$price->getPdRechargeList($where_array);
		if($data){
			responseApiJson(1,'',$data);
		}else{
			responseApiJson(0, '您所查询的区没有数据');
		}
	}

	/*
	* 所有城主区域
	*/
	public function cityOp(){
		$price=Model('red_city_price');
		$where_array['area_name'] = array('like', '%' . $_GET['area'] . '%');
		$data=$price->getPdRechargeList($where_array);
		if($data){
			responseApiJson(1,'',$data);
		}else{
			responseApiJson(0, '您所查询的区没有数据');
		}
	}

	/*
	* 城主原价
	*/
	public function cityprimaryOp(){
		$cityprice=Model('red_city_price');
		$where['province_name'] =substr($_POST['province'],0,strlen($_POST['province'])-3);
		$where['city_name']		= $_POST['city'];
		$where['area_name']		= $_POST['area'];
		$pricefind=$cityprice->envelopesget($where);
		if($pricefind){
			responseApiJson(1,'',$pricefind);
		}else{
			responseApiJson(0, '此城不开放');
		}
	}
	/*
	* 城主竞拍
	*/
	public function citypriceOp(){
		$token=Model('mb_user_token');
		$member=Model('member');
		$where_array['token']=$_POST['token'];
		$rowtoken=$token->getMbUserTokenInfo($where_array);
		$cityprice=Model('red_city_price');
		$where['province_name'] =substr($_POST['province'],0,strlen($_POST['province'])-3);
		$where['city_name']		= $_POST['city'];
		$where['area_name']		= $_POST['area'];
		if($_POST['member_id']){
			$data['last_lord_id'] = base64_decode($_POST['member_id']);
			$data['last_lord_money']=$_POST['rob_money'];
		}else{
			$data['last_lord_id'] = '';
		}
		$pricefind=$cityprice->envelopesget($where);
		if($pricefind){
			$percentage				= $_POST['percentage'];
			$data['money']			= $pricefind['present_price'] + (($pricefind['present_price']*$percentage)/100);
			$data['member_id']		= $rowtoken['member_id'];
			$data['order_number']	= 'RDCT'.time().rand('0000','9999');
			$data['addtime']		= time();
			$data['area_id']		= $pricefind['area'];
			$data['state']			= '0';
			//添加订单
			$price = $cityprice->lordadd($data);
			if($price){
				$datalist['money']=$data['money'];
	        	$datalist['price']=$price;
	            responseApiJson(1,'',$datalist);
			}else{
				responseApiJson(0, '竞拍订单失败');
			}
		}else{
			responseApiJson(0, '此地区没有竞拍');
		}
	}

	/*
	* 城主竞拍情况
	*/
	public function citysituationOp(){
		$cityprice=Model('red_city_price');
		$benefits=Model('red_city_lord_share_benefits');
		$where['area_name']		= $_POST['area'];
		$data=$cityprice->citylog($where);
		$databenefits=$benefits->red_getRow('1');
		$data_array=array();
		if($data){
			foreach ($data as $key => $value) {
				$data_array[$key]['member_id']=base64_encode($value['member_id']);
				$data_array[$key]['primarycity_name']=$value['name'];
				$data_array[$key]['primarycity_avatar']=UPLOAD_SITE_URL. '/' .ATTACH_AVATAR.'/'.$value['avatar'];
				$data_array[$key]['robcity_name']=$value['Iname'];
				$data_array[$key]['robcity_avatar']=UPLOAD_SITE_URL. '/' .ATTACH_AVATAR.'/'.$value['Iavatar'];
				$data_array[$key]['primary_money']=($value['money']*$databenefits['city_rebate'])/100;
				$data_array[$key]['rob_money']=$value['money'];
			}
			responseApiJson(1,'',$data_array);
		}else{
			responseApiJson(1,'',$data_array);
		}
	}

	/*
	* 用户抢红包给城主分润
	*/
	public function roesOp($money,$number,$member_id){
		$member=Model('member');
		$benefits=Model('red_share_benefits');
		$administration=Model('red_city_administration');
		$where['member_id']=$number['member_id'];
		$data_array=$member->getMemberInfo($where);
		$find=$manage->getfind($rowtoken);
		if($find['redbay_memder']==1){
			$money_logo = Model('red_money_logo');
			$data_array['state']='3';
            $data_array['money']=$money;
            $data_array['member_id']=$data['member_id'];
            $data_array['addtime']=time();
            $money_logo->logoadd($data_array);
	    }
	}
}
