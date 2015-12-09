<?php
if (!defined('DEFAULT_CHARSET')) define('DEFAULT_CHARSET', 'utf-8');// 页面默认字符集
if (!defined('THEME')) define('THEME', ROOT_PATH . 'theme/');//模板根目录 
if (!defined('THEME_ROOT_PATH')) define('THEME_ROOT_PATH', THEME.'admin/default/');//模板根目录
define('THEME_COMPILE_ROOT_PATH', DATA_CACHE_ROOT_PATH.'cache/admin/');//模板的缓存目录
if (!defined('PAGE')) define('PAGE', '15');//每页显示默认数据的默认值
if (!defined('JAVASCRIPT_URL')) define('JAVASCRIPT_URL', ROOT_URL.'javascript/');//javascript的url地址
if (!defined('IMG_SERVER_ROOT')) define('IMG_SERVER_ROOT','uploads_url');//图片服务器及图片存放的根目录