<?php
namespace Helper;

class Analyzer {

	public function __construct() {
		if(!isset($this->redis))
			$this->redis = \Lib\common\Redism::get_redis();
		
		date_default_timezone_set(@date_default_timezone_get());
		$domainInWebsiteId = array ( '1'=> 'milanoo', '2' => 'dressinwedding', '3' => 'lolitashow', '4' => 'cosplay', '5'=> 'costumeslive', '101' => 'wap','6'=> 'milanoo.de','7'=> 'milanoo.fr','201'=> 'iPad','666'=> 'ALL');
		//$acceptCountryCookie = array ( 'FR' => 'en', 'JP' => 'jp', 'FR' => 'fr', 'ES' => 'es', 'DE' => 'de', 'IT' => 'it', 'RU' => 'ru','PT'=>'pt','all'=>'ALL');
		$this -> websiteId 			= !empty($_SESSION['ma_websiteId'])?$_SESSION['ma_websiteId']:1;
		$this -> redis_key_name 	= ($domainInWebsiteId[$this ->websiteId]).':'.$_SESSION['ma_lang'];
		//echo '<hr>',$this -> redis_key_name;die;
	}
	
	protected function exact_time() {
		$time = strtotime(date("Y-m-d H:i:s"));
		return $time;
	}

	//最近 24+3 时间段
	protected function time27h() {
		$now = strtotime(date("Y-m-d G:00:00"));

		for ($i = 0; $i <= 27; $i++) {
			$hour = (-27) + $i;
			$timeline = $past = strtotime(date("Y-m-d G:00:00", strtotime($hour . " hour")));
			$date[] = date("Y-m-d H", $timeline);
		}
		return $date;
	}

	//今天的时间段
	protected function timeperiods() {
		$Botday = strtotime(date("Y-m-d 00:00:00"));
		$teotday = strtotime(date("Y-m-d" . "23:59:59"));
		$timeperiods = $Botday . '#' . $teotday;
		return $timeperiods;
	}

	//昨天的时间
	protected function yesterday() {
		$yesterday = date("Y-m-d", strtotime("-1 day"));
		return $yesterday;
	}

	//现在的时间戳不带小时
	protected function now() {
		$today = date("Y-m-d");
		return $today;
	}

	protected function time1() {
		for ($i = 0; $i < 1; $i++) {
			$hour = (-1) + $i;
			$timeline = $past = strtotime(date("G:i:00", strtotime($hour . " minutes")));
			$date[] = date("Y-m-d H/i", $timeline);
	
		}
		return $date;
	}
	
	protected function time15() {
		for ($i = 0; $i < 15; $i++) {
			$hour = (-15) + $i;
			$timeline = $past = strtotime(date("G:i:00", strtotime($hour . " minutes")));
			$date[] = date("Y-m-d H/i", $timeline);

		}
		return $date;
	}

	protected function time30() {
		for ($i = 0; $i < 30; $i++) {
			$hour = (-30) + $i;
			$timeline = $past = strtotime(date("G:i:00", strtotime($hour . " minutes")));
			$date[] = date("Y-m-d H/i", $timeline);
		}
		return $date;
	}

	protected function per_time30( $today ,$time_rang ) {
		//$today = strtotime(date("Y-m-d 00:00:00"))-1800;
		for ($i = 0; $i < 30; $i++) {
			$hour = $i;
			$timeline = $past = $today+($i*60+$time_rang);
			$date[] = date("Y-m-d H/i", $timeline);
		}
		return $date;
	}	
	

	//获得最近 1分钟 时段的统计数据
	function get_1m_statistics() {
		$r 			= $this->redis;
		$timeline 	= $this -> time1();
		//var_dump($timeline);
		$type = array( 'pv' );
		foreach ($timeline as $time) {
			$mk = strtotime(str_replace('/', ':', $time));
			foreach ($type as $key => $value) {
				$last_visit = $r -> HGET($this -> redis_key_name . ":minutes:" . $time . ":total", "$value");
				if (!$last_visit) {
					$last_visit = "0";
				}
				$count[$mk][$value] = $last_visit;
			}
			$count[$mk]['time']= trim(strstr(str_replace('/', ':', $time),' '));
		}
		//echo '<pre>';print_r($count);exit;
		return $count;
	}
	
	//获得最近 15分钟 时段的统计数据
	function get_15m_statistics() {
		$r 			= $this->redis;
		$timeline 	= $this -> time15();
		$type = array('ip', 'newUv', 'pv', 'uv');		
		foreach ($timeline as $time) {
			$mk = strtotime(str_replace('/', ':', $time));			
			foreach ($type as $key => $value) {
				$last_visit = $r -> HGET($this -> redis_key_name . ":minutes:" . $time . ":total", "$value");
				if (!$last_visit) {
					$last_visit = "0";
				}
				$count[$mk][$value] = $last_visit;
			}
			$count[$mk]['time']= trim(strstr(str_replace('/', ':', $time),' '));
		}
		//echo '<pre>';print_r($count);exit;
		return $count;
	}

	function get_30m_statistics() {
		$r = redisLink();
		$timeline = $this -> time30();
		$type = array('ip', 'newUv', 'pv', 'uv');
		
		foreach ($timeline as $time) {
			$mk = strtotime(str_replace('/', ':', $time));
			foreach ($type as $key => $value) {
				$last_visit = $r -> HGET($this -> redis_key_name . ":minutes:" . $time . ":total", "$value");

				if (!$last_visit) {
					$last_visit = "0";
				}
				$count[$mk][$value] = $last_visit;
			}
		}
		//var_dump($count);exit;
		//生成 XML
		$type = array('pv', 'uv', 'ip' , 'newUv');
		$format = "H:i:s";
		$this -> generate_chart ( $count , $type , $format);
	}	

