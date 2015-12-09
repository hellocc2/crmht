<?php
/**
 * Smarty plugin
 */


/**
 * Smarty {html_account} function plugin
 */
function smarty_function_html_account($params, &$smarty)
{
    $html='';
	$html='<div class="account-container">
				
					<div class="account-avatar">
						<img src="'.MEDIA_URL.'/images/head.jpg" alt="" class="thumbnail" />
					</div> <!-- /account-avatar -->
				
					<div class="account-details">
					
						<span class="account-name">阳光</span>
						
						<span class="account-role">管理员</span>
						
						<span class="account-actions">
							<a href="javascript:;">档案</a> |
							
							<a href="javascript:;">设置</a>
						</span>
					
					</div> <!-- /account-details -->
				
				</div>';
	return $html;

}

?>
