<?php
namespace Module\Admin;
use Helper\RequestUtil as R;
use Helper\CheckLogin as CheckLogin;

class Privacy extends \Lib\common\Application {
	public function __construct() {
		$tpl = \Lib\common\Template::getSmarty ();
        $db = \Lib\common\Db::get_db();
		$action=R::getParams ('action');
		
		$tpl->assign('action',$action);
        $tpl->display ('admin_privacy.html');
        

	}
}