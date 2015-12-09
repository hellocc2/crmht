<?php
use Helper\RequestUtil as R;
/**
 * Smarty plugin
 */


/**
 * Smarty {html_siderbar} function plugin
 */
function smarty_function_html_siderbar($params, &$smarty)
{
	$action=R::getParams ('action');
	switch($action){
		case 'Summary':
			$Summary='class="active"';
		break;
		case 'Thing':
			$Thing='class="active"';
		break;
		case 'Plans':
			$Plans='class="active"';
		break;
		case 'History':
			$History='class="active"';
		break;
		case 'Chart':
			$Chart='class="active"';
		break;
		case 'Account':
			$Account='class="active"';
		break;
		default:
			$News='class="active"';		
	}

    $html='';
	$html='<ul id="main-nav" class="nav nav-tabs nav-stacked">
	
					<li '.$News.'>
						<a href="index.php?module=index&action=Index">
							<i class="icon-home"></i>
							重大新闻 		
						</a>
					</li>
					
					<li '.$Summary.'>
						<a href="index.php?module=operate&action=Summary">
							<i class="icon-home"></i>
							经验总结 		
						</a>
					</li>
					
					<li '.$Thing.'>
						<a href="index.php?module=operate&action=Thing">
							<i class="icon-pushpin"></i>
							事件分析	
						</a>
					</li>
					
					<li '.$Plans.'>
						<a href="index.php?module=operate&action=Plans">
							<i class="icon-th-list"></i>
							操单情况		
						</a>
					</li>
					
					<li '.$History.'>
						<a href="index.php?module=operate&action=History">
							<i class="icon-th-large"></i>
							行情回顾	
						</a>
					</li>
					
					<li '.$Chart.'>
						<a href="index.php?module=operate&action=Chart">
							<i class="icon-signal"></i>
							图表统计	
						</a>
					</li>
					
					<li '.$Account.'>
						<a href="index.php?module=member&action=Account">
							<i class="icon-user"></i>
							用户信息							
						</a>
					</li>';
	if($action=='Loginout'){
		$html.='<li ><a href="index.php?module=member&action=Login"><i class="icon-lock"></i>登入</a></li>';
	}else{
		$html.='<li ><a href="index.php?module=member&action=Login"><i class="icon-lock"></i>登出</a></li>';
	}			
							
	$html.='</ul>';
	return $html;

}

?>
