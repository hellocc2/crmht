<?php
namespace Helper;
/**
 * 与JS相关的方法调用
 */
class Js {
    /**
     * 显示提示页面,并在指定的停留时间后转向指定的页面
     * @param string $msg 显示在页面上的消息
     * @param string $url 要跳转的页面.如果为空则返回HTTP_REFERER页,否则返回主页
     * @param int $exit 是否在执行此方法后立即退出PHP进程,默认为1,为1时将立即退出PHP进程,否则显示页面并继续下面的过程
     * @param int $timeout 多少毫秒后自动跳转.默认为4000,即4秒钟. 如果为0或空,则不自动进行跳转.
     * @param string $msgFromLangPack $msg是否为语言包的一个键, 默认为1, 否则将直接输出$msg内容
     * @param string $success 操作是否成功
     * @param string $tplFile 使用的模版.默认为'alert_forward.htm'
     */
	public static function alertForward($msg, $url=null ,$exit=1, $timeout = 4000,$msgFromLangPack=1,$success=false,$tplFile='alert_forward.htm')
	{
		$tpl = \Lib\common\Template::getSmarty();
		if(is_null($url))
		{
		    if(isset($_SERVER['HTTP_REFERER']))
		    {
		        $url = $_SERVER['HTTP_REFERER'];
		    }
		    else
		    {
		        $url = ROOT_URL;
		    }
		}		
		$tpl->assign(array('msg' => $msg,
							'url' => $url,
							'timeout' => $timeout,
							'msgFromLangPack' => $msgFromLangPack,
							'success' => $success
		));
		$tpl->display($tplFile);
		if($exit==1) die();
		return;
	}	
}