<?php
define('MAGIC_QUOTES_GPC', get_magic_quotes_gpc());//函数取得 PHP 环境配置的变量

if($_SERVER["SERVER_PORT"]==443)  $http="https"; else  $http="http";

if (!defined('HTTP')) define('HTTP', $http);
if (!defined('ROOT_URL'))
{	
	define('ROOT_URL',$http."://".$_SERVER["HTTP_HOST"]."/");		
	
}
if (!defined('CDN_ROOT'))
{
	//define('CDN_ROOT',"http://".$_SERVER["HTTP_HOST"]."/");	
	if($http == 'https') {
		define('CDN_ROOT',"https://".$_SERVER["HTTP_HOST"]."/");
		if (!defined('UED_ROOT')) define('UED_ROOT', ROOT_URL);
	} else {
		define('CDN_ROOT',$http."://".$_SERVER["HTTP_HOST"]."/");
		if (!defined('UED_ROOT')) define('UED_ROOT', ROOT_URL."ued/");
	}
}
if (!defined('MD5_pass')) define('MD5_pass', 'milanoo_');
if (!defined('SESSION_PREFIX')) define('SESSION_PREFIX', md5('milanoo_session_'));//session加密字符
if (!defined('FileUp')) define('FileUp', ROOT_PATH . 'upload');//上传文件目录
if (!defined('CDN_IMAGE_URL')) define('CDN_IMAGE_URL', CDN_ROOT);//新图片地址image
if (!defined('CDN_JAVASCRIPT_URL')) define('CDN_JAVASCRIPT_URL', ROOT_URL.'ued/javascript/');//新JS文件地址
if (!defined('CDN_CSS_URL')) define('CDN_CSS_URL', ROOT_URL.'ued/image/default/css/');//新CSS文件地址
if (!defined('IMAGE_GLOBAL_URL')) define('IMAGE_GLOBAL_URL', ROOT_URL.'ued/image/default/');//模板公用图片目录的url地址
if (!defined('CDN_UPLOAD_URL')) define('CDN_UPLOAD_URL', CDN_ROOT);//新图片地址upload/
if (!defined('CDN_UPLAN_URL')) define('CDN_UPLAN_URL', CDN_ROOT);//新图片地址upload/up(en,fr,···)
if (!defined('STATICS_OPEN')) define('STATICS_OPEN', 1);//是否开启统计代码显示
if (!defined('MEDIA_URL'))define('MEDIA_URL', CDN_IMAGE_URL .'media');//模板图片目录的url地址
		

//数据缓存根目录
if (!defined('DATA_CACHE_ROOT_PATH')) define('DATA_CACHE_ROOT_PATH', ROOT_PATH . 'data'.DIRECTORY_SEPARATOR);

/**
 * 调试模式开关.调试模式下会输出所有错误信息
 * @var boolean
 */
define('DEBUG_MODE',1);
/**
 * 调试级别.
 * @var INT 0,输出所有调试信息.<br /> 
 * 			1,只输出内存消耗及脚本耗时.<br /> 
 *      	2,除了1的内容外,还会输出webservice调用参数传输信息及耗时(但不会输出webservice返回的数据).
 * @todo 完善各级别的定义
 */
define('DEBUG_LEVEL',2);

define('JAVA_WEBSERVICE_URL','http://192.168.3.67:8080');