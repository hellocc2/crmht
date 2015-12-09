<?php
error_reporting ( E_ALL ^ E_NOTICE );
ini_set ( 'display_errors', 'On' );
date_default_timezone_set ( 'Asia/Chongqing' );
ini_set ( 'memory_limit', '800M' );

function db_connect() {
	$db = mysql_connect ( "192.168.11.23", "bi", "bi", true ) or die ( "Cann't open database!!! case:" . mysql_error () );
	mysql_select_db ( 'web_statis' );
	$mysql_result = mysql_query ( "set names 'utf8'" );
}

/* 配置数据库 */
//ini_set ( 'mysql.default_port', '5029' );
db_connect ();

$ora_host = "192.168.11.26";
$ora_port = "1521";
$ora_sid = "orcl";
$ora_username = "bi";
$ora_password = "bi";
$charset = "UTF8"; ### zhs16gbk ###


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
	/* 导入开始日期 2012/1/03 2012/1/10 */
	$s_time = strtotime ( $argv [1] );
	$e_time = strtotime ( $argv [2] );
} else {
	$s_time = strtotime ( date ( "Y/m/d", strtotime ( "yesterday" ) ) );
	$e_time = strtotime ( date ( "Y/m/d", strtotime ( "yesterday" ) ) );
}

$acceptLangCookie = array ('en-uk' => 'EN', 'ja-jp' => 'JP', 'fr-fr' => 'FR', 'es-sp' => 'ES', 'de-ge' => 'DE', 'it-it' => 'IT', 'ru-ru' => 'RU', 'pt-pt' => 'PT', 'all' => 'all' );

for($d = $s_time; $d <= $e_time; $d = $d + 86400) {
	$day_array [] = $d;
}

