<?php 
namespace Lib\common;
class Debug{   
   /**
    * 存储所有调试信息
    * @var array
    */
   private static $debugInfo = array();
   /**
    * 输出当当前调用此方法时内存最高占用及脚本执行时间信息
    * @param  boolean $return 是否返回.否则将直接输出接口.默认(true)
    * @return 
    */
   public static function consumption($return=true)
   {
        $str = '';
        $str .= '<div class="debug_consume_section">memory peak usage&nbsp;'.(memory_get_peak_usage()/1024/1024).'m&nbsp;&nbsp;';
        $str .= 'proceeded in &nbsp;'.(sprintf('%.8f',microtime(true)-SCRIPT_TIME_START)*1000).'&nbsp;ms';
        $str .= '</div>';
        if(!$return)
        {
            echo $str;
        }   
        else
        {
            return $str;
        }
   }
   public static function printTrace($format='plain')
   {
       if($format == 'html')
       {
           echo '<pre>';
           echo debug_print_backtrace();
           echo '</pre>';
       }
       else
       {
           debug_print_backtrace();
       }
   }
   
   /**
    * 添加调试信息.当$title已经存在时,则替换原来的信息
    * @param string $title 信息标题
    * @param string $msg 信息内容
    * @param boolean $isArray $title所指定的元素是否为数组.当为true时,每次设置$title的值时,以push的方式存入
    */
   public static function setInfo($title,$msg,$isArray=false)
   {
    	if($isArray)
    	{
    		self::$debugInfo[$title][] = $msg;
    	}   
    	else
    	{
    		self::$debugInfo[$title] = $msg;
    	}   	
   }
   
   /**
    * 输出调试信息
	* @todo 完善各级别的调试信息输出
    */
   public static function outPut()
   {        
   		$contentType = \Helper\ResponseUtil::getHeader('content-type');
   		$contentType = explode(';', $contentType);
   		$contentType = trim($contentType[0]);
   		if(DEBUG_LEVEL === 0)
   		{
   			self::$debugInfo['IncludedFiles'] = get_included_files();
   		}   		
   		switch ($contentType)
   		{
   			case 'text/html' :
   			case 'text/plain' :
   				continue;
   			case 'application/json' :
   				$str = ob_get_contents();			
   				$debugStr = json_encode(self::$debugInfo);
   				
   				if(strpos($str,'[') === 0)
   				{
   					$str = substr($str, 0, strlen($str)-1).',{"debugInfo":'.$debugStr.'}]';
   				}
   				else if(strpos($str,'{') === 0)
   				{
   					$str = substr($str, 0, strlen($str)-1).',"debugInfo":'.$debugStr.'}';;
   				}
   				else
   				{
   					$str = $debugStr;
   				}
   				ob_end_clean();
   				echo $str;
   				return;
   			defautl :
   				return;
   		}

       $str = '<style type="text/css">@import url("'.IMAGE_URL.'debug.css");</style>';     
       if(DEBUG_LEVEL == 0 || DEBUG_LEVEL == 2)
       {    
           $str .= '<div id="milanooDebugFrame">'.self::consumption().self::infoToStr(self::$debugInfo);           
       }   
       else if(DEBUG_LEVEL === 1) 
       {
           $str .= '<div id="milanooDebugFrame">'.self::consumption();
       } 
       $str .= '</div>';
       echo $str;   
   }
   /**
    * 将信息数组转成字符串
    * @param array|string $info
    */
   private static function infoTostr($info)
   {
       $str = '';
       foreach($info as $title => $msg)
       {
           if(is_array($msg))
           {
               $str .= '<ul><li class="debug_item_title">'.htmlspecialchars($title).':</li><li class="debug_item_value">'.self::infoToStr($msg).'</li></ul>';                   
           }
           else 
           {
               $msg = preg_replace('#(\n|\r\n)+#', '<br />', htmlspecialchars($msg));
               $str .= '<ul><li class="debug_item_title">'.htmlspecialchars($title).':</li><li class="debug_item_value">&nbsp;&nbsp;&nbsp;'.$msg.'</li></ul>';
           }
       }   
       return $str;         
   }
}