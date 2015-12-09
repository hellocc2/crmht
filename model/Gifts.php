<?php
namespace Model;
/**
 * 各种导航所需的数据层
 * @author yangyi
 * 
 */

class Gifts extends Base {
	/**
	 * 获取详情页的礼品
	 */
	public function queryViewGifts($data){
		$interface = $this->getInterface ( 'json' ); //在model中实例化数据接口
		$interface->setNeedCache(false);
		$interface->setMethod('POST');
		return $interface->call ( 'sp/shoppingCart/queryViewGifts',array(),$data );
	}
}
