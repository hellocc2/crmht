<?php

  namespace Module\task;

  use Helper\RequestUtil as R;
  use Helper\ResponseUtil as rew;

  /**
   * 任务设置
   * 
   * @author tanjiang
   *         @sinc 2014-04-18
   * @param
   *        	int
   * @param
   *        	int
   */
  class TaskManage extends \Lib\common\Application
  {
  		public function __construct()
  		{       
  		        $tpl = \Lib\common\Template::getSmarty();
                //引入配置文件
                $taskType =  \config\snsConfig::$taskType;
                $taskTypeOne = \config\snsConfig::$taskTypeOne;
                $tpl->assign ( 'taskType', $taskType );
                $tpl->assign ( 'taskTypeOne', $taskTypeOne );
                
  				$params = R::getParams();
  				$act = $params->act; //取得动作参数
  				$db = \Lib\common\Db::get_db();
  				//$db->debug=1;
                $base_url = "index.php?module=task&action=TaskManage";
  				/*
  				*此处根据动作参数不同进行获取预约信息，删除功能等
  				*/
  				switch ( $act )
  				{         
  				          //编辑时取得预约信息
  						case 'ajax_getTask':
  								$ajaxRes = array( 'status' => 0 );
  								$params_ajax = R::getParams();
  								$id = $params_ajax->id;
  								$sql = "SELECT * FROM milanoo.milanoo_sns_event_task WHERE id={$id} ";
  								if ( $info = $db->GetRow( $sql ) )
  								{
  										$ajaxRes = array( 'status' => 1, 'detail' => $info );
  								}
  								echo json_encode( $ajaxRes );
  								exit();
                                break;
                        case 'ajax_subTask':
                            $ajaxRes = array('status'=>0);             
                            $params_ajax = R::getParams();
                            $taskId = $params_ajax->taskId;
                            $name = $params_ajax->name;
                            $type = $params_ajax->type;
                            $score = $params_ajax->score;
                                
                                if(!empty($taskId)){
                                    //更新
                                    $sql = "UPDATE milanoo.milanoo_sns_event_task 
                                            SET name = '{$name}',type={$type},score={$score}
                                            WHERE id={$taskId} ";                                    
                                }else{
                                   //新增
                                    $sql = "INSERT INTO milanoo.milanoo_sns_event_task
                                            (name,type,score) 
                                            VALUES ('{$name}',{$type},'{$score}') "; 
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
                                        $db->StartTrans();
                                        $sql_del_s = "UPDATE milanoo.milanoo_sns_event_task SET data_status = 1 WHERE id = '{$id}' ";
                                        if($db->Execute( $sql_del_s )){
                                            //删除相应的步骤
                                            $sql_del_c = "UPDATE milanoo.milanoo_sns_event_step SET data_status = 1 WHERE task_id = '{$id}' ";
                                            if($db->Execute( $sql_del_c )){
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
                                        $sql_del = "UPDATE milanoo.milanoo_sns_event_task SET data_status = 1 WHERE id in({$idStr}) ";
                                        if($db->Execute( $sql_del )){
                                            //删除相应步骤
                                            $sql_del_c = "UPDATE milanoo.milanoo_sns_event_step SET data_status = 1 WHERE task_id in({$idStr}) ";
                                            if($db->Execute( $sql_del_c )){
                                                $db->CompleteTrans();
                                            }else{
                                                $db->RollBackTrans();
                                            }
                                            $this->jumpTo($base_url );                                        
                                    }
                                }
                                break;


  				}
                //搜索
                $so_array['sname'] = $params->sname;
                $tpl->assign ( 'so_array', $so_array );
  				/*
  				*以下是取得列表数据
  				*/
  				$sql = "SELECT a.*,GROUP_CONCAT(b.id) as step FROM milanoo.milanoo_sns_event_task a
                        LEFT JOIN (SELECT id,NAME,task_id FROM milanoo.milanoo_sns_event_step WHERE data_status=0 ORDER BY id ASC )b
                        ON b.task_id=a.id
                        WHERE data_status=0 ";                       
                
                if(!empty($so_array['sname']) && $so_array['sname']!='输入任务名称进行搜索'){
                    $sql.=" AND a.name LIKE '%{$so_array['sname']}%' ";
                }
                $sql.=" GROUP BY a.id";
  				$sql .= " ORDER BY a.id DESC ";

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

  				$reload = $_SERVER['PHP_SELF'] . "?module=task&action=TaskManage";
                if(!empty($so_array['sname'])){
                    $reload.="&sname='{$so_array['sname']}'";
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
  				$taskData = array();
  				if ( $rs->RecordCount() )
  				{
  						while ( ! $rs->EOF )
  						{
  								$row = $rs->fields;
  								$taskData[] = $row;
  								$rs->MoveNext();
  						}
  				}
  				//print_r($taskData);
  				$tpl->assign( 'task', $taskData );
  				$tpl->display( 'task_list.htm' );
  		}
  }
?>