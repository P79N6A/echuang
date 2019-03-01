<?php
/**
 * 支付接口
 */

interface pay {
	public function doPay();
	public function verify($data);
}

// abstract class Paybase {
// 	// 支付方式名
// 	protected $name;

// 	// 支付配置
// 	protected $config;

// 	public function __construct($name) {
// 		$this->name = $name;
// 	}

// 	// 支付接口
// 	protected abstract function doPay();

// 	// 获取配置信息接口
// 	protected abstract function getConfig();

// 	// 同步回调接口
// 	protected abstract function syncCallback();

// 	// 异步回调接口
// 	protected abstract function asynCallback();

// }