	function get_per_30m_statistics ( $today ,$time_rang ) {
		$r = redisLink();
		$timeline = $this -> per_time30( $today ,$time_rang );
		$type = array('pv','uv');
		foreach ($timeline as $time) {
			//var_dump($time);
			//$mk = strtotime(str_replace('/', ':', $time));
			foreach ($type as $key => $value) {
				$last_visit = $r -> HGET($this -> redis_key_name . ":minutes:" . $time . ":total", "$value");	
				if (!$last_visit) {
					$last_visit = "0";
				}
				if (empty($count[$timeline[0]])) {
					$count[$value][$timeline[0]]= $last_visit;
				} else {
					$count[$value][$timeline[0]]= $count[$value][$timeline[0]]+$last_visit;
				}

			}
		}
		return $count;
	}
	
	//获得最近 24+3 时段的统计数据
	function get_27hours_statistics() {
		$timeline = $this -> time27h();
		//var_dump($timeline);exit;
		$r = redisLink();
		$type = array('ip', 'newUv', 'pv', 'uv','timeOnSite');

		foreach ($timeline as $time) {
			$mk = $time . ':00:00';
			$mk = strtotime($mk);
			//$mk = date("Ymd-H:i:s", $mk);
			//$time = strtotime($time);
			foreach ($type as $key => $value) {
				$visit = $r -> HGET($this -> redis_key_name . ":hour:" . $time . ":total", "$value");
				if (!$visit) {
					$visit = "0";
				}
				$count[$mk][$value] = $visit;
			}
		}
		
		$type = array('pv', 'uv', 'ip' , 'newUv','timeOnSite');
		//var_dump($count);exit;
		$this -> generate_chart ( $count , $type);

	}

	function get_minute_statistics( $minute_timepicker_start, $minute_timepicker_end ) {		
		$time_range =array();
		$count = array();
		$start_time_u = strtotime($minute_timepicker_start);
		$end_time_u = strtotime($minute_timepicker_end);		
		for ($i=$start_time_u; $i <= $end_time_u; $i=$i+60) {
			$timeline[]= date("Y-m-d H/i" , $i);
		}		
		$r =$this->redis;
		$type = array('ip', 'newUv', 'pv', 'uv');	
		//echo '<pre>';print_r($minute_timepicker_end);die;		
		foreach ($timeline as $time) {
			$mk = strtotime(str_replace('/', ':', $time));
			foreach ($type as $key => $value) {
				//echo $this -> redis_key_name . ":minutes:" . $time . ":total", "$value";die;
				$last_visit = $r -> HGET($this -> redis_key_name . ":minutes:" . $time . ":total", "$value");
				if (!$last_visit) {
					$last_visit = "0";
				}
				$count[$mk][$value] = $last_visit;
			}
			$count[$mk]['time']= trim(strstr(str_replace('/', ':', $time),' '));
		}	
		//echo '<pre>';print_r($count);die;		
		return $count;
	}
	function get_compare_statistics( $minute_timepicker_start, $minute_timepicker_end,$direct='l' ) {		
		$time_range =array();
		$count = array();
		$start_time_u = strtotime($minute_timepicker_start);
		$end_time_u = strtotime($minute_timepicker_end);		
		for ($i=$start_time_u; $i <= $end_time_u; $i=$i+60) {
			$timeline[]= date("Y-m-d H/i" , $i);
		}		
		$r =$this->redis;
		$type = array('ip', 'newUv', 'pv', 'uv');	
		//echo '<pre>';print_r($minute_timepicker_start);die;		
		foreach ($timeline as $time) {
			$mk = strtotime(str_replace('/', ':', $time));
			$hm=trim(strstr(str_replace('/', ':', $time),' '));;
			$count[$hm]['time']= $hm;
			$count[$hm][$direct.'_time']=$time;
			foreach ($type as $key => $value) {
				$last_visit = $r -> HGET($this -> redis_key_name . ":minutes:" . $time . ":total", "$value");
				if (!$last_visit) {
					$last_visit = "0";
				}
				$count[$hm][$direct.'_'.$value] = $last_visit;
			}
		}	
		//echo '<pre>';print_r($count);die;		
		return $count;
	}
	function getTimeStatisticsByRedis( $minute_timepicker_start, $minute_timepicker_end,$format='m') {		
		$time_range =array();
		$count = array();
		if($format=='m'){
			$time_i			=60;
			$key_2			=$this -> redis_key_name . ':minutes:';
			$timeFormat='Y-m-d H/i';
		}else if($format=='h'){
			$time_i			=3600;
			$key_2			=$this -> redis_key_name . ':hour:';
			$timeFormat='Y-m-d H';
		}
		
		for ($i=$minute_timepicker_start; $i <= $minute_timepicker_end; $i=$i+$time_i) {
			$timeline[]= date($timeFormat , $i);
		}		
		$r =$this->redis;
		$type = array('ip', 'newUv', 'pv', 'uv');	
		//echo $minute_timepicker_start,'----------' ,$minute_timepicker_end;
		//echo '<pre>';print_r($timeline);die;		
		foreach ($timeline as $time) {
			$mk = strtotime(str_replace('/', ':', $time));
			//$hm=trim(strstr(str_replace('/', ':', $time),' '));;
			//$count[$hm]['time']= $hm;
			$count[]['time']=$time;
			foreach ($type as $key => $value) {
				//echo '--'.$key_2. $time . ":total", "$value";die;
				$last_visit = $r -> HGET( $key_2. $time . ":total", "$value");
				if (!$last_visit) {
					$last_visit = "0";
				}
				$count[count($count)-1][$value] = $last_visit;
			}
		}	
		//echo '333<pre>';print_r($count);die;		
		return $count;
	}
	function get_compare_day_statistics( $minute_timepicker_start, $minute_timepicker_end,$direct='l' ) {		
		$time_range =array();
		$count = array();
		$start_time_u = strtotime($minute_timepicker_start);
		$end_time_u = strtotime($minute_timepicker_end);		
		for ($i=$start_time_u; $i <= $end_time_u; $i=$i+3600*24) {
			$timeline[]= date("Y-m-d" , $i);
		}		
		$r =$this->redis;
		$type = array('ip', 'newUv', 'pv', 'uv');	
		//echo '<pre>';print_r($minute_timepicker_start);die;		
		foreach ($timeline as $time) {
			$mk 	= strtotime(str_replace('/', ':', $time));
			$hm		= trim(strstr(str_replace('/', ':', $time),' '));
			$count[$hm]['time']= $time;
			$count[$hm][$direct.'_time']=$time;
			foreach ($type as $key => $value) {
				$last_visit = $r -> HGET($this -> redis_key_name . ":day:" . $time . ":total", "$value");
				if (!$last_visit) {
					$last_visit = "0";
				}
				$count[$hm][$direct.'_'.$value] = $last_visit;
			}
		}	
		//echo '<pre>';print_r($count);die;		
		return $count;
	}

