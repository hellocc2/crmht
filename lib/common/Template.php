<?php 
namespace Lib\common;
use Lib\smarty\Smarty;
/**
 * 模版引擎管理类
 * @author Su Chao<suchao@163.com>
 * @todo 多引擎支持
 * @since 2011-10-10
 */
class Template{
    /**
     * Smarty 对象 
     * @var Smarty
     */
    protected static $smarty ;
    /**
     * 获取smarty对象
     * @return \Lib\Smarty
     */   
    public static function getSmarty()
    {
        if(self::$smarty instanceof \Lib\smarty\Smarty)
        {
            return self::$smarty;
        }
        
		if (!defined('THEME_ROOT_PATH')) define('THEME_ROOT_PATH', THEME . 'default/');//模板目录					
				
		if (!defined('IMAGE_URL'))
		{
			define('IMAGE_URL', CDN_IMAGE_URL .'image/');//模板图片目录的url地址
		}
        
        if (!defined('MEDIA_URL'))
		{
			define('MEDIA_URL', CDN_IMAGE_URL .'media');//模板图片目录的url地址
		}
		
		if (!defined('THEME_LEFT_DELIMITER')) define('THEME_LEFT_DELIMITER', '{-');
		if (!defined('THEME_RIGHT_DELIMITER')) define('THEME_RIGHT_DELIMITER', '-}');//模板语法标签

		if(!is_dir(THEME_COMPILE_ROOT_PATH)) mkdir(THEME_COMPILE_ROOT_PATH,0777,true); //判断模板缓存目录是否存在	
				
		$tpl	= new \Lib\smarty\Smarty();
		$tpl->template_dir		= THEME_ROOT_PATH;
		$tpl->compile_dir		= THEME_COMPILE_ROOT_PATH."/default/";
		$tpl->left_delimiter	= THEME_LEFT_DELIMITER;
		$tpl->right_delimiter	= THEME_RIGHT_DELIMITER;
	

		$tpl->assign('HTTP', HTTP);
		$tpl->assign('default_charset', DEFAULT_CHARSET);//--
		$tpl->assign('root_url', ROOT_URL);
		$tpl->assign('javascript_url', JAVASCRIPT_URL);//	en(CDN_JAVASCRIPT_URL)
        $tpl->assign('popup_url', POPUP_URL);
		$tpl->assign('image_url', IMAGE_URL);	
        $tpl->assign('media_url', MEDIA_URL);
		$tpl->assign('image_global_url', IMAGE_GLOBAL_URL);	
		$tpl->assign('theme_url',ROOT_URL."theme/default");
		$tpl->assign('themeRoot', THEME);
        $tpl->assign('public_url', ROOT_URL."public");
	            
		$tpl->assign('statics_open', STATICS_OPEN);		
		$tpl->assign('cdn_css_url', CDN_CSS_URL);
		$tpl->assign('cdn_javascript_url', CDN_JAVASCRIPT_URL);//
		$tpl->assign('cdn_base_url', CDN_IMAGE_URL);
		$tpl->assign('cdn_upload_url', CDN_UPLOAD_URL);
		$tpl->assign('cdn_uplan_url', CDN_UPLAN_URL);
		$tpl->assign('Feature_img_url',CDN_UPLAN_URL.'feature/');
		$tpl->assign('action',\Helper\RequestUtil::getParams('action'));
		$tpl->assign('module',\Helper\RequestUtil::getParams('module'));

		
		return self::$smarty = $tpl;        
    }
}