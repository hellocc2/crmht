<?php

/*
	V4.94 23 Jan 2007  (c) 2000-2007 John Lim (jlim#natsoft.com.my). All rights reserved.
	  Released under both BSD license and Lesser GPL library license. 
	  Whenever there is any discrepancy between the two licenses, 
	  the BSD license will take precedence. 
	  Set tabs to 4 for best viewing.

  	This class provides recordset pagination with 
	First/Prev/Next/Last links. 
	
	Feel free to modify this class for your own use as
	it is very basic. To learn how to use it, see the 
	example in adodb/tests/testpaging.php.
	
	"Pablo Costa" <pablo@cbsp.com.br> implemented Render_PageLinks().
	
	Please note, this class is entirely unsupported, 
	and no free support requests except for bug reports
	will be entertained by the author.

*/
class ADODB_Pager {
	var $id; 	// unique id for pager (defaults to 'adodb')
	var $db; 	// ADODB connection object
	var $sql; 	// sql used
	var $rs;	// recordset generated
	var $curr_page;	// current page number before Render() called, calculated in constructor
	var $rows;		// number of rows per page
    var $linksPerPage=10; // number of links per page in navigation bar
    var $showPageLinks; 
	var $url;		// jayliaoscu append @ 2007-8-8
	var $linkParameter;	// jayliaoscu append @ 2007-7-26


	var $gridAttributes = 'width=100% border=1 bgcolor=white';
	
	// Localize text strings here
	var $first = '<code>|&lt;</code>';
	var $prev = '<code>&lt;&lt;</code>';
	var $next = '<code>>></code>';
	var $last = '<code>>|</code>';
	var $moreLinks = '...';
	var $startLinks = '...';
	var $gridHeader = false;
	var $htmlSpecialChars = true;
	var $page = 'Page';
	var $linkSelectedColor = 'red';
	var $cache = 0;  #secs to cache with CachePageExecute()
	
	//----------------------------------------------
	// constructor
	//
	// $db	adodb connection object
	// $sql	sql statement
	// $id	optional id to identify which pager, 
	//		if you have multiple on 1 page. 
	//		$id should be only be [a-z0-9]*
	//
	function ADODB_Pager(&$db,$sql,$id = 'adodb', $showPageLinks = false)
	{
		global $PHP_SELF;
	
		$curr_page = $id.'_curr_page';
		if (empty($PHP_SELF)) $PHP_SELF = htmlspecialchars($_SERVER['PHP_SELF']); // htmlspecialchars() to prevent XSS attacks
		
		$this->sql = $sql;
		$this->id = $id;
		$this->db = $db;
		$this->showPageLinks = $showPageLinks;
		
		$next_page = $id.'_n';	
		
		$tmp_params		= explode('/', $PHP_SELF);
		for ($i=0; $i<sizeof($tmp_params); $i++)
		{
			$tmp_params_2	= explode('-', $tmp_params[$i]);
		}
		
		for ($j=0; $j<sizeof($tmp_params_2); $j++)
		{
			if ($tmp_params_2[$j]==$this->id . "_n")
			{
				$next_page_tmp	= $tmp_params_2[$j+1];
				break;
			}
		}

		if (isset($next_page_tmp))
		{
			$_SESSION[$curr_page] = (integer) $next_page_tmp;
		}
		else
		//if (empty($_SESSION[$curr_page]))
		{
			$_SESSION[$curr_page] = 1; ## at first page
		}

		$this->curr_page = $_SESSION[$curr_page];
		
	}
	
	//---------------------------
	// Display link to first page
	function Render_First($anchor=true)
	{
		global $PHP_SELF;
		if ($anchor)
		{
		?>
		<?php
			// edit by jayliaoscu @ 2007-8-8
			echo "<a href=\"" . $this->url . "-" . $this->id . "_n-1";
			if (!empty($this->linksParameter))
			{
				echo "" . $this->linksParameter;
			}
			echo ".html";
			echo "\">";
			echo $this->first;
			echo "</a>&nbsp;";
		}
		else
		{
			print "$this->first &nbsp; ";
		}
	}
	
