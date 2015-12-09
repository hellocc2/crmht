<?php
namespace config;
class snsConfig{
    /**
     * 货币符号及名称翻译
     * @var array
     */
     //上传sns类型
    public static $snsType = array(
	   array('name'=>'SNS','val'=>1),
       array('name'=>'Blog','val'=>2),
       array('name'=>'Video','val'=>3)
	);
    //图片尺寸
     public static $pictureSize = array(
	    array('name'=>'200*200','val'=>1),
        array('name'=>'728*90','val'=>2),
        array('name'=>'160*600','val'=>3),
     	array('name'=>'170*220','val'=>4)
	);
    //任务类型
     public static $taskType = array(
	   array('name'=>'只能完成一次','val'=>1),
       array('name'=>'可以重复完成','val'=>2)
	);
     //上传sns类型
    public static $snsTypeOne = array(
        '1'=>'SNS',
        '2'=>'Blog',
        '3'=>'Video',
    	'4'=>'SNS 批量'
	);
    //图片尺寸
     public static $pictureSizeOne = array(
            '1'=>'200*200',
            '2'=>'728*90',
            '3'=>'160*600',
     		'4'=>'170*220',
            '0'=>' '
	);
    //任务类型
    public static $taskTypeOne = array(
        '1'=>'只能完成一次',
        '2'=>'可以重复完成'
	);
    
    //转发类型1 Blog Banners,2 Social Site Post 3.Youtube Video
    public static $sendTypeOne = array(
        '1'=>'Blog Banners',
        '2'=>'Social Site Post',
        '3'=>'Youtube Video'
	);
    
    //商品站点配置
    public static $websiteType = array(
	   array('name'=>'Milanoo','val'=>1),
       array('name'=>'Dressinwedding','val'=>2),
       array('name'=>'LolitaShow','val'=>3),
       array('name'=>'CosplayShow','val'=>4),
       array('name'=>'CostumesLive','val'=>5)
	);
    
     public static $websiteTypeOne = array(
        '1'=>'Milanoo',
        '2'=>'Dressinwedding',
        '3'=>'LolitaShow',
        '4'=>'CosplayShow',
        '5'=>'CostumesLive',
	);
    
    //后台订单审核状态配置
     public static $choosedStateOne = array(
        '0'=>'未审核',
        '1'=>'通过',
        '2'=>'不通过'
	);
    
    //后台语言中文配置
     public static $language = array(
         array('name'=>'英语','val'=>'en-uk'),
         array('name'=>'日语','val'=>'ja-jp'),
         array('name'=>'法语','val'=>'fr-fr'),
         array('name'=>'西班牙语','val'=>'es-sp'),
         array('name'=>'德语','val'=>'de-ge'),
         array('name'=>'意大利语','val'=>'it-it'),
         array('name'=>'葡萄牙语','val'=>'pt-pt'),
         array('name'=>'俄语','val'=>'ru-ru'),
         array('name'=>'荷兰语','val'=>'nl-nl')
	);
    
     public static $languageOne = array(
        'en-uk'=>'英',
        'ja-jp'=>'日',
        'fr-fr'=>'法',
        'es-sp'=>'西',
        'de-ge'=>'德',
        'it-it'=>'意',
        'pt-pt'=>'葡',
        'ru-ru'=>'俄',
        'nl-nl'=>'荷'
	);
    //后台管理员权限名单配置
     public static $adminManageOne = array(
        '0'=>'马国涛',
        '1'=>'maguotao',
        '2'=>'admin'
	);

    // 数据状态
    const DATA_STATUS_NORMAL=0;     // 正常显示在前台
    const DATA_STATUS_DELETED=1;   // 逻辑删除
    const DATA_STATUS_HIDDEN=2;     // 在前台不显示，在后台可查看和管理

    const DATA_STATUS_MEMO_NORMAL='正常';
    const DATA_STATUS_MEMO_DELETED='逻辑删除';
    const DATA_STATUS_MEMO_HIDDEN='未激活';

    public static $dataStatus=array(
        'normal'            =>self::DATA_STATUS_NORMAL,
        'hidden-normal'    =>self::DATA_STATUS_NORMAL,
        'deleted'           =>self::DATA_STATUS_DELETED,
        'hidden'            =>self::DATA_STATUS_HIDDEN
    );

    public static $dataStatusMemo=array(
        self::DATA_STATUS_NORMAL    =>self::DATA_STATUS_MEMO_NORMAL,
        self::DATA_STATUS_HIDDEN    =>self::DATA_STATUS_MEMO_HIDDEN,
    );

    public static $dataStatusForShow=array(
        array('val'=>self::DATA_STATUS_NORMAL, 'name'=>self::DATA_STATUS_MEMO_NORMAL),
        array('val'=>self::DATA_STATUS_HIDDEN, 'name'=>self::DATA_STATUS_MEMO_HIDDEN),
    );

    // #29387 积分操作时，在数据库表 milanoo_sns_member_score_detail的actionName字段中，以该符号分隔操作名称 和 备注
    public static $scoreDescGlue='###';
}
?>