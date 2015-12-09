<?php

  namespace Module\step;

  use Helper\RequestUtil as R;
  use Helper\ResponseUtil as rew;

  /**
   * 步骤设置
   * 
   * @author tanjiang
   *         @sinc 2014-04-18
   * @param
   *        	int
   * @param
   *        	int
   */
  class StepManage extends \Lib\common\Application
  {
  		public function __construct()
  		{       
  		        $tpl = \Lib\common\Template::getSmarty();                
  				$params = R::getParams();
  				$act = $params->act; //取得动作参数
  				$db = \Lib\common\Db::get_db();
  				//$db->debug=1;
                $base_url = "index.php?module=step&action=StepManage";
  				/*
  				*此处根据动作参数不同进行获取预约信息，删除功能等
  				*/
  				switch ( $act )
  				{         
  				          //编辑时取得预约信息
  						case 'ajax_getStep':
  								$ajaxRes = array( 'status' => 0 );
  								$params_ajax = R::getParams();
  								$id = $params_ajax->id;
  								$sql = "SELECT * FROM milanoo.milanoo_sns_event_step WHERE id={$id} AND data_status=0 ";
  								if ( $info = $db->GetRow( $sql ) )
  								{
  										$ajaxRes = array( 'status' => 1, 'detail' => $info );
  								}
  								echo json_encode( $ajaxRes );
  								exit();
                                break;
                        case 'ajax_subStep':
                            $ajaxRes = array('status'=>0);
                            $params_ajax = R::getParams();
                            $stepId = $params_ajax->stepId;
                            $taskId = $params_ajax->taskId;
                            $name = $params_ajax->name;
                            $score = $params_ajax->score;
                                
                                if(!empty($stepId)){
                                    //更新
                                    $sql = "UPDATE milanoo.milanoo_sns_event_step 
                                            SET name = '{$name}',task_id='{$taskId}',score={$score}
                                            WHERE id={$stepId} ";                                    
                                }else{
                                   //新增
                                    $sql = "INSERT INTO milanoo.milanoo_sns_event_step
                                            (name,task_id,score) 
                                            VALUES ('{$name}','{$taskId}','{$score}') "; 
                                }
                                
                                if($db->Execute($sql)){
                                    $ajaxRes = array('status'=>1);
                                }                            
                            
                            echo json_encode($ajaxRes);
                            exit();
                            break;
                         
                          
                          //单独删除      
                        case 'delSingle':
                                $params_del = R::getParams();
                                $ajaxDel = array('status'=>0);
                                $id = $params_del->id;
                                if(!empty($id)){                                                                            
                                        $sql_del_s = "UPDATE milanoo.milanoo_sns_event_step SET data_status = 1 WHERE id = '{$id}' ";
                                        if($db->Execute( $sql_del_s )){
                                            $ajaxDel = array('status'=>1);
                                            echo json_encode( $ajaxDel);
                                            exit();
                                    }
                                }
                                break;
                         
                         //批量删除       
                        case 'delChoosed':
                                $idarray = $_POST["idarray"];
                                if(!empty($idarray)){
                                        $idStr = implode( ",", $idarray );                                    
                                        $sql_del = "UPDATE milanoo.milanoo_sns_event_step SET data_status = 1 WHERE id in({$idStr}) ";
                                        if($db->Execute( $sql_del )){
                                        $this->jumpTo($base_url );                                        
                                    }
                                }
                                break;


  				}
                //读取任务数据供添加步骤选择
                $sql_task = "SELECT id,name FROM milanoo.milanoo_sns_event_task ORDER BY id DESC";
                $taskData = $db->GetAll($sql_task);
                $tpl->assign('taskData',$taskData);
                
                //搜索
                $so_array['stitle'] = $params->stitle;
                $tpl->assign ( 'so_array', $so_array );
  				/*
  				*以下是取得列表数据
  				*/
  				$sql = "SELECT a.*,b.name as tname FROM milanoo.milanoo_sns_event_step a
                        LEFT JOIN milanoo.milanoo_sns_event_task b
                        ON a.task_id=b.id AND b.data_status=0 
                        WHERE a.data_status=0 ";
                        
                if(!empty($so_array['stitle']) && $so_array['stitle']!='输入步骤名称进行搜索'){
                    $sql.=" AND a.name LIKE '%{$so_array['stitle']}%' ";
                }
                //$sql.=" GROUP BY tname ";
  				$sql .= "ORDER BY a.task_id ASC";
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

  				$reload = $_SERVER['PHP_SELF'] . "?module=step&action=StepManage";
               
                if(!empty($so_array['stitle'])){
                    $reload.="&stitle='{$so_array['stitle']}'";
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
  				$stepData = array();
  				if ( $rs->RecordCount() )
  				{
  						while ( ! $rs->EOF )
  						{
  								$row = $rs->fields;
  								$stepData[] = $row;
  								$rs->MoveNext();
  						}
  				}
                
  				//print_r($stepData);
  				$tpl->assign( 'step', $stepData );
  				$tpl->display( 'step_list.htm' );
  		}
  }
?>