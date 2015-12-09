<?php
namespace Module\Ajax;
use Helper\RequestUtil as R;

class Category extends \Lib\common\Application {
	function __construct() {
		//------------------获取页面传入参数---------------------------------
		$categoryId = R::getParams ( 'cid' );
		$cids = R::getParams ( 'cids' );
		$act = R::getParams ( 'act' );
		
		$category = new \Model\Category ();
		if ($act == "search") {
			$search = R::requestParam("seach");
			$result = $category->getSearchCategory ( $search );
		} 
		elseif ($cids){
			$result = $category->getCategoryName ( $cids );
		}
		else {
			$result = $category->getCategory ( $categoryId );
		}
	}
}