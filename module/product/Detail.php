<?php
  namespace Module\product;

  use Helper\RequestUtil as R;
  use Helper\ResponseUtil as rew;

  /**
   * 商品细节
   */
  class Detail extends \Lib\common\Application
  {
  		public function __construct()
  		{
  		       	$tpl = \Lib\common\Template::getSmarty();
                
  				$params = R::getParams();
  				
  				$tpl->display( 'product_detail.htm' );
  		}
        
       
  }
?>