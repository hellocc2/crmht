<?php
namespace Model;
/**
 * 购物流程相关Model
 * @Jiang Lin<jianglin@milanoo.com>
 *
 */
class ShoppingProcess extends Base {
	public function __construct() {
		$this->interface = $this->getInterface();
	}
	
	/**
	 * 根据用户ID获取地址列表
	 *
	 * @param array $data 数据
	 */
	public function getAddressList($data) {
		$this->interface->setNeedCache(false);
		$this->interface->setMethod('POST');
		return $this->interface->call('sp/courier/getAddressByLoginUser', array(),$data);
	}
	
	public function setAddress($data) {
		$this->interface->setNeedCache(false);
		$this->interface->setMethod('POST');
		return $this->interface->call('sp/courier/operateAddressByLoginUser', array(),$data);
	}
	
	/**
	* 获取登录用户billingAddress
	*/
	public function getBillAdress($data){
		$this->interface->setNeedCache(false);
		$this->interface->setMethod('POST');
		return $this->interface->call('sp/courier/getBillingAddress', array(),$data);
	}
	/**
	* 设置登录用户billingAddress
	*/
	public function setBillingAddress($data){
		$this->interface->setNeedCache(false);
		$this->interface->setMethod('POST');
		return $this->interface->call('sp/courier/updateBillingAddress', array(),$data);
	}
	
	/**
	 * 获取支付流程数据
	 *
	 * @param array $data 数据
	 */
	public function reInitData($data) {
		$this->interface->setNeedCache(false);
		//return $this->interface->call('sp/shoppingCart/getCartInfo',$data);
		$this->interface->setMethod('POST');
		return $this->interface->call('sp/shoppingCart/getCartInfo', array(), $data);
	}
	
	/**
	 * 
	 * 生成订单
	 * @param array $data
	 */
	public function CreatOrder($data) {
		$this->interface->setNeedCache(false);
		//return $this->interface->call('sp/shoppingCart/getCartInfo',$data);
		$this->interface->setMethod('POST');
		return $this->interface->call('sp/courier/saveOrderInfoByJSONObject', array(), $data);
	}
	
	/**
	 * 
	 * 通过订单ID获取订单详情
	 * @param unknown_type $data
	 */
	public function GetOrderById($data) {
		$this->interface->setNeedCache(false);
		$this->interface->setMethod('POST');
		return $this->interface->call('sp/courier/getOrderInfoByOrderId',array(), $data);
	}
	
	/**
	 *
	 * 通过订单CID获取订单详情
	 * @param unknown_type $data
	 */
	public function GetOrderByCid($data) {
		$this->interface->setNeedCache(false);
		$this->interface->setMethod('POST');
		return $this->interface->call('sp/courier/getOrderInfoByCid',array(), $data);
	}
	
	/**
	 * 
	 * 支付完成后更改订单相应状态
	 * @param array $data <Map>泛型 
	 */
	public function updateOrder($data) {
		$this->interface->setNeedCache(false);
		$this->interface->setMethod('POST');
		return $this->interface->call('sp/courier/updateOrderState', array(),$data);
	}
	
	/**
	 * 
	 * 修改BILLING地址
	 * @param array $data <Map>泛型 
	 */
	public function updateBillingAddress($data) {
		$this->interface->setNeedCache(false);
		$this->interface->setMethod('POST');
		return $this->interface->call('sp/courier/updateOrderBillingAddress', array(),$data);
	}
	
	/**
	 *
	 * 修改订单的会员ID
	 * @param array $data <Map>泛型 
	 */
	public function updateOrderMemberId($data) {
		$this->interface->setNeedCache(false);
		$this->interface->setMethod('POST');
		return $this->interface->call('sp/courier/updateOrderMemberId', array(),$data);
	}
	
	/**
	 *
	 * 插入支付确认的系统日志
	 * @param array $data <Map>泛型 
	 */
	public function insertAdminRecord($data) {
		$this->interface->setNeedCache(false);
		$this->interface->setMethod('POST');
		return $this->interface->call('sp/courier/insertAdminRecord', array(),$data);
	}
	
	/**
	 *
	 * 根据用户ID获取订单信息
	 * @param array $data <Map>泛型 
	 */
	public function getOrdersByMemberId($data) {
		$this->interface->setNeedCache(false);
		$this->interface->setMethod('POST');
		return $this->interface->call('member/order/getOrders', array(),$data);
	}
}
