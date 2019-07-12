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
	$root_url=$smarty->_tpl_vars['root_url'];
	$actioin=strtolower($action);
	
	
	switch($action){
		case 'dashboard':
			$chart='class="active"';
		break;
		case 'user':
			$user='class="active"';
		break;	
		case 'check':
			$check='class="active"';
		break;
		case 'data':
			$data='class="active"';
		break;	
		default:
			$news='class="active"';		
	}

    $html='';
	$html='<div id="mws-navigation">
            	<ul>
                	<li class="active"><a href="'.$root_url.'operate/dashboard.html" class="mws-i-24 i-home">系统管理</a>
					
						<ul>
							<li><a href="'.$root_url.'admin/index.html">管理员信息</a></li>
							<li><a href="'.$root_url.'admin/power.html">权限资源管理</a></li>
							<li><a href="'.$root_url.'admin/role.html">角色管理</a></li>							
                        	<li><a href="'.$root_url.'admin/daily.html">日志管理</a></li>
							<li><a href="'.$root_url.'admin/plan.html">轮播图管理</a></li>
							<li><a href="'.$root_url.'admin/aboutus.html">关于我们</a></li>
							<li><a href="'.$root_url.'admin/agreement.html">用户使用协议</a></li>
							<li><a href="'.$root_url.'admin/privacy.html">隐私政策</a></li>
                        </ul>
					
					</li>
                	<li '.$user.'><a href="'.$root_url.'operate/user.html" class="mws-i-24 i-file-cabinet">用户管理</a>
						<ul>
							<li><a href="'.$root_url.'user/index.html">用户管理</a></li>
							<li><a href="'.$root_url.'user/rebate.html">邀请返利</a></li>							
                        </ul>
					</li>
					<li '.$check.'><a href="'.$root_url.'operate/check.html" class="mws-i-24 i-cog">审核管理</a></li>
                	<li '.$data.'><a href="'.$root_url.'operate/data.html" class="mws-i-24 i-chart">基础数据</a></li>
					<li '.$send.'><a href="'.$root_url.'operate/semd.html" class="mws-i-24 i-alert-2">发布管理</a></li>
					<li '.$accessory.'><a href="'.$root_url.'operate/accessory.html" class="mws-i-24 i-pacman">配件与维护管理</a></li>
					<li '.$product.'><a href="'.$root_url.'operate/product.html" class="mws-i-24 i-list">商品管理</a></li>
                	
                </ul>
            </div>';	
							
	
	return $html;

}

?>
