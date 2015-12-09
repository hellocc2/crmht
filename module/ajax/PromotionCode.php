<?php
namespace Module\Ajax;
use \helper\RequestUtil as R;
/**
 * 详细页评论helpful
 * @author Jerry Yang<yang.tao.php@gmail.com>
 * @sinc 2011-12-8
 */
class promotionCode extends \Lib\common\Application{
    public function __construct(){
		// -------------发表评论评价：是否有帮助--------------
		 $libkey=trim( R::getParams('libkey'));
		 $cart_obj=new \Model\Cart ();
		 $data = array(
		 	'coupon'=>$libkey,
		 );
		 $data=$cart_obj->getCart($data);
		 $status = 0;
		 $error = '';
		 //print '<pre>';print_r($data);exit;
		 if(intval($data['code']) === 0){
			if($data['shoppingCart']['coupon']['status'] === 0){
				$status = 0;
				$_SESSION['COUPON'] = $libkey;
			}else{
				$status = 1;
				unset($_SESSION['COUPON']);
				switch ($data['shoppingCart']['coupon']['status']){
					case 1:
						$error = \LangPack::$items['coupon_error1'];
					break;
					case 2:
						$error = \LangPack::$items['coupon_error2'];
					break;
					case 3:
						$error = \LangPack::$items['coupon_error5'];
					break;
					case 4:
						$num2use = isset($data['shoppingCart']['coupon']['num2use']) ? $data['shoppingCart']['coupon']['num2use'] : 0;
						if($num2use > 0){
							$error = sprintf(\LangPack::$items['coupon_error4'],$num2use);
						}else{
							$error = \LangPack::$items['coupon_error1'];
						}
					break;
					case 5:
						$price = isset($data['shoppingCart']['coupon']['price2use']) ? $data['shoppingCart']['coupon']['price2use'] : 0;
						if($price > 0){
							$price = \helper\String::numberFormat($price);
							$price_str = Currency.$price;
							$error = sprintf(\LangPack::$items['coupon_error3'],$price_str);
						}else{
							$error = \LangPack::$items['coupon_error1'];
						}
					break;
					default:
						$error = \LangPack::$items['coupon_error1'];
				}
				//$error = 'Promotional code invalid or promotional code does not meet the requirements of orders.';
			}
		 }else{
		 	$status = 1;
		 	$error = \LangPack::$items['coupon_error1'];
		 }
		 $result = array(
		 	'status'=>$status,
		 	'error'=>$error,
		 );
		 echo json_encode($result);
    }
}