	//--------------------------
	// Display link to next page
	function render_next($anchor=true)
	{
		global $PHP_SELF;
	
		if ($anchor)
		{
			// edit by jayliaoscu @ 2007-8-8
			echo "<a href=\"".$this->url."-".$this->id."_n-".($this->rs->AbsolutePage() + 1);
			
			if( !empty( $this->linksParameter ) )
			{
				echo "".$this->linksParameter;
			}
			echo ".html";
			
			echo "\">";
			echo  $this->next;
			echo "</a>&nbsp;";
		}
		else
		{
			print "$this->next &nbsp; ";
		}
	}
	
	//------------------
	// Link to last page
	// 
	// for better performance with large recordsets, you can set
	// $this->db->pageExecuteCountRows = false, which disables
	// last page counting.
	function render_last($anchor=true)
	{
		global $PHP_SELF;
	
		if (!$this->db->pageExecuteCountRows) return;
		
		if ($anchor)
		{
			// edit by jayliaoscu @ 2007-8-8
			echo "<a href=\"".$this->url."-".$this->id."_n-".$this->rs->LastPageNo();
			
			if( !empty( $this->linksParameter ) ){
				echo "".$this->linksParameter;
			}
			echo ".html";
			
			echo "\">";
			echo $this->last;
			echo "</a>&nbsp;";
		}
		else
		{
			print "$this->last &nbsp; ";
		}
	}
	
	//---------------------------------------------------
	// original code by "Pablo Costa" <pablo@cbsp.com.br> 
        function render_pagelinks()
        {
			global $PHP_SELF;

			// edit by jayliaoscu @ 2007-7-26
			// add paramenter deliver function
			if (!empty($this->linksparameter))
			{
				$param	= "&" . $this->linksParameter;
			}

            $pages        = $this->rs->LastPageNo();
            $linksperpage = $this->linksPerPage ? $this->linksPerPage : $pages;
            for($i=1; $i <= $pages; $i+=$linksperpage)
            {
                if($this->rs->AbsolutePage() >= $i)
                {
                    $start = $i;
                }
            }
			$numbers = '';
            $end = $start+$linksperpage-1;
			$link = $this->id . "_n";
            if($end > $pages) $end = $pages;
			
			
			if ($this->startLinks && $start > 1) {
				$pos = $start - 1;
				$numbers .= "<a href=$this->url-$link-$pos>$this->startLinks</a>  ";
            } 
			
			for($i=$start; $i <= $end; $i++) {
                if ($this->rs->AbsolutePage() == $i)
                    $numbers .= "<font color=$this->linkSelectedColor><b>$i</b></font>  ";
                else 
                     $numbers .= "<a href=$this->url-$link-$i>$i</a>  ";
            
            }
			if ($this->moreLinks && $end < $pages) 
				$numbers .= "<a href=$this->url-$link-$i>$this->moreLinks</a>  ";
            print $numbers . ' &nbsp; ';
        }
	// Link to previous page
	function render_prev($anchor=true)
	{
		global $PHP_SELF;
		if ($anchor)
		{
			// edit by jayliaoscu @ 2007-7-26
			echo "<a href=\"".$this->url."-".$this->id."_n-".($this->rs->AbsolutePage() - 1);
			
			if(!empty( $this->linksParameter ))
			{
				echo $this->linksParameter;
			}
			echo ".html";
			
			echo "\">";
			echo $this->prev;
			echo "</a>&nbsp;";
		}
		else
		{
			print "$this->prev &nbsp; ";
		}
	}
	
	//--------------------------------------------------------
	// Simply rendering of grid. You should override this for
	// better control over the format of the grid
	//
	// We use output buffering to keep code clean and readable.
	function RenderGrid()
	{
	global $gSQLBlockRows; // used by rs2html to indicate how many rows to display
		include_once(ADODB_DIR.'/tohtml.inc.php');
		ob_start();
		$gSQLBlockRows = $this->rows;
		rs2html($this->rs,$this->gridAttributes,$this->gridHeader,$this->htmlSpecialChars);
		$s = ob_get_contents();
		ob_end_clean();
		return $s;
	}
	
	//-------------------------------------------------------
	// Navigation bar
	//
	// we use output buffering to keep the code easy to read.
	function RenderNav()
	{
		ob_start();
		if (!$this->rs->AtFirstPage()) {
			$this->Render_First();
			$this->Render_Prev();
		} else {
			$this->Render_First(false);
			$this->Render_Prev(false);
		}
        if ($this->showPageLinks){
            $this->Render_PageLinks();
        }
		if (!$this->rs->AtLastPage()) {
			$this->Render_Next();
			$this->Render_Last();
		} else {
			$this->Render_Next(false);
			$this->Render_Last(false);
		}
		$s = ob_get_contents();
		ob_end_clean();
		return $s;
	}
	
	//-------------------
	// This is the footer
	function RenderPageCount()
	{
		if (!$this->db->pageExecuteCountRows) return '';
		$lastPage = $this->rs->LastPageNo();
		if ($lastPage == -1) $lastPage = 1; // check for empty rs.
		if ($this->curr_page > $lastPage) $this->curr_page = 1;
		return "<font size=-1>$this->page ".$this->curr_page."/".$lastPage."</font>";
	}
	
	//-----------------------------------
	// This is the select menu
	// Added by LeoJay at 2007-8-8
	function SelectMenu()
	{
		global $PHP_SELF;
		
		$linksParameter	= $this->url;
		if( !empty( $this->linksParameter ) ){
			$linksParameter .= $this->linksParameter;
		}
		$linksParameter	.= "-". $this->id ."_n-'+this.value+'.html'";

		ob_start();
		echo "<select onchange=\"window.location='". $linksParameter ."\">";
		for ( $i = 1; $i<=$this->rs->LastPageNo(); $i++ ) {
			if ( $i==$this->curr_page )
				echo "<option value='". $i ."' selected>". $i ."</option>\n";
			else
				echo "<option value='". $i ."'>". $i ."</option>\n";
		}
		echo "</select>";
		$jump	= ob_get_contents();
		ob_end_clean();
		return $jump;
	}

	//-----------------------------------
	// Call this class to draw everything.
	function Render($rows=10)
	{
	global $ADODB_COUNTRECS;
	
		$this->rows = $rows;
		
		if ($this->db->dataProvider == 'informix') $this->db->cursorType = IFX_SCROLL;
		
		$savec = $ADODB_COUNTRECS;
		if ($this->db->pageExecuteCountRows) $ADODB_COUNTRECS = true;
		if ($this->cache)
			$rs = &$this->db->CachePageExecute($this->cache,$this->sql,$rows,$this->curr_page);
		else
			$rs = &$this->db->PageExecute($this->sql,$rows,$this->curr_page);
		$ADODB_COUNTRECS = $savec;
		
		$this->rs = &$rs;
		if (!$rs) {
			print "<h3>Query failed: $this->sql</h3>";
			return;
		}
		
		if (!$rs->EOF && (!$rs->AtFirstPage() || !$rs->AtLastPage())) 
			$header = $this->RenderNav();
		else
			$header = "&nbsp;";
		
		$grid = $this->RenderGrid();
		$footer = $this->RenderPageCount();
		
		$this->RenderLayout($header,$grid,$footer);
		
		$rs->Close();
		$this->rs = false;
	}
	
	//------------------------------------------------------
	// override this to control overall layout and formating
	function RenderLayout($header,$grid,$footer,$attributes='border=1 bgcolor=beige')
	{
		echo "<table ".$attributes."><tr><td>",
				$header,
			"</td></tr><tr><td>",
				$grid,
			"</td></tr><tr><td>",
				$footer,
			"</td></tr></table>";
	}
}


?>