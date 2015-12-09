<?php

namespace Module\blogger;

use Helper\RequestUtil as R;
use Helper\ResponseUtil as rew;
use config\snsConfig as CONF;

/**
 * 博主管理
 *
 * @author Tanyujiang
 *         @sinc 2014-05-13
 * @param
 *        	int
 * @param
 *        	int
 */
class BloggerManage extends \Lib\common\Application {
	public function __construct() {
		$tpl = \Lib\common\Template::getSmarty ();
		$params = R::getParams ();
		$act = $params->act; // 取得动作参数
		$db = \Lib\common\Db::get_db ();
		//$db->debug=1;
		$base_url = "index.php?module=blogger&action=BloggerManage";
		
		switch ($act) {
			
			// 查看详细
			case 'memberDetail' :
				$params_detail = R::getParams ();
				$memberId = $params_detail->memberId;
				$isActive = $params_detail->isactive ? $params_detail->isactive : 'base';
				$tpl->assign ( 'isActive', $isActive );
				// 基本信息获取
				$sql_1 = "SELECT  mm.MemberId,mm.MemberContact,mm.MemberSex,mm.MemberEmail,sm.interests,sm.age_range 
                                        FROM milanoo.milanoo_member mm  
                                        LEFT JOIN  milanoo.milanoo_sns_member sm ON mm.MemberId = sm.memberid
                                        WHERE mm.MemberType = 'FashionBlogger'
                                        AND mm.MemberId = '{$memberId}' ";
				$baseInfo = $db->GetRow ( $sql_1 );

                // 处理博客地址
				$sql_blog = "SELECT url FROM milanoo_sns_info WHERE memberid = '{$memberId}' ";
				$blogRs = $db->SelectLimit ( $sql_blog );

				$blogs = array ();
				if ($blogRs->RecordCount() ) {
                    $patternUrl = '/^https?\:\/\//iu';

                    while(!$blogRs->EOF ) {
                        $url=$blogRs->fields['url'];
                        $duplicatedMemberIds=$this->checkDuplicatedUrlMemberIds($memberId, $url);

                        if (! preg_match ($patternUrl, $url)) {
                            $url = "http://" . $url;
						}

                        // 检查是否与其他某用户的博客地址重复，支持多个
						$blogs [] = array('url'=>$url, 'duplicatedMemberId'=>$duplicatedMemberIds);
						$blogRs->MoveNext ();
					}
				}
				$baseInfo ['blogs'] = $blogs;
				$tpl->assign ( 'baseInfo', $baseInfo );
				
				// 体型信息
				$sql_2 = "SELECT * FROM milanoo_member_custom WHERE memberid = '{$memberId}' AND name = 'sns' ";
				$bwhInfo = $db->GetRow ( $sql_2 );
				$tpl->assign ( 'bwhInfo', $bwhInfo );
				
				// 收货地址
				$sql_3 = "SELECT mc.MemberUrbanAreas,mc.ConsigneeCity,mc.ConsigneeStateId,mc.ConsigneePostalcode,mc.ConsigneePhone FROM milanoo_member_consignee mc WHERE mc.MemberId='{$memberId}' AND mc.DefaultAddress=2 ";
				$addressInfo = $db->GetRow ( $sql_3 );
				$country = $this->getMemberContry ( $addressInfo ['ConsigneeStateId'] );
				$alllcountry_array = $this->getALLMemberContry ();
				$tpl->assign ( 'alllcountry_array', json_encode($alllcountry_array));
				$addressInfo ['country'] = $country;
				$tpl->assign ( 'addressInfo', $addressInfo );
				
				// 积分记录
                // 总分
				$sql_score = "SELECT score FROM milanoo_sns_member WHERE memberid = '{$memberId}' ";
				$memberScore = $db->GetOne ( $sql_score );
				$tpl->assign ( 'memberScore', $memberScore );

                // 积分变更记录
				$sql_4 = "SELECT d.content,r.actionName,r.score,r.gmt_create FROM milanoo_sns_member_score_record r LEFT JOIN `milanoo_sns_event_detail` d ON r.`event_id` = d.`event_id` AND r.`detail_id` = d.`id` WHERE r.memberid = '{$memberId}' ORDER BY r.gmt_create DESC ";
				$scoreRecord = $db->GetAll ( $sql_4 );

                $_glue=CONF::$scoreDescGlue;
                foreach($scoreRecord as &$record) {
                    $record['actionColor']=$record['score']>0 ? '' : 'red';

                    // 在actionName字段中，检测是否有备注分隔符，有 则进行处理，并保证不与原有的content冲突
                    if(strpos($record['actionName'], $_glue) > 0) {
                        $_actionName=explode($_glue, $record['actionName']);
                        $record['actionName']=$_actionName[0];
                        $record['content']=$record['content']
                            ? $record['content'].'<br/>'.$_actionName[1]
                            : $_actionName[1];
                    }
                }
				$tpl->assign ( 'scoreRecord', $scoreRecord );
				
				// 所选商品
				$sql_5 = "SELECT mm.MemberId,mm.MemberContact,mm.MemberEmail,o.gmt_create,o.id,o.background_state,
                                            group_concat(op.product_id) as productIds,
                                            sum(op.score) as scores
                                        FROM milanoo.milanoo_member mm  
                                        INNER JOIN  milanoo.milanoo_sns_order o ON mm.MemberId = o.memberid
                                        LEFT JOIN milanoo.milanoo_sns_order_product op ON op.order_id = o.id
                                        WHERE mm.MemberType='FashionBlogger'
                                        AND o.memberid = '{$memberId}'
                                        AND o.data_status = 0
                                        AND op.data_status = 0
                                        AND o.state = 1 ";
				$sql_5 .= " GROUP BY o.id ";
				$sql_5 .= " ORDER BY o.id DESC ";
				$query_5 = $db->execute ( $sql_5 );
				$total_results_5 = $query_5->MaxRecordCount ( $query_5 );
				$total_pages_5 = ceil ( $total_results_5 / PAGE );
				
				if ($isActive == 'choosed') {
					if (isset ( $_GET ['page'] )) {
						$show_page_5 = $_GET ['page'];
						if ($show_page_5 > 0 && $show_page_5 <= $total_pages_5) {
							$start_5 = ($show_page_5 - 1) * PAGE;
							$end_5 = $start_5 + PAGE;
						} else {
							$start_5 = 0;
							$end_5 = PAGE;
						}
					} else {
						$start_5 = 0;
						$end_5 = PAGE;
					}
					
					$page_5 = intval ( $_GET ['page'] );
				}
				
				$tpages_5 = $total_pages_5;
				if ($page_5 <= 0)
					$page_5 = 1;
				
				$reload_5 = $_SERVER ['PHP_SELF'] . "?module=blogger&action=BloggerManage&act=memberDetail&memberId={$memberId}&isactive=choosed";
				
				$reload_5 .= "&tpages=" . $tpages_5;
				for($i = $start_5; $i < $end_5; $i ++) {
					if ($i == $total_results_5) {
						break;
					}
				}
				
				if ($total_pages_5 > 1) {
					$page_html_5 = \Lib\common\Page::paginate ( $reload_5, $page_5, $tpages_5 );
					$tpl->assign ( 'choosedPage', $page_html_5 );
				}
				$i = $i - PAGE;
				$rs_5 = $db->SelectLimit ( $sql_5, PAGE, $i );
				// print_r($rs);
				$choosedData = array ();
				if ($rs_5->RecordCount ()) {
					while ( ! $rs_5->EOF ) {
						$row_5 = $rs_5->fields;
						$choosedData [] = $row_5;
						$rs_5->MoveNext ();
					}
				}
				
				// print_r($choosedData);
				$tpl->assign ( 'choosed_data', $choosedData );
				
				// 所发信息
				$sql_6 = "SELECT a.MemberId,a.MemberContact,a.MemberEmail,b.* FROM milanoo.milanoo_member a
                                        INNER JOIN milanoo.milanoo_sns_event_detail b ON a.MemberId=b.memberid
                                        WHERE a.MemberType='FashionBlogger'
                                        AND b.data_status=0
                                        AND b.share_type<>0 
                                        AND a.MemberId = '{$memberId}' ";
				$sql_6 .= "ORDER BY b.id DESC";
				// echo $sql_6;
				$query = $db->execute ( $sql_6 );
				$total_results = $query->MaxRecordCount ( $query );
				$total_pages = ceil ( $total_results / PAGE );
				
				if ($isActive == 'memInfo') {
					if (isset ( $_GET ['page'] )) {
						$show_page = $_GET ['page'];
						if ($show_page > 0 && $show_page <= $total_pages) {
							$start = ($show_page - 1) * PAGE;
							$end = $start + PAGE;
						} else {
							$start = 0;
							$end = PAGE;
						}
					} else {
						
						$start = 0;
						$end = PAGE;
					}
					
					$page = intval ( $_GET ['page'] );
				}
				
				$tpages = $total_pages;
				if ($page <= 0)
					$page = 1;
				
				$reload = $_SERVER ['PHP_SELF'] . "?module=blogger&action=BloggerManage&act=memberDetail&memberId={$memberId}&isactive=memInfo";
				
				$reload .= "&tpages=" . $tpages;
				for($i = $start; $i < $end; $i ++)
					// {
					// if ( $i == $total_results )
					// {
					// break;
					// }
					// }
					
					if ($total_pages > 1) {
						$page_html = \Lib\common\Page::paginate ( $reload, $page, $tpages );
						$tpl->assign ( 'infoPage', $page_html );
					}
				$i = $i - PAGE;
				$rs = $db->SelectLimit ( $sql_6, PAGE, $i );
				// print_r($rs);
				$infoData = array ();
				if ($rs->RecordCount ()) {
					while ( ! $rs->EOF ) {
						$row = $rs->fields;
						$infoData [] = $row;
						$rs->MoveNext ();
					}
				}
				
				// print_r($infoData);
				$tpl->assign ( 'information', $infoData );
				$tpl->assign ( 'memberid', $memberId );
				$tpl->display ( 'blogger_detail.htm' );
				exit ();
				break;
			case 'delSingle' :
				$params_del = R::getParams ();
				$ajaxDel = array (
						'status' => 0 
				);
				$id = $params_del->id;
				if (! empty ( $id )) {
					$sql_del_s = "UPDATE milanoo.milanoo_sns_member SET softDeletes = 1 WHERE memberid = '{$id}' ";
					if ($db->Execute ( $sql_del_s )) {
						$ajaxDel = array (
								'status' => 1 
						);
						echo json_encode ( $ajaxDel );
						exit ();
					}
				}
				exit ();
				break;
			case 'ajax_subScore' :
				$ajaxReturn = array (
					'status' => 0
				);
				$params_sub = R::getParams ();
				$memberId = $params_sub->memId;
				$score = $params_sub->score;
                $action = $params_sub->scoreAction;
                $desc = $params_sub->desc;

                // 处理积分操作名称，和积分数值及操作符
                if($action=='2') {
                    $score='-' . $score;
                    $actionName='减去积分';
                } else {
                    $actionName='再分配积分';
                }

                // #29387 使用 actionName 字段同时保存操作名称和备注
                // 该方法并不科学，只是为了不增加字段，也尽量不与其他表发生关联，才取此下策
                $desc && $actionName.=CONF::$scoreDescGlue.$desc;

				$sql_ck = "SELECT id FROM milanoo_sns_member WHERE memberid = '{$memberId}' ";
				if ($db->GetOne ( $sql_ck )) {
					$sql = 'UPDATE milanoo_sns_member SET score = score+'.$score." WHERE memberid = '{$memberId}' ";
				} else {
					$sql = "INSERT INTO milanoo_sns_member (memberid,score) VALUES('{$memberId}','{$score}') ";
				}

				if ($db->Execute ( $sql )) {
					$dateNow = date ( "Y-m-d H:i:s" );
					// 更新积分记录
					$sql_up = 'INSERT INTO milanoo_sns_member_score_record'
                             .' (memberid,actionName,score,event_id,detail_id,gmt_create)'
                             .' VALUES ('.$memberId.",'".$actionName."',".$score.",-17,17,'".$dateNow."')";
					$db->Execute ( $sql_up );

					$ajaxReturn ['status'] = 1;
				}
				echo json_encode ( $ajaxReturn );
				exit ();
				break;
			case 'binding' :
				$allMember_array = $this->getALLMember ();
				$allMember_key = array_flip($allMember_array);
				$sql = "UPDATE milanoo_sns_member SET `trace_user` = '{$allMember_key[$_POST['name']]}' WHERE memberid = '{$_POST['id']}' ";
				$db->Execute ( $sql );
				echo $_POST ['name'];
				exit ();
				break;
			case 'edit' :
				$memberid = $_GET ['memberid'];
				
				switch ($_POST ['id']) {
					case 'age_range' :
						$sql = "UPDATE milanoo_sns_member SET `{$_POST['id']}` = '{$_POST['name']}' WHERE memberid = '{$memberid}' ";
						break;
					case 'interests' :
						$sql = "UPDATE milanoo_sns_member SET `{$_POST['id']}` = '{$_POST['name']}' WHERE memberid = '{$memberid}' ";
						break;
					case 'url' :
						$sql = "UPDATE milanoo_sns_info SET `{$_POST['id']}` = '{$_POST['name']}' WHERE memberid = '{$memberid}' ";
						break;
					case 'chest' :
						$sql = "SELECT * FROM milanoo_member_custom WHERE memberid = '{$memberid}' AND name = 'sns' ";
						$customInfo = $db->GetRow ( $sql );
						if (! empty ( $customInfo )) {
							$sql = "UPDATE milanoo_member_custom SET `{$_POST['id']}` = '{$_POST['name']}' WHERE memberid = '{$memberid}' ";
						} else {
							$sql = "INSERT INTO milanoo_member_custom ({$_POST['id']},memberid,name)VALUES('{$_POST['name']}','{$memberid}','sns') ";
						}
						break;
					case 'waist' :
						$sql = "SELECT * FROM milanoo_member_custom WHERE memberid = '{$memberid}' AND name = 'sns' ";
						$customInfo = $db->GetRow ( $sql );
						if (! empty ( $customInfo )) {
							$sql = "UPDATE milanoo_member_custom SET `{$_POST['id']}` = '{$_POST['name']}' WHERE memberid = '{$memberid}' ";
						} else {
							$sql = "INSERT INTO milanoo_member_custom ({$_POST['id']},memberid,name)VALUES('{$_POST['name']}','{$memberid}','sns') ";
						}
						break;
					case 'hips' :
						$sql = "SELECT * FROM milanoo_member_custom WHERE memberid = '{$memberid}' AND name = 'sns' ";
						$customInfo = $db->GetRow ( $sql );
						if (! empty ( $customInfo )) {
							$sql = "UPDATE milanoo_member_custom SET `{$_POST['id']}` = '{$_POST['name']}' WHERE memberid = '{$memberid}' ";
						} else {
							$sql = "INSERT INTO milanoo_member_custom ({$_POST['id']},memberid,name)VALUES('{$_POST['name']}','{$memberid}','sns') ";
						}
						break;
					case 'height' :
						$sql = "SELECT * FROM milanoo_member_custom WHERE memberid = '{$memberid}' AND name = 'sns' ";
						$customInfo = $db->GetRow ( $sql );
						if (! empty ( $customInfo )) {
							$sql = "UPDATE milanoo_member_custom SET `{$_POST['id']}` = '{$_POST['name']}' WHERE memberid = '{$memberid}' ";
						} else {
							$sql = "INSERT INTO milanoo_member_custom ({$_POST['id']},memberid,name)VALUES('{$_POST['name']}','{$memberid}','sns') ";
						}
						break;
					case 'MemberUrbanAreas' :
						$sql = "SELECT * FROM milanoo_member_consignee WHERE memberid = '{$memberid}'";
						$consigneeInfo = $db->GetRow ( $sql );
						if (! empty ( $consigneeInfo )) {
							$sql = "UPDATE milanoo_member_consignee SET `{$_POST['id']}` = '{$_POST['name']}' WHERE memberid = '{$memberid}' ";
						} else {
							$sql = "INSERT INTO milanoo_member_consignee ({$_POST['id']},memberid)VALUES('{$_POST['name']}','{$memberid}') ";
						}
						break;
					case 'ConsigneeCity' :
						$sql = "SELECT * FROM milanoo_member_consignee WHERE memberid = '{$memberid}'";
						$consigneeInfo = $db->GetRow ( $sql );
						if (! empty ( $consigneeInfo )) {
							$sql = "UPDATE milanoo_member_consignee SET `{$_POST['id']}` = '{$_POST['name']}' WHERE memberid = '{$memberid}' ";
						} else {
							$sql = "INSERT INTO milanoo_member_consignee ({$_POST['id']},memberid)VALUES('{$_POST['name']}','{$memberid}') ";
						}
						break;
					case 'ConsigneePostalcode' :
						$sql = "SELECT * FROM milanoo_member_consignee WHERE memberid = '{$memberid}'";
						$consigneeInfo = $db->GetRow ( $sql );
						if (! empty ( $consigneeInfo )) {
							$sql = "UPDATE milanoo_member_consignee SET `{$_POST['id']}` = '{$_POST['name']}' WHERE memberid = '{$memberid}' ";
						} else {
							$sql = "INSERT INTO milanoo_member_consignee ({$_POST['id']},memberid)VALUES('{$_POST['name']}','{$memberid}') ";
						}
						break;
					case 'ConsigneePhone' :
						$sql = "SELECT * FROM milanoo_member_consignee WHERE memberid = '{$memberid}'";
						$consigneeInfo = $db->GetRow ( $sql );
						if (! empty ( $consigneeInfo )) {
							$sql = "UPDATE milanoo_member_consignee SET `{$_POST['id']}` = '{$_POST['name']}' WHERE memberid = '{$memberid}' ";
						} else {
							$sql = "INSERT INTO milanoo_member_consignee ({$_POST['id']},memberid)VALUES('{$_POST['name']}','{$memberid}') ";
						}
						break;
					case 'ConsigneeStateId' :
						$sql = "SELECT * FROM milanoo_member_consignee WHERE memberid = '{$memberid}'";
						$consigneeInfo = $db->GetRow ( $sql );
						if (! empty ( $consigneeInfo )) {
							$sql = "UPDATE milanoo_member_consignee SET `{$_POST['id']}` = '{$_POST['name']}' WHERE memberid = '{$memberid}' ";
						} else {
							$sql = "INSERT INTO milanoo_member_consignee ({$_POST['id']},memberid,DefaultAddress)VALUES('{$_POST['name']}','{$memberid}',2) ";
						}
						$db->Execute ( $sql );
						echo $this->getMemberContry ( $_POST ['name'] );
						exit ();
						break;
				}
				$db->Execute ( $sql );
				echo $_POST ['name'];
				exit ();
				break;
		}
		
		/*
		 * 以下是取得列表数据
		 */
		// 搜索
		$so_array ['memid'] = $params->memid;
		$so_array ['suname'] = $params->suname;
		$so_array ['mail'] = $params->mail;
		$so_array ['blog'] = $params->blog;
		$tpl->assign ( 'so_array', $so_array );

		//全部用户
		$allMember_array = $this->getALLMember ();
		$allMember_key = array_flip($allMember_array);
		
		$allMember_srt = '"'.implode('","', $allMember_array).'"';
		$tpl->assign ( 'allMember_srt', $allMember_srt );
		
		$sql = "SELECT DISTINCT mm.MemberId,sm.trace_user,mm.MemberEmail,mm.MemberContact,mm.MemberSex,sm.score,GROUP_CONCAT(info.url) AS urls FROM milanoo.milanoo_sns_member sm, milanoo.milanoo_member mm LEFT JOIN milanoo_sns_info info ON info.MemberId = mm.MemberId WHERE mm.MemberType='FashionBlogger' AND mm.MemberId = sm.memberid AND sm.memberid=info.MemberId and sm.softDeletes !=1";
		
		if (! empty ( $so_array ['memid'] ) && $so_array ['memid'] != '输入用户ID进行搜索') {
			$sql .= " AND mm.MemberId = '{$so_array['memid']}' ";
		}
		
		if (! empty ( $so_array ['suname'] ) && $so_array ['suname'] != '输入姓名进行搜索') {
			$sql .= " AND mm.MemberContact LIKE '%{$so_array['suname']}%' ";
		}
		
		if (! empty ( $so_array ['mail'] ) && $so_array ['mail'] != '输入邮箱地址进行搜索') {
			$sql .= " AND mm.MemberEmail LIKE '%{$so_array['mail']}%' ";
		}
		
		if (! empty ( $so_array ['blog'] ) && $so_array ['blog'] != '输入BLOG地址进行搜索(不要加http www)') {
			$sql .= " AND info.url like '%{$so_array['blog']}%' ";
		}
		$sql .= " GROUP BY mm.MemberId ORDER BY sm.memberid DESC,info.id ASC";
		
		$query = $db->execute ( $sql );
		$total_results = $query->MaxRecordCount ( $query );
		$total_pages = ceil ( $total_results / PAGE );
		
		if (isset ( $_GET ['page'] )) {
			$show_page = $_GET ['page'];
			if ($show_page > 0 && $show_page <= $total_pages) {
				$start = ($show_page - 1) * PAGE;
				$end = $start + PAGE;
			} else {
				$start = 0;
				$end = PAGE;
			}
		} else {
			$start = 0;
			$end = PAGE;
		}
		
		$page = intval ( $_GET ['page'] );
		
		$tpages = $total_pages;
		if ($page <= 0)
			$page = 1;
		
		$reload = $_SERVER ['PHP_SELF'] . "?module=blogger&action=BloggerManage";
		
		if (! empty ( $so_array ['suname'] )) {
			$reload .= "&suname={$so_array['suname']}";
		}
		if (! empty ( $so_array ['memid'] )) {
			$reload .= "&memid={$so_array['memid']}";
		}
		$reload .= "&tpages=" . $tpages;
		
		for($i = $start; $i < $end; $i ++) {
			if ($total_pages > 1) {
				$page_html = \Lib\common\Page::paginate ( $reload, $page, $tpages );
				$tpl->assign ( 'page', $page_html );
			}
        }
		$i = $i - PAGE;
		$rs = $db->SelectLimit ( $sql, PAGE, $i );

		$memberData = array ();
		if ($rs->RecordCount ()) {
            $pattern = '/^https?\:\/\//iu';
			while ( ! $rs->EOF ) {
                $row = $rs->fields;
                $blogs = array ();

				$urls = $rs->fields['urls'];
				if ($urls) {
                    $urls=explode(',', $urls);
                    foreach($urls as $_url) {
						if($_url) {
                            if (! preg_match ( $pattern, $_url)) {
                                $_url = "http://" . $_url;
                            }
                            $blogs [] = $_url;
                        }
					}
				}
				$row ['blogs'] = $blogs;
				$row ['trace_name'] = trim($allMember_array[$row ['trace_user']]);
				$memberData [] = $row;
				$rs->MoveNext ();
			}
		}
		$tpl->assign ( 'member_info', $memberData );
		
		$tpl->display ( 'blogger_list.htm' );
	}
	
