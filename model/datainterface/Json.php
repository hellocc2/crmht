<?php
namespace Model\Datainterface;
use Lib\common\Debug as debug;
/**
 * json数据协议的webservice 访问类.结果缓存时间为5分钟,如果定义了\config\Memcache::DEFAULT_CACHE_TIME则缓存时间为此处定义时间.如果要修改单个数据的缓存时间在调用call方法之前使用{@link setCacheTime}进行设置.
 * @uses \config\Webserice
 * @todo https及更多的协议支持
 */
class Json{
    /**
     * webservice的调用地址.默认为:Webservice::SERVER_BASE
     * @var string 
     */
    private $serviceUrl;
    /**
     * 请求方式.默认为GET
     * @var string
     */
    private $requestMethod = 'GET';
    /**
     * 与webervice服务返回的错误信息
     * @var array 'code'=>'msg'
     */    
    private $errorInfo=null;
    /**
     * webservice 返回的原始内容(只在DEBUG模式下有效).
     * @var string
     */
    private $rawResponse;
    /**
     * 通信过程信息(只在DEBUG模式下有效)
     * @var array
     */
    private $communicationInfo;
    /**
     * 页面内结果缓存.避免在一个请求中使用同样的参数多次调用webservice
     * @var array
     */
    private static $cachedResult=array();
    /**
     * webservice调用信息栈(调式时使用).
     * @var array
     */
    private static $wsCallInfoStack=array();
	/**
     * 是否需要缓存
     * @var int
     */
	private $needCache = true;
	/**
     * 设置缓存时间
     * @var int
     */
	private $cacheTime = 300;
	/**
	 *设置副缓存后缀
	 *@var String
	 */
	private $suffix = '_c';
    /**
     * webservice 的url地址
     * @param string $url
     */
    public function __construct($url=null){
        $this->serviceUrl = is_string($url) ? $url : \config\Webservice::SERVER_BASE;      
        if(defined('\config\Memcache::DEFAULT_CACHE_TIME'))
        {
            $this->cacheTime = \config\Memcache::DEFAULT_CACHE_TIME;
        }
        
        if(defined('\config\Memcache::CACHE_OFF') && \config\Memcache::CACHE_OFF)
        {
            $this->needCache = false;
        }
    }
	
