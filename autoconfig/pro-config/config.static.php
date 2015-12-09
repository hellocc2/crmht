<?php
//物理根目录
if (!defined('ROOT_PATH'))
{
	define('ROOT_PATH', dirname(__DIR__.'../').DIRECTORY_SEPARATOR);
}
if(!defined('CONFIG_PATH'))
{
    define('CONFIG_PATH',__DIR__.DIRECTORY_SEPARATOR);
}

//session加密字符
if (!defined('SESSION_PREFIX'))
{
    define('SESSION_PREFIX', md5('milanoo_session_'));
} 

//缓存根目录
if(!defined('HTML_CACHE_ROOT'))
{
    define('HTML_CACHE_ROOT', ROOT_PATH.'data/html'.DIRECTORY_SEPARATOR);
}

//应用服务器的统一域名
if(!defined('HTML_CACHE_APP_SERVER_DOMAIN'))
{
    define('HTML_CACHE_APP_SERVER_DOMAIN', 'www.milanoo.com');    
}

//静态页面中的链接需要被替换掉的域名. (使用HTML_CACHE_APP_SERVER_DOMAIN来替换)
if(!defined('HTML_CACHE_SERVER_DOMAIN_REPLACED'))
{
    define('HTML_CACHE_SERVER_DOMAIN_REPLACED', 'http://222.73.181.236,http://ht.milanoo.com,http://milanoo.com');
}

//是否为开发模式. 在开发模式下静态页面中相关的链接的域名将使用本地地址,否则使用HTML_CACHE_APP_SERVER_DOMAIN定义的域名
if(!defined('HTML_CACHE_DEV_MODE'))
{
    define('HTML_CACHE_DEV_MODE', true);
}

if(!defined('HTML_STATIC_FILE_LIFETIME'))
{
    /**
     * 静态化文件缓存生存周期
     * @const int
     */
    define('HTML_STATIC_FILE_LIFETIME', 260);
}
if(!defined('HTML_CACHE_ON'))
{
    define('HTML_CACHE_ON', false);
}
/**
 * 允许被缓存的module和action组
 * @var array
 */
$staticCacheModuleAction = array('index'=>array('index','notfound','contact_us','links','seeall'),
                                 'Brand'=>array('index','item'),
                                 'thing'=>array('glist','index','item'),
                                 'promotions'=>array('specials'),
                                 'producttags'=>array('index','sort'),
                                 'sale' => array('index','glist'),

);