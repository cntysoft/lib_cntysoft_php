<?php
/**
 * Cntysoft Cloud Software Team
 * 
 * @author Arvin <cntyfeng@163.com>
 * @copyright Copyright (c) 2010-2015 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license http://www.cntysoft.com/license/new-bsd     New BSD License
*/
namespace Cntysoft\Framework\Pay\WeChat;

class Constant
{
   /**
    * APP_ID：绑定支付的appId（必须配置，开户邮件中可查看）
    * APP_SECRET：公众帐号secert（仅JSAPI支付的时候需要配置， 登录公众平台，进入开发者中心可设置），获取地址：https://mp.weixin.qq.com/advanced/advanced?action=dev&t=advanced/dev&token=2005451881&lang=zh_CN
    * MERCHANT_ID：商户号（必须配置，开户邮件中可查看）
    * KEY：商户支付密钥，参考开户邮件设置（必须配置，登录商户平台自行设置）,设置地址：https://pay.weixin.qq.com/index.php/account/api_cert
    */
   //公众号相关支付参数
   const MP_APP_ID = 'wx61a19cda5d4d8a9d';
   const MP_APP_SECRET = 'e96c5901532608b36b810d01a1ccb295';
   const MP_MERCHANT_ID = '1273867401';
   const MP_KEY = '61851df3711f2567d6e53a3af15f8c24';
   //APP相关支付参数
   const APP_APP_ID = 'wxc752851abec3c987';
   const APP_APP_SECRET = 'd4624c36b6795d1d99dcf0547af5443d';
   const APP_MERCHANT_ID = '1281938301';
   const APP_KEY = 'F33F3E24621C5A3B83CF0595C881BFD7';
   
   /**
    * 支付回调地址，用来通知商户是否已经支付成功
    */
   const NOTIFY_URL = 'http://www.fhzc.com/notify/wechat.html';
   
   /**
    * 设置商户证书路径,注意应该填写绝对路径（仅退款、撤销订单时需要，可登录商户平台下载，
	 * API证书下载地址：https://pay.weixin.qq.com/index.php/account/api_cert，下载之前需要安装商户操作证书）
   */
   //公众号支付的证书地址
   const MP_SSL_CERT_PATH = '';
   const MP_SSL_KEY_PATH = '';
   //APP支付的证书地址
   const APP_SSL_CERT_PATH = '';
   const APP_SSL_KEY_PATH = '';
   
   //这里设置代理机器，只有需要代理的时候才设置，不需要代理，请设置为0.0.0.0和0
   const CURL_PROXY_HOST = '0.0.0.0';
   const CURL_PROXY_PORT = '0';
   
   /**
    * 接口调用上报等级，默认仅错误上报（注意：上报超时间为【1s】，上报无论成败【永不抛出异常】，
	 * 不会影响接口调用流程），开启上报之后，方便微信监控请求调用的质量，建议至少开启错误上报。
	 * 上报等级，0.关闭上报; 1.仅错误出错上报; 2.全量上报
   */
   const REPORT_LEVEL = 1;
   
   /**
    * 统一下单的网址
    */
   const UNIFIED_ORDER_URL = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
   /**
    * 错误上报网址
    */
   const REPORT_URL = 'https://api.mch.weixin.qq.com/payitil/report';
   /**
    * 查询订单状态的网址
    */
   const ORDER_QUERY_URL = 'https://api.mch.weixin.qq.com/pay/orderquery';
   /**
    * 关闭订单的网址
    */
   const ORDER_CLOSE_URL = 'https://api.mch.weixin.qq.com/pay/closeorder';
   /**
    * 申请退款的网址
    */
   const REFUND_URL = 'https://api.mch.weixin.qq.com/secapi/pay/refund';
   /**
    * 退款查询网址
    */
   const REFUND_QUERY_URL = 'https://api.mch.weixin.qq.com/pay/refundquery';
   /**
    * 下载对账单的网址
    */
   const DOWNLOAD_BILL_URL = 'https://api.mch.weixin.qq.com/pay/downloadbill';
   /**
    * 提交被扫支付API接口的调用网址
    */
   const MICRO_PARY_URL = 'https://api.mch.weixin.qq.com/pay/micropay';
   /**
    * 撤销订单网址
    */
   const REVERSE_URL = 'https://api.mch.weixin.qq.com/secapi/pay/reverse';
   /**
    * 转换链接为短链接
    */
   const SHORT_URL = 'https://api.mch.weixin.qq.com/tools/shorturl';
   /**
    * 用户授权认证，获取code的网址
    */
   const AUTHORIZE = 'https://open.weixin.qq.com/connect/oauth2/authorize?';
   /**
    * 获取用户access_token的网址
    */
   const ACCESS_TOKEN = 'https://api.weixin.qq.com/sns/oauth2/access_token?';
}
