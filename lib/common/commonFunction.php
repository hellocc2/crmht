<?php
    /**
     * 公用方法
     */ 
     namespace Lib\common;
     class commonFunction{           
                        
         /**
          * js跳转url
          */
            function ajaxJumpTo($msg,$url){
                 echo "<script language='javascript' type='text/javascript'>";
                 if(!empty($msg)){
                    echo "alert('{$msg}');";
                 }                 
                 echo "window.parent.mainFrame.location.href='$url'";
                 echo "</script>";
            }
            
          /**
           *
           */ 
           function getServiceData($module, $action, $param, $method = 'GET', $namespace = '') {
            	if (empty ( $namespace )) {
            		$url = JAVA_WEBSERVICE_URL.'/sns/';
            	} else {
            		$url = JAVA_WEBSERVICE_URL;
            	}
            	$url = rtrim ( $url, '?\/' );
            	if (is_string ( $module ) && is_string ( $action )) {
            		if (! empty ( $namespace )) {
            			$url .= '/' . $namespace;
            		}
            		$url .= '/' . $module . '/' . $action . '.htm';
            	}
            	
            	if ($method == 'GET') {
            		$appendedValues = array ();
            		if (is_array ( $param )) {
            			foreach ( $param as $k => $v ) {
            				$appendedValues [] = $k . '=' . urlencode ( $v );
            			}
            		}
            		$appendedStr = implode ( '&', $appendedValues );
            		if (! empty ( $appendedValues )) {
            			$url .= '?' . $appendedStr;
            		}
            	}
            	
            	$ch = curl_init ();
            	if ($method == 'POST') {
            		curl_setopt ( $ch, CURLOPT_POST, true );
            		curl_setopt ( $ch, CURLOPT_POSTFIELDS, $param );
            	}
            	 //print_r($url);die;
            	// echo '<br />';
            	curl_setopt_array ( $ch, array (
            			CURLOPT_URL => $url,
            			CURLOPT_RETURNTRANSFER => true,
            			CURLOPT_CONNECTTIMEOUT => 5 
            	) );
            	$response = curl_exec ( $ch );
                if(curl_error($ch)){//出错则显示错误信息
	                   print curl_error($ch);
	               }
            	curl_close ( $ch );
            	$str = gzuncompress ( $response );
            	
            	$responseArr = json_decode ( $str, true );
            	
            	if ($responseArr ['code'] == '0') {
            		return $responseArr;
            	}
            }
            
             /**
         * 取得上传文件的后缀类型
         */ 
            function getExt($filename) {
                $pathinfo = pathinfo($filename);
                return $pathinfo['extension'];
            }
     }
?>