<?php
namespace Module\Errors;
/**
 * 页面报错模块
 * @author ren fei
 * @sinc 2012-10-17
 * @param int 
 * @param int 
 */
class Url404 extends \Lib\common\Application {
	public function __construct() {
		//罗列各语言站翻译备用
		$arr_en=array(
				"1"=>"Opps…",
				"2"=>"Sorry, the page you are looking for could not be found or no longer exists.",
				"3"=>"Tips:",
				"4"=>"• Check the spelling of the URL and enter it again.",
				"5"=>"• Go to ",
				"6"=>"Milanoo.com Home Page.",
				"7"=>"Sitemap",
				"8"=>"",
				"langhomepage"=>"http://www.milanoo.com",			
		);
		
		$arr_fr=array(
				"1"=>"Désolé…",
				"2"=>"La page que vous recherchez est invalide ou n’existe plus. ",
				"3"=>"Suggestions:",
				"4"=>"• Vérifier si le lien est correct et ressayer plus tard.",
				"5"=>"• Accéder à la ",
				"6"=>"page d’accueil Milanoo.com.",
				"7"=>"Plan du site",
				"8"=>"",
				"langhomepage"=>"http://www.milanoo.com/fr",				
		);
		
		$arr_de=array(
				"1"=>"Ups!",
				"2"=>"Die von Ihnen angeforderte Seite konnte leider nicht gefunden werden.",
				"3"=>"Tipps:",
				"4"=>"• Überprüfen Sie den Link oder versuchen Sie noch einmal.",
				"5"=>"• Zu ",
				"6"=>"Home von Milanoo.com.",
				"7"=>"Sitemap",
				"8"=>"",
				"langhomepage"=>"http://www.milanoo.com/de",
		);
		
		
		$arr_jp=array(
				"1"=>"ページが見つかりません……",
				"2"=>"申し訳ございません。ご指定のページが見つかりませんでした。本当に必死に探したんですけどね……",
				"3"=>"そこで以下のことをお試しください。",
				"4"=>"・URLが正しいかどうかを再度ご確認ください。もし間違えてたらな、もうこのうっかりさん！",
				"5"=>"・もしくは",
				"6"=>"Milanoo.comホームページ",
				"7"=>"サイトマップ",
				"8"=>"にお戻りください。ミラノーはいいものいっぱいで楽しいサイトですよ。今回はページが見つかりませんでしたが、こういうことは人生には起こりうるものです。でも決してあきらめないでください。このページを見たあなたにはきっと幸せが訪れます。なにせ「４０４」ですよ。「４・幸せ」・「０・を」・「４・呼ぶ」ページなんですから！なので、これにめげず、もう一度ミラノーで楽しいお買い物を！　そして、素晴らしき人生を！",
				"langhomepage"=>"http://www.milanoo.com/jp",				
		);
		
		
		$arr_it=array(
				"1"=>"Opps…",
				"2"=>"Siamo spiacienti, la pagina che state cercando non e' trovata o non esiste piu'.",
				"3"=>"Consigli:",
				"4"=>"• Controlla l'ortografia dell'indirizzo e inserirlo nuovamente.",
				"5"=>"• Andate alla ",
				"6"=>"Home Page di Milanoo.com.",
				"7"=>"Mappa del sito",
				"8"=>"",
				"langhomepage"=>"http://www.milanoo.com/it",
		);
		
		
		$arr_ru=array(
				"1"=>"Упс-с-с...",
				"2"=>"Извините, страница, которую вы ищете, не найдена или больше не существует.",
				"3"=>"Совет:",
				"4"=>"•Проверьте правильность написания URL и введите его еще раз.",
				"5"=>"•На ",
				"6"=>"главную страницу Milanoo.com.",
				"7"=>"Карта сайта",
				"8"=>'',
				"langhomepage"=>"http://www.milanoo.com/ru",
		);
		
		
		$arr_es=array(
				"1"=>"Uyyy…",
				"2"=>"No se encontró la página. Puede que ya no exista, haya cambiado de nombre o no esté disponible temporalmente.",
				"3"=>"Pruebe lo siguiente:",
				"4"=>"• Si escribió la dirección de la página en la barra de direcciones, compruebe que esté escrita correctamente.",
				"5"=>"• Abra ",
				"6"=>"la página principal de milanoo.com",
				"7"=>"sisukaart",
				"8"=>" y busque vínculos a la información que desea.",
				"langhomepage"=>"http://www.milanoo.com/es",
		);
		
		$arr_pt=array(
				"1"=>"Opps…",
				"2"=>"Desculpe, a página que você está a procurar não pode ser encontrado ou não existe mais.",
				"3"=>"Dicas:",
				"4"=>"• Verifique a ortografia da URL e inseri-lo novamente.",
				"5"=>"• Vai para ",
				"6"=>"Milanoo.com Home Page.",
				"7"=>"Site map",
				"8"=>'',
				"langhomepage"=>"http://www.milanoo.com/pt",
		);
		
		//拿cookie判断站点语言
		//if(isset($_COOKIE['lang_cookie'])){
		//		$langcookie=$_COOKIE['lang_cookie'];
		//}else{
		//	$langcookie="en-uk";
		//}
		
		switch(SELLER_LANG){
			case "en-uk":
				$arr=$arr_en; break;
			case "ja-jp":
				$arr=$arr_jp; break;
			case "fr-fr":
				$arr=$arr_fr; break;
			case "es-sp":
				$arr=$arr_es; break;
			case "de-ge":
				$arr=$arr_de; break;
			case "it-it":
				$arr=$arr_it; break;
			case "ru-ru":
				$arr=$arr_ru; break;
			case "pt-pt":
				$arr=$arr_pt; break;
			default:
				$arr=$arr_en;				
		}
		
		//接口得到回复的菜单，到2级目录
		$mNav = new \Model\Navigator();
		$secondNav = $mNav->getNav(0,'-1:-1');
		//递归调用回复得到数组
		$menuList=\Helper\String::strDosTrip($secondNav['resultList']);
		
		
		//var_dump($menuList);
		//\Helper\ResponseUtil::rewrite(array('url'=>'?module=thing&action=glist&class=391','seo'='catName'))
		//echo \Helper\ResponseUtil::rewrite(array('url'=>'?module=thing&action=glist&class=391','seo'=>'Wedding'));
		//exit();
		
		//嵌套到模板
		$tpl = \Lib\common\Template::getSmarty ();
		//$params=\Helper\RequestUtil::getParams();
		//$params=$params->params;
	
		$tpl->assign('line1', $arr["1"]);
		$tpl->assign('line2', $arr["2"]);
		$tpl->assign('line3', $arr["3"]);
		$tpl->assign('line4', $arr["4"]);
		$tpl->assign('line5', $arr["5"]);
		$tpl->assign('line6', $arr["6"]);
		$tpl->assign('line7', $arr["7"]);
		$tpl->assign('line8', $arr["8"]);
		
		
		$tpl->assign('langhomepage', $arr["langhomepage"]);
		$tpl->assign('menulist', $menuList);
		
		$tpl->display ( 'error.htm' );
	}
}





