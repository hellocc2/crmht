<?php

  namespace Module\choosed;

  use Helper\RequestUtil as R;
  use Helper\ResponseUtil as rew;

  /**
   * 选择商品
   * 
   * @author tanyujiang
   *         @sinc 2014-05-15
   * @param
   *        	int
   * @param
   *        	int
   */
  class ChoosedManage extends \Lib\common\Application
  {
  		public function __construct()
  		{
  				$tpl = \Lib\common\Template::getSmarty();
                 //引入转发类型配置  
                $choosedStateOne = \config\snsConfig::$choosedStateOne; 
                $languageOne = \config\snsConfig::$languageOne; 
                $tpl->assign('choosedState',$choosedStateOne);
                
                $commonFun = new \Lib\common\commonFunction();  //引入公共方法
                
  				$params = R::getParams();
                $act = $params->act; //取得动作参数
                $db = \Lib\common\Db::get_db();
                //  $db->debug=1;
                $base_url = "index.php?module=choosed&action=ChoosedManage";
                
               	switch ( $act ){
               	             //单独删除      
                        case 'delSingle':
                                $params_del = R::getParams();
                                $ajaxDel = array('status'=>0);
                                $id = $params_del->id;
                                if(!empty($id)){
                                        $db->StartTrans();
                                        $sql_del_s = "UPDATE milanoo.milanoo_sns_order SET data_status = -1 WHERE id = '{$id}' ";
                                        if($db->Execute( $sql_del_s )){
                                            $sql_del_s2 = "UPDATE milanoo.milanoo_sns_order_product SET data_status = -1 WHERE order_id = '{$id}'";
                                            if($db->Execute( $sql_del_s2)){
                                               $db->CompleteTrans(); 
                                               $ajaxDel = array('status'=>1);
                                            }else{
                                               $db->RollBackTrans(); 
                                            }  
                                    }
                                }
                                echo json_encode( $ajaxDel);
                                exit();
                                break;
                         
                         //批量删除       
                        case 'delChoosed':
                                $idarray = $_POST["idarray"];
                                if(!empty($idarray)){
                                        $idStr = implode( ",", $idarray ); 
                                        $db->StartTrans();                                  
                                        $sql_del = "UPDATE milanoo.milanoo_sns_order SET data_status = 1 WHERE id in({$idStr}) ";
                                        if($db->Execute( $sql_del)){
                                            $sql_del2 = "UPDATE milanoo.milanoo_sns_order_product SET data_status = 1 WHERE order_id in({$idStr}) ";
                                            if($db->Execute( $sql_del2)){
                                                $db->CompleteTrans(); 
                                                $this->jumpTo($base_url );                                                   
                                            }else{
                                               $db->RollBackTrans();  
                                            }                                      
                                    }
                                }
                                break;
                        case 'choosedDetail':
                                $params_detail = R::getParams();
                                $orderId = $params_detail->oid;
                                $memberId = $params_detail->memid;
                                $memberName = $params_detail->memname;
                                $subDate = $params_detail->subdate;
                                $state = $params_detail->state;

                                $choosedDetail = array();

                                // 查询审核备注
                                $SQL='SELECT content FROM milanoo_sns_event_detail WHERE step_id=11 AND event_id=(SELECT event_id FROM milanoo_sns_order WHERE id='.$orderId.' LIMIT 1) LIMIT 1';
                                $rs = $db->GetRow($SQL);
                                $choosedDetail['judgeContent']=isset($rs['content']) ? $rs['content'] : '';

                                // 查询订单产品详情
                                $sql_tail = "SELECT * FROM milanoo_sns_order_product WHERE order_id = '{$orderId}' AND data_status != -1";
                                $rs = $db->SelectLimit( $sql_tail);
                  				//print_r($rs);
                                $choosedDetail['memberId'] = $memberId;
                                $choosedDetail['memberName'] = $memberName;
                                $choosedDetail['subDate'] = $subDate;
                                $choosedDetail['state'] = $state;
                  				if ( $rs->RecordCount() )
                  				{
                  						while ( ! $rs->EOF )
                  						{
                  								$row = $rs->fields; 
                                                //根据商品id和skuid找到商品属性
                                                 $pdata = array(
                                                        'productId'=>$row['product_id'],
                                                        'languageCode'=>'en-uk',
                                                        'skuId'=>$row['sku_id']
                                                    );                                                
                                                
                                                $properties = $this->getProductProperty($pdata);
                                                $row['itemcode'] = $properties['item_code'];
                                                $row['size'] = $properties['size'];
                                                $row['categoryName'] = $properties['categoryName'];
                                                $row['credits'] = $properties['credits'];
                                                if(!empty($properties['customSizeProperty'])){
                                                    $row['customSizeProperty'] = $properties['customSizeProperty'];
                                                }
                                                if(!empty($properties['color'])){
                                                    $row['color'] = $properties['color'];
                                                }    
                                                if(!empty($properties['customProperty'])){
                                                    $row['customProperty'] = $properties['customProperty'];
                                                }   
                                                if(!empty($properties['properties'])){
                                                    $row['properties'] = $properties['properties'];
                                                }                                                
                                                $row['product_img'] = $properties['img_url'];                   
                  								$choosedDetail['list'][] = $row;
                  								$rs->MoveNext();
                  						}
                  				}
                                //print_r($choosedDetail);
                                $tpl->assign('orderid',$orderId);
                                $tpl->assign('choosed_detail',$choosedDetail);
                                $tpl->display('choosed_detail.htm');exit();
                                break;
                        case 'updateState':  //审核
                                $params_state = R::getParams();
                                $ajaxState = array('status'=>0);
                                $oid = $params_state->oid;
                                $state = $params_state->state;
                                $desc = trim($params_state->desc);  // 备注原因

                                if(!empty($oid)){
                                    $db->StartTrans();//开始事务
                                    //根据oid取得eventid 
                                    $sqlEvtid = "SELECT event_id,memberid FROM milanoo_sns_order WHERE id = '{$oid}' ";
                                    $orderData = $db->GetRow($sqlEvtid);
                                    $event_id = $orderData['event_id'];
                                    $memberid = $orderData['memberid'];
                                    $gmtCreate = date("Y-m-d H:i:s");
                                    $sql_state = "UPDATE milanoo_sns_order SET background_state = '{$state}' WHERE id = '{$oid}' ";
                                    if($db->Execute($sql_state)){
                                        if($state==1){//审核通过，那么相关任务的步骤也通过
                                            $sql_upEvtDetail = "INSERT INTO milanoo_sns_event_detail "
                                                                .'(event_id,step_id,memberid,state,gmt_create,content) VALUES('
                                                                .$event_id.',11,'.$memberid.",1,'".$gmtCreate."','".$desc."')";
                                            if($db->Execute($sql_upEvtDetail)){
                                                $db->CompleteTrans();
                                                $ajaxState = array('status'=>1);
                                            }else{
                                                $db->RollBackTrans();
                                                $ajaxState = array('status'=>0);
                                            }
                                        }elseif($state==2){//审核不通过，任务不通过，步骤也不通过,返还积分，并写入积分记录
                                            $sql_upEvtDetail = "INSERT INTO milanoo_sns_event_detail "
                                                                .'(event_id,step_id,memberid,state,gmt_create,content) VALUES('
                                                                .$event_id.',11,'.$memberid.",2,'".$gmtCreate."','".$desc."')";
                                            $evtDetail = $db->Execute($sql_upEvtDetail);
                                            $detailId = $db->Insert_ID();
                                            $sql_upEvt = "UPDATE milanoo_sns_event SET state = '2' WHERE id = '{$event_id}'";
                                            $evtData = $db->Execute($sql_upEvt);
                                            //返还用户被扣积分
                                            $sql_ordered = "SELECT SUM(score) FROM milanoo_sns_order_product WHERE order_id='{$oid}' AND data_status=0 ";
                                            $scores = $db->GetOne($sql_ordered);
                                            $sql_mid = "SELECT memberid FROM milanoo_sns_order WHERE id={$oid}";
                                            $memId = $db->GetOne($sql_mid);
                                            $sql_addScore = "UPDATE milanoo_sns_member SET score=score+{$scores} WHERE memberid = '{$memId}'";
                                            $addScore = $db->Execute($sql_addScore);
											
											//审核未通过更新产品状态
											$sql_mgt = "UPDATE milanoo_sns_order_product SET data_status = '-2' WHERE order_id='{$oid}' and data_status != -1";
                                            $mgtData = $db->Execute($sql_mgt);
											
                                            //插入积分记录（返还积分）
                                            $sql_evt_record = "INSERT INTO milanoo_sns_member_score_record 
                                                                (memberid,actionName,score,event_id,detail_id,gmt_create)
                                                                VALUES('{$memberid}','审核未通过，返还积分','{$scores}','{$event_id}','{$detailId}','{$gmtCreate}') ";
                                            $recordRs = $db->Execute($sql_evt_record);            
                                            if($evtDetail&&$evtData&&$addScore&&$recordRs&&$mgtData){
                                                $db->CompleteTrans();
                                                $ajaxState = array('status'=>1);
                                            }else{
                                                $db->RollBackTrans();
                                                $ajaxState = array('status'=>0);
                                            }
                                        }else{
                                            $db->RollBackTrans();
                                            $ajaxState = array('status'=>0);
                                        }
                                    }
                                }
                                echo json_encode($ajaxState);exit();
                                break;
                        case 'exportChoosed':
								 //$db->debug=1;
                                 $idarray = $_POST["idarray"];
                                if(!empty($idarray)){
                                    $idStr = implode( ",", $idarray ); 
                                    $sql_export = "SELECT o.* FROM  milanoo_sns_order o WHERE o.id in ({$idStr}) ";
                                    $rs = $db->SelectLimit( $sql_export);
                                   	$exportData = array();
                      				if ( $rs->RecordCount())
                      				{
                      						while ( ! $rs->EOF )
                      						{
                      								$row = $rs->fields; 
                                                    //根据lang获得对应中文名称
                                                    $row['lang_cn'] = $languageOne["{$row['lang']}"]; 
                                                    //根据国家名获得国家编码
                                                    $sql_country = "SELECT CountriesName,CountriesFlag FROM milanoo_countries WHERE id='{$row['ConsigneeStateId']}' "; 
                                                    $country = $db->GetRow($sql_country);                                                    
                                                    $row['country_code'] =  $country['CountriesFlag'];
                                                    //获得国家英语名称
                                                    $countryNames = $country['CountriesName'];
                                                    $countryArr = explode(",",$countryNames);
                                                    $countriesArr = array();
                                                    foreach($countryArr as $key=>$val){
                                                        $valArr = explode("|",$val);
                                                        $countriesArr[$valArr['0']] = $valArr['1'];
                                                    }
                                                    $row['country_en'] = $countriesArr['en-uk'];
                                                    //根据订单id获得订单商品和skuid
                                                    $sql_sku = "SELECT sku_id FROM milanoo_sns_order_product WHERE order_id='{$row['id']}' and data_status != -1";
                                                    $skuData = $db->GetAll($sql_sku); 
                                                    $row['sku_data'] =  $skuData;                        
                      								$exportData[] = $row;
													
													//审核未通过更新产品状态
													$sql_export = "UPDATE milanoo_sns_order SET export_status = '1' WHERE id='{$row['id']}'";
		                                            $db->Execute($sql_export);
													
                      								$rs->MoveNext();
                      						}
                      				} 
                                     //print_r($exportData);  exit();
                                }
                                $tpl->assign('export_data',$exportData);
            					header ( "Content-type:application/vnd.ms-excel" );
            					$fire_name = urlencode ( "choosed_list.xls" );
            					header ( "Content-Disposition:filename=" . $fire_name );
            					$tpl->display ( 'choosed_export.htm' );
            					exit ();
                            break;
               	}         
                
  				/*
  				*以下是取得列表数据
  				*/
                 //搜索
                $so_array['suname'] = $params->suname;
                $so_array['suid'] = $params->suid;
                $so_array['semail'] = $params->semail;
				$so_array['audit'] = $params->audit;
				
  				$sql = "SELECT o.export_status,mm.MemberId,mm.MemberContact,mm.MemberEmail,o.gmt_create,o.id,o.background_state,
                                group_concat(op.product_id) as productIds,SUM(op.score) AS scores
                        FROM milanoo.milanoo_sns_member sm,milanoo.milanoo_member mm  
                        INNER JOIN  milanoo.milanoo_sns_order o ON mm.MemberId = o.memberid
                        LEFT JOIN milanoo.milanoo_sns_order_product op ON op.order_id = o.id
                        WHERE mm.MemberType='FashionBlogger'
  						AND mm.MemberId = sm.memberid
  						AND sm.`softDeletes` != 1
                        AND o.data_status = 0
                        AND op.data_status != -1
                        AND o.state = 1 ";
      
  				if ( ! empty( $so_array['suname'] ) && $so_array['suname'] != '输入姓名进行搜索' )
  				{
  						$sql .= " AND mm.MemberContact LIKE '%{$so_array['suname']}%' ";
  				}
                if ( ! empty( $so_array['suid'] ) && $so_array['suid'] != '输入用户ID进行搜索' )
  				{
  						$sql .= " AND mm.MemberId = '{$so_array['suid']}' ";
  				}
                if ( ! empty( $so_array['semail'] ) && $so_array['semail'] != '输入用户邮箱进行搜索' )
  				{
  						$sql .= " AND mm.MemberEmail LIKE '%{$so_array['semail']}%' ";
  				}
                if ( is_numeric( $so_array['audit'] ) && $so_array['audit'] != '请选择审核状态进行搜索' && $so_array['audit'] !=-1 )
  				{
  					$sql .= " AND o.background_state =  '{$so_array['audit']}' ";
  				} else {
  					$so_array['audit'] = '-1';
  				}
                $sql .= " GROUP BY o.id ";
  				$sql .= " ORDER BY o.id DESC ";
  				$query = $db->execute( $sql );

  				$total_results = $query->MaxRecordCount( $query );
  				$total_pages = ceil( $total_results / PAGE );

  				if ( isset( $_GET['page'] ) )
  				{
  						$show_page = $_GET['page'];
  						if ( $show_page > 0 && $show_page <= $total_pages )
  						{
  								$start = ( $show_page - 1 ) * PAGE;
  								$end = $start + PAGE;
  						} else
  						{
  								$start = 0;
  								$end = PAGE;
  						}
  				} else
  				{

  						$start = 0;
  						$end = PAGE;
  				}

  				$page = intval( $_GET['page'] );

  				$tpages = $total_pages;
  				
  				if ( $page <= 0 )
  						$page = 1;

  				$reload = $_SERVER['PHP_SELF'] . "?module=choosed&action=ChoosedManage";

                if(!empty($so_array['suname'])){
                    $reload.="&suname={$so_array['suname']}";
                }
				if(is_numeric( $so_array['audit'] ) && $so_array['audit'] != '请选择审核状态进行搜索' && $so_array['audit'] !=-1){
                    $reload.="&audit={$so_array['audit']}";
                }
  				$reload .= "&tpages=" . $tpages;
                
  				for ( $i = $start; $i < $end; $i++ )
  				// {
  						// if ( $i == $total_results )
  						// {
  								// break;
  						// }
  				// }

  				if ( $total_pages > 1 )
  				{
  						$page_html = \Lib\common\Page::paginate( $reload, $page, $tpages );
  						$tpl->assign( 'page', $page_html );
  				}
				
  				$i = $i - PAGE;
				
  				$rs = $db->SelectLimit( $sql, PAGE, $i );
  				//print_r($rs);
  				$choosedData = array();
  				if ( $rs->RecordCount() )
  				{
  						while ( ! $rs->EOF )
  						{
  								$row = $rs->fields;                                
  								$choosedData[] = $row;
  								$rs->MoveNext();
  						}
  				}
                //print_r($memberData);
                $tpl->assign ( 'so_array', $so_array );
                
  				$tpl->assign( 'choosed_data', $choosedData );

  				$tpl->display( 'choosed_list.htm' );
  		}
        /**
        * 通过接口找到商品属性
        */
        function getProductProperty($data){ 
            $property = array();
            $commonFun = new \Lib\common\commonFunction();  //引入公共方法
            $returnData = $commonFun->getServiceData('product','snsProductDetail',$data,'GET','sns');
            $property['img_url'] = "http://www.mlo.me/upen/v/".$returnData['productDetail']['productPicturesArr']['0']['picture_url'];                   
            $property['item_code'] = $returnData['productDetail']['productCode'];
            $property['categoryName'] = $returnData['productDetail']['categoryName'];
            $property['credits'] = $returnData['productDetail']['credits'];
            if(!empty($returnData['productDetail']['skuPropertyVos'])){//销售属性
                foreach($returnData['productDetail']['skuPropertyVos'] as $skuKey=>$skuVal){
                    if($skuVal['colorProperty']==2){    //尺寸
                        $property['size'] = $skuVal['configurationName'];
                    }
                    
                    if($skuVal['colorProperty']==1){    //颜色
                        $property['color'][$skuKey]['value'] = $skuVal['configurationName'];
                        $property['color'][$skuKey]['propertyName'] = $skuVal['propertyName'];
                    }
                }
            }
            if(!empty($returnData['productDetail']['productPropertys'])){//非销售属性
                foreach($returnData['productDetail']['productPropertys'] as $proKey=>$proVal){
                    if($proVal['propertyType']=='checkbox_text'){   //如果是选中项目
                        foreach($proVal['propertyOption'] as $val){
                            if($val['configurationName']==$property['size']){
                                $property['properties'][$proKey]['propertiesName'] = $proVal['propertyName'];
                                $property['properties'][$proKey]['value'] = $val['configurationContent'];
                            }
                        }
                    }
                }
            }
            if($property['size']=='Tailor Made'){   //定制信息
                if(!empty($returnData['productDetail']['customArgsArr']['customArgArr'])){
                    foreach($returnData['productDetail']['customArgsArr']['customArgArr'] as $cusKey=>$cusVal){
                        $property['customProperty'][$cusKey]['propertiesName'] = $cusVal['argsName'];
                        $property['customProperty'][$cusKey]['value'] = $cusVal['argsValue'];
                    }
                }
            }
            if(!empty($returnData['productDetail']['customPropertyVo']['customPropertyArr'])){//有尺寸的定制信息
                foreach($returnData['productDetail']['customPropertyVo']['customPropertyArr'] as $cusSizeKey=>$cusSizeVal){
                    $property['customSizeProperty'][$cusSizeVal['customCategoryId']]['categoryName'] = $cusSizeVal['categoryName'];
                    $property['customSizeProperty'][$cusSizeVal['customCategoryId']]['customName'][] = $cusSizeVal['customName'];
                } 
            }           
            return $property;
        }
  }
