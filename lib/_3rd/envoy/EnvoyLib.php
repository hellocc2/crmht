<?php
namespace Lib\_3rd\envoy;
/**
 * 
 * Enter description here ...
 * @author Jiang Lin<Jiang Lin>
 *
 */
class EnvoyLib {
	
	private $Authentication;
	
	private $client;
	public function __construct() {
		$this->Authentication = new \stdClass();
		$this->Authentication->username = 'dingshaoning@milanoo.cn';
		$this->Authentication->password = 'milanoo123';
				
		$this->client = new \SoapClient(dirname(__FILE__)."/MerchantAPI_live.wsdl", array('cache_wsdl' => WSDL_CACHE_NONE));
	}
	
	public function payInConfirmation($request) {
		//构造request
		$request->auth = $this->Authentication;
		
		$response = $this->client->payInConfirmation($request);
		
		return $response;
	}
	
	public function oneClickPaymentRequest($request) {
		//构造request
		$request->auth = $this->Authentication;
		
		$response = $this->client->oneClickPaymentRequest($request);
		
		return $response;
	}
}

?>
