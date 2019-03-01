<?php
/**
 * 发红包
 *
 *
 *
 * *  (c) ()
 * @license
 * @link
 * @since
 */
defined('In33hao') or exit('Access Invalid!');
class red_redbag_issueModel extends Model {
    /*
    *读取列表发红包
    *
    */
    public function getPdRechargeList($condition = array(), $pagesize = '', $fields = '*', $limit = ''){
        return $this->table('member_envelopes')->where($condition)->field($fields)->limit($limit)->page($pagesize)->select();
    }

    /*
    *读取单个发红包
    *
    */
    public function getenvelopesfind($condition){
        return $this->table('member_envelopes')->where($condition)->find();
    }
    /*
     *  读取发布优恵劵
     */
    public function getexcellentsecurities($where){
        return $this->table('redbag_mini')->where($where)->select();
    }

    /*
     *  读取单条优恵劵
     */
    public function getexcel($where){
        return $this->table('redbag_mini')->where($where)->find();
    }
    /**
     * 读取单个发红包
     * @param array $where
     *
     */
    public function listgetfind($where){
        return $this->table('redbay_mini')->where($where)->find();
    }
    /*
    * 读取列表发红包
    */
    public function envelopesList($city,$area,$limit) {
        return $this->table('red_envelopes')->where("(red_envelopes_city='".$city."' || red_envelopes_city='不限') and (red_envelopes_area='".$area."' || red_envelopes_area='不限') and state=2 and red_envelopes_unissued!=0")->limit($limit)->order('RAND()')->select();
    }

    /**
     * 读取列表发红包的小红包
     * @param array $condition
     *
     */
    public function getList($red_envelopes_order_number,$city,$area,$limit=1) {
        return $this->table('redbay_mini')->where("(red_envelopes_city='".$city."' || red_envelopes_city='') && (red_envelopes_area='".$area."' || red_envelopes_area='') && state=2 and red_envelopes_unissued!=0 and mini_state=0 and red_envelopes_order_number='".$red_envelopes_order_number."'")->limit($limit)->order('RAND()')->find();
    }
    /*
     * 查询发红包的订单号
     * 返回订单信息
     */
    public function envelopesget($where){
        return $this->table('red_envelopes')->where($where)->find();
    }

    /*
     * 查询发红包的订单号
     * 返回订单信息
     */
    public function envelopesgetfind($where){
        return $this->table('redbay_mini')->where($where)->find();
    }

    /*
    * 更改状态
    */
   public function miniupdate($where,$data){
       return $this->table('redbag_mini')->where($where)->update($data);
   }

   /*
    * 更改状态
    */
   public function modify($where,$data){
       return $this->table('red_envelopes')->where($where)->update($data);
   }
    /*
    * 发红包add
    */
    public function redbayadd($data){
      return  $this->table('red_envelopes')->insert($data);
    }
    /*
    * 生成小包
    */
    public function miniadd($data){
        return  $this->table('redbag_mini')->insert($data);
    }
}
