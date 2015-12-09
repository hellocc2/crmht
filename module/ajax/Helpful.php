<?php
namespace Module\Ajax;
use \helper\RequestUtil as R;
/**
 * 详细页评论helpful
 * @author Jerry Yang<yang.tao.php@gmail.com>
 * @sinc 2011-12-8
 */
class helpful extends \Lib\common\Application{
    public function __construct(){
		// -------------发表评论评价：是否有帮助--------------
		 $comment_id=trim(R::getParams('id'));
		 $help_ful=trim(R::getParams('value_helpful'));
		 $comment_obj=new \Model\Comment ();
		 $result=$comment_obj->commentHelpFul($comment_id,$help_ful);
		 
		 if($result['code']==0){
			$stack = isset($_COOKIE["milanoo_helpful"])?$_COOKIE["milanoo_helpful"]:'';
			$stack.=",".$comment_id;
		 	setcookie("milanoo_helpful", $stack,(time()+86400),"/");
			die('1');
		 }else{
			die('0');
		 }
    }
}