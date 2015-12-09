<?php
namespace Lib\_3rd;
/**
 * PHP version 5
 *
 * @package    UASparser
 * @author     Jaroslav Mallat (http://mallat.cz/)
 * @copyright  Copyright (c) 2008 Jaroslav Mallat
 * @copyright  Copyright (c) 2010 Alex Stanev (http://stanev.org)
 * @version    0.4.2 beta
 * @license    http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @link       http://user-agent-string.info/download/UASparser
 */

// view source this file and exit

class UASparser 
{
	public $IniUrl   		= 'http://user-agent-string.info/rpc/get_data.php?key=free&format=ini';
	public $VerUrl   		= 'http://user-agent-string.info/rpc/get_data.php?key=free&format=ini&ver=y';
	public $md5Url			= 'http://user-agent-string.info/rpc/get_data.php?format=ini&md5=y';
	public $InfoUrl   		= 'http://user-agent-string.info';
	public $cache_dir       = null;
	public $updateInterval	= 31536000; // 1 day

	private $_data    		= array();
	private $_ret    		= array();
	private $test			= null;
	private $id_browser		= null;
	private $os_id			= null;
	
	public function __construct() {	
	}

	public function Parse($useragent = null) {
		$_ret['typ']			= 'unknown';
		$_ret['ua_family']		= 'unknown';
		$_ret['ua_name']		= 'unknown';
		$_ret['ua_url']			= 'unknown';
		$_ret['ua_company']		= 'unknown';
		$_ret['ua_company_url']		= 'unknown';
		$_ret['ua_icon']		= 'unknown.png';
		$_ret["ua_info_url"]		= 'unknown';
		$_ret["os_family"]		= 'unknown';
		$_ret["os_name"]		= 'unknown';
		$_ret["os_url"]			= 'unknown';
		$_ret["os_company"]		= 'unknown';
		$_ret["os_company_url"]		= 'unknown';
		$_ret["os_icon"]		= 'unknown.png';
		
		if (!isset($useragent) && !empty($_SERVER['HTTP_USER_AGENT'])) {
			$useragent = $_SERVER['HTTP_USER_AGENT'];
		}
		else
		{
			return $_ret;
		}
		$_data = $this->_loadData();//var_dump(count($_data['robots']));die();
		if($_data) {						
			// browser
			foreach ($_data['browser_reg'] as $test) {
				if (@preg_match($test[0],$useragent,$info)) { // $info contains version
					$id_browser = $test[1];
					break;
		  		}
	 		}
			if (!empty($id_browser)) { // browser detail
				if ($_data['browser_type'][$_data['browser'][$id_browser][0]][0]) $_ret['typ']	= $_data['browser_type'][$_data['browser'][$id_browser][0]][0];
			}		
			return $_ret;
		}
		return $_ret;
	}

	private function _loadData() {
		if (!file_exists($this->cacheDir.'/cache2.0.ini')) {
			$this->_downloadData();
		}
		
		if (file_exists($this->cacheDir.'/cache2.0.ini')) {
			$cacheIni = parse_ini_file($this->cacheDir.'/cache2.0.ini');
		}
		else
		{
			die('ERROR: No datafile (cache2.0.ini in Cache Dir), maybe update the file manually.');
		}		
		
		if ($cacheIni['lastupdate'] < time() - $this->updateInterval || $cacheIni['lastupdatestatus'] != "0") {
			$this->_downloadData();
		}
		if (file_exists($this->cacheDir.'/uasdata2.0.ini')) {
			return @parse_ini_file($this->cacheDir.'/uasdata2.0.ini', true);			
		}
		else {
			die('ERROR: No datafile (uasdata2.0.ini in Cache Dir), maybe update the file manually.');
		}
	}
	private function _downloadData() {
		if(ini_get('allow_url_fopen')) {
			$status = 1;
			if (file_exists($this->cacheDir.'/cache2.0.ini')) {
				$cacheIni = parse_ini_file($this->cacheDir.'/cache2.0.ini');
			}
			$ctx = stream_context_create(array('http' => array('timeout' => 5)));
			!$ver = @file_get_contents($this->VerUrl, 0, $ctx);
			if (strlen($ver) != 11) {
				if($cacheIni['localversion']) {
					$ver = $cacheIni['localversion'];
				}
				else {
					$ver = 'none';
				}
			}
			
			if($ini = @file_get_contents($this->IniUrl, 0, $ctx)) {
				$md5hash = @file_get_contents($this->md5Url, 0, $ctx);
				if(md5($ini) == $md5hash) {
					/**只保留浏览器的相关信息**/
					$ini = parse_ini_string($ini,true);
					$ini['robots'] = $ini['os_reg'] = $ini['browser_os'] = $ini['os_reg'] = $ini['os'] = array();
					foreach ($ini['browser'] as $k => $v)
					{
						$ini['browser'][$k] = array($v[0]);
					}
					$ini = \Helper\String::arrayToIni($ini);
					
					@file_put_contents($this->cacheDir.'/uasdata2.0.ini', $ini);
					$status = 0;
				}
			}

			$cacheIni = "; cache info for class UASparser - http://user-agent-string.info/download/UASparser\n";
			$cacheIni .= "[main]\n";
			$cacheIni .= "localversion = \"$ver\"\n";
			$cacheIni .= 'lastupdate = "'.time()."\"\n";
			$cacheIni .= "lastupdatestatus = \"$status\"\n";
			@file_put_contents($this->cacheDir.'/cache2.0.ini', $cacheIni);
		}
		else {
			die('ERROR: function file_get_contents not allowed URL open. Update the datafile (uasdata2.0.ini in Cache Dir) manually.');
		}
	}
	public function SetCacheDir($cache_dir) {
		if (!is_writable($cache_dir)) {
			die('ERROR: Cache dir('.$cache_dir.') is not writable');
		}
		$cache_dir = realpath($cache_dir);
		$this->cacheDir = $cache_dir;
	}
}