<?php

namespace Module\advertise;

use Helper\RequestUtil as R;
use Helper\ResponseUtil as rew;

/**
 * 会员注册
 *
 * @author tanjiang
 *         @sinc 2014-04-18
 * @param
 *        	int
 * @param
 *        	int
 */
class AdvertiseManage extends \Lib\common\Application {

    public function __construct() {
		$commonFun = new \Lib\common\commonFunction (); // 引入公共方法
		$tpl = \Lib\common\Template::getSmarty ();

		// 引入配置文件
		$snsType = \config\snsConfig::$snsType;
		$pictureSize = \config\snsConfig::$pictureSize;
		$snsTypeOne = \config\snsConfig::$snsTypeOne;
		$pictureSizeOne = \config\snsConfig::$pictureSizeOne;
		$language = \config\snsConfig::$language;
		$languageOne = \config\snsConfig::$languageOne;

        $dataStatus = \config\snsConfig::$dataStatus;
        $dataStatusMemo = \config\snsConfig::$dataStatusMemo;
        $dataStatusForShow = \config\snsConfig::$dataStatusForShow;

		$tpl->assign ( 'snsType', $snsType );
		$tpl->assign ( 'pictureSize', $pictureSize );
		$tpl->assign ( 'snsTypeOne', $snsTypeOne );
		$tpl->assign ( 'pictureSizeOne', $pictureSizeOne );
		$tpl->assign ( 'language', $language );
        $tpl->assign ( 'languageOne', $languageOne );
        $tpl->assign ( 'dataStatusForShow', $dataStatusForShow );

		$params = R::getParams ();
		$act = $params->act; // 取得动作参数
		$db = \Lib\common\Db::get_db ();
		//$db->debug=1;
		$base_url = "index.php?module=advertise&action=AdvertiseManage";
		/*
		 * 此处根据动作参数不同进行获取预约信息，删除功能等
		 */
		switch ($act) {
			// 编辑时取得预约信息
			case 'ajax_getAd' :
				$ajaxRes = array (
						'status' => 0 
				);
				$params_ajax = R::getParams ();
				$id = $params_ajax->id;
				$sql = "SELECT * FROM milanoo.milanoo_sns_ad WHERE id={$id} ";
				if ($info = $db->GetRow ( $sql )) {
					$ajaxRes = array (
							'status' => 1,
							'detail' => $info 
					);
				}
				echo json_encode ( $ajaxRes);
				exit ();
				break;
			case 'ajax_subAd' :
				// 引入上传文件
				$ajaxRes = array (
					'flag' => 0
				);
				$dateNow = date ( "y-m-d H:i:s" );

                $params_ajax = R::getParams ();

				$adId = $params_ajax->adId;
				if ($adId == 'undefined') {
					unset ( $adId );
				}
				$title = $params_ajax->title;
				$type = $params_ajax->type;
				$size = $params_ajax->size;
				$targetUrl = $params_ajax->targetUrl;
				$describe = $params_ajax->describe;
                $language = $params_ajax->lang;
                $data_status = $params_ajax->data_status;

				if ($params_ajax->type != 'batcht') {
					if ($type == 3 || $type == 1) { // 如果是视频那尺寸就不上传默认为0
						$size = 0;
					}

                    $upload = new \Lib\common\UploadFile ();
					$upload->autoSub = true;
					$upload->allowExts = array (
							'jpg',
							'gif',
							'png',
							'jpeg' 
					); // 设置附件上传类型
					$upload->maxSize = 5242880; // 限制上传大小5M;
					$upload->subType = 'date';
					$upload->dateFormat = 'Ymd';
					$upRootPath = dirname ( dirname ( dirname ( dirname ( __FILE__ ) ) ) );
					$uploadRs = $upload->upload ( $upRootPath . '/upload/snsad/' );
					if ($adId || $uploadRs) {
						// 上传成功取得上传信息
						$uploadInfo = $upload->getUploadFileInfo ();
						if ($uploadInfo) {
							$imgUrl = '/upload/snsad/' . $uploadInfo [0] ['savename'];
						} else {
							$imgUrl = '';
						}

						if (! empty ( $adId )) {
							// 更新
							$sql = "UPDATE milanoo.milanoo_sns_ad 
	                                            SET title = '{$title}',ad_type={$type},picture_size={$size},
	                                            target_url='{$targetUrl}',description='{$describe}',lang='{$language}',data_status={$data_status} ";
							if (! empty ( $imgUrl )) {
								$sql .= ",picture='{$imgUrl}' ";
							}
							$sql .= " WHERE id={$adId} ";
						} else {
							// 新增
							$sql = "INSERT INTO milanoo.milanoo_sns_ad
	                                            (title,ad_type,picture,picture_size,target_url,description,gmt_create,lang,data_status)
	                                            VALUES ('{$title}',{$type},'{$imgUrl}',{$size},'{$targetUrl}','{$describe}','{$dateNow}','{$language}',{$data_status})";
						}
						
						if ($db->Execute ( $sql )) {
							$ajaxRes = array (
									'flag' => 1 
							);
						} else {
							$ajaxRes = array (
									'flag' => 2,
									'msg' => '信息保存到数据库失败' 
							);
						}
					} else {
						$ajaxRes = array (
								'flag' => 2,
								'msg' => $upload->getErrorMsg () 
						); // 上传图片错误信息
					}
				} else {    // 开始批量保存处理
					$productId_array = explode ( ",", $describe );
					define ( "SELLER_LANG", "en-uk" );
					$mProduct = new \Model\Product ();

					if ($language == 'ja-jp'){
						$lan_url = 'http://www.milanoo.jp';
					} else {
						$lang_str = explode('-', $language);
						$lan_url = 'http://www.milanoo.com/'.$lang_str[0];
					}

                    // 初始化统计结果id数组：成功的，未查询到商品的，保存到db失败的
                    $result_ids_success=$result_ids_failed=$result_ids_db_error=array();
                    $result_msg=array();

                    foreach ( $productId_array as $productId ) {
						$search_arr = array (
							'productId' => $productId
						);
						$details = $mProduct->getProductsDetails ( $search_arr );

						if ($details['productDetails'] == null) {
                            // 未查询到商品
                            $result_ids_failed[]=$productId;
						} else {
                            $pic_name = '/upen/m/' . $details ['productDetails'] ['productPicturesArr'] [0];

                            if (! empty ( $adId )) {
                                // 更新
                                $sql = "UPDATE milanoo.milanoo_sns_ad SET title = '批量-{$productId}',ad_type='1',picture_size='4',target_url='{$lan_url}/p{$productId}.html',lang='{$language}'";
                                if (! empty ( $imgUrl )) {
                                    $sql .= ",picture='{$pic_name}' ";
                                }
                                $sql .= " WHERE id={$adId} ";
                            } else {
                                // 新增
                                $sql = "INSERT INTO milanoo.milanoo_sns_ad (title,ad_type,picture,picture_size,target_url,gmt_create,lang) VALUES ('批量-{$productId}',1,'{$pic_name}',4,'{$lan_url}/p{$productId}.html','{$dateNow}','{$language}') ";
                            }

                            // 根据保存结果，放入不同的统计数组
                            if ($db->Execute ( $sql )) {
                                $result_ids_success[]=$productId;
                            } else {
                                $result_ids_db_error[]=$productId;
                            }
                            unset ( $pic_name );
                        }
					}

                    // 在所有商品处理完成之后，对处理结果进行统计，并返回用户端
                    // 成功的
                    if($n_success=count($result_ids_success)) {
                        $result_msg[]=$n_success.'个成功('.implode(',', $result_ids_success).')';
                    }
                    // 保存到数据库失败的
                    if($i=count($result_ids_db_error)) {
                        $result_msg[]=$i.'个保存失败('.implode(',', $result_ids_db_error).')';
                    }
                    // 通过java接口未查询到商品数据的
                    if($i=count($result_ids_failed)) {
                        $result_msg[]=$i.'个商品不存在('.implode(',', $result_ids_failed).')';
                    }

                    // 是否全部成功
                    $_is_all_success = $n_success==count($productId_array);

                    // 是否部分成功=有成功，但未全部成功
                    $_is_part_success = $n_success>0 && !$_is_all_success;

                    // 是否全部失败
                    $_is_all_failed = ($n_success==0);

                    $result_msg=($_is_all_success ? '成功' : ($_is_part_success ? '部分成功：' : '失败：'))
                                .implode(', ', $result_msg);

                    $ajaxRes = array (
                        'flag' => $_is_all_failed ? 2 : 1,  // 只要部分成功，就需要跳转
                        'msg' => $result_msg
                    );
				}


				echo json_encode ( $ajaxRes );
				exit ();
				break;

			// 单独删除
			case 'delSingle' :
				$params_del = R::getParams ();
				$ajaxDel = array (
						'status' => 0 
				);
				$id = $params_del->id;
				if (! empty ( $id )) {
					$sql_del_s = 'UPDATE milanoo.milanoo_sns_ad SET data_status = '.$dataStatus['deleted']. " WHERE id = ".$id;
					if ($db->Execute ( $sql_del_s )) {
						$ajaxDel = array (
								'status' => 1 
						);
						echo json_encode ( $ajaxDel );
						exit ();
					}
				}
				break;

			// 批量删除
			case 'delChoosed' :
				$idarray = $_POST ["idarray"];
				if (! empty ( $idarray )) {
					$idStr = implode ( ",", $idarray );
                    $sql_del = 'UPDATE milanoo.milanoo_sns_ad SET data_status = '.$dataStatus['deleted']. ' WHERE id IN ('.$idStr.')';
					if ($db->Execute ( $sql_del )) {
						$this->jumpTo ( $base_url );
					}
				}
				break;


            // 更改状态
            // 目前应用到 设置或取消 激活状态
            // #29115 , chengbolin@milanoo.com, 2014.9.26
            case 'setStatus' :
                $params = R::getParams();

                $id = $params->id;
                $newStatus=$params->newStatus;  // 修改后的新状态，取值包括 hidden,hidden-normal

                if (! empty ( $id )) {
                    $sql_hide_s = 'UPDATE milanoo.milanoo_sns_ad SET data_status = '.$dataStatus[$newStatus]. " WHERE id = ".$id;
                    if ($db->Execute ( $sql_hide_s )) {
                        $ajaxDel = array (
                            'status' => 1
                        );
                        echo json_encode ( $ajaxDel );
                        exit ();
                    }
                }
                break;

            // 批量 隐藏(取消激活)
            // #29115 ,chengbolin@milanoo.com, 2014.9.25
            // 2014.9.28 : chengbolin, 增加批量显示的功能
            case 'setBatchStatus' :
                $idarray = $_POST ["idarray"];
                $newStatus = $params->newStatus;

                if (! empty ( $idarray ) && array_key_exists($newStatus, $dataStatus)) {
                    $idStr = implode ( ",", $idarray );
                    $sql_set_status = 'UPDATE milanoo.milanoo_sns_ad SET data_status = '.$dataStatus[$newStatus]. ' WHERE id IN ('.$idStr.')';
                    if ($db->Execute ( $sql_set_status )) {
                        $this->jumpTo ( $base_url );
                    }
                }
                break;
		}
		
		// 搜索
		$so_array ['stime'] = $params->stime;
		$so_array ['stitle'] = $params->stitle;
		$so_array ['language'] = $params->language;
		$tpl->assign ( 'so_array', $so_array );
		/*
		 * 以下是取得列表数据
		 *
		 * #29115
		 * 2014.9.25
		 * chengbolin@milanoo.com
		 * 扩展 data_status 定义后，需要将隐藏的记录也显示出来，通过显示进行区分
		 */
		$sql = 'SELECT * FROM milanoo.milanoo_sns_ad WHERE (data_status='.$dataStatus['normal'].' || data_status='.$dataStatus['hidden'].')';

		if (! empty ( $so_array ['language'] )) {
			$sql .= " AND lang = '{$so_array['language']}' ";
		}
		
		if (! empty ( $so_array ['stime'] ) && $so_array ['stime'] != '选择创建日期进行搜索') {
			$sql .= " AND CAST(gmt_create as date) = '{$so_array['stime']}' ";
		}
		if (! empty ( $so_array ['stitle'] ) && $so_array ['stitle'] != '输入广告标题进行搜索') {
			$sql .= " AND title LIKE '%{$so_array['stitle']}%' ";
		}
		$sql .= " ORDER BY id DESC ";
		
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
		
		$reload = $_SERVER ['PHP_SELF'] . "?module=advertise&action=AdvertiseManage";
		if (! empty ( $so_array ['stime'] )) {
			$reload .= "&stime={$so_array['stime']}";
		}
		if (! empty ( $so_array ['language'] )) {
			$reload .= "&language={$so_array['language']}";
		}
		if (! empty ( $so_array ['suname'] )) {
			$reload .= "&title='{$so_array['title']}'";
		}
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
				$tpl->assign ( 'page', $page_html );
			}
		$i = $i - PAGE;
		$rs = $db->SelectLimit ( $sql, PAGE, $i );
		// print_r($rs);
		$adData = array ();
		if ($rs->RecordCount ()) {
			while ( ! $rs->EOF ) {
				$row = $rs->fields;
				$row['is_sns'] = explode("-",$row ['title']);
                $row['data_status_memo']=$dataStatusMemo[$row['data_status']];
				$adData [] = $row;
				$rs->MoveNext ();
			}
		}
		
		// print_r($adData);
		$tpl->assign ( 'advertise', $adData );
		$tpl->display ( 'advertise_list.htm' );
	}
}
?>