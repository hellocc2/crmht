<?php

namespace config;

$file = '../config/Db.php';

if (is_file ( $file )) {
	include $file;
	$db_config = Db::$default;
}

namespace Helper;

$file_promotion = '../helper/Promotion.php';
if (is_file ( $file_promotion )) {
	include $file_promotion;
}

try {
	// Open database connection
	$con = mysqli_connect ( $db_config ['host'].":".$db_config ['port'], $db_config ['dbuser'], $db_config ['dbpassword']);
	mysql_set_charset("utf8", $con);
	mysql_query('SET NAMES utf8');
	mysql_select_db ( "web_statis", $con );
	
	$where = " and 1=1";
	$websiteId = $_GET ["websiteId"];
	
	if( $websiteId == 666 ){
		$websiteId = 'v.`WebsiteId`';
	}
	
	if (isset ( $_GET ["lang"] )) {
		$lang = $_GET ["lang"];
	}
		
	if (! empty ( $_POST ["PromotionName"] )) {
		$PromotionName = trim ( $_POST ["PromotionName"] );
		$sql = "SELECT DISTINCT category from milanoo_promotionurl where PromotionName like '".$PromotionName."%'";
		$result = mysql_query ( $sql );
		$row = mysql_fetch_array ( $result );
		$category = $row ['category'];
	    
		if ($category == 0){
			$where .= " and p.`PromotionName` like '".$PromotionName."%'";
		} else {
			//$where .= " and p.`PromotionName` like '".$PromotionName."%' and pa.`Id` ='" . $category . "'";
			$where .= " and p.`PromotionName` like '".$PromotionName."%'";
		}
		
	}
	
	if (! empty ( $_GET ["s_range"] )) {
		$s_range = date ( "Y-m-d 00:00:00", $_GET ["s_range"] );
		$where .= " and `time` >='" . $s_range . "'";
	}
	
	if (! empty ( $_GET ["e_range"] )) {
		$e_range = date ( "Y-m-d 00:00:00", $_GET ["e_range"] );
		$where .= " and `time` <='" . $e_range . "'";
	}
	
	if (! empty ( $_POST ["name"] ) and  $_POST ["name"] != 0) {
		$name = $_POST ["name"];
		//$where .= " and pa.`id` ='" . $name . "'";		
		@$class_all = new Promotion ( 'promotion_category', 0, 'ASC', '', 0, 1 );
		@$pid = $class_all->idALL ( $name );
		$where .= " and pa.`id` in (" . $name . $pid . ")";
	}

	if (! empty ( $_GET ["category"] ) and  $_GET ["category"] != 0) {
		$category_chart = $_GET ["category"];
		//$where .= " and p.`category` ='" . $category_chart . "'";
		@$class_all = new Promotion ( 'promotion_category', 0, 'ASC', '', 0, 1 );
		@$pid = $class_all->idALL ( $category_chart );
		$where .= " and pa.`id` in (" . $category_chart . $pid . ")";
	}
	
	// Getting records (listAction)
	if ($_GET ["action"] == "list") {
		
		if (! empty ( $lang )) {
			$where .= " and v.`lang` ='" . $lang . "'";
		}

		$sql = "SELECT COUNT(DISTINCT promotionid) AS RecordCount FROM `milanoo_promotionurl` p LEFT JOIN `milanoo_promotion_category` pa ON pa.`Id` = `category` AND pa.`WebsiteId` = '1', `ma_promotion_visits` v WHERE v.`WebsiteId` = " . $websiteId . " AND p.id = v.`promotionid`" . $where;
		// Get record count
		$result = mysql_query ( $sql );
		$row = mysql_fetch_array ( $result );
		$recordCount = $row ['RecordCount'];
		
		$sql = "SELECT IFNULL(SUM(`unpayorder`),0) as sum_unpayorder,IFNULL(SUM(`payorder`),0) as sum_payorder,IFNULL(SUM(`pv`),0) as sum_pv,IFNULL(SUM(`ip`),0) as sum_ip,IFNULL(SUM(`uv`),0) as sum_uv,IFNULL(SUM(`newUv`),0) as sum_newUv,IFNULL(SUM(`payamount`),0) as sum_payamount FROM `milanoo_promotionurl` p LEFT JOIN `milanoo_promotion_category` pa ON pa.`Id` = `category` AND pa.`WebsiteId` = 1,`ma_promotion_visits` v  WHERE v.`WebsiteId` = " . $websiteId . " AND p.id = v.`promotionid` " . $where . "";
		$result = mysql_query ( $sql );
		$row = mysql_fetch_array ( $result );
		$payorderTotal = $row ['sum_payorder'];
		$unpayorderTotal = $row ['sum_unpayorder'];
		$pvTotal = $row ['sum_pv'];
		$ipTotal = $row ['sum_ip'];
		$uvTotal = $row ['sum_uv'];
		$newUvTotal = $row ['sum_newUv'];
		$payamountTotal = $row ['sum_payamount'];

		// Get records from database
		$sql = "SELECT ".$pvTotal." as pvTotal ,".$ipTotal." as ipTotal ,".$uvTotal." as uvTotal ,".$newUvTotal." as newUvTotal ,".$payamountTotal." as payamountTotal,".$unpayorderTotal." as unPayorderTotal,".$payorderTotal." as PayorderTotal ,IFNULL(concat(round(SUM(`payorder`)/SUM(v.`uv`)*100,2),'%'),0) as purate, pa.`name`,p.`id`,p.`PromotionName`,p.`category`,IFNULL(SUM(v.`pv`),0) as pv,IFNULL(SUM(v.`uv`),0) as uv,IFNULL(SUM(v.`newUv`),0) as newUv,IFNULL(SUM(`payorder`),0) as payorder,IFNULL(SUM(`unpayorder`),0) as unpayorder,IFNULL(SUM(`payamount`), 0) AS payamount,IFNULL(SUM(`regmember`),0) as regmember,IFNULL(SUM(`subscribers`),0) as subscribers FROM `milanoo_promotionurl` p LEFT JOIN `milanoo_promotion_category` pa ON pa.`Id` = `category` AND pa.`WebsiteId` = 1,`ma_promotion_visits` v  WHERE v.`WebsiteId` = " . $websiteId . " AND p.id = v.`promotionid` " . $where . " GROUP BY  `promotionid` ORDER BY " . $_GET ["jtSorting"] . " LIMIT " . $_GET ["jtStartIndex"] . "," . $_GET ["jtPageSize"] . ";";
		// echo $sql;exit;
		$result = mysql_query ( $sql );
		// Add all records to an array
		$rows = array ();
		while ( $row = mysql_fetch_array ( $result ) ) {
			$rows [] = $row;
		}
		//var_dump($rows);exit;
		// Return result to jTable
		$jTableResult = array ();
		$jTableResult ['Result'] = "OK";
		$jTableResult ['TotalRecordCount'] = $recordCount;
		//$jTableResult ['PayorderTotal'] = $payorderTotal;
		$jTableResult ['Records'] = $rows;
		print json_encode ( $jTableResult );
	} 	// Creating a new record (createAction)
	else if ($_GET ["action"] == "create") {
		// Insert record into database
		$result = mysql_query ( "INSERT INTO people(Name, Age, RecordDate) VALUES('" . $_POST ["Name"] . "', " . $_POST ["Age"] . ",now());" );
		
		// Get last inserted record (to return to jTable)
		$result = mysql_query ( "SELECT * FROM people WHERE PersonId = LAST_INSERT_ID();" );
		$row = mysql_fetch_array ( $result );
		
		// Return result to jTable
		$jTableResult = array ();
		$jTableResult ['Result'] = "OK";
		$jTableResult ['Record'] = $row;
		print json_encode ( $jTableResult );
	} 	// Updating a record (updateAction)
	else if ($_GET ["action"] == "update") {
		// Update record in database
		$result = mysql_query ( "UPDATE people SET Name = '" . $_POST ["Name"] . "', Age = " . $_POST ["Age"] . " WHERE PersonId = " . $_POST ["PersonId"] . ";" );
		
		// Return result to jTable
		$jTableResult = array ();
		$jTableResult ['Result'] = "OK";
		print json_encode ( $jTableResult );
	} 	// Deleting a record (deleteAction)
	else if ($_GET ["action"] == "delete") {
		// Delete from database
		$result = mysql_query ( "DELETE FROM people WHERE PersonId = " . $_POST ["PersonId"] . ";" );
		
		// Return result to jTable
		$jTableResult = array ();
		$jTableResult ['Result'] = "OK";
		print json_encode ( $jTableResult );
	}
	else if ($_GET ["action"] == "chart") {
	
		if (! empty ( $lang )) {
			$where .= " and `lang` ='" . $lang . "'";
		}

		$s_range = strtotime ( $s_range );
		$e_range = strtotime ( $e_range );
		$ss_range = date ( "Y-m-d", $s_range );
		$ee_range = date ( "Y-m-d", $e_range );
		
		for($d = $s_range; $d <= $e_range; $d = $d + 86400) {
			$day_array [date ( "Y-m-d", $d )] = array ();
		}		
		
		if (! empty ( $_GET ["promotion_name_row"] ) and isset($_GET ["islike"])) {
			$PromotionName = trim ( $_GET ["promotion_name_row"] );
			$sql = "SELECT DISTINCT category from milanoo_promotionurl where PromotionName = '".$PromotionName."'";
			$result = mysql_query ( $sql );
			$row = mysql_fetch_array ( $result );
			$category = $row ['category'];
			$where .= " and p.`PromotionName` = '".$PromotionName."' and p.`category` ='" . $category . "'";
		} elseif (! empty ( $_GET ["promotion_name_row"] ) and !isset($_GET ["islike"])) {
			$PromotionName = trim ( $_GET ["promotion_name_row"] );
			//$sql = "SELECT DISTINCT category from milanoo_promotionurl where PromotionName = '".$PromotionName."'";
			//$result = mysql_query ( $sql );
			//$row = mysql_fetch_array ( $result );
			//$category = $row ['category'];
			$where .= " and p.`PromotionName` like '".$PromotionName."%'";
		}
		
		$sql = "SELECT v.`time` ,SUM(`payorder`)/SUM(v.`uv`) as purate,SUM(v.`ip`) as ip ,SUM(v.`pv`) as pv ,SUM(v.`uv`) as uv ,SUM(v.`newUv`) as newUv ,SUM(`payorder`) as payorder,SUM(`unpayorder`) as unpayorder ,SUM(`regmember`) as regmember ,SUM(`subscribers`) as subscribers FROM `milanoo_promotionurl` p LEFT JOIN `milanoo_promotion_category` pa ON pa.`Id` = `category` AND pa.`WebsiteId` = 1,`ma_promotion_visits` v WHERE v.`WebsiteId` = " . $websiteId . " and p.id = v.`promotionid` " . $where . " GROUP BY  v.`time` ORDER BY v.`time`";
		$result = mysql_query ( $sql );
		// Add all records to an array
		//echo $sql;
		$row = array ();
		while ( $row = mysql_fetch_assoc ( $result ) ) {
			
				if (empty ( $row ['ip'] )) {
					$row ['ip'] = 0;
				}
				if (empty ( $row ['pv'] )) {
					$row ['pv'] = 0;
				}
				if (empty ( $row ['uv'] )) {
					$row ['uv'] = 0;
				}
				if (empty ( $row ['newUv'] )) {
					$row ['newUv'] = 0;
				}
				if (empty ( $row ['payorder'] )) {
					$row ['payorder'] = 0;
				}
				if (empty ( $row ['unpayorder'] )) {
					$row ['unpayorder'] = 0;
				}
				if (empty ( $row ['regmember'] )) {
					$row ['regmember'] = 0;
				}
				if (empty ( $row ['subscribers'] )) {
					$row ['subscribers'] = 0;
				}
				if (empty ( $row ['payamount'] )) {
					$row ['payamount'] = 0;
				}
				if (empty ( $row ['purate'] )) {
					$row ['purate'] = 0;
				}
				$row ["time"] = date ( "Y-m-d", strtotime ( $row ["time"] ) );
				$day_array [date ( "Y-m-d", strtotime ( $row ["time"] ) )] = $row;
		}
		//var_dump($day_array);exit;
		foreach ( $day_array as $key => $value ) {
			if (empty ( $value )) {
				$data_array [] = $day_array [$key] = array (
						'ip' => 0,
						'pv' => 0,
						'uv' => 0,
						'newUv' => 0,
						'payorder' => 0,
						'unpayorder' => 0,
						'payamount' => 0,
						'purate' => 0,
						'regmember' => 0,
						'subscribers' => 0,
						'time' => $key 
				);
			} else {
				$data_array [] = $value;
			}
		}
		
		print json_encode ( $data_array );
	}
	
	// Close database connection
	mysql_close ( $con );
} catch ( Exception $ex ) {
	// Return error message
	$jTableResult = array ();
	$jTableResult ['Result'] = "ERROR";
	$jTableResult ['Message'] = $ex->getMessage ();
	print json_encode ( $jTableResult );
}

?>