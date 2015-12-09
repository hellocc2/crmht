<?php
namespace config;
use PDO;
class Db{
	public static $default=array('driver'=>'mysqli',
						  'host'=>'103.224.22.13',
						  'port'=>3306,
						  'dbname'=>'a56rmgri_money',
						  'dbuser'=>'a56rmgri_wellier',
						  'dbpassword'=>'a;a;1987',
						  'driveroptions'=>array(PDO::MYSQL_ATTR_INIT_COMMAND=>'SET NAMES \'UTF8\'')
	
	);
	public static $products=array('driver'=>'mysql',
						  'host'=>'192.168.3.70',
						  'port'=>3306,
						  'dbname'=>'milanoo_gaea',
						  'dbuser'=>'milanoo',
						  'dbpassword'=>'milanoo',
						  'driveroptions'=>array(PDO::MYSQL_ATTR_INIT_COMMAND=>'SET NAMES \'UTF8\'')
	
	);
	public static $bi=array('driver'=>'mysql',
							  'host'=>'172.20.100.12',
							  'port'=>3307,
							  'dbname'=>'web_statis',
							  'dbuser'=>'bi',
							  'dbpassword'=>'bi',
							  'driveroptions'=>array(PDO::MYSQL_ATTR_INIT_COMMAND=>'SET NAMES \'UTF8\'')
	
	);
	public static $milanoo=array('driver'=>'mysql',
							  'host'=>'192.168.11.17',
							  'port'=>3306,
							  'dbname'=>'milanoo',
							  'dbuser'=>'milanoo',
							  'dbpassword'=>'milanoo',
							  'driveroptions'=>array(PDO::MYSQL_ATTR_INIT_COMMAND=>'SET NAMES \'UTF8\'')
		
	);
}