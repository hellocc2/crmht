<?php
namespace Lib\common;
use Helper\RequestUtil;
/**
 * 应用基础类,其它应用类均应继承此类
 * @author Su Chao<suchaoabc@163.com>
 *
 */
class Application{
    /**
     * 解析后的客户HTTP请求参数.包含模块名(moduel)和动作名(action)及其它自定义参数
     * @var Object
     */
    protected static $requestParams;
    public function __construct()
    {
        if(DEBUG_MODE)
        {
            ini_set('display_errors',1);
            error_reporting( E_ALL | E_STRICT);
        }
        else
       {
            ini_set('display_errors',0);
        }
    }
    
    /**
     * 启动应用
     */
    public function run()
    {        
    	self::$requestParams = RequestUtil::getParams();    	
        $moduleAction = 'Module\\' .self::$requestParams->module.'\\'. ucfirst(self::$requestParams->action);
        if(!class_exists($moduleAction, true))
        {
        	$msg = 'Milanoo module/action not found !'.$moduleAction."\n".'Parsed request parameters:'."\n".var_export(self::$requestParams,true);
        	if(DEBUG_MODE)
        	{
        		\Lib\common\Debug::setInfo('MethodNotFound', $msg);
        		return;
        	}        	
			error_log($msg);
            header ('HTTP/1.1 404 Not found');
           
            require ROOT_PATH.'errors/404.php';
			die();
        }
        header('content-type:text/html;charset=utf-8');
        
        //启动各模块之前运行各额外方法      
        \Lib\Bootstrap::run();        
        //执行个相应模块
        $module = new $moduleAction();
    }
    
    /**
     * 获取服务器当前的时间戳,精确到微秒(默认)
     * @param int $decimal 小数位位数,默认为6位小数,即一微秒
     * @return float 
     */
    public static function getMicroTime($decimal=6)
    {
        return number_format(microtime(true),(int)$decimal,'.','');
    } 
    
    /**
    * js跳转url
    */
  		function jumpTo($url )
  		{
  				echo "<script language='javascript' type='text/javascript'>";
  				echo "window.parent.mainFrame.location.href='$url'";
  				echo "</script>";
  		}   
} 