    /**
     * 调用webservice.例:<br />
     * [code]
     * $interface = $this->getInterface('json');//在model中实例化数据接口
     * $data = $interface->call(null,array('id'=>23,'status'=0));//使用\config\Webserice::serviceUrl 作为webservice的URL
     * $data2 = $interface->call('product/product/getDetails',array('pid'=>3123)); //使用\config\Webserice::serviceUrl并附加根据模块名和动作名组成的子空间地址
     * [/code]
     * 当直接使用\config\Webserice::serviceUrl定义的地址或setUrl()指定的地址时需留$subNamespace为null
	 * @param string $subNamespace  根据命名空间,模块及方法区分的子地址,属于webservice url中的一段
     * @param array $paramGet 附加到url中进行提交的数据
     * @param array $paramPost 通过post进行提交的数据
     * @param boolen $forceArray 强制返回关联数组.默认true
     * @param string $appendix api地址后缀名称.默认为.htm
     * @return Object json deocde后的对象
     */
    public function call($subNamespace,$paramGet=array(),$paramPost=array(),$forceArray=true,$appendix='.htm')
    {
        if(DEBUG_MODE)
        {
           $invokeStartTime = sprintf('%.8f', microtime(true));
           $invokeKey = 'CallStartTime:'.$invokeStartTime;
           $e = new \Exception();
           self::$wsCallInfoStack[$invokeKey]['StackTrace'] = $e->getTraceAsString();                              
        }
        $serviceUrl = $this->serviceUrl;
        /**
         * 作为页面内及memcache缓存key
         * @var string
         */
        $cacheKey = md5($serviceUrl . $subNamespace . serialize($paramPost) . serialize($paramGet));
        if(isset(self::$cachedResult[$cacheKey]))
        {
           return self::$cachedResult[$cacheKey]; 
        }        
        
        $result = false;

        //20120905需求，当http://www.milanoo.com/?nocache=true，则不需要缓存
    	if(isset($_GET['nocache']) && $_GET['nocache']=='true'){
        	$this->needCache = false;
        }
        
    	if($this->needCache) {
    	    ini_set('memcache.chunk_size',1042 * 90);
			$m = \Lib\Cache::init();
			$result = $m->get($cacheKey); 			
			if(DEBUG_MODE)
			{
			    self::$wsCallInfoStack[$invokeKey]['TimeConsumedInFetchFromMemcache'] = sprintf('%.8f', (microtime(true)-$invokeStartTime)*1000).'ms';
			}
		}

		//当读取缓存失败或者不需要缓存时
		if(false === $result || !$this->needCache) 
		{
			if($this->needCache) { 
				$dataBak = $m->get($cacheKey.$this->suffix);
				if(false !== $dataBak)
				{//主键缓存失败时,从副键缓存中读取至主键,以便其后的请求读取成功
				    $m->set($cacheKey,$dataBak,0,$this->cacheTime);
				    if(DEBUG_MODE)
				    {
					    //标记读取了副键缓存的数据
					    self::$wsCallInfoStack[$invokeKey]['CacheHit'] = 2;
				    }
				}
				
				if(DEBUG_MODE)
    			{
    			    self::$wsCallInfoStack[$invokeKey]['TimeConsumedInFetchFromMemcache'] = sprintf('%.8f', (microtime(true)-$invokeStartTime)*1000).'ms';
    			    if(false !== $dataBak)
    			    {
    			        self::$wsCallInfoStack[$invokeKey]['ContentLengthStoredInMemcache'] = number_format(strlen($dataBak));
    			    }
    			}				
			}
            if(is_string($subNamespace))
            {//@todo webservice url地址需根据webservice的地址规则进行改变
                $serviceUrl = rtrim($serviceUrl,'?\/').'/'.$subNamespace.$appendix;
            }
            
            $appendedValues = array();
            if(is_array($paramGet))
            {
                foreach ($paramGet as $k=>$v)
                {
                    $appendedValues[] = $k.'='.urlencode($v);
                }
            }
            $appendedValues = implode('&', $appendedValues);
            
            if(!empty($appendedValues))
            {
                if(strpos($this->serviceUrl, '?') === false)
                {
                   $appendedValues = '?'.$appendedValues;
                }
                else 
                {
                    $appendedValues = '&'.$appendedValues;
                }
                $serviceUrl .= $appendedValues;
            }
		    //die($serviceUrl);

    		$ch = curl_init();
            if($this->requestMethod == 'POST')
            {
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $paramPost);
            }	   
            curl_setopt_array($ch, array(CURLOPT_URL=>$serviceUrl,
                                         CURLOPT_RETURNTRANSFER => true,
                                       //  CURLOPT_ENCODING => '',//注释encoding头,保持webservice返回的压缩数据,直接存入memcache
                                         CURLOPT_TIMEOUT => \config\Webservice::TIMEOUT,
            							 CURLOPT_HEADER => true
            ));
            
            if(DEBUG_MODE)
            {
                self::$wsCallInfoStack[$invokeKey]['TimeStart'] = $invokeStartTime;
                self::$wsCallInfoStack[$invokeKey]['URL'] = $serviceUrl;
                self::$wsCallInfoStack[$invokeKey]['GET'] = $paramGet;
                self::$wsCallInfoStack[$invokeKey]['POST'] = $paramPost;
            }
            $rawReturn = curl_exec($ch);

            if(DEBUG_MODE)
            {
                $this->communicationInfo = curl_getinfo($ch);
                self::$wsCallInfoStack[$invokeKey]['Info'] = $this->communicationInfo;
                self::$wsCallInfoStack[$invokeKey]['TimeConsumedInWebservice'] = sprintf('%.8f', (microtime(true)-$invokeStartTime)*1000).'ms';
            }  
            if($errNo = curl_errno($ch))
            {
            	$err = new \Exception('webservice错误:'."\n".var_export( $this->communicationInfo,true)."\n");
            	error_log($err->getMessage()."\n".$err->getTraceAsString());
            	            	
                $this->errorInfo[$errNo] = curl_error($ch);
                if(DEBUG_MODE)
                {
                    self::$wsCallInfoStack[$invokeKey]['Error'] = $this->errorInfo;
                    debug::setInfo('WSCall', self::$wsCallInfoStack);
                }         
                curl_close($ch);
                if($this->needCache && false !== $dataBak)
                {//webservice 调用失败,返回副键值
                    $dataBak = gzuncompress($dataBak);                    
                	if(DEBUG_LEVEL === 0)
    		        {
    		            self::$wsCallInfoStack[$invokeKey]['CachedData'] = $dataBak;
    		        }            		        
    		        return self::$cachedResult[$cacheKey] = json_decode($dataBak,$forceArray); 
                }  
                else 
              {
                    return false;
                }                  
            }
            curl_close($ch);
            $parsedRawReturn = explode("\r\n\r\n",$rawReturn);
            if(count($parsedRawReturn) > 1)
            {
            	$rawReturn = array_pop($parsedRawReturn);            	
            }     
            else
           {
          		$rawReturn = '';
            }   
            $rawReturnHeaders = explode("\r\n", array_pop($parsedRawReturn));            
            $headers = array('Protocol'=>array_shift($rawReturnHeaders));
                        
            foreach($rawReturnHeaders as $header)
            {
            	$header = explode(': ', $header);
            	$headers[$header[0]] = $header[1];
            }
           
            if(isset($headers['Content-Encoding']))
            {
            	$return = gzuncompress($rawReturn);
            }           
            else
          {
          		$return = $rawReturn;
           		$rawReturn = gzcompress($rawReturn, 9);
            }

            if(DEBUG_MODE)
            {
                $this->rawResponse = $return;
            }      
  			
            $return = json_decode($return,$forceArray);

		    if(DEBUG_MODE)
            {
                if(DEBUG_LEVEL === 0)
                {
                    self::$wsCallInfoStack[$invokeKey]['RawResponse'] = $this->rawResponse;
                }
                debug::setInfo('WSCall', self::$wsCallInfoStack);
            }  
                
            if(($decodeError = json_last_error()) !== JSON_ERROR_NONE && !$this->needCache)
            {//webservice返回的数据解码错误
                $err = new \Exception('数据解析错误.'."\n".'webservice 返回:'.$this->rawResponse."\n");
                error_log($err->getMessage()."\n".$err->getTraceAsString());
                return false;
            }
            else 
          {
    			if($this->needCache)
    			{
    			    if($decodeError !== JSON_ERROR_NONE || !isset($return['code']) || $return['code'] >= 10000)
    			    {//解码失败,或者没有返回状态code属性, 或者webservice APP报错系统错误
    			    	if($dataBak !== false)
    			    	{//结果错误时则使用上次的正确结果(非开发模式下)
    			    		$return = gzuncompress($dataBak);
    			    		$return = json_decode($return,$forceArray);
    			    		$rawReturn = &$dataBak;
    			    	}   	
    			    }
    			    else
    			    {//当只有结果正确时才更新永久缓存,尽可能减小cache操作
						$m->set($cacheKey.$this->suffix,$rawReturn,0,0);
    			    }
    				$m->set($cacheKey,$rawReturn,0,$this->cacheTime);    				
    			}
    			self::$cachedResult[$cacheKey] = $return;
    		}
    		
    		if(DEBUG_MODE)
    		{
    		    debug::setInfo('WSCall', self::$wsCallInfoStack);
    		} 
            return $return;
		}
		else 
		{
		    if(DEBUG_MODE)
		    {
                self::$wsCallInfoStack[$invokeKey]['ContentLengthStoredInMemcache'] = number_format(strlen($result));		        
		    }
		    
		    $result = gzuncompress($result);
		    if(DEBUG_MODE)
		    {
		        //标记读取了副键缓存的数据
		        self::$wsCallInfoStack[$invokeKey]['CacheHit'] = 1;
		        if(DEBUG_LEVEL === 0)
		        {
		            self::$wsCallInfoStack[$invokeKey]['CachedData'] = $result;
		        }			    	        
		        debug::setInfo('WSCall', self::$wsCallInfoStack);
		    }
		    
			return self::$cachedResult[$cacheKey] = json_decode($result,$forceArray);
		}
    }
    /**
     * 设置请求方式
     * @param string $method 请求方式.GET或POST
     */
    public function setMethod($method)
    {
        switch ($method)
        {
            case 'POST' :
                $this->requestMethod = 'POST';
            continue;
            default:
                $this->requestMethod = 'GET';
        }
        return $this->requestMethod;
    }
    /**
     * 设置webservice的调用地址.如果不调用此方法重新设置webservice url,则将使用{@link Webservice::SERVER_BASE}作为webservice的调用地址
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->serviceUrl = $url;
    }
    /**
     * 
     * 获取webservice 调用信息
     */
    public function getInfo()
    {
        return $this->communicationInfo;
    }
    
    /**
     * 获取webservice返回原始数据
     * @return string
     */
    public function getRawResponse()
    {
        return $this->rawResponse;
    }
    /**
     * 根据webservice 的名称和动作名称获取完整的webservice url
     * @param string $wsName webservice 名称 
     * @param string $wsAction  webservice 动作名
     */
    public function getApiUrl($wsName,$wsAction)
    {
        
    }
	/**
     * 设置缓存过期时间
     * @param int $time
     */
	public function setCacheTime($time=300){
		$this->cacheTime = $time;
	}
	
	/**
     * 设置是否需要缓存
     * @param boolean $flag
     */
	public function setNeedCache($flag = true){
		$this->needCache = $flag;
	}
	
	public static function getCachedResult()
	{
	    return self::$cachedResult;
	}
	
	private function getStreamHandler($handler='curl')
	{
	    
	}
}