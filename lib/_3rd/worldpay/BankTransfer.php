<?php
namespace Lib\_3rd\worldpay;
class BankTransfer {
	var $merchantCode = 'MILANOOUSD';
	var $merchantPassword = 'Cu8rUyeD';

	var $xml;

	var $orderId;
	var $totalammount;
	var $shopperDetails;
	var $description;

	function BankTransferstart($debug) {
		$this->debug = $debug;
		if($this->debug)
		$this->url = "https://" . $this->merchantCode . ":" . $this->merchantPassword . "@secure-test.worldpay.com/jsp/merchant/xml/paymentService.jsp";
		else
		$this->url = "https://" . $this->merchantCode . ":" . $this->merchantPassword . "@secure.worldpay.com/jsp/merchant/xml/paymentService.jsp";
	}

	function CreateConnection() {
		$ch = curl_init ($this->url);
		curl_setopt($ch, CURLOPT_POST,1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $this->xml); //$xml is the xml string
		curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type: text/xml"));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_NOPROGRESS, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);  
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		// echo "ch: $ch<HR>" ;

		$result = curl_exec ($ch); // result will contain XML reply from Bibit
		curl_close ($ch);
		if ( $result == false )
		print "Curl could not retrieve page '$this->url', curl_exec returns false";
		//print_r($result);exit;
		return $result;
	}
	
	function CreateConnection1() {
		
		$ch = curl_init ($this->url);
		
		curl_setopt($ch, CURLOPT_POST,1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $this->xml); //$xml is the xml string
		curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type: text/xml"));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_NOPROGRESS, 0);
		curl_setopt($ch, CURLOPT_COOKIEJAR, "fengxi.cookie");
		curl_setopt($ch, CURLOPT_COOKIEFILE, "fengxi.cookie");

		// echo "ch: $ch<HR>" ;

		$result = curl_exec ($ch); // result will contain XML reply from Bibit
		curl_close ($ch);
		if ( $result == false )
		print "Curl could not retrieve page '$this->url', curl_exec returns false";
		return $result;
	}


	function StartXML() {
		$this->xml = <<<EOT
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE paymentService PUBLIC "-//WorldPay/DTD WorldPay PaymentService v1//EN" "http://dtd.worldpay.com/paymentService_v1.dtd">
<paymentService version='1.4' merchantCode='{$this->merchantCode}'>
  <submit>
    <order orderCode = '{$this->orderId}' installationId='284984'>
      <description>{$this->description}</description>
      <amount value='{$this->totalammount}' currencyCode = '{$this->currencyCode}' exponent = '2'/>\n
EOT;
}

function FillDataXML($invoiceData) {
	$this->xml .= <<<EOT
      <orderContent>
        <![CDATA[{$invoiceData}]]>
      </orderContent>\n
EOT;
}

function FillBankXml($countryCode){
	$this->xml .= <<<EOT
      <paymentMethodMask>
		<include code="TRANSFER_{$countryCode}-BANK"/>
	  </paymentMethodMask>\n
EOT;
}

function FillShopperXML($shopperArray) {
	$this->xml .= <<<EOT
      <shopper>
        <shopperEmailAddress>{$shopperArray['email']}</shopperEmailAddress>
      </shopper>
      <shippingAddress>
        <address>
          <firstName>{$shopperArray['firstname']}</firstName>
          <lastName>{$shopperArray['lastname']}</lastName>
          <street>{$shopperArray['street']}</street>
          <postalCode>{$shopperArray['postalcode']}</postalCode>
          <city>{$shopperArray['city']}</city>
          <countryCode>{$shopperArray['countrycode']}</countryCode>
          <telephoneNumber>{$shopperArray['telephone']}</telephoneNumber>
        </address>
      </shippingAddress>\n
EOT;
}



function EndXML() {
	$this->xml .= <<<EOT
    </order>
  </submit>
</paymentService>
EOT;
}
}