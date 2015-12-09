<?php
namespace Module\operate;
use Helper\RequestUtil as R;

/**
 * 历史行情
 */
class History extends \Lib\common\Application {
	public function __construct() {
		//$client=\Helper\CheckLogin::sso();
		$tpl = \Lib\common\Template::getSmarty ();
		$tpl->display ( 'operate_history.html' );
	}
}



