<?php
namespace Module\Ajax;
use Helper\RequestUtil as R;
/**
 * FileName:getItemGoodsTheme.php
 * AJAX获取终端页商品模板
 * Author:@{chengjun cgjp123@163.com}
 * Date:@{2011-12-16 04:56:54}
 */
class GetItemGoodsTheme extends \Lib\common\Application{
    public function __construct(){
    	$productId = R::getParams('productId');
    	if(!empty($productId)){
	    	$cacheKey = md5(SELLER_LANG.$productId).'_proTheme';
	    	$temp = '';
	    	$mem = \Lib\Cache::init();
	    	$temp = $mem->get($cacheKey);
	    	echo $temp;
    	}else{
    		echo '';
    	}
    }
}