	/**
	 * 取得用户所在国家
	 */
	function getMemberContry($countryId) {
		$db = \Lib\common\Db::get_db ();
		$sql = "SELECT CountriesName FROM milanoo_countries WHERE Id = '{$countryId}' ";
		$country = $db->GetOne ( $sql );
		$countryArr = explode ( ',', $country );
		$enCountryArr = explode ( '|', $countryArr [1] );
		return $enCountryArr [1];
	}
	function getALLMemberContry() {
		$db = \Lib\common\Db::get_db ();
		$sql = "SELECT Id,CountriesName FROM milanoo_countries";
		$rs = $db->SelectLimit ( $sql );
				$country_array = array ();
				if ($rs->RecordCount ()) {
					while ( ! $rs->EOF ) {
						$country_array = $rs->fields;
						$countryArr = explode ( ',', $country_array['CountriesName'] );
						$enCountryArr = explode ( '|', $countryArr [1] );
						$alllcountry_array[$country_array['Id']] = $enCountryArr[1];
						$rs->MoveNext ();
					}
				}
		return $alllcountry_array ;
	}	
	function getALLMember() {
		$db = \Lib\common\Db::get_db ();
		$sql = "SELECT uid,realname FROM `milanoo_admin_user` WHERE `activation` = 1";
		$rs = $db->SelectLimit ( $sql );

		if ($rs->RecordCount ()) {
			while ( ! $rs->EOF ) {
				$use_array = $rs->fields;
				$allMember_array[$use_array['uid']] = $use_array['realname'];
				$rs->MoveNext ();
			}
		}
		return $allMember_array ;
	}	

