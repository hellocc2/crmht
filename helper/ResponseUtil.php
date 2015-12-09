<?php
namespace Helper;
/**
 * 服务端响应时的应用辅助库
 * @author Su Chao<suchaoabc@163.com>
 *
 */
class ResponseUtil{
    /**
     * seo相关的url重写
     * @param array $rawParams 重写时模版传入的参数.参数的详细说明见{@link smarty_function_rewrite()}
     */
    public static function rewrite($rawParams)
    {
        $url = '';
        $isxs='yes';
        $html='.html';
        $seo='';
        $IS_REWRITE_URL='';
        $rewriteDirName='';
        $force_seo=0;
        $forceNoDomainPrepend = 0;
        $domain = null;
        $protocol = null;
        $style = '';
        extract($rawParams);
        $seo = html_entity_decode($seo,ENT_QUOTES,'UTF-8');
        $seo = self::replaceSpecialCharacters($seo);
    	if (SELLER_LANG!='en-uk' && SELLER_LANG!='fr-fr'&& SELLER_LANG!='pt-pt' && !$force_seo){$seo='';}
    	if ($IS_REWRITE_URL=="") $IS_REWRITE_URL=IS_REWRITE_URL;
    	$rewriteBaseDirName = '';
    	if(SELLER_LANG != 'en-uk')
    	{
    		$rewriteBaseDirName = LangDirName.'/';
    	}
    	if(!empty($rewriteDirName))
    	{
    		$rewriteBaseDirName .= trim($rewriteDirName,' /').'/';
    	}
    
    	if($IS_REWRITE_URL!=0)
    	{
    		$module=$action='index';
    		$params='';
    		$url_array=explode("?",$url);

    	    $Garray = self::parseStr($url_array[1]);
    		if(isset($Garray['module']))
    		{
    		    $module = $Garray['module'];  
    		    unset($Garray['module']);
    		}
    	    
    		if(isset($Garray['action']))
    		{
    		    $action = $Garray['action'];    		
    		    unset($Garray['action']);    		    
    		}
    		
    		if(isset($Garray['style']))
    		{
    			$style = $Garray['style'];//用于判断面包屑的spotlight是否要用重写规则
    			unset($Garray['style']);
    		}
            
    		$params = array();
    		foreach ($Garray as $k=>$v)
    		{//@todo 不支持二维以上的数组
    		    if(is_string($v))
    		    {
    		        $params[] = $k.'-'.urlencode($v);
    		    }
    		    else if(is_array($v))
    		    {
    		        foreach ($v as $v_i)
    		        {
    		            $params[] = $k. '-'.urlencode($v_i);    		           
    		        }
    		    }
    		}
    		$params = implode('-', $params);

    		$url=$url_array[0];

			if($IS_REWRITE_URL==1)
			{
				$url.='?/';
			}
			elseif($IS_REWRITE_URL==2 && $rewriteDirName!="en")
			{
				$url .= $rewriteBaseDirName;
			}

			$patterns = array("#['’\"\(\)\&\%\$\:\[\]\{\}\+\!\,]#u","#\s*[\-\*\/\.\<\>\:\+×]\s*|\s+#u",'#-{2,}#u','#\s+#u','#%#u');
			$replace = array("","-",'-','','%25');
    		if($seo)
    		{
    			$seo=trim($seo);
    			$seo=preg_replace( $patterns,$replace,$seo);
    			$seo.="-";
    		}
    		if( $rewriteDirName !="dhtml")
    		{
    			switch ($module)
    			{
    				case '':
    					$url.=$seo;
    					if($action!="index" && $action!="") $url.=$action;
    					if($params!="")
    					{
    						if($action!="index" && $action!="") $url.="-";
    						$url.=$params;
    					}
    					if(($action!="index" && $action!="") || $params!="") $url.=$html;
    					break;
    				case 'index':
    					$url.=$seo;
    					if($action!="index" && $action!="") $url.=$action.$html;
    					break;
    				default:
    					if($seo) $seo.="module-";
    					$url.=$module.'/'.$seo.$action;
    					if(($seo!="" || $action!="")&&$params!="") $url.='-';
    					if($params!="") $url.=$params.$html;
    					elseif($action!="") $url.=$html; 
    			}
    		}
    		else
    		{
    			if($seo) $seo.="module-";
    			$url.=$module.'/'.$seo.$action;
    			if(($seo!="" || $action!="")&&$params!="") $url.='-';
    			if($params!="") $url.=$params.$html;
    			elseif($action!="") $url.=$html;
    			if(!$action && !$params && $rewriteDirName=="dhtml") $url.='index'.$html;
    		}
    	}
    	if(!empty($domain)) {
			$url = rtrim($domain, '/') . rtrim($url, '/');
		} else if(!$forceNoDomainPrepend) {
			if($protocol == 'https') {
				$url = ROOT_URLS . $url;
			} else if($protocol == 'http') {
				$url = ROOT_URLD . $url;
			} else {
				$url = ROOT_URL . $url;
			}
		}

    	if($isxs=='yes') echo $url;
    
    	else return $url;        
    }
    
