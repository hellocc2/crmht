<?php
namespace Lib;
use Helper\RequestUtil as R;
/**
 * 系统启动时的一些额外操作.通过{@link Bootstrap::$runCondition}及{@link Bootstrap::$excludedBootMethod}中的定义在各模块中来加载附加的过程
 * @author Su Chao<suchaoabc@163.com>
 * @since 2011-10-21
 */
class Bootstrap {
	/**
	 * 每个bootstrap方法运行的条件
	 * @var array 如:
	 * [code]
	 * $runCondition = array(
	 * 'promotionUrl' => array('index'=>array('index','poll','seall'))
	 * ); 
	 * //其中promotionUrl为方法名, index为模块名, index,poll,seall 动作名称.只要在index模块的index,poll,seall这些方法中才会运行这些方法
	 * //* 表示所有模块.如 '*'=>array('index','item')表示在所有模块的inde及item动作中只执行.
	 * //动作名称必须为'*'或者和数组.如:'*'=>'*'表示在所有模块的所有动作执行
	 * //模块名如果以"~ "开头则表示使用正则表达式,"~ "以后的部分为匹配模式; ！~ 则表示当匹配不到时就执行相应的方法
	 * [/code]
	 */
	
	private static $runCondition = array ('maCookie' => array ('*' => '*' ),'checklogin'=> array ('*' => '*' ),'startSession' => array ('*' => '*' ) );
	
	/**
	 * 不作为bootstrap运行的方法名称
	 * @var array
	 */
	private static $excludedBootMethod = array ('run', 'methodCountUp' );
	
	/**
	 * 已经运行过的方法.一般情况下方法只运行一次
	 * @var array
	 */
	private static $methodExecuted = array ();
	
	/**
	 * 调用Bootstrap中定义的方法.当不传入参数时将按{@link self::$runCondition}中的配置来运行bootstrap方法.否则强制运行$callbacks指定的方法
	 * @param string|array $callbacks 调用的方法及参数数组.如array('sayWord'=>'Hello!','startCount','endCounter'=>array(2,3)).<br />
	 * 其中sayWord为方法名,hello为sayWord所需的参数.当callback为字符串是即表示调用不带参数的方法.<br />
	 * 当传入的数字索引数组,键名为数字时,键值应该为方法名.如:array('method1','method2').<br />
	 * 当$callback为null时,则按在类中定义的顺序运行$runCondition中指定的所有方法
	 * @param boolean $force 是否强制运行.一般情况下每个方法只允许执行一次.如:Bootstrap::run(null,true)将强制执行所有方法
	 */
	public static function run($callbacks = null, $force = false) {
		static $allMethods;
		if (! $allMethods) {
			$allMethods = get_class_methods ( __CLASS__ );
		}
		
		if ($callbacks) { //运行指定的方法
			if (is_string ( $callbacks ) && in_array ( $callbacks, $allMethods, true ) && (! array_key_exists ( $callbacks, self::$methodExecuted ) || $force)) {
				$result = self::$callbacks ();
				self::methodCountUp ( $callbacks );
				return $result;
			} else if (is_array ( $callbacks )) {
				foreach ( $callbacks as $method => $args ) {
					if (is_int ( $method )) { //当传入的数字索引数组,键名为数字时,键值应该为方法名
						$method = $args;
					}
					
					if (in_array ( $method, $allMethods, true ) && ($force || ! array_key_exists ( $method, self::$methodExecuted ))) {
						$return = self::$method ( $args );
						self::methodCountUp ( $method );
						return $return;
					}
				}
			}
		} else if (is_null ( $callbacks )) { //根据配置来运行方法          
			$requestParams = \Helper\RequestUtil::getParams ();
			foreach ( $allMethods as $method ) {
				$allowedExecute = false;
				if (! in_array ( $method, self::$excludedBootMethod, true ) && ($force || ! array_key_exists ( $method, self::$methodExecuted ))) { //当方法不被排除
					if (key_exists ( $method, self::$runCondition )) { //当方法允许被执行       
						foreach ( self::$runCondition [$method] as $k => $v ) {
							if (0 === strpos ( $k, '~' )) { //正则模式
								$patternType = 1;
							} else if (0 === strpos ( $k, '!~' )) { //正则模式取反
								$patternType = 2;
							} else { //非正则模式
								$patternType = 0;
							}
							
							if (0 === $patternType) { //非正则匹配
								if ($k === '*' || $k === $requestParams->module) { //允许在所有的模块中执行
									if ($v === '*') { //允许在所有的动作中执行
										$allowedExecute = true;
										break;
									} else if (is_array ( $v ) && in_array ( $requestParams->action, $v, true )) { //允许在指定的动作中执行
										$allowedExecute = true;
										break;
									}
								}
							} else { //正则匹配
								if ($patternType === 1) {
									$kPattern = preg_replace ( '#^~ +#', '', $k );
								} else {
									$kPattern = preg_replace ( '#^!~ +#', '', $k );
								}
								$matched = preg_match ( '#' . $kPattern . '#', $requestParams->module );
								if ($patternType === 2) {
									$matched = ! $matched;
								}
								if ($matched) { //允许在所有的模块中执行
									if ($v === '*') {
										//允许在所有的动作中执行
										$allowedExecute = true;
										break;
									} else if (is_array ( $v ) && in_array ( $requestParams->action, $v, true )) {
										//允许在指定的动作中执行
										$allowedExecute = true;
										break;
									}
								}
							}
						}
					}
				}
				
				if ($allowedExecute) {
					self::$method ();
					self::methodCountUp ( $method );
				}
			}
		}
	}
	
	/**
	 * 启动session,初始化一些session参数
	 */
	private static function startSession() {
		if (! session_id ()) {
			session_name ( "milanooId" );
			session_start ();
		}
	}
	
	/**
	 * 客户端缓存控制
	 */
	private static function setClientCache() {
		header ( 'Cache-Control:no-cache,must-revalidate' );
		header ( 'Pragma:no-cache' );
		header ( 'Expires:' . gmdate ( "D, d M Y H:i:s", time () - 3600 * 24 * 100 ) . ' GMT' );
	}
	
	/**
	 * 获取已经执行过的方法
	 */
	public static function getExecutedMethods() {
		return self::$methodExecuted;
	}
	
	/**
	 * 方法调用次数记录加一
	 * @param string $methodName
	 * @return int 方法的调用次数
	 */
	private static function methodCountUp($methodName) {
		if (! isset ( self::$methodExecuted [$methodName] )) {
			self::$methodExecuted [$methodName] = 1;
		} else {
			self::$methodExecuted [$methodName] ++;
		}
		return self::$methodExecuted [$methodName];
	}
	
	private static function checklogin() {
		//$a = file_get_contents("http://ht.milanoo.com/api/test.php");
		//print_r($a);exit;
	}
	
	private static function maCookie() {
		$params_all = R::getParams ();
		if (!empty ( $params_all->starttime )) {
			$ma_starttime = $params_all->starttime;
			$_SESSION ["ma_starttime"] = $ma_starttime;
		} elseif (!isset($_SESSION ["ma_starttime"])) {
			$ma_starttime = date ( "Y-m-d", strtotime ( "-3 day" ) );
			$_SESSION ["ma_starttime"] = $ma_starttime;
		}
		if (!empty ( $params_all->endtime )) {
			$ma_endtime = $params_all->endtime;
			$_SESSION ["ma_endtime"] = $ma_endtime;
		} elseif (empty($_SESSION ["ma_endtime"])) {
			$ma_endtime = date ( "Y-m-d", strtotime ( "-1 day" ) );
			$_SESSION ["ma_endtime"] = $ma_endtime;
		}
		if (!empty( $_GET['lang'] )) {
			$ma_lang = $_GET['lang'];
			$_SESSION ["ma_lang"] = $ma_lang;
			//echo '-',$_SESSION ["ma_lang"],'-';
		}elseif (empty($_SESSION ["ma_lang"])) {
			$_SESSION ["ma_lang"] = 'all';
		}
		if (!empty( $_GET['websiteId'] )) {
			$ma_websiteId = $_GET['websiteId'];
			$_SESSION ['ma_websiteId'] = $ma_websiteId;
			//echo '-',$_SESSION ["ma_websiteId"],'-';
		}else{
			if(empty($_SESSION ['ma_websiteId'])){
				$_SESSION ['ma_websiteId']=1;
			}
		}
		
	}
}