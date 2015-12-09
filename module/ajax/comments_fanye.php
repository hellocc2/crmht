<?php
namespace Module\Ajax;
use \Model\Comment;
use Helper\RequestUtil as R;
use Helper\Page as P;
/**
*	留言评论翻页
*/
class comments_fanye extends \Lib\common\Application{
    public function __construct(){
		$comment_obj=new \Model\Comment ();
		$pageNo=R::getParams('page');
		if(empty($pageNo)){$pageNo=1;}
		$WebsiteId=R::getParams('WebsiteId');
		if(empty($WebsiteId)){$WebsiteId=1;}
		$class=R::getParams('class');
		$id=R::getParams('pid');
		// -------------获取评论-------------
		$pageSize=5;
		$comments=$comment_obj->getCommentsByPid($id,$WebsiteId,$pageNo,$pageSize);
		//分页
		$url_p ="?module=thing&action=item&class=" . $class . "&id=" . $id;
		$pages = P::getpage ( $comments ['listResults'] ['totalCount'], $pageSize, $pageNo, $url_p, 'wedding dress','.html#reviews',1);
		
		if($comments['code']==0 && $comments ['listResults'] ['totalCount']!=0){
		/* <div class='reviews' id='reviews'>
		<h3>".\LangPack::$items['thing_item_reviews'].":</h3> */
			$html_result="
	<div id='reviews_wrap'>";
			foreach($comments['listResults']['results'] as $com){//评论
					$score=$com['productScore']*20;
					$html_result.="
	<h4>
	<span class='fr'>".$com['gmtCreate']."</span>
	<span class='star'><b style='width:".$score."%'></b></span> ".\LangPack::$items['thing_item_by']." <b>".(isset($com['memberName'])?stripcslashes($com['memberName']):'')."</b>
	</h4>
	<p class='review'>".stripcslashes($com['commentContent'])."</p>
	";
					foreach($com['commentReplyList'] as $reply){//回复
						$html_result.="
	<div class='reply'>
	<span class='fr'>".$reply['gmtCreate'].'</span>'.\LangPack::$items['thing_item_reply'].':<br />'.\LangPack::$items['thing_item_dear'].'&nbsp;'.(isset($com['memberName'])?$com['memberName']:'').',<br />'.htmlspecialchars_decode($reply['replyContent']).'</div>';
					}
					if(isset($_COOKIE['milanoo_helpful'])){
						$stack=explode(',',$_COOKIE['milanoo_helpful']);
					}else{
						$stack=array();
					}
					if(in_array($com['commentId'],$stack)){
						$html_result.="
	<p class='helpful'><a class='help_yes' href='javascript:void(0)' ><span id='ishelpful".$com['commentId']."'>(".$com['helpFulNum'].")</span></a><a class='help_no' href='javascript:void(0)' ><span id='nohelpful".$com['commentId']."'>(".$com['notHelpFulNum'].")</span></a>
	</p>";
					}else{
						$html_result.="
	<p class='helpful'><a class='help_yes' href='javascript:void(0)' onclick='helpful(\"1\",\"".$com['commentId']."\");'><span id='ishelpful".$com['commentId']."'>(".$com['helpFulNum'].")</span></a><a class='help_no' href='javascript:void(0)' onclick='helpful(\"0\",\"".$com['commentId']."\");'><span id='nohelpful".$com['commentId']."'>(".$com['notHelpFulNum'].")</span></a></p>";
					}
			}
			$html_result.="<div class='pages'>".$pages."</div></div>";
			
		}else{
			echo  $comments['msg'];
			die;
		}
		echo $html_result;
		die;
	}
}