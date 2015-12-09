<?php

function soaputils_autoFindSoapRequest() {
	global $HTTP_RAW_POST_DATA;
	
	if($HTTP_RAW_POST_DATA)
		return $HTTP_RAW_POST_DATA;
	
	$f = file("php://input");
	return implode(" ", $f);
	//return $f;
}

function xml2array($contents, $get_attributes = 1, $priority = 'tag') {
	if(!$contents)
		return array();
	
	if(!function_exists('xml_parser_create')) {
		//print "'xml_parser_create()' function not found!";
		return array();
	}
	
	//Get the XML parser of PHP - PHP must have this module for the parser to work
	$parser = xml_parser_create('');
	xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8"); # http://minutillo.com/steve/weblog/2004/6/17/php-xml-and-character-encodings-a-tale-of-sadness-rage-and-data-loss
	xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
	xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
	xml_parse_into_struct($parser, trim($contents), $xml_values);
	xml_parser_free($parser);
	
	if(!$xml_values)
		return; //Hmm...
	

	//Initializations
	$xml_array = array();
	$parents = array();
	$opened_tags = array();
	$arr = array();
	
	$current = &$xml_array; //Refference
	

	//Go through the tags.
	$repeated_tag_index = array(); //Multiple tags with same name will be turned into an array
	foreach($xml_values as $data) {
		unset($attributes, $value); //Remove existing values, or there will be trouble
		

		//This command will extract these variables into the foreach scope
		// tag(string), type(string), level(int), attributes(array).
		extract($data); //We could use the array by itself, but this cooler.
		

		$result = array();
		$attributes_data = array();
		
		if(isset($value)) {
			if($priority == 'tag')
				$result = $value;
			else
				$result['value'] = $value; //Put the value in a assoc array if we are in the 'Attribute' mode
		}
		
		//Set the attributes too.
		if(isset($attributes) and $get_attributes) {
			foreach($attributes as $attr => $val) {
				if($priority == 'tag')
					$attributes_data[$attr] = $val;
				else
					$result['attr'][$attr] = $val; //Set all the attributes in a array called 'attr'
			}
		}
		
		//See tag status and do the needed.
		if($type == "open") { //The starting of the tag '<tag>'
			$parent[$level - 1] = &$current;
			if(!is_array($current) or (!in_array($tag, array_keys($current)))) { //Insert New tag
				$current[$tag] = $result;
				if($attributes_data)
					$current[$tag . '_attr'] = $attributes_data;
				$repeated_tag_index[$tag . '_' . $level] = 1;
				
				$current = &$current[$tag];
			
			} else { //There was another element with the same tag name
				

				if(isset($current[$tag][0])) { //If there is a 0th element it is already an array
					$current[$tag][$repeated_tag_index[$tag . '_' . $level]] = $result;
					$repeated_tag_index[$tag . '_' . $level]++;
				} else { //This section will make the value an array if multiple tags with the same name appear together
					$current[$tag] = array($current[$tag], $result); //This will combine the existing item and the new item together to make an array
					$repeated_tag_index[$tag . '_' . $level] = 2;
					
					if(isset($current[$tag . '_attr'])) { //The attribute of the last(0th) tag must be moved as well
						$current[$tag]['0_attr'] = $current[$tag . '_attr'];
						unset($current[$tag . '_attr']);
					}
				
				}
				$last_item_index = $repeated_tag_index[$tag . '_' . $level] - 1;
				$current = &$current[$tag][$last_item_index];
			}
		
		} elseif($type == "complete") { //Tags that ends in 1 line '<tag />'
			//See if the key is already taken.
			if(!isset($current[$tag])) { //New Key
				$current[$tag] = $result;
				$repeated_tag_index[$tag . '_' . $level] = 1;
				if($priority == 'tag' and $attributes_data)
					$current[$tag . '_attr'] = $attributes_data;
			
			} else { //If taken, put all things inside a list(array)
				if(isset($current[$tag][0]) and is_array($current[$tag])) { //If it is already an array...
					

					// ...push the new element into that array.
					$current[$tag][$repeated_tag_index[$tag . '_' . $level]] = $result;
					
					if($priority == 'tag' and $get_attributes and $attributes_data) {
						$current[$tag][$repeated_tag_index[$tag . '_' . $level] . '_attr'] = $attributes_data;
					}
					$repeated_tag_index[$tag . '_' . $level]++;
				
				} else { //If it is not an array...
					$current[$tag] = array($current[$tag], $result); //...Make it an array using using the existing value and the new value
					$repeated_tag_index[$tag . '_' . $level] = 1;
					if($priority == 'tag' and $get_attributes) {
						if(isset($current[$tag . '_attr'])) { //The attribute of the last(0th) tag must be moved as well
							

							$current[$tag]['0_attr'] = $current[$tag . '_attr'];
							unset($current[$tag . '_attr']);
						}
						
						if($attributes_data) {
							$current[$tag][$repeated_tag_index[$tag . '_' . $level] . '_attr'] = $attributes_data;
						}
					}
					$repeated_tag_index[$tag . '_' . $level]++; //0 and 1 index is already taken
				}
			}
		
		} elseif($type == 'close') { //End of tag '</tag>'
			$current = &$parent[$level - 1];
		}
	}
	
	return ($xml_array);
}

