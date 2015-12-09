<?php
namespace Model;

class PageVisits {
	
	public function getPageVisits($search, $url) {
		$db = \Lib\common\Db::get_db ( "default" );
		//$sql = "select a.pagename,b.* from web_statis.ma_page_name a,web_statis.ma_page_visits b,milanoo_gaea.products_categories c,milanoo_gaea.products p where a.id=b.pageid and c.id=p.CategoriesId and p.id=a.pagepid";
		if (! isset ( $page ))
			$page = PAGE;
		$paging = $search ["paging"];
		if (! $paging)
			$paging = 1;
		$last_num = $page * ($paging - 1);
		$sql_day = "select sum(pv) as pv,sum(uv) as uv from day where 1=1 ";
		
		$query_num = "select count(pagename) as pagenum,sum(pv) as pv,sum(uv) as uv,avg(averagetime) as averagetime,sum(pageenter) as pageenter,sum(pagebounce) as pagebounce,sum(pageexit) as pageexit,sum(payamount) as payamount from (";
		$sql = "select a.pagename,a.pagepid,sum(b.pv) as pv,sum(b.uv) as uv,avg(b.averagetime) as averagetime,sum(b.pageenter) as pageenter,sum(b.pagebounce) as pagebounce,sum(b.pageexit) as pageexit,sum(b.paynum) as paynum,sum(b.payamount) as payamount,sum(b.payorder) as payorder from ma_page_name a,ma_page_visits b where a.id=b.pageid";
		if ($_SESSION ["ma_starttime"]) {
			$sql .= " and b.time>='" . $_SESSION ["ma_starttime"] . "'";
			$sql_day .= " and time>='" . $_SESSION ["ma_starttime"] . "'";
		}
		if ($_SESSION ["ma_endtime"]) {
			$sql .= " and b.time<='" . $_SESSION ["ma_endtime"] . "' ";
			$sql_day .= " and time<='" . $_SESSION ["ma_endtime"] . "' ";
		}
		if ($_SESSION ["ma_lang"]) {
			$sql .= " and lang='" . $_SESSION ["ma_lang"] . "'";
			$sql_day .= " and lang='" . $_SESSION ["ma_lang"] . "'";
		}
		if ($search ["pagetype"]) {
			$sql .= " and a.pagetype='" . $search ["pagetype"] . "'";
			$url .= "&pagetype=" . $search ["pagetype"];
		}
		if ($search ["pagename"]) {
			$sql .= " and a.pagename like '%" . $search ["pagename"] . "%'";
			$url .= "&pagename=" . $search ["pagename"];
		}
		if ($search ["category"]) {
			$sql .= " and a.`pagelever` REGEXP 'c[0-9]-" . $search ["category"] . "(/|#|$)+'";
			$url .= "&category=" . $search ["category"];
		}
		$sql .= " group by a.pagename";
		$query = $query_num . $sql . ") c";
		$pagenum = $db->getrow ( $query );
		$daydata = $db->getrow ( $sql_day );
		$daydata["pvrate"] = number_format($pagenum["pv"]/$daydata["pv"]*100,2);
		//$daydata["uvrate"] = number_format($pagenum["uv"]/$daydata["uv"]*100,2);
		$new_url = "";
		if (isset ( $search ["desc"] )) {
			$desc = $search ["desc"];
			$new_url .= "&desc=" . $desc;
		} else
			$desc = "desc";
		if (isset ( $search ["order"] )) {
			$order = $search ["order"];
			if ($order == "pagebounce")
				$order = "sum(pagebounce)/sum(uv)";
			if ($order == "pageexit")
				$order = "sum(pageexit)/sum(pv)";
			$sql .= " order by " . $order . " " . $desc;
			$new_url .= "&order=" . $search ["order"];
		} else
			$sql .= " order by pv " . $desc;
		//echo $sql;
		$rs = $db->SelectLimit ( $sql, $page, $last_num );
		if ($rs->RecordCount ()) {
			$row = $page_list_array = array ();
			while ( ! $rs->EOF ) {
				$row = $rs->fields;
				$row ["averagetime"] = self::dataformat ( $row ["averagetime"] );
				$row ["pagename"] = urldecode ( $row ["pagename"] );
				$row ["bouncerate"] = round ( $row ["pagebounce"] / $row ["uv"] * 100, 2 );
				$row ["exitrate"] = round ( $row ["pageexit"] / $row ["pv"] * 100, 2 );
				$page_list_array [] = $row;
				$rs->MoveNext ();
			}
		}
		$pages = \Helper\Page::getpage ( $pagenum ["pagenum"], $page, $paging, $url . $new_url );
		$page_list ["page"] = $pages;
		$page_list ["url"] = $url;
		$page_list ["row"] = $page_list_array;
		$pagenum ["averagetime"] = self::dataformat ( $pagenum ["averagetime"] );
		$pagenum ["bouncerate"] = round ( $pagenum ["pagebounce"] / $pagenum ["uv"] * 100, 2 );
		$pagenum ["exitrate"] = round ( $pagenum ["pageexit"] / $pagenum ["pv"] * 100, 2 );
		$page_list ["collect"] = $pagenum;
		$page_list ["daydata"] = $daydata;
		return $page_list;
	}
	function dataformat($num) {
		$hour = floor ( $num / 3600 );
		$minute = floor ( ($num - 3600 * $hour) / 60 );
		$second = floor ( (($num - 3600 * $hour) - 60 * $minute) % 60 );
		return $hour . ':' . $minute . ':' . $second;
	}
	
	function getproductsale($pid) {
		$db = \Lib\common\Db::get_db ( "bi" );
		$sql = "select b.time,sum(b.payorder) as payorder,sum(b.paynum) as paynum,sum(b.payamount) as payamount from ma_page_name a,ma_page_visits b where a.id=b.pageid and a.pagepid='$pid' and a.pagetype='item'";
		if ($_SESSION ["ma_starttime"]) {
			$sql .= " and b.time>='" . $_SESSION ["ma_starttime"] . "'";
		}
		if ($_SESSION ["ma_endtime"]) {
			$sql .= " and b.time<='" . $_SESSION ["ma_endtime"] . "' ";
		}
		if ($_SESSION ["ma_lang"]) {
			$sql .= " and lang='" . $_SESSION ["ma_lang"] . "'";
		}
		$sql .= " group by b.time order by b.time asc";
		$rs = $db->SelectLimit ( $sql );
		if ($rs->RecordCount ()) {
			$row = $page_list_array = array ();
			while ( ! $rs->EOF ) {
				$row = $rs->fields;
				if(!$row["payamount"])$row["payamount"]=0;
				if(!$row["payorder"])$row["payorder"]=0;
				if(!$row["paynum"])$row["paynum"]=0;
				$time = strtotime($row["time"]);
				$row["time"] = date("m-d",$time);
				
				$page_list_array [] = $row;
				$rs->MoveNext ();
			}
		}
		return $page_list_array;
	}

}