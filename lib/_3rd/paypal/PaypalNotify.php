<?php
/**
 * paypal listener
 * 
 * 开启paypal IPN之后，该文件用于监听paypal付款的操作，
 * 当客户在paypal中付款时，就会向该文件发送相关信息，
 * 根据发过来的信息，即可判断客户付款是否成功，从而修改
 * 订单的状态
 * 
 * @author  ljiang  <jiang.lin.person@gmail.com>
 * @date    2011-04-27
 * @copyright   milanoo.com
 * @version bete 1.0
 * 
 */ 



    session_name("milanooId");
    session_start();
    define('in_milanoo', true);
    require_once '../extension.inc';
    require_once '../config/config.inc.php';
    require_once '../config/b2cconfig.inc.php';
    include_once LIB_PATH . 'comm/db.class.' . PHP_EX;
    include_once LIB_PATH . 'comm/b2cbaseAction.class.' . PHP_EX;
    $get_smarty	= new b2cbase_action;
    $tpl	= $get_smarty->get_smarty();
    $db	= new db;
    $db	= $db->get_database();
    $db_host=$db_user=$db_pass=$db_name=$db_type=NULL;
   
    // read the post from PayPal system and add 'cmd'
    	$req = 'cmd=_notify-validate';
    	
    	foreach ($_POST as $key => $value) 
    	{
    	    
    		$value = urlencode(stripslashes($value));
    		$req .= "&$key=$value";
    	}
    
    	// post back to PayPal system to validate
    	$header  = "POST /cgi-bin/webscr HTTP/1.0\r\n";
    	$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
    	$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
    
      	//If testing on Sandbox use:
