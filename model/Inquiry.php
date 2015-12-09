<?php
namespace Model;
/**
 * inquiry的数据层
 * 
 */

class Inquiry extends Base {
	/**
	 * 
	 * 咨询的默认语言
	 * @var string
	 */
	private $languageCode = SELLER_LANG;
	/**
	 * 
	 * CRM的reason默认语言
	 * @var string
	 */
	private $crmLanguageCode = 'en-uk';
	
	/**
	 * 获取空查询关键词
	 * 
	 */
	public function InsertData($inquiryData=array()) {
		//在model中实例化数据接口
		$interface = $this->getInterface ( 'json' ); 
		$interface->setNeedCache(false);
		$interface->setMethod('POST');
		$data = array();
		foreach ($inquiryData as $key => $value )
		{
			$data['iq.'.$key] = $value;
		}
		unset($inquiryData);
		//$data['iq.languageCode'] = $this->languageCode;	
		$data['iq.crmLanguageCode'] = $this->crmLanguageCode;	
		//时间
		$data['iq.submitTime'] = time();		
		return $interface->call ( 'products/inquiry/submitInquiry', array() ,$data );
	}
	
	/**
	 * 
	 * 更新inquiry的状态
	 * @param unknown_type $data
	 */
	public function UpdateData($data){
		//在model中实例化数据接口
		$interface = $this->getInterface ( 'json' ); 
		$interface->setMethod('POST');
		$interface->setNeedCache(false);
		return $interface->call ( 'products/inquiry/updateInquiryStatus', array(), $data );
	}
	
	/**
	 * 
	 * 获取咨询类型:咨询、投诉
	 * @param array $data
	 */
	public function getAdvisoryCategory($data = array()){
		//在model中实例化数据接口
		$interface = $this->getInterface ( 'json' ); 
		$interface->setNeedCache(true);
		$data['languageCode'] = $this->languageCode;	
		return $interface->call ( 'products/inquiry/findInquiryCategory', $data );
	}
	
}