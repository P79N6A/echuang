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

defined('In33hao') or exit('Access Invalid!');
class brandControl extends mobileHomeControl {
	public function __construct() {
		parent::__construct();
	}

	public function recommend_listOp() {
		$good_list = Model('goods')->getGoodsCommonList(array('goods_commend' => 1), '', 50);
		// dump($good_list);
		// die;
		foreach ($good_list as $key => &$value) {
			$goods_info = Model('goods')->getGoodsInfo(array('goods_commonid' => $value['goods_commonid']));
			$goods_list_common = Model('goods')->getGoodsList(array('goods_commonid' => $goods_info['goods_commonid']));
			$good_list[$key]['goods_id'] = $goods_info['goods_id'];
			$good_list[$key]['goods_image'] = explode('_', $value['goods_image']);

			$good_list[$key]['goods_image'] = cthumb($goods_info['goods_image'], 240, $good_list[$key]['goods_image'][0]);
			// UPLOAD_SITE_URL . '/shop/store/goods/' . $good_list[$key]['goods_image'][0] . '/' . $goods_info['goods_image'];
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
		// dump($good_list);
		// die;
		output_data(array('good_list' => $good_list));
		// $brand_list = Model('brand')->getBrandPassedList(array('brand_recommend' => '1'), 'brand_id,brand_name,brand_pic');
		// if (!empty($brand_list)) {
		// 	foreach ($brand_list as $key => $val) {
		// 		$brand_list[$key]['brand_pic'] = brandImage($val['brand_pic']);
		// 	}
		// }
		// output_data(array('brand_list' => $brand_list));
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
