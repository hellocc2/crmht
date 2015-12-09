<?php
namespace Helper;
class Upload{
	private static $imgType = array('jpg','gif','png');
	public static function imageUpload($img,$imgSize,$imgName,$size=2048000,$serverUrl='fs/file/uploadComment.htm'){
		$fileExtension = self::getExtension($imgName);
		if(!in_array($fileExtension, self::$imgType)){
			return '10000'; //图片格式不对
		}
		if($imgSize > $size){
			return '10001'; //超过最大图片大小限制
		}
		$data = array(
			'fileData'=>'@'.$img,	
			'fileType'=>$fileExtension,
				
		);
		$result = self::getServiceData($data,'POST',$serverUrl);
		return $result;
	}
	
	private static function getExtension($file){
		return strtolower(substr(strrchr($file, '.'), 1));
	}
	
	private static function getServiceData($param,$method = 'POST',$uploadUrl) {
		//$url = self::$uploadUrl;
		$url = COMMENT_UPLOAD_URL.$uploadUrl;
		$url = rtrim($url, '?\/');
		if($method == 'GET') {
			$appendedValues = array();
			if(is_array($param)) {
				foreach($param as $k => $v) {
					$appendedValues[] = $k . '=' . urlencode($v);
				}
			}
			$appendedStr = implode('&', $appendedValues);
			if(!empty($appendedValues)) {
				$url .= '?' . $appendedStr;
			}
		}
	
		$ch = curl_init();
		if($method == 'POST') {
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
		}
		//print_r($url);exit;
		
		curl_setopt_array($ch, array(CURLOPT_URL => $url, CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 10,));
		$response = curl_exec($ch);
		if($errNo = curl_errno($ch)) {
			$handle = fopen(ROOT_PATH . 'data/curl.txt', 'a');
			$info = curl_getinfo($ch);
			fwrite($handle, 'error--');
			fwrite($handle, $url."\n");
			fwrite($handle, var_export ($info,true)."\n\r");
			fwrite($handle, "end--\n\r");
			fclose($handle);
	
			curl_close($ch);
			return false;
		}
		curl_close($ch);
		$responseArr = json_decode($response,true);
		//print '<pre>';print_r($responseArr);
		if($responseArr['message'][0]=='100001') {
			return $responseArr;
		} else {
			$handle = fopen(ROOT_PATH . 'data/curl.txt', 'a');
			$info = curl_getinfo($ch);
			fwrite($handle, 'request--');
			fwrite($handle, $url."\n");
			fwrite($handle, var_export ($responseArr,true)."\n\r");
			fwrite($handle, "end--\n\r");
			fclose($handle);
			return false;
		}
	
	} 
}