//    	$fp = fsockopen ('ssl://www.sandbox.paypal.com', 443, $errno, $errstr, 30);
		$fp = fsockopen ('ssl://www.paypal.com', 443, $errno, $errstr, 30);
    
    if (!$fp) {
    	// HTTP ERROR
        //此处可以进行日志写操作
    } else {
    	fputs ($fp, $header . $req);
    	// read the body data
    	$res = '';
    	while (!feof($fp)) {
    		$res = fgets ($fp, 1024);
    		
    				
            //VERIFIED:意味着付款正确，但不意味着付款结束
    		if (strcmp($res, "VERIFIED") == 0) {
                
    			$invoice	 = $_POST['invoice'];
        		$mc_currency = $_POST['mc_currency'];
        		$payment_gross = $_POST['mc_gross'];
        		$custom		 = $_POST['custom'];
                $receiver_email = $_POST['receiver_email'];
                $payment_status = strtoupper($_POST['payment_status']);
                $payment_fee = $_POST['mc_fee'];
                
                //检查付款是否完成，只有当钱到我们账号上后，才为完成状态
                if($payment_status == 'COMPLETED' && $receiver_email == 'paypal@milanoo.com') {
                    
                    $invoice_array=explode("_",$invoice);
                    
                    /*--------Test code------------//
                    
                    $k .= '\r\n----success';
                    $path = "test.log";
    				$handle = fopen($path,"a");
    				$c = fwrite($handle,$k);
    				fclose($handle);
                    
                    //----------------------------//
                    */
                    
            		if($invoice_array[0]=="voucher")
            		{
            			if($invoice_array[1]==$custom)
            			{
            				$sql	= "	SELECT * FROM `" . TABLE_PREFIX . "balance_voucher` where `id`='".$invoice_array[1]."'";
            				$sth	= $db->Prepare($sql);
            				$res	= $db->Execute($sth);
            				$Account= slashes($res->FetchRow());
            				if($Account['BalanceState']!="Yestdz")
            				{
            					if($Account['Currency']==$mc_currency)
            					{
            						if($Account['TheAmountOf']==$payment_gross)
            						{
            							$sql			= "	UPDATE `" . TABLE_PREFIX . "balance_voucher`
            									SET `BalanceState` = ? ,`Paytime` = ? ,`PayWay` = ?  where id= ? ";
            							$sth			= $db->Prepare($sql);
            							$res			= $db->Execute($sth, array('Yestdz',time()+Time_tdoa,'paypal',$custom));
            							include_once LIB_PATH . 'comm/ExchangeRate.class.' . PHP_EX;
            							$ExchangeRate= new ExchangeRate();
            							$ExchangeRate=$ExchangeRate->ExchangeRate;
            							$Remarks='Clearing the exchange rate 1 : '.$ExchangeRate[$mc_currency].'<br>'.$Account['Remarks'];
            							$sql			= "INSERT INTO `" . TABLE_PREFIX . "balance_log`
            														(`MemberId`,
            														`TheAmountOf`,
            														`BalanceAction`,
            														`BalanceState`,
            														`BalanceTime`,
            														`Remarks`) 
            														VALUES (?,?,?,?,?,?)";
            							$sth			= $db->Prepare($sql);
            							$res			= $db->Execute($sth, array($Account['MemberId'],(round($payment_gross*$ExchangeRate[$mc_currency]*100)/100),'Voucher','Success',time()+Time_tdoa,$Remarks));
            							$sql			= "	UPDATE `" . TABLE_PREFIX . "member`
            										SET `Balance` = `Balance` + ".(round($payment_gross*$ExchangeRate[$mc_currency]*100)/100) ." where `MemberId`= ? ";
            							$sth			= $db->Prepare($sql);
            							$res			= $db->Execute($sth, array($Account['MemberId']));
            						}
            						else
            						{
            							$sql			= "	UPDATE `" . TABLE_PREFIX . "balance_voucher`
            									SET `Remarks` = ?  where id= ? ";
            							$sth			= $db->Prepare($sql);
            							$res			= $db->Execute($sth, array('充值数据不符，取得数据'.$mc_currency.":".$payment_gross,'paypal',$custom));
            						}
            					}
            					else
            					{
            						$sql			= "	UPDATE `" . TABLE_PREFIX . "balance_voucher`
            									SET `Remarks` = ?  where id= ? ";
            						$sth			= $db->Prepare($sql);
            						$res			= $db->Execute($sth, array('充值数据不符，取得数据'.$mc_currency.":".$payment_gross,'paypal',$custom));
            					}
            				}
            			}
            		}
            		else
            		{
            			$paytime=time()+Time_tdoa;
            			$OrdersPayDetails='Payment:paypal|'.'CurrencyCode:'.$mc_currency.'|'.'amount:'.$payment_gross.'|'.'REFERENCEID:'.$tx_token.'|'.'Remarks:'.$Remarks.'|'.'time:'.$paytime;
            			$sql			= "	UPDATE `" . TABLE_PREFIX . "orders`
            										SET `OrdersPay` = ? ,`OrdersPayFeeamt` = ?,`OrdersPayDetails` = ? where OrdersCid= ?";
            			$sth			= $db->Prepare($sql);
            			$res			= $db->Execute($sth, array('1',$payment_fee,$OrdersPayDetails,$invoice));
						
						$sql = "	UPDATE `" . TABLE_PREFIX . "orders`
									SET `OrdersEstate` = ?  where OrdersCid= ? ";
						$sth = $db->Prepare ( $sql );
						$res = $db->Execute ( $sth, array ('payConfirm',$invoice ) );
						$sql="select `OrdersId` from `" . TABLE_PREFIX . "orders` where OrdersCid='{$invoice}'";
						$sth			= $db->Prepare($sql);
						$res			= $db->Execute($sth);
						$Orders		= slashes($res->FetchRow());
						$sql = "INSERT INTO `" . TABLE_PREFIX . "admin_records` (`action`,`username`,`userip`,`action_time`,`OrdersId`) VALUES (?,?,?,?,?)";
						$sth = $db->Prepare($sql);
						$res = $db->Execute($sth, array('支付确认','系统','127.0.0.1',time(),$Orders['OrdersId']));
            		}
                }	
    		}
    		else if (strcmp ($res, "INVALID") == 0) 
    		{
    		  //todo
              //Fail to virified
              //此处可进行错误日志写操作
    		}
    	
    	}
        fclose ($fp);
    }

?>