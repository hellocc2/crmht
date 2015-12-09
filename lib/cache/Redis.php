<?php
namespace Lib\cache;
/**
 * 基于pecl提供的Redis类扩展
 * @author Su Chao<suchaoabc@163.com>
 *
 */
class Redis extends \Redis{
	/**
	* Redis 服务器地址
	* @var string
	*/
	private $host='127.0.0.1';
	/**
	 * Redis 服务器端口
	 * @var int
	 */
	private $port=6379;
	/**
	 * Redis服务器连接超时时间
	 * @var int
	 */
	private $connectionTimeout=1;
	
	public function __construct()
	{
		if(defined('\config\Redis::HOST'))
		{
			$this->host = \config\Redis::HOST;
		}
	
		if(defined('\config\Redis::PORT'))
		{
			$this->port = \config\Redis::PORT;
		}
	
		if(defined('\config\Redis::CONNECTION_TIMEOUT'))
		{
			$this->connectionTimeout = \config\Redis::CONNECTION_TIMEOUT;
		}		 
		$this->connect($this->host,$this->port,$this->connectionTimeout);
	}
	
	/**
	 * 存入数据.
	 * @param string $key
	 * @param string $value
	 * @param int $flag 不使用.仅用于兼容Memecache::set()方法
	 * @param int $expire 过期时间
	 * @return Boolean
	 */
	public function set($key,$value,$flag=null,$expire=0)
	{
		if($expire === 0)
		{
			$expire = 9999999; 
		}
		return $this->setex($key,$expire,$value);
	}
}