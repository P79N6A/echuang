<?php
/**
 * 前台品牌分类
 *
 *
 *  (c) ()
 * @license
 * @link
 * @since
 */
header("Access-Control-Allow-Origin: *");
defined('In33hao') or exit('Access Invalid!');
class brandControl extends apiEncryptCommonControl {
	public function __construct() {
		parent::__construct();
	}
    /**
     * 首页
     */
    public function indexOp() {
        $model_mb_special = Model('mb_special');
        $data = $model_mb_special->getMbSpecialIndex($_GET['special_id']);
//        var_dump($data);die();
        $this->_output_special($data, $_GET['type']);
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
        }
        responseApiJson(1, '', $data);
    }

	public function recommend_listOp() {
		//品牌推荐改为商品推荐
		//        $brand_list = Model('brand')->getBrandPassedList(array('brand_recommend' => '1'), 'brand_id,brand_name,brand_pic');
		$good_list = Model('goods')->getGoodsCommonList(array('goods_commend' => 1), '', 50);
        foreach ($good_list as $key => &$value) {
            $goods_info = Model('goods')->getGoodsInfo(array('goods_commonid' => $value['goods_commonid']));
            $goods_list_common = Model('goods')->getGoodsList(array('goods_commonid' => $goods_info['goods_commonid']));
            $good_list[$key]['goods_id'] = $goods_info['goods_id'];
//            $good_list[$key]['goods_image'] = explode('_', $value['goods_image']);
            $good_list[$key]['goods_image'] = UPLOAD_SITE_URL . '/shop/store/goods/' . $good_list[$key]['goods_image'][0] . '/' . $value['goods_image'];
            $goods_golden_bean = array();
            foreach ($goods_list_common as $k => $va) {
                $goods_price = !empty($va['goods_price']) ? $va['goods_price'] : "0";
                $goods_cost = !empty($va['goods_cost']) ? $va['goods_cost'] : "0";
                $goods_golden_bean[] = ncPriceFormat($goods_price - $goods_cost);
            }
            $gold_bean = $this->_sort_goldenbean($goods_golden_bean);
            $good_list[$key]['golden_min_bean'] = !empty($gold_bean['golden_min_bean']) ? $gold_bean['golden_min_bean'] : "0";
            $good_list[$key]['golden_max_bean'] = !empty($gold_bean['golden_max_bean']) ? $gold_bean['golden_max_bean'] : "0";
            unset($good_list[$key]['goods_body']);
        }
        responseApiJson(1, '', array('good_list' => $good_list));
	}

	/**
	 * 金豆排序区间
	 */
	private function _sort_goldenbean($goods_golden_bean) {
		$goods_golden_min = $goods_golden_bean;
		sort($goods_golden_min);
		$golden_bean['golden_min_bean'] = ncPriceFormat($goods_golden_min[0]);
		$goods_golden_max = $goods_golden_bean;
		rsort($goods_golden_max);
		$golden_bean['golden_max_bean'] = ncPriceFormat($goods_golden_max[0]);
		return $golden_bean;
	}
}
