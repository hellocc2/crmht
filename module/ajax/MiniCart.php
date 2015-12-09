<?php
namespace Module\Ajax;
use Helper\RequestUtil as R;
/**
 * mini购物车商品操作
 * @author Administrator
 *
 */
class MiniCart extends \Lib\common\Application {
	function __construct(){
		$cart_obj=new \Model\MiniCart ();
		$cartId = R::getParams('cartId');
		$reload = R::getParams('reload');
		if($reload){
			$this->reLoadMini();
			exit;
		}
		$jsonReturn = array();
		if(empty($cartId)){
			$jsonReturn['status'] = 1;
			$jsonReturnString = json_encode($jsonReturn);
			exit($jsonReturnString);//返回失败
		}
		$data['cartId'] = $cartId;
		$returnData = $cart_obj->removePro($data);
		if(!empty($returnData) && $returnData['code']==0){
			$systemDelIds = array();
			$systemDelIds['status'] = 0;
			if(!empty($returnData['shoppingCart'])){				
				//系统自动删除部分数据
				if(!empty($returnData['shoppingCart']['messages'])){
					foreach($returnData['shoppingCart']['messages'] as $k=>$v){
						$systemDelIds['msg'][] = $v['cartId'];
					}
				}
				
				//商品数量
				if(!empty($returnData['shoppingCart']['productNum'])){
					$systemDelIds['pnum'] = $returnData['shoppingCart']['productNum'];
				}else{
					$systemDelIds['pnum'] = 0;
				}
				
				//商品价格
				if(!empty($returnData['shoppingCart']['cartPriceTotal'])){
					$systemDelIds['totalPrice'] = $returnData['shoppingCart']['cartPriceTotal'];
				}else{
					$systemDelIds['totalPrice'] = 0;
				}
				
				//货币
				$systemDelIds['Currency'] = Currency; 
				
				if(!empty($systemDelIds)){
					$systemDelIdsJson = json_encode($systemDelIds);
				}
				exit($systemDelIdsJson);
			}else{
				$systemDelIds['pnum'] = 0;
				$systemDelIds['totalPrice'] = 0;
				$systemDelIds['Currency'] = Currency;
				if(!empty($systemDelIds)){
					$systemDelIdsJson = json_encode($systemDelIds);
				}
				exit($systemDelIdsJson);
			}
		}else{
			$jsonReturn['status'] = 1;
			$jsonReturnString = json_encode($jsonReturn);
			exit($jsonReturnString);//返回失败
		}
	}
	
	/**
	 * 重载mini购物车
	 */
	public function reLoadMini(){
		$html_result = '';
		$cart_obj=new \Model\MiniCart ();
		$result=$cart_obj->getMiniCart();
		$data = array();
		if($result['code'] == 0){
			$data = $result['shoppingCart'];
			if(!empty($data['products'])){
				$html_result .= '<div class="bag"><a id="link_btn" href="'.\helper\ResponseUtil::rewrite(array('url'=>'?module=shop&action=Cart','isxs'=>'no')).'" rel="nofollow"></a>';
				$html_result .= '<a href="'.\helper\ResponseUtil::rewrite(array('url'=>'?module=shop&action=Cart','isxs'=>'no')).'" rel="nofollow">'.\LangPack::$items['shopping_bag'].'</a><br />';
				$html_result .= '<b class="items_num miniCart_TotalNum">('.$data['productNum'].')</b>'.\LangPack::$items['theme_Cart_items'].'<br />';
				$html_result .= '<b class="items_price">'.\LangPack::$items['thing_Item_Total'].':'.Currency.' <span id="miniCart_TotalPrice">'.\Helper\String::numberFormat($data['cartPriceTotal']).'</span></b></div>';
				// $html_result .= '<a href="javascript:void(0);" class="minicontrol arrow_up"><span class="no_more"></span></a>';
				$html_result .= ' <ul class="bag_list clear">';
				$data['products'] = \helper\String::strDosTrip($data['products']);
				foreach($data['products'] as $k=>$v){
					$pName = $v['productName'];
					$pNum = $v['buyNum'];
					$imgSrc = CDN_UPLOAD_URL . 'upen/m/' . $v ['firstPictureUrl'];
					$proUrl = \helper\ResponseUtil::rewrite(array('url'=>'?module=thing&action=item&id='.$v['productId'],'seo'=>$pName,'isxs'=>'no'));
					$html_result .= '<li id="minicart_row_'. $v['cartId'] .'"><a href="'.$proUrl.'" target="_blank"><img width="38" height="50" src="'.$imgSrc.'">';
					$html_result .= '</a><a class="g_name" href="'.$proUrl.'"  target="_blank">'.$pName.'</a><span class="g_price">'.Currency.' '.\Helper\String::numberFormat($v['unitPrice']).'</span>';
					$html_result .= '<span class="qty"> &times; '.$pNum.'</span>';
					//$html_result .= '<a class="remove_btn" href="javascript:minicart.removeItem('. $v['cartId'] .');"></a>';
					$html_result .= '</li>';
				}
				$html_result .= '</ul>';
				$html_result .= '<div class="gotocart"><a id="link_btn" href="'.\helper\ResponseUtil::rewrite(array('url'=>'?module=shop&action=Cart','isxs'=>'no')).'" rel="nofollow">'.\LangPack::$items['shopping_bag'].'<em></em></a></div>';
			}
		}
		echo $html_result;
		exit;
	}
}