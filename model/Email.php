<?php
namespace Model;
/**
 * 邮件订阅数据层
 * @Lin Jiang<jiang.lin.person@gmail.com>
 *
 */
class Email extends Base{

	/**
	 * 订阅邮件
	 *
	 * @param String $email 邮件地址
     * @param String $firstName 客户姓名
	 */
    public function sentEmail($firstName='',$email='')
    {
        $interface = $this->getInterface();
        $interface->setNeedCache(false);
        $interface->setMethod('POST');
        return $interface->call('products/email/findOrAddEmail',array(),array('firstName'=>$firstName,'email'=>$email,'webSiteId'=>1,'languageCode'=>SELLER_LANG));
    }
}