<?php
namespace Api;
/**
 * JAVA客户系统API接口
 * 
 **/
//error_reporting(E_ALL);
class Crm {
	/**
	 * http链接超时时间
	 **/
	private $timeout = 30;
	
	/**
	 * 语言转换数组
	 **/
	protected $ToLang = array ('en-uk' => 'English', 'fr-fr' => 'French', 'ja-jp' => 'Japanese', 'es-sp' => 'Spanish', 'de-ge' => 'German', 'it-it' => 'Italian', 'pt-pt' => 'Portuguese', 'ru-ru' => 'Russian', 'ar-ar' => 'Arabic' );
	
	/**
	 * post客户问题咨询提交地址
	 **/
	//测试环境
	//private $addAskUrl = "https://cs5.salesforce.com/servlet/servlet.WebToCase?encoding=UTF-8";
	//正式环境
	private $addAskUrl = "https://www.salesforce.com/servlet/servlet.WebToCase?encoding=UTF-8";
	//private $addAskUrl = "http://119.254.23.59:8080/case-web/wsdl/CaseService.wsdl";
	

	/**
	 * saleforce上的企业id
	 **/
	//测试key
	//private $orgid = "00DO00000005zCF";
	//正式key
	private $orgid = "00D90000000Yjjc";
	

	/**
	 * post客户留言提交地址
	 **/
	private $addRewriteUrl = "";
	//测试环境
	//private $javaReplyWebservice = "http://114.80.110.183:8090/case-web/services/CaseService?wsdl";
	private $javaReplyWebservice = "http://172.20.20.36:8989/case-web/services/CaseService?wsdl ";
	//正式环境
	//private $javaReplyWebservice = "http://119.254.23.59:8080/case-web/wsdl/CaseService.wsdl";
	

	/**
	 * webservice的加密key
	 **/
	private $javaWebserviceKey = "FJDSKJFKD6567SJFKj3452f76889DF";
	
	/**
	 * 提交顾客咨询到SaleForce
	 * 
	 **/
	public function AskToSaleForce($inquiryData) {
		if (empty ( $inquiryData ))
			return false;
		$data = array ('orgid' => $this->orgid, 'external' => 1, '00N9000000228Jl' => $this->ToLang [$inquiryData ['languageCode']], /**提交入口： Milanoo(主站)、Dressinweddin(婚纱垂直站)、Cosplayshow(Cosplay垂直站)、
						 Lolitashow(洛丽塔垂直站)、WAP(手机网站)、APP(苹果IOS设备入口)
		 */
		'00N9000000228OM' => 'Milanoo' );
		//提交的数据字段
		$data_keys = array ('name' => 'memberName', 'email' => 'memberEmail', 'subject' => 'inquiryTitle', 'description' => 'inquiryContent', 'reason' => 'crmInquiryTitle', '00N9000000228Jc' => 'inquiryCaseId', '00N900000022BzK' => 'memberId', '00N90000002YYVF' => 'crmType', '00N9000000228OM'=>'webType' );
		
		foreach ( $data_keys as $key => $value ) {
			if(isset($inquiryData [$value])){
				$data [$key] = htmlspecialchars_decode ( $inquiryData [$value], ENT_QUOTES );
			}
		}
		$result = $this->curlPost ( $this->addAskUrl, $data );
		return $result;
	}
	
	/**
	 * 模拟post提交
	 * 
	 * @param  string   url           提交的URL地址
	 * @param  array    data          提交的参数
	 **/
	protected function curlPost($url, $data) {
		$ssl = substr ( $url, 0, 8 ) == "https://" ? TRUE : FALSE;
		$ch = curl_init ( $url );
		$parms = array ();
		foreach ( $data as $key => $v ) {
			$parms [] = $key . "=" . urlencode ( $v );
		}
		$ch = curl_init ( $url );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt ( $ch, CURLOPT_POST, 1 );
		curl_setopt ( $ch, CURLOPT_POSTFIELDS, implode ( "&", $parms ) );
		if ($ssl) {
			curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, 1 );
			curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
		}
		$response = curl_exec ( $ch );
		if (curl_errno ( $ch ) != 0) {
			return FALSE;
		}
		curl_close ( $ch );
		return TRUE;
	}

}