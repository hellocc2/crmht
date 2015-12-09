<?php
/**
 * Smarty plugin
 */


/**
 * Smarty {html_headnav} function plugin
 */
function smarty_function_html_headnav($params, &$smarty)
{
    $html='';
	$html='<div class="container">
			
			<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse"> 
				<span class="icon-bar"></span> 
				<span class="icon-bar"></span> 
				<span class="icon-bar"></span> 				
			</a>
			
			<a class="brand" href="./">统计后台</a>
			
			<div class="nav-collapse">
			
				<ul class="nav pull-right">
					<li>
						<a href="#"><span class="badge badge-warning">7</span></a>
					</li>
					
					<li class="divider-vertical"></li>
					
					<li class="dropdown">
						
						<a data-toggle="dropdown" class="dropdown-toggle " href="#">
							阳光 <b class="caret"></b>							
						</a>
						
						<ul class="dropdown-menu">
							<li>
								<a href="/index.php?module=member&aciton=Account"><i class="icon-user"></i>账户设置</a>
							</li>
							
							<li>
								<a href="/index.php?module=member&aciton=Account&act=changepassword"><i class="icon-lock"></i>改变密码</a>
							</li>
							
							<li class="divider"></li>
							
							<li>
								<a href="/index.php?module=member&aciton=Logout"><i class="icon-off"></i>登出</a>
							</li>
						</ul>
					</li>
				</ul>
				
			</div> <!-- /nav-collapse -->
			
		</div> <!-- /container -->';
	return $html;

}

?>
