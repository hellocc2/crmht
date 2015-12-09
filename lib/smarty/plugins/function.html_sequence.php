<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * Smarty {html_sequence} function plugin
 *
 * 
 * @author     Christopher Kvarme <christopher.kvarme@flashjab.com>
 * @author credits to Monte Ohrt <monte at ohrt dot com>
 * @version    1.0
 * @param array
 * @param Smarty
 * @return string
 * @uses smarty_function_escape_special_chars()
 */
function smarty_function_html_sequence($params, &$smarty) {
	//print_r($params);
	$match = explode ( "||", $params ["url"] );
	$params_all = Helper\RequestUtil::getParams ();
	
	if ($match [2] == $match [3]) {
		if ($params_all->desc == "asc")
			$desc = "&desc=desc";
		else
			$desc = "&desc=asc";
		$img = "arrow_down_mini.gif";
	} else
		$desc = "&desc=desc";
	$sting = $match [0] . "&order=" . $match [3] . $desc;
	$url = "<a href=\"" . $sting . "\">" . $match [1];
	if ($match [2] == $match [3]) {
		if ($params_all->desc == "desc" || ! $params_all->desc)
			$url .= "<img src=\"" . IMAGE_URL . "icons/arrow_down_mini.gif\" width=\"16\" height=\"16\" />";
		else
			$url .= "<img src=\"" . IMAGE_URL . "icons/arrow_up_mini.gif\" width=\"16\" height=\"16\" />";
	}
	$url .= "</a>";
	return $url;
}

?>