$response1 =  soaputils_autoFindSoapRequest();
$data = xml2array($response1,0);

$payment = $data['soap:Envelope']['soap:Body']['PaymentNotification']['payment'];

$request = new stdClass();

//ENVOY验证信息
$Authentication = new stdClass();
$Authentication->username = 'dingshaoning@milanoo.cn';
$Authentication->password = 'milanoo123';

//构造request
$request->auth = $Authentication;
$request->epacsReference = $payment['epacsReference'];

$client = new SoapClient("MerchantAPI_live.wsdl",array());

$response = $client->payInConfirmation($request);

//引用文件链接数据库--START
session_name("milanooId");
session_start();
define('in_milanoo', true);
require_once '../../extension.inc';
require_once '../../config/config.inc.php';
require_once '../../config/b2cconfig.inc.php';
require_once '../../lib/comm/lib_main.php';
include_once LIB_PATH . 'comm/db.class.' . PHP_EX;
include_once LIB_PATH . 'comm/b2cbaseAction.class.' . PHP_EX;
$handle = fopen(ROOT_PATH .'data/param.txt', 'a');
$db = new db();
$db = $db->get_database();
$db_host = $db_user = $db_pass = $db_name = $db_type = NULL;

//--END

$query = "SELECT `OrdersId`,`OrdersMemberId`,`CurrencyCode`,`OrdersAmount`,`OrdersLogisticsCosts` FROM `" . TABLE_PREFIX . "orders` WHERE `OrdersCid`= ?";
$sth = $db->Prepare($query);
$res = $db->Execute($sth, array($response->payInConfirmationResult->payment->merchantReference));
$orderInfo = slashes($res->FetchRow());

//更新数据库中的数据
if($response->payInConfirmationResult->statusCode == 0) {
	$paytime = strtotime($response->payInConfirmationResult->payment->postingDate) + Time_zone;
	$OrdersPayDetails = 'Payment:yhzx|' . 'CurrencyCode:EUR|' . 'amount:' . $response->payInConfirmationResult->payment->bankAmount . '|' . 'Remarks:' . $response->payInConfirmationResult->payment->bankInformation . '|' . 'time:' . $paytime;
	$sql = "	UPDATE `" . TABLE_PREFIX . "orders` SET `OrdersPay` = ? ,`OrdersPayDetails` = ? where OrdersCid= ? ";
	$sth = $db->Prepare($sql);
	$res = $db->Execute($sth, array(1, $OrdersPayDetails, $response->payInConfirmationResult->payment->merchantReference));
}
fwrite($handle, "--------start\n");
fwrite($handle, var_export($response1,1)."\n\r");
fwrite($handle, var_export($response,1)."\n\r");
fwrite($handle, "--------end\n\r");
fclose($handle);

echo '<?xml version="1.0" encoding="utf-8"?>'; 
echo '<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
xmlns:xsd="http://www.w3.org/2001/XMLSchema" 
xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/"> 
  <soap:Body> 
    <PaymentNotificationResponse xmlns="http://apilistener.envoyservices.com"> 
      <PaymentNotificationResult>Success</PaymentNotificationResult> 
    </PaymentNotificationResponse> 
  </soap:Body> 
</soap:Envelope>';
