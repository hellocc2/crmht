<?php
namespace Module\Ajax;
use helper\RequestUtil as R;

class sale extends \Lib\common\Application {
	public function __construct() {
		$tpl = \Lib\common\Template::getSmarty ();
		$pid = R::getParams ( 'pid' );
		$pagelist = new \model\Index ();
		$products = $pagelist->getproductsale ( $pid );
		//print_r($products);
		$tpl->assign ( 'products', $products );
		
		//echo '<script src="{-$javascript_url-}amcharts.js" type="text/javascript"></script>';
		 $tpl->display ( 'ajax_sale.htm' );
	}
}