    /**
     * 查询出 $memberId 之外其他用户的博客地址是否与其任一地址重复
     *
     * @param: $url string
     *
     * 返回值：所有重复博主的id列表，数组形式
     */
    function checkDuplicatedUrlMemberIds($memberId, $url)
    {
        if(!$url) {
            return false;
        }

        $SQL='SELECT GROUP_CONCAT(memberid) AS memberIds FROM milanoo_sns_info WHERE 1=1';
        if($memberId) {
            $SQL.=' AND memberid!='.$memberId;
        }

        if(strpos($url, 'http://')===0) {
            $url=substr($url, 7);
        }
        if(strpos($url, 'https://')===0) {
            $url=substr($url, 8);
        }
        if(strpos($url, 'www.')===0) {
            $url=substr($url, 4);
        }
        if(substr($url, -1)=='/') {
            $url=substr($url, 0, -1);
        }

        $SQL.=" AND url LIKE '%".$url."%'";

        $db = \Lib\common\Db::get_db ();
        $rs = $db->SelectLimit ( $SQL );

        $result=false;
        if ($rs->RecordCount ()) {
            $result = array ();
            while ( ! $rs->EOF ) {
                if($rs->fields['memberIds']) {
                    $result=explode(',', $rs->fields['memberIds']);
                }
                $rs->MoveNext ();
            }
        }
        count($result) || $result=false;

        return $result ;
    }
}
