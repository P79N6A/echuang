<?php
/**
 * 商品
 *
 * @
 * @license
 * @link
 * @since
 */

defined('In33hao') or exit('Access Invalid!');

class goodsControl extends apiEncryptCommonControl
{
    private $PI = 3.14159265358979324;
    private $x_pi = 0;

    public function __construct()
    {
        $this->x_pi = 3.14159265358979324 * 3000.0 / 180.0;
        parent::__construct();
    }

    /**
     * 商品列表
     */
    public function goods_listOp()
    {
        $model_goods = Model('goods');
        $model_search = Model('search');
        $_GET['is_book'] = 0;

        //查询条件
        $condition = array();
        // ==== 暂时不显示定金预售商品，手机端未做。  ====
        $condition['is_book'] = 0;
        if (!empty($_GET['gc_id']) && intval($_GET['gc_id']) > 0) {
            $condition['gc_id'] = $_GET['gc_id'];
        }
        if (!empty($_GET['keyword'])) {
            // V5.5  全文搜
            //$condition['goods_name|goods_jingle'] = array('like', '%' . $_GET['keyword'] . '%');
            $keys = explode(' ', $_GET['keyword']);
            $datakey = array();
            foreach ($keys as $key => $val) {
                $datakey[] = array('like', '%' . $val . '%');
            }
            $datakey[] = 'and';
            $condition['goods_name|goods_jingle'] = $datakey;

            if ($_COOKIE['hisSearch'] == '') {
                $his_sh_list = array();
            } else {
                $his_sh_list = explode('~', $_COOKIE['hisSearch']);
            }
            if (strlen($_GET['keyword']) <= 20 && !in_array($_GET['keyword'], $his_sh_list)) {
                if (array_unshift($his_sh_list, $_GET['keyword']) > 8) {
                    array_pop($his_sh_list);
                }
            }
            setcookie('hisSearch', implode('~', $his_sh_list), time() + 2592000, '/', SUBDOMAIN_SUFFIX ? SUBDOMAIN_SUFFIX : '', false);

        } elseif (!empty($_GET['barcode'])) {
            $condition['goods_barcode'] = $_GET['barcode'];
        } elseif (!empty($_GET['b_id']) && intval($_GET['b_id'] > 0)) {
            $condition['brand_id'] = intval($_GET['b_id']);
        }
        $price_from = preg_match('/^[\d.]{1,20}$/', $_GET['price_from']) ? $_GET['price_from'] : null;
        $price_to = preg_match('/^[\d.]{1,20}$/', $_GET['price_to']) ? $_GET['price_to'] : null;

        //所需字段
        $fieldstr = "goods_id,goods_commonid,store_id,goods_name,goods_jingle,goods_price,goods_promotion_price,goods_promotion_type,goods_marketprice,goods_image,goods_salenum,evaluation_good_star,evaluation_count,goods_cost";

        $fieldstr .= ',is_virtual,is_presell,is_fcode,have_gift,goods_jingle,store_id,store_name,is_own_shop';

        //排序方式
        $order = $this->_goods_list_order($_GET['key'], $_GET['order']);

        //全文搜索搜索参数
        $indexer_searcharr = $_GET;
        //搜索消费者保障服务
        $search_ci_arr = array();
        $_GET['ci'] = trim($_GET['ci'], '_');
        if ($_GET['ci'] && $_GET['ci'] != 0 && is_string($_GET['ci'])) {
            //处理参数
            $search_ci = $_GET['ci'];
            $search_ci_arr = explode('_', $search_ci);
            $indexer_searcharr['search_ci_arr'] = $search_ci_arr;
        }
        if ($_GET['own_shop'] == 1) {
            $indexer_searcharr['type'] = 1;
        }
        $indexer_searcharr['price_from'] = $price_from;
        $indexer_searcharr['price_to'] = $price_to;

        //优先从全文索引库里查找
        list($goods_list, $indexer_count) = $model_search->indexerSearch($indexer_searcharr, $this->page);
        if (!is_null($goods_list)) {
            $goods_list = array_values($goods_list);
            pagecmd('setEachNum', $this->page);
            pagecmd('setTotalNum', $indexer_count);
        } else {
            //查询消费者保障服务
            $contract_item = array();
            if (C('contract_allow') == 1) {
                $contract_item = Model('contract')->getContractItemByCache();
            }
            //消费者保障服务
            if ($contract_item && $search_ci_arr) {
                foreach ($search_ci_arr as $ci_val) {
                    $condition["contract_{$ci_val}"] = 1;
                }
            }

            if ($price_from && $price_from) {
                $condition['goods_promotion_price'] = array('between', "{$price_from},{$price_to}");
            } elseif ($price_from) {
                $condition['goods_promotion_price'] = array('egt', $price_from);
            } elseif ($price_to) {
                $condition['goods_promotion_price'] = array('elt', $price_to);
            }
            if ($_GET['gift'] == 1) {
                $condition['have_gift'] = 1;
            }
            if ($_GET['own_shop'] == 1) {
                $condition['store_id'] = 1;
            }
            if (intval($_GET['area_id']) > 0) {
                $condition['areaid_1'] = intval($_GET['area_id']);
            }

            //抢购和限时折扣搜索
            $_tmp = array();
            if ($_GET['groupbuy'] == 1) {
                $_tmp[] = 1;
            }
            if ($_GET['xianshi'] == 1) {
                $_tmp[] = 2;
            }
            if ($_tmp) {
                $condition['goods_promotion_type'] = array('in', $_tmp);
            }
            unset($_tmp);

            //虚拟商品
            if ($_GET['virtual'] == 1) {
                $condition['is_virtual'] = 1;
            }
            $goods_list = $model_goods->getGoodsListByColorDistinct($condition, $fieldstr, $order);
        }
        //处理商品列表(抢购、限时折扣、商品图片)
        $goods_list = $this->_goods_list_extend($goods_list);
        foreach ($goods_list as $key => $value) {
            $goods_info = Model('goods')->getGoodsInfoByID($value['goods_id']);
            $goods_list_common = Model('goods')->getGoodsList(array('goods_commonid' => $goods_info['goods_commonid']));
            $goods_golden_bean = array();
            foreach ($goods_list_common as $k => $va) {
                $goods_price = !empty($va['goods_price']) ? $va['goods_price'] : "0";
                $goods_cost = !empty($va['goods_cost']) ? $va['goods_cost'] : "0";
                $goods_golden_bean[] = ncPriceFormat($goods_price - $goods_cost);
            }
            $gold_bean = $this->_sort_goldenbean($goods_golden_bean);
            $goods_list[$key]['golden_min_bean'] = !empty($gold_bean['golden_min_bean']) ? $gold_bean['golden_min_bean'] : "0";
            $goods_list[$key]['golden_max_bean'] = !empty($gold_bean['golden_max_bean']) ? $gold_bean['golden_max_bean'] : "0";
        }
        $page_count = $model_goods->gettotalpage();
        responseApiJson(1, '', array('goods_list' => $goods_list), mobile_page($page_count));
    }

