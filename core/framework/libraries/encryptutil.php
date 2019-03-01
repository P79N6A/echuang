<?php
/**
 * 加密工具类
 */
defined('In33hao') or exit('Access Invild!');
class EncryptUtil {
	// 偏移量
	const IV = '00000000000000000000000000000000';

	/**
	 * encrypt 加密算法
	 * @param  [type] $cleartext   明文
	 * @param  string $key         密钥
	 * @return [type]       [description]
	 */

	public static function encrypt($cleartext, $key) {
		$key = hash('sha256', $key, true);
		$block = 16;
		$pad = $block - (strlen($cleartext) % $block);
		$cleartext .= str_repeat(chr($pad), $pad);
		$encrypted = openssl_encrypt($cleartext, 'AES-256-CBC', $key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, self::_hexToStr(self::IV));
		return base64_encode($encrypted);
	}

	/**
	 * decrypt 解密算法
	 * @param  [type] $ciphertext 密文
	 * @param  string $key        密钥
	 * @return [type]             [description]
	 */
	public static function decrypt($ciphertext, $key) {
		$key = hash('sha256', $key, true);
		$decrypted = openssl_decrypt(base64_decode($ciphertext), 'AES-256-CBC', $key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, self::_hexToStr(self::IV));
		return self::_strippadding($decrypted);
	}

	/**
	 * _addpadding cs7补码
	 * @param  [type]  $string    [description]
	 * @param  integer $blocksize [description]
	 * @return [type]             [description]
	 */
	private static function _addpadding($string, $blocksize = 16) {
		$len = strlen($string);
		$pad = $blocksize - ($len % $blocksize);
		$string .= str_repeat(chr($pad), $pad);
		return $string;
	}

	private static function _strippadding($string) {
		$slast = ord(substr($string, -1));
		$slastc = chr($slast);
		$pcheck = substr($string, -$slast);
		if (@preg_match("/$slastc{" . $slast . "}/", $string)) {
			$string = substr($string, 0, strlen($string) - $slast);
			return $string;
		} else {
			return false;
		}
	}

	/**
	 * _hexToStr 16进制转字符串
	 * @param  [type] $hex [description]
	 * @return [type]      [description]
	 */
	private static function _hexToStr($hex) {
		$string = '';
		for ($i = 0; $i < strlen($hex) - 1; $i += 2) {
			$string .= chr(hexdec($hex[$i] . $hex[$i + 1]));
		}
		return $string;
	}
}