foreach ( $day_array as $time_value ) {
	
	$s_timerang = date ( 'Y-m-d 00:00:00', $time_value );
	$e_timerang = date ( 'Y-m-d 23:59:59', $time_value );
	
	$time_rang = " gmt_datetime>=to_date('" . $s_timerang . "','yyyy-mm-dd hh24:mi:ss') and gmt_datetime<=to_date('" . $e_timerang . "','yyyy-mm-dd hh24:mi:ss')";
	
	//统计页面访问
	echo "\n";
	echo '-----------------------------------------------------------------@' . $s_timerang . '-----------------pageview---------------------------------------';
	echo "\n";
	
	$query = "select START_PAGE,WEBSITEID,site_lang,COUNT(ip),COUNT(DISTINCT ip),COUNT(DISTINCT session_id) from web_log_result where ";
	$query .= $time_rang . " GROUP BY START_PAGE,site_lang,WEBSITEID";
	$oracle_result = oci_parse ( $conn, $query );
	if (! $oracle_result) {
		$e = oci_error ( $conn );
		print htmlentities ( $e ['message'] );
		exit ();
	}
	$pattern = "/(.*?)p([0-9]+)\.html(.*)/";
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
		if ($row ["START_PAGE"]) {
			$sql = "select id,pagename from `ma_page_name` where `pagename`='" . $row ["START_PAGE"] . "'";
			$p_query = mysql_query ( $sql );
			$p_result = mysql_fetch_row ( $p_query );
			$insert_id = $p_result [0];
			if (! $p_result) {
				$query = "INSERT INTO `ma_page_name` ( `pagename`,pagetype,pagelever )  VALUES ( '" . $row ["START_PAGE"] . " ','','' )";
				$mysql_insert_result = mysql_query ( $query );
				$insert_id = mysql_insert_id ();
			}
			$query = "INSERT INTO `ma_page_visits` ( `id` , `pv` , `ip` , `uv` , `pageid`,`lang` , `time` , `websiteid` )  VALUES ( NULL, '" . $row ['COUNT(IP)'] . "', '" . $row ['COUNT(DISTINCTIP)'] . "', '" . $row ['COUNT(DISTINCTSESSION_ID)'] . "', '" . $insert_id . "','" . $lang . "', '" . $s_timerang . "', '" . $row["WEBSITEID"] . "' )";
			$mysql_insert_result = mysql_query ( $query );
		}
		
	/*$sql_orc = "select START_PAGE,VIEW_PAGE,VIEW_PATH,url from web_log_result where " . $time_rang . " and START_PAGE='" . $row ["START_PAGE"] . "' and rownum =1";
		$oracle_row = oci_parse ( $conn, $sql_orc );
		oci_execute ( $oracle_row );
		$rs = oci_fetch_assoc ( $oracle_row );
		if ($rs) {
			preg_match ( $pattern, $rs["URL"], $product );
			$sql_o = "update ma_page_name set pagetype='" . $rs ["VIEW_PAGE"] . "',pagelever='" . $rs ["VIEW_PATH"] . "',pagepid='".$product[2]."' where id='$insert_id'";
			mysql_query ( $sql_o );
		}*/
	
	}
	mysql_free_result ( $mysql_result );
	
	//统计页面访问
	echo "\n";
	echo '-----------------------------------------------------------------@' . $s_timerang . '-----------------page other---------------------------------------';
	echo "\n";
	
	$sql_orc = "select START_PAGE,VIEW_PAGE,VIEW_PATH,url from web_log_result where " . $time_rang . " group by START_PAGE,VIEW_PAGE,VIEW_PATH,url";
	$oracle_row = oci_parse ( $conn, $sql_orc );
	oci_execute ( $oracle_row );
	$pattern = "/(.*?)p([0-9]+)\.html(.*)/";
	while ( $rs = oci_fetch_array ( $oracle_row, OCI_ASSOC + OCI_RETURN_NULLS ) ) {
		if ($rs) {
			preg_match ( $pattern, $rs ["URL"], $product );
			$sql_o = "update ma_page_name set pagetype='" . $rs ["VIEW_PAGE"] . "',pagelever='" . $rs ["VIEW_PATH"] . "',pagepid='" . $product [2] . "' where pagename='" . $rs ["START_PAGE"] . "'";
			
			mysql_query ( $sql_o );
		}
	}
	mysql_free_result ( $mysql_result );
	
	//统计跳出率
	echo "\n";
	echo '-----------------------------------------------------------------@' . $s_timerang . '-----------------bounce rate---------------------------------------';
	echo "\n";
	
	$query = "SELECT start_page, A .SITE_LANG,WEBSITEID, COUNT (A .SESSION_ID) FROM web_log_result A, ( SELECT session_id FROM web_log_result WHERE " . $time_rang . " HAVING COUNT (session_id) = 1 GROUP BY session_id ) b WHERE A .SESSION_ID = b.SESSION_ID AND " . $time_rang . " GROUP BY START_PAGE, SITE_LANG,WEBSITEID";
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
		$sql = "update ma_page_visits v,ma_page_name n set pagebounce='" . $row ['COUNT(A.SESSION_ID)'] . "' where n.id=v.pageid and n.pagename='" . $row ["START_PAGE"] . "' and lang='$lang' and time='$s_timerang' and websiteid='" . $row ["WEBSITEID"] . "'";
		mysql_query ( $sql );
	}
	
	//统计页面访问平均时间
	echo "\n";
	echo '-----------------------------------------------------------------@' . $s_timerang . '-----------------visits time---------------------------------------';
	echo "\n";
	
	//$query = "SELECT A .start_page, A .SITE_LANG, AVG (b.GMT_DATETIME - A .GMT_DATETIME) * 24*3600 as avgtime FROM web_log_result A, web_log_result b WHERE A .SESSION_ID = b.SESSION_ID AND A .GMT_DATETIME < b.GMT_DATETIME AND A .url = b.REFERER AND A .gmt_datetime >= TO_DATE ( '" . $s_timerang . "', 'yyyy-mm-dd hh24:mi:ss' ) AND A .gmt_datetime <= TO_DATE ( '" . $e_timerang . "', 'yyyy-mm-dd hh24:mi:ss' ) AND b.gmt_datetime >= TO_DATE ( '" . $s_timerang . "', 'yyyy-mm-dd hh24:mi:ss' ) AND b.gmt_datetime <= TO_DATE ( '" . $e_timerang . "', 'yyyy-mm-dd hh24:mi:ss' ) ";
	//$query .= " AND ( SELECT SESSION_ID FROM web_log_result WHERE SESSION_ID = A .session_id AND GMT_DATETIME < b.GMT_DATETIME AND GMT_DATETIME > A .GMT_DATETIME AND ROWNUM = 1 ) IS NULL";
	//$query .= " GROUP BY A .start_page, A .SITE_LANG";
	$query = "select ccc.start_page,ccc.SITE_LANG,ccc.WEBSITEID,avg(cctime) from  (SELECT 
  aa.session_id,TO_CHAR(aa.gmt_datetime,'yyyy-mm-dd hh24:mi:ss') AS atime,bb.session_id,TO_CHAR(bb.gmt_datetime,'yyyy-mm-dd hh24:mi:ss') AS btime,cc.session_id,
  aa.start_page,aa.SITE_LANG,(bb.gmt_datetime - aa.gmt_datetime) * 24*3600  as cctime
