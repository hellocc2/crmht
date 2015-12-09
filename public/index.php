<?php
/**
 * 脚本开始执行的时间
 * @var float
 */
$PHP_SELF=explode("?", $_SERVER["REQUEST_URI"]);
$_SERVER["PHP_SELF"]=$PHP_SELF[0];
define ( 'SCRIPT_TIME_START', microtime ( true ) );
define ( 'ROOT_PATH', realpath ( __DIR__ . '/..' ) . DIRECTORY_SEPARATOR );
require ROOT_PATH . 'lib/Log.php';
//Lib\Log::setErrorHandler ();
require ROOT_PATH . '/config/config.static.php';
require ROOT_PATH . 'config/config.inc.php';
//调入类库自动加载过程
require ROOT_PATH . 'lib/common/autoloader.php';
require ROOT_PATH . 'config/admin.config.inc.php';

if ($_POST) {
	foreach ($_POST as $key => $value) {
		@$output .="\n".$key."=>".$value."\n";
	}
	file_put_contents( ROOT_PATH ."errors/".date("Y-m-d").".log",date("Y-m-d H:i:s").$output );
}

$app = new Lib\common\Application ();
$app->run ();

//@TODO 调试级别设定
if (DEBUG_MODE) {
	Lib\common\Debug::outPut ();
}