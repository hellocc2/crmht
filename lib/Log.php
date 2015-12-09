<?php
namespace Lib;
use Helper\String;

class Log{
	protected static $errorConstantMap = array(1 => 'E_ERROR',2 => 'E_WARNING',
										3 => 'E_PARSE',4 => 'E_NOTICE',
										8 => 'E_NOTICE', 16 => 'E_CORE_ERROR',
										32 => 'E_CORE_WARNING', 64 => 'E_COMPILE_ERROR',
										128 => 'E_COMPILE_WARNING', 256 => 'E_USER_ERROR',
										512 => 'E_USER_WARNING', 1024 => 'E_USER_NOTICE',
										2048 => 'E_STRICT', 4096 => 'E_RECOVERABLE_ERROR',
										8192 => 'E_DEPRECATED', 16384 => 'E_USER_DEPRECATED',
										32767 => 'E_ALL'
	);
	public static function setErrorHandler()
	{
		set_error_handler('\Lib\Log::errorHandler');
		set_exception_handler('\Lib\Log::errorHandler');
	}
	
	/**
	 * 参数参考{@link set_error_handler}.<br />
	 * 要完成日志的正确记录,必须正确设置好linux的syslog.conf. (消息程序类型必须设置为local5.debug).<br />
	 * 例如:<br />
	 * 日志类型配置(如:/etc/syslog.conf)中添加设置:local5.debug     /var/log/php.log   <br />
	 *      如果使用统一的日志服务器来记录则为, local5.debug   @192.168.0.119   <br />
	 *      其中 192.168.0.119为日志文件服务器. 然后还需到192.168.0.119修改syslog配置,来允许来自远程的日志.<br />
	 *      在(如:/etc/sysconfig/syslog)SYSLOG_OPTIONS选项中添加 -r .
	 * @param int $errno
	 * @param string $errstr
	 * @param string $errfile
	 * @param int $errline
	 * @param array $errcontext
	 */
	public static function errorHandler($errno,$errstr,$errfile,$errline,&$errcontext)
	{
		$time = date('Y-m-d H:i:s');
		if(is_object($errno))
		{//当errorHanlder扑捉的为异常时
			$errorMsg = (String) $errno;
			echo $errorMsg;
		}
		else
		{
			$errorMsg = '';
			$errorMsg .= 'PHPServer: '. $_SERVER['SERVER_ADDR']." \n";
			$errorMsg .= 'ErrorLevel: '. self::$errorConstantMap[$errno]." \n";
			$errorMsg .= 'File: '. $errfile." \n";
			$errorMsg .= 'Line: '. $errline." \n";
			$errorMsg .= 'Details: '. $errstr;
		}
		//openlog(null, LOG_CONS, LOG_LOCAL5);
		//syslog(LOG_DEBUG, str_replace("\n",'#',$errorMsg));
		
		if(DEBUG_MODE)
		{	
			$errorMsg = $time . " \n".$errorMsg;
			require_once ROOT_PATH.'/lib/common/Debug.php';
			common\Debug::setInfo('PHP_Error', $errorMsg,true);
			if(DEBUG_LEVEL==0)echo $errorMsg;
		}
		return true;
	}	
}