    /**
     * 金豆排序区间
     */
    private function _sort_goldenbean($goods_golden_bean)
    {
        $goods_golden_min = $goods_golden_bean;
        sort($goods_golden_min);
        $golden_bean['golden_min_bean'] = ncPriceFormat($goods_golden_min[0]);
        $goods_golden_max = $goods_golden_bean;
        rsort($goods_golden_max);
        $golden_bean['golden_max_bean'] = ncPriceFormat($goods_golden_max[0]);
        return $golden_bean;
    }

    /**
     * 商品列表排序方式
     */
    private function _goods_list_order($key, $order)
    {
        $result = 'is_own_shop desc,goods_id desc';
        if (!empty($key)) {

            $sequence = 'desc';
            if ($order == 1) {
                $sequence = 'asc';
            }

            switch ($key) {
                //销量
                case '1':
                    $result = 'goods_salenum' . ' ' . $sequence;
                    break;
                //浏览量
                case '2':
                    $result = 'goods_click' . ' ' . $sequence;
                    break;
                //价格
                case '3':
                    $result = 'goods_price' . ' ' . $sequence;
                    break;
            }
        }
        return $result;
    }

    /**
     * 处理商品列表(抢购、限时折扣、商品图片)
     */
    private function _goods_list_extend($goods_list)
    {
        //获取商品列表编号数组
        $goodsid_array = array();
        foreach ($goods_list as $key => $value) {
            $goodsid_array[] = $value['goods_id'];
        }

        $sole_array = Model('p_sole')->getSoleGoodsList(array('goods_id' => array('in', $goodsid_array)));
        $sole_array = array_under_reset($sole_array, 'goods_id');
        foreach ($goods_list as $key => $value) {
            $goods_common_info = Model('goods')->getGoodsCommonInfoByID($value['goods_commonid']);
            $goods_list[$key]['sole_flag'] = false;
            $goods_list[$key]['goods_name'] = $goods_common_info['goods_name'];
            $goods_list[$key]['group_flag'] = false;
            $goods_list[$key]['xianshi_flag'] = false;
            if (!empty($sole_array[$value['goods_id']])) {
                $goods_list[$key]['goods_price'] = $sole_array[$value['goods_id']]['sole_price'];
                $goods_list[$key]['sole_flag'] = true;
            } else {
                $goods_list[$key]['goods_price'] = $value['goods_promotion_price'];
                switch ($value['goods_promotion_type']) {
                    case 1:
                        $goods_list[$key]['group_flag'] = true;
                        break;
                    case 2:
                        $goods_list[$key]['xianshi_flag'] = true;
                        break;
                }

            }

            //商品图片url
            $goods_list[$key]['goods_image_url'] = cthumb($value['goods_image'], 360, $value['store_id']);

            unset($goods_list[$key]['goods_promotion_type']);
            unset($goods_list[$key]['goods_promotion_price']);
            unset($goods_list[$key]['goods_commonid']);
        }

        return $goods_list;
    }