	//计算平流量今天的除外
	function get_avg_statistics() {
		$totle = 0;
		$Comparison_value = 0;
		$r = redisLink();
		$keys = $r -> keys($this -> redis_key_name . ':day:*:total');
		sort($keys);
		//var_dump($keys);
		if (empty($keys)) {
			$totle = 0;
			return $totle;
		}

		$keys_nu = count($keys);
		if ($keys_nu !=1) {
			//array_pop($keys);
		}
		$types = array('ip', 'newUv', 'pv', 'uv');
		$count = array();
		foreach ($types as $type) {

			foreach ($keys as $index => $value) {
				$hvd = $r -> HGET("$value", "$type");
				// echo $key;
				// echo $value;
				// var_dump($hvd);
				if (!$hvd) {
					$hvd = "0";
				}
				if (empty($count[$type])) {
					$count[$type] = $hvd;
				}
				$count[$type] = $count[$type] + round($hvd);
			}
			$count[$type] = round($count[$type]/$keys_nu);
		}
		//var_dump($count);
		$totle = $count;
		return $totle;
	}

	function get_all_site_today_statistics( $site_name ) {
		$totle = 0;
		$today = $this -> now();
		//var_dump($today);exit;
		$r = redisLink();
		//var_dump($r);
		$type = array('ip', 'newUv', 'pv', 'uv');

		$r = redisLink();
		foreach ($type as $key => $value) {
			$today_visit = $r -> HGET($site_name . ":day:" . $today . ":total", "$value");
			//var_dump($this->redis_key_name.":day:" . $today . ":total", "$value");exit;
			if (!$today_visit) {
				$today_visit = "0";
			}
			$count[$value] = $today_visit;
		}

		return $count;
	}

	function get_all_site_avg_statistics( $site_name ) {
		$totle = 0;
		$Comparison_value = 0;
		$r = redisLink();
		$keys = $r -> keys($site_name . ':day:*:total');
		sort($keys);
		//var_dump($keys);
		if (empty($keys)) {
			$totle = 0;
			return $totle;
		}

		$keys_nu = count($keys);
		if ($keys_nu !=1) {
			//array_pop($keys);
		}
		$types = array('ip', 'newUv', 'pv', 'uv');
		$count = array();
		foreach ($types as $type) {

			foreach ($keys as $index => $value) {
				$hvd = $r -> HGET("$value", "$type");
				// echo $key;
				// echo $value;
				// var_dump($hvd);
				if (!$hvd) {
					$hvd = "0";
				}
				if (empty($count[$type])) {
					$count[$type] = $hvd;
				}
				$count[$type] = $count[$type] + round($hvd);
			}
			$count[$type] = round($count[$type]/$keys_nu);
		}
		//var_dump($count);
		$totle = $count;
		return $totle;
	}

	//计算历史高记录
	function get_h_max_statistics() {

		$totle = 0;
		$Comparison_value = 0;
		$r = redisLink();
		$keys = $r -> keys($this -> redis_key_name . ':day:*:total');

		$keys_nu = count($keys);
		if ($keys_nu == 0) {
			$totle = 0;
			return $totle;
		}
		$type = array('ip', 'newUv', 'pv', 'uv');
		$at_time = array();
		$totle = array();
		$count = array();
		foreach ($keys as $key) {

			foreach ($type as $index => $value) {
				//echo $value;
				$hvd = $r -> HGET("$key", "$value");
				preg_match("/(?<=\:)[\d-]+(?=\:)/", $key, $matched);
				
				if (!$hvd) {
					$hvd = "0";
				}
				if (empty($count[$value])) {
					$count[$value] = $hvd;
					$at_time[$value] = $matched[0];
				}
				if ($count[$value] >= $hvd) {
					$count[$value] = $count[$value];
					$at_time[$value] = $at_time[$value];
				} else {
					$count[$value] = $hvd;
					$at_time[$value] = $matched[0];
				}
			}
		}

		foreach ($type as $key => $value) {
			$totle[$value] = $count[$value] . "|" . $at_time[$value];
		}
		//var_dump($totle);
		return $totle;
	}

	function get_totle_statistics() {

		$totle = 0;
		$Comparison_value = 0;
		$r = redisLink();
		$keys = $r -> keys($this -> redis_key_name . ':day:*:total');
		if (empty($keys)) {
			$totle = 0;
			return $totle;
		}
		$type = array('ip', 'newUv', 'pv', 'uv');
		$totle = array();
		foreach ($keys as $key) {

			foreach ($type as $index => $value) {
				//echo $value;
				$hvd = $r -> HGET("$key", "$value");
				if (!$hvd) {
					$hvd = "0";
				}
				if (empty($count[$value])) {
					$count[$value] = 0;
				}
				$count[$value] = $count[$value] + round($hvd);
			}
		}

		$totle = $count;
		return $totle;
	}

