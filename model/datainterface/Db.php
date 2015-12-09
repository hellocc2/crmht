<?php
namespace Model\Datainterface;
use PDO;
/**
 * 基于PDO的数据库访问抽象层
 * @author Su Chao<suchaoabc@163.com>
 *
 */
class Db extends PDO{
	/**
	 * 初始化Db层实例
	 * @param array|object $params 数据库初始化连接参数.根据不同的数据库类型,参数的元素个数可能有所不同.<br />
	 * 如.<br />
	 * [code]
	 * $dbParams = array('driver'=>'mysql',
	 * 					 'host'=>'127.0.0.1',
	 * 					 'port'=>3306,
	 * 				     'dbname'=>'milanoo'
	 * 					 'dbuser'=>'admin',
	 * 					 'dbpassword'=>'123',
	 * 					 'driveroptions'=>array(PDO::MYSQL_ATTR_INIT_COMMAND=>'SET NAMES \'UTF8\'',
	 * 										    PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true
	 * 										    )
	 * 				);
	 * $link = new Db($dbParams);
	 * [/code]
	 */
	public function __construct($params)
	{
		$params = (object) $params;
		switch ($params->driver)
		{
			case 'mysql':
				$dsn = 'mysql:dbname='.$params->dbname.';host='.$params->host.';port='.$params->port;
				if($link = parent::__construct($dsn,$params->dbuser,$params->dbpassword,$params->driveroptions))
				{
					return $link;
				}
				else
				{
					return false;
				}
				continue;
			default :
				throw new PDOException('数据库类型未指定或不支持', 4000, $previous);
		}
	}
}