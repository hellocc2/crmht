<?php
namespace config;
class Currency{
    /**
     * 货币符号及名称翻译
     * @var array
     */
    public static $currencyTranslations = array(
	"USD"=>array('css'=>"us",'Currency'=>'US$','CurrencyCode'=>'$ USD','name'=>array('zh-cn'=>'','en-uk'=>'US Dollar','ja-jp'=>'米ドル','fr-fr'=>'US Dollar','es-sp'=>'US Dollar','de-ge'=>'US Dollar','it-it'=>'Dollaro Americano','pt-pt'=>'US Dollar','ru-ru'=>'Доллары США','ar-ar'=>'دولار أمريكي')),
	"EUR"=>array('css'=>"eu",'Currency'=>'€','CurrencyCode'=>'€ EUR','name'=>array('zh-cn'=>'','en-uk'=>'Euro','ja-jp'=>'ユーロ','fr-fr'=>'Euro','es-sp'=>'Euro','de-ge'=>'Euro','it-it'=>'Euro','pt-pt'=>'Euro','ru-ru'=>'Евро','ar-ar'=>'يورو')),
	"JPY"=>array('css'=>"jp",'Currency'=>'¥','CurrencyCode'=>'￥ YUAN','name'=>array('zh-cn'=>'','en-uk'=>'Japanese Yen','ja-jp'=>'円','fr-fr'=>'Japanese Yen','es-sp'=>'Japanese Yen','de-ge'=>'Japanese Yen','it-it'=>'Yen Giapponese','pt-pt'=>'Japanese Yen','ru-ru'=>'Японские Йены','ar-ar'=>'الين الياباني')),
	"GBP"=>array('css'=>"en",'Currency'=>'£','CurrencyCode'=>'£ GBP','name'=>array('zh-cn'=>'','en-uk'=>'GB Pound','ja-jp'=>'英ボンド','fr-fr'=>'GB Pound','es-sp'=>'GB Pound','de-ge'=>'GB Pound','it-it'=>'Sterlina','pt-pt'=>'GB Pound','ru-ru'=>'Фунты Стерлингов','ar-ar'=>'الجنيه الأسترليني')),
	"CAD"=>array('css'=>"ca",'Currency'=>'CA$','CurrencyCode'=>'$ CAD','name'=>array('zh-cn'=>'','en-uk'=>'Canadian Dollar','ja-jp'=>'カナダドル','fr-fr'=>'Canadian Dollar','es-sp'=>'Canadian Dollar','de-ge'=>'Canadian Dollar','it-it'=>'Dollaro Canadese','pt-pt'=>'Canadian Dollar','ru-ru'=>'Канадские Доллары','ar-ar'=>'الدولار الكندي')),
	"AUD"=>array('css'=>"au",'Currency'=>'AU$','CurrencyCode'=>'$ AUD','name'=>array('zh-cn'=>'','en-uk'=>'Australian Dollar','ja-jp'=>'オーストラリアドル','fr-fr'=>'Australian Dollar','es-sp'=>'Australian Dollar','de-ge'=>'Australian Dollar','it-it'=>'Dollaro Australiano','pt-pt'=>'Australian Dollar','ru-ru'=>'Австралийские Доллары','ar-ar'=>'الدولار الاسترالي')),
	"CHF"=>array('css'=>"sw",'Currency'=>'CHF','CurrencyCode'=>'CHF','name'=>array('zh-cn'=>'','en-uk'=>'Switzerland Francs','ja-jp'=>'スイス・フラン','fr-fr'=>'Switzerland Francs','es-sp'=>'Switzerland Francs','de-ge'=>'Switzerland Francs','it-it'=>'Franco Svizzero','pt-pt'=>'Switzerland Francs','ru-ru'=>'Швейцарские Франки','ar-ar'=>'الفرنك السويسري')),
	"HKD"=>array('css'=>"hk",'Currency'=>'HK$','CurrencyCode'=>'$ HKD','name'=>array('zh-cn'=>'','en-uk'=>'Hong Kong Dollars','ja-jp'=>'ＨＫドル','fr-fr'=>'Hong Kong Dollars','es-sp'=>'Hong Kong Dollars','de-ge'=>'Hong Kong Dollars','it-it'=>'Dollaro di Hong Kong','pt-pt'=>'Hong Kong Dollars','ru-ru'=>'Гонконгские Доллары','ar-ar'=>'الدولار الهونغ الكونغي')),	
 	"RUB"=>array('css'=>"ru",'Currency'=>'руб. ','CurrencyCode'=>'руб','name'=>array('zh-cn'=>'','en-uk'=>'Rouble','ja-jp'=>'卢布','fr-fr'=>'Rouble','es-sp'=>'Rouble','de-ge'=>'Rouble','it-it'=>'Rouble','pt-pt'=>'Rouble','ru-ru'=>'Рубли','ar-ar'=>'الروبل')),
 	"MXN"=>array('css'=>"mx",'Currency'=>'$MXN','CurrencyCode'=>'$ MXD','name'=>array('zh-cn'=>'','en-uk'=>'MexicanPeso','ja-jp'=>'墨西哥比索','fr-fr'=>'MexicanPeso','es-sp'=>'MexicanPeso','de-ge'=>'MexicanPeso','it-it'=>'MexicanPeso','pt-pt'=>'MexicanPeso','ru-ru'=>'MexicanPeso','ar-ar'=>'البيزو المكسيكي')),
	);
}