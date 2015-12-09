<?php
namespace Module\Index;
use Helper\RequestUtil as R;

class Left extends \Lib\common\Application {
	public function __construct() {
		$tpl = \Lib\common\Template::getSmarty ();
        //引入管理员权限配置名单
        $adminManageArr = \config\snsConfig::$adminManageOne; 
        $loginUser = $_SESSION['phpCAS']['user'];
        if(in_array($loginUser,$adminManageArr)){
            $isSuper = true;
        }else{
            $isSuper = false;
        }
        $tpl->assign('isSuper',$isSuper);
		$tpl->display ( 'left.htm' );
	}
}