<?php
namespace Model;
/**
 * 各种导航所需的数据层
 * @author chengjun
 * 
 */

class Search extends Base {
	/**
	 * 获取空查询关键词
	 * 
	 */
	//$parentCategoryId = 1615, $languageCode, $pageSize=NULL, $pageNo, $searchContent, $priceRange, $baseColor, $propertyArray, $brandId
	public function getKeywordList($Search_criteria=array()) {
		$interface = $this->getInterface ( 'json' ); //在model中实例化数据接口
		$interface->setNeedCache(true);
		$Search_criteria['languageCode'] = SELLER_LANG;
		return $interface->call ( 'products/keyword/getRecommendKeyword', $Search_criteria );
	}
	
	public function getSearchKeywords($searchContent,$returnNum=10){
		$interface = $this->getInterface ( 'json' ); //在model中实例化数据接口
		$interface->setNeedCache(true);
		$SearchParms = array(
			'languageCode'=>SELLER_LANG,
			'searchContent'=>$searchContent,
			'returnNum'=>$returnNum,
		);
		return $interface->call ( 'products/keyword/getKeywordBySearchContent', $SearchParms );
	}
}