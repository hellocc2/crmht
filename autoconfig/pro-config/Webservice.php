<?php 
namespace config;
/**
 * Webservice 相关的配置
 * @author Su Chao<suchaoabc@163.com>
 * @since 2011-10-11
 * @todo 认证协议
 */
class Webservice{	
    /**
     * webservice 服务器域名及服务调用的根目录.如:http://www.milanoo.com/ws/
     * @var string
     */
    const SERVER_BASE = 'http://172.20.20.37:8080/';//本地接口 #29144,chengbolin@milanoo.com 更正线上环境为正确的地址
	//const SERVER_BASE = 'http://118.123.244.91:8080/products/';//温江外网测试接口
	/**
	 * PHP API根目录(1.0的API目录).
	 * @var string
	 */
	const SERVER_BASE2 = 'http://apache.milanoo.com/api/';
    /**
     * webservice服务器需要的认证标识
     * @var string
     */
    const AUTH_TOKEN = '';
    
    /**
     * webservice 调用超时时间.单位:秒.注意:由于cul自身的问题,这个是数值必须大于等于1000ms,否则将报超时错误.
     * @var int
     */
    const TIMEOUT  = 30;
}