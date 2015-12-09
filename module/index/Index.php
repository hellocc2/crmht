<?php
namespace Module\Index;
use Helper\RequestUtil as R;
use Helper\CheckLogin as CheckLogin;

class Index extends \Lib\common\Application {
	public function __construct() {
		$tpl = \Lib\common\Template::getSmarty ();
        $db = \Lib\common\Db::get_db();
		$action=R::getParams ('action');
		
		$tpl->assign('action',$action);
        $tpl->display ('index.html');
        

	}
}