	function get_browser_totle_statistics() {
		$browser_type = array('Chrome', 'Firefox', 'IE', 'Opera', 'Safari', 'other');
		
		$totle = 0;
		$Comparison_value = 0;
		$r = redisLink();
		$totle = array(); 

		unset($array_null);
		$array_null = array();
		foreach ($browser_type as $browser_key => $browser_value) {
			$keys = $r -> keys($this -> redis_key_name . ':day:*:brower:'.$browser_value.'*');
			if( count($keys) == 0 ){
				if (empty ($array_null)){
					$array_null =  array( $browser_value => 0);
				} else {
					$array_null = $array_null + array( $browser_value => 0 );
				}
				$totle = $array_null;
			} else {
				foreach ($keys as $key) {
						//echo $key.'<br />';
						$hvd = $r -> HGET("$key", "uv");
						if (!$hvd) {
							$hvd = "0";
						}
						if (empty($array_null) || empty ($array_null[$browser_value])){
							$array_null[$browser_value] = $hvd;
						} else {
							$array_null[$browser_value] =  $array_null[$browser_value]+$hvd ;
						}
						$totle = $array_null;
				}
			}
		}
		arsort($totle);
		return $totle;
	}

	//获取浏览器分类比例
	function get_all_browser_date_range_statistics( $start_time ,$end_time ) {
		$time_range =array();
		$count = array();
		$start_time_u = strtotime($start_time);
		$end_time_u = strtotime($end_time);
		
		for ($i=$start_time_u; $i <= $end_time_u; $i=$i+60*60*24) {
			$time_range[]= date("Y-m-d" , $i);
		}

		$browser_type = array('Chrome', 'Firefox', 'IE', 'Opera', 'Safari', 'other');
		
		$r = redisLink();
		$totle = array(); 

		foreach ($time_range as $time_key => $time_value) {
			$time = strtotime($time_value);
			foreach ($browser_type as $browser_key => $browser_value) {
				$keys = $r -> keys($this -> redis_key_name . ':day:'.$time_value.':brower:'.$browser_value.'*');
				unset($array_null);
				$array_null =array();
				if( count($keys) == 0 ){
					continue;
				} else {
					foreach ($keys as $key) {
							preg_match('/(?:.*:brower:(.*))/s', $key, $resolution);
							
							$hvd = $r -> HGET("$key", "uv");
							if (!$hvd) {
								$hvd = "0";
							}
							if (empty($array_null) || empty ($array_null[$resolution[1]])){
								$array_null[$resolution[1]] = $hvd;
							} else {
								$array_null[$resolution[1]] =  $array_null[$resolution[1]]+$hvd ;
							}
							
					}
				}
				if (empty($totle) || empty ($totle[$browser_value])){
					$totle[$browser_value]= $array_null;
				} else {
					$totle[$browser_value]= $totle[$browser_value]+$array_null;
				}
			}
		}
		return $totle;
	}
	
	//获取浏览器比例
	function get_browser_date_range_statistics( $start_time ,$end_time ) {
		$time_range =array();
		$count = array();
		$start_time_u = strtotime($start_time);
		$end_time_u = strtotime($end_time);
		
		for ($i=$start_time_u; $i <= $end_time_u; $i=$i+60*60*24) {
			$time_range[]= date("Y-m-d" , $i);
		}

		$browser_type = array('Chrome', 'Firefox', 'IE', 'Opera', 'Safari', 'other');
		
		$totle = 0;
		$Comparison_value = 0;
		$r = redisLink();
		$totle = array(); 

		foreach ($time_range as $time_key => $time_value) {
			$time = strtotime($time_value);
			unset($array_null);
			$array_null = array();
			foreach ($browser_type as $browser_key => $browser_value) {
				$keys = $r -> keys($this -> redis_key_name . ':day:'.$time_value.':brower:'.$browser_value.'*');
				//var_dump($keys);
				if( count($keys) == 0 ){
					if (empty ($array_null)){
						$array_null =  array( $browser_value => 0);
					} else {
						$array_null = $array_null + array( $browser_value => 0 );
					}
					$totle[$time] = $array_null;
				} else {
					foreach ($keys as $key) {
							//echo $key.'<br />';
							$hvd = $r -> HGET("$key", "uv");
							if (!$hvd) {
								$hvd = "0";
							}
							if (empty($array_null) || empty ($array_null[$browser_value])){
								$array_null[$browser_value] = $hvd;
							} else {
								$array_null[$browser_value] =  $array_null[$browser_value]+$hvd ;
							}
							$totle[$time] = $array_null;
					}
				}
			}
		}
		//var_dump($totle);exit;
		return $totle;
	}

	function get_browser_all_date_range_statistics( $start_time ,$end_time ) {
		$time_range =array();
		$count = array();
		$start_time_u = strtotime($start_time);
		$end_time_u = strtotime($end_time);
		
		for ($i=$start_time_u; $i <= $end_time_u; $i=$i+60*60*24) {
			$time_range[]= date("Y-m-d" , $i);
		}

		$browser_type = array('Chrome', 'Firefox', 'IE', 'Opera', 'Safari', 'other');
		
		$r = redisLink();

		foreach ($time_range as $time_key => $time_value) {
			foreach ($browser_type as $browser_key => $browser_value) {
				$keys = $r -> keys($this -> redis_key_name . ':day:'.$time_value.':brower:'.$browser_value.'*');
				//var_dump($keys);
				if( count($keys) == 0 ){
					continue;
				} else {
					foreach ($keys as $key) {
							//echo $key.'<br />';
							$hvd = $r -> HGETALL("$key");	
							foreach ($hvd as $hvd_key => $hvd_value) {
								if (empty($count[$browser_value][$hvd_key])) {
									$count[$browser_value][$hvd_key]=$hvd_value;
								} else {
									$count[$browser_value][$hvd_key]=$count[$browser_value][$hvd_key] + $hvd_value;
								}
							}
					}
				}
			}
		}
		return $count;
	}

