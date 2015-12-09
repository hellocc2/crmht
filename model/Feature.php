<?php
namespace Model;
/**
 * 专题模型
 * @Jerry.Yang<yang.tao.php@gamil.com>
 *
 */
class Feature extends Base{

	/**
	 * 获取专题信息
	 *
     * @param int $returnNum 返回对应专题信息
	 */
   
	public function getFeatureInfoById($id,$customUrl=''){
		$interface = $this->getInterface();
		$interface->setNeedCache(false);
		$interface->setMethod('POST');
        return $interface->call('/products/promotion/findPromotionProducts','',array('featureId'=>$id,'languageCode'=>SELLER_LANG,'customUrl'=>$customUrl));
	}
}