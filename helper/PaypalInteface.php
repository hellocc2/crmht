<?php
namespace Helper;
use helper\ResponseUtil as Rewrite;
/**
 * PAYPAL接口
 */
class PaypalInteface {
	
	
	private static $receiverEmail = 'paypal@milanoo.com';
	
	private static $paypalUrl = 'www.paypal.com';
	
	private static $paypalSandBoxUrl = 'www.sandbox.paypal.com';
	
	/**
	 * 
	 * 成功页面
	 */
	public static function getPaypalData() {
		$req = 'cmd=_notify-synch';
		
		$tx_token = $_REQUEST['tx'];
		$auth_token = "h_CYbjUsWBv4NEwyPsmb91zXIpiB7zyPN54g33F8lFoTh5TPpmSdl1CGyoG";
		$req .= "&tx=$tx_token&at=$auth_token";
		
		// post back to PayPal system to validate
		$header .= "POST /cgi-bin/webscr HTTP/1.0\r\n";
		$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
		
		$url = '';
		
		if($isSandBox === true) {
			$url = self::$paypalSandBoxUrl;
		} else {
			$url = self::$paypalUrl;
		}
		$fp = fsockopen($url, 80, $errno, $errstr, 30);
		
		if($fp) {
			fputs($fp, $header . $req);
			// read the body data
			$res = '';
			$headerdone = false;
			while(!feof($fp)) {
				$line = fgets($fp, 1024);
				if(strcmp($line, "\r\n") == 0) {
					// read the header
					$headerdone = true;
				} else if($headerdone) {
					// header has been read. now read the contents
					$res .= $line;
				}
			}
			
			// parse the data
			$lines = explode("\n", $res);
			
			$keyarray = array();
			if(strcmp($lines[0], "SUCCESS") == 0) {
				for($i = 1; $i < count($lines); $i++) {
					list($key, $val) = explode("=", $lines[$i]);
					$keyarray[urldecode($key)] = urldecode($val);
				}
				
				$invoice = $keyarray['invoice'];
				$mc_currency = $keyarray['mc_currency'];
				$payment_gross = $keyarray['mc_gross'];
				$custom = $keyarray['custom'];
				$mc_fee = $keyarray['mc_fee'];
				$receiver_email = urldecode($keyarray['receiver_email']);
				$payment_status = strtoupper($keyarray['payment_status']);
				$invoice_array = explode("_", $invoice);
				if($payment_status == 'COMPLETED' && $receiver_email == self::$receiverEmail) {
					$paytime = time();
					$OrdersPayDetails = 'Payment:paypal|' . 'CurrencyCode:' . $mc_currency . '|' . 'amount:' . $payment_gross . '|' . 'REFERENCEID:' . $tx_token . '|' . 'Remarks:' . $Remarks . '|' . 'time:' . $paytime;
					
					$shoppingProcess = new \Model\ShoppingProcess();
					$lang = strtolower(substr(trim($invoice), 0, 5));
					$orderInfoFromCid = $shoppingProcess->GetOrderByCid(array('cr.ordersCid'=>$invoice,'cr.lang'=>$lang));
					$orderFromCid = $orderInfoFromCid['orderInfo']['order'];
					$response = $shoppingProcess->updateOrder(array('cr.ordersCid' => $invoice, 'cr.ordersPay' => 1, 'cr.ordersPayDetails' => $OrdersPayDetails, 'cr.cardType' => 'Paypal', 'cr.pamentToken' => $tx_token, 'cr.payTime' => $paytime, 'cr.payClass' => 'paypal', 'cr.ordersPayFeeamt' => $mc_fee,'cr.endTime'=>$paytime + (($orderFromCid['viewStock']+$orderFromCid['expressTime']) * 24 * 3600)));
					header("Location:" . Rewrite::rewrite(array('url' => '?module=shop&action=Achieve&id=' . $response['ordersId'], 'isxs' => 'no')));
					exit();
				}
			
			}
		}
	}
	
