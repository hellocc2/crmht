<?php
namespace Module\member;
use Helper\RequestUtil as R;

/**
 * 会员登录
 * @author jiangcai
 */

class Login extends \Lib\common\Application {
	public function __construct() {
		$tpl = \Lib\common\Template::getSmarty ();
		$username = R::getParams ('username');
		$password = R::getParams ('password');
		//var_dump(R::getParams());exit;
		if (!empty ($username)) {

			$db = \Lib\common\Db::get_db ();
			$password=md5($password);

			if ($username&&$password) {
 				$sql = "SELECT * FROM `rmb_money_member` WHERE member_name='".$username."' AND member_password='".$password."'";
				$row = $db->getRow($sql);
				//echo '<pre/>';var_dump($row);exit;
 				if (empty($row)){
 					$tpl->assign ( 'error', '登录失败，请重新登陆' );
 					$tpl->display ( 'member_login.html' );
					exit ();
 				}
				
				$_SESSION [SESSION_PREFIX . "MemberId"] = $row['id'];
				header ( "Location: index.php" );
				exit;
			} else {
				$tpl->assign ( 'error', '登录失败，请重新登陆' );
				$tpl->display ( 'member_login.htm' );
				exit ();
			}
		}
		$tpl->display ( 'member_login.html' );
	}
}



