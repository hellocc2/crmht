<?php
namespace Lib;
/**
 * 图像处理类
 * @author Su Chao<suchaoabc@163.com>
 * @since 2011-11-22
 */
class Image{
	/**
	 * 生成验证码
	 * @param int $width 验证码图片宽度.默认130
	 * @param int $height 验证码图片高度.默认40
	 * @param int $fontSize 验证码字体大小.默认20
	 * @param int $length 验证码字符个数.默认4
	 * @return string  验证码中的字符串
	 */
	public static function getCaptcha($width='130', $height='40', $fontSize='20', $length='4')
	{

		$chars = '0123456789abcdefghijklmnopqrstuvwxyz';
		$randStr = substr(str_shuffle($chars), 0, $length);
		
		$image			= imagecreatetruecolor($width, $height);
		
		// 定义背景色
		$bgColor		= imagecolorallocate($image, 0xFF, 0xFF, 0xFF);
		// 定义文字及边框颜色
		$blackColor	= imagecolorallocate($image, 0x00, 0x00, 0x00);
		 
		//生成矩形边框
		imagefilledrectangle($image, 0, 0, $width, $height, $bgColor);		
		
		// 循环生成雪花点
		for ($i = 0; $i < 200; $i++)
		{
			$grayColor = imagecolorallocate($image, 128 + rand(0, 128), 128 + rand(0, 128), 128 + rand(0, 128));
			imagesetpixel($image, rand(1, $width-2), rand(4, $height-2), $grayColor);
		}
		$font	= ROOT_PATH . 'resources/fonts/acidic.ttf';
		// 把随机字符串输入图片
		$i=-1;
		while (isset($randStr[++$i]))
		{
			$fontColor	= imagecolorallocate($image, rand(0, 100), rand(0, 100),rand(0, 100));
			if(!function_exists('imagettftext')) 
			{
				imagechar( $image, $fontSize ,  15 + $i*30, rand(5,20), $randStr[$i], $fontColor );
			}
			else
			{
				imagettftext($image, $fontSize, 0, 10 + $i*30, rand(25,35), $fontColor, $font, $randStr[$i]);
			}
		}		
		imagepng($image);
		$image=$bgColor=$blackColor=$grayColor=$fontColor=null;
		return $randStr;
	}
}