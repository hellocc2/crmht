<?php
namespace Module\User;
use Helper\RequestUtil as R;
use Helper\CheckLogin as CheckLogin;

class Rebate extends \Lib\common\Application {
	public function __construct() {
		$tpl = \Lib\common\Template::getSmarty ();
        $db = \Lib\common\Db::get_db();
		$action=R::getParams ('action');
		
		$tpl->assign('action',$action);
        $tpl->display ('user_rebate.html');
        

	}
}