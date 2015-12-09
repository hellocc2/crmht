<?php
namespace Model;
use \Helper\Analyzer as Analyzer;

class Conversion{
	
	/**
	 * 转化率统计
	 * @param array $data
	 */
	public function getConversionRate(){
		//$db = \Lib\common\Db::get_db ( "bi" );
		$db = \Lib\common\Db::get_db ('default');
		//$db->debug=1;
		$sql = "select ROUND((sum(paynum)/sum(uv)*100),2) as rate,(sum(member)/sum(newUv)*100) as regrate,ROUND((sum(paynum)/sum(notpaynum)*100),2) as payrate,ROUND(avg(visitdepth),2) as visitdepth,time from day where 1=1";
		if ($_SESSION ["ma_starttime"]) {
			$sql .= " and time>='" . $_SESSION ["ma_starttime"] . "'";
		}
		if ($_SESSION ["ma_endtime"]) {
			$sql .= " and time<='" . $_SESSION ["ma_endtime"] . "' ";
		}
		if (!empty($_SESSION ["ma_lang"])) {
			if($_SESSION ["ma_lang"]!='all'){
				$sql .= " and lang='" . $_SESSION ["ma_lang"] . "'";
			}
		}
		if ($_SESSION ["ma_websiteId"] == 101) {
			$sql .= " and websiteId='1' and a.device_type>1";
		} elseif (  $_SESSION ["ma_websiteId"]  == 666 ) {
			$sql .= " and 1='1'";
		} elseif (! empty ( $_SESSION ["ma_websiteId"] )) {
			$sql .= " and websiteId='" . $_SESSION ["ma_websiteId"] . "'";
		}
		$sql .= " group by time order by time";
		//echo $sql;
		$rs = $db->SelectLimit ( $sql );
		$row = $page_list_array = array ();
		if ($rs->RecordCount ()) {
			while ( ! $rs->EOF ) {
				$row = $rs->fields;
				$time = strtotime($row["time"]);
				$row["time"] = date("m-d",$time);
				$page_list_array [] = $row;
				$rs->MoveNext ();
			}
		}
		//echo '123<pre>';print_r($page_list_array);die;
		return $page_list_array;
	}
	/**
	 * 按1分钟前的数据分析  PV
	 * @param array $data
	 */
	public function getBase1mdataByRedis(){
		$analyzer = new Analyzer();
		//echo '<pre>';print_r($_GET);print_r($_POST);die;
		if (isset($_POST['minute_range'])) {
			$time = explode("-", $_POST['minute_range']);
			$day=$_POST['minute_range'];
			$start=$end='';
			if (count($time) == 1) {
				$start ="00:00";
				$end = "23:00";
			} else {
				if(!empty($_POST['minute_timepicker_start']) &&!empty($_POST['minute_timepicker_end'])){
					$start = str_replace("h", ":", $_POST['minute_timepicker_start']);
					$end = str_replace("h", ":", $_POST['minute_timepicker_end']);
				}
			}
			$start_time 	= $day . ' ' . $start;
			$end_time 	    = $day . ' ' . $end;
			$result			= $analyzer -> get_minute_statistics( $start_time, $end_time);
		} else {
			$result=$analyzer -> get_1m_statistics();
		}
		return $result;
	}	
	
	
	/**
	 * 按时分析 PV|UV|IP|新增独立访客
	 * @param array $data
	 */
	public function getBaseDataByRedis(){
		$analyzer = new Analyzer();
		//echo '<pre>';print_r($_GET);print_r($_POST);die;
		if (isset($_POST['minute_range'])) {
			$time = explode("-", $_POST['minute_range']);
			$day=$_POST['minute_range'];
			$start=$end='';
			if (count($time) == 1) {
				$start ="00:00";
				$end = "23:00";
			} else {
				if(!empty($_POST['minute_timepicker_start']) &&!empty($_POST['minute_timepicker_end'])){		
					$start = str_replace("h", ":", $_POST['minute_timepicker_start']);
					$end = str_replace("h", ":", $_POST['minute_timepicker_end']);
				}
			}			
			$start_time 	= $day . ' ' . $start;
			$end_time 	= $day . ' ' . $end;			
			$result			= $analyzer -> get_minute_statistics( $start_time, $end_time);
		} else {
			$result=$analyzer -> get_15m_statistics();
		}
		return $result;
	}
	/**
	 * 按时分析 PV|UV|IP|新增独立访客
	 * @param array $data
	 */
	public function getDayBaseDataByRedis($day_range){
		$analyzer = new Analyzer();
		$time	= explode("-", $day_range);
		$day	=$day_range;
		$start	=$end='';
		if (count($time) == 1) {
			$start ="00:00";
			$end = "23:00";
		} else {
			if(!empty($_POST['minute_timepicker_start']) &&!empty($_POST['minute_timepicker_end'])){		
				$start = str_replace("h", ":", $_POST['minute_timepicker_start']);
				$end = str_replace("h", ":", $_POST['minute_timepicker_end']);
			}
		}
		$start_time = $day . ' ' . $start;
		$end_time = $day . ' ' . $end;
		$result= $analyzer -> get_hour_range_statistics( $start_time, $end_time);
		return $result;
	}
	
	
	/**
	 * 按时分析 PV|UV|IP|新增独立访客
	 * @param array $data
	 */
	public function getDayHourByRedis(){
		//echo '<pre>';print_r($_GET);print_r($_POST);die;
		$time='';
		if (isset($_POST['range'])) {
			$time = explode(" - ", $_POST['range']);
			if (count($time) == 1) {
				$start_time = $time['0'] . "00:00";
				$end_time = $time['0'] . "23:00";
			} else {
				$start_time = $time['0'];
				$end_time = $time['1'];
			}
		} else {
			$start_time = date("m\/d\/Y", strtotime("-1 month"));
			$end_time = date("m\/d\/Y", time());
		}
		
		$analyzer = new Analyzer();
		if (empty($_POST['dayhour']) || $_POST['dayhour']=='1') {
			$date = $analyzer -> get_date_range_statistics($start_time, $end_time);
		} else {
			$date = $analyzer -> get_hour_range_statistics($start_time, $end_time);
		}		
		return $date;
	}
	
	
	/**
	 * 从redis获取比较数据
	 * @param array $data
	 */
	public function getCompareByRedis($l_range,$r_range,$start_time='',$end_time='',$format='m'){
		$analyzer = new Analyzer();		
		if(empty($start_time) || empty($end_time)){
			$start_time ="00:00";
			$end_time = "23:00";
		}
		$start_time = str_replace("h", ":", $start_time);
		$end_time = str_replace("h", ":", $end_time);		
		//左边区域时间段数据	
		$l_start_time 	= strtotime($l_range . " " .$start_time);
		$l_end_time 		= strtotime( $l_range . " " .$end_time);
		
		$l_date 			= $analyzer -> getTimeStatisticsByRedis($l_start_time, $l_end_time,$format);
		//echo '<pre>';print_r($l_date);die;
		//右边区域时间段数据		
		$r_start_time 	= strtotime($r_range . " " .$start_time);
		$r_end_time 	= strtotime($r_range . " " .$end_time);
		$r_date 			= $analyzer -> getTimeStatisticsByRedis($r_start_time, $r_end_time,$format);
		//echo '<pre>';print_r($l_date);print_r($r_date);die;
		//组合两区域数据
		$new_date=$all_date=array();
		$l_count=count($l_date);
		$r_count=count($r_date);
		if($l_count<$r_count){
			$temp		=$r_date;
			$r_date	=$l_date;
			$l_date		=$temp;
			$max=$r_count;
		}else{
			$max=$l_count;
		}
		//echo $max;die;
		for($i=0;$i<$max;$i++){
			$new_date=array();			
			//echo '123<pre>';print_r($l_date);die;
			foreach($l_date[$i] as $key=>$value){
				$new_date['l_'.$key]=$value;
			}
			foreach($r_date[$i] as $key=>$value){
				$new_date['r_'.$key]=$value;
			}
			$new_date['dimension']=$i;
			$all_date[]=$new_date;
		}
		//echo '123<pre>';print_r($all_date);die;
		return $all_date;
	}
	/**
	 * 从数据库获取比较数据，专门用于天与天之间的对比
	 * @param array $data
	 */
	public function getCompareByDb($l_range,$r_range){
		//左边区域时间段数据		
		$l_time = explode(" - ", $l_range);
		$l_start_time 	= strtotime($l_time['0']);
		$l_end_time 	= strtotime($l_time['1']);
		$l_date 			= $this -> getDayStatisticsByDb($l_start_time, $l_end_time);
		//右边区域时间段数据		
		$r_time = explode(" - ", $r_range);
		$r_start_time 	= strtotime($r_time['0']);
		$r_end_time 	= strtotime($r_time['1']);
		$r_date 			= $this -> getDayStatisticsByDb($r_start_time, $r_end_time);
		//组合两区域数据
		$new_date=$all_date=array();
		$l_count=count($l_date);
		$r_count=count($r_date);
		if($l_count<$r_count){
			$temp		=$r_date;
			$r_date	=$l_date;
			$l_date		=$temp;
			$max=$r_count;
		}else{
			$max=$l_count;
		}
		for($i=0;$i<$max;$i++){
			$new_date=array();			
			foreach($l_date[$i] as $key=>$value){
				$new_date['l_'.$key]=$value;
			}
			foreach($r_date[$i] as $key=>$value){
				$new_date['r_'.$key]=$value;
			}
			$new_date['dimension']=$i;
			$all_date[]=$new_date;
		}
		//echo '123<pre>';print_r($all_date);die;
		return $all_date;
	}
	/**
	 * 从数据库获取日期数据
	 * @param array $data
	 */
	public function getDayStatisticsByDb($starttime,$endtime){	
		$db = \Lib\common\Db::get_db ('default');
		//$db->debug=1;
		//if($_SESSION ["ma_lang"]!='all'){		
			//$sql = "select  * from day where 1=1";
		//}else{
			$sql = "select  sum(pv) as pv,sum(ip) as ip,sum(uv) as uv,sum(newUv) as newUv,sum(visittime) as visittime,time from day where 1=1";
		//}
		if ($starttime) {
			$starttime 	= date('Y-m-d',$starttime);
			$sql .= " and time>='" . $starttime . "'";
		}
		if ($endtime) {
			$endtime 	= date('Y-m-d', $endtime);
			$sql .= " and time<='" . $endtime . "' ";
		}
		if (!empty($_SESSION ["ma_lang"])) {
			if($_SESSION ["ma_lang"]!='all'){		
				$sql .= " and lang='" . $_SESSION ["ma_lang"] . "'";
			}
		}
		if ( $_SESSION ["ma_websiteId"]  == 666 ) {
			$sql .= " and 1='1'";
		} elseif (  !empty($_SESSION ["ma_websiteId"]) ) {
			$sql .= " and websiteId='" . $_SESSION ["ma_websiteId"] . "'";
		}
		$sql .= " group by time order by time";
		
		$rs = $db->SelectLimit ( $sql );
		$row = $page_list_array = array ();
		if ($rs->RecordCount ()) {
			while ( ! $rs->EOF ) {
				$row = $rs->fields;
				$time = strtotime($row["time"]);
				$row["time"] = date("m-d",$time);
				$row["day"] = date("j",$time);//用于同期比较时候的相同维度值
				$page_list_array [] = $row;
				$rs->MoveNext ();
			}
		}
		//echo '123<pre>';print_r($page_list_array);die;
		return $page_list_array;
	}
} 