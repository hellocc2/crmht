<?php
error_reporting ( E_ALL ^ E_NOTICE );
ini_set ( 'display_errors', 'On' );
date_default_timezone_set ( 'Asia/Chongqing' );
ini_set ( 'memory_limit', '800M' );

/* 配置数据库 */

/* ora 的 */
$ora_host = "192.168.11.26";
$ora_port = "1521";
$ora_sid = "orcl";
$ora_username = "bi";
$ora_password = "bi";
$charset = "UTF8"; // # zhs16gbk ###

/* milanoo 主站的 */
define ( "mysql_host", "192.168.3.70" );
define ( "mysql_port", "3306" );
define ( "mysql_username", "milanoo" );
define ( "mysql_password", "milanoodb" );

/* web_statis 的 */
// ini_set ( 'mysql.default_port', '5029' );
function db_connect() {
	$web_statis_db = mysql_connect ( "192.168.11.23", "bi", "bi", true ) or die ( "Cann't open database!!! case:" . mysql_error () );
	mysql_select_db ( 'web_statis' );
	$mysql_result = mysql_query ( "set names 'utf8'" );
	return $web_statis_db;
}
function milanoo_connect() {
	$milanoo_db = mysql_connect ( mysql_host, mysql_username, mysql_password, true ) or die ( "Cann't open database!!! case:" . mysql_error () );
	mysql_select_db ( 'milanoo' );
	$milanoo_result = mysql_query ( "set names 'utf8'" );
	return $milanoo_db;
}

$web_statis_link = db_connect ();

$ora_connstr = "(description=(address=(protocol=tcp)
(host=" . $ora_host . ")(port=" . $ora_port . "))
(connect_data=(service_name=" . $ora_sid . ")))";
$conn = oci_connect ( $ora_username, $ora_password, $ora_connstr );
if (! $conn) {
	$e = oci_error ();
	print htmlentities ( $e ['message'] );
}

$stime = microtime ( true ); // 获取程序开始执行的时间
if (isset ( $argv [1] ) and isset ( $argv [2] )) {
	/* 导入开始日期 2013/8/21 2013/8/22 */
	$s_time = strtotime ( $argv [1] );
	$e_time = strtotime ( $argv [2] );
} else {
	$s_time = strtotime ( date ( "Y/m/d", strtotime ( "yesterday" ) ) );
	$e_time = strtotime ( date ( "Y/m/d", strtotime ( "yesterday" ) ) );
}

$acceptLangCookie = array (
		'en-uk' => 'EN',
		'ja-jp' => 'JP',
		'fr-fr' => 'FR',
		'es-sp' => 'ES',
		'de-ge' => 'DE',
		'it-it' => 'IT',
		'ru-ru' => 'RU',
		'pt-pt' => 'PT',
		'all' => 'all' 
);

$brower_type = array (
		'%msie%' => 'IE',
		'%firefox%' => 'Firefox',
		'%chrome%' => 'Chrome',
		'%opera%' => 'Opera',
		'%version%safari%' => 'Safari' 
);

for($d = $s_time; $d <= $e_time; $d = $d + 86400) {
	$day_array [] = $d;
}

for($h = $s_time; $h <= $e_time + 86399; $h = $h + 3600) {
	$hour_array [] = $h;
}

// foreach ( $hour_array as $time_value ) {
// var_dump( date ( 'Y-m-d H:i:s', $time_value + 3599 ));
// echo "\n";
// }

for($h = $s_time; $h <= $e_time + 86399; $h = $h + 60) {
	$minutes_array [] = $h;
}

// 计算referer相关信息
function getRefererHost($refererInfo) {
	if (strlen ( $refererInfo ) < 1)
		return "direct";
	$hostInfo = parse_url ( $refererInfo );
	if (isset ( $hostInfo ['host'] )) {
		// if(substr($hostInfo['host'],0,11)=='www.google.
		return $hostInfo ['host'];
	}
	return "direct";
}
// parse_str ( $baseData[5], $pageData );
// $refererInfo = $pageData['ref'];
// var_dump(getRefererHost());
// exit;

foreach ( $day_array as $time_value ) {
	$s_timerang = date ( 'Y-m-d 00:00:00', $time_value );
	$e_timerang = date ( 'Y-m-d 23:59:59', $time_value );
	
	// 时间戳
	$s_timestamp = strtotime ( $s_timerang );
	$e_timestamp = strtotime ( $e_timerang );
	
	$time_rang = " gmt_datetime>=to_date('" . $s_timerang . "','yyyy-mm-dd hh24:mi:ss') and gmt_datetime<=to_date('" . $e_timerang . "','yyyy-mm-dd hh24:mi:ss')";
	
	// 统计外链访问数据补全
	echo "\n";
	echo '----------------------------------------------------------------- @' . $s_timerang . '-- promotion ---------------------------------------------';
	echo "\n";
	
	$query = "select upper(promotionurl) as promotionurl,site_lang,WEBSITEID,device_type,COUNT(ip),COUNT(DISTINCT ip),COUNT(DISTINCT session_id) from web_log_result where ";
	$query .= $time_rang . " and promotionurl is not null and promotionurl!='-' GROUP BY upper(promotionurl),site_lang ,WEBSITEID,device_type";

	$oracle_result = oci_parse ( $conn, $query );
	if (! $oracle_result) {
		$e = oci_error ( $conn );
		print htmlentities ( $e ['message'] );
		exit ();
	}
	oci_execute ( $oracle_result );
	$milanoo_link = milanoo_connect ();
	while ( $row = oci_fetch_array ( $oracle_result, OCI_ASSOC + OCI_RETURN_NULLS ) ) {
		if ($row ['SITE_LANG'] == '') {
			$row ['SITE_LANG'] = 'all';
		}
		
		$WebsiteId = $row ["WEBSITEID"];
		
		if ($row ["WEBSITEID"] == NULL) {
			$WebsiteId = 1;
			$row ["WEBSITEID"] = 1;
		}
		
		$lang = trim ( $row ['SITE_LANG'] );
		$lang = $acceptLangCookie [$lang];
		if ($lang == NULL) {
			continue;
		}
		$sql = "select id,PromotionName from milanoo_promotionurl where PromotionName='" . $row ["PROMOTIONURL"] . "'";
		$p_query = mysql_query ( $sql, $web_statis_link );
		$p_result = mysql_fetch_row ( $p_query );
		$promotionid = $p_result [0];
		
		if (! $p_result) {
			$query = "INSERT INTO milanoo_promotionurl ( PromotionName ) VALUES ( '" . $row ["PROMOTIONURL"] . "' )";
			$mysql_insert_result = mysql_query ( $query, $web_statis_link );
			$promotionid = mysql_insert_id ();
		}
		
		$milanoo_lang = array_search ( $lang, $acceptLangCookie );
			
		// 如果是 WAP 站的 $row["WEBSITEID"] = 101 米兰表 device_type = 2
		if ($row ["WEBSITEID"] == '101') {
			$where = "device_type = 2 AND ";
			$WebsiteId = 1;
		} else {
			$where = "device_type = 1 AND ";
		}
		
		//获得某个 PROMOTIONURL 当时的注册用户数
		$sql = "SELECT COUNT(MemberId) AS regmember FROM `milanoo_member` WHERE " . $where . "`PromotionURL` = '" . $row ["PROMOTIONURL"] . "' AND WebsiteId = '".$WebsiteId."' AND MemberLang = '" . $milanoo_lang . "' AND MemberUserTime > '" . $s_timestamp . "' AND MemberUserTime < '" . $e_timestamp . "'";
		$milanoo_query = mysql_query( $sql, $milanoo_link );
		
		if ($milanoo_query){
			$regmember_row = mysql_fetch_row($milanoo_query);
		}
		$regmember = $regmember_row[0];
		
		//获得某个 PROMOTIONURL 当时的订阅用户数
		$sql = "SELECT COUNT(id) AS subscribers FROM `milanoo_mail_del` WHERE `PromotionURL` = '" . $row ["PROMOTIONURL"] . "' AND WebsiteId = '".$WebsiteId."' AND lang = '" . $milanoo_lang . "' AND ADDTIME > '" . $s_timestamp . "' AND ADDTIME < '" . $e_timestamp . "'";
		$milanoo_query = mysql_query( $sql, $milanoo_link );
		if ($milanoo_query){
			$subscribers_row = mysql_fetch_row($milanoo_query);
		}
		$subscribers = $subscribers_row[0];

		//获得某个 PROMOTIONURL 当时支付订单数
		$sql = "SELECT COUNT(OrdersId) AS OrdersNUM FROM `milanoo_orders` s WHERE " . $where . "Promotion = '" . $row ["PROMOTIONURL"] . "' AND WebsiteId = '".$WebsiteId."' AND `OrdersEstate` != 'RefuseOrders' AND `OrdersAddTime` > '" . $s_timestamp . "' AND `OrdersPay` > '0' AND `OrdersAddTime` < '" . $e_timestamp . "' AND lang = '" . $milanoo_lang . "'";
		$milanoo_query = mysql_query( $sql, $milanoo_link );
		if ($milanoo_query){
			$OrdersNUM_row = mysql_fetch_row($milanoo_query);
		}
		$OrdersNUM = $OrdersNUM_row[0];	

		//获得某个 PROMOTIONURL 当时未支付订单数
		$sql = "SELECT COUNT(OrdersId) AS OrdersNUM FROM `milanoo_orders` s WHERE " . $where . "Promotion = '" . $row ["PROMOTIONURL"] . "' AND WebsiteId = '".$WebsiteId."' AND `OrdersEstate` != 'RefuseOrders' AND `OrdersAddTime` > '" . $s_timestamp . "' AND `OrdersPay` = '0' AND `OrdersAddTime` < '" . $e_timestamp . "' AND lang = '" . $milanoo_lang . "'";
		$milanoo_query = mysql_query( $sql, $milanoo_link );
		if ($milanoo_query){
			$Orders_Unpay_NUM_row = mysql_fetch_row($milanoo_query);
		}
		$Orders_Unpay_NUM = $Orders_Unpay_NUM_row[0];		
		
		//获得某个 PROMOTIONURL 当时的订单总金额
		$sql = "SELECT SUM(ROUND(OrdersAmount * IFNULL(SUBSTRING_INDEX(exchange_rate,',',-1),1) / IFNULL(SUBSTRING_INDEX(exchange_rate,',',1),1),2)) AS OrdersAmount FROM `milanoo_orders` s WHERE " . $where . "Promotion = '" . $row ["PROMOTIONURL"] . "' AND WebsiteId = '".$WebsiteId."' AND `OrdersEstate` != 'RefuseOrders' AND `OrdersAddTime` > '" . $s_timestamp . "' AND `OrdersPay` > '0' AND `OrdersAddTime` < '" . $e_timestamp . "' AND lang = '" . $milanoo_lang . "'";
		$milanoo_query = mysql_query( $sql, $milanoo_link );
		if ($milanoo_query){
			$OrdersAmount_row = mysql_fetch_row($milanoo_query);
		}
		$OrdersAmount = $OrdersAmount_row[0];
		
		$query = "UPDATE ma_promotion_visits set payamount = '" . $OrdersAmount . "',unpayorder = '" . $Orders_Unpay_NUM . "',payorder = '" . $OrdersNUM . "', regmember = '" . $regmember . "', subscribers = '" . $subscribers . "' where promotionid = '" . $promotionid . "' AND lang ='" . $lang . "' and time ='" . $s_timerang . " ' and websiteid='" . $row ['WEBSITEID'] . "'";
		mysql_query ( $query, $web_statis_link );
		
		unset ( $regmember );
		unset ( $regmember_row );
		unset ( $subscribers );
		unset ( $subscribers_row );
		unset ( $milanoo_lang );
		unset ( $where );
	}
	mysql_close ( $milanoo_link );
	mysql_free_result ( $mysql_result );
	
	echo "\n";
	echo '-----------------------------------------------------------------Now memory_get_usage: ' . memory_get_usage () . "\n";
	echo '-----------------------------------------------------------------referer NewUV while end-------------------------';
	echo "\n";
}