<?php

namespace Module\operate;

use Helper\RequestUtil as R;

/**
 * 经验总结
 */
class Summary extends \Lib\common\Application {
	public function __construct() {
		//$client=\Helper\CheckLogin::sso();
		$tpl = \Lib\common\Template::getSmarty ();
		$params_all = R::getParams ();
		
		
		$tpl->display ( 'operate_summary.html' );
	}
}



