<?php

namespace Module\ajax;

use Helper\RequestUtil as R;

use Helper\String as H;

/**

 * FileName:GetProductGoodsTheme.php

 * AJAX获取终端页商品模板

 * Author:@{chengjun cgjp123@163.com}

 * Date:@{2012-2-9 04:33:09

 * */

class GetProductGoodsTheme extends \Module\thing\Item {

	public function __construct(){

		$productId = R::getParams('productId');

		$mProduct = new \Model\Product ();
		$pObject = new \Helper\ProductsDetails($productId);



		

		$UEDGetInfoMark = R::getParams('UEDGetInfoMark');//UED获取模版数据标记，此标记为真则强制显示商品模版，不显示混合属性模版

		$lang = R::getParams('lang');

		//都用英文

		$lang = 'en-uk';

		//if(empty($lang)) $lang = 'en-uk';

		if(empty($UEDGetInfoMark)) $UEDGetInfoMark = 0;

		if(!empty($productId)){

			R::resetParam('id',$productId);

			R::resetParam('ajaxGetInfoMark',1);

			$result = parent::__construct();

			if(!empty($result)){

				$tpl = \Lib\common\Template::getSmarty ();

				$parentCategories=H::cat_ga_custom_var($result['productDetails']['productCategory'],2);

				$parentCategories=explode(',',$parentCategories);

				$productMixedPropertys = array();

				if(!$UEDGetInfoMark){//UED标记为0时才显示混合属性

				//混合属性尺码表数据封装

					if(!empty($result['productDetails']['productPropertys'])){

						$result['productDetails']['productPropertys'] = H::strDosTrip($result['productDetails']['productPropertys']);

						foreach($result['productDetails']['productPropertys'] as $k=>$v){

							if(!empty($v['propertyOption']) && $v['isSizeChart']==1 && $v['propertyType']=='checkbox_text'){

								foreach($v['propertyOption'] as $k2=>$v2){

									if(!empty($v2['configurationContent'])){

										$productMixedPropertys[$v['propertyName']][$k2]['name'] = $v2['configurationName'];

										$productMixedPropertys[$v['propertyName']][$k2]['content'] = $v2['configurationContent'];

										$productMixedPropertys[$v['propertyName']][$k2]['value'] = $v2['configurationValue'];

										$productMixedPropertys[$v['propertyName']][$k2]['id'] = $v2['configurationId'];

										//转换尺码到英寸

										if(strpos($v2['configurationContent'],'-')!==false){

											//处理出现区间类型的尺寸 如：86-102

											$inchConten = explode('-',$v2['configurationContent']);

											if(!empty($inchConten) && count($inchConten)>=2){

												$tempOne = round($inchConten[0]/2.54,0);

												$tempTwo = round($inchConten[1]/2.54,0);

												$productMixedPropertys[$v['propertyName']][$k2]['inchContent'] = $tempOne.'-'.$tempTwo;

											}

										}else{

											//处理正常的尺寸

											$tempInch = round($v2['configurationContent']/2.54,0);

											$productMixedPropertys[$v['propertyName']][$k2]['inchContent'] = $tempInch;

										}

									}

								}

							}

						}

					}

				}

				$displayContent = '';

				$productTheme = '';

				

				$productModules = $pObject->getProductModules();

				//print_r($productModules);

				$displayContent='';

				if(!empty($productModules)){
					foreach($productModules['templateModule'] as $k=>$v){
						if($v['categoryId']==4){
							$tpl->assign('sizeChartShowContent',$v['content']);
							$tpl->assign('sizeChartShow',$k);
						}elseif($v['categoryId']==6){
							$tpl->assign('colorChartShow',$k);
						}

						$displayContent.=$v['content'];
					}
					$tpl->assign('productModules',$productModules['templateModule']);
					$tpl->assign('tabArray',$productModules['tabArray']);
				}

				

				//信息返回标记，如果此标记为真，则说明有接口需调用商品信息 add by chengjun 2012-02-09
				$ajaxGetInfoMark = R::getParams('ajaxGetInfoMark');
				if(isset($ajaxGetInfoMark) && $ajaxGetInfoMark===1){
					//return $result;
				}

				//print_r($productModules);

				echo $displayContent;;

				

			}

		}

		exit();

	}

}