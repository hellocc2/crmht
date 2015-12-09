<?php
namespace Helper;
/**
 * 公共函数类
 */
class String {	
	/* 过滤HTML代码的
	* @para string or array $string
	* 
	* @return string or array
	*/
	public static function dhtmlspecialchars($string) {
		if (is_array ( $string )) {
			foreach ( $string as $key => $val ) {
				$string [$key] = Self::dhtmlspecialchars ( $val );
			}
		} else {
			$string = preg_replace ( '/&amp;((#(\d{3,5}|x[a-fA-F0-9]{4})|[a-zA-Z][a-z0-9]{2,5});)/', '&\\1', str_replace ( array ('&', '"', '<', '>' ), array ('&amp;', '&quot;', '&lt;', '&gt;' ), $string ) );
		}
		return $string;
	}
	public static function slashes($string) {
		if (is_array ( $string )) {
			foreach ( $string as $key => $val ) {
				$string [$key] = self::slashes ( $val );
			}
		} else {
			$string = stripslashes ( $string );
		}
		return $string;
	}
	
	/**
	 * 获取GA pageview字符串
	 * Jerry Yang
	 */
	public static function get_ga_pageview() {
		$rewriteDir = array ('zh-cn' => 'cn', 'en-uk' => 'en', 'ja-jp' => 'jp', 'zh-hk' => 'hk', 'fr-fr' => 'fr', 'es-sp' => 'es', 'de-ge' => 'de', 'it-it' => 'it', 'ru-ru' => 'ru', 'pt-pt' => 'pt', 'ar-ar' => 'ar' );
		if (strstr ( $_SERVER ['REQUEST_URI'], "/" . $rewriteDir [SELLER_LANG] . "/" )) {
			$ga_path_temp = $_SERVER ['REQUEST_URI'];
		} else {
			$ga_path_temp = "/" . $rewriteDir [SELLER_LANG] . $_SERVER ['REQUEST_URI'];
		}
		return $ga_path_temp;
	}
	/**
	 * 处理接口返回数组生成ga字符串
	 * Jerry Yang
	 */
	public static function cat_ga_custom_var($ar,$be_remarket=0) {
	
		if($be_remarket==0) {//列表页的GA
			if (! is_array ( $ar )){	return '';	}
			if (! isset ( $ar ['categoryId'] )) {	return '';	}
			$temp_str= $ar ['categoryId'];
			if (isset( $ar ['nextCategory'] )) {
				return $temp_str.','.self::cat_ga_custom_var ( $ar ['nextCategory'] ,0);
			}
			return $temp_str;
		}else if($be_remarket==2){//详细页的GA
			if (! is_array ( $ar )){	return ''; }
			if (! isset ( $ar ['categoryId'] )) {		return '';		}
			$temp_str= $ar ['categoryId'];
			if (isset( $ar ['parentProductCategory'] )) {
				return $temp_str.','.self::cat_ga_custom_var ( $ar ['parentProductCategory'] ,2);
			}
			return $temp_str;
		}else if($be_remarket==1){//再营销
			static $parent_categories;
			if (! is_array ( $ar )){	return ''; }
			if (! isset ( $ar ['categoryId'] )) {		return '';		}
			$parent_categories[]= $ar ['categoryId'];
			if (isset( $ar ['parentProductCategory'] )) {
				self::cat_ga_custom_var ( $ar ['parentProductCategory'] ,1);
			}
			return $parent_categories;
		}
	}
	/**
	 * 获取GA自定义字符串
	 * Jerry Yang
	 */
	public static function get_ga_custom_var($action, $CategoriesId, $page = 1,$pid=0) {
	
		$rewriteDir = array ('zh-cn' => 'cn', 'en-uk' => 'en', 'ja-jp' => 'jp', 'zh-hk' => 'hk', 'fr-fr' => 'fr', 'es-sp' => 'es', 'de-ge' => 'de', 'it-it' => 'it', 'ru-ru' => 'ru', 'pt-pt' => 'pt', 'ar-ar' => 'ar' );
		$class_tree_assoc = $ga_custom_var = '';
		if ($action == 'glist') {
			$parentC=self::cat_ga_custom_var($CategoriesId,0);
			$categories=explode(',',$parentC);
			
			if(!empty($categories)){
				$i=0;
				$ga_custom_var = '/' . $rewriteDir [SELLER_LANG];
				foreach($categories as $v){
					$ga_custom_var .='/c'.++$i.'-'.$v;
				}
				$ga_custom_var .= '#' . $page;
			}
			if (empty ( $ga_custom_var )) {
				$ga_custom_var = self::get_ga_pageview ();
			}
		} else if ($action == 'item') {
			$parentC=self::cat_ga_custom_var($CategoriesId,2);
			$categories=explode(',',$parentC);
			
			if(!empty($categories)){
				krsort($categories);
				$i=0;
				$ga_custom_var = '/' . $rewriteDir [SELLER_LANG];
				foreach($categories as $v){
					$ga_custom_var .='/c'.++$i.'-'.$v;
				}
				$ga_custom_var .='/p'.$pid;
			}
			if (empty ( $ga_custom_var )) {				$ga_custom_var = self::get_ga_pageview ();			}
			
		} else {
			$ga_custom_var = self::get_ga_pageview ();
		}
		if (strlen ( $ga_custom_var ) > 50) {
			$ga_custom_var = substr ( $ga_custom_var, 0, 50 );
		}
		return $ga_custom_var;
	}
	/**
	 * 获取再营销字符串
	 * Jerry Yang
	 */
	public static function get_remarket_code($parentCategories,$type=0){
		$Remarket_code = '';
		include CONFIG_PATH.'google_Remarketing.php';
		if($type==120){
			$RemarketingArray = $Remarketing_120[SELLER_LANG];
		}else if($type==30){
			$RemarketingArray = $Remarketing_30[SELLER_LANG];
		}else{
			$RemarketingArray = $Remarketing[SELLER_LANG];
		}
		if (!empty($RemarketingArray)) {
			foreach ($parentCategories as $value0) {
				if(isset($RemarketingArray[$value0])){
					$Remarket_code=$RemarketingArray[$value0]['code'];
				}
			}
		}
		return $Remarket_code;
	} 
	
	/**
	 * 将字符串中的双引号和单引号转成对应的html实体
	 * @param unknown_type $string
	 * @author Su Chao<suchaoabc@163.com>
	 */
	public static function escQuotes($string) {
		return str_replace ( "'", '&#039;', str_replace ( '"', '&quot;', $string ) );
	}
	
	/**
	 * 将数组转成将INI格式的字符串.
	 * @param  array $arr 关联数组.不超过三维.
	 */
	public static function arrayToIni($arr) {
		$str = '';
		foreach ( $arr as $k => $v ) {
			if (is_array ( $v ) || is_object ( $v )) {
				$str .= "[$k]\n";
				foreach ( $v as $kSub => $vSub ) {
					if (is_array ( $vSub ) || is_object ( $vSub )) {
						foreach ( $vSub as $kSub2 => $vSub2 ) {
							$str .= "{$kSub}[] = \"" . $vSub2 . "\"\n";
						}
					} else if (is_string ( $vSub ) || is_numeric ( $vSub )) {
						$str .= "$kSub = \"$vSub\"\n";
					}
				}
			} else if (is_string ( $v ) || is_numeric ( $v )) {
				$str .= "$k = \"$v\"\n";
			}
		}
		return $str;
	}
	
	
	/**
	 * 
	 * 去掉数组中值的反斜杠并转换实体
	 */
	public static function strDosTrip($value){
		if (is_array ( $value )) {
			$value = array_map ( 'self::strDosTrip', $value );
		} else {
			$value = stripslashes ( $value );
			$value = htmlspecialchars_decode ( $value );
		}
		return $value;
	}
	
	public static function numberFormat($string,$CurrencyC='',$lang=''){
		if(empty($CurrencyC)){
			$CurrencyC = CurrencyCode;
		}
		if($CurrencyC=='JPY'){
			return str_replace(',','，',number_format($string));
		}
		else{
			if(empty($lang)){
				$lang = SELLER_LANG;
			}
			switch($lang){
				case 'de-ge':
					return number_format($string,2,",",".");
				break;
				case 'fr-fr':
					return number_format($string,2,",",".");
				break;
				case 'es-sp':
					return number_format($string,2,",",".");
				break;
				case 'it-it':
					return number_format($string,2,",",".");
				break;
				case 'ru-ru':
					if($string > 9999){
						return number_format($string,2,","," ");
					}else{
						return number_format($string,2,",","");
					}
				break;
				default:
					return number_format($string,2);
			}
		}
	}
}