	function get_resolution_totle_statistics() {
		
		$totle = 0;
		$Comparison_value = 0;
		$r = redisLink();
		
	    $keys = $r -> keys($this -> redis_key_name . ':day:*:screen:*');

        if (empty($keys)) {
			$totle = 0;
			return $totle;
		}
		$type = array('uv');
		$totle = array();
		foreach ($keys as $key) {
			preg_match('/(?:.*screen:(.*))/s', $key , $resolution);
			foreach ($type as $index => $value) {
				$hvd = $r -> HGET("$key", "$value");
				if (!$hvd) {
					$hvd = "0";
				}
				if (empty($count[$resolution[1]])) {
					$count[$resolution[1]] = $hvd;
				} else {
					$count[$resolution[1]] = $count[$resolution[1]] + $hvd;
				}
				
			}
	}
		//arsort($count);
		//$count = array_slice($count, 0, 8);//取出考前的 9 种分辨率
		//var_dump($count);exit;
		$totle = $count;
		return $totle;
	}

	function get_country_date_range_statistics( $start_time ,$end_time ) {
		$time_range =array();
		$count = array();
		$start_time_u = strtotime($start_time);
		$end_time_u = strtotime($end_time);
		
		for ($i=$start_time_u; $i <= $end_time_u; $i=$i+60*60*24) {
			$time_range[]= date("Y-m-d" , $i);
		}

		$r = redisLink();
		$country = array();
		foreach ($time_range as $time_key => $time_value) {
			$keys = $r -> keys($this -> redis_key_name . ':day:'.$time_value.':lang:*');
			foreach ($keys as $key => $value) {
				preg_match('/(?:.*:lang:(.*))/s', $value , $country_name);
				array_push($country ,$country_name[1]);
			}
		}
		$country = array_unique($country);
		natcasesort($country);

		foreach ($time_range as $time_key => $time_value) {
			foreach ($country as $country_key => $country_value) {
				$key = $this -> redis_key_name . ':day:'.$time_value.':lang:'.$country_value;
				$hvd = $r -> HGETALL("$key");

				if( count($hvd) == 0 ){
					continue;
				} else {
					foreach ($hvd as $hvd_key => $hvd_value) {
						if (empty($count[$country_value][$hvd_key])) {
							$count[$country_value][$hvd_key]=$hvd_value;
						} else {
							$count[$country_value][$hvd_key]=$count[$country_value][$hvd_key] + $hvd_value;
						}
					}
				}
			}
		}
		return $count;
	}

	function search_engine_match( $data ,$array_keywords ) {
		$array_search_engine = array();
		foreach ($data as $data_key => $data_value) {
			foreach ($array_keywords as $value) {
				if (strstr($data_key, $value) !== false)
				$array_search_engine = $array_search_engine + array ($data_key => $data[$data_key]['uv']);
			}
		}
		return $array_search_engine;
	}

	function search_engine_categories_match( $data ,$array_keywords ) {
		$array_search_engine = array();
		foreach ($data as $data_key => $data_value) {
			foreach ($array_keywords as $value) {
				if (strstr($data_key, $value) !== false){
					switch ($value) {
						case 'www.google.':
							$key_name = 'Google';
							break;
						case '.bing.com':
							$key_name = 'Bing';
							break;						
						case '.search.yahoo.com':
							$key_name = 'Yahoo';
							break;
						case 'ask.com':
							$key_name = 'Ask';
							break;
						case '.search.aol.com':
							$key_name = 'AOL';
							break;	
						case 'yandex.':
							$key_name = 'yandex';
							break;																				
					}
					if(empty($array_search_engine)||empty($array_search_engine[$key_name])){
						$array_search_engine[$key_name] = $data[$data_key]['uv'];
					} else {
						$array_search_engine[$key_name] = $array_search_engine[$key_name] + $data[$data_key]['uv'];
					}
				}
			}
		}
		return $array_search_engine;
	}

/**
	 * 获取页面refer信息，来路域名，来路关键词等等
	 * 目前处理google yahoo bing search aol yandex ask
	 */
	function getRefer($referer){
		//$referer = !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
		if(!empty($referer)){//跳转进入页面
			$referInfo = array();
			$hostName = '';
			$referer_param = parse_url($referer);
			$referHost = $referer_param['host'];
			$referQuery = !empty($referer_param['query']) ? $referer_param['query'] : '' ;
			if(!empty($referHost)){
				$referInfo['host'] = $referHost;
				$domain = array('com','cn','name','org','net');
				$domainReg = implode('|', $domain);
				if(preg_match('/(?<![\w-])([\w-]+)(?=\.'.$domainReg.')(?:.*)$/', $referHost, $match)){
					$referInfo['hostname'] = $match['1'];
					$hostName = $match['1'];
				}
			}
		} else{//直接输入地址进入页面
			return true;
		}
	}

