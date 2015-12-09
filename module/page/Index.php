<?php
namespace Module\Page;
use Helper\RequestUtil as R;

class Index extends \Lib\common\Application {
	public function __construct() {
		$tpl = \Lib\common\Template::getSmarty ();
		//$params_all = R::getParams ();

		$search ["pagetype"] = R::requestParam ( 'pagetype' );
		$search ["pagename"] = R::requestParam ( 'pagename' );
		$search ["category"] = R::requestParam ( 'category' );
		$search ["paging"] = R::requestParam ( 'page' );
		$search ["order"] = R::requestParam ( 'order' );
		if (! isset ( $search ["order"] ))
			$search ["order"] = "payamount";
		$search ["desc"] = R::requestParam ( 'desc' );
		if ($search ["pagetype"] == "list" || $search ["pagetype"] == "item")
			$tpl->assign ( 'saledata', 1 );
		$url = "?module=page&action=index";
		$pagelist = new \model\PageVisits ();
		$pagevisits = $pagelist->getPageVisits ( $search, $url );
		$tpl->assign ( 'search', $search );
		$tpl->assign ( 'url', $pagevisits ["url"] );
		$tpl->assign ( 'pagevisits', $pagevisits ["row"] );
		$tpl->assign ( 'page', $pagevisits ["page"] );
		$tpl->assign ( 'collect', $pagevisits ["collect"] );
		$tpl->assign ( 'daydata', $pagevisits ["daydata"] );
		
		$tpl->assign ( 'lang', !empty($_SESSION["ma_lang"])?$_SESSION["ma_lang"]:'' );
		$tpl->assign ( 'websiteId', !empty($_SESSION["ma_websiteId"])?$_SESSION["ma_websiteId"]:1 );
		$tpl->assign ( 'start_time', $_SESSION["ma_starttime"] );
		$tpl->assign ( 'end_time', $_SESSION["ma_endtime"] );
		
		$tpl->display ( 'page_visits.htm' );
	}
}