FROM web_log_result aa JOIN (SELECT * FROM web_log_result a WHERE A .gmt_datetime >= TO_DATE ( '" . $s_timerang . "', 'yyyy-mm-dd hh24:mi:ss' )
  AND A .gmt_datetime   <= TO_DATE ( '" . $e_timerang . "', 'yyyy-mm-dd hh24:mi:ss' )
  ) bb ON aa.SESSION_ID    = bb.SESSION_ID AND aa.GMT_DATETIME < bb.GMT_DATETIME
AND aa.url          = bb.REFERER LEFT JOIN web_log_result cc ON cc.GMT_DATETIME     > bb.GMT_DATETIME AND cc.GMT_DATETIME    < aa.GMT_DATETIME
AND cc.SESSION_ID      = aa.session_id WHERE aa.gmt_datetime >= TO_DATE ( '" . $s_timerang . "', 'yyyy-mm-dd hh24:mi:ss')
AND aa.gmt_datetime   <= TO_DATE ( '" . $e_timerang . "', 'yyyy-mm-dd hh24:mi:ss') AND cc.session_id     IS NULL ) ccc GROUP BY ccc.start_page,ccc.SITE_LANG,ccc.WEBSITEID";
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
		$sql = "update ma_page_visits v,ma_page_name n set averagetime='" . $row ['AVGTIME'] . "' where n.id=v.pageid and n.pagename='" . $row ["START_PAGE"] . "' and lang='$lang' and time='$s_timerang' and websiteid='" . $row ["WEBSITEID"] . "'";
		mysql_query ( $sql );
	}
	
	//统计页面进入次数
	echo "\n";
	echo '-----------------------------------------------------------------@' . $s_timerang . '-----------------enter rate---------------------------------------';
	echo "\n";
	
	$query = "SELECT COUNT (DISTINCT A .SESSION_ID), START_PAGE, SITE_LANG,WEBSITEID FROM web_log_result A WHERE NOT EXISTS ( SELECT DISTINCT SESSION_ID FROM web_log_result WHERE A .GMT_DATETIME > GMT_DATETIME AND A .SESSION_ID = SESSION_ID AND " . $time_rang . ") AND A .gmt_datetime >= TO_DATE ( '" . $s_timerang . "', 'yyyy-mm-dd hh24:mi:ss' ) AND A .gmt_datetime <= TO_DATE ( '" . $e_timerang . "', 'yyyy-mm-dd hh24:mi:ss' ) ";
	$query .= " GROUP BY START_PAGE, SITE_LANG,WEBSITEID, ORDER BY COUNT (DISTINCT A .SESSION_ID) DESC";
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
		$sql = "update ma_page_visits v,ma_page_name n set pageenter='" . $row ['COUNT(DISTINCTA.SESSION_ID)'] . "' where n.id=v.pageid and n.pagename='" . $row ["START_PAGE"] . "' and lang='$lang' and time='$s_timerang' and websiteid='" . $row ["WEBSITEID"] . "'";
		mysql_query ( $sql );
	}
	
	//统计页面退出次数
	echo "\n";
	echo '-----------------------------------------------------------------@' . $s_timerang . '-----------------exit rate---------------------------------------';
	echo "\n";
	
	$query = "SELECT COUNT (DISTINCT A .SESSION_ID), START_PAGE, SITE_LANG,WEBSITEID FROM web_log_result A WHERE NOT EXISTS ( SELECT DISTINCT SESSION_ID FROM web_log_result WHERE A .GMT_DATETIME < GMT_DATETIME AND A .SESSION_ID = SESSION_ID AND " . $time_rang . ") AND A .gmt_datetime >= TO_DATE ( '" . $s_timerang . "', 'yyyy-mm-dd hh24:mi:ss' ) AND A .gmt_datetime <= TO_DATE ( '" . $e_timerang . "', 'yyyy-mm-dd hh24:mi:ss' ) ";
	$query .= " GROUP BY START_PAGE, SITE_LANG,WEBSITEID ORDER BY COUNT (DISTINCT A .SESSION_ID) DESC";
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
		$sql = "update ma_page_visits v,ma_page_name n set pageexit='" . $row ['COUNT(DISTINCTA.SESSION_ID)'] . "' where n.id=v.pageid and n.pagename='" . $row ["START_PAGE"] . "' and lang='$lang' and time='$s_timerang' and websiteid='" . $row ["WEBSITEID"] . "'";
		mysql_query ( $sql );
	}
	
	//统计商品数量和金额
	

	/*echo "\n";
	echo '-----------------------------------------------------------------@' . $s_timerang . '-----------------order products---------------------------------------';
	echo "\n";
	
	$order_array = get_order ( $time_value, $acceptLangCookie );
	db_connect ();
	foreach ( $order_array as $key => $value ) {
		foreach ( $value as $k => $v ) {
			$sql = "update ma_page_visits v,ma_page_name n set paynum='" . $v ['paynum'] . "',payamount='" . $v ['payamount'] . "',payorder='" . $v ['payorder'] . "' where n.id=v.pageid and n.pagepid='" . $key . "' and lang='$k' and time='$s_timerang'";
			mysql_query ( $sql );
		}
	}
	
	echo "\n";
	echo '-----------------------------------------------------------------Now memory_get_usage: ' . memory_get_usage () . "\n";
	echo '-----------------------------------------------------------------referer NewUV while end-------------------------';
	echo "\n";*/

}
function get_order($time_value, $acceptLangCookie) {
	$end_date = $time_value + 86400;
	$dbbase = mysql_connect ( "192.168.3.70", "milanoo", "milanoodb", true ) or die ( "Cann't open database!!! case:" . mysql_error () );
	mysql_select_db ( 'milanoo' );
	$mysql_result = mysql_query ( "set names 'utf8'" );
	$query = "select * from t_exchange_rate";
	$mysql_result = mysql_query ( $query ) or die ( mysql_error () );
	while ( $rs = mysql_fetch_assoc ( $mysql_result ) ) {
		$currency [$rs ["currency"]] = $rs ["ex_rate"];
	}
	
	$sql = "select count(distinct o.OrdersId) as payorder,o.CurrencyCode,o.lang,p.ProductsId,sum(p.ProductsNum) as paynum,sum(p.ProductsPrice*p.ProductsNum) as payamount  from `milanoo_orders` o,milanoo_orders_products p where o.OrdersId=p.OrdersId";
	$sql .= " and OrdersAddTime>='$time_value' and OrdersAddTime<'$end_date' and OrdersPay!=0 and OrdersEstate!='RefuseOrders'";
	$sql .= " group by p.ProductsId,o.CurrencyCode,o.lang";
	$mysql_result = mysql_query ( $sql ) or die ( mysql_error () );
	$order_array = array ();
	while ( $row = mysql_fetch_assoc ( $mysql_result ) ) {
		if ($row ['lang'] == '') {
			$row ['lang'] = 'all';
		}
		$lang = trim ( $row ['lang'] );
		$lang = $acceptLangCookie [$lang];
		if ($lang == NULL) {
			continue;
		}
		$payamount = 0;
		if ($row ["CurrencyCode"] != "USD") {
			$row ["payamount"] = round ( $row ["payamount"] * $currency [$row ["CurrencyCode"]] / $currency ["USD"], 2 );
		}
		
		$order_array [$row ["ProductsId"]] [$lang] ["payamount"] += $row ["payamount"];
		$order_array [$row ["ProductsId"]] [$lang] ["paynum"] += $row ["paynum"];
		$order_array [$row ["ProductsId"]] [$lang] ["payorder"] += $row ["payorder"];
	
		//$order_array[] = $row;
	}
	mysql_close ();
	return $order_array;
}
$etime = microtime ( true ); // 获取程序执行结束的时间
$total = $etime - $stime; // 计算差值
echo "\n" . $total . "times";
