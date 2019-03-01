<?php
/**
 * 会员等级模块
 * 
 */
defined('In33hao') or exit('Access Invild!');
class levelModel extends Model {
	public function __construct() {
		parent::__construct('member_level');
	}

	/**
	 * getMemberLevelList 获取会员等级列表
	 * @param  array  $condition [description]
	 * @param  string $fields    [description]
	 * @param  [type] $page      [description]
	 * @param  string $order     [description]
	 * @param  string $limit     [description]
	 * @return [type]            [description]
	 */
	public function getMemberLevelList($condition = array(), $fields = '*', $page = null, $order = 'ml_id asc', $limit = '') {
		$res =  $this->field($fields)->where($condition)->page($page)->order($order)->limit($limit)->select();
		return $res;
	}

	/**
	 * getMemberLevelInfo 获取会员等级信息
	 * @param  [type]  $condition [description]
	 * @param  string  $fields    [description]
	 * @param  boolean $master    [description]
	 * @return [type]             [description]
	 */
	public function getMemberLevelInfo($condition, $fields = '*', $master = false) {
        return $this->field($fields)->where($condition)->master($master)->find();
    }

    /**
     * addMemberLevel 添加会员等级
     * @param [type] $data [description]
     */
	public function addMemberLevel($data) {
		$insert = $this->insert($data);
		if (!$insert) {
			throw new Exception('添加会员等级失败');
		} else {
			return $insert;
		}
	}

	/**
	 * editMemberLevel 更新会员等级
	 * @param  [type] $condition [description]
	 * @param  [type] $data      [description]
	 * @return [type]            [description]
	 */
	public function editMemberLevel($condition, $data) {
		$update = $this->where($condition)->update($data);
		if (!$update) {
			throw new Exception('更新会员等级失败');
		}
	}

	public function getLevelArr() {
		$arr = array();
		$list = $this->getMemberLevelList();
		foreach ($list as $value) {
			$ml_id = $value['ml_id'];
			unset($value['ml_id']);
			$arr[$ml_id] = $value;
		}
		return $arr;
	}

    /**
     * 获取推荐某一等级会员的直推奖
     * @param $member_level
     * @return mixed
     */
    public function getDirectPrize($member_level){
        return $this->where(array('ml_id'=>$member_level))->field(['ml_direct_prize'])->find();
    }

    /**
     * 获取升级时需要直推的该VIP人数
     * @param $member_level int 该VIP的的等级
     * @return mixed
     */
    public function getDirectVipNum($member_level){
        $res = $this->field('ml_direct_vip_num')->where(['ml_id'=>$member_level])->find();
        if (!$res){
            return false;
        }
        return $res;
    }

    /**
     * 获取升级时团队需要达到该VIP的人数
     * @param $member_level int 该VIP的的等级
     * @return bool
     */
    public function getTeamVipNum($member_level){
        $res = $this->field('ml_team_vip_num')->where(['ml_id'=>$member_level])->find();
        if (!$res){
            return false;
        }
        return $res;
    }

    /**
     * 添加会员升级记录
     * @param $member_id
     * @param $member_name
     * @param $member_level
     * @return bool
     */
    public function memberUpLevelRecord($member_id,$member_name,$member_level){
        $insert['member_id'] = $member_id;
        $insert['member_name'] = $member_name;
        $res = str_replace(array(1,2,3,4,5),array('Vip','店主','合伙人','高级合伙人','战略合伙人'),$member_level);
        $insert['member_level'] = $res;
        $insert['up_time'] = time();
        $insert['content'] = "会员".$member_name."，团队达到升级标准，升级为".$res;
        try{
            $this->table('member_uplevel_record')->insert($insert);
        }catch(Exception $e){
            return false;
        }
        return true;
    }

    /**
     * 获取对应等级配送的产品数量
     * @param $level
     * @return mixed
     */
    public function getGiveProduct($level){
        return $this->field('ml_give_product')->where(['ml_id'=>$level])->find();
    }

    /**
     * 获取对应等级配送的产品数量
     * @param $level
     * @return mixed
     */
    public function getProductPrice(){
        return $this->field('ml_franchise_fee')->where(['ml_id'=>1])->find();
    }


    /**
     * 获取升级各等级所需直推、团队人数数组
     * @return null
     */
    public function getUpLevelNeedArr(){
        $res = $this->table('member_level')->field('ml_direct_vip_num,ml_team_vip_num')->select();
        return $res;
    }

    /**
     * 获取等级名称
     * @param $member_level
     * @return mixed
     */
    public function getOneMemberLevelName($member_level){
        $level_name = $this->table('member_level')->where(['ml_id'=>$member_level])->field('ml_level_name')->find();
        return $level_name['ml_level_name'];
    }

    /**
     * 获取折扣
     * @param $member_level
     * @return mixed
     */
    public function getMemberDiscount($member_level){
        if ($member_level == 0){
            $res = 1;
        }else{
            $res = $this->table('member_level')->where(['ml_id'=>$member_level])->field('ml_discount_ratio')->find();
            $res = $res['ml_discount_ratio'];
        }
        return $res;
    }

    /**
     * 获取会员加盟费数组
     * @return mixed
     */
    public function franchiseFeeArr(){
        return $this->table('member_level')->field('ml_franchise_fee')->select();
    }


    /**
     * 获取会员高级设置列表
     */
    public function getMemberSettingList(){
        return $member_level_setting = $this->table('member_level')->select();
    }


    public function getMemberSystemSetting(){
        return $this->table('member_system_set')->select();
    }

    /**
 * 添加会员升级记录
 * @param $insert
 * @return mixed
 */
    public function addUpLevelRecord($insert){
        $res = $this->table('member_uplevel_record')->insert($insert);
        return $res;
    }

}