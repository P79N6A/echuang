<?php
/**
 * 回调基类
 *
 */

// 回调接口
interface calllback {
	// 同步回调
	public function syncCallbackOp();
	// 异步回调
	public function asynCallbackOp();
}

// 回调基类
class callbackBaseControl {
	protected $payment_info;

	public function __construct() {

	}
}