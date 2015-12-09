<?php
$orderInfo =  array();
if(isset($_REQUEST['customerRef'])&&!empty($_REQUEST['customerRef'])) {
	$cid = $_GET['customerRef'];
	$query = "SELECT `OrdersPay`,`OrdersId`,`OrdersMemberId`,`CurrencyCode`,`OrdersAmount`,`OrdersLogisticsCosts` FROM `" . TABLE_PREFIX . "orders` WHERE `OrdersCid`= ?";
	$sth = $db->Prepare($query);
	$res = $db->Execute($sth, array($cid));
	$orderInfo = slashes($res->FetchRow());
	$memberid=$orderInfo['OrdersMemberId'];
}
if(isset($_REQUEST['epacsReference'])&&!empty($_REQUEST['epacsReference'])) {
	$request = new stdClass();

	//ENVOY验证信息
	$Authentication = new stdClass();
	$Authentication->username = 'dingshaoning@milanoo.cn';
	$Authentication->password = 'milanoo123';

	//构造request
	$request->auth = $Authentication;
	$request->epacsReference = $_REQUEST['epacsReference'];

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

	$db = new db();
	$db = $db->get_database();
	$db_host = $db_user = $db_pass = $db_name = $db_type = NULL;

	//--END
	
	
	$handle = fopen(ROOT_PATH . 'data/envoy.txt', 'a');
	fwrite($handle, "--------start\n");
	fwrite($handle, var_export($_REQUEST,1)."\n\r");
	fwrite($handle, var_export($response,1)."\n\r");
	fwrite($handle, "--------end\n\r");
				
	fclose($handle);

	//更新数据库中的数据
	if($response->payInConfirmationResult->statusCode == 0) {
		
			$paytime = strtotime($response->payInConfirmationResult->payment->postingDate) + Time_zone;
			$OrdersPayDetails = 'Payment:yhzx|' . 'CurrencyCode:EUR|' . 'amount:' . $response->payInConfirmationResult->payment->bankAmount . '|' . 'Remarks:' . $response->payInConfirmationResult->payment->bankInformation . '|' . 'time:' . $paytime;
			$sql = "	UPDATE `" . TABLE_PREFIX . "orders` SET `OrdersPay` = ? ,`OrdersPayDetails` = ? where OrdersCid= ? ";
			$sth = $db->Prepare($sql);
			$res = $db->Execute($sth, array(1, $OrdersPayDetails, $response->payInConfirmationResult->payment->merchantReference));
			
			if($_SESSION[SESSION_PREFIX . "MemberId"]){
				header("Location:".RewriteUrl(ROOT_URL."?module=shop&action=Achieve&id=".$orderInfo['OrdersId'],1));
			}else{
				header("Location:".RewriteUrl(ROOT_URL."?module=shop&action=Achieve&id=".$orderInfo['OrdersId']."&mid=".$memberid."&md=".md5($memberid.$orderInfo['OrdersId'].MD5_pass),1));
			}
			exit;
		
	}
		


	
}

if(!empty($orderInfo)) {
	//跳转到二次支付页面
	if($orderInfo['OrdersPay']) {
		header("Location:".RewriteUrl(ROOT_URL."?module=shop&action=Achieve&id=".$orderInfo['OrdersId']."&mid=".$memberid."&md=".md5($memberid.$orderInfo['OrdersId'].MD5_pass),1));
	} else {
		header("Location:" . RewriteUrl(ROOT_URL . "?module=shop&action=failure_pay&id=" . $orderInfo['OrdersId'] . "&mid=" . $memberid . "&md=" . md5($memberid.$orderInfo['OrdersId'].MD5_pass), 1));
	}
	
} else {
	header("Location:" . ROOT_URL );
}
exit;
