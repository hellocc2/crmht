<?php

namespace Module\member;

use Helper\RequestUtil as R;
//use Helper\ResponseUtil as rew;

/**
 * 会员注册
 *
 * @author wujianjun<wujianjun127@163.com>
 *         @sinc 2012-05-15
 * @param
 *        	int
 * @param
 *        	int
 */
class Login extends \Lib\common\Application {
	public function __construct() {
		$client=\Helper\CheckLogin::sso();
		$tpl = \Lib\common\Template::getSmarty ();
		$params_all = R::getParams ();
		
		if (!empty ( $client['uid'] )) {

			$db = \Lib\common\Db::get_db ( 'milanoo' );

			$uid = $client['uid'];
			if ($uid) {
// 				$sql = "SELECT * FROM `milanoo_admin_user` au, milanoo_admin_competence ac WHERE ac.id IN (au.competence_id) AND uid = {$uid} AND FIND_IN_SET  ('1351', competence_menu)";
// 				$row = $db->getrow ( $sql );
// 				if (empty ( $row )) {
// 					$tpl->assign ( 'error', '用户名密码验证成功，但是你没有查看 MA 的权限请找相关人员开通' );
// 					$tpl->display ( 'member_login.htm' );
// 					exit ();
// 				}
				//var_dump($row['realname']);exit;
				$_SESSION [SESSION_PREFIX . "MemberId"] = $client['uid'];
				
				// setcookie('auth', '1', time() + 60 * 60 * 24 * 30);
				header ( "Location: index.php" );
				exit;
			} else {
				$tpl->assign ( 'error', '登录失败，请使用米兰账号登陆' );
				$tpl->display ( 'member_login.htm' );
				exit ();
			}
		}
		$tpl->display ( 'member_login.htm' );
	}
}



