<?php
namespace Module\mail;
class Stomp {
	public function __construct(){
		$mail = new \Lib\Mail();
		$data = array('to'=>'suchao@milanoo.com','subject'=>'Stomp消息测试一','content'=>file_get_contents('http://www.csdn.com/'),'headers'=>'Content-type:text/plain;charset=utf-8;'."\r\n".'Cc: 李健 <lijian@milanoo.com>'.";\r\n".'From: 米兰邮件中心 <milanoo_mailtest@milanoo.com>','params'=>'');
		$data = array($data);
		$data[] = array('to'=>'suchaotest@163.com','subject'=>'Stomp消息测试二','content'=>'测试内容<br /><ul><li><h1>测试</h1></li><li>第一行</li></ul>','headers'=>'Content-type:text/html;charset=utf-8;'."\r\n".'Cc: 李健2 <lijian@milanoo.com>','params'=>'');
		$re = $mail->send($data);
		var_dump($re);
		header('Content-type:application/json;charset=utf-8');
	}
}