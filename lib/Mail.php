<?php
namespace Lib;
use config\Mail as cfg;
/**
 * 邮件处理类
 * @author Su Chao<suchaoabc@163.com>
 * @todo SMTP 协议邮件发送
 */
class Mail{
	protected $protocol = 'stomp';
	protected $validProtocols = array('mail','smtp','stomp');
	public function setProtocol($protocol)
	{
		if(!in_array($protocol, $this->validProtocols))
		{
			return false;
		}
		return $this->protocol = $protocol;
	}
	
	/**
	 * 发送邮件\n	 
	 * @param array $data 邮件相关的内容,包括接收人,标题,正文,邮件附加头及附加参数.参考{@link mail()}.如果为多维数组则表示多封邮件.如:<br />
	 * @param array $extraOptions 附加设置.默认:$extraOptions=array('async'=>true
	 * [code]
	 * $data = array('to'=>'suchao@milanoo.com','subject'=>'Stomp消息测试','content'=>'测试内容<br /><ul><li><h1>测试</h1></li><li>第一行</li></ul>','headers'=>'Content-type:text/html;charset=utf-8;'."\r\n".'Cc: 李健 <lijian@milanoo.com>','params'=>'');
	 * $mail->send($data);
	 * [/code]
	 */
	public function send($data = array('to'=>null,'subject'=>null,'content'=>null,'headers'=>'','params'=>''),$extraOptions=array('async'=>true))
	{
		switch ($this->protocol)
		{
			case 'stomp':
				return $this->stompSend($data,$extraOptions);
				continue;
			case 'smtp':
				continue;
			default:
				return false;
		}
	}
	/**
	 * 获取一个stomp接口实例	 
	 * @param $linkStr stomp服务器链接.为null时使用系统默认的链接地址
	 * @return Stomp 
	 */
	public function stomp($linkStr=null)
	{
		static $inst=null;
		if(!empty($inst))
		{
			return $inst;
		}

		if(is_null($linkStr) && defined('\config\Mail::STOMP_SERVER'))
		{
			$linkStr = cfg::STOMP_SERVER;
		}
		$userName = $password = null;
		
		$userName = cfg::STOMP_USER;
		
		
		
		$password = cfg::STOMP_PWD;
		
		
		try{
			$inst = new \Stomp($linkStr, $userName, $password);	
		}
		catch(\StompException $err)
		{			
			$errorStr = 'Stomp 服务器链接失败.'."\n".'服务器:'.$linkStr."\n".'用户名:'.$userName."\n".'密码:'.$password."\n".'Trace:'."\n".$err;
			
			if(DEBUG_MODE)
			{
				\Lib\common\Debug::setInfo('StompError', $errorStr);
			}
				
			error_log($errorStr);
			return false;
		}
		return $inst;
	}
	
	/**
	 * 使用STOMP对象发送.消息格式\n
	 * {
	 *   "from": "米兰邮件中心 <mailcenter@milanoo.com>",
	 *   "to": "suchaotest@163.com",
	 *   "subject": "Stomp消息测试",
	 *   "content": "测试内容<br /><ul><li><h1>测试</h1></li><li>第一行</li></ul>",
	 *   "headers": "Content-type:text/html;charset=utf-8;\r\nCc: 李健2 <lijian@milanoo.com>",
	 *   "params": "",
	 *   "username": "milanoo_mailtest@milanoo.com",
	 *   "password": "milanoo999"	
	 *	}
	 * @param array $data 邮件
 	 * @param array $extraOptions 附加设置.默认:$extraOptions=array('async'=>true)	 
 	 * @return Boolean
	 */
	public function stompSend($data,$extraOptions=array('async'=>true))
	{
		
		$stomp = $this->stomp();
		if(!$stomp)
		{
			return false;
		}
		
		$send = function(&$msg) use ($stomp,&$extraOptions)
		{
			if(DEBUG_MODE)
			{
				\Lib\common\Debug::setInfo('StompMsg', $msg,true);
			}	
				
			$msg = json_encode($msg);
			$header = array('persistent'=>'true');
			if(isset($extraOptions['async']) && !$extraOptions['async'])
			{
				$header['receipt'] = 'none';
			}

			if(isset($extraOptions['priority']))
			{
				$header['priority'] = (int) $extraOptions['priority'];
			}
				
			$result = $stomp->send(cfg::STOMP_MAIL_QUEUE,$msg, $header);	
			return $result;	
		};
		
				
		$result = false;
		
		if(isset($extraOptions['async']) && !$extraOptions['async'])
		{
			$transactionId = date('ID-Y-m-d-H-i-s');
			$stomp->begin($transactionId,array('transaction'=>$transactionId));			
		}
		
		if(!is_array(current($data)))
		{
			$data = array($data);
		}
		
		foreach ($data as $msg)
		{
			
			if(empty($msg['username']))
			{
				$msg['username'] = cfg::SMTP_USER;
			}									
			
			if(empty($msg['from']))
			{
				if(defined('\config\Mail::SMTP_FROM'))
				{
					$msg['from'] = cfg::SMTP_FROM;
				}
				else
				{
					$msg['from'] = cfg::SMTP_USER;
				}
			}
			
			if(empty($msg['password']) && defined('\config\Mail::SMTP_PWD'))
			{
				$msg['password'] = cfg::SMTP_PWD;
			}
			
			$result = $send($msg);
			if(!$result)
			{
				break;
			}
		}
		
		if($result)
		{
			if(!empty($transactionId))
			{
				$result = $stomp->commit($transactionId);
			}
		}
		else
		{//发送消息失败
			if(DEBUG_MODE)
			{
				\Lib\common\Debug::setInfo('StompError', $stomp->error());
			}
			
			if(!empty($transactionId))
			{
				$result = $stomp->abort($transactionId);
			}			
		}
		
		if(!$result)
		{//发送消息失败后的其它错误
			if(DEBUG_MODE)
			{
				\Lib\common\Debug::setInfo('StompError', $stomp->error());
			}			
		}
		
		return $result;
	}
	
	/**
	 * 对邮件地址进行base64编码.
	 * @param string $addr 多个邮件地址用","分开 
	 */
	public static function encodeAddr($addr)
	{
		$addr = explode(',', $addr);
		foreach($addr as $k => $v)
		{
			if(strlen(trim($v)) == 0)
			{
				unset($addr[$k]);
				continue;
			}

		   if(preg_match('#^([^<>]+)<#u', $v, $matches))
		   {
		   	   $alia = $matches[1];
		   }
		   
		   $matches = null;

		   if(preg_match('#[^@<>]+@[^@<>]+#u', $v,$matches))
		   {
		   		$from = $matches[0];
		   }
		   
		   if(empty($alia))
		   {
		   		$addr[$k] = $from;
		   }
		   else
		   {
		   		$addr[$k] = '=?UTF-8?B?'.base64_encode($alia)."?= <{$from}>";
		   }
		}
	
		return implode(',', $addr);
	}
}
