<?php

namespace Helper;

class Promotion {
	var $table; // 表名
	var $pid; // 开始ID
	var $order; // 排序方式
	var $lang; // 语言
	var $this_zid; // 当前操作编号
	var $nextif; // 是否允许当前编号的下级分类显示
	var $WebsiteId; // 站点的ID
	
	/**
	 * constructed function
	 *
	 * @return null
	 * @param
	 *        	null
	 */
	public function __construct($table = 'promotion_category', $pid = '0', $order = 'ASC', $this_zid = '', $nextif = 'no', $WebsiteId = 1) {
		$this->table = trim ( $table );
		$this->pid = trim ( $pid );
		$this->order = trim ( $order );
		$this->this_zid = trim ( $this_zid );
		$this->nextif = trim ( $nextif );
		$this->WebsiteId = trim ( $WebsiteId );
		//$this->lang = trim ( $lang );
		
		static $promotionurl_all, $promotionurl_all_id;
		// echo $this->WebsiteId;exit;
		if ($this->WebsiteId == 1) {
			$table2 = $table;
		} else {
			$table2 = $table . "_" . $this->WebsiteId;
		}
		
		if (file_exists ( '../data/' . $table2 . '.php' )) {
			include_once '../data/' . $table2 . '.php';
		} else {
			$this->ClassDb ();
			include_once '../data/' . $table2 . '.php';
		}
		// var_dump($promotionurl_all_id);
		$this->promotionurl_all_id = $promotionurl_all_id;
		$this->promotionurl_all = $promotionurl_all;
	}
	function ClassDb() {
		$db = \Lib\common\Db::get_db ( 'default' );
		// $db->debug=1;
		$sql = "SELECT * FROM `milanoo_promotion_category` WHERE `WebsiteId`='" . $this->WebsiteId . "' ORDER BY `PCOriesOrder` ASC";
		$rs = $db->SelectLimit ( $sql );
		$row = array ();
		if ($rs->RecordCount ()) {
			while ( ! $rs->EOF ) {
				$row = $rs->fields;
				$promotionurl_all_id [$row ["pid"]] [] = $row ["Id"];
				$promotionurl_all [$row ["Id"]] = array (
						'name' => $row ['name'],
						'id' => $row ['Id'],
						'pid' => $row ['pid'],
						'order' => $row ['PCOriesOrder'] 
				);
				$rs->MoveNext ();
			}
		}
		$promotionurl_data = "\$promotionurl_all=array(\r\n";
		$promotionurlId_data = "\$promotionurl_all_id=array(\r\n";
		if (is_array ( $promotionurl_all_id )) {
			foreach ( $promotionurl_all_id as $pid => $id_all ) {
				$promotionurlId_data .= "	\"" . $pid . "\"=>array(\r\n";
				for($i = 0; $i < sizeof ( $id_all ); $i ++) {
					$promotionurlId_data .= "		\"" . $i . "\"=>\"" . $id_all [$i] . "\",\r\n";
				}
				$promotionurlId_data .= "	),\r\n";
			}
		}
		$promotionurlId_data .= ");\r\n";
		
		if (is_array ( $promotionurl_all )) {
			foreach ( $promotionurl_all as $id => $id_all ) {
				$promotionurl_data .= "	\"" . $id . "\"=>array(\r\n";
				foreach ( $id_all as $name => $key ) {
					$promotionurl_data .= "		\"" . $name . "\"=>\"" . $key . "\",\r\n";
				}
				$promotionurl_data .= "	),\r\n";
			}
		}
		$promotionurl_data .= ");\r\n";
		if ($this->WebsiteId == 1) {
			$file = "../data/promotion_category.php";
			$filebak = "../data/promotion_category.bak.php";
		} else {
			$file = "../data/promotion_category_" . $this->WebsiteId . ".php";
			$filebak = "../data/promotion_category_" . $this->WebsiteId . ".bak.php";
		}
		
		$handle = fopen ( $filebak, "w" );
		fputs ( $handle, "<?\r\n" . $promotionurlId_data . "\r\n" . $promotionurl_data . "?>" );
		
		fclose ( $handle );
		@unlink ( $file );
		rename ( $filebak, $file );
	}
	function class_print($pid = 0, $url = '') {
		// $this->Promotion ($table = 'promotion_category', $pid = '0', $order = 'ASC', $this_zid = '', $nextif = 'no', $WebsiteId = $url);
		$class_all_id = $this->promotionurl_all_id;
		$class_all = $this->promotionurl_all;
		// var_dump($class_all_id);
		$i_name = 'i_' . $pid;
		for($$i_name = 0; $$i_name < sizeof ( $class_all_id [$pid] ); $$i_name ++) {
			$this_id = '';
			$this_id = $class_all_id [$pid] [$$i_name];
			if ($class_all [$this_id] ["name"]) {
				$string .= "<li><b>" . stripslashes ( $class_all [$this_id] ["name"] ) . "</b>";
				$string .= " -
				<a href=\"index.php?module=statistics&action=category&menu_action=add&id=" . $class_all [$this_id] ["id"] . "\" title=\"添加下一级分类\">[添加]</a>&nbsp;
				<a href=\"index.php?module=statistics&action=category&menu_action=edit&id=" . $class_all [$this_id] ["id"] . "\" title=\"修改当前分类\">[修改]</a>&nbsp;
				<a href=\"index.php?module=statistics&action=category&menu_action=del&id=" . $class_all [$this_id] ["id"] . "\" title=\"删除当前分类\" onclick=\"javascript: return confirm('是否确认要删除当前分类?'); \">[删除]</a>";

				if ($this->class_print ( $this_id, $url )) {
					$string .= "<ul><li>" . stripslashes ( $this->class_print ( $this_id, $url ) ) . "</li></ul>";
				}
				$string .= "</li>";
			}
		}
		return $string;
	}
	function class_option($nbsp = '', $pid = 0, $id = '', $lang = '', $this_zid = '') {
		// var_dump($this->WebsiteId);
		// $this->Promotion ($table = 'promotion_category', $pid = '0', $order = 'ASC', $this_zid = '', $nextif = 'no', $WebsiteId);
		$class_all_id = $this->promotionurl_all_id;
		$class_all = $this->promotionurl_all;
		if ($id == "")
			$this_pid = $this->this_pid;
		else
			$this_pid = $id;
		$this_pid_array = explode ( ",", $this_pid );
		$nextif = $this->nextif;
		if ($this_zid == "")
			$this_zid = $this->this_zid;
		$i_name = 'i_' . $pid;
		if ($pid != 0)
			$nbsp .= '&nbsp;&nbsp;&nbsp;';
		for($$i_name = 0; $$i_name < sizeof ( $class_all_id [$pid] ); $$i_name ++) {
			$this_id = '';
			$this_id = $class_all_id [$pid] [$$i_name];
			if ($this_zid == $this_id && $nextif == 'no') {
			} else {
				if (in_array ( $this_id, $this_pid_array ))
					$selected = 'selected';
				else
					$selected = '';
				$string .= "<option value=\"" . $this_id . "\" " . $selected . ">" . $nbsp . "&nbsp;&nbsp; &gt; " . stripslashes ( $class_all [$this_id] ["name"] ) . "</option>";
				$string .= $this->class_option ( $nbsp, $this_id, $id, $lang, $this_zid );
			}
		}
		return $string;
	}
	function id2nameALL($id, $url = '', $fh = '/') {
		$class_all_id = $this->promotionurl_all_id;
		$class_all = $this->promotionurl_all;
		if ($url == '')
			$id2nameALL = ' ' . $fh . ' ' . stripslashes ( $class_all [$id] ["name"] );
		else
			$id2nameALL = ' ' . $fh . ' <a href="' . RewriteUrl ( $url . $id, 'no' ) . '">' . stripslashes ( $class_all [$id] ["name"] ) . '</a>';
		if ($class_all [$id] ["pid"] != 0)
			$id2nameALL = $this->id2nameALL ( $class_all [$id] ["pid"], $url ) . $id2nameALL;
		return $id2nameALL;
	}
	function idALL($id) {
		$class_all_id = $this->promotionurl_all_id;
		for($i = 0; $i < sizeof ( $class_all_id [$id] ); $i ++) {
			if ($class_all_id [$id] [$i]) {
				$idALL .= ',' . $class_all_id [$id] [$i];
				$idALL .= $this->idALL ( $class_all_id [$id] [$i] );
			}
		}
		return $idALL;
	}
}

?>