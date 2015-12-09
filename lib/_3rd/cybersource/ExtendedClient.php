<?php
namespace Lib\_3rd\cyberSource;

/**
 * 
 * Enter description here ...
 * @author Jiang Lin<Jiang Lin>
 *
 */

class ExtendedClient extends \SoapClient {
	
	function __construct($wsdl, $options = null) {
		parent::__construct($wsdl, $options);
	}
	
	// This section inserts the UsernameToken information in the outgoing SOAP message.
	function __doRequest($request, $location, $action, $version) {
		
		$user = 'milanoocom';
		$password = 'uQvrmMXWs8wLJorz2LgyIvxhB78O1kUCRZFKCZrd8Zyb4hVqXD1DOpYHWy4uqng/qHDHrJiB6ORO/tBfqBIGxl1ZA8E9jvsn+c8kfoUFveo8DgkcpWmu1Z4EGnUIX6dcwTMGXpC1D1IFvUoTZtrgo0l2HdctpSGqTecjG/8RqvVuj8VZMJmYqgPGzgADBTziNzb51Z4EGnUIX6dcwTMGXpC1D1IFvUoTZtrgo0l2HdctpSGqTecjG/8RqvV1iXeDGKOiOLksaq6aeNP1Hg0DIFmNenAKelMUrQ9XKWZMgczwSOCLXjB1GeYN/qIp2Fjv0di6wl+QrIyA7ZQJHE42FA==';
		
		$soapHeader = "<SOAP-ENV:Header xmlns:SOAP-ENV=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:wsse=\"http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd\"><wsse:Security SOAP-ENV:mustUnderstand=\"1\"><wsse:UsernameToken><wsse:Username>$user</wsse:Username><wsse:Password Type=\"http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText\">$password</wsse:Password></wsse:UsernameToken></wsse:Security></SOAP-ENV:Header>";
		
		$requestDOM = new \DOMDocument('1.0');
		$soapHeaderDOM = new \DOMDocument('1.0');
		 
		try {
			
			$requestDOM->loadXML($request);
			$soapHeaderDOM->loadXML($soapHeader);
			
			$node = $requestDOM->importNode($soapHeaderDOM->firstChild, true);
			$requestDOM->firstChild->insertBefore($node, $requestDOM->firstChild->firstChild);
			
			$request = $requestDOM->saveXML();
		
		//	  printf( "Modified Request:\n*$request*\n" );
		

		} catch(DOMException $e) {
			die('Error adding UsernameToken: ' . $e->code);
		}
		
		return parent::__doRequest($request, $location, $action, $version);
	}
}