    /**
     * 商品详细页
     */
    public function goods_detailOp()
    {
        $goods_id = intval($_GET['goods_id']);
        $area_id = intval($_GET['area_id']);
        // 商品详细信息
        $model_goods = Model('goods');
        $goods_detail = $model_goods->getGoodsDetail($goods_id);
        if (empty($goods_detail)) {
            responseApiJson(0, '商品不存在', '');
        }

        // 默认预订商品不支持手机端显示
        if ($goods_detail['is_book']) {
            responseApiJson(0, '预订商品不支持手机端显示', '');
        }

        $goods_list = $model_goods->getGoodsContract(array(0 => $goods_detail['goods_info']));
        $goods_detail['goods_info'] = $goods_list[0];
        //推荐商品
        $hot_sales = $model_goods->getGoodsCommendList($goods_detail['goods_info']['store_id'], 8);
        $goodsid_array = array();
        foreach ($hot_sales as $value) {
            $goodsid_array[] = $value['goods_id'];
        }
        $sole_array = Model('p_sole')->getSoleGoodsList(array('goods_id' => array('in', $goodsid_array)));
        $sole_array = array_under_reset($sole_array, 'goods_id');
        $goods_commend_list = array();
        foreach ($hot_sales as $value) {
            $goods_commend = array();
            $goods_commend['goods_id'] = $value['goods_id'];
            $goods_commend['goods_name'] = $value['goods_name'];
            $goods_commend['goods_price'] = $value['goods_price'];
            $goods_commend['goods_promotion_price'] = $value['goods_promotion_price'];
            if (!empty($sole_array[$value['goods_id']])) {
                $goods_commend['goods_promotion_price'] = $sole_array[$value['goods_id']]['sole_price'];
            }
            $goods_commend['goods_image_url'] = cthumb($value['goods_image'], 240);
            $goods_commend_list[] = $goods_commend;
        }

        $goods_detail['goods_commend_list'] = $goods_commend_list;
        $store_info = Model('store')->getStoreInfoByID($goods_detail['goods_info']['store_id']);
        $goods_detail['store_info']['store_id'] = $store_info['store_id'];
        $goods_detail['store_info']['store_name'] = $store_info['store_name'];
        $goods_detail['store_info']['member_id'] = $store_info['member_id'];
        $goods_detail['store_info']['member_name'] = $store_info['member_name'];
        $goods_detail['store_info']['is_own_shop'] = $store_info['is_own_shop'];
        $goods_detail['store_info']['is_only'] = $store_info['is_only'];
        //添加QQ IM
        $goods_detail['store_info']['store_qq'] = $store_info['store_qq'];
        $goods_detail['store_info']['node_chat'] = C('node_chat');

        $goods_detail['store_info']['goods_count'] = $store_info['goods_count'];

        $storeCredit = array();
        $percentClassTextMap = array(
            'equal' => '平',
            'high' => '高',
            'low' => '低',
        );
        foreach ((array)$store_info['store_credit'] as $k => $v) {
            $v['percent_text'] = $percentClassTextMap[$v['percent_class']];
            $storeCredit[$k] = $v;
        }
        $goods_detail['store_info']['store_credit'] = $storeCredit;

        //商品详细信息处理
        $goods_detail = $this->_goods_detail_extend($goods_detail);
        // 如果已登录 判断该商品是否已被收藏&&添加浏览记录
        if ($member_id = $this->getMemberIdIfExists()) {
            $c = (int)Model('favorites')->getGoodsFavoritesCountByGoodsId($goods_id, $member_id);
            $goods_detail['is_favorate'] = $c > 0;

            QueueClient::push('addViewedGoods', array('goods_id' => $goods_id, 'member_id' => $member_id));
        }
        if (!$goods_detail['goods_info']['is_virtual']) {
            // 店铺优惠券
            $condition = array();
            $condition['voucher_t_gettype'] = 3;
            $condition['voucher_t_state'] = 1;
            $condition['voucher_t_end_date'] = array('gt', time());
            $condition['voucher_t_store_id'] = array('in', $store_info['store_id']);
            $voucher_template = Model('voucher')->getVoucherTemplateList($condition);
            if (!empty($voucher_template)) {
                foreach ($voucher_template as $val) {
                    $param = array();
                    $param['voucher_t_id'] = $val['voucher_t_id'];
                    $param['voucher_t_price'] = $val['voucher_t_price'];
                    $param['voucher_t_limit'] = $val['voucher_t_limit'];
                    $param['voucher_t_end_date'] = date('Y年m月d日', $val['voucher_t_end_date']);
                    $goods_detail['voucher'][] = $param;
                }
            }
        }
        //修复抢购限制
        $goods_detail['IsHaveBuy'] = (string)0;
        $promotion_type = $goods_detail['goods_info']["promotion_type"];
        if ($promotion_type == 'groupbuy') {
            $upper_limit = Model('groupbuy')->getUpperLimit($goods_detail['goods_info']['goods_commonid']);
            if ($upper_limit > 0) {
                $goods_commonid = $goods_detail['goods_info']['goods_commonid'];
                //查询该商品其它所有规格goods_id
                $goods_id_arr = Model('goods')->getGoodsIdArr(array('goods_commonid'=>$goods_commonid));
                foreach ($goods_id_arr as $k=>$v){
                    $order_goods_list = Model('order')->getOrderGoodsList(array('goods_id' => $v['goods_id'], 'buyer_id' => $member_id, 'goods_type' => 2));

                    if ($order_goods_list) {
                        //取得上次购买的活动编号(防一个商品参加多次团购活动的问题)
                        $promotions_id = $order_goods_list[0]["promotions_id"];
                        //用此编号取数据，检测是否这次活动的订单商品。
                        $model_groupbuy = Model('groupbuy');
                        $groupbuy_info = $model_groupbuy->getGroupbuyInfo(array('groupbuy_id' => $promotions_id));
                        if ($groupbuy_info && time()<strtotime($groupbuy_info['end_time_text'])) {
                            $goods_detail['IsHaveBuy'] = (string)1;
                            break;
                        } else {
                            $goods_detail['IsHaveBuy'] = (string)0;
                        }
                    }
                }
            }
        }
        // 评价列表
        $goods_eval_list = Model("evaluate_goods")->getEvaluateGoodsList(array('geval_goodsid' => $goods_id), null, '10', 'geval_id desc', 'geval_id,geval_scores,geval_content,geval_addtime,geval_frommembername,geval_explain');
        //$goods_eval_list = Logic('member_evaluate')->evaluateListDity($goods_eval_list);
        foreach ($goods_eval_list as $key => $value) {
            $goods_eval_list[$key]['geval_addtime'] = date('Y-m-d H:i:s', $goods_eval_list[$key]['geval_addtime']);
            if (empty($goods_eval_list[$key]['geval_explain'])) {
                $goods_eval_list[$key]['geval_explain'] = "";
            }
        }
        $goods_detail['goods_eval_list'] = $goods_eval_list;

        //评价信息
        $goods_evaluate_info = Model('evaluate_goods')->getEvaluateGoodsInfoByGoodsID($goods_id);
        $goods_detail['goods_evaluate_info'] = $goods_evaluate_info;

        $goods_detail['goods_hair_info'] = $this->_calc($area_id, $goods_id);
        $goods_detail['web_goods_url'] = BASE_SITE_URL . "/wap/tmpl/product_info.html?goods_id=" . $goods_id;

        //获取会员购买该商品的利润（折扣金额）
        if (empty($_REQUEST['token'])) {
            $goods_detail['goods_info']['discount_amount'] = (string)0;
        } else {
            $member_id = $this->getMemberIdIfExists();
            $member_level = Model('member_extend')->getMemberExtendInfo(array('member_id' => $member_id), 'member_level', 'union');
            $discount = Model('level')->getMemberDiscount($member_level['member_level']);
            if ($goods_detail['goods_info']['goods_promotion_type'] != 0) {
                $discount_amount = $goods_detail['goods_info']['goods_promotion_price'] * (1 - $discount);
            } else {
                $discount_amount = $goods_detail['goods_info']['goods_price'] * (1 - $discount);
            }
            $goods_detail['goods_info']['discount_amount'] = $discount_amount;
        }
        responseApiJson(1, '', $goods_detail);
    }

