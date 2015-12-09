<?php
namespace Helper;
class Stomp {
	//	public function __construct(){
	//		$mail = new \Lib\Mail();
	//		$data = array('to'=>'suchao@milanoo.com','subject'=>'Stomp消息测试一','content'=>file_get_contents('http://www.csdn.com/'),'headers'=>'Content-type:text/plain;charset=utf-8;'."\r\n".'Cc: 李健 <lijian@milanoo.com>'.";\r\n".'From: 米兰邮件中心 <milanoo_mailtest@milanoo.com>','params'=>'');
	//		$data = array($data);
	//		$data[] = array('to'=>'suchaotest@163.com','subject'=>'Stomp消息测试二','content'=>'测试内容<br /><ul><li><h1>测试</h1></li><li>第一行</li></ul>','headers'=>'Content-type:text/html;charset=utf-8;'."\r\n".'Cc: 李健2 <lijian@milanoo.com>','params'=>'');
	//		$re = $mail->send($data);
	//		var_dump($re);
	//		header('Content-type:application/json;charset=utf-8');
	//	}
	

	public static function SendEmail($emailAll) {
		
		$action_lang = \Lib\common\Language::loadLangPack($emailAll['lang']);
		
		if(defined('THEME_ROOT_PATH')) {
			include (THEME_ROOT_PATH . 'email/' . $emailAll['theme']);
		} else {
			include ($emailAll['theme']);
		}
		
		if(!isset($emailLR)) {
			include ($emailAll['theme']);
		}
		
		
		$email_from = (isset($emailAll['email_from'])&&!empty($emailAll['email_from']))?$emailAll['email_from']:'';
		$emailuser = (isset($emailAll['emailuser'])&&!empty($emailAll['emailuser']))?$emailAll['emailuser']:'';
		if(empty($email_from)){
			$preFix = '';
			switch ($emailAll['lang']) {
				case 'fr-fr':
					$preFix = 'france-';
					break;
					case 'de-ge':
					$preFix = 'germany-';
					break;
					case 'es-sp':
					$preFix = 'spanish-';
					break;
					case 'it-it':
					$preFix = 'italia-';
					break;
					case 'ru-ru':
					$preFix = 'russia-';
					break;
					case 'pt-pt':
					$preFix = 'portugal-';
					break;
			}
			$email_from = $preFix.'service@milanoo.com';
		}
		$data = array('username'=>$email_from,'from'=>'Milanoo.com<'.$email_from.'>','to'=>$emailAll['email'],'subject'=>isset($action_lang[$emailAll['emailtitle']])?$action_lang[$emailAll['emailtitle']]:$emailAll['emailtitle'],'content'=>$emailLR,'headers'=>"Content-type:text/html;charset=utf-8;\r\n");
		
		$mail = new \Lib\Mail();
		
		$re = $mail->send($data);
	}
}