<?php
namespace Lib\_3rd\cyberSource;

/**
 * 
 * Enter description here ...
 * @author Jiang Lin<Jiang Lin>
 *
 */

class CyberLib {
	
	
	public static function runTransaction($request) {
		try {
			$client = new \Lib\_3rd\cyberSource\ExtendedClient(dirname(__FILE__)."/CyberSourceTransaction_1.59.wsdl",array());
			$request->merchantID = 'milanoocom';
			return $client->runTransaction($request);
		} catch(Exception $e) {
			
		}
		
	}
}