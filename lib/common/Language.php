<?php
namespace Lib\common;
/**
 * 多语言相关处理类
 * @author Su Chao<suchaoabc@163.com>
 * @use \Helper\RequestUtil
 */
class Language{
    /**
     * @var 客户端语种代码映射,参考\config\Language.php 中的$client_lang_to_web_lang
     */
    private static $langMap;   
    private static $exchangeRate; 
    /**
     * 设置语种相关的初始化参数,包括cookie及session
     */
    public static function setLang() 
    {
    		$rewriteDir=array(
    		//'zh-cn'=>'cn',
    		'en-uk'=>'en',
    		'ja-jp'=>'jp',
    		'fr-fr'=>'fr',
    		'es-sp'=>'es',
    		'de-ge'=>'de',
    		'it-it'=>'it',
    		'pt-pt'=>'pt',
    		'ru-ru'=>'ru',
    		'zh-hk'=>'hk',
    		'cn-cn'=>'cn',
    		'en'=>'en-uk',
    		'jp'=>'ja-jp',
    		'ja'=>'ja-jp',
    		'fr'=>'fr-fr',
    		'es'=>'es-sp',
    		'de'=>'de-ge',
    		'it'=>'it-it',
    		'ru'=>'ru-ru',
    		'hk'=>'zh-hk',
    		'cn'=>'cn-cn',
    		'pt'=>'pt-pt',
    		);
    		
            if(isset($_COOKIE['lang_cookie']))
    		{
    		   $SELLER_LANG=$_COOKIE['lang_cookie'];
    		}
    		else 
    		{
    		    $SELLER_LANG='';
    		}
    		$path = \Helper\RequestUtil::getUrl();
    		$path=explode('/', $path);
    		if($path[3]=='ja') $rewriteDir['ja-jp']="ja";
    		
    		if(($path[3]=='jp' || $path[3]=='ja' || $path[3]=="en" || $path[3]=='fr' || $path[3]=='es' || $path[3]=='de' || $path[3]=='it' || $path[3]=='pt' || $path[3]=='ru' || $path[3]=='hk'|| $path[3]=='ar'|| $path[3]=='cn'))  
    		{
    		    $SELLER_LANG=$rewriteDir[$path[3]];   
    		}
    		elseif(isset($_GET['adlang'])) $SELLER_LANG=$_GET['adlang'];

            if(empty($SELLER_LANG))
            {
            	 if(!self::$langMap)
        	    {
                    self::$langMap = \config\Language::$client_lang_to_web_lang;
        	    }    
        	    $SELLER_LANG = self::$langMap[self::getClientLang()];            
              
            }
            
            //后台服务器访问需求增加
            if(!empty($_GET['lang'])){
            	$SELLER_LANG = $_GET['lang'];
            }

    		if (!defined('SELLER_LANG')) define('SELLER_LANG', $SELLER_LANG);
    		if (!defined('LangDirName'))
    		{
    			/**
    			 * URL中的语种名称目录
    			 * @var string
    			 */
    			define('LangDirName', $rewriteDir[$SELLER_LANG]);
    		}
    		
			if (!defined('CurrencyCode'))
			{	
        		if((!isset($_COOKIE['lang_cookie']) || $_COOKIE['lang_cookie'] != $SELLER_LANG) || (isset($_COOKIE['CurrencyCode']) && ($_COOKIE['CurrencyCode']=='RMB')) ||!isset($_COOKIE['CurrencyCode']))
        		{//当语种变化时, 或者币种的cookie不存在,或者为RMB时, 则重新根据语种进行币种设置
        			switch(SELLER_LANG){
        				case 'ja-jp': 
        					$CurrencyC='JPY';
        					break;
        				case 'ru-ru':
        					$CurrencyC='RUB';
        					break;
        				case 'fr-fr':
        				case 'es-sp':
        				case 'de-ge':
        				case 'it-it':
        				case 'pt-pt':
        					$CurrencyC='EUR';
        					break;
        				default:
        					if(isset($_SERVER['HTTP_X_REAL_COUNTRY']) && $_SERVER['HTTP_X_REAL_COUNTRY'] == 'GB')
	        				{//当来IP自英国时,默认货币使用英镑
	        					$CurrencyC='GBP';
	        				}
	        				else
	        				{
	        					$CurrencyC='USD';
	        				}
        					break;
        			}			
        		}
        		else if(isset($_COOKIE['CurrencyCode']))
        		{
        		    $CurrencyC = $_COOKIE['CurrencyCode'];
        		}
        		//日语站只使用日元
        		if(SELLER_LANG=='ja-jp') $CurrencyC='JPY';
        		if(!isset($CurrencyC))
        		{
        		    $CurrencyC = 'USD';
        		}

	    		if(!isset($_COOKIE['CurrencyCode']) || $_COOKIE['CurrencyCode'] != $CurrencyC)
    		    {        			    			
			    	setcookie("CurrencyCode", $CurrencyC,(time()+(24*3600*365*10)),'/');
			    	$_COOKIE['CurrencyCode'] = $CurrencyC;
    		    }
				
				define('CurrencyCode',  $CurrencyC);
				define('Currency',\config\Currency::$currencyTranslations[CurrencyCode]['Currency']);				
			}			
    		
			if(!isset($_COOKIE['lang_cookie']) || $_COOKIE['lang_cookie'] != $SELLER_LANG)
			{
				setcookie("lang_cookie", $SELLER_LANG,(time()+(24*3600*365*10)),'/');
				$_COOKIE['lang_cookie'] = $SELLER_LANG;
			}
						
    		if($_SERVER['REQUEST_URI']=='/en' || $_SERVER['REQUEST_URI']=='/en/')
    		{
    	 		 header('HTTP/1.1 302 Moved Temporarily');//发出302头部 
    	 		 header('Location:http://'.$_SERVER["HTTP_HOST"]);
    		}
    }   
    
	/**
	 * 获取客户端所使用的语言
	 */
	public static function getClientLang()
	{
	    if(!self::$langMap)
	    {
            self::$langMap = \config\Language::$client_lang_to_web_lang;
	    }
	    if(!isset($_SERVER['HTTP_ACCEPT_LANGUAGE']))
	    {
	        $_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'en-uk';
	    }
		$clientLangPos = strpos($_SERVER['HTTP_ACCEPT_LANGUAGE'], ',');
		if(!$clientLangPos)
		{
			$clientLangPos = strpos($_SERVER['HTTP_ACCEPT_LANGUAGE'], ';');
		}
		if(!$clientLangPos)
		{
			$clientLang = trim($_SERVER['HTTP_ACCEPT_LANGUAGE']);
		}
		else 
		{
			$clientLang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'],0,$clientLangPos);
		}	
		$clientLang = strtolower($clientLang);
		if(!key_exists($clientLang, self::$langMap))
		{
			$clientLang = 'en-uk';
		}		
		return $clientLang;    
	}    
	
	/**
	 * 获取以人民币为基准的汇率.<br />汇率缓存为一天
	 */
	public static function getExchangeRate()
	{
	    if(self::$exchangeRate)
	    {
	       return self::$exchangeRate; 
	    }    
	    else 
	    {
	    	$rate =new \Model\Rate();
	    	$rateList = $rate->getRate();
	    	$ExchangeRate = array();
	    	if (is_array($rateList['exchangeRateArr'])) {
	    		foreach($rateList['exchangeRateArr'] as $rateData) {
	    			$ExchangeRate[$rateData['currency']] = $rateData['exRate'];
	    		}
	    	}
	        self::$exchangeRate = $ExchangeRate;
	    }
	    return self::$exchangeRate;
	}
	
	/**
	 * 加载当前语种的语言包对象.当指定的语言包不存在时则加载en-uk语言包,如果en-uk也不存在则抛出致命错误
	 * @param string $langCode 语种代码.当不传入语种代码时,默认为SELLER_LANG定义的语种. 当不为SELLER_LANG时,则将自动附加语种命名空间,并返回对应的语言包.<br />
	 * 如: loadLangPack('ja-jp');//ja-jp不等于SELLER_LANG,将生成并载入命名空间 \Lang\ja_jp\下的LangPack. 并返回LangPack::$items.
	 * @uses SELLER_LANG
	 */
	public static function loadLangPack($langCode=null)
	{
		if(is_null($langCode))
		{
			$langCode = SELLER_LANG;
		}
		
		if($langCode != SELLER_LANG)
		{
			$langPackNamespace = 'Lang\\'.str_replace('-', '_', $langCode);
			$langPackName = '\\'.$langPackNamespace.'\LangPack';
		}
		else
		{
			$langPackName = 'LangPack';
		}
	    if(class_exists($langPackName,false))
	    {
	    	return $langPackName::$items;
	    }	
	     
		$langPath = LANG_ROOT . DIRECTORY_SEPARATOR . $langCode . DIRECTORY_SEPARATOR . 'Lang.php';
	    if(!file_exists($langPath))
	    {
	        $langPath = LANG_ROOT.DIRECTORY_SEPARATOR.'en-uk'.DIRECTORY_SEPARATOR.'Lang.php';    
	    }
	    if(!file_exists($langPath))
	    {
	    	return array();
	    }
	    if($langCode != SELLER_LANG)
	    {
	    	$langClassStr = file_get_contents($langPath);
	    	$langClassStr = ltrim($langClassStr);
	    	$langClassStr = preg_replace('#^<\?php(\n|\r\n|\r)*#', "namespace ".$langPackNamespace.";\n", $langClassStr,1);
	    	eval($langClassStr);	    	
	    }
	    else
	    {
	    	require_once $langPath;
	    }	  
	    return $langPackName::$items;
	}
	
	/**
	 * 通过货币汇率进行价格转换
	 * @param int|float|array $prices 价格.当为数组时对其包含price的对象字段进行转换
	 * @param string $currencyTo 目标货币,如:JPY.如果留空,则转换则当前页面时候的币种
	 * @param string $currencyFrom 源货币.如:USD(默认)
	 * @param boolean $firstTimeCall 是否为递归的第一次调用.不要使用此参数.
	 */
	public static function priceByCurrency($prices,$currencyTo=null,$currencyFrom='USD',$firstTimeCall=true)
	{
	    static $exchangeRate;
	    $decimal = $currencyTo == 'JPY' ? 0 : 2 ;
	    if($firstTimeCall)
	    {
	        $exchangeRate = self::getExchangeRate();
	        if(empty($currencyTo))
	        {
	           $currencyTo = CurrencyCode; 
	        }
	    	if(!key_exists($currencyFrom, $exchangeRate) || !key_exists($currencyTo, $exchangeRate))
    	    {
    	        error_log('没有货币'.$currencyFrom.'至'.$currencyTo.'的转换率.');
    	        return null;
    	    }	
    	    if($currencyFrom == $currencyTo) {
    	    	$prices = round($prices,$decimal);
    	    	return $prices;  
    	    }
	    }
	   
        if(is_numeric($prices))
        {
            $price = $prices * ($exchangeRate[$currencyFrom]/$exchangeRate[$currencyTo]);      
            $price = round($price,$decimal);
            return $price;
        }
        else if(is_array($prices))
        {
            foreach ($prices as $k=>$v)
            {
                if(is_array($v) || (is_numeric($v) && stripos($k, 'price') !== false))
                {
                    $prices[$k] = self::priceByCurrency($v, $currencyTo,$currencyFrom,false);
                }
            }
            return $prices;
        }
        else 
        {
            return null;
        }
	    
	}
	
	/**
	 * 切换货币
	 * @param string $currency 传入需要切换的货币
	 */
	public static function changeCurrency($currency){
		$bast_url	= isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:ROOT_URLD;
		$currency=trim($currency);
		if($currency)
		{
			setcookie('CurrencyCode', $currency,(time()+2592000),'/');
			$_SESSION["CurrencyCodeT"]=$currency;
		}
		if ($_SESSION[SESSION_PREFIX . "MemberType"]=='Distributors')
		{
			setcookie('CurrencyCode', 'RMB',(time()+2592000),'/');
			$_SESSION["CurrencyCodeT"]='RMB';
		}
		header("Location:".$bast_url);
		exit;
	}
	
	public static function changeLang($lang){
		$bast_url	= isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:ROOT_URLD;
		$lang = trim($lang);
		if($lang){
			if(isset($_COOKIE['lang_cookie'])){
					
			}
			setcookie("lang_cookie", $lang,(time()+(24*3600*365*10)),'/');
			$_COOKIE['lang_cookie'] = $lang;
		}
		header("Location:".$bast_url);
		exit;
	}
}