<?php

namespace Helper;

use helper\ResponseUtil as Rewrite;

/**
 * 登录检查类
 */
class CheckLogin {
	/**
	 * 判断是否登录
	 */
	public static function getMemberID() {
		$memberId = $_SESSION [SESSION_PREFIX . "MemberId"];
		if (empty ( $memberId )) {
			header ( "Location:?module=member&action=login" );
		}
		
		return $memberId;
	}
	public static function getNoReMemberId() {
		$memberId = $_SESSION [SESSION_PREFIX . "MemberId"];
		return $memberId;
	}
	public static function sso($action = 'login') {
		include_once ROOT_PATH . 'lib/cas/CAS.php';
		include_once ROOT_PATH . 'config/cas.php';
		$client = '';
		// error_reporting(E_ALL);
		// ini_set("display_errors", 1);
		
		$cas_host = CAS_HOST;
		$cas_port = intval(CAS_PORT);
 
		$cas_context = CAS_CONTEXT;
		
		$phpCAS = new \phpCAS ();
		// $phpCAS->setDebug();
		$phpCAS->client ( CAS_VERSION_2_0, $cas_host, $cas_port, $cas_context );
		$phpCAS->setNoCasServerValidation ();
		$phpCAS->handleLogoutRequests ();
		$phpCAS->forceAuthentication ();
		
		if (isset ( $action ) && $action == 'logout') {
			$phpCAS->logout ();
		}
		$client = $phpCAS->getAttributes ();
		return $client;
	}
}