<?php
namespace Model;
/**
 * 购物流程相关Model
 * @Jiang Lin<jianglin@milanoo.com>
 *
 */
class HelpCenter extends Base {
    public function __construct() {
        $this->interface = $this->getInterface();
    }

    /**
     * 帮助中心
     *
     * @param array $data 数据
     */
    public function getHelp($data=array()) {
    	$this->interface->setNeedCache(true);
        return $this->interface->call('member/help/helpCenter', $data);
    }
    
    /**
     * 提交咨询
     */
    public function sentAsk($data=array()){
    	$this->interface->setNeedCache(false);
    	return $this->interface->call('member/advisory/addAdvisory', $data);
    }
    
    /**
     * 获取咨询类型
     * @return Ambigous <object, boolean, multitype:, unknown>
     */
    public function getConsultation($data=array()){
    	$this->interface->setNeedCache(true);
    	return $this->interface->call('member/advisory/findConsultation', $data);
    }
}
