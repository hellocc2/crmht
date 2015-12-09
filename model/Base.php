<?php
namespace Model;
/**
 * 业务基类模型.<br />目前,继承此基类的model类的主要目的为封装各模块的各类事务,以实现事务的重用.<br />事务过程中不宜使直接使用系统的各类超全局变量,如:session,request
 * @author suchao<suchaoabc@163.com>
 * @todo 目前每一种类型的接口只允许一个实例.不能实现对同一种类型的接口,根据不同的初始化参数来提供不同的实例.如果以后这种情况成
 */
class Base{
    /**
     * 数据操作接口.支持json,database,soap.
     * @todo 暂只实现json接口 
     * @todo 完善的错误消息处理.包括错误消息规范定义等.
     * @var string
     */
    private $interfaceType = 'json';
    /**
     * 已经初始的数据接口
     * @var array
     */
    private static $initializedInterfaces = array();
	/**
	 * 错误栈
	 * @var array 格式:array('错误代码,整型,如:0110'=>'错误信息内容,字符串型').如:
	 * [code]
	 * $errors = array(array('0110'=>'webservice连接超时'),
	 * 				   array('0112'=>'无法连接webservice服务器'),
	 * 				   array('0113'=>'webservice返回了错误的数据格式'),
	 * 				   array('0200'=>'SQL执行出错')
	 * );
	 * [/code]
	 */
	private $errors=array();
	/**
	 * 获取上一次发生的错误信息
	 * @return array {@link self::$previousError} array('code'=>'错误代码,整型,如:0110','msg'=>'错误信息内容,字符串型.多条消息用"||"分割')
	 */
	public function getPrevError()
	{
		return current($this->errors);
	}
	/**
	 * 提交错误代码及错误消息至错误栈
	 * @param int $code 错误代码
	 * @param string $msg 错误消息
	 */
	protected function error($code,$msg)
	{
	    $this->errors[] = array((int)$code => (string)$msg);
	}
	/**
	 * 获取错误栈中的所有错误
	 */
	protected function getErrors()
	{
	    return $this->errors;
	}
	/**
	 * 获取数据接口实例.
	 * @param string $type 数据操作接口类型(默认为Json).参考{@link $interfaceType}.当type为null时,使用当前已经定义的数据接口,否则使用type所指定的接口类型
	 * @param mixed $initArgs 借口初始化时所需参数
	 * @return \Model\Datainterface\Json
	 */
	public function getInterface($type=null,$initArgs=null)
	{
	    if(!is_null($type))
	    {
	        if(!$this->setInterfaceType($type))
	        {
	            return false;
	        }
	    }
	    if(false === ($instIndex = self::interfaceInstanceExists($this->interfaceType,$initArgs)))
	    {
	           return $this->initInterface($initArgs);	       
	    }
	    else
	   {
	    	return self::$initializedInterfaces[$this->interfaceType][$instIndex]['instance'];	    	
	    }
	    
	}
	/**
	 * 设置数据接口类型	 
	 * @param string $type 数据操作接口类型.参考{@link $interface}
	 */
	protected function setInterfaceType($type)
	{
	    static $validInterfaces = array('json','database','soap');
	    if(!in_array($type, $validInterfaces,true))
	    {	    	
	        $this->error('3010','错误的数据接口类型'.$type);       
	        return false;
	    }
	    $this->interfaceType = $type;
	    return true;
	}
	
	/**
	 * 初始化数据操作接口对象
	 * @param mixed $initArgs 借口初始化时所需参数
	 * @todo 除json之外的其它接口
	 * @return boolen|Object 初始化成功,则返回指定的类型接口实例, 否则false
	 */
	private function initInterface($initArgs=null)
	{
	    switch($this->interfaceType)
	    {
	        case 'json' : 
	        	$inst = new Datainterface\Json($initArgs);
	        	if($inst === false)
	        	{
	        		return false;
	        	}
	            self::$initializedInterfaces[$this->interfaceType][] = array('initArgs'=>$initArgs,'instance' => $inst);	            	            
	            return $inst;
	            case 'database' :
	            	$inst = new Datainterface\Db($initArgs);
	            	if($inst === false)
	            	{
	            		return false;
	            	}
	            	self::$initializedInterfaces[$this->interfaceType][] = array('initArgs'=>$initArgs,'instance' => $inst);
	            	return $inst;	            
	       		default:
	            return false;
	    }
	}
	
	/**
	 * 数据接口实例是否存在
	 * @param string $type 接口类型
	 * @param mixed $initArgs 接口初始化参数
	 * @return boolean|int  如果相同类型相同初始化参数的接口实例已经存在,则返回此类型接口的索引号.否则false. 由于索引号可能为0,所以需对返回结果进行严格比较
	 */
	public static function interfaceInstanceExists($type,$initArgs=null)
	{
		if(!isset(self::$initializedInterfaces[$type]))
		{
			return false;
		}
		else
		{
			foreach(self::$initializedInterfaces[$type] as $k => &$i)
			{
				if($i['initArgs'] === $initArgs)
				{
					return $k;
				}
			}
		}
		return false;		
	}
}