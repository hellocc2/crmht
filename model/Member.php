<?php
namespace Model;
/**
 * 购物流程相关Model
 * @Jiang Lin<jianglin@milanoo.com>
 *
 */
class Member extends Base {
    public function __construct() {
        $this->interface = $this->getInterface();
    }

    /**
     * 根据用户ID获取地址列表
     *
     * @param array $data 数据
     */
    public function addMember($data) {
    	$this->interface->setNeedCache(false);
    	$this->interface->setMethod('POST');
        return $this->interface->call('sp/member/addMember',array(), $data);
    }
	
	public function getMember($data) {
		$this->interface->setNeedCache(false);
        return $this->interface->call('sp/member/memberLogin', $data);
    }
}
