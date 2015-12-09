<?php
namespace Module\mail;

class Unsubscribe extends \Lib\common\Application{
	public function __construct(){
		global $tpl ;
		$tpl = \Lib\common\Template::getSmarty ();
		if($_POST){
			$curl=curl_init();
			$url='http://link.milanoo.com/u/register_bg.php?owner_id=141342820&key_id=3&f=10746&optin=n&inp_3='.$_POST['em'];			
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_HEADER, 0);
			//curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:11.0) Gecko/20100101 Firefox/11.0');
			$data = curl_exec($curl);
			curl_close($curl);
			//echo $data;exit;
			$returnFlag=preg_match('/user_id/', $data);
			if($returnFlag){
				$tpl->display('Unsubscribe-2.htm');
			}
		}
		else{
			 if(!empty($_GET['email'])){
				$tpl->assign('Email',$_GET['email']);
				$tpl->display('Unsubscribe.htm');
			 }
		}


	}
}
?>