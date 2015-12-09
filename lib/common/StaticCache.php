<?php 
namespace Lib\common;
use Helper\RequestUtil;
/**
 * 静态文件获取类
 * @author Su Chao<suchaoabc@163.com>
 * @since 2011-09-03
 */
class StaticCache{   
    /**
     * 解析后的客户端HTTP请求参数
     * @var Object
     */
    protected static $requestParams;
    
    /**
     * 输出静态文件
     * @param boolean $forceExit 输出静态页面后,是否强制退出PHP程序.默认为true
     * @return boolean 当不需要缓存或者缓存过期时返回false,读取缓存成功后返回true
     */
    public static function cacheResponse($forceExit=true)
    {        
        if(!HTML_CACHE_ON)
        {
            return false;    
        }        
        
        if(!isset(self::$requestParams))
        {
            self::$requestParams = RequestUtil::getParams();
        }
		//暂不缓存搜索页
		if(RequestUtil::parseSearchFields())
		{
			return false;
		}
        //此module action组是否需要缓存
        if(self::needCache(self::$requestParams->module,self::$requestParams->action))
        {
            $cachedFileName = self::generateFilePathName(); 
            if(self::isCacheFileExpired($cachedFileName))
            {//缓存过期则返回,继续执行其他程序
                
                return false;
            }
            else 
            {
               //执行其它额外启动过程
                \Lib\Bootstrap::run();
                
                //获取静态缓存
                require($cachedFileName); 
                if($forceExit)
                {
                   die();    
                }
                else 
                {
                    return true;
                }
            }
        }
        else 
        {
            return false;
        }
       
    }

    public static function generateFilePathName()
    { 
        $cachedFileName = '';
        if(!isset(self::$requestParams->module) && !isset(self::$requestParams->action))
        {
            self::$requestParams =  RequestUtil::getParams();
            self::$requestParams->module = self::$requestParams->module;
            self::$requestParams->action = self::$requestParams->action;
        }   
        $dirName =   HTML_CACHE_ROOT.SELLER_LANG.'-'.CurrencyCode.'/'.self::$requestParams->module.'-'.self::$requestParams->action.'/';
        switch (self::$requestParams->module) {
            case 'index':
                switch (self::$requestParams->action)
                {
                    case 'index':
                        $cachedFileName = $dirName.'index.html';
                    continue;
                    case 'seeall' :
                         $cachedFileName = $dirName.'seeall.html';
                        continue;
                    default:
                        $cachedFileName = $dirName.self::encodeFileName(self::$requestParams);
                    continue;
                }
            continue;
                     
            case 'thing' :
                switch (self::$requestParams->action)
                {
                    case 'glist':
                        if( 3 == count(self::$requestParams) && isset(self::$requestParams->class))
                        {//目录页不带分页参数                         
                            $cachedFileName = $dirName.RequestUtil::getStaticScriptName().'.html';
                        }
                        else
                        {//目录页带了其他参数
                            $scriptName = 
                            $cachedFileName = $dirName.RequestUtil::getStaticScriptName()
                                              .'-'
                                              .self::encodeFileName(self::$requestParams)
                                              .'.html'; 
                        }
                    continue;
                    
                    case 'item' :                                               
                        $cachedFileName = $dirName. self::genItemDir(self::$requestParams->id);
                        $cachedFileName .= RequestUtil::getStaticScriptName().'.html';                                            
                    continue;
                    
                    case 'index' :
                        $cachedFileName = $dirName.'index.html';
                    default: 
                        $cachedFileName = $dirName.self::encodeFileName(self::$requestParams);
                    continue;
                }
            continue;
            
            case 'Brand' : 
                    switch (self::$requestParams->action)
                    {
                        case 'index' : 
                             $cachedFileName = $dirName. 'index.html';                            
                        continue;
                        case 'item' :          
                             if(count(self::$requestParams) === 3 && isset(self::$requestParams->bid))
                             {
                                 $cachedFileName = $dirName.'b'.self::$requestParams->bid.'.html';
                                 continue;                                                                                                                               
                             }
                             else if(count(self::$requestParams) > 3)
                             {
                                 $cachedFileName = $dirName.'b'.self::$requestParams->bid
                                                   .'-'.self::encodeFileName(self::$requestParams)
                                                   .'.html';
                                 continue;
                             }
                        default:
                            $cachedFileName = $dirName.self::encodeFileName(self::$requestParams);
                        continue;
                    }
            continue;
           
            case 'promotions' :
                switch (self::$requestParams->action)
                {
                    case 'specials' :
                        if(isset(self::$requestParams->params['id']) && count(1 == self::$requestParams->params))
                        {
                            $cachedFileName = $dirName.RequestUtil::getStaticScriptName();
                        }
                        else
                        {
                           $cachedFileName = $dirName.self::encodeFileName(self::$requestParams); 
                        }
                    continue;
                    
                    default: 
                        $cachedFileName = $dirName.self::encodeFileName(self::$requestParams);
                    continue;
                }
                
            continue;
            
            case 'sale' :
                switch (self::$requestParams->action)
                {
                    case 'index' :
                         $cachedFileName = $dirName. 'index.html'; 
                    continue;
                    
                    case 'gilist' :
                        $cachedFileName = $dirName. 'index.html'; 
                    continue;
                    default: 
                        $cachedFileName = $dirName.self::encodeFileName(self::$requestParams);
                    continue;
                }
                
            continue;
                        
            default:
                $cachedFileName = $dirName.self::encodeFileName(self::$requestParams);
            continue;
        }  
        return $cachedFileName;  
    }     
    /**
     * 判断module及action是否需要缓存
     * @param string $module 模块名
     * @param string $action  动作名
     * @return boolean 
     * @global $staticCacheModuleAction
     */
    public static function needCache($module,$action)
    {
        global $staticCacheModuleAction;
        if($_SERVER['REQUEST_METHOD'] != 'GET')
        {//暂只缓存get请求
          return false;  
        }
        if(key_exists($module, $staticCacheModuleAction) && in_array($action, $staticCacheModuleAction[$module]))
        {
            return true;
        }
        return false;
    }
    /**
     * 生成文件名
     * @param array $params  $_GET及$_POST合并后的参数
     * @return 根据参数编码后的字符串
     */
    public static function encodeFileName($params)
    {
        $nameStr = md5(serialize($params));
        return $nameStr;
    }
        
    /**
     * 检查文件似否存在且未过期
     * @param string $filePath 文件的绝对路径
     */
    public static function isCacheFileExpired($filePath)
    {
        if(!file_exists($filePath))
        {
            return true;
        }
        else
        {
            $mtime = filemtime($filePath);
            if(time() - $mtime < HTML_STATIC_FILE_LIFETIME)
            {
                return false;
            }
            return true;
        }
    }
    
    /**
     * 根据产品ID生成产品的文件夹
     * @param int $productId 产品ID
     * @return string 产品ID hash后的存储目录
     */
    public static function genItemDir($productId)
    {
        /**
         * 产品ID标准长度
         * @var int
         */
        $stdLen = 8;
        /**
         * 文件夹名称最大长度         
         * @var int
         */
        $dirNameLen = 4;
        $productId = str_pad($productId, $stdLen,'0');
        $idLen = strlen($productId);
        $dirName = substr($productId, 0,$dirNameLen);
        $path = DIRECTORY_SEPARATOR . $dirName . DIRECTORY_SEPARATOR;
        return $path;
    }
}