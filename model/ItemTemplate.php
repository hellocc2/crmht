<?php
namespace Model;
/**
 * FileName:ItemTemplate.php
 * 详细页模板相关接口
 * Author:@{chengjun cgjp123@163.com}
 * Date:@{2011-12-8 09:47:35}
 */
class ItemTemplate extends Base{
	/**
	 * 
	 * 商品终端页根据商品ID，语言编码返回对用模块信息
	 * @param array $Search_criteria
	 */
	public function getTagModule($Search_criteria=array()){
		$interface = $this->getInterface ( 'json' ); //在model中实例化数据接口
		$interface->setNeedCache(true);
		$Search_criteria['languageCode'] = SELLER_LANG;
		return $interface->call ( 'products/productTemplate/tagModule', $Search_criteria );
	}
	
	/**
	 * 
	 * 商品终端页根据商品ID，语言编码返回对应定制参数模板信息
	 * @param array $Search_criteria
	 */
	public function getCustomParameters($Search_criteria=array()){
		$interface = $this->getInterface ( 'json' ); //在model中实例化数据接口
		$interface->setNeedCache(true);
		$Search_criteria['languageCode'] = SELLER_LANG;
		return $interface->call ( 'products/productTemplate/customParameters', $Search_criteria );
	}
}