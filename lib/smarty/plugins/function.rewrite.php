<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty {rewrite} function plugin
 *
 * Type:     function<br>
 * Name:     rewrite<br>
 * Purpose:  重写为符合SEO的URL地址
 * @param array $params 包含的属性为$url,<br />
 * 						$isxs='yes',返回重写后的值还是直接输入.默认为直接输出.<br />
 * 						$html='.html',生成的地址后缀.<br />
 * 					    $seo='',生成seo的字符.<br />
 * 						$IS_REWRITE_URL='',<br />
 * 						$rewriteDirName='',<br />
 * 						$force_seo=0,<br />
 * 						$forceNoDomainPrepend=0,是否强制不添加完整的域名.<br />
 * 						$domain 指定域名
 * 						$seoTargetLanguage 指定Seo目标语言
 * @param Smarty
 */
function smarty_function_rewrite($rawParams,&$smarty)
{
    return \Helper\ResponseUtil::rewrite($rawParams);
}       