	/**
	 * 
	 * paypal IPN
	 * @param unknown_type $isSandBox
	 */
	public static function getIpn($isSandBox = false) {
		
		//设置请求参数
		$req = 'cmd=_notify-validate';
		foreach($_POST as $key => $value) {
			$value = urlencode(stripslashes($value));
			$req .= "&{$key}={$value}";
		}
		// 设置请求头
		$header = "POST /cgi-bin/webscr HTTP/1.0\r\n";
		$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
		
		//sandbox测试链接
		$handle = fopen(DATA_CACHE_ROOT_PATH . 'ipn.txt', 'a');
		fwrite($handle, var_export($_POST, 1));
		$url = '';
		
		if($isSandBox === true) {
			$url = self::$paypalSandBoxUrl;
		} else {
			$url = self::$paypalUrl;
		}
		
		//$fp = fsockopen('ssl://www.sandbox.paypal.com', 443, $errno, $errstr, 30);
		$fp = fsockopen('ssl://www.paypal.com', 443, $errno, $errstr, 30);
		fwrite($handle, var_export($fp, 1));
		
		if($fp) {
			fputs($fp, $header . $req);
			// read the body data
			$res = '';
			while(!feof($fp)) {
				$res = fgets($fp, 1024);
				
				fwrite($handle, $res);
				
				if(strcmp($res, "VERIFIED") == 0) {
					$invoice = $_POST['invoice'];
					$mc_currency = $_POST['mc_currency'];
					$payment_gross = $_POST['mc_gross'];
					$custom = $_POST['custom'];
					$receiver_email = $_POST['receiver_email'];
					$payment_status = strtoupper($_POST['payment_status']);
					$payment_fee = $_POST['mc_fee'];
					
					//检查付款是否完成，只有当钱到我们账号上后，才为完成状态
					if($payment_status == 'COMPLETED' && $receiver_email == self::$receiverEmail) {
						$invoice_array = explode("_", $invoice);
						
						$paytime = time();
						$OrdersPayDetails = 'Payment:paypal|' . 'CurrencyCode:' . $mc_currency . '|' . 'amount:' . $payment_gross . '|' . 'REFERENCEID:' . $tx_token . '|' . 'Remarks:' . $Remarks . '|' . 'time:' . $paytime;
						fwrite($handle, $OrdersPayDetails);
						
						$lang = strtolower(substr(trim($invoice), 0, 5));
						$shoppingProcess = new \Model\ShoppingProcess();
						$orderInfoFromCid = $shoppingProcess->GetOrderByCid(array('cr.ordersCid'=>$invoice,'cr.lang'=>$lang));
						$orderFromCid = $orderInfoFromCid['orderInfo']['order'];
						$response = $shoppingProcess->updateOrder(array('cr.ordersCid' => $invoice, 'cr.ordersPay' => 1, 'cr.ordersPayDetails' => $OrdersPayDetails, 'cr.ordersEstate' => 'payConfirm', 'cr.payTime' => $paytime,'cr.ordersPayFeeamt' => $payment_fee,'cr.endTime'=>$paytime + (($orderFromCid['viewStock']+$orderFromCid['expressTime']) * 24 * 3600)));
						$shoppingProcess->insertAdminRecord(array('record.ordersId' => $response['ordersId'], 'record.action' => '支付确认', 'record.username' => '系统', 'record.userip' => '127.0.0.1', 'record.action_time' => time()));
						
						$order = $shoppingProcess->GetOrderById(array('cr.ordersId' => $response['ordersId'], 'cr.lang' => $lang));
						
						$orderInfo = $order['orderInfo'];
						
						$emailAll = array('lang' => $lang, 'email' => $orderInfo['order']['consigneeEmail'], 'products' => $orderInfo['productList'], 'Orders' => $orderInfo, 'emailtitle' => 'Email_CKOK', 'theme' => THEME . 'default/email/order_achieve.htm');
						
						\Helper\Stomp::SendEmail($emailAll);
					}
				}
			}
		}
		
		fclose($handle);
	
	}

}