	function get_totle_custom_traffic_sources_date_range_statistics( $start_time, $end_time, $value) {
		if($value =='')
		return null;
		$str = explode('->|<-', $value);
		$macth_type = $str['0'];
		$macth_value = $str['1'];
		if($str['0']== 'regular')
		$macth_regular = $str['2'];
		$filter_type = $macth_type;
		$filter_name = $macth_value;		
		
		$time_range =array();
		$count = array();
		$start_time_u = strtotime($start_time);
		$end_time_u = strtotime($end_time);
		
		for ($i=$start_time_u; $i <= $end_time_u; $i=$i+60*60*24) {
			$time_range[]= date("Y-m-d" , $i);
		}

		$r = redisLink();
		$referer = array();
		foreach ($time_range as $time_key => $time_value) {
			$keys = $r -> keys($this -> redis_key_name . ':day:'.$time_value.':referer:*'.$filter_name.'*');
			//var_dump($this -> redis_key_name . ':day:'.$time_value.':referer:*'.$filter_name.'*');
			foreach ($keys as $key => $value) {
				preg_match('/(?:.*:referer:(.*))/s', $value , $referer_name);
				array_push($referer ,$referer_name['1']);
			}
		}
		$referer = array_unique($referer);
		natcasesort($referer);
		foreach ($time_range as $time_key => $time_value) {
			foreach ($referer as $referer_key => $referer_value) {
				$key = $this -> redis_key_name . ':day:'.$time_value.':referer:'.$referer_value;
				$hvd = $r -> HGETALL("$key");
				if( count($hvd) == 0 ){
					continue;
				} else {
					switch ($filter_type) {
						case 'contain':
							foreach ($hvd as $hvd_key => $hvd_value) {
								if (empty($count[$referer_value][$hvd_key])) {
									$count[$referer_value][$hvd_key]=$hvd_value;
								} else {
									$count[$referer_value][$hvd_key]=$count[$referer_value][$hvd_key] + $hvd_value;
								}
							}
							break;
						case 'regular':
							preg_match($macth_regular, $referer_value, $match);
							if(!$match)
							continue;
							//preg_match("/([a-z0-9][a-z0-9\-]*?\.(?:com|edu|cn|net|org|gov|info|la|cc|co|tv|travel|coop|biz|pro|or|museum|mobi|[a-z][a-z])(?:\.(?:[a-z][a-z]))?)$/i", $referer_value, $match);
							foreach ($hvd as $hvd_key => $hvd_value) {
								if (empty($count[$match['1']][$hvd_key])) {
									$count[$match['1']][$hvd_key]=$hvd_value;
								} else {
									$count[$match['1']][$hvd_key]=$count[$match['1']][$hvd_key] + $hvd_value;
								}
							}												
							break;
					}

				}
			}
		}
		return $count;
	}

	function get_custom_traffic_sources_date_range_statistics( $start_time, $end_time, $value) {
		if($value =='')
		return null;
		$str = explode('->|<-', $value);
		$macth_type = $str['0'];
		$macth_value = $str['1'];
		if($str['0']== 'regular')
		$macth_regular = $str['2'];
		$filter_type = $macth_type;
		$filter_name = $macth_value;
		
		$time_range =array();
		$count = array();
		$start_time_u = strtotime($start_time);
		$end_time_u = strtotime($end_time);
		
		for ($i=$start_time_u; $i <= $end_time_u; $i=$i+60*60*24) {
			$time_range[]= date("Y-m-d" , $i);
		}

		$r = redisLink();
		$referer = array();

		foreach ($time_range as $time_key => $time_value) {
			$keys = $r -> keys($this -> redis_key_name . ':day:'.$time_value.':referer:*'.$filter_name.'*');
			foreach ($keys as $key => $value) {
				preg_match('/(?:.*:referer:(.*))/s', $value , $referer_name);
				array_push($referer ,$referer_name['1']);
			}
		}
		$referer = array_unique($referer);
		natcasesort($referer);
		foreach ($time_range as $time_key => $time_value) {
			foreach ($referer as $referer_key => $referer_value) {
				$key = $this -> redis_key_name . ':day:'.$time_value.':referer:'.$referer_value;
				$hvd = $r -> HGETALL("$key");
				$time = strtotime($time_value);
				$type = array('pv', 'ip', 'uv', 'newUv');

				switch ($filter_type) {
					case 'contain':
						foreach ($type as $value) {
							if( empty($hvd) ){
								if (empty($count)||empty($count[$time][$value])) {
									$count[$time][$value] = 0;
								} else {
									$count[$time][$value] = $count[$time][$value] + 0;
								}
							} else {
								if (empty($count)||empty($count[$time][$value])) {
									$count[$time][$value] = $hvd[$value];
								} else {
									$count[$time][$value] = $count[$time][$value] + $hvd[$value];
								}
							}
						}
						break;
					case 'regular':
						preg_match($macth_regular, $referer_value, $match);
						if(!$match)
						continue;
						//preg_match("/([a-z0-9][a-z0-9\-]*?\.(?:com|edu|cn|net|org|gov|info|la|cc|co|tv|travel|coop|biz|pro|or|museum|mobi|[a-z][a-z])(?:\.(?:[a-z][a-z]))?)$/i", $referer_value, $match);
						foreach ($type as $value) {
							if( empty($hvd) ){
								if (empty($count)||empty($count[$time][$value])) {
									$count[$time][$value] = 0;
								} else {
									$count[$time][$value] = $count[$time][$value] + 0;
								}
							} else {
								if (empty($count)||empty($count[$time][$value])) {
									$count[$time][$value] = $hvd[$value];
								} else {
									$count[$time][$value] = $count[$time][$value] + $hvd[$value];
								}
							}
						}												
						break;
				}
				
			}
		};
		return $count;
	}

