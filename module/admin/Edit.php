<?php
namespace Module\Admin;
use Helper\RequestUtil as R;
use Helper\CheckLogin as CheckLogin;

class Edit extends \Lib\common\Application {
	public function __construct() {
		$tpl = \Lib\common\Template::getSmarty ();
        $db = \Lib\common\Db::get_db();
		$act=R::getParams ('act');
		
		
		
		if($act=='edit'){
			
			echo 'success!!';exit;
			
		}
		
		
        $tpl->display ('admin_edit.html');
        

	}
}