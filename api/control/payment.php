<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/14 0014
 * Time: 下午 20:16
 */
/**
 * 以post方式提交到对应的接口url
 *
 * @param string $url  url
 * @param array $date  需要post的数据
 * @param bool $useCert 是否需要证书，默认不需要
 * @param int $second   url执行超时时间，默认30s
 *
 * @return string   返回结果
 * @throws WxPayException
 */
 header("Access-Control-Allow-Origin: *");
 function postParams($url,$params, $second = 6,$json=true,$useCert = false,$sslCertPath='',$sslKeyPath='',
                           $curlProxyHost='0.0.0.0',$curlProxyPort=0)
{

    DI()->logger->debug('postParams',$params);
    DI()->logger->debug('curl url',$url);
    $ch = curl_init();
    //设置超时
    curl_setopt($ch, CURLOPT_TIMEOUT, $second);

    //如果有配置代理这里就设置代理
    if($curlProxyHost != "0.0.0.0"
        && $curlProxyPort != 0){
        curl_setopt($ch,CURLOPT_PROXY, $curlProxyHost);
        curl_setopt($ch,CURLOPT_PROXYPORT, $curlProxyPort);
    }
    curl_setopt($ch,CURLOPT_URL, $url);
    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
    curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,0);//2 严格校验 ,不想验证也可设为0
    //设置header
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    //要求结果为字符串且输出到屏幕上
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

    if($useCert == true){
        curl_setopt($ch, CURLOPT_VERBOSE, '1'); //debug模式，方便出错调试
        //设置证书
        //使用证书：cert 与 key 分别属于两个.pem文件
        curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
        //DI()->logger->debug('certPath',dirname(dirname(dirname(__FILE__))) . '/' . $this->config['sslCertPath']);
        curl_setopt($ch,CURLOPT_SSLCERT,  $sslCertPath);
        //curl_setopt($ch, CURLOPT_SSLCERTPASSWD,"Abcd1234");
        curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
        curl_setopt($ch,CURLOPT_SSLKEY, $sslKeyPath);
        //curl_setopt($ch, CURLOPT_SSLKEYPASSWD,"Abcd1234");
    }
    if($json && is_array($params)) {
        $params = json_encode($params);
    }
    //设置post方式提交
    curl_setopt($ch, CURLOPT_POST, 1);
    //设置post数据
    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);


    if($json){ //发送JSON数据
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER,
            array('Content-Type: application/json; charset=utf-8',
                'Content-Length:' . strlen($params)
            )
        );
    }
    else{
        curl_setopt($ch, CURLOPT_HTTPHEADER,
            array('Content-Type: application/x-www-form-urlencoded; charset=utf-8'));
    }
    //运行curl
    $data = curl_exec($ch);
    //返回结果
    if($data){
        curl_close($ch);
        DI()->logger->debug('curl reback data',$data);
        return $data;
    } else {
        $error = curl_errno($ch);
        curl_close($ch);
        DI()->logger->error("curl出错","错误码:$error");
        return false;
    }
}