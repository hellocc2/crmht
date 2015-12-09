<?php
namespace Helper;

use helper\ResponseUtil as Rewrite;
/**
 * 
 * 错误提示类
 * @author JiangLin <jianglin@milanoo.com>
 *
 */
class ErrorTip {
	/**
	 * 判断是否登录
	 *
	 */
	public static function setError($msg) {
		$error = array();
		if(isset($_SESSION[SESSION_PREFIX . "errorTip"])) {
			$error = $_SESSION[SESSION_PREFIX . "errorTip"];
		}
		
		if(empty($error)) {
			$error = array();
		}
		$error[] = $msg;
		$_SESSION[SESSION_PREFIX . "errorTip"] = $error;
	}
	
	/**
	 * 
	 * 当获取完错误信息，自动清除
	 */
	public static function getErrorAndClean() {
		$error = array();
		if(isset($_SESSION[SESSION_PREFIX . "errorTip"])) {
			$error = $_SESSION[SESSION_PREFIX . "errorTip"];
			unset($_SESSION[SESSION_PREFIX . "errorTip"]);
		}
		return $error;
	}
}