<?php
namespace config;
use PDO;
class Db{
	public static $default=array('driver'=>'mysqli',
						  'host'=>'localhost',
						  'port'=>3306,
						  'dbname'=>'cmsht',
						  'dbuser'=>'root',
						  'dbpassword'=>'',
						  'driveroptions'=>array(PDO::MYSQL_ATTR_INIT_COMMAND=>'SET NAMES \'UTF8\'')
	
	);
	
}