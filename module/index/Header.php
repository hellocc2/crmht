<?php
namespace Module\Index;
use Helper\RequestUtil as R;

class Header extends \Lib\common\Application {
	public function __construct() {
		$tpl = \Lib\common\Template::getSmarty ();
		//$params_all = R::getParams ();
		$tpl->assign ( 'lang', !empty($_SESSION["ma_lang"])?$_SESSION["ma_lang"]:'' );
		$tpl->assign ( 'websiteId', !empty($_SESSION["ma_websiteId"])?$_SESSION["ma_websiteId"]:1 );
		$tpl->assign ( 'start_time', $_SESSION["ma_starttime"] );
		$tpl->assign ( 'end_time', $_SESSION["ma_endtime"] );
		$tpl->display ( 'index_header.htm' );
	}
}