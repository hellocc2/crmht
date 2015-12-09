<?php
namespace Module\Ajax;
class searchkeywords extends \Lib\common\Application{
    public function __construct(){
    	$keywords = isset($_GET['keywords']) ? trim($_GET['keywords']) : '';
    	if(!empty($keywords)){
	    	$search_keyword = new \Model\Search(); 
	    	$result = $search_keyword->getSearchKeywords($keywords);
	    	if(isset($result['listResults']) && count($result['listResults']) > 0){
	    		foreach ($result['listResults'] as $v){
	    			$keywords_array[] = stripslashes($v);
	    		}
	    		header('Content-Type: application/json;charset=utf-8');
	    		echo json_encode($keywords_array);
	    	}else{
	    		echo '';
	    	}
	    	return;
    	}
    }
}