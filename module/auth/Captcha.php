<?php
namespace Module\Auth;
/**
 * 验证码生成和设置类
 * @author Su Chao<suchaoabc@163.com>
 * @since 2011-11-22
 */
class captcha extends \Lib\common\Application{
	public function __construct()
	{
		if(isset(self::$requestParams->act))
		{
			$act = self::$requestParams->act;
		}
		else
		{
			$act = 'reg';
		}
		header("Content-type: image/png");
		$captchaStr = \Lib\Image::getCaptcha();		
		$_SESSION['captcha'][$act] = $captchaStr;						 
	}
}