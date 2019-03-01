<?php
/**
 * 家族中心
 */
class member_familyControl extends mobileMemberControl {

	public function __construct() {
		parent::__construct();
	}

	/**
	 * memberInviteOp 会员邀请码
	 * @return [type] [description]
	 */
	public function memberInviteOp() {
		$mobile = $this->member_info['member_mobile'];
		$encode_mobile = base64_encode($mobile);
		$myurl = WAP_SITE_URL . "/tmpl/member_system/register.html?invite=" . $encode_mobile;
		$str_member = "memberqr_" . $this->member_info['member_id'];
		$myurl_src = UPLOAD_SITE_URL . DS . "shop" . DS . "member" . DS . $str_member . '.png';
		$imgfile = BASE_UPLOAD_PATH . DS . "shop" . DS . "member" . DS . $str_member . '.png';
		if (!file_exists($imgfile)) {
			require_once BASE_RESOURCE_PATH . DS . 'phpqrcode' . DS . 'index.php';
			$PhpQRCode = new PhpQRCode();

			$PhpQRCode->set('pngTempDir', BASE_UPLOAD_PATH . DS . "shop" . DS . "member" . DS);
			$PhpQRCode->set('date', $myurl);
			$PhpQRCode->set('pngTempName', $str_member . '.png');
			$PhpQRCode->init();
		}
		output_data(array('invite' => array('myurl_src' => $myurl_src)));
	}

	/**
	 * memberDirectListOp 我的直推
	 * @return [type] [description]
	 */
	public function memberDirectListOp() {
		$direct_list = array();
		$member_id = $this->member_info['member_id'];
		$model_member_extend = Model('member_extend');
		$fields = 'member_id,member_name,member_mobile,member_time,member_avatar';
		$invite_list = $model_member_extend->getMemberExtendList(array('invite_id' => $member_id), $fields, 'union');
		foreach ($invite_list as $v) {
			$param = array();
			$param['id'] = $v['member_id'];
			$param['name'] = $v['member_name'];
			$param['mobile'] = $v['member_mobile'];
			$param['time'] = $v['member_time'] ? date('Y-m-d H:i:s', $v['member_time']) : '';
			$param['avatar'] = getMemberAvatar($v['member_avatar']);
			$direct_list[] = $param;
		}
		output_data(array('list' => $direct_list));
	}

	/**
	 * accessFamilyTreeOp 接点人族谱图
	 * @return [type] [description]
	 */
	public function accessFamilyTreeOp() {
		$binary_tree = Model('member_extend')->buildBinaryTreeArr($this->member_info['member_id']);
		$performance['performance1'] = $binary_tree['performance1'];
		$performance['performance2'] = $binary_tree['performance2'];
		$performance['surplus1'] = $binary_tree['surplus1'];
		$performance['surplus2'] = $binary_tree['surplus2'];
		if ($_POST) {
			$member_id = $_POST['member_id'];
			$new_arr = $this->_build_arr($member_id);
		} else {
			$new_arr = $this->_build_arr();
		}
		output_data(array('map_arr' => $new_arr, 'performance' => $performance));
	}

	/**
	 * _build_arr 组装族谱图数组
	 * @param  string $member_id [description]
	 * @return [type]            [description]
	 */
	private function _build_arr($member_id = "") {
		if (empty($member_id)) {
			$binary_tree = Model('member_extend')->buildBinaryTreeArr($this->member_info['member_id']);
		} else {
			$binary_tree = Model('member_extend')->buildBinaryTreeArr($member_id, false);
		}
		if (!empty($binary_tree)) {
			$level1_arr['member_id'] = $binary_tree['member_id'];
			$level1_arr['member_mobile'] = $binary_tree['member_mobile'];
			$new_arr[0][] = $level1_arr;
			if (!empty($binary_tree['child'])) {
				foreach ($binary_tree['child'] as $key => $value) {
					if ($value['member_id'] == 0) {
						$level2_arr['member_id'] = "";
						$level2_arr['member_mobile'] = "";
						$member_info = Model('member')->getMemberInfoByID($value['access_id']);
						$level2_arr['access_mobile'] = $member_info['member_mobile'];
						$new_arr[1][] = $level2_arr;
					} else {
						$level2_arr['member_id'] = $value['member_id'];
						$level2_arr['member_mobile'] = $value['member_mobile'];
						$new_arr[1][] = $level2_arr;
					}
					if (!empty($value['child'])) {
						foreach ($value['child'] as $k => $v) {
							if ($v['member_id'] == 0) {
								$level3_arr['member_id'] = "";
								$level3_arr['member_mobile'] = "";
								$member_info1 = Model('member')->getMemberInfoByID($v['access_id']);
								$level3_arr['access_mobile'] = $member_info1['member_mobile'];
								$new_arr[2][] = $level3_arr;
							} else {
								$level3_arr['member_id'] = $v['member_id'];
								$level3_arr['member_mobile'] = $v['member_mobile'];
								$new_arr[2][] = $level3_arr;
							}
						}
					} else {
						$level3_arr['access_mobile'] = "";
						$level3_arr['member_id'] = "";
						$level3_arr['member_mobile'] = "";
						$new_arr[2][] = $level3_arr;
						$new_arr[2][] = $level3_arr;
					}
				}
			}
		}
		return $new_arr;
	}

