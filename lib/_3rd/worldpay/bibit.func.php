<?
function StartElement($parser, $name, $attrs)
{
	global $resultArray;
	$resultArray['currentTag'] = $name;

	switch ($name) {
		case "ERROR":
			$resultArray['errorcode'] = $attrs['CODE']; //example of how to catch the error code number (i.e. 1 to 7)
			// $url_error = "error_order.php";
			break;
		case "REFERENCE":
			$resultArray['referenceID'] = $attrs['ID'];//for storage in your own database
			break;
		case "ORDERSTATUS":
			$resultArray['ordercode'] = $attrs['ORDERCODE'];
			break;
		default:
			break;
	}
}

function EndElement($parser, $name) {
	global $resultArray;
	$resultArray['currentTag'] = "";
}

function CharacterData($parser, $result) {
	global $resultArray;
	switch ($resultArray['currentTag']) {
		case "REFERENCE":
			//there is a REFERENCE so there must be an url which was provided by bibit for the actual payment. echo $result;
			$resultArray['url_togoto'] = $result;
			break;
		default:
			break;
	}
}

function ParseXML($bibitResult) {
	$xml_parser = xml_parser_create();
	// set callback functions
	xml_set_element_handler($xml_parser, "startElement", "endElement");
	xml_set_character_data_handler($xml_parser, "characterData");
	if (!xml_parse($xml_parser, $bibitResult))
	{
		die(sprintf("XML error: %s at line %d",
		xml_error_string(xml_get_error_code($xml_parser)),
		xml_get_current_line_number($xml_parser)));
	}
	// clean up
	xml_parser_free($xml_parser);
}

function ReadXml($xml,$getElement,$attr='',$type='string',$elementtype='tagName'){
	/**
 函数返回字符串.
 参数说明  : (1) $xml - xml文档 ,可以是字符串(4)$type='string' ,或者xml文件路径 (4)$type='filepath'
		    (2) $getElement - 要获取的元素名, 可以是 tagName(标签名) (5)$elementtype='tagName'  或 id(ID) (5)$elementtype='id'
			(3) $attr 属性
**/	
	$dom = new DOMDocument();
	if($type=='string'){
		$dom->loadXML($xml);
	}else{
		$dom->load($xml);
	}
	$xpath = new DOMXPath($dom);
	if($elementtype=='tagName'){
		$nodelist = $xpath->query("//{$getElement}");
	}else{
		$nodelist = $dom->getElementById($getElement);
	}
	foreach($nodelist as $v){
		if($attr==''){
			return $v->nodeValue;
		}else{
			if($v->getAttribute($attr)){
				return $v->getAttribute($attr);
			}
		}
	}
}
function RedirectXykURL($ReplyArray,$yxkUrl){
	$paRequest=ReadXml($ReplyArray,"paRequest");
	$issuerURL=ReadXml($ReplyArray,"issuerURL");
	$echoData=ReadXml($ReplyArray,"echoData");
	$htmltext='<html>
		<head>
		<title>3-D Secure helper page</title>
		</head>
		<body OnLoad="OnLoadEvent();" style="display:none;">
		<form name="theForm" method="POST" action="'.$issuerURL.'" >
		<input type="hidden" name="PaReq" value="'.$paRequest.'" />
		<input type="hidden" name="TermUrl" value="'.$yxkUrl.'" />
		<input type="hidden" name="MD" value="'.$echoData.'" />
		<input type="submit" name="Send" />
		</form>
		<script language="Javascript">
		<!--
		function OnLoadEvent()
		{
			document.theForm.submit();
		}
		// -->
		</script>
		</body>
		</html>';
	return $htmltext;
}
?>