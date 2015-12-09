<?php
namespace config;
/**
 * Redis 配置类.使用于{@link \Lib\cache\Cache} 
 *
 */
class Redis{
    const HOST = '127.0.0.1';
    const PORT = 6379;
    /**
     * 连接超时时间,一般不大于1秒
     * @var int
     */
    const CONNECTION_TIMEOUT = 1;
    /**
     * 默认缓存时间
     * @var int
     */
    const DEFAULT_CACHE_TIME = 10;
    
    /**
     * 关闭所有接口缓存
     * @var boolean
     */
    const CACHE_OFF = false;
}