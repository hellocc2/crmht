<?php
namespace config;
/**
 * 语种相关的配置
 */
class Language{
	public static $nu_lang = array ('1' => '中文','2' => '英语', '3' => '日语', '4' => '法语', '5' => '西班牙语', '6' => '德语', '7' => '意语', '8' => '俄语', '9' => '葡萄牙语' );
	
	public static $acceptLang = array ('EN' => 'en-uk', 'JP' => 'ja-jp', 'FR' => 'fr-fr', 'ES' => 'es-sp', 'DE' => 'de-ge', 'IT' => 'it-it', 'RU' => 'ru-ru', 'PT' => 'pt-pt' );
    
	public static $admin_Language=array(
    	array('name'=>'简体','lang'=>'zh-cn','theme'=>'default','CurrencyCode'=>'RMB'),
    	array('name'=>'英文','lang'=>'en-uk','theme'=>'default','CurrencyCode'=>'USD'),
    	array('name'=>'日文','lang'=>'ja-jp','theme'=>'default','CurrencyCode'=>'JPY'),
    	array('name'=>'繁体中文','lang'=>'zh-hk','theme'=>'default','CurrencyCode'=>'HKD'),
    	array('name'=>'中文','lang'=>'cn-cn','theme'=>'default','CurrencyCode'=>'RMB'),
    	array('name'=>'法文','lang'=>'fr-fr','theme'=>'default','CurrencyCode'=>'EUR'),
    	array('name'=>'西班牙文','lang'=>'es-sp','theme'=>'default','CurrencyCode'=>'EUR'),
    	array('name'=>'德文','lang'=>'de-ge','theme'=>'default','CurrencyCode'=>'EUR'),
    	array('name'=>'意大利文','lang'=>'it-it','theme'=>'default','CurrencyCode'=>'EUR'),
    	array('name'=>'葡萄牙文','lang'=>'pt-pt','theme'=>'default','CurrencyCode'=>'EUR'),
    	array('name'=>'俄文','lang'=>'ru-ru','theme'=>'default','CurrencyCode'=>'RUB'),
    	array('name'=>'阿拉伯文','lang'=>'ar-ar','theme'=>'default','CurrencyCode'=>'USD'),
    );
    
    public static $web_Language=array(
    	array('name'=>'简体','lang'=>'zh-cn','theme'=>'default','CurrencyCode'=>'RMB','CountryCode'=>'12'),
    	array('name'=>'英文','lang'=>'en-uk','theme'=>'default','CurrencyCode'=>'USD','CountryCode'=>'1'),
    	array('name'=>'日文','lang'=>'ja-jp','theme'=>'default','CurrencyCode'=>'JPY','CountryCode'=>'36'),
    	array('name'=>'繁体中文','lang'=>'zh-hk','theme'=>'default','CurrencyCode'=>'HKD','CountryCode'=>'28'),
    	array('name'=>'中文','lang'=>'cn-cn','theme'=>'default','CurrencyCode'=>'RMB','CountryCode'=>'12'),
    	array('name'=>'法文','lang'=>'fr-fr','theme'=>'default','CurrencyCode'=>'EUR','CountryCode'=>'23'),
    	array('name'=>'西班牙文','lang'=>'es-sp','theme'=>'default','CurrencyCode'=>'EUR','CountryCode'=>'85'),
    	array('name'=>'德文','lang'=>'de-ge','theme'=>'default','CurrencyCode'=>'EUR','CountryCode'=>'24'),
    	array('name'=>'意大利文','lang'=>'it-it','theme'=>'default','CurrencyCode'=>'EUR','CountryCode'=>'34'),
    	array('name'=>'葡萄牙文','lang'=>'pt-pt','theme'=>'default','CurrencyCode'=>'EUR','CountryCode'=>'54'),
    	array('name'=>'俄文','lang'=>'ru-ru','theme'=>'default','CurrencyCode'=>'RUB','CountryCode'=>'58'),
    );

  
}