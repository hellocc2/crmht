<?php
namespace Module\Ajax;
/**
 * FileName:SearchTopQuery.php
 * AJAX获取终端页顶部推荐商品
 * Author:@{chengjun cgjp123@163.com}
 * Date:@{2011-11-24 11:32:32}
 */
use Helper\RequestUtil as R;
use Helper\ResponseUtil as Rewrite;
class SearchTopQuery extends \lib\common\Application{
	public function __construct(){
		$productId = R::getParams('productId');
		$keyword = R::getParams('keyword');
		$page = R::getParams('page');
		$pageSize = R::getParams('pageSize');
		if(!empty($productId)){
			$page = !empty($page) ? $page : 1;
			$keyword = !empty($keyword) ? $keyword : '';
			$pageSize = !empty($pageSize) ? $pageSize : 9;
			
			$searchTopQueryArray = array('productId'=>$productId,'searchContent'=>$keyword,'pageSize'=>$pageSize,'pageNo'=>$page);
			$getTopQueryRecommend = new \Model\ItemOtherProducts();
			$topQueryData = $getTopQueryRecommend->getTopQuery($searchTopQueryArray);
			$html='';
			if(!empty($topQueryData) && $topQueryData['code']==0){
				header('Content-Type: text/html;charset=utf-8');
				if(!empty($topQueryData['listResults']['results'])){
					//去掉反斜杠
					$topQueryData['listResults']['results'] = $this->dostrip($topQueryData['listResults']['results']);
					foreach($topQueryData['listResults']['results'] as $val){
						$html .= '<dl>';
						if($keyword){
							$html .= '<dt><a href="'.Rewrite::rewrite(array('url'=>'?module=thing&action=item&id='.$val['productId'],'seo'=>$val['productName'],'isxs' => 'no')).'?searchKeyword='.$keyword.'"><img src="'.CDN_UPLOAD_URL.'upen/m/'.$val['firstPictureUrl'].'" width="66" height="89" /></a></dt>';
						}else{
							$html .= '<dt><a href="'.Rewrite::rewrite(array('url'=>'?module=thing&action=item&id='.$val['productId'],'seo'=>$val['productName'],'isxs' => 'no')).'"><img src="'.CDN_UPLOAD_URL.'upen/m/'.$val['firstPictureUrl'].'" width="66" height="89" /></a></dt>';
						}
						$html .= '<dd><b>'.Currency.\Lib\common\Language::priceByCurrency ( $val ['productPrice'] ).'</b></dd>';
						$html .= '</dl>';
					}
				}
				echo $html;
			}else{
				echo '';
			}
		}else{
			echo 'no productId';
		}
	}
	
	/**
	 * 去掉反斜杠
	 */
	function dostrip($value) {
		if (is_array ( $value )) {
			$value = array_map ( 'self::dostrip', $value );
		} else {
			$value = stripslashes ( $value );
			$value = htmlspecialchars_decode ( $value );
		}
		return $value;
	}
}