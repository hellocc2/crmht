<?php
namespace Model;

class Register {
	
	/**
	 * 会员管理
	 * @param array $data
	 */
    public function getUserList(){
        $db = \Lib\common\Db::get_db ( "milanoo" );
        $acceptLang = \config\Language::$acceptLang;
    }
	public function getSaleSell($time) {
		$db = \Lib\common\Db::get_db ( "milanoo" );
		
		$acceptLang = \config\Language::$acceptLang;
		if ($time == "month")
			$str = "y-m";
		else
			$str = "y-m-d";
		
		$sql = "select a.device_type,a.ordersaddtime,ROUND(sum(a.`OrdersAmount` * c.ex_rate / d.`ex_rate` ),2) as OrdersAmount,ROUND(sum(a.OrdersLogisticsCosts* c.ex_rate / d.`ex_rate` ),2) as LogisticsAmount,ROUND(sum(a.insurance* c.ex_rate / d.`ex_rate` ),2) as Insurance from milanoo.`milanoo_orders` a,milanoo.`t_exchange_rate` c,milanoo.`t_exchange_rate` d WHERE a.`CurrencyCode` = c.`currency` AND d.`currency` = 'USD'  AND a.orderspay > 0";
		if ($_SESSION ["ma_starttime"]) {
			$starttime = strtotime ( $_SESSION ["ma_starttime"] );
			$sql .= " and a.ordersaddtime>='" . $starttime . "'";
		}
		if ($_SESSION ["ma_endtime"]) {
			$endtime = explode ( "-", $_SESSION ["ma_endtime"] );
			$endtime = mktime ( 0, 0, 0, $endtime [1], $endtime [2] + 1, $endtime [0] );
			$sql .= " and a.ordersaddtime<'" . $endtime . "' ";
		}
		if ($_SESSION ["ma_lang"] && $_SESSION ["ma_lang"] != "all") {
			$lang = $acceptLang [$_SESSION ["ma_lang"]];
			$sql .= " and a.lang='" . $lang . "'";
		}
		$sql .= " group by a.device_type,a.`OrdersId` order by a.`OrdersId` asc";
		$rs = $db->SelectLimit ( $sql );
		if ($rs->RecordCount ()) {
			$row = $page_list_array = array ();
			while ( ! $rs->EOF ) {
				$row = $rs->fields;
				if ($time == "week") {
					$date = $this->get_week_format ( $row ["ordersaddtime"] );
				} else
					$date = date ( $str, $row ["ordersaddtime"] );
				$page_list_array [$date] [$row ["device_type"]] ["OrdersAmount"] += $row ["OrdersAmount"];
				$page_list_array [$date] [$row ["device_type"]] ["Insurance"] += $row ["Insurance"];
				$page_list_array [$date] [$row ["device_type"]] ["LogisticsAmount"] += $row ["LogisticsAmount"];
				$page_list_array [$date] [$row ["device_type"]] ["OrderNum"] += 1;
				$rs->MoveNext ();
			}
		}
		return $page_list_array;
	}
	
