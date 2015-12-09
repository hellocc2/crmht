<?php

namespace Module\member;

use Helper\RequestUtil as R;

/**
 * 会员个人信息
 */
class Account extends \Lib\common\Application {
	public function __construct() {
		//$client=\Helper\CheckLogin::sso();
		$tpl = \Lib\common\Template::getSmarty ();
		$act=R::getParams ('act');
		switch($act){
			case 'changepassword':
			$tpl->display ( 'member_changepassword.html' );
			exit;
			break;
		}
		
		$tpl->display ( 'member_account.html' );
	}
}



