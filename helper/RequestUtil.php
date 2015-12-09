<?php 
namespace Helper;
use \Lib\common\Language;
/**
 * 客户端HTTP请求处理类
 * @author Su Chao <suchaoabc@163.com>
 * @since 2011-09-03
 * @uses \Lib\common\Language;
 */
class RequestUtil{
    /**
     * 客户端的HTTP请求参数(GET,POST 以及URL重写解析后生成的参数).包含模块名(module)和动作名(action)及其它自定义参数
     * @var Object
     */
    protected static $requestParams;
    
    /**
     * 获取解析后的HTTP请求参数,包括重写后的URL解析.
     * @param string $name 参数名称. 当为null,或不指定参数名时返回所有参数
     * @return mixed
     */
    public static function getParams($name=null)
    {
        if(!isset(self::$requestParams))
        {
            self::initParams();
        }
        if(is_string($name))
        {
            if(isset(self::$requestParams->$name))
            {
                return self::$requestParams->$name;
            }
            else
            {
                return null;
            }
        }
        else
        {
            return self::$requestParams;
        }
    }
    
    /**
     * 重新设置RequestUtil::$requestParams指定变量的值
     * @param string $name 参数名称
     * @param mixed $value 参数值
     */
    public static function resetParam($name,$value)
    {
        return self::$requestParams->$name = $value;
    }
    
    /**
     * 初始话客户端的请求参数
     * @return 初始化的后的客户端请求参数
     */
    public static function initParams()
    {
        $module = self::requestParam('module');
        $action = self::requestParam('action');
        if(empty($module) && isset($params['module'])) $module = $params['module'];
      
        if(empty($action) && isset($params['action'])) $action = $params['action'];
        if(!empty($module) && !empty($action))
        	$params = $_GET;
          
        if(empty($module))
        {
            $module = 'index';   
            $params['module'] = 'index';
        }
        
        if(empty($action))
        {
           $action = 'index';  
           $params['action'] = 'index';
        } 
        
        $params = array_merge($params,$_POST);   

        if(isset($params['aparams']))
        {
            $params = array_merge($params,self::parseAparams($params['aparams']));
        }
        $params = array_merge($params, self::parseSearchFields());
        return self::$requestParams = (Object) $params; 
    }               
    
    /**
     *  获取客户端的GET或POST参数
     * @param string $name 参数名称
     */
    public static function requestParam($name=null)
    {
        if(!is_string($name))return null;
        $value = isset($_GET[$name]) ? $_GET[$name] : (isset($_POST[$name])? $_POST[$name] : null);
        return $value;
    }
    
    /**
     * 获取客户端请求的完整的URL地址
     * @param boolean $withQueryString  是否包括query string,默认为true
     * @return string URL
     */
    public static function getUrl($withQueryString=true)
    {
    	$url	= strtolower(strstr($_SERVER['SERVER_PROTOCOL'], '/', true)).'://'.$_SERVER['HTTP_HOST'];
		//@todo 全部转到2.0框架后,以下一行应该被清除掉
		$_SERVER["REQUEST_URI"] = str_replace('index.ued','',$_SERVER["REQUEST_URI"]);    
    	if (isset ($_SERVER["REQUEST_URI"]))
    	{
    		$url	.= $_SERVER["REQUEST_URI"];
    	}
    	else
    	{
    		$url	.= $_SERVER["PHP_SELF"];
    		if (!empty($_SERVER["QUERY_STRING"]))
    		{
    			$url	.= "?".$_SERVER["QUERY_STRING"];
    		}
    	}
    	
    	if(!$withQueryString)
    	{
    		if(($queryStrPos = strpos($url, '?')) !== false)
    		{
    			$url = substr($url, 0, $queryStrPos);
    		}
    	}    	
    	return urldecode($url);
    }
    
    
    /**
     * 解析额外的url参数, 
     * @param string $aparams
     */
    public static function parseAparams($aparams)
    {
        $parsed = array();
        
        $aparams = array_chunk(explode('-', $aparams),2);
        foreach ($aparams as $param)
        {
            if(isset($param[1]))
            {
				if(isset($parsed[$param[0]])){
					if(is_array($parsed[$param[0]])) {
						array_push($parsed[$param[0]],$param[1]);
					} else {
						$parsed[$param[0]] = array($parsed[$param[0]],$param[1]);
					}
				} else {
					$parsed[$param[0]] = $param[1];
				}
                
            }
            else
            {
                 $parsed['page'] = $param[0];
            }
        }
        return $parsed;
    }
    
    /**
     * 生成以html为后缀的 $_SERVER['REQUEST_URI']
     */
    public static function getStaticScriptName()
    {
        if(isset($_SERVER['REQUEST_URI']))
        {
            return preg_replace('#\.html *$#', '', trim($_SERVER['REQUEST_URI'],'/'));
        }    
        return  null;
    }
	
	public static function parseSearchFields()
	{
		$searchFields = array();
		if(($searchStr = strstr($_SERVER["REQUEST_URI"], '/search?')) !== false)
		{		
			parse_str(str_replace('/search?','',$searchStr),$searchFields);
			return $searchFields;
		}
		else
		{
		    return array();
		}
		
	}		
	
    public static function getClientIp() {
        $ip = false;
        if(!empty($_SERVER["HTTP_CLIENT_IP"])) {
            $ip = $_SERVER["HTTP_CLIENT_IP"];
        }
        if(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(", ", $_SERVER['HTTP_X_FORWARDED_FOR']);
            if($ip) {
                array_unshift($ips, $ip);
                $ip = FALSE;
            }
            for($i = 0; $i < count($ips); $i++) {
                if(!preg_match("#^(10|172\.16|192\.168)\.#si", $ips[$i])) {
                    $ip = $ips[$i];
                    break;
                }
            }
        }
        if(!$ip)
        {
        	$ip = $_SERVER['REMOTE_ADDR'];
        }
        if(!$ip)
        {
        	$ip = $_SERVER['HTTP_X_REAL_IP'];
        }
        
        if(empty($ip)){
        	//当没有IP信息的时候写入SERVER信息
        	/*
        	$fileName = ROOT_PATH.'/data/log/'.date('Ym').'_noip_serverInfo.log';
        	$title = date('Y-m-d H:i:s').' SERVER info record:';
        	$handle = fopen($filename, 'a+');
        	if($handle!==false){
				fwrite($handle, $title."\n");
				fwrite($handle, var_export($_SERVER,true)."\n------END------\n\n");
				fclose($handle);
        	}else{
        		$f = '';
        		$serverInfo = $title."\n".var_export($_SERVER,true)."\n------END------\n\n";
        		if(file_exists($fileName)){
        			$f = file_get_contents($fileName);
        		}
        		$f .= $serverInfo;
        		file_put_contents($fileName,$f);
        	}
        	*/
        }
        return $ip;
    }	
    
    /**
     * 获取最原始的URL中的query string (在未被服务器重写之前的query string部分, 从REQUEST_URI中分析)
     */
    public static function getRawQueryString()
    {
    	static $qs;
    	if(!isset($qs))
    	{
    		$qs = strstr($_SERVER['REQUEST_URI'], '?');
    		if($qs !== false)
    		{
    			//去掉"?"
    			$qs = substr($qs, 1);
    		}
    		
    	}
    	return $qs;
    }
}