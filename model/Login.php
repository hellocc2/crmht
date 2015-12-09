<?php
namespace Model;
/**
 * 各种导航所需的数据层
 * @author wujianjun
 * 
 */

class Login extends Base {
	public function __construct() {
        $this->interface = $this->getInterface();
    }
	/**
	 * 会员注册
	 */
	public function memberLogin($Data) {
		$this->interface->setMethod('POST');
		$this->interface->setNeedCache(false);
		return $this->interface->call ( 'sp/member/memberLogin',array(), $Data );
	}
}