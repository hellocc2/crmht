<?php
namespace Module\ask;

use Helper\RequestUtil as R;
use Helper\ResponseUtil as rew;
/**
提交问题
*/
  
class index extends \Lib\common\Application{
  		public function __construct()
  		{       
  		    $tpl = \Lib\common\Template::getSmarty();
                 

  			$tpl->display( 'task_list.htm' );
  		}
  }
?>