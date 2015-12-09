<?php
namespace Helper;

/**
 * 
 * 表单验证生成
 * @author Jianglin <jianglin@milanoo.com>
 *
 */
class FormAuth {
	
	/**
	 * 
	 * 验证函数
	 */
	public static function auth($msg='',$url='') {
		//获取传递参数
		if(!empty($_POST)) {
			$params_all = \Helper\RequestUtil::getParams();
			$auth = false;
			if($params_all->formAuth == $_SESSION[SESSION_PREFIX . "FormAuth"]) {
				$auth = true;
				unset($_SESSION[SESSION_PREFIX . "FormAuth"]);
			}
			
			if(!$auth) {
				if(empty($msg)) {
					$msg = 'You have Submited!';
				}
				if(empty($url)) {
					$url = $_SERVER['HTTP_REFERER'];
				}
				\Helper\ErrorTip::setError($msg);
				header("Location:" . $url);
				exit();
			}
		}
	}
	
	/**
	 * 
	 * 生成验证码
	 */
	public static function createAuthCode() {
		$formAuthMD5 = md5(time() . $_COOKIE['CartId'] . rand(0, 100));
		$_SESSION[SESSION_PREFIX . "FormAuth"] = $formAuthMD5;
		return $formAuthMD5;
	}
}