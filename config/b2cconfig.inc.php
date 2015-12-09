<?php

if (!defined('DEFAULT_CHARSET'))
				define('DEFAULT_CHARSET', 'utf-8'); // 页面默认字符集
if (!defined('SITE_NAME'))
				define('SITE_NAME', 'milanoo');

if (!defined('ROOT_URL'))
				define('ROOT_URL', "http://" . $_SERVER["HTTP_HOST"] . "/");
if (!defined('ROOT_URLD'))
				define('ROOT_URLD', "http://" . $_SERVER["HTTP_HOST"] . "/");
if (!defined('ROOT_URLS'))
				define('ROOT_URLS', "http://" . $_SERVER["HTTP_HOST"] . "/");

if (!defined('THEME'))
				define('THEME', ROOT_PATH . 'theme/'); //模板根目录
if (!defined('THEME_COMPILE_ROOT_PATH'))
				define('THEME_COMPILE_ROOT_PATH', DATA_CACHE_ROOT_PATH . '/templatecache/'); //模板的缓存目录
//语言包跟目录
if (!defined('LANG_ROOT'))
				define('LANG_ROOT', ROOT_PATH . 'lang' . DIRECTORY_SEPARATOR);
if (!defined('MODULE_PATH'))
				define('MODULE_PATH', ROOT_PATH . 'module/'); //模块目录
if (!defined('JAVASCRIPT_URL'))
				define('JAVASCRIPT_URL', ROOT_URL . 'javascript/'); //javascript的url地址
if (!defined('IMG_SERVER_ROOT'))
				define('IMG_SERVER_ROOT', 'uploads_url'); //图片服务器及图片存放的根目录
if (!defined('PAGE'))
				define('PAGE', '15'); //每页显示默认数据的默认值
if (!defined('IS_REWRITE_URL'))
				define('IS_REWRITE_URL', 2); //仿静态 1无配制的仿静态 2为配制后的仿静态
if (!defined('ClearanceDiscount'))
				define('ClearanceDiscount', 1);
if (!defined('IS_AB_TEST'))
				define('IS_AB_TEST', 1);
if (!defined('IS_MAIN_WEB'))
				define('IS_MAIN_WEB', 0);
define('MERCHANT_ID', 'milanoocom');
define('TRANSACTION_KEY',
				'ik566y0AckArJNgPJ8i5nu9DClKDF5tSaaIInmaAOwHb675jskMGAApqwxYboIW51wSjQLoOQaaJf+gpPvriX9kLTsT+mCECQQDEYz9S5oyL2SP4JH/AJ8n8WRbUsmRvqjKzzuo+33sn5QhdsfBmyilBN/40Navx2y0Bc36i2w088tOyxL2/vJVGdVx9OiDvQwpSgxebUmmiCJ5mgDsB2+u+Y7JDBgAKasMWG6CFudcEo0C6DkGmiX/oKT764l/ZC07E/pghAkEAxGM/UuaMi9kj+CR/wCfJ/FkW1LJkb6oys87qPt97J+UIXbHwZsopQTf+NDWr8dstAXN+otsN4g==');
define('WSDL_URL', 'https://ics2wstest.ic3.com/commerce/1.x/transactionProcessor/CyberSourceTransaction_1.59.wsdl');
