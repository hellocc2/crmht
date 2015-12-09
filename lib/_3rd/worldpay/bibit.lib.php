<?
namespace Lib\_3rd\worldpay;

class Bibit {
	var $merchantCode;
	var $merchantPassword;

	var $xml;

	var $orderId;
	var $totalammount;
	var $shopperDetails;
	var $description;

	function Bibitstart($debug) {
		$this->debug = $debug;
		if($this->debug)
		$this->url = "https://" . $this->merchantCode . ":" . $this->merchantPassword . "@secure-test.bibit.com/jsp/merchant/xml/paymentService.jsp";
		else
		$this->url = "https://" . $this->merchantCode . ":" . $this->merchantPassword . "@secure.bibit.com/jsp/merchant/xml/paymentService.jsp";
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
<?xml version='1.0' encoding='UTF-8'?>
<!DOCTYPE paymentService PUBLIC '-//Bibit//DTD Bibit PaymentService v1//EN' 'http://dtd.bibit.com/paymentService_v1.dtd'>
<paymentService version='1.4' merchantCode='{$this->merchantCode}'>
  <submit>
    <order orderCode = '{$this->orderId}'>
      <description>{$this->description}</description>
      <amount value='{$this->totalammount}' currencyCode = '{$this->currencyCode}' exponent = '2'/>\n
EOT;
}

function FillDataXML($invoiceData) {
	$this->xml .= <<<EOT
      <orderContent>
        <![CDATA[{$invoiceData}]]>
      </orderContent>
EOT;
}

function FillShopperXML($shopperArray) {
	$this->xml .= <<<EOT
      <shopper>
        <shopperEmailAddress>{$shopperArray['email']}</shopperEmailAddress>
        <browser>
	<acceptHeader>text/html</acceptHeader> <userAgentHeader>{$_SERVER['HTTP_USER_AGENT']}</userAgentHeader>
	</browser>
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

function FillPaymentVISAXML($PaymentArray,$Reply=0) {
	if($Reply==1)$info3DSecure="<info3DSecure>
<paResponse>{$PaymentArray['paResponse']}</paResponse>
</info3DSecure>";
	else $info3DSecure=null;
	$this->xml .= <<<EOT
    <paymentDetails> 
	<VISA-SSL> 
	<cardNumber>{$PaymentArray['cardNumber']}</cardNumber> 
	<expiryDate> 
	<date month="{$PaymentArray['expirymonth']}" year="{$PaymentArray['expiryyear']}"/> 
	</expiryDate> 
	<cardHolderName>{$PaymentArray['cardHolderName']}</cardHolderName>
	<cvc>{$PaymentArray['cvc']}</cvc> 
	<cardAddress>
	<address>
	<firstName>{$PaymentArray['billFirstName']}</firstName>
	<lastName>{$PaymentArray['billLastName']}</lastName>
	<street>{$PaymentArray['billStreet']}</street>
	<postalCode>{$PaymentArray['billPostalcode']}</postalCode>
	<city>{$PaymentArray['billCity']}</city>
	<countryCode>{$PaymentArray['billCountryCode']}</countryCode>
	<telephoneNumber>{$PaymentArray['billTelephoneNumber']}</telephoneNumber>
	</address>
	</cardAddress>
	</VISA-SSL>
	<session shopperIPAddress="{$PaymentArray['shopperIPAddress']}" id="{$PaymentArray['id']}"/> 
	$info3DSecure
	</paymentDetails>\n
EOT;
}

function FillPaymentAMEXXML($PaymentArray) {
	$this->xml .= <<<EOT
    <paymentDetails> 
	<AMEX-SSL> 
	<cardNumber>{$PaymentArray['cardNumber']}</cardNumber> 
	<expiryDate> 
	<date month="{$PaymentArray['expirymonth']}" year="{$PaymentArray['expiryyear']}"/> 
	</expiryDate> 
	<cardHolderName>{$PaymentArray['cardHolderName']}</cardHolderName>
	<cvc>{$PaymentArray['cvc']}</cvc>
	<cardAddress>
	<address>
	<firstName>{$PaymentArray['billFirstName']}</firstName>
	<lastName>{$PaymentArray['billLastName']}</lastName>
	<street>{$PaymentArray['billStreet']}</street>
	<postalCode>{$PaymentArray['billPostalcode']}</postalCode>
	<city>{$PaymentArray['billCity']}</city>
	<countryCode>{$PaymentArray['billCountryCode']}</countryCode>
	<telephoneNumber>{$PaymentArray['billTelephoneNumber']}</telephoneNumber>
	</address>
	</cardAddress>
	</AMEX-SSL>
	<session shopperIPAddress="{$PaymentArray['shopperIPAddress']}" id="{$PaymentArray['id']}"/> 
	</paymentDetails>\n
EOT;
}

function FillPaymentECMCXML($PaymentArray) {
	$this->xml .= <<<EOT
    <paymentDetails> 
	<ECMC-SSL> 
	<cardNumber>{$PaymentArray['cardNumber']}</cardNumber> 
	<expiryDate> 
	<date month="{$PaymentArray['expirymonth']}" year="{$PaymentArray['expiryyear']}"/> 
	</expiryDate> 
	<cardHolderName>{$PaymentArray['cardHolderName']}</cardHolderName>
	<cvc>{$PaymentArray['cvc']}</cvc>
	<cardAddress>
	<address>
	<firstName>{$PaymentArray['billFirstName']}</firstName>
	<lastName>{$PaymentArray['billLastName']}</lastName>
	<street>{$PaymentArray['billStreet']}</street>
	<postalCode>{$PaymentArray['billPostalcode']}</postalCode>
	<city>{$PaymentArray['billCity']}</city>
	<countryCode>{$PaymentArray['billCountryCode']}</countryCode>
	<telephoneNumber>{$PaymentArray['billTelephoneNumber']}</telephoneNumber>
	</address>
	</cardAddress>
	</ECMC-SSL>
	<session shopperIPAddress="{$PaymentArray['shopperIPAddress']}" id="{$PaymentArray['id']}"/> 
	</paymentDetails>\n
EOT;
}
function FillPaymentJCBXML($PaymentArray) {
	$this->xml .= <<<EOT
    <paymentDetails> 
	<JCB-SSL> 
	<cardNumber>{$PaymentArray['cardNumber']}</cardNumber> 
	<expiryDate> 
	<date month="{$PaymentArray['expirymonth']}" year="{$PaymentArray['expiryyear']}"/> 
	</expiryDate> 
	<cardHolderName>{$PaymentArray['cardHolderName']}</cardHolderName>
	<cvc>{$PaymentArray['cvc']}</cvc>
	<cardAddress>
	<address>
	<firstName>{$PaymentArray['billFirstName']}</firstName>
	<lastName>{$PaymentArray['billLastName']}</lastName>
	<street>{$PaymentArray['billStreet']}</street>
	<postalCode>{$PaymentArray['billPostalcode']}</postalCode>
	<city>{$PaymentArray['billCity']}</city>
	<countryCode>{$PaymentArray['billCountryCode']}</countryCode>
	<telephoneNumber>{$PaymentArray['billTelephoneNumber']}</telephoneNumber>
	</address>
	</cardAddress>
	</JCB-SSL>
	<session shopperIPAddress="{$PaymentArray['shopperIPAddress']}" id="{$PaymentArray['id']}"/>  
	</paymentDetails>\n
EOT;
}
function FillPaymentDISCOVERXML($PaymentArray) {
	$this->xml .= <<<EOT
    <paymentDetails> 
	<DISCOVER-SSL> 
	<cardNumber>{$PaymentArray['cardNumber']}</cardNumber> 
	<expiryDate> 
	<date month="{$PaymentArray['expirymonth']}" year="{$PaymentArray['expiryyear']}"/> 
	</expiryDate> 
	<cardHolderName>{$PaymentArray['cardHolderName']}</cardHolderName>
	<cvc>{$PaymentArray['cvc']}</cvc>
	<cardAddress>
	<address>
	<firstName>{$PaymentArray['billFirstName']}</firstName>
	<lastName>{$PaymentArray['billLastName']}</lastName>
	<street>{$PaymentArray['billStreet']}</street>
	<postalCode>{$PaymentArray['billPostalcode']}</postalCode>
	<city>{$PaymentArray['billCity']}</city>
	<countryCode>{$PaymentArray['billCountryCode']}</countryCode>
	<telephoneNumber>{$PaymentArray['billTelephoneNumber']}</telephoneNumber>
	</address>
	</cardAddress>	
	</DISCOVER-SSL>
	<session shopperIPAddress="{$PaymentArray['shopperIPAddress']}" id="{$PaymentArray['id']}"/>  
	</paymentDetails>\n
EOT;
}
function FillPaymentDINERSXML($PaymentArray) {
	$this->xml .= <<<EOT
    <paymentDetails> 
	<DINERS-SSL> 
	<cardNumber>{$PaymentArray['cardNumber']}</cardNumber> 
	<expiryDate> 
	<date month="{$PaymentArray['expirymonth']}" year="{$PaymentArray['expiryyear']}"/> 
	</expiryDate> 
	<cardHolderName>{$PaymentArray['cardHolderName']}</cardHolderName>
	<cvc>{$PaymentArray['cvc']}</cvc>
	<cardAddress>
	<address>
	<firstName>{$PaymentArray['billFirstName']}</firstName>
	<lastName>{$PaymentArray['billLastName']}</lastName>
	<street>{$PaymentArray['billStreet']}</street>
	<postalCode>{$PaymentArray['billPostalcode']}</postalCode>
	<city>{$PaymentArray['billCity']}</city>
	<countryCode>{$PaymentArray['billCountryCode']}</countryCode>
	<telephoneNumber>{$PaymentArray['billTelephoneNumber']}</telephoneNumber>
	</address>
	</cardAddress>		
	</DINERS-SSL>
	<session shopperIPAddress="{$PaymentArray['shopperIPAddress']}" id="{$PaymentArray['id']}"/>  
	</paymentDetails>\n
EOT;
}
function FillPaymentCARTEBLEUEXML($PaymentArray) {
	$this->xml .= <<<EOT
    <paymentDetails> 
	<CARTEBLEUE-SSL> 
	<cardNumber>{$PaymentArray['cardNumber']}</cardNumber> 
	<expiryDate> 
	<date month="{$PaymentArray['expirymonth']}" year="{$PaymentArray['expiryyear']}"/> 
	</expiryDate> 
	<cardHolderName>{$PaymentArray['cardHolderName']}</cardHolderName>
	<cvc>{$PaymentArray['cvc']}</cvc>
	<cardAddress>
	<address>
	<firstName>{$PaymentArray['billFirstName']}</firstName>
	<lastName>{$PaymentArray['billLastName']}</lastName>
	<street>{$PaymentArray['billStreet']}</street>
	<postalCode>{$PaymentArray['billPostalcode']}</postalCode>
	<city>{$PaymentArray['billCity']}</city>
	<countryCode>{$PaymentArray['billCountryCode']}</countryCode>
	<telephoneNumber>{$PaymentArray['billTelephoneNumber']}</telephoneNumber>
	</address>
	</cardAddress>	
	</CARTEBLEUE-SSL>
	<session shopperIPAddress="{$PaymentArray['shopperIPAddress']}" id="{$PaymentArray['id']}"/>  
	</paymentDetails>\n
EOT;
}

function FillPaymentGLOBALXML($PaymentArray,$preFix) {
	$this->xml .= <<<EOT
    <paymentDetails> 
	<{$preFix}> 
	<cardNumber>{$PaymentArray['cardNumber']}</cardNumber> 
	<expiryDate> 
	<date month="{$PaymentArray['expirymonth']}" year="{$PaymentArray['expiryyear']}"/> 
	</expiryDate> 
	<cardHolderName>{$PaymentArray['cardHolderName']}</cardHolderName>
	<cvc>{$PaymentArray['cvc']}</cvc>
	<cardAddress>
	<address>
	<firstName>{$PaymentArray['billFirstName']}</firstName>
	<lastName>{$PaymentArray['billLastName']}</lastName>
	<street>{$PaymentArray['billStreet']}</street>
	<postalCode>{$PaymentArray['billPostalcode']}</postalCode>
	<city>{$PaymentArray['billCity']}</city>
	<countryCode>{$PaymentArray['billCountryCode']}</countryCode>
	<telephoneNumber>{$PaymentArray['billTelephoneNumber']}</telephoneNumber>
	</address>
	</cardAddress>	
	</{$preFix}>
	<session shopperIPAddress="{$PaymentArray['shopperIPAddress']}" id="{$PaymentArray['id']}"/>  
	</paymentDetails>\n
EOT;
}

function FillPaymentMAESTROXML($PaymentArray) {
	$this->xml .= <<<EOT
    <paymentDetails> 
	<MAESTRO-SSL> 
	<cardNumber>{$PaymentArray['cardNumber']}</cardNumber> 
	<expiryDate> 
	<date month="{$PaymentArray['expirymonth']}" year="{$PaymentArray['expiryyear']}"/> 
	</expiryDate> 
	<cardHolderName>{$PaymentArray['cardHolderName']}</cardHolderName>
	<cvc>{$PaymentArray['cvc']}</cvc>
	<cardAddress>
	<address>
	<firstName>{$PaymentArray['billFirstName']}</firstName>
	<lastName>{$PaymentArray['billLastName']}</lastName>
	<street>{$PaymentArray['billStreet']}</street>
	<postalCode>{$PaymentArray['billPostalcode']}</postalCode>
	<city>{$PaymentArray['billCity']}</city>
	<countryCode>{$PaymentArray['billCountryCode']}</countryCode>
	<telephoneNumber>{$PaymentArray['billTelephoneNumber']}</telephoneNumber>
	</address>
	</cardAddress>	
	</MAESTRO-SSL>
	<session shopperIPAddress="{$PaymentArray['shopperIPAddress']}" id="{$PaymentArray['id']}"/>  
	</paymentDetails>\n
EOT;
}

function BrowserXML() {
	$this->xml .= <<<EOT
    <shopper>
	<browser>
	<acceptHeader>text/html</acceptHeader> <userAgentHeader>{$_SERVER['HTTP_USER_AGENT']}</userAgentHeader>
	</browser>
	</shopper>
EOT;
}
function DataXML($echoData){
	$this->xml .= <<<EOT
	<echoData>$echoData</echoData>
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
?>