<?php
namespace Lib\cache;
/**
 * 基于pecl提供的Memcache类扩展.
 * @author Su Chao<suchaoabc@163.com> 
 */
class Memcache extends \Memcache{
    /**
     * Memcache 服务器地址
     * @var string
     */
    private $host='127.0.0.1';
    /**
     * Memcache 服务器端口
     * @var int
     */
    private $port=11211;
    /**
     * memcache连接超时时间     
     * @var int
     */
    private $connectionTimeout=1;    
    
    /**
     * 是有使用持久连接
     * @var Boolean
     */
    private $persistent = false;
    
    public function __construct()
    {
    	static $servers = array();
    	static $ports = array();
        if(defined('\config\Memcache::HOST'))
        {
            $this->host = \config\Memcache::HOST;            
        }
        
        if(defined('\config\Memcache::PORT'))
        {
            $this->port = \config\Memcache::PORT;            
        }  
        
        if(defined('\config\Memcache::CONNECTION_TIMEOUT'))
        {
            $this->connectionTimeout = \config\Memcache::CONNECTION_TIMEOUT;            
        }         

        if(defined('\config\Memcache::PERSISTENT'))
        {
        	$this->persistent = \config\Memcache::PERSISTENT;
        }

        if(empty($servers))
        {
	        $servers = explode(',', $this->host);
	        $ports = explode(',', $this->port);
	        $port = empty($ports) ? '11211' : current($ports);
        }

        foreach ($servers as $k => $v)
        {
        	if(isset($ports[$k]))
        	{
        		$port = $ports[$k];
        	}
        	$this->addServer($v, $port, $this->persistent,1,$this->connectionTimeout,15,true,array($this,'logFailure'));
        }  
    }
    
    public function logFailure($host,$tcpPort,$udpPort,$errMsg,$errNo)
    {
    	error_log('Memcache出错:'."\t".$host.':'.$tcpPort."\t".$errNo.'-'.$errMsg);
    }
}