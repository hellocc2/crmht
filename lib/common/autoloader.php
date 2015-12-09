<?php
namespace Lib\common;
/**
 * 类库自动加载规则及注册.可编写相应的加载方法并注册
 * @author suchao<suchaoabc@163.com>
 * @since 2011-07-05
 */
class AutoLoader{  
    /**
     * 通过命名空间进行分级加载.每级的命名空间名即目录名.而目录名全为小写.类文件名与类名相同.如:<br />命名空间Lib\Cache\Manager对应的类文件全目录为lib\cache\Manager.php
     * @param string $name 命名空间名及类名
     * @uses ROOT_PATH
     */
    public static function loadByNameSpace($name)
    {
        $nameWord = explode('\\', $name);
        if(count($nameWord) < 2)
        {//命名空间加类名的长度一般必须大于二级
            return false;
        }
        $className = array_pop($nameWord);
        $dirPath = ROOT_PATH.strtolower(implode(DIRECTORY_SEPARATOR, $nameWord));
        $filePath = $dirPath . DIRECTORY_SEPARATOR . $className . '.php';
        if(file_exists($filePath))
        {
           require $filePath;
           return true; 
        }
        else
        {
            return false;
        }
        
    }   
	
	/**
	 * 根据语种名称自动加载语言包
	 * @use SELLER_LANG,ROOT_PATH,LANG_ROOT
	 */
    public static function loadLang($name)
    {
    	if($name != 'LangPack') return false;
        $filePath = LANG_ROOT.DIRECTORY_SEPARATOR.SELLER_LANG.DIRECTORY_SEPARATOR.'Lang.php';
        if(file_exists($filePath))
        {
           require $filePath;
           return true; 
        }
        else
        {
            return false;
        }
        
    }   	
}
spl_autoload_register(array('Lib\common\AutoLoader','loadByNameSpace'));
spl_autoload_register(array('Lib\common\AutoLoader','loadLang'));