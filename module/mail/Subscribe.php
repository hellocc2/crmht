<?php
namespace Module\mail;
/**
 * 关键词首字母查询显示模块
 * @author Su Chao<suchaoabc@163.com>
 * @sinc 2011-10-17
 * @param int 
 * @param int 
 */
class Subscribe extends \Lib\common\Application {
	public function __construct() {
		if($_POST){
			$name = $_POST['firstname'];
			$mail = $_POST['email'];
			if(empty($name)){
				\Helper\Js::alertForward('noMemberContact', null);
			}
			if(!\Helper\Verification::isemail($mail)) {
				\Helper\Js::alertForward('email1', null);
			}
			
			$sendMail = new \Model\Email(); 
    		$result = $sendMail->sentEmail($name,$mail);
    		\Helper\Js::alertForward('subscribe_mail', null);
		}
	}
}