    /**
     * 商品详细信息处理
     */
    private function _goods_detail_extend($goods_detail)
    {
        //整理商品规格
        unset($goods_detail['spec_list']);
        $goods_detail['spec_list'] = $goods_detail['spec_list_mobile'];
        unset($goods_detail['spec_list_mobile']);

        //整理商品图片
        unset($goods_detail['goods_image']);
        $goods_detail['goods_image'] = implode(',', $goods_detail['goods_image_mobile']);
        unset($goods_detail['goods_image_mobile']);
        //商品链接
        $goods_detail['goods_info']['goods_url'] = urlShop('goods', 'index', array('goods_id' => $goods_detail['goods_info']['goods_id']));

        //整理数据
        //        unset($goods_detail['goods_info']['goods_commonid']);
        $goods_detail['goods_info']['goods_image'] = BASE_SITE_URL . "/data/upload/shop/store/goods/1/" . $goods_detail['goods_info']['goods_image'];
        unset($goods_detail['goods_info']['gc_id']);
        unset($goods_detail['goods_info']['gc_name']);
        unset($goods_detail['goods_info']['store_id']);
        unset($goods_detail['goods_info']['store_name']);
        unset($goods_detail['goods_info']['brand_id']);
        unset($goods_detail['goods_info']['brand_name']);
        unset($goods_detail['goods_info']['type_id']);
//		unset($goods_detail['goods_info']['goods_image']);
        unset($goods_detail['goods_info']['goods_body']);
        unset($goods_detail['goods_info']['goods_state']);
        unset($goods_detail['goods_info']['goods_stateremark']);
        unset($goods_detail['goods_info']['goods_verify']);
        unset($goods_detail['goods_info']['goods_verifyremark']);
        unset($goods_detail['goods_info']['goods_lock']);
        unset($goods_detail['goods_info']['goods_addtime']);
        unset($goods_detail['goods_info']['goods_edittime']);
        unset($goods_detail['goods_info']['goods_selltime']);
        unset($goods_detail['goods_info']['goods_show']);
        unset($goods_detail['goods_info']['goods_commend']);
        unset($goods_detail['goods_info']['explain']);
        unset($goods_detail['goods_info']['buynow_text']);
        unset($goods_detail['groupbuy_info']);
        unset($goods_detail['xianshi_info']);

        return $goods_detail;
    }

    /**
     * 商品详细页
     */
    public function goods_bodyOp()
    {
        header("Access-Control-Allow-Origin:*");
        $goods_id = intval($_GET['goods_id']);

        $model_goods = Model('goods');

        $result1 = $model_goods->getGoodsInfoByID($goods_id, 'goods_commonid,goods_body,mobile_body');
        if (empty($result1['goods_body'])) {
            unset($result1['goods_body']);
        }

        if (empty($result1['mobile_body'])) {
            unset($result1['mobile_body']);
        }

        $result2 = $model_goods->getGoodsCommonInfoByID($result1['goods_commonid'], 'goods_commonid,goods_body,mobile_body,plateid_top,plateid_bottom');
        $goods_info = array_merge($result2, $result1);
        $pattern1 = '<img.*?src="(.*?)">';
        //$pattern2= '/<img((?!src).)*src[\s]*=[\s]*[\'"](?<src>[^\'"]*)[\'"]/i';
        //$html = '<img id="pic" name="pic" src="aaa.jpg" style="width: 640px;">';
        preg_match_all($pattern1, $goods_info['goods_body'], $matches);
        // 手机商品描述
        if ($goods_info['mobile_body'] != '') {
            $mobile_body_array = unserialize($goods_info['mobile_body']);
            $mobile_body = '';
            if (is_array($mobile_body_array)) {
                foreach ($mobile_body_array as $val) {
                    switch ($val['type']) {
                        case 'text':
                            $mobile_body .= '<div>' . $val['value'] . '</div>';
                            break;
                        case 'image':
                            $mobile_body .= '<img src="' . $val['value'] . '">';
                            break;
                    }
                }
            }
            $goods_info['mobile_body'] = $mobile_body;
        }

        $model_plate = Model('store_plate');
        $goods_body = '';
        if ($goods_info['plateid_top'] > 0) {
            $plate_top = $model_plate->getStorePlateInfoByID($goods_info['plateid_top']);
            if (!empty($plate_top)) {
                $goods_body .= '<div class="top-template">' . $plate_top['plate_content'] . '</div>';
            }

        }
        $goods_body .= '<div class="default">' . $goods_info['goods_body'] . '</div>';
        // 底部关联版式
        if ($goods_info['plateid_bottom'] > 0) {
            $plate_bottom = $model_plate->getStorePlateInfoByID($goods_info['plateid_bottom']);
            if (!empty($plate_bottom)) {
                $goods_body .= '<div class="bottom-template">' . $plate_bottom['plate_content'] . '</div>';
            }

        }
        $goods_info['goods_body'] = $goods_body;
        $url = BASE_SITE_URL . "/wap/tmpl/product_info.html?goods_id=" . $goods_id;
        responseApiJson(1, '', array('url' => $url, 'goods_images' => $matches[1]));
        Tpl::output('goods_info', $goods_info);
        Tpl::showpage('goods_body');
    }

    function startWith($str, $needle)
    {
        return strpos($str, $needle) === 0;
    }

    public function goods_evaluateOp()
    {
        $goods_id = intval($_GET['goods_id']);
        $type = intval($_GET['type']);

        $condition = array();
        $condition['geval_goodsid'] = $goods_id;
        switch ($type) {
            case '1':
                $condition['geval_scores'] = array('in', '5,4');
                break;
            case '2':
                $condition['geval_scores'] = array('in', '3,2');
                break;
            case '3':
                $condition['geval_scores'] = array('in', '1');
                break;
            case '4':
                $condition['geval_image|geval_image_again'] = array('neq', '');
                break;
            case '5':
                $condition['geval_content_again'] = array('neq', '');
                break;
        }

        //查询商品评分信息
        $model_evaluate_goods = Model("evaluate_goods");
        $goods_eval_list = $model_evaluate_goods->getEvaluateGoodsList($condition, 10);
        foreach ($goods_eval_list as $key => $value) {
            $goods_eval_list[$key]['geval_addtime'] = date('Y-m-d H:i:s', $goods_eval_list[$key]['geval_addtime']);
            if (empty($goods_eval_list[$key]['geval_explain'])) {
                $goods_eval_list[$key]['geval_explain'] = "";
            }
        }
        $goods_eval_list = Logic('member_evaluate')->evaluateListDity($goods_eval_list);

        responseApiJson(1, '', array('goods_eval_list' => $goods_eval_list));
    }

    /**
     * 商品详细页运费显示
     *
     * @return unknown
     */
    public function calcOp()
    {
        $area_id = intval($_GET['area_id']);
        $goods_id = intval($_GET['goods_id']);
        $goods_n = 1;
        if (intval($_GET['num'])) {
            $goods_n = intval($_GET['num']);
        }

        responseApiJson(1, '', $this->_calc($area_id, $goods_id, $goods_n));
    }

    public function _calc($area_id, $goods_id, $goods_n = 1)
    {
        $goods_info = Model('goods')->getGoodsInfo(array('goods_id' => $goods_id), 'transport_id,store_id,goods_freight,goods_storage,goods_trans_v');
        $store_info = Model('store')->getStoreInfoByID($goods_info['store_id']);
        if ($area_id <= 0) {
            if (strpos($store_info['deliver_region'], '|')) {
                $store_info['deliver_region'] = explode('|', $store_info['deliver_region']);
                $store_info['deliver_region_ids'] = explode(' ', $store_info['deliver_region'][0]);
            }
            $area_id = intval($store_info['deliver_region_ids'][1]);
            $area_name = $store_info['deliver_region'][1];
        }
        if ($goods_info['transport_id'] && $area_id > 0) {
            $goods_info['goods_n'] = $goods_n;
            $freight_total = Model('transport')->goods_trans_calc($goods_info, $area_id);
            if ($freight_total > 0) {
                if ($store_info['store_free_price'] > 0) {
                    $freight_total = '运费：' . $freight_total . ' 元，店铺满 ' . $store_info['store_free_price'] . ' 元 免运费';
                } else {
                    $freight_total = '运费：' . $freight_total . ' 元';
                }
            } else {
                if ($freight_total === false) {
                    $if_store = false;
                }
                $freight_total = '免运费';
            }
        } else {
            $freight_total = $goods_info['goods_freight'] > 0 ? '运费：' . $goods_info['goods_freight'] . ' 元' : '请选择地区';
        }

        return array('content' => $freight_total, 'if_store_cn' => $if_store === false ? '无货' : ($goods_info['goods_storage'] > 0 ? '有货' : '无货'), 'if_store' => $if_store === false ? false : true, 'area_name' => $area_name ? $area_name : '全国');
    }

    /**
     * 取得店铺分店地址
     */
    public function store_o2o_addrOp()
    {
        $store_id = intval($_GET['store_id']);
        $lng = $_GET['lng'];
        $lat = $_GET['lat'];
        $condition = array();
        $condition['store_id'] = $store_id;
        $map_list = Model('store_map')->getStoreMapList($condition, '', '', 'map_id asc');
        $map_new_list = array();
        foreach ($map_list as $k => $v) {
            $map_new_list[$k]['key'] = $k;
            $map_new_list[$k]['map_id'] = $v['map_id'];
            $map_new_list[$k]['name_info'] = $v['name_info'];
            $map_new_list[$k]['address_info'] = $v['address_info'];
            $map_new_list[$k]['phone_info'] = $v['phone_info'];
            $map_new_list[$k]['bus_info'] = $v['bus_info'];
            $map_new_list[$k]['city'] = $v['baidu_city'];
            $map_new_list[$k]['district'] = $v['baidu_district'];
            $map_new_list[$k]['lng'] = $v['baidu_lng'];
            $map_new_list[$k]['lat'] = $v['baidu_lat'];
            $gcj = $this->bd_decrypt($v['baidu_lat'], $v['baidu_lng']);
            $map_new_list[$k]['gcjlng'] = $gcj['lon'];
            $map_new_list[$k]['gcjlat'] = $gcj['lat'];
            if ($lng != '' && $lat != '') {
                $map_new_list[$k]['distance'] = $this->getDistance($lat, $lng, $gcj['lat'], $gcj['lon']);
            } else {
                $map_new_list[$k]['distance'] = '';
            }
        }
        if ($lng != '' && $lat != '') {
            usort($map_new_list, function ($a, $b) {
                if ($a['distance'] == $b['distance']) {
                    return 0;
                }

                return ($a['distance'] < $b['distance']) ? -1 : 1;
            });
        }
        foreach ($map_new_list as $k => $v) {
            $map_new_list[$k]['distance'] = $this->parseDistance($v['distance']);
        }
        responseApiJson(0, array('addr_list' => $map_new_list), '');
    }

    public function auto_completeOp()
    {
        $data = Model('search')->autoComplete(array('term' => $_GET['term']));
        foreach ($data as $k => $v) {
            $data[$k] = $v['value'];
        }
        responseApiJson(1, array('list' => $data), '');
    }

    /**
     * 经纬度转换
     * @param unknown $bdLat
     * @param unknown $bdLon
     * @return multitype:number
     */
    public function bd_decrypt($bdLat, $bdLon)
    {
        $x = $bdLon - 0.0065;
        $y = $bdLat - 0.006;
        $z = sqrt($x * $x + $y * $y) - 0.00002 * sin($y * $this->x_pi);
        $theta = atan2($y, $x) - 0.000003 * cos($x * $this->x_pi);
        $gcjLon = $z * cos($theta);
        $gcjLat = $z * sin($theta);
        return array('lat' => $gcjLat, 'lon' => $gcjLon);
    }

    /**
     * @desc 根据两点间的经纬度计算距离
     * @param float $lat 纬度值
     * @param float $lng 经度值
     */
    private function getDistance($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6367000; //approximate radius of earth in meters

        /*
                     Convert these degrees to radians
                    to work with the formula
        */

        $lat1 = ($lat1 * pi()) / 180;
        $lng1 = ($lng1 * pi()) / 180;

        $lat2 = ($lat2 * pi()) / 180;
        $lng2 = ($lng2 * pi()) / 180;

        /*
                     Using the
                    Haversine formula

                    http://en.wikipedia.org/wiki/Haversine_formula

                    calculate the distance
        */

        $calcLongitude = $lng2 - $lng1;
        $calcLatitude = $lat2 - $lat1;
        $stepOne = pow(sin($calcLatitude / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($calcLongitude / 2), 2);
        $stepTwo = 2 * asin(min(1, sqrt($stepOne)));
        $calculatedDistance = $earthRadius * $stepTwo;

        return round($calculatedDistance);
    }

    private function parseDistance($num = 0)
    {
        $num = floatval($num);
        if ($num >= 1000) {
            $num = $num / 1000;
            return str_replace('.0', '', number_format($num, 1, '.', '')) . 'km';
        } else {
            return $num . 'm';
        }
    }

    /**
     * @desc 根据商品规格处理商品信息
     */
    public function getGoodsInfoBySpecOp()
    {
        $goods_spec = array();
        if (!is_array($_POST['spec_list'])) {
            if (strpos($_POST['spec_list'], "&quot") !== false) {
                $goods_spec_str1 = str_replace("&quot", "\"", $_POST['spec_list']);
                $goods_spec_str2 = str_replace(";", "", $goods_spec_str1);
                $goods_spec = json_decode($goods_spec_str2, true);
            }
        } else {
            $goods_spec = $_POST['spec_list'];
        }
        $goods_commonid['goods_commonid'] = $_POST['goods_commonid'];
        $goods_common_list = Model('goods')->getGoodsList($goods_commonid);
        $goods_id = "";
        foreach ($goods_common_list as $key => $value) {
            $sort1 = unserialize($value['goods_spec']);
            $sort2 = $goods_spec;
            if ($sort1 == $sort2) {
                $goods_id = $value['goods_id'];
                break;
            }
        }
        $goods_info = Model('goods')->getGoodsInfoByID($goods_id);
        if (empty($goods_info)) {
            responseApiJson(0, '没有该商品', '');
        } else {
            $goods_detail = $this->_goods_detail($goods_info['goods_id']);
            responseApiJson(1, '', $goods_detail);
        }
    }

    /**
     * 商品详细页
     */
    private function _goods_detail($goods_id, $area_id = "")
    {
        // 商品详细信息
        $model_goods = Model('goods');
        $goods_detail = $model_goods->getGoodsDetail($goods_id);

        // 默认预订商品不支持手机端显示
        if ($goods_detail['is_book']) {
            responseApiJson(0, '预订商品不支持手机端显示', '');
        }

        $goods_list = $model_goods->getGoodsContract(array(0 => $goods_detail['goods_info']));
        $goods_detail['goods_info'] = $goods_list[0];

        //推荐商品
        $hot_sales = $model_goods->getGoodsCommendList($goods_detail['goods_info']['store_id'], 8);
        $goodsid_array = array();
        foreach ($hot_sales as $value) {
            $goodsid_array[] = $value['goods_id'];
        }
        $sole_array = Model('p_sole')->getSoleGoodsList(array('goods_id' => array('in', $goodsid_array)));
        $sole_array = array_under_reset($sole_array, 'goods_id');
        $goods_commend_list = array();
        foreach ($hot_sales as $value) {
            $goods_commend = array();
            $goods_commend['goods_id'] = $value['goods_id'];
            $goods_commend['goods_name'] = $value['goods_name'];
            $goods_commend['goods_price'] = $value['goods_price'];
            $goods_commend['goods_promotion_price'] = $value['goods_promotion_price'];
            if (!empty($sole_array[$value['goods_id']])) {
                $goods_commend['goods_promotion_price'] = $sole_array[$value['goods_id']]['sole_price'];
            }
            $goods_commend['goods_image_url'] = cthumb($value['goods_image'], 240);
            $goods_commend_list[] = $goods_commend;
        }

        $goods_detail['goods_commend_list'] = $goods_commend_list;
        $store_info = Model('store')->getStoreInfoByID($goods_detail['goods_info']['store_id']);

        $goods_detail['store_info']['store_id'] = $store_info['store_id'];
        $goods_detail['store_info']['store_name'] = $store_info['store_name'];
        $goods_detail['store_info']['member_id'] = $store_info['member_id'];
        $goods_detail['store_info']['member_name'] = $store_info['member_name'];
        $goods_detail['store_info']['is_own_shop'] = $store_info['is_own_shop'];
        $goods_detail['store_info']['is_only'] = $store_info['is_only'];
        // 添加QQ IM
        $goods_detail['store_info']['store_qq'] = $store_info['store_qq'];
        $goods_detail['store_info']['node_chat'] = C('node_chat');

        $goods_detail['store_info']['goods_count'] = $store_info['goods_count'];

        $storeCredit = array();
        $percentClassTextMap = array(
            'equal' => '平',
            'high' => '高',
            'low' => '低',
        );
        foreach ((array)$store_info['store_credit'] as $k => $v) {
            $v['percent_text'] = $percentClassTextMap[$v['percent_class']];
            $storeCredit[$k] = $v;
        }
        $goods_detail['store_info']['store_credit'] = $storeCredit;

        //商品详细信息处理
        $goods_detail = $this->_goods_detail_extend($goods_detail);

        //修复抢购限制
        $IsHaveBuy = 0;

        // 如果已登录 判断该商品是否已被收藏&&添加浏览记录
        if ($member_id = $this->getMemberIdIfExists()) {
            $c = (int)Model('favorites')->getGoodsFavoritesCountByGoodsId($goods_id, $member_id);
            $goods_detail['is_favorate'] = $c > 0;

            QueueClient::push('addViewedGoods', array('goods_id' => $goods_id, 'member_id' => $member_id));

            if (!$goods_detail['goods_info']['is_virtual']) {
                // 店铺优惠券
                $condition = array();
                $condition['voucher_t_gettype'] = 3;
                $condition['voucher_t_state'] = 1;
                $condition['voucher_t_end_date'] = array('gt', time());
                $condition['voucher_t_store_id'] = array('in', $store_info['store_id']);
                $voucher_template = Model('voucher')->getVoucherTemplateList($condition);
                if (!empty($voucher_template)) {
                    foreach ($voucher_template as $val) {
                        $param = array();
                        $param['voucher_t_id'] = $val['voucher_t_id'];
                        $param['voucher_t_price'] = $val['voucher_t_price'];
                        $param['voucher_t_limit'] = $val['voucher_t_limit'];
                        $param['voucher_t_end_date'] = date('Y年m月d日', $val['voucher_t_end_date']);
                        $goods_detail['voucher'][] = $param;
                    }
                }
            }

            //修复抢购限制
            $buyer_id = $member_id;
            $promotion_type = $goods_detail['goods_info']["promotion_type"];

            if ($promotion_type == 'groupbuy') {
                $upper_limit = $goods_detail['goods_info']["upper_limit"];

                if ($upper_limit > 0) {

                    $model_order = Model('order');
                    $order_goods_list = $model_order->getOrderGoodsList(array('goods_id' => $goods_id, 'buyer_id' => $buyer_id, 'goods_type' => 2));
                    if ($order_goods_list) {
                        $promotions_id = $order_goods_list[0]["promotions_id"];
                        $model_groupbuy = Model('groupbuy');
                        $groupbuy_info = $model_groupbuy->getGroupbuyInfo(array('groupbuy_id' => $promotions_id));
                        if ($groupbuy_info) {
                            $IsHaveBuy = 1;
                        } else {
                            $IsHaveBuy = 0;

                        }
                    }
                }
            }

        }

        // 评价列表
        $goods_eval_list = Model("evaluate_goods")->getEvaluateGoodsList(array('geval_goodsid' => $goods_id), null, '10', 'geval_id desc', 'geval_id,geval_scores,geval_content,geval_addtime,geval_frommembername,geval_explain');
        //$goods_eval_list = Logic('member_evaluate')->evaluateListDity($goods_eval_list);
        foreach ($goods_eval_list as $key => $value) {
            $goods_eval_list[$key]['geval_addtime'] = date('Y-m-d H:i:s', $goods_eval_list[$key]['geval_addtime']);
            if (empty($goods_eval_list[$key]['geval_explain'])) {
                $goods_eval_list[$key]['geval_explain'] = "";
            }
        }
        $goods_detail['goods_eval_list'] = $goods_eval_list;

        //评价信息
        $goods_evaluate_info = Model('evaluate_goods')->getEvaluateGoodsInfoByGoodsID($goods_id);
        $goods_detail['goods_evaluate_info'] = $goods_evaluate_info;

        $goods_detail['goods_hair_info'] = $this->_calc($area_id, $goods_id);
        $goods_detail['web_goods_url'] = BASE_SITE_URL . "/wap/tmpl/product_info.html?goods_id=" . $goods_id;
        //修复抢购限制
        //        $goods_detail['IsHaveBuy']=$IsHaveBuy;
        //        responseApiJson(1,'',$goods_detail);
        return $goods_detail;
    }

    /**
     * 商品咨询列表
     * @throws Exception
     */
    public function consulting_listOp()
    {
//        Tpl::output('hidden_nctoolbar', 1);
        $goods_id = intval($_GET['goods_id']);
        $token = $_GET['token'];

        //查询会员信息
        $member_id = Model('mb_user_token')->getMbUserTokenInfo(array('token' => $token));
//        $member_id = $token;
        $member_model = Model('member');
        $member_info = $member_model->getMemberInfo(array('member_id' => $member_id['member_id']));
//        $member_info = $member_model->getMemberInfo(array('member_id' => $member_id));

        if (empty($member_info)) {
            responseApiJson(0, "无该用户信息");
        }
        if (empty($goods_id) || $goods_id <= 0) {
            responseApiJson(0, "商品不存在");
        }

        // 商品详细信息
        $model_goods = Model('goods');
        $goods_info = $model_goods->getGoodsInfoByID($goods_id, '*');
        // 验证商品是否存在
        if (empty($goods_info)) {
            responseApiJson(0, "商品不存在");
        }
        $data['goods'] = $goods_info;

        $this->getStoreInfo($goods_info['store_id']);

        //得到商品咨询信息
        $model_consult = Model('consult');
        $where = array();
        $where['goods_id'] = $goods_id;
        $where['member_id'] = $member_info['member_id'];
        $consult_list = $model_consult->getConsultList($where, '*', 0, 20);
        foreach ($consult_list as $k => $v) {
            $data1[$k]['consult_content'] = $v['consult_content'];
            $data1[$k]['consult_addtime'] = date("Y-m-d H:i:s", $v['consult_addtime']);
            $data1[$k]['content_type'] = "consult";
            if (!empty($v['consult_reply']) || !empty($v['consult_reply_time'])) {
                $data2[$k]['consult_content'] = $v['consult_reply'];
                $data2[$k]['consult_addtime'] = date("Y-m-d H:i:s", $v['consult_reply_time']);
                $data2[$k]['content_type'] = "reply";
                $consult_list_response[] = $data2[$k];
            }
            $consult_list_response[] = $data1[$k];
        }
        $consult_addtime = array_column($consult_list_response, 'consult_addtime');
        array_multisort($consult_addtime, SORT_ASC, $consult_list_response);
        $data['consult_list'] = $consult_list_response;
        $data['show_page'] = $model_consult->showpage();

        // 咨询类型
        $consult_type = rkcache('consult_type', true);
        $data['consult_type'] = $consult_type;

        $seo_param = array();
        $seo_param['name'] = $goods_info['goods_name'];
        $seo_param['key'] = $goods_info['goods_keywords'];
        $seo_param['description'] = $goods_info['goods_description'];
        Model('seo')->type('product')->param($seo_param)->show();

        $data['consult_able'] = $this->checkConsultAble($goods_info['store_id']);
        responseApiJson(1, '', $data['consult_list']);
    }

    /**
     * 商品咨询
     */
    public function save_consultOp()
    {
        //检查是否可以评论
        $goods_id = intval($_POST['goods_id']);
        if ($goods_id <= 0) {
            responseApiJson(0, "参数错误");
        }
        //咨询内容的非空验证
        if (trim($_POST['goods_content']) == "") {
            responseApiJson(0, "请输入咨询内容");
        }

        if (!$_GET['token']) {
            responseApiJson(0, "未登录状态无法咨询");
        }

        //查询会员信息
        $member_id = Model('mb_user_token')->getMbUserTokenInfo(array('token' => $_GET['token']));
        $member_model = Model('member');
        $member_info = $member_model->getMemberInfo(array('member_id' => $member_id['member_id']));
        $store_id = Model('seller')->getSellerInfo(array('member_id' => $member_id['member_id']));

        //判断商品编号的存在性和合法性
        $goods = Model('goods');
        $goods_info = $goods->getGoodsInfoByID($goods_id, 'goods_name,store_id');
        if (empty($goods_info)) {
            responseApiJson(0, "您咨询的商品并不存在");

        }
        //判断是否是店主本人
        if ($store_id['store_id'] == $goods_info['store_id']) {
            responseApiJson(0, "您不能咨询自己的商品");

        }
        //检查店铺状态
        $store_model = Model('store');
        $store_info = $store_model->getStoreInfoByID($goods_info['store_id']);
        if ($store_info['store_state'] == '0' || intval($store_info['store_state']) == '2' || (intval($store_info['store_end_time']) != 0 && $store_info['store_end_time'] <= time())) {
            responseApiJson(0, "发布该商品的店铺已关闭");

        }
        //接收数据并保存
        $input = array();
        $input['goods_id'] = $goods_id;
        $input['goods_name'] = $goods_info['goods_name'];
        $input['member_id'] = $member_info['member_id'] > 0 ? $member_info['member_id'] : 0;
        $input['member_name'] = $member_info['member_name'] ? $member_info['member_name'] : '';
        $input['store_id'] = $store_info['store_id'];
        $input['store_name'] = $store_info['store_name'];
//        $input['ct_id'] = intval($_POST['consult_type_id']);
        $input['ct_id'] = 1;
        $input['consult_addtime'] = TIMESTAMP;
        if (strtoupper(CHARSET) == 'GBK') {
            $input['consult_content'] = Language::getGBK($_POST['goods_content']);
        } else {
            $input['consult_content'] = $_POST['goods_content'];
        }
        $input['isanonymous'] = $_POST['hide'];
        $consult_model = Model('consult');
        if ($consult_model->addConsult($input)) {
            responseApiJson(1, "发布成功");
        } else {
            responseApiJson(0, "发布失败");
        }
    }


    private function getStoreInfo($store_id)
    {
        $model_store = Model('store');
        $store_info = $model_store->getStoreOnlineInfoByID($store_id);
        if (empty($store_info)) {
            showMessage(L('nc_store_close'), '', '', 'error');
        }
        if ($_COOKIE['dregion']) {
            $store_info['deliver_region'] = $_COOKIE['dregion'];
        }
        if (strpos($store_info['deliver_region'], '|')) {
            $store_info['deliver_region'] = explode('|', $store_info['deliver_region']);
            $store_info['deliver_region_ids'] = explode(' ', $store_info['deliver_region'][0]);
            $store_info['deliver_region_names'] = explode(' ', $store_info['deliver_region'][1]);
        }
        return $store_info;
    }

    private function checkConsultAble($store_id = 0)
    {
        //检查是否为店主本身
        $store_self = false;
        if (!empty($_SESSION['store_id'])) {
            if (($store_id == 0 && intval($_GET['store_id']) == $_SESSION['store_id']) || ($store_id != 0 && $store_id == $_SESSION['store_id'])) {
                $store_self = true;
            }
        }
        //查询会员信息
        $member_info = array();
        $member_model = Model('member');
        if (!empty($_SESSION['member_id'])) {
            $member_info = $member_model->getMemberInfoByID($_SESSION['member_id'], 'is_allowtalk');
        }

        //检查是否可以评论
        $consult_able = true;
        if ((!C('guest_comment') && !$_SESSION['member_id']) || $store_self == true || ($_SESSION['member_id'] > 0 && $member_info['is_allowtalk'] == 0)) {
            $consult_able = false;
        }
        return $consult_able;
    }
}
