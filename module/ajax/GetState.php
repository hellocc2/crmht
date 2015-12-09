<?php
namespace Module\Ajax;
use helper\RequestUtil as R;
class GetState extends \Lib\common\Application {
	public function __construct() {
		$data = array ();
		$data ['country_id'] = trim ( R::getParams ( 'country_id' ) );
		$data ['keywords'] = trim ( R::getParams ( 'keywords' ) );
		if(isset($_REQUEST['state'])) {
			$data ['state'] = trim ( R::getParams ( 'state' ) );
		}
		$CountryList = new \Model\CountryList ();
		$result = $CountryList->getStateList( $data );
		
		if($result['code']==0 && $result['states']){
			foreach ( $result ['states'] as $v ) {
				$keywords_array [] = stripslashes ( $v['areaName'] );
			}
			header ( 'Content-Type: application/json;charset=utf-8' );
			echo json_encode ( $keywords_array );
			die();
		} else {
			die('');
		}
		return;
	
	}
}