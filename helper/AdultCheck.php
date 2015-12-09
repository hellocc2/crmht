<?php
namespace Helper;
/**
 * PPC广告，含成人内容目录检查
 * FileName:AdultCheck.php
 * @Author:chengjun <cgjp123@163.com>
 * @Since:2012-5-3
 */
 class AdultCheck {
 	/**
 	*
 	* 捕获结果，默认为false,当验证成功返回true
 	* boolean
 	*/
 	public $getResult = false;
 	
 	/**
 	 * 
 	 * url参数
 	 * @var array
 	 */
 	public $params = array();
 	
 	public function __construct($classId,$filterRules=''){
 		$params = array();
 		$matString = '';
 		if($classId){
 			if(!empty($filterRules)){
 				$this->params = \Helper\RequestUtil::getRewritedUrl();
 				if(!empty($this->params) && !empty($this->params['redirect']) && $this->params['redirect']==true){
 					$this->getResult = false;
 					return;
 				}
 				if(!empty($this->params) && !empty($this->params['Promotion'])){
 					$matString = 'Promotion='.$this->params['Promotion'];
 				}
 				$filterRulesArray = explode('|', $filterRules);
 				if(!empty($filterRulesArray)){
 					foreach($filterRulesArray as $k=>$v){
 						if(!empty($v)){
 							$v = '/^'.$v.'.*$/i';
	 						if(preg_match($v, $matString,$matches)!=false){
	 							$this->getResult = true;
	 							break;
	 						}
 						}
 					}
 				}
 			}
 		}
 		return;
 	}
 	
 	/**
 	 * 
 	 * 解析url
 	 * @param string $url
 	 */
 	public function parseUrl($url){
 		$param = array();
 		$params = array();
 		$param = parse_url($url);
 		if(!empty($param['query'])){
 			$paramArray = explode('&', $param['query']);
 			if(!empty($paramArray)){
 				foreach($paramArray as $k=>$v){
 					if(!empty($v)){
 						$temp = explode('=', $v);
						if(!empty($temp) && !empty($temp[0]) && !empty($temp[1])){
							$params[$temp[0]] = $temp[1];
						}
					}
 				}
 			}
 		}
 		return $params;
 	}
 	
 	/**
 	 * 
 	 * 生成包含验证成功参数的URL
 	 */
 	public function creatUrl(){
 		$url = \Helper\RequestUtil::getUrl();
 		$url .= substr($url,-1)=='&' ? 'redirect=true' : '&redirect=true';
 		return $url;
 	}
 }