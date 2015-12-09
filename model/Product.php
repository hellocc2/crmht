<?php
namespace Model;
/**
 * 各种导航所需的数据层
 * @author yangyi
 * 
 */

class Product extends Base {
	/**
	 * 获取某个商品列表分类
	 * 
	 */
	//$parentCategoryId = 1615, $languageCode, $pageSize=NULL, $pageNo, $searchContent, $priceRange, $baseColor, $propertyArray, $brandId
	public function getProductList($Search_criteria=array()) {
		$interface = $this->getInterface ( 'json' ); //在model中实例化数据接口
		$interface->setNeedCache(true);
		$Search_criteria['pcs.languageCode'] = SELLER_LANG;
		$interface->setMethod('POST');
		//print_r($Search_criteria);exit;
		return $interface->call ( 'products/products/getProductsCategorySearch',array(), $Search_criteria );
	}
	
	/**
	 * 
	 * 获取日文关键字
	 * @param array $Search_criteria
	 */
	public function getJpKeyword($Search_criteria=array()) {
		$interface = $this->getInterface ( 'json' ); //在model中实例化数据接口
		$interface->setNeedCache(true);
		//$interface->setMethod('POST');
		//print_r($Search_criteria);exit;
		return $interface->call ( 'products/keyword/getJpKeywordNameById', $Search_criteria );
	}
	
	/**
	 * 获取商品详细信息
	 *
	 * @param array $data
	 * @return unknown
	 */
	public function getProductsDetails($data = array()){
		$interface = $this->getInterface ( 'json' ); //在model中实例化数据接口
		$interface->setNeedCache(false);
		$data['languageCode'] = SELLER_LANG;
		return $interface->call ( 'products/products/productDetails', $data );
	}
	
	/**
	 * 获取商品的自定参数
	 *
	 * @param array $data
	 * @return unknown
	 */
	public function getProductsCustomParameters($data = array()){
		$interface = $this->getInterface ( 'json' ); //在model中实例化数据接口
		$interface->setNeedCache(true);
		$data['languageCode'] = SELLER_LANG;
		return $interface->call ( 'products/productTemplate/customParameters', $data );
	}
	
	/**
	 * 获取商品的模块信息
	 *
	 * @param array $data
	 * @return unknown
	 */
	public function getProductsTagModule($data = array()){
		$interface = $this->getInterface ( 'json' ); //在model中实例化数据接口
		$interface->setNeedCache(true);
		$data['languageCode'] = SELLER_LANG;
		return $interface->call ( 'products/productTemplate/tagModule', $data );
	}
	
	/**
	 * 
	 * 获取高级搜索产品信息
	 * @param array $Search_criteria
	 */
	public function getProductsFromSop($Search_criteria=array()){
		$interface = $this->getInterface ( 'json' ); //在model中实例化数据接口
		$interface->setNeedCache(true);
		$interface->setMethod('GET');
		if(!isset($Search_criteria['sop.languageCode'])){
			$Search_criteria['sop.languageCode'] = SELLER_LANG;
		}
		return $interface->call ( 'products/sop/getProductsFromSop',$Search_criteria,array(),true,'.json' );
	}
	
	/**
	 * 
	 * 新到货热销产品集合模式
	 * @param array $Search_criteria
	 */
	public function getProductsInStepType($Search_criteria=array()){
		$interface = $this->getInterface ( 'json' ); //在model中实例化数据接口
		$interface->setNeedCache(false);
		$interface->setMethod('POST');
		if(!isset($Search_criteria['languageCode'])){
			$Search_criteria['languageCode'] = SELLER_LANG;
		}
		return $interface->call ( 'nb/product/getProductStep',array() ,$Search_criteria);
	}
	
	/**
	 * 
	 * 新到货热销产品列表模式
	 * @param array $Search_criteria
	 */
	public function getProductsInListType($Search_criteria=array()){
		$interface = $this->getInterface ( 'json' ); //在model中实例化数据接口
		$interface->setNeedCache(true);
		//$interface->setMethod('POST');
		if(!isset($Search_criteria['languageCode'])){
			$Search_criteria['languageCode'] = SELLER_LANG;
		}
		return $interface->call ( 'nb/product/getProductList',$Search_criteria);
	}
	
	/**
	 * 
	 * 新到货热销商品类目
	 * @param array $Search_criteria
	 */
	public function getProductsCate($Search_criteria=array()){
		$interface = $this->getInterface ( 'json' ); //在model中实例化数据接口
		$interface->setNeedCache(false);
		$interface->setMethod('POST');
		if(!isset($Search_criteria['languageCode'])){
			$Search_criteria['languageCode'] = SELLER_LANG;
		}
		return $interface->call ( 'nb/product/getProductCategory',array(), $Search_criteria);
	}
	
	/**
	 * 
	 * DCP商品及类目加载
	 * @param array $Search_criteria
	 */
	public function getProductsCateDcp($Search_criteria=array()){
		$interface = $this->getInterface ( 'json' ); //在model中实例化数据接口
		$interface->setNeedCache(true);
		if(!isset($Search_criteria['languageCode'])){
			$Search_criteria['languageCode'] = SELLER_LANG;
		}
		return $interface->call ( 'nb/dcp/getProductsCategoryDcp', $Search_criteria);
	}
	
	/**
	 * DCP分类标记
	 * @param int $categoryId
	 * @return Ambigous <object, boolean, multitype:, mixed>
	 */
	public function getNavShowType($categoryId=0)
	{
		$interface = $this->getInterface();
		$interface->setNeedCache(true);
		//$m = \Lib\Cache::init();
		return $interface->call('nb/dcp/productsCategoryShowType',array('categoryId'=>$categoryId));
	}
	
	/**
	 * 获取商品的运费
	 */
	public function getProducsTransportPrice($data)
	{
		$interface = $this->getInterface ( 'json' ); //在model中实例化数据接口
		$interface->setNeedCache(false);
		return $interface->call ( 'sp/transPort/getProducsTransportPrice',$data );
		//$interface->setMethod('POST');
		//return $interface->call ( 'sp/transPort/getProducsTransportPrice',array(), $data );
	}
	
	public function recentHistoryRecommend($data){
		$interface = $this->getInterface();
		$this->interface->setNeedCache(true);
		return $interface->call('products/productRecommend/recentHistoryRecommend',$data);
	}

	/**
	 * 根据商品ID获取商品信息
	 */
	public function getProductsByIds($data){
		$interface = $this->getInterface ( 'json' ); //在model中实例化数据接口
		$interface->setNeedCache(true);
		if(!isset($data['languageCode'])){
			$data['languageCode'] = SELLER_LANG;
		}
		return $interface->call ( 'products/products/getProductsByIds',$data );
	}
	
	/**
	 * 缺货商品
	 */
	public function getArriveEmail($data){
		$interface = $this->getInterface ( 'json' ); //在model中实例化数据接口
		$interface->setNeedCache(true);
		return $interface->call ( 'products/products/arriveEmail',$data );
	}
}