	function get_referer_date_range_statistics( $start_time ,$end_time, $se_type) {
		$time_range =array();
		$count = array();
		$start_time_u = strtotime($start_time);
		$end_time_u = strtotime($end_time);
		
		for ($i=$start_time_u; $i <= $end_time_u; $i=$i+60*60*24) {
			$time_range[]= date("Y-m-d" , $i);
		}

		$r = redisLink();
		$referer = array();
		
		foreach ($time_range as $time_key => $time_value) {
			foreach ($se_type as $se_name) {
				$keys = $r -> keys($this -> redis_key_name . ':day:'.$time_value.':referer:*'.$se_name.'*');
				foreach ($keys as $key => $value) {
					preg_match('/(?:.*:referer:(.*))/s', $value , $referer_name);
					array_push($referer ,$referer_name['1']);
				}
			}
		}
	
		$referer = array_unique($referer);
		natcasesort($referer);
		
		foreach ($time_range as $time_key => $time_value) {
			foreach ($referer as $referer_key => $referer_value) {
				$key = $this -> redis_key_name . ':day:'.$time_value.':referer:'.$referer_value;
				$hvd = $r -> HGETALL("$key");

				if( count($hvd) == 0 ){
					continue;
				} else {
					foreach ($hvd as $hvd_key => $hvd_value) {
						if (empty($count[$referer_value][$hvd_key])) {
							$count[$referer_value][$hvd_key]=$hvd_value;
						} else {
							$count[$referer_value][$hvd_key]=$count[$referer_value][$hvd_key] + $hvd_value;
						}
					}
				}
			}
		}
		return $count;
	}

	function get_resolution_date_range_statistics( $start_time ,$end_time ) {
		$time_range =array();
		$count = array();
		$start_time_u = strtotime($start_time);
		$end_time_u = strtotime($end_time);
		
		for ($i=$start_time_u; $i <= $end_time_u; $i=$i+60*60*24) {
			$time_range[]= date("Y-m-d" , $i);
		}

		$r = redisLink();
		$resolution_type = array();
		foreach ($time_range as $time_key => $time_value) {
			$keys = $r -> keys($this -> redis_key_name . ':day:'.$time_value.':screen:*');
			foreach ($keys as $key => $value) {
				preg_match('/(?:.*screen:(.*))/s', $value , $resolution);
				array_push($resolution_type ,$resolution[1]);
			}
		}
		
		$resolution_type = array_unique($resolution_type);
		natcasesort($resolution_type);

		foreach ($time_range as $time_key => $time_value) {
			
			foreach ($resolution_type as $resolution_key => $resolution_value) {
				$key = $this -> redis_key_name . ':day:'.$time_value.':screen:'.$resolution_value;

				$hvd = $r -> HGET("$key", "uv");
				if (!$hvd) {
					$hvd = "0";
				}
				if (empty($array_null) || empty ($array_null[$resolution_value])){
					$array_null[$resolution_value] = $hvd;
				} else {
					$array_null[$resolution_value] =  $array_null[$resolution_value]+$hvd ;
				}
			}
		}
		arsort($array_null);
		$totle = array_slice($array_null, 0, 9);//取出靠前分辨率
		//var_dump($totle);exit;
		$this -> generate_resolution_pieanddonut( $totle );
	}

	function get_date_range_statistics($start_time ,$end_time) {
		$r =  $this->redis;
		$time_range =array();
		$count = array();
		$start_time_u = strtotime($start_time);
		$end_time_u = strtotime($end_time);		
		for ($i=$start_time_u; $i <= $end_time_u; $i=$i+3600*24) {
			$time_range[]= date("Y-m-d" , $i);
		}		
		//var_dump($time_range);		
		$totle = 0;
		$Comparison_value = 0;		
		$type = array('pv', 'ip', 'uv', 'newUv');
		foreach ($time_range as $day) {
            //HVALS all:day:2011-11-30:total
            $time = strtotime($day);
			//echo $this -> redis_key_name . ':day:'.$day.':total';die;
			$hvd = $r -> HVALS($this -> redis_key_name . ':day:'.$day.':total');
			//var_dump($hvd);exit;
			foreach ($type as $key => $value) {					
				if(empty($hvd[$key])){
					$hvd[$key] = 0;
				}
				$type_array[$value] = $hvd[$key];				
			}
			$type_array['time'] = date('y-m-j',$time);
			$count[$time] = $type_array;
		}		
		return $count;	
	}

	function get_day_range_timeonsite_statistics($start_time ,$end_time) {
		$time_range =array();
		$count = array();
		$start_time_u = strtotime($start_time);
		$end_time_u = strtotime($end_time)+3600*24;
			
		for ($i=$start_time_u; $i < $end_time_u; $i=$i+60*60) {
			$time_range[]= date("Y-m-d H" , $i);
		}
		
		$array_null = array();
		$r = redisLink();
 		
		foreach ($time_range as $hour ) {
			$hvd = $r -> HGET($this -> redis_key_name . ':hour:'.$hour.':total', 'timeOnSite');
			$time = date("Y-m-d", strtotime($hour.':00'));
			$time = strtotime($time);
			if (!$hvd) {
				if (empty ($array_null[$time]) || empty ($array_null)){
					$array_null[$time] = 0 ;
				} else {
					$array_null[$time] = $array_null[$time] + 0;
				}
			} else {
				if (empty ($array_null[$time]) || empty ($array_null)){
					$array_null[$time] = $hvd ;
				} else {
					$array_null[$time] = $array_null[$time] + $hvd;
				}
			}
		}
		$totle = $array_null;
		return $totle;
	}

	function dyadic_array_sum($f ,$s) {
		foreach ($f as $f_key => $f_value) {
			$f[$f_key] = $f[$f_key] + array( 'timeOnSite'=> $s[$f_key] );
		}
		return $f;
	}

