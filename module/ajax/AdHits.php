<?php 
namespace Module\Ajax;
use Helper\RequestUtil as R;

/**
 * 广告点击处理
 * 记录点击数，跳转目标URL
 * @author chengjun <chengjun@milanoo.com>
 *
 */
class adHits extends \Lib\common\Application{
	/**
	 * 缓存点击最大次数
	 * @var unknown_type
	 */
	const MAX_SIZE = 100;
	/**
	 * memcache点击次数和显示次数键值
	 * @var unknown_type
	 */
	const CACHE_AD_HITS_KEY = 'adMainWebHits_';
	const CACHE_AD_SHOW_KEY = 'adMainWebShow_';
	
	public function __construct(){
		$hitsRateData = array();
		$hitsCache = 0;
		$showCache = 0;
		
		$params = R::getParams('params');
		$url = !empty($params['url']) ? $params['url'] : '';
		$adId = !empty($params['adId']) ? $params['adId'] : '';
		$noHeader = !empty($params['noHeader']) ? $params['noHeader'] : '';
		if($noHeader==1){
			//表示直连广告通过ajax请求写入点击次数
			if('XMLHttpRequest' !== $_SERVER[ 'HTTP_X_REQUESTED_WITH' ]){
			// 回應 非法 AJAX 請求，例如JSON格式
				header ('HTTP/1.1 404 Not found');
            	require ROOT_PATH.'errors/404.php';
            	exit;
			}
			$url = '';
		}elseif(!$adId || !$url){
			return false;
		}
		$url = base64_decode ( urldecode ( $url ) );
		
		//memcached初始化	
		$mem = \Lib\Cache::init();
		$hitsCache = $mem->get(self::CACHE_AD_HITS_KEY.$adId);
		$showCache = $mem->get(self::CACHE_AD_SHOW_KEY.$adId);
		//缓存点击次数，
		if(!$hitsCache){
			$hitsCache +=1;
			$mem->set(self::CACHE_AD_HITS_KEY.$adId,$hitsCache);
		}else{
			$hitsCache +=1;
			if($hitsCache < self::MAX_SIZE){
				$mem->set(self::CACHE_AD_HITS_KEY.$adId,$hitsCache);
			}else{
				$hitsRateData[] = array('advertId'=>$content['adId'],'hits'=>$hitsCache,'showTimes'=>$showCache);
				//重置计算次数
				$showCache = 0;
				$hitsCache = 0;
				$mem->set(self::CACHE_AD_SHOW_KEY.$adId, $showCache);
				$mem->set(self::CACHE_AD_HITS_KEY.$adId, $hitsCache);
				//更新进数据库
				$adM = new \Model\Ad();
				$adM->updateHitsRate(array('advertUpdateStr'=>json_encode($hitsRateData)));
			}
		}
		if(empty($noHeader)){
			//header ( "Location:" . $url );
			echo "<script>window.location.href='$url'</script>";
			exit;
		}else{
			exit('success');
		}
		return ;
	}
}