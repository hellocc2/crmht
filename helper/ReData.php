<?php
namespace Helper;

/**
 * 登录检查类
 */
class ReData {
	/**
	 * 判断是否登录
	 *
	 */
	public static function getOrder($data, $order, $param) {
		
		
		$shoppingProcess = new \Model\ShoppingProcess();
		$dataJson = json_encode($data);
		$request = array();
		$request['cookieId'] = $param['cacheKey'];
		$request['memberId'] = $param['memberId'];
		if(isset($param['countryCode'])){
			$request['countryCode'] = strtolower($param['countryCode']);
		} else {
			$request['countryId'] = isset($order['shippingAddress']['consigneeStateId'])?$order['shippingAddress']['consigneeStateId']:$data['shoppingCart']['countryId'];
		}
		
		$request['priceUnit'] = $data['shoppingCart']['priceUnit'];
		if(isset($param['coupon'])) {
			$request['coupon'] = $param['coupon'];
		} else {
			if(isset($data['shoppingCart']['coupon'])&&is_array($data['shoppingCart']['coupon'])) {
				$request['coupon'] = $data['shoppingCart']['coupon']['libkey'];
			}
			
		}
		
		$request['languageCode'] = SELLER_LANG;
		if(isset($param['logistics_key'])&&!empty($param['logistics_key'])) {
			$request['expressType'] = $param['logistics_key'];
		}
		
		
		$request['returnJson'] = $dataJson;
		
		
		$data = $shoppingProcess->reInitData($request);
		
		
		$order['shoppingCart'] = $data['shoppingCart'];
		$order['data'] = $data;
		$mem = \Lib\Cache::init();
		$mem->set($param['cacheKey'] . 'order', $order);
		return $order;
	}
	
	public static function emptyOrder($cacheId) {
		\Helper\ShoppingCart::emptyCartCache();
		$mem = \Lib\Cache::init();
		$mem->delete($cacheId . 'order');
		$mem->delete($cacheId . 'paypalEX');
	}
}