	function get_hour_range_statistics($start_time ,$end_time) {
		$r =  $this->redis;
		$time_range =array();
		$count = array();
		$start_time_u = strtotime($start_time);
		$end_time_u = strtotime($end_time);		
		for ($i=$start_time_u; $i <= $end_time_u; $i=$i+3600) {
			$time_range[]= date("Y-m-d H" , $i);
		}	
		$totle = 0;
		$Comparison_value = 0;		
		$type = array('pv', 'ip', 'uv', 'newUv');
		foreach ($time_range as $day) {
            $time = strtotime($day.':00');
			//echo $this -> redis_key_name . ':hour:'.$day.':total';die;
			$hvd = $r ->HVALS($this -> redis_key_name . ':hour:'.$day.':total');
			foreach ($type as $key => $value) {
				if(empty($hvd[$key])){
					$hvd[$key] = 0;
				}
				$type_array[$value] = $hvd[$key];
			}
			$type_array['time'] = date('y-m-j H:i',$time);
			$type_array['time2'] = date('H:i',$time);
			$count[$time] = $type_array;
		}
		//echo '<pre>';print_r($count);
		return $count;
	}
	function getHourStatisticsByRedis($start_time ,$end_time) {
		$r =  $this->redis;
		$time_range =array();
		$count = array();
		for ($i=$start_time; $i <= $end_time; $i=$i+3600) {
			$time_range[]= date("Y-m-d H" , $i);
		}	
		$totle = 0;
		$Comparison_value = 0;		
		$type = array('pv', 'ip', 'uv', 'newUv');
		foreach ($time_range as $day) {
            $time = strtotime($day.':00');
			$hvd = $r ->HVALS($this -> redis_key_name . ':hour:'.$day.':total');
			foreach ($type as $key => $value) {
				if(empty($hvd[$key])){
					$hvd[$key] = 0;
				}
				$type_array[$value] = $hvd[$key];
			}
			$type_array['time'] = date('H:i',$time);
			$count[$time] = $type_array;
		}
		//echo '<pre>';print_r($time_range);die;
		return $count;
	}

	//获得每日平均，历史高，累计访问数据
	function get_avg_max_totle_statistics($type) {

		$totle = 0;
		$Comparison_value = 0;
		//var_dump($today);
		$r = redisLink();
		$time_line = $r -> keys('*analysis:history:date:*');
		$count_days = count($time_line);
		foreach ($time_line as $key) {
			$key_value[] = str_replace('analysis:history:date:', '', $key);
		}
		foreach ($key_value as $today) {

			$today_all_date = $r -> hGetAll("analysis:history:" . "$type" . ":" . "$today" . ":");

			foreach ($today_all_date as $row) {
				if ($row >= $Comparison_value) {
					$Comparison_value = $row;
				}
				$count = $row;
				$totle = $count + $totle;
			}
		}
		$avg = round($totle / $count_days);
		return $avg . '#' . $Comparison_value . '#' . $totle;
	}

	//获得今天 以及 今天这个时候的统计数据
	function get_today_statistics() {
		$totle = 0;
		$today = $this -> now();
		//var_dump($today);exit;
		$r = redisLink();
		//var_dump($r);
		$type = array('ip', 'newUv', 'pv', 'uv');

		$r = redisLink();
		foreach ($type as $key => $value) {
			$today_visit = $r -> HGET($this -> redis_key_name . ":day:" . $today . ":total", "$value");
			//var_dump($this->redis_key_name.":day:" . $today . ":total", "$value");exit;
			if (!$today_visit) {
				$today_visit = "0";
			}
			$count[$value] = $today_visit;
		}

		return $count;
	}
	//获得当前这一分钟的统计数据
	function get_nowM_statistics() {
		$nowM = date('Y-m-d H/i',time()-60);
		//$nowM='2013-06-20 01/02';
		$r = $this->redis;
		$type = array('ip', 'newUv', 'pv', 'uv');
		//$mk = strtotime(str_replace('/', ':', $nowM));
		foreach ($type as $value) {			
			//echo $this -> redis_key_name . ":minutes:" . $nowM . ":total", "$value";die;
			$today_visit = $r -> HGET($this -> redis_key_name . ":minutes:" . $nowM . ":total", "$value");
			if (!$today_visit) {
				$today_visit = "0";
			}
			$count[$value] = $today_visit;			
		}
		$count['time']= trim(strstr(str_replace('/', ':', $nowM),' '));
		return $count;
	}
	function get_yesterday_statistics() {
		$totle = 0;
		$yesterday = $this -> yesterday();
		$r = redisLink();
		//var_dump($r);
		$type = array('ip', 'newUv', 'pv', 'uv');

		$r = redisLink();
		foreach ($type as $key => $value) {
			$yesterday_visit = $r -> HGET($this -> redis_key_name . ":day:" . $yesterday . ":total", "$value");
			if (!$yesterday_visit) {
				$yesterday_visit = "0";
			}
			$count[$value] = $yesterday_visit;
		}

		return $count;
	}

	function combine_arr($a, $b) //用于比较数据的时候 长度不相等的数据截取
		{ 
		    $acount = count($a); 
		    $bcount = count($b); 
		    $size = ($acount > $bcount) ? $bcount : $acount; 
		    $a = array_slice($a, 0, $size); 
		    $b = array_slice($b, 0, $size); 
		    return array_combine($a, $b); 
		}

}

/**
 * 
 */
class User {

	function isLoggedIn() {
		if (isset($_COOKIE['auth'])) {
			return TRUE;
		} else {
			return false;
		}

	}

	function RemoveCookieLive($name) {
		unset($_COOKIE[$name]);
		return setcookie($name, NULL, -1);
	}

}

class Rule {
	function addtrafficRule( $name, $rule) {
		$r = redisLink();
		$result = $r -> hSetNx('setting:traffic_custom_rules', $name, $rule);
		return $result;
	}
	
	function gettrafficRule($hkey) {
		$r = redisLink();
		$result = $r -> hGet('setting:traffic_custom_rules', $hkey);
		return $result;
	}
	
	function getalltrafficRule() {
		$r = redisLink();
		$result = $r -> hGetAll('setting:traffic_custom_rules');
		return $result;
	}

	function removetrafficRule($hkey) {
		$r = redisLink();
		$result = $r -> HDEL('setting:traffic_custom_rules', $hkey);
		return $result;
	}
}

?>