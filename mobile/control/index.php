<?php
/**
 * 手机端首页控制
 *
 * @
 * @license
 * @link
 * @since
 *
 */
header("Access-Control-Allow-Origin: *");
defined('In33hao') or exit('Access Invalid!');
class indexControl extends mobileHomeControl {

	public function __construct() {
		parent::__construct();
	}

	/**
	 * 首页
	 */
	public function indexOp() {
		$model_mb_special = Model('mb_special');
		$data = $model_mb_special->getMbSpecialIndex();
		$this->_output_special($data, $_GET['type']);
	}

	/**
	 * 专题
	 */
	public function specialOp() {
		$model_mb_special = Model('mb_special');
		$info = $model_mb_special->getMbSpecialInfoByID($_GET['special_id']);
		$list = $model_mb_special->getMbSpecialItemUsableListByID($_GET['special_id']);
		$data = array_merge($info, array('list' => $list));
		$this->_output_special($data, $_GET['type'], $_GET['special_id']);
	}

	/**
	 * 输出专题
	 */
	private function _output_special($data, $type = 'json', $special_id = 0) {
		$model_special = Model('mb_special');
		if ($_GET['type'] == 'html') {
			$html_path = $model_special->getMbSpecialHtmlPath($special_id);
			if (!is_file($html_path)) {
				ob_start();
				Tpl::output('list', $data);
				Tpl::showpage('mb_special');
				file_put_contents($html_path, ob_get_clean());
			}
			header('Location: ' . $model_special->getMbSpecialHtmlUrl($special_id));
			die;
		} else {
			output_data($data);
		}
	}

	/**
	 * android客户端版本号
	 */
	public function apk_versionOp() {
		$version = C('mobile_apk_version');
		$url = C('mobile_apk');
		if (empty($version)) {
			$version = '';
		}
		if (empty($url)) {
			$url = '';
		}

		output_data(array('version' => $version, 'url' => $url));
	}

	/**
	 * 默认搜索词列表
	 */
	public function search_key_listOp() {
		$list = @explode(',', C('hot_search'));
		if (!$list || !is_array($list)) {
			$list = array();
		}
		if ($_COOKIE['hisSearch'] != '') {
			$his_search_list = explode('~', $_COOKIE['hisSearch']);
		}
		if (!$his_search_list || !is_array($his_search_list)) {
			$his_search_list = array();
		}
		output_data(array('list' => $list, 'his_list' => $his_search_list));
	}

	/**
	 * 热门搜索列表
	 */
	public function search_hot_infoOp() {
		if (C('rec_search') != '') {
			$rec_search_list = @unserialize(C('rec_search'));
		}
		$rec_search_list = is_array($rec_search_list) ? $rec_search_list : array();
		$result = array();
		if (!empty($rec_search_list)) {
			$result = $rec_search_list[array_rand($rec_search_list)];
		}
		output_data(array('hot_info' => $result ? $result : array()));
	}

	/**
	 * 高级搜索
	 */
	public function search_advOp() {
		$area_list = Model('area')->getAreaList(array('area_deep' => 1), 'area_id,area_name');
		if (C('contract_allow') == 1) {
			$contract_list = Model('contract')->getContractItemByCache();
			$_tmp = array();
			$i = 0;
			foreach ($contract_list as $k => $v) {
				$_tmp[$i]['id'] = $v['cti_id'];
				$_tmp[$i]['name'] = $v['cti_name'];
				$i++;
			}
		}
		output_data(array('area_list' => $area_list ? $area_list : array(), 'contract_list' => $_tmp));
	}

	/**
	 * 公告列表
	 */
	public function getggOp() {
		if (!empty($_GET['ac_id']) && intval($_GET['ac_id']) > 0) {
			$article_class_model = Model('article_class');
			$article_model = Model('article');
			$condition = array();

			$child_class_list = $article_class_model->getChildClass(intval($_GET['ac_id']));
			$ac_ids = array();
			if (!empty($child_class_list) && is_array($child_class_list)) {
				foreach ($child_class_list as $v) {
					$ac_ids[] = $v['ac_id'];
				}
			}
			$ac_ids = implode(',', $ac_ids);
			$condition['ac_ids'] = $ac_ids;
			$condition['article_show'] = '1';
			$article_list = $article_model->getArticleList($condition, 5);
			//$article_type_name = $this->article_type_name($ac_ids);
			//output_data(array('article_list' => $article_list, 'article_type_name'=> $article_type_name));
			output_data(array('article_list' => $article_list));
		} else {
			output_error('缺少参数:文章类别编号');
		}
	}

    /**
     * 轮播图 +文章
     */
    public function newsBulletinOp(){
        //轮播图
        $model_mb_special = Model('mb_special');
        $special_item_list = $model_mb_special->getMbSpecialItemListByID($model_mb_special::INDEX_SPECIAL_ID);
        foreach ($special_item_list as $k=>$v){
            if($v['item_type'] == 'adv_list'){
                if(empty($v['item_data']['item'])){
                    $data['sowing_map'][] =  UPLOAD_SITE_URL.'/'.ATTACH_ARTICLE.'/'.'sowing_map_default.jpg';
                }else{
                    foreach ($v['item_data']['item'] as $k2=>$v2){
                        $data['sowing_map'][] = UPLOAD_SITE_URL.'/'.mobile.'/'.special.'/'.s0.'/'.$k2;
                    }
                }
            }
        }
        //文章信息
        $condition['ac_id'] = 8;
        $condition['article_show'] = 1;
        $data['article_list'] = Model('article')->getApiArticleList($condition);
        foreach ($data['article_list'] as $k=>$v){
            if (!empty($v['article_image'])){
                $data['article_list'][$k]['article_image'] = UPLOAD_SITE_URL.'/'.ATTACH_ARTICLE.'/'.$v['article_image'];
            }
            if (!empty($v['article_title'])){
                $data['article_list'][$k]['article_title'] = html_entity_decode($v['article_title']);
            }
        }
        //var_dump($data);
        responseApiJson(1, "",$data);
    }
}