	public function getCategorySell($time,$pid,$leve) {
		$db = \Lib\common\Db::get_db ( "milanoo" );
		
		$acceptLang = \config\Language::$acceptLang;
		if ($time == "month")
			$str = "y-m";
		else
			$str = "y-m-d";
		$sql = "select pa.`id`,pa.`category_code`,length(pa.`category_code`) as level,pl.`category_name`,a.ordersaddtime,count(a.`OrdersId`) as OrderNum,sum(op.`ProductsPrice`*op.`ProductsNum`* c.ex_rate / d.`ex_rate` ) as ProductsPrice,sum(op.`SuppliersPrice`*op.`ProductsNum` / d.`ex_rate` ) as SuppliersPrice,sum(op.`ProductsNum`) as ProductsNum from milanoo.`milanoo_orders` a,milanoo.`milanoo_orders_products` op,milanoo_gaea.`products` p,milanoo_gaea.`products_categories` ca,milanoo_gaea.`products_categories_lang` l,milanoo_gaea.`products_categories` pa,milanoo_gaea.`products_categories_lang` pl,milanoo.`t_exchange_rate` c,milanoo.`t_exchange_rate` d WHERE a.`CurrencyCode` = c.`currency` AND d.`currency` = 'USD' and a.OrdersId=op.`OrdersId` and op.`ProductsId`=p.`id` and p.`CategoriesId`=ca.`id` and ca.`id`=l.products_categorie_id and l.`language_id`=1 and pl.`language_id`=1 and pa.`id`=pl.products_categorie_id  AND a.orderspay > 0 ";
		if ($_SESSION ["ma_starttime"]) {
			$starttime = strtotime ( $_SESSION ["ma_starttime"] );
			$sql .= " and a.ordersaddtime>='" . $starttime . "'";
		}
		if ($_SESSION ["ma_endtime"]) {
			$endtime = explode ( "-", $_SESSION ["ma_endtime"] );
			$endtime = mktime ( 0, 0, 0, $endtime [1], $endtime [2] + 1, $endtime [0] );
			$sql .= " and a.ordersaddtime<'" . $endtime . "' ";
		}
		if ($_SESSION ["ma_lang"] && $_SESSION ["ma_lang"] != "all") {
			$lang = $acceptLang [$_SESSION ["ma_lang"]];
			$sql .= " and a.lang='" . $lang . "'";
		}
		if ($_SESSION ["ma_websiteId"] == 101) {
			$sql .= " and a.websiteId='1' and a.device_type>1";
		} elseif (  $_SESSION ["ma_websiteId"]  == 666 ) {
			$sql .= " and 1='1'";
		} elseif (! empty ( $_SESSION ["ma_websiteId"] )) {
			$sql .= " and a.websiteId='" . $_SESSION ["ma_websiteId"] . "'";
		}
		if ($pid) {
			$length=$leve?($leve+1)*5:5;
			$sql .= " and SUBSTR(ca.`category_code`, 1, ".$length.") = pa.`category_code`";
			$sql .= " and ca.`category_code` LIKE '".$pid."%'";
		} else {
			$sql .= " and SUBSTR(ca.`category_code`, 1, 5) = pa.`category_code`";
		}
		$sql .= " group by pa.`id`,a.`OrdersId` order by a.`OrdersId` asc";

		$rs = $db->SelectLimit ( $sql );
		if ($rs->RecordCount ()) {
			$row = $page_list_array = array ();
			while ( ! $rs->EOF ) {
				$row = $rs->fields;
				if ($time == "week") {
					$date = $this->get_week_format ( $row ["ordersaddtime"] );
				} else
					$date = date ( $str, $row ["ordersaddtime"] );
				$page_list_array [$date] [$row ["id"]] ["OrderNum"] += 1;
				$page_list_array [$date] [$row ["id"]] ["ProductsNum"] += $row ["ProductsNum"];
				$page_list_array [$date] [$row ["id"]] ["SuppliersPrice"] += $row ["SuppliersPrice"];
				$page_list_array [$date] [$row ["id"]] ["ProductsPrice"] += $row ["ProductsPrice"];
				$page_list_array [$date] [$row ["id"]] ["category_name"] = $row ["category_name"];
				$page_list_array [$date] [$row ["id"]] ["category_code"] = $row ["category_code"];
				$page_list_array [$date] [$row ["id"]] ["level"] = $row ["level"]/5;
				$rs->MoveNext ();
			}
		}
		return $page_list_array;
	
	}
	
	public function get_week_format($time, $start = 4, $end = 3) {
		$week = date ( "N", $time );
		$last = $week - $start;
		if ($last >= 0) {
			$data = 6 - abs ( $week - $start );
		} else {
			$last = 6 - abs ( $end - $week );
			$data = abs ( $end - $week );
		}
		$result = date ( "y-m-d", mktime ( 0, 0, 0, date ( "m", $time ), date ( "d", $time ) - $last, date ( "Y", $time ) ) ) . " - " . date ( "y-m-d", mktime ( 0, 0, 0, date ( "m", $time ), date ( "d", $time ) + $data, date ( "Y", $time ) ) );
		return $result;
	}

} 