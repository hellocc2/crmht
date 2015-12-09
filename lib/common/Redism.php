<?php
namespace Lib\common;
class Redism {
	/**
	 * redis类
	 * @return string
	 */
	public static function get_redis() {
		global $redis;
		if($redis)
			return  $redis;
		$redis = new \Redis();
		//$redis->connect('localhost', 6379, 2.5);
		$redis->connect(\config\Redis::HOST, \config\Redis::PORT, \config\Redis::CONNECTION_TIMEOUT);
		return $redis;
	}
}


?>