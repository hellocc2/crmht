<?php
namespace Module\member;
use Helper\RequestUtil as R;
use Helper\ResponseUtil as rew;
/**
 * 会员注册
 * @author wujianjun<wujianjun127@163.com>
 * @sinc 2012-05-15
 * @param int 
 * @param int 
 */
class Reg extends \Lib\common\Application {
	public function __construct() {
		$tpl = \Lib\common\Template::getSmarty ();	
		if($_POST){

			$loginmethod = R::getParams('loginmethod');
			$forward     = R::getParams('forward');		
			$Conditions  = R::getParams('Conditions');
			if(!$Conditions && $loginmethod != 'cart'){
				\helper\Js::alertForward('noConditions','',1);					
			} 
			$UserPass    = R::getParams('UserPass');
			$UserPass2   = R::getParams('UserPass2');
			$email       = R::getParams('email');
			$describes   = R::getParams('describes');
			$CompanyName = R::getParams('CompanyName');
			$KnowWeb     = R::getParams('KnowWeb');
			$reg_array = array();
			
			if(!\helper\Verification::isemail($email)) {
				if($loginmethod == 'cart'){
					$msg = array(
						'error_status'=>4,
						'msg'=>\LangPack::$items['email1'],
					);
					echo json_encode($msg);exit();
				}else{			
					\helper\Js::alertForward('email1','',1);					
				}						
			}
			
			$reg_array['member.email'] = $email;
			if(!$UserPass){
				if($loginmethod == 'cart'){
					$msg = array(
						'error_status'=>1,
						'msg'=>\LangPack::$items['pass1'],
					);
					echo json_encode($msg);exit();
				}else{
					\helper\Js::alertForward('pass1','',1);				
				}
			}
			
			if(!$UserPass || $UserPass != addslashes($UserPass)){
				if($loginmethod == 'cart'){
					$msg = array(
						'error_status'=>2,
						'msg'=>\LangPack::$items['pass2'],
					);
					echo json_encode($msg);exit();
				}else{	
					\helper\Js::alertForward('pass2','',1);				
				}	
			}
			
			if($UserPass != $UserPass2){
				if($loginmethod == 'cart'){
					$msg = array(
						'error_status'=>3,
						'msg'=>\LangPack::$items['pass3'],
					);
					echo json_encode($msg);exit();
				}else{	
					\helper\Js::alertForward('pass3','',1);			
				}				
			}
			
			$UserPass=md5($UserPass.MD5_pass);
			$reg_array['member.userPass'] = $UserPass;
			$emailsDy = R::getParams('emailsDy');
			if(empty($emailsDy)){
				$reg_array['member.emailsDy'] = $emailsDy;
			}
			if(empty($describes)){
				$reg_array['member.describes'] = $describes;
			}
			if(empty($CompanyName)){
				$reg_array['member.companyName'] = $CompanyName;
			}
			if(empty($KnowWeb)){
				$reg_array['member.knowWeb'] = $KnowWeb;
			}
			$reg_array['member.userState'] = 1;
			$reg_array['member.integral'] = 0;
			$reg_array['member.type'] = 'Personal';
			$reg_array['member.loginTime'] = time();
			$reg_array['member.loginNum'] = 1;
			$reg_array['member.webSiteId'] = MAIN_WEBSITEID;
			$clientIp = \Helper\RequestUtil::getClientIp();
			$reg_array['member.loginIp'] = $clientIp;
			$reg_array['member.userTime'] = time();
			if(isset($_COOKIE['PromotionURL'])){
				$reg_array['member.promotionURL'] = addslashes($_COOKIE['PromotionURL']);
			}
			$regObj = new \Model\Register();
			$data = $regObj->registerMember($reg_array);
			if(isset($data['addFlag']) && $data['addFlag'] == 2){
				if($loginmethod == 'cart'){
					$msg = array(
						'error_status'=>5,
						'msg'=>\LangPack::$items['email2'],
					);
					echo json_encode($msg);exit();
				}else{
					\helper\Js::alertForward('email2','',1);							
				}		
			}
			
			if(isset($data['code']) && $data['code'] == 0) {
				/*统计代码需要记录第一次注册的操作*/
				$_SESSION[SESSION_PREFIX . "reg_record_once"]='reg';
				/*统计代码需要记录第一次注册的操作 end*/
				$_SESSION[SESSION_PREFIX . "MemberId"]=$data['id'];
				$_SESSION[SESSION_PREFIX . "MemberEmail"]=$email;
				$pos = strpos($forward,'Step1');
				$loginType = 0;
				if($pos !== false){
					$loginType = 1;
				}
				$cartParmas = array(
					'cookieId'=>isset($_COOKIE['CartId']) ? $_COOKIE['CartId'] : '',
					'memberId'=>$data['id'],
					'loginType'=>$loginType,
					'languageCode'=>SELLER_LANG,
				);
				$cartObj = new \model\Cart();
				$cartObj->mergeShoppingCart($cartParmas);
				if($loginmethod=='cart'){
					$msg = array(
						'error_status'=>0,
						'forward'=>$forward,
					);
					echo json_encode($msg);exit();	
				}else{
					if(!$forward) $forward=rew::rewrite(array('url'=>'?module=index&action=index','isxs'=>'no'));
					header("Location:".$forward);
					exit;
				}
			}
			return;
		}else{
			$forward=rew::rewrite(array('url'=>'?module=member&action=login','isxs'=>'no'));
			header("Location:".$forward);
			exit;
		}
	}
}


