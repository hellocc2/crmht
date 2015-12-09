<?php
namespace Model;
/**
 * 列表页topselling数据层
 * @Jerry Yang<yang.tao.php@gmail.com>
 *
 */
class TopSelling extends Base{

	/**
	 * 获取topselling商品信息
	 *
     * @param int $returnNum 返回商品个数
	 */
    public function getTopSelling($categoryId,$returnNum=4)
    {
        $interface = $this->getInterface();
        $interface->setNeedCache(true);
        return $interface->call('products/products/getProductTopSelling',array('returnNum'=>$returnNum,'languageCode'=>SELLER_LANG,'categoryId'=>$categoryId));
    }
	
}