    /**
     * 将GET参数格式的字符串转换成关联数组.与parse_str()方法相比,parseStr()不会对key的特殊字符进行处理,如:将空格转换成下划线
     * @param string $url
     * @return array
     */
    public static function parseStr($url)
    {
        $url = explode('?', $url);
        $url = array_pop($url);
        $com = explode('&', $url);
        $url = array();
        foreach ($com as $v)
        {
            $v = explode('=', $v);
            if(!empty($v[0]))
            {
                $varName = preg_replace('#([^\[\]]+)|(\[[^\]\[]*\])#', '\1\2', $v[0]);                
                if(!isset($v[1])) $v[1] = '';
                $varNameSub = strstr($varName, '[');
                if(false !== $varNameSub)
                {
                    $varName = strstr($varName, '[',true);
                    eval('$url[\''.addslashes($varName).'\']'.$varNameSub.'=$v[1];');                    
                }
                else 
                {
                    $url[$varName] = $v[1]; 
                }
            }
        }        
        return $url;
    }
	
	/**
	 * 将法语特殊字符转换成英文字符
	 *
	 * @param $str string       	
	 * @return string
	 */
	public static function replaceSpecialCharacters($str) {
		switch (SELLER_LANG) {
			case 'fr-fr' :
				$replace_arr = array ('À' => 'A', 'à' => 'a', 'à' => 'a', 'Â' => 'A', 'â' => 'a', 'È' => 'E', 'è' => 'e', 'É' => 'E', 'é' => 'e', 'Ê' => 'E', 'ê' => 'e', 'Ë' => 'E', 'ë' => 'e', 'Î' => 'I', 'î' => 'i', 'Ï' => 'I', 'ï' => 'i', 'ç' => 'c', 'ô' => 'o', 'Œ' => 'OE', 'œ' => 'oe', 'Ù' => 'U', 'ù' => 'u', 'Û' => 'U', 'û' => 'u', 'Ü' => 'U', 'ü' => 'u', 'µ' => 'u' );
				$str = strtr ( $str, $replace_arr );
				break;
			case 'pt-pt' :
				$replace_arr = array ('Á' => 'A', 'á' => 'a', 'Ã' => 'A', 'ã' => 'a', 'À' => 'A', 'à' => 'a','Â' => 'A','â' => 'a', 'Ç' => 'C', 'ç' => 'c','Ê' => 'E','ê' => 'e','É' => 'E', 'é' => 'e', 'Í' => 'I', 'í' => 'i', 'Ó' => 'O', 'ó' => 'o', 'Õ' => 'O', 'õ' => 'o', 'Ô' => 'O', 'ô' => 'o','Ú' => 'U','ú' => 'u','€'=>'');
				$str = strtr ( $str, $replace_arr );
				break;
		}
		return $str;
	}
    
    /**
     * 返回已经发送或者准备发送的header信息
     * @param string $name header名称
     * @return string header的内容.如果header不存在则返回null
     */
    public static function getHeader($name)
    {
    	$headers = headers_list();
    	foreach ($headers as &$header)
    	{
    		if(stripos($header, $name . ':') === 0)
    		{
    			return str_ireplace($name . ':', '', $header);
    		}
    	}
    	return null;
    }
	
	public static function formatArrSpe($arr)  {
		foreach($arr as $key => $item) {
			if(is_array($item))  {
				$item = self::formatArrSpe($item);
			} else {
				$item = html_entity_decode($item, ENT_COMPAT, 'UTF-8');
			}
			$arr[$key] = $item;
		}
		return $arr;
	}
}