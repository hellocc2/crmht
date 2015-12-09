<?php
namespace Lib\common;
class Db {
	/**
	 * 数据库类
	 * @return string
	 */
	public static function get_db($config = 'default') {
		global $db;
		if ($db && $db->databaseName) {
			return $db;
		}
		$db_config = \config\Db::$$config;
		$db_connect = new \config\Db ();
		
		include_once ROOT_PATH . 'lib/adodb/adodb.inc.php';
		$db = &ADONewConnection ( $db_config["driver"] );
		$db->debug = 0;
		ini_set($db_config["driver"].'.default_port', $db_config["port"]);
		$db->Connect ( $db_config["host"], $db_config["dbuser"], $db_config["dbpassword"], $db_config["dbname"] );
		$ADODB_CACHE_DIR = ROOT_PATH . "data/db";
		$db->query ( 'SET NAMES UTF8' );
		$db->SetFetchMode ( 2 );
		//这只缓存时间
		if (! defined ( 'CacheTime' ))
			define ( 'CacheTime', 600 );
		if (! $db) {
			die ( '不能连接数据库.\n' );
		}
		//print_r($db);
		$GLOBALS ['db'] = $db;
		return $db;
	}
}