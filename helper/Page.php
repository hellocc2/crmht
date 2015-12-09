<?php
namespace Helper;
use Helper\ResponseUtil as Rewrite;
use Lib\common\Language as Lang;
/**
 * 分页类
 */
class Page {
	
	public static function getpage($total, $perpage = 12, $page = 1, $url = '') {
		if (! $total || ! $perpage) {
			return false;
		}
		$pages = ceil ( $total / $perpage );
		if ($pages == 1 || $pages == 0)
			return;
		$page = min ( $pages, $page );
		$prepg = $page - 1;
		$nextpg = ($page == $pages ? 0 : $page + 1);
		$pagenav='';
		/*if ($total != 0 && $perpage * $page < $total)
		$pagenav = "<div class=\"right\"> " . (($perpage * ($page - 1)) + 1) . " - " . ($perpage * $page) . "共" . $total . "";
	elseif ($total != 0)
		$pagenav = "<div class=\"right\">" . $action_lang ["theme_Showing_Results"] . " " . (($perpage * ($page - 1)) + 1) . " - " . $total . " of " . $total . "";
	if ($total == 0)
		$pagenav = "<div class=\"right\">" . $action_lang ["theme_Showing_Results"] . " " . $total . "";*/
		if (($pages - $page) >= 6)
			$s = $page - 2;
		else
			$s = $pages - 7;
		if (($pages - $page) >= 6)
			$e = $page + 5;
		else
			$e = $pages;
		if ($s < 1) {
			$s = 1;
			$e = 8;
			if ($e > $pages)
				$e = $pages;
		} elseif ($e > $pages) {
			$e = $pages;
			if ($s < 1)
				$s = 1;
		}
		if ($s > 1)
			$pagenav .= "<a href=\"" . $url . "&page=1\"><span>1...</span></a>";
		if ($prepg) {
			$pagenav .= "<a href=\"" . $url . "&page=" . $prepg . "\"><span><上一页</span></a>";
		}
		for($i = $s; $i <= $e; $i ++) {
			if ($i == $page)
				$pagenav .= "<a href=\"" . $url . "&page=" . $i . "\" class=\"current\"><span><strong>$page</strong></span></a>";
			else
				$pagenav .= "<a href=\"" . $url . "&page=" . $i . "\"><span>$i</span></a>";
		}
		if ($nextpg) {
			$pagenav .= "<a href=\"" . $url . "&page=" . $nextpg . "\"><span>下一页></span></a>";
		}
		if ($pages > $e)
			$pagenav .= "<a href=\"" . $url . "&page=" . $pages . "\"><span>...$pages</span></a>";
		$pagenav .= "<kbd><input type=\"text\" name=\"custompage\" id=\"custompage\" size=\"1\" onKeyDown=\"if(event.keyCode==13) {window.location='" . $url . "&page=" . "'+this.value + ' '; return false;}\" onBlur=\"pageGo(this.value);\"  value=\"" . $page . "\"></kbd>";
		
			$pagenav .= "<input name=\"go\" onclick=\"window.location='" . $url . "&page=" . "'+$('#custompage').val() + '';\" type=\"button\" value=\"go\"/>";
		//$pagenav .= "</div>";
		return $pagenav;
	}
}