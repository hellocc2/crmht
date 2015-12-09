<?php
namespace Model;
/**
 * 各种导航所需的数据层
 * @author Su Chao<suchaoabc@163.com>
 *
 */
class Navigator extends Base{
    /**
     * 获取顶部导航的数据
     * @param int $depth 需要的分类深度.如:0 所有分类,1 只获取顶级分类,3 获取顶级至第三级的目录
     * @param int $cacheMaxLifeTime 数据的最大缓存时间(单位秒).默认300秒.
     */
    public function getNav($parentCategoryId=0,$returnChildNum='-1:-1:-1',$cacheMaxLifeTime=300)
    {
        $interface = $this->getInterface();   
        $interface->setNeedCache(true);
		//$m = \Lib\Cache::init();
        return $interface->call('products/products/getProductsCategory',array('parentCategoryId'=>$parentCategoryId,'languageCode'=>SELLER_LANG,'returnChildNum'=>$returnChildNum));
    }
    
	public function getPid($categoryId=0)
    {
        $interface = $this->getInterface();
        $interface->setNeedCache(true);
		//$m = \Lib\Cache::init();
        return $interface->call('products/products/getParentIdFromCategoryId',array('categoryId'=>$categoryId));
    }
}