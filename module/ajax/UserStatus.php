<?php
namespace Module\Ajax;
use config\Currency;

use \helper\RequestUtil as R;
/**
 * 详细页评论helpful
 * @author Jerry Yang<yang.tao.php@gmail.com>
 * @sinc 2011-12-8
 */
class UserStatus extends \Lib\common\Application{
    public function __construct(){
		// -------------发表评论评价：是否有帮助--------------
		$act = isset($_GET['act']) ? $_GET['act'] : 'login';
    	switch ($act){
    		case 'login':
    			$this->cartLogin();
    		break;
    		case 'logout':
    			$this->cartLogout();
    		break;
    	}
    }
    
    function cartLogout(){
    	session_unregister ( 'is_login_failed' );
    	session_unregister ( SESSION_PREFIX . "MemberId" );
    	session_unregister ( SESSION_PREFIX . "MemberUserName" );
    	session_unregister ( SESSION_PREFIX . "MemberEmail" );
    	session_unregister ( SESSION_PREFIX . "MemberType" );
    	session_unregister ( SESSION_PREFIX . "CartProducts" );
    	session_unregister ( SESSION_PREFIX . "Consignee" );
    	session_unregister ( SESSION_PREFIX . "shrgj" );
    	session_unregister ( SESSION_PREFIX . "Payment" );
    	session_unregister ( SESSION_PREFIX . "Remarks" );
    	session_unregister ( SESSION_PREFIX . "logistics_key" );
    }
    
    function cartLogin(){
    	if(isset($_SESSION[SESSION_PREFIX.'MemberEmail'])){
    		$temp_username=substr($_SESSION[SESSION_PREFIX.'MemberEmail'],0,strpos($_SESSION[SESSION_PREFIX.'MemberEmail'],'@'));
    		$temp_username=!empty($_SESSION[SESSION_PREFIX.'MemberUserName'])?$_SESSION[SESSION_PREFIX.'MemberUserName']:$temp_username;
    		$json_data ['MemberUsername']=$temp_username;
    	}
    	$price = 0;
    	$productsCount = 0;
    	$data = \Helper\ShoppingCart::getCart();
    	if(isset($data['shoppingCart'])){
    		$shoppingCart = $data['shoppingCart'];
    		if(isset($shoppingCart['productCarts'])){
    			foreach ($shoppingCart['productCarts'] as $key=>$v){
    				$productsCount += $v['buyNum'];
    			}
    		}
    		if(isset($shoppingCart['cartPriceTotal'])){
    			$price = round($shoppingCart['cartPriceTotal'],2);
    		}
    		if($price > 0){
	    		if(CurrencyCode == 'JPY'){
	    			$price =  str_replace ( ',', '，', number_format ( $price ) );
	    		}else{
	    			$price = \helper\String::numberFormat($price);
	    		}
    		}
    		
    	}
    	
    	$json_data ['cart_price'] = Currency . $price;
    	$json_data ['cart_number'] = $productsCount;
    	if (isset ( $_SESSION [SESSION_PREFIX . "MemberId"] )) {
    		$json_data ['status'] = true;
    		$json_data ['mail'] = $_SESSION [SESSION_PREFIX . "MemberEmail"];
    	}else {
    		$json_data ['status'] = false;
    	}
    	echo json_encode ( $json_data );
    	exit;
    }
}