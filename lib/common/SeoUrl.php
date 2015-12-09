<?php
namespace Lib\common;
use Helper\RequestUtil as Rq; 
use Helper\ResponseUtil as Rp;
/**
 * 符合SEO业务需求的url处理类
 * @author Su Chao<suchaoabc@163.com>
 * @uses \Model\Redirect30,\Helper\RequestUtil,SELLER_LANG
 */
class SeoUrl{
	/**
	 * 类实例
	 * @var SeoUrl
	 */
	protected static $instance;
	protected function __construct(){

	}
		
	/**
	 * 发出Location头,进行url跳转
	 * @param string $targetUrl 目标url
	 * @param int $statusCode http状态码,默认为301
	 */
	protected function redirect($targetUrl,$statusCode=301)
	{
		header('HTTP/1.1 301 Moved Permanently');
		header('Location:'.$targetUrl);
	}
	
	/**
	 * 获取此类的实例	 
	 * @return \Lib\common\SeoUrl
	 */
	public static function init()
	{
		if(!self::$instance)
		{
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	/**
	 * 根据后台设定和通用规则,对指定的当前页面进行30X 跳转. 来源:需求#6894
	 * @param boolean $forceExit 是否强制终止程序
	 */
	public function fix($forceExit=false)
	{
		$redirected = false;
		$m = new \Model\Redirect30X();
		$rules = $m->getRules();
		$requestUrl = \Helper\RequestUtil::getUrl();
		$key = md5($requestUrl.SELLER_LANG);
		//指定的规则优先与通用规则
		if(isset($rules[$key]['target_url']))
		{//根据后台指定的规则进行修正
			
			$this->redirect($rules[$key]['target_url']);
			$redirected = true;
			
			$m->increaseClicks($rules[$key]['id']);
		}		
		else
		{//通用规则跳转
			$params = Rq::getParams();
			if(isset($params->module) && $params->module == 'thing')
			{				
				if(isset($params->action) && $params->action == 'glist' && isset($params->class) && empty($params->aparams))
				{
					$catId = $params->class;
					$model = new \Model\Navigator();
					$catInfo = $model->getNav($catId, '0:0:0');				
					if(isset($catInfo['code']) && $catInfo['code'] == 0)
					{
						$seoUrl = Rp::rewrite(array('url'=>'?module=thing&action=glist&class='.$catId,'isxs'=>'no','seo'=>stripslashes($catInfo['selfCategory']['categoryName'])));
						$origUrl = Rq::getUrl(false);
						$origQueryStr = Rq::getRawQueryString();
						if ($origUrl != $seoUrl)
						{
							if($origQueryStr)
							{
								$seoUrl .= '?'.$origQueryStr;
							}
							$this->redirect($seoUrl);
							$redirected = true;
						}
							
					}
				}
			
			}			
			
		}
		
		if($redirected && $forceExit)
		{
			die();
		}
	}
	
}