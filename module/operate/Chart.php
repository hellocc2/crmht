<?php

namespace Module\operate;

use Helper\RequestUtil as R;

/**
 * 统计表格
 */
class Chart extends \Lib\common\Application {
	public function __construct() {
		//$client=\Helper\CheckLogin::sso();
		$tpl = \Lib\common\Template::getSmarty ();
		$startdate=R::getParams('startdate');
		$endate=R::getParams('endate');
		
		if(empty($startdate)){
			$startdate=date("Y-m-d", strtotime("-30 days"));
		}
		//echo $startdate;exit;		
		$history=new \Model\History();
		$data=array();
		$data['startdate']=$startdate;
		$data['endate']=$endate;
		$res=$history->selectHistory($data);
		//echo '<pre/>';print_r($res);exit;
		
		$this->type_color = array(
				'DOMESTIC'=>'#FCD202',
                "DOCUMENT"=>'#FCE002',
				'CUSTOMER'=>'#FF9E01',
				'ORDERGROUP'=>'#FF6600',
				'INTERNATIONAL'=>'#FF0F00',
				'REFUND'=>'#00cc0B',
				'EDITORIAL'=>'#FF0F01',
		);
	
		if(!empty($res)){
			$tpl->assign('history',json_encode($res));
			$tpl->assign('startdate',$startdate);
			$tpl->assign('endate',$endate);
		}
	
		$tpl->display ( 'operate_chart.html' );
	}
}



