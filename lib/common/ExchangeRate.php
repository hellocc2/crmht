<?php
namespace Lib\common;
class ExchangeRate
{
    /**
     * 通过webservice 获取人民币至其它货币的汇率
     */
    public static function getExchangeRate()
    {
        set_time_limit(0);
        $wsdl = 'http://www.webservicex.net/CurrencyConvertor.asmx/ConversionRate?ToCurrency=CNY&FromCurrency=';
        $currency = array(	
                    	'GBP'=>'10.326',
                    	'HKD'=>'0.8276',
                    	'USD'=>'6.4307',
                    	'CHF'=>'7.2607',
                    	'SGD'=>'5.0986',
                    	'SEK'=>'1.0327',
                    	'DKK'=>'1.245',
                    	'NOK'=>'1.1838',
                    	'JPY'=>'0.077503',
                    	'CAD'=>'6.5768',
                    	'AUD'=>'6.7829',
                    	'EUR'=>'9.283',
                    	'NZD'=>'5.1382',
                    	'RUB'=>'0.2361',
                    	'MXN'=>'0.5486',
                        );
        foreach ($currency as $k => $v)
        {
            $rate = file_get_contents($wsdl.$k);
            if(false !== $rate)
            {
                $rate = simplexml_load_string($rate);
                if(false !== $rate && ($rate = (float)$rate) > 0)
                {                    
                    $currency[$k] = $rate;
                }
            }   
        } 
        $currency['CNY'] = $currency['RMB'] = 1;
        return $currency;                       
    }
    
 
}