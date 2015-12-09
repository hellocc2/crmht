<?php
namespace Model;
/**
 * FileName:TopQuery.php
 * 终端页获取其他商品数据
 * Author:@{chengjun cgjp123@163.com}
 * Date:@{2011-11-24 09:34:49}
 */
class ItemOtherProducts extends Base{
	/**
	 * 
	 * 商品终端页顶部根据外链关键词获取推荐商品
 	 * 如果不能取得外链关键词则根据商品id返回同类目商品
	 * @param array $Search_criteria
	 */
	public function getTopQuery($Search_criteria=array()){
		$interface = $this->getInterface ( 'json' ); //在model中实例化数据接口
		$interface->setNeedCache(true);
		$Search_criteria['languageCode'] = SELLER_LANG;
		return $interface->call ( 'products/productRecommend/topQueryRecommend', $Search_criteria );
	}
	
	/**
	 * 
	 * 用户可能会购买的推荐商品
	 * @param array $Search_criteria
	 */
	public function getReconmmend($Search_criteria=array()){
		$interface = $this->getInterface ( 'json' ); //在model中实例化数据接口
		$interface->setNeedCache(true);
		$Search_criteria['languageCode'] = SELLER_LANG;
		return $interface->call ( 'products/productRecommend/mayPurchaseRecommend', $Search_criteria );
	}
	
	/**
	 * 
	 * 获取用户最近浏览商品，并根据最近浏览商品获取相关推荐商品
	 * @param array $Search_criteria
	 */
	public function getHistoryRecommend($Search_criteria=array()){
		$interface = $this->getInterface ( 'json' ); //在model中实例化数据接口
		$interface->setNeedCache(false);
		$Search_criteria['languageCode'] = SELLER_LANG;
		return $interface->call ( 'products/productRecommend/recentHistoryRecommend', $Search_criteria );
	}
	
} 