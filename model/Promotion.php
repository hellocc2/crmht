<?php
namespace Model;
/**
 * 促销数据层
 * @Lin Jiang<jiang.lin.person@gmail.com>
 *
 */
class Promotion extends Base{

	/**
	 * 获取促销数据
	 *
	 * @param String $libkey 促销关键字
     * @param int $returnNum 返回商品个数
	 */
    public function getPromotion($libkey='',$returnNum=4)
    {
        $interface = $this->getInterface();
        $interface->setNeedCache(true);
        return $interface->call('products/promotion/findPromotion',array('libkey'=>$libkey,'returnNum'=>$returnNum,'languageCode'=>SELLER_LANG));
    }
    
    /**
     * 获取每日秒杀促销数据
     * 因为每日秒杀接口参数修改，传的参数与其他促销不一样，所以新写一个方法来处理
     *
     * @param String $libkey 促销关键字
     * @param int $returnNum 返回商品行数
     * @param int $returnNum 返回商品每行个数
     */
    public function getPromotionDailyMadness($libkey='DailyMadness',$row=5,$rowNum=5)
    {
    	$interface = $this->getInterface();
    	$interface->setNeedCache(true);
    	return $interface->call('products/promotion/findPromotion',array('libkey'=>$libkey,'rows'=>$row,'rowNum'=>$rowNum,'languageCode'=>SELLER_LANG));
    }
	
	/**
	 * 获取促销数据
	 *
     * @param int $returnNum 返回商品个数
	 */
	public function getListFeature($returnNum=8){
		$interface = $this->getInterface();
		$interface->setNeedCache(true);
        return $interface->call('products/promotion/findPromotionFeatrue',array('returnNum'=>$returnNum,'languageCode'=>SELLER_LANG));
	}
	
	/**
	 * 推广统计插入
	 *
     * @param String $linkAddressUrl 链接的URL
	 * @param String $refererAddressURL 来源的URL
	 * @param String $promotionType 推广关键字
	 */
	public function sentPromotionUrl($linkAddressUrl='',$refererAddressURL='',$promotionType=''){
		$interface = $this->getInterface();
		$interface->setNeedCache(false);
		$interface->setMethod('POST');
        return $interface->call('products/promotion/urlAccessRecord',array(),array('linkAddressUrl'=>$linkAddressUrl,'refererAddressURL'=>$refererAddressURL,'promotionType'=>$promotionType,'refererAddressURLmd5'=>md5($refererAddressURL)));
	}
	
	/**
	 * 获取专题数据
	 *
	 */
	public function getFeatureData($param=array()){
		$interface = $this->getInterface();
		$interface->setNeedCache(true);
		return $interface->call('/products/promotion/findPromotionProducts',$param);
	}
	
	/**
	 * cashback查询已支付订单(包含商品)
	 *
	 */
	public function findOrderForCashBack($param=array()){
		$interface = $this->getInterface();
		$interface->setNeedCache(true);
		return $interface->call('member/cashback/findOrderAndProductForCashback',$param);
	}
	
	/**
	 * cashback查询已支付订单(不包含商品)
	 *
	 */
	public function findOrderForCashBackNoProducts($param=array()){
		$interface = $this->getInterface();
		$interface->setNeedCache(true);
		return $interface->call('member/cashback/findOrderForCashback',$param);
	}
	
	/**
	 * cashback查询订单商品
	 *
	 */
	public function findOrderProductForCashback($param=array()){
		$interface = $this->getInterface();
		$interface->setNeedCache(true);
		$interface->setMethod('POST');
		return $interface->call('member/cashback/findOrderProductForCashback',array(),$param);
	}
	
	/**
	 * 新增cashback
	 *
	 */
	public function addCashback($param=array()){
		$interface = $this->getInterface();
		$interface->setNeedCache(false);
		$interface->setMethod('POST');
		return $interface->call('member/cashback/addCashback',array(),$param);
	}
}