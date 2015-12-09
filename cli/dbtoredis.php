<?php
error_reporting ( E_ALL ^ E_NOTICE );
ini_set ( 'display_errors', 'On' );
date_default_timezone_set ( 'Asia/Chongqing' );
ini_set ( 'memory_limit', '1024M' );

/* 配置数据库 */

/* ora 的 */
$ora_host = "192.168.11.26";
$ora_port = "1521";
$ora_sid = "orcl";
$ora_username = "bi";
$ora_password = "bi";
$charset = "UTF8"; ### zhs16gbk ###

/* milanoo 主站的 */
define("mysql_host","192.168.3.70");
define("mysql_port","3306");
define("mysql_username","milanoo");
define("mysql_password","milanoodb");

/* web_statis 的 */
//ini_set ( 'mysql.default_port', '5029' );
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
	$update_s_time = strtotime ( "-1 week",strtotime ( $argv [1] ) );
} else {
	$s_time = strtotime ( date ( "Y/m/d", strtotime ( "yesterday" ) ) );
	$e_time = strtotime ( date ( "Y/m/d", strtotime ( "yesterday" ) ) );
	$update_s_time = strtotime ( "-1 week",strtotime ( "yesterday" ) );
}

$acceptLangCookie = array ('en-uk' => 'EN', 'ja-jp' => 'JP', 'fr-fr' => 'FR', 'es-sp' => 'ES', 'de-ge' => 'DE', 'it-it' => 'IT', 'ru-ru' => 'RU', 'pt-pt' => 'PT', 'all' => 'all' );

$brower_type = array ('%msie%' => 'IE', '%firefox%' => 'Firefox', '%chrome%' => 'Chrome', '%opera%' => 'Opera', '%version%safari%' => 'Safari' );

for($d = $s_time; $d <= $e_time; $d = $d + 86400) {
	$day_array [] = $d;
}

for($update_d = $update_s_time; $update_d <= $s_time; $update_d = $update_d + 86400) {
	$update_day_array [] = $update_d;
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
// var_dump($update_day_array);exit;

foreach ( $day_array as $time_value ) {
	$s_timerang = date ( 'Y-m-d 00:00:00', $time_value );
	$e_timerang = date ( 'Y-m-d 23:59:59', $time_value );
	
	// 时间戳
	$s_timestamp = strtotime($s_timerang);
	$e_timestamp = strtotime($e_timerang);
	
	$time_rang = " gmt_datetime>=to_date('" . $s_timerang . "','yyyy-mm-dd hh24:mi:ss') and gmt_datetime<=to_date('" . $e_timerang . "','yyyy-mm-dd hh24:mi:ss')";
	$query = "DELETE FROM `day` WHERE `time` = '".$s_timerang."';";
	mysql_query ( $query ,$web_statis_link);
	$query = "DELETE FROM `ma_promotion_visits` WHERE `time` = '".$s_timerang."';";
	mysql_query ( $query ,$web_statis_link);
	//$query = "DELETE FROM `ma_page_visits` WHERE `time` = '".$s_timerang."';";
	//mysql_query ( $query ,$web_statis_link);
    
	// 每日的数据
	// 这里开始屏蔽
	echo "\n";
	echo '----------------------------------------------------------------- ' . $code . '@' . $s_timerang . '--------------------------------------------------------';
	echo "\n";
	
	// PV IP UV
	$query = "select site_lang,WEBSITEID,COUNT(ip),COUNT(DISTINCT ip),COUNT(DISTINCT session_id) from web_log_result where ";
	$query .= $time_rang . " GROUP BY site_lang,WEBSITEID";
	$oracle_result = oci_parse ( $conn, $query );
	oci_execute ( $oracle_result );
	while ( $row = oci_fetch_array ( $oracle_result, OCI_ASSOC + OCI_RETURN_NULLS ) ) {
		if ($row ['SITE_LANG'] == '') {
			$row ['SITE_LANG'] = 'all';
		}
		$lang = trim ( $row ['SITE_LANG'] );
		$lang = $acceptLangCookie [$lang];
		if ($lang == NULL) {
			continue;
		}
		$query = "INSERT INTO day ( id , pv , ip , uv ,lang , time ,websiteid)  VALUES ( NULL, '" . $row ['COUNT(IP)'] . "', '" . $row ['COUNT(DISTINCTIP)'] . "', '" . $row ['COUNT(DISTINCTSESSION_ID)'] . "', '" . $lang . "', '" . $s_timerang . "' , '" . $row ['WEBSITEID'] . "')";
		$mysql_insert_result = mysql_query ( $query ,$web_statis_link);
	}
	oci_free_statement($oracle_result);
	
	$query = "select site_lang,WEBSITEID,COUNT(DISTINCT session_id) from web_log_result where ";
	$query .= $time_rang . " and is_newuser = '1' GROUP BY site_lang,WEBSITEID";
	$oracle_result = oci_parse ( $conn, $query );
	oci_execute ( $oracle_result );
	while ( $row = oci_fetch_array ( $oracle_result, OCI_ASSOC + OCI_RETURN_NULLS ) ) {
		if ($row ['SITE_LANG'] == '') {
			$row ['SITE_LANG'] = 'all';
		}
		$lang = trim ( $row ['SITE_LANG'] );
		$lang = $acceptLangCookie [$lang];
		if ($lang == NULL) {
			continue;
		}
		$query = "UPDATE day set newUv = '" . $row ['COUNT(DISTINCTSESSION_ID)'] . "' where lang ='" . $lang . "' and time ='" . $s_timerang . "' and websiteid='" . $row ['WEBSITEID'] . "'";
		$mysql_insert_result = mysql_query ( $query ,$web_statis_link);
	}
	oci_free_statement($oracle_result);
	
	//统计每天访问深度和平均停留时间
	echo "\n";
	echo '----------------------------------------------------------------- @' . $s_timerang . '-- order products---------------------------------------';
	echo "\n";
	$website_array = get_website ();
	//print_r($website_array);exit;
	foreach ( $website_array as $v ) {
		foreach ( $acceptLangCookie as $key => $value ) {
			$query = "select avg(COUNT(ip)) as visitdepth,AVG (max(GMT_DATETIME) - min(GMT_DATETIME)) * 24*3600 as avgtime from web_log_result where ";
			$query .= $time_rang . " and site_lang='$key' and WEBSITEID='$v' GROUP BY session_id";
			$oracle_result = oci_parse ( $conn, $query );
			oci_execute ( $oracle_result );
			while ( $row = oci_fetch_array ( $oracle_result, OCI_ASSOC + OCI_RETURN_NULLS ) ) {
				$query = "UPDATE day set visitdepth = '" . $row ['VISITDEPTH'] . "',visittime='" . $row ["AVGTIME"] . "' where lang ='" . $value . "' and time ='" . $s_timerang . "' and websiteid='$v'";
				$mysql_insert_result = mysql_query ( $query,$web_statis_link);
			}
		}
	}
	oci_free_statement($oracle_result);
	
	//统计每天订单数及注册数和订单
	echo "\n";
	echo '----------------------------------------------------------------- @' . $s_timerang . '-- order products ---------------------------------------';
	echo "\n";
	
	$order_array = get_order ( $time_value, $acceptLangCookie );
	db_connect ();
	
	foreach ( $order_array as $key => $value ) {
		foreach ( $value as $k => $v ) {
			$sql = "update day set paynum='" . $v ['pay'] . "',notpaynum='" . $v ['notpay'] . "',member='" . $v ['member'] . "',payproduct='" . $v ['payproduct'] . "',paypostage='" . $v ['paypostage'] . "' where lang='$k' and time='$s_timerang' and websiteid='$key'";
			mysql_query ( $sql,$web_statis_link );
		}
	}
	
	//统计外链访问
	echo "\n";
	echo '----------------------------------------------------------------- @' . $s_timerang . '-- promotion ---------------------------------------------';
	echo "\n";
	
	$query = "select upper(promotionurl) as promotionurl,site_lang,WEBSITEID,COUNT(ip),COUNT(DISTINCT ip),COUNT(DISTINCT session_id) from web_log_result where ";
	$query .= $time_rang . " and promotionurl is not null and promotionurl!='-' GROUP BY upper(promotionurl),site_lang ,WEBSITEID";
	$oracle_result = oci_parse ( $conn, $query );
	
	if (! $oracle_result) {
		$e = oci_error ( $conn );
		print htmlentities ( $e ['message'] );
		exit ();
	}
	oci_execute ( $oracle_result );
	$milanoo_link = milanoo_connect();
	while ( $row = oci_fetch_array ( $oracle_result, OCI_ASSOC + OCI_RETURN_NULLS ) ) {
		if ($row ['SITE_LANG'] == '') {
			$row ['SITE_LANG'] = 'all';
		}
		
		$WebsiteId = $row ["WEBSITEID"];
		
		$lang = trim ( $row ['SITE_LANG'] );
		$lang = $acceptLangCookie [$lang];
		if ($lang == NULL) {
			continue;
		}
		$sql = "select id,PromotionName from milanoo_promotionurl where PromotionName='" . $row ["PROMOTIONURL"] . "'";
		$p_query = mysql_query ( $sql, $web_statis_link );
		$p_result = mysql_fetch_row ( $p_query );
		$promotionid = $p_result [0];
		
		if (! $p_result ) {
			$query = "INSERT INTO milanoo_promotionurl ( PromotionName ) VALUES ( '" . $row ["PROMOTIONURL"] . "' )";
			$mysql_insert_result = mysql_query ( $query, $web_statis_link );
			$promotionid = mysql_insert_id ($web_statis_link);
		}
		
		$milanoo_lang = array_search ( $lang, $acceptLangCookie );
			
		// 如果是 WAP 站的 $row["WEBSITEID"] = 101 米兰表 device_type = 2
		if ($row ["WEBSITEID"] == '101') {
			$where = "device_type = 2 AND ";
			$WebsiteId = 1;
		} elseif ($row ["WEBSITEID"] == '201') {
			$where = "device_type = 5 AND ";
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
		$sql = "SELECT SUM(ROUND((OrdersAmount + OrdersLogisticsCosts + insurance) * IFNULL(SUBSTRING_INDEX(exchange_rate,',',-1),1) / IFNULL(SUBSTRING_INDEX(exchange_rate,',',1),1),2)) AS OrdersAmount FROM `milanoo_orders` s WHERE " . $where . "Promotion = '" . $row ["PROMOTIONURL"] . "' AND WebsiteId = '".$WebsiteId."' AND `OrdersEstate` != 'RefuseOrders' AND `OrdersAddTime` > '" . $s_timestamp . "' AND `OrdersPay` > '0' AND `OrdersAddTime` < '" . $e_timestamp . "' AND lang = '" . $milanoo_lang . "'";
		$milanoo_query = mysql_query( $sql, $milanoo_link );
		if ($milanoo_query){
			$OrdersAmount_row = mysql_fetch_row($milanoo_query);
		}
		$OrdersAmount = $OrdersAmount_row[0];
		
		$query = "INSERT INTO ma_promotion_visits ( id , pv , ip , uv , promotionid,lang , time,websiteid,payorder,payamount,regmember,subscribers,unpayorder)  VALUES ( NULL, '" . $row ['COUNT(IP)'] . "', '" . $row ['COUNT(DISTINCTIP)'] . "', '" . $row ['COUNT(DISTINCTSESSION_ID)'] . "', '" . $promotionid . "','" . $lang . "', '" . $s_timerang . "','".$row["WEBSITEID"]."','".$OrdersNUM."','".$OrdersAmount."','".$regmember."','".$subscribers."','".$Orders_Unpay_NUM."')";
		$mysql_insert_result = mysql_query ( $query,$web_statis_link );
		
		unset($regmember);
		unset($regmember_row);
		unset($subscribers);
		unset($subscribers_row);
		unset($milanoo_lang);
		unset($where);
		unset($promotionid);
		mysql_free_result ( $milanoo_query );
		mysql_free_result ( $p_query );
	}
	oci_free_statement($oracle_result);
	
	$query = "select promotionurl,site_lang,WEBSITEID,COUNT(DISTINCT session_id) from web_log_result where ";
	$query .= $time_rang . " and promotionurl is not null and promotionurl!='-' and is_newuser = '1' GROUP BY promotionurl,site_lang,WEBSITEID";
	
	$oracle_result = oci_parse ( $conn, $query );
	oci_execute ( $oracle_result );
	while ( $row = oci_fetch_array ( $oracle_result, OCI_ASSOC + OCI_RETURN_NULLS ) ) {
		if ($row ['SITE_LANG'] == '') {
			$row ['SITE_LANG'] = 'all';
		}
		$lang = trim ( $row ['SITE_LANG'] );
		$lang = $acceptLangCookie [$lang];
		if ($lang == NULL) {
			continue;
		}
		$query = "UPDATE ma_promotion_visits set newUv = '" . $row ['COUNT(DISTINCTSESSION_ID)'] . "' where lang ='" . $lang . "' and time ='" . $s_timerang . " ' and websiteid='" . $row ['WEBSITEID'] . "'";
		$mysql_insert_result = mysql_query ( $query,$web_statis_link );
	}
	oci_free_statement($oracle_result);

	echo "\n";
	echo '-----------------------------------------------------------------Now memory_get_usage: ' . round((memory_get_usage()/(1024)/(1024)),3) . " Mb \n";
	echo '-----------------------------------------------------------------referer NewUV while end-------------------------';
	echo "\n";

}

//更新一个星期内的数据
foreach ( $update_day_array as $time_value ) {
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
	
	$query = "select upper(promotionurl) as promotionurl,site_lang,WEBSITEID,COUNT(ip),COUNT(DISTINCT ip),COUNT(DISTINCT session_id) from web_log_result where ";
	$query .= $time_rang . " and promotionurl is not null and promotionurl!='-' GROUP BY upper(promotionurl),site_lang ,WEBSITEID";
	$oracle_result = oci_parse ( $conn, $query );
	if (! $oracle_result) {
		$e = oci_error ( $conn );
		print htmlentities ( $e ['message'] );
		exit ();
	}
	oci_execute ( $oracle_result );
	//$milanoo_link = milanoo_connect ();
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
		mysql_free_result ( $p_query );

		if (! $p_result ) {
			$query = "INSERT INTO milanoo_promotionurl ( PromotionName ) VALUES ( '" . $row ["PROMOTIONURL"] . "' )";
			$mysql_insert_result = mysql_query ( $query, $web_statis_link );
			$promotionid = mysql_insert_id ($web_statis_link);
		}
		
		echo "\n";
		echo '-----------------------------------------------------------------promotion id: ' . $promotionid . "\n";
		echo "\n";
		
		$milanoo_lang = array_search ( $lang, $acceptLangCookie );
			
		// 如果是 WAP 站的 $row["WEBSITEID"] = 101 米兰表 device_type = 2
		if ($row ["WEBSITEID"] == '101') {
			$where = "device_type = 2 AND ";
			$WebsiteId = 1;
		} elseif ($row ["WEBSITEID"] == '201') {
			$where = "device_type = 5 AND ";
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
		
		echo "\n";
		echo '-----------------------------------------------------------------UPDATE ma_promotion_visits promotion id: ' . $promotionid . "\n";
		echo "\n";

		$query = "UPDATE ma_promotion_visits set payamount = '" . $OrdersAmount . "',unpayorder = '" . $Orders_Unpay_NUM . "',payorder = '" . $OrdersNUM . "', regmember = '" . $regmember . "', subscribers = '" . $subscribers . "' where promotionid = '" . $promotionid . "' AND lang ='" . $lang . "' and time ='" . $s_timerang . " ' and websiteid='" . $row ['WEBSITEID'] . "'";
		mysql_query ( $query, $web_statis_link );
		
		unset ( $regmember );
		unset ( $regmember_row );
		unset ( $subscribers );
		unset ( $subscribers_row );
		unset ( $milanoo_lang );
		unset ( $where );
		
		mysql_free_result ( $milanoo_query );
	}
	oci_free_statement($oracle_result);

	echo "\n";
	echo '-----------------------------------------------------------------Now memory_get_usage: ' . round((memory_get_usage()/(1024)/(1024)),3) . " Mb \n";
	echo '-----------------------------------------------------------------referer week date end-------------------------';
	echo "\n";
}


function get_order($time_value, $acceptLangCookie) {
	$end_date = $time_value + 86400;
	$dbbase = mysql_connect ( "192.168.3.70", "milanoo", "milanoodb", true ) or die ( "Cann't open database!!! case:" . mysql_error () );
	mysql_select_db ( 'milanoo' );
	$mysql_result = mysql_query ( "set names 'utf8'" );
	
	$sql = "select count(OrdersId) as OrderNum,lang,WebsiteId,device_type from milanoo_orders where OrdersAddTime>='$time_value' AND OrdersAddTime<'$end_date' and OrdersPay>0  group by lang,WebsiteId,device_type";
	$mysql_result = mysql_query ( $sql ) or die ( mysql_error () );
	$order_pay = array ();
	while ( $row = mysql_fetch_assoc ( $mysql_result ) ) {
		if ($row ['lang'] == '') {
			$row ['lang'] = 'all';
		}
		$lang = trim ( $row ['lang'] );
		$lang = $acceptLangCookie [$lang];
		if ($lang == NULL) {
			continue;
		}
		$order_pay ["pay"] [$row ["device_type"]] [$row ["WebsiteId"]] [$lang] = $row ["OrderNum"];
	}
	
	$sql = "select count(OrdersId) as OrderNum,lang,WebsiteId,device_type from milanoo_orders where OrdersAddTime>='$time_value' and OrdersAddTime<'$end_date' and OrdersEstate!='RefuseOrders' group by lang,WebsiteId,device_type";
	$mysql_result = mysql_query ( $sql ) or die ( mysql_error () );
	//$order_pay = array ();
	while ( $row = mysql_fetch_assoc ( $mysql_result ) ) {
		if ($row ['lang'] == '') {
			$row ['lang'] = 'all';
		}
		$lang = trim ( $row ['lang'] );
		$lang = $acceptLangCookie [$lang];
		if ($lang == NULL) {
			continue;
		}
		$order_pay ["notpay"] [$row ["device_type"]] [$row ["WebsiteId"]] [$lang] = $row ["OrderNum"];
	}
	
	$sql = "select count(MemberId) as num,MemberLang,WebsiteId,device_type from `milanoo_member` where MemberUserTime>='$time_value' and MemberUserTime<'$end_date' and MemberLang!='' group by MemberLang,WebsiteId,device_type";
	$mysql_result = mysql_query ( $sql ) or die ( mysql_error () );
	//$order_pay = array ();
	while ( $row = mysql_fetch_assoc ( $mysql_result ) ) {
		if ($row ['MemberLang'] == '') {
			$row ['MemberLang'] = 'all';
		}
		$lang = trim ( $row ['MemberLang'] );
		$lang = $acceptLangCookie [$lang];
		if ($lang == NULL) {
			continue;
		}
		$order_pay ["member"] [$row ["device_type"]] [$row ["WebsiteId"]] [$lang] = $row ["num"];
	}
	
	$sql = "select a.`WebsiteId`,a.device_type,a.lang,ROUND(sum(a.`OrdersAmount` * c.ex_rate / d.`ex_rate`),2) as pm,ROUND(sum(a.`OrdersLogisticsCosts` * c.ex_rate / d.`ex_rate`),2) as fm FROM `milanoo_orders` a,`t_exchange_rate` c,`t_exchange_rate` d WHERE a.`CurrencyCode` = c.`currency` AND d.`currency` = 'USD' AND a.ordersaddtime >='$time_value' AND a.ordersaddtime <'$end_date' AND a.orderspay > 0 group by a.lang,a.`WebsiteId`,a.device_type";
	$mysql_result = mysql_query ( $sql ) or die ( mysql_error () );
	//$order_pay = array ();
	while ( $row = mysql_fetch_assoc ( $mysql_result ) ) {
		if ($row ['lang'] == '') {
			$row ['lang'] = 'all';
		}
		$lang = trim ( $row ['lang'] );
		$lang = $acceptLangCookie [$lang];
		if ($lang == NULL) {
			continue;
		}
		$order_pay ["payproduct"] [$row ["device_type"]] [$row ["WebsiteId"]] [$lang] = $row ["pm"];
		$order_pay ["paypostage"] [$row ["device_type"]] [$row ["WebsiteId"]] [$lang] = $row ["fm"];
	}
	
	$order_array = array ();
	//var_dump($order_pay);exit;
	foreach ( $order_pay as $key => $value ) {
		foreach ( $value as $k => $v ) {
			if ($k == 2) {
				foreach ( $v as $kk => $vv ) {
					foreach ( $vv as $kkk => $vvv ) {
						$order_array [101] [$kkk] [$key] = $vvv;
					}
				}
			} 
			elseif ($k==1) {
				foreach ( $v as $kk => $vv ) {
					foreach ( $vv as $kkk => $vvv ) {
						$order_array [$kk] [$kkk] [$key] = $vvv;
					}
				}
			}
		}
	}
	mysql_close ();
	return $order_array;
}

function get_website() {
	$dbbase = mysql_connect ( "192.168.3.70", "milanoo", "milanoodb", true ) or die ( "Cann't open database!!! case:" . mysql_error () );
	mysql_select_db ( 'milanoo_gaea' );
	$mysql_result = mysql_query ( "set names 'utf8'" );
	
	$sql = "select * from web_site";
	$mysql_result = mysql_query ( $sql ) or die ( mysql_error () );
	$order_pay = array ();
	while ( $row = mysql_fetch_assoc ( $mysql_result ) ) {
		$website_array [] = $row ["websiteid"];
	}
	$website_array[101] = 101;
	$website_array[201] = 201;
	mysql_close ();
	return $website_array;
}

$etime = microtime ( true ); // 获取程序执行结束的时间
$total = $etime - $stime; // 计算差值
echo "\n" . $total . "times";