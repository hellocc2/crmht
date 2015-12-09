<?php
namespace Module\Ajax;
use \helper\RequestUtil as R;
/**
 * 详细页评论helpful
 * @author Jerry Yang<yang.tao.php@gmail.com>
 * @sinc 2011-12-8
 */
class shipping extends \Lib\common\Application{
    public function __construct(){
		// -------------发表评论评价：是否有帮助--------------
		 $act = trim(R::getParams('menu'));
		 switch ($act){
			case 'itemshippingcost':
				$countryId=intval(trim(R::getParams('countryId')));
				$ajax_item_shipping_data = array(
					'categoryId'=>intval(R::getParams('categoryId')),
					'price'=>R::getParams('price'),
					'weight'=>R::getParams('weight'),
					'countryId'=>R::getParams('countryId'),
					'languageCode'=>SELLER_LANG,
					'priceUnit'=>CurrencyCode,
					'num'=>1,
				);
				$promotionType = R::getParams('promotionType');
				if($promotionType){
					$ajax_item_shipping_data['promotionType'] = $promotionType;		
					$ajax_item_shipping_data['superposition'] = intval(R::getParams('superposition'));
				}
				$html = '';
				if($countryId > 0){
					$mProduct = new \Model\Product ();
					$result = $mProduct->getProducsTransportPrice ( $ajax_item_shipping_data ); 
					
					if(isset($result['code']) && $result['code'] == 0 && isset($result['freight']) && count($result['freight']) > 0){
						$html .= '<table width="80%" cellspacing="0" cellpadding="0" border="0">';
						$html .= '<tbody><tr><th>Shipping Methods</th><th>Shipping Time</th></tr>';
						foreach ($result['freight'] as $v){
							$html .= '<tr><td>'.$v['name'].'</td><td>'.$v['postTime'].' '.\LangPack::$items['thing_item_days'].'</td></tr>';
						}
		 				$html .= '</tbody></table>';
					}
				}
				echo $html;
			break; 
		 	default: 
				$countryId=trim(R::getParams('countryid'));
				$cart_obj=new \Model\Cart ();
				$data=$cart_obj->getShipping($countryId);
				//print '<pre>';print_r($data);exit;
				if($data['code']==0) {
					if(isset($data['shoppingCart']['freightCountList'])){
						setcookie('shippingCountry',$countryId,time()+3600*24*300,"/");
						$html = '<table width="100%" class="popcartlist" id="shipping_method" cellpadding="0" cellspacing="0">';
						$html .= '<tr>';
						$html .= '<th>'.\LangPack::$items['cart_shpping_method'].'</th>';
						$html .= '<th>'.\LangPack::$items['cart_shpping_method_time'].'</th>';
						$html .= '<th>'.\LangPack::$items['thing_Shipping_Cost'].'</th>';
						$freighCountList = $data['shoppingCart']['freightCountList'];
						krsort($freighCountList);
						$html .= '</tr>';
						foreach ($freighCountList as $key=>$v) {
							$value = $v['expressType'];
							$checked = $_SESSION['expressType'] == $v['expressType'] ? 'checked' : '';
							$html .= '<tr>';
							//$html .= '<td><input type="radio" name="shipping_method" days="'.$v['postTime'].'" value="'.$value.'" '.$checked.' /> <b class="color_y">'.$v['name'].'</b></td>';
							$html .= '<td><b class="color_y">'.$v['name'].'</b></td>';
							$html .= '<td>'.$v['postTime'].' '.\LangPack::$items['cart_business_days'].'</td>';
							if($value == 'Standard'){
								$old_price = round($v['priceTotal'] / 0.6,2);
								$shipping_off = "40";
							}else{
								$old_price = round($v['priceTotal'] / 0.5,2);
								$shipping_off = "50";
							}
							$priceTotal = \Helper\String::numberFormat($v['priceTotal']);
							$old_price = \Helper\String::numberFormat($old_price);
							if($value == 'Standard'){
								$js_old_price = '<del>'.Currency.' '.$old_price.'</del>';
								$js_shipping_off = $shipping_off."% OFF";
								$js_price_total = Currency.' '.$priceTotal;
							}
							if($value == 'Standard' && $v['priceTotal'] == 0){
								$html .= '<td><span style="color:red">'.\LangPack::$items['thing_item_free_shipping'].'</span></td>';
							}else{
								$html .= '<td><del>'.Currency.$old_price.'</del>  '.Currency.$priceTotal.'  <span style="color:red">('.$shipping_off.'% OFF)</span></td>';
							}
							$html .= '</tr>';
						}
						$html .= '</table>';
							
						//$html = htmlentities($html);
						$html = str_replace('<', '&lt;', $html);
						$html = str_replace('>', '&gt;', $html);
						$html = str_replace('"', '&quot;', $html);
						$html = str_replace("'", '&#039;', $html);
						$result = array(
							'html'=>addslashes($html),
							'js_old_price'=>$js_old_price,
							'js_shipping_off'=>$js_shipping_off,
							'js_price_total'=>$js_price_total,	
							'price'=>$v['priceTotal'],
							'unit'=>Currency,
						);
						
						echo json_encode($result);
					}else{
						die('');
					}
				}else{
					die('');
				}
	    }
    }
}