<?php
namespace config;
/**
 * Memcache 配置类.使用于{@link \Lib\cache\Cache} 
 *
 */
class Memcache{
	/**
	 * 服务器地址,多台服务器用逗号分隔
	 * @var string
	 */
    const HOST = '192.168.0.161';
    /**
     * 服务器端口,多个端口用逗号分隔.如果端口个数少于服务器个数不相等,则下一台服务器将使用上一个端口
     * @var sting
     */
    const PORT = '11211';
    /**
     * 连接超时时间,一般不大于1秒
     * @var int
     */
    const CONNECTION_TIMEOUT = 3;
    /**
     * 默认缓存时间
     * @var int
     */
    const DEFAULT_CACHE_TIME = 120;
    
    /**
     * 关闭所有接口缓存
     * @var boolean
     */
    const CACHE_OFF = false;
    
    /**
     * 是否使用持久连接
     * @var false
     */
    const PERSISTENT = false;
}