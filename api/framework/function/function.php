<?php
/**
 * mobile公共方法
 *
 * 公共方法
 *
 */
defined('In33hao') or exit('Access Invalid!');

function responseApiJson($status = 0, $msg = '', $data = null) {
	echo json_encode(array('status' => $status, 'msg' => $msg, 'data' => $data));
	die;
}

 function output_data($datas, $extend_data = array(), $error = false) {
     $data = array();
     $data['code'] = 200;
     if($error) {
         $data['code'] = 400;
     }

     if(!empty($extend_data)) {
         $data = array_merge($data, $extend_data);
     }

     $data['datas'] = $datas;

     $jsonFlag = 0 && C('debug') && version_compare(PHP_VERSION, '5.4.0') >= 0
         ? JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
         : 0;

     if ($jsonFlag) {
         header('Content-type: text/plain; charset=utf-8');
     }

     if (!empty($_GET['callback'])) {
         echo $_GET['callback'].'('.json_encode($data, $jsonFlag).')';die;
     } else {
         header("Access-Control-Allow-Origin:*");
         echo json_encode($data, $jsonFlag);die;
     }
 }

function output_error($message, $extend_data = array()) {
	$datas = array('error' => $message);
	output_data($datas, $extend_data, true);
}

function mobile_page($page_count) {
	//输出是否有下一页
	$extend_data = array();
	$current_page = intval($_GET['curpage']);
	if ($current_page <= 0) {
		$current_page = 1;
	}
	if ($current_page >= $page_count) {
		$extend_data['hasmore'] = false;
	} else {
		$extend_data['hasmore'] = true;
	}
	$extend_data['page_total'] = $page_count;
	return $extend_data;
}

function get_server_ip() {
	if (isset($_SERVER)) {
		if ($_SERVER['SERVER_ADDR']) {
			$server_ip = $_SERVER['SERVER_ADDR'];
		} else {
			$server_ip = $_SERVER['LOCAL_ADDR'];
		}
	} else {
		$server_ip = getenv('SERVER_ADDR');
	}
	return $server_ip;
}

function http_get($url) {
	return file_get_contents($url);
}

function http_post($url, $param) {
	$postdata = http_build_query($param);

	$opts = array(
		'http' => array(
			'method' => 'POST',
			'header' => 'Content-type: application/x-www-form-urlencoded',
			'content' => $postdata,
		),
	);

	$context = stream_context_create($opts);

	return @file_get_contents($url, false, $context);
}

function http_postdata($url, $postdata) {
	$opts = array(
		'http' => array(
			'method' => 'POST',
			'header' => 'Content-type: application/x-www-form-urlencoded',
			'content' => $postdata,
		),
	);

	$context = stream_context_create($opts);

	return @file_get_contents($url, false, $context);
}

/**
 * 创建http header参数
 * @param array $data
 * @return bool
 */
function createHttpHeader() {
    $nonce = mt_rand();
    $timeStamp = time();
    $sign = sha1('e0E0N0xLUZooS'.$nonce.$timeStamp);
    return array(
            'RC-App-Key:'.'pvxdm17jpogzr',
            'RC-Nonce:'.$nonce,
            'RC-Timestamp:'.$timeStamp,
            'RC-Signature:'.$sign,
    );
}

/**
 * 重写实现 http_build_query 提交实现(同名key)key=val1&key=val2
 * @param array $formData 数据数组
 * @param string $numericPrefix 数字索引时附加的Key前缀
 * @param string $argSeparator 参数分隔符(默认为&)
 * @param string $prefixKey Key 数组参数，实现同名方式调用接口
 * @return string
 */
function build_query($formData, $numericPrefix = '', $argSeparator = '&', $prefixKey = '') {
    $str = '';
    foreach ($formData as $key => $val) {
        if (!is_array($val)) {
            $str .= $argSeparator;
            if ($prefixKey === '') {
                if (is_int($key)) {
                    $str .= $numericPrefix;
                }
                $str .= urlencode($key) . '=' . urlencode($val);
            } else {
                $str .= urlencode($prefixKey) . '=' . urlencode($val);
            }
        } else {
            if ($prefixKey == '') {
                $prefixKey .= $key;
            }
            if (isset($val[0]) && is_array($val[0])) {
                $arr = array();
                $arr[$key] = $val[0];
                $str .= $argSeparator . http_build_query($arr);
            } else {
                $str .= $argSeparator . build_query($val, $numericPrefix, $argSeparator, $prefixKey);
            }
            $prefixKey = '';
        }
    }
    return substr($str, strlen($argSeparator));
}

/**
 * 发起 server 请求
 * @param $action
 * @param $params
 * @param $httpHeader
 * @return mixed
 */
function curl($action, $params,$contentType='urlencoded',$module = 'im',$httpMethod='POST') {
    switch ($module){
        case 'im':
            $action = 'http://api-cn.ronghub.com'.$action;
            break;
        default:
            $action = 'http://api-cn.ronghub.com'.$action;
    }
    $httpHeader = createHttpHeader();
    $ch = curl_init();
    if ($httpMethod=='POST' && $contentType=='urlencoded') {
        $httpHeader[] = 'Content-Type:application/x-www-form-urlencoded';
        curl_setopt($ch, CURLOPT_POSTFIELDS, build_query($params));
    }
    if ($httpMethod=='POST' && $contentType=='json') {
        $httpHeader[] = 'Content-Type:Application/json';
       	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params) );
    }
    curl_setopt($ch, CURLOPT_URL, $action);
    curl_setopt($ch, CURLOPT_POST, $httpMethod=='POST');
    curl_setopt($ch, CURLOPT_HTTPHEADER, $httpHeader);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false); //处理http证书问题
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_DNS_USE_GLOBAL_CACHE, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $ret = curl_exec($ch);
    return $ret;
}

/**
 * 获取 Token 方法 
 * 
 * @param  userId:用户 Id，最大长度 64 字节.是用户在 App 中的唯一标识码，必须保证在同一个 App 内不重复，重复的用户 Id 将被当作是同一用户。（必传）
 * @param  name:用户名称，最大长度 128 字节.用来在 Push 推送时显示用户的名称.用户名称，最大长度 128 字节.用来在 Push 推送时显示用户的名称。（必传）
 * @param  portraitUri:用户头像 URI，最大长度 1024 字节.用来在 Push 推送时显示用户的头像。（必传）
 *
 * @return $json
 **/
function getToken($userId, $name, $portraitUri='') {
	$params = array (
	'userId' => $userId,
	'name' => $name,
	'portraitUri' => $portraitUri
	);
	$ret = curl('/user/getToken.json',$params);
	return $ret;
}