    /**
     * 我的直推、间推人数
     */
    public function member_teamOp(){
        //直推人数
        $model_member_extend = Model('member_extend');
        $d_invite_list = $model_member_extend->getMemberExtendList(array('invite_id' => $this->member_info['member_id']), 'member_id', 'union');
//        var_dump($invite_list);
        $data['d_invite_num'] = (string) count($d_invite_list);
        //间接推荐人数
        $child_mobile_arr = $model_member_extend->getInviteChildMobileArr($this->member_info['member_id']);
        array_shift($child_mobile_arr);
        $data['ind_invite_num'] = (string) (count($child_mobile_arr) - $data['d_invite_num']);
        responseApiJson(1,'',$data);
    }

    /**
     * 我的直推各等级人数
     */
    public function direct_level_numOp()
    {
        //直推各等级人数
        $model_member_extend = Model('member_extend');
        $d_child_level_arr = $model_member_extend->getMemberExtendList(array('invite_id' => $this->member_info['member_id']), 'member_level', 'union');
        $direct_level_num = array_count_values(array_column($d_child_level_arr, 'member_level'));
        ksort($direct_level_num);
        $direct_level_num2 = array();
        for ($i=0;$i<=5;$i++) {
            if (!isset($direct_level_num[$i])){
                $direct_level_num[$i] = 0;
            }
            $direct_level_num2[str_replace(array(0, 1, 2, 3, 4, 5), array("ordinary_user", "vip", "shopkeeper", "partner", "senior_partner", "strategic_partner"), $i)] = (string)$direct_level_num[$i];
        }
        responseApiJson(1,'',$direct_level_num2);
    }


    /**
     * 我的间推各等级人数
     */
    public function in_direct_level_numOp(){
        $model_member_extend = Model('member_extend');
        //直推
        $d_child_level_arr = $model_member_extend->getMemberExtendList(array('invite_id' => $this->member_info['member_id']), 'member_level', 'union');
        $direct_level_num = array_count_values(array_column($d_child_level_arr, 'member_level'));
        for ($i=0;$i<=5;$i++) {
            if (!isset($direct_level_num[$i])) {
                $direct_level_num[$i] = 0;
            }
        }
        ksort($direct_level_num);
        //全部
        $child_level_arr = $model_member_extend->getAllChildLevelArr($this->member_info['member_id']);
        array_shift($child_level_arr);
        $all_direct_level_num = @array_count_values($child_level_arr);
        for ($i=0;$i<=5;$i++) {
            if (!isset($all_direct_level_num[$i])) {
                $all_direct_level_num[$i] = 0;
            }
        }
        ksort($all_direct_level_num);
        //间推各等级人数
        for ($i=0;$i<=5;$i++){
            $in_direct_level_num[$i] = $all_direct_level_num[$i] - $direct_level_num[$i];
        }
        ksort($in_direct_level_num);
        $in_direct_level_num2 = array();
        foreach ($in_direct_level_num as $k=>$v){
            $in_direct_level_num2[str_replace(array(0,1,2,3,4,5),array("ordinary_user", "vip", "shopkeeper", "partner", "senior_partner", "strategic_partner"),$k)] = (string)$v;
        }
        responseApiJson(1,'',$in_direct_level_num2);
    }

    /**
     * member_direct_listOp 我的直推列表
     * @return [type] [description]
     */
    public function member_direct_listOp() {
        $level = $_GET['level'];
        $direct_list = array();
        $member_id = $this->member_info['member_id'];
        $model_member_extend = Model('member_extend');
        $fields = 'member_id,member_name,member_mobile,member_time,member_avatar';
        $invite_list = $model_member_extend->getMemberExtendList(array('invite_id' => $member_id,'member_level'=>$level), $fields, 'union');
        $total_num = $model_member_extend->gettotalpage();
        foreach ($invite_list as $v) {
            $param = array();
            $param['id'] = $v['member_id'];
            $param['name'] = $v['member_name'];
            $param['mobile'] = $v['member_mobile'];
            $param['time'] = $v['member_time'] ? date('Y-m-d H:i:s', $v['member_time']) : '';
            $param['avatar'] = getMemberAvatar($v['member_avatar']);
            $param['level'] = str_replace(array(0,1,2,3,4,5),array("ordinary_user", "vip", "shopkeeper", "partner", "senior_partner", "strategic_partner"),$level);
            $direct_list[] = $param;
        }
        responseApiJson(1, '', array('list' => $direct_list, 'total_num' => $total_num));
    }

    /**
     * 我的间推列表
     */
    public function member_in_direct_listOp(){
        $level = $_GET['level'];
        $model_member_extend = Model('member_extend');
        $member_id = $this->member_info['member_id'];
        $all_child_id = $model_member_extend->getInviteChildInfoArr($member_id);
        array_shift($all_child_id);
        $invite_list = array_column($model_member_extend->getMemberExtendList(array('invite_id' => $member_id,'member_level'=>$level), 'member_id', 'union'),'member_id');
        $in_direct_id = array_diff($all_child_id,$invite_list);
        $child_info = $model_member_extend->getMemberExtendList2(array('member_id'=>['in',$in_direct_id],'member_level'=>$level),'member_name,member_mobile,from_unixtime(member_time) as add_time,member_avatar');

        $param = array();
        foreach ($child_info as $k=>$v){
            $param[$k]['name'] = $v['member_name'];
            $param[$k]['mobile'] = $v['member_mobile'];
            $param[$k]['time'] = $v['add_time'];
            $param[$k]['avatar'] = BASE_SITE_URL.'/data/upload/shop/avatar/'.$v['member_avatar'];
            $param[$k]['level'] = str_replace(array(0,1,2,3,4,5),array("ordinary_user", "vip", "shopkeeper", "partner", "senior_partner", "strategic_partner"),$level);
        }
        $total_num = $model_member_extend->gettotalpage();
        responseApiJson(1,'',array('list' => $param, 'total_num' => $total_num));
    }

}