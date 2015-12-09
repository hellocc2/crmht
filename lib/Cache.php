<?php
namespace Lib;
/**
 * 缓存类
 * @author Su Chao<suchaoabc@163.com>
 *
 */
class Cache {
    private static $cacheManagers = array();
    /**
     * 获取缓存类实例
     * @param string $interfaceType 缓存类型. 默认:memcache
     * @todo 当需要时实现其它缓存方式实例获取
     */
    public static function init($interfaceType='memcache')
    {
        switch ($interfaceType)
        {
            case 'memcache' :
                if(!isset(self::$cacheManagers[$interfaceType]) || !self::$cacheManagers[$interfaceType])
                {
                    self::$cacheManagers[$interfaceType] = new \Lib\cache\Memcache();
                }
                return self::$cacheManagers[$interfaceType];
                continue;
            case 'redis' :
            	if(!isset(self::$cacheManagers[$interfaceType]) || !self::$cacheManagers[$interfaceType])
            	{
            		self::$cacheManagers[$interfaceType] = new \Lib\cache\Redis();
            	}
            	return self::$cacheManagers[$interfaceType];            	
            	continue;
            default:
                trigger_error('错误的缓存接口类型:'.$interfaceType , E_USER_ERROR);
                continue;                
        }
    }
}