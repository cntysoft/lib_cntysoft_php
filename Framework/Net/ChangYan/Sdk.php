<?php
/**
 * Cntysoft Cloud Software Team
 *
 * @author SOFTBOY <cntysoft@163.com>
 * @copyright  Copyright (c) 2010-2011 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license    http://www.cntysoft.com/license/new-bsd     New BSD License
 */
namespace Cntysoft\Framework\Net\ChangYan;
use Zend\Http\Client as HttpClient;
use Zend\Http\Request as HttpRequest;
use Cntysoft\Kernel;
use Cntysoft\Kernel\ConfigProxy;
use Zend\Stdlib\Parameters;
/**
 * 畅言sdk封装
 */
class Sdk
{
   const CACHE_KEY = 'ChangYanCacheKey';
   /**
    * @var \Zend\Http\Client $client
    */
   protected $client = null;
   /**
    * @var string $appId
    */
   protected $appId = null;
   /**
    * @var string $appSecret
    */
   protected $appSecret = null;
   /**
    * @var array $apiErrorMap
    */
   protected $apiErrorMap = null;

   /**
    * @var \Phalcon\Cache\Backend\File
    */
   protected $cacher = null;

   public function  retrieveAccessToken($code)
   {
      if(null == $this->appId || null == $this->appSecret){
         $meta = self::getAppIdAndAppKey();
         $this->appId = $meta->appid;
         $this->appSecret = $meta->appkey;
      }
      $ret = $this->requestApiUrl(Constant::ACCESS_TOKEN_POINT, true, array(
         'client_id' => $this->appId,
         'client_secret' => $this->appSecret,
         'grant_type' => 'authorization_code',
         'code' => $code,
         'redirect_uri' => 'http://changyan.gongzuoyi.net/ChangYan/Callback'
      ));
      if(isset($ret['error_msg'])){
         $errorType = ErrorType::getInstance();
         Kernel\throw_exception(new Exception(
            $errorType->msg('E_API_INVOKE_ERROR', $ret['error_msg']),
            $errorType->code('E_API_INVOKE_ERROR')
         ));
      }
      $cacher = $this->getCacher();
      $cacher->save(self::CACHE_KEY, $ret['access_token'], $ret['expires_in']);
   }

   protected function requestApiUrl($url, $isPost, array $params = array())
   {
      $client = $this->getHttpClient();
      $request = new HttpRequest();
      if($isPost){
         if(!empty($params)){
            $request->setPost(new Parameters($params));
         }
         $request->setMethod('POST');
         $client->setEncType(HttpClient::ENC_URLENCODED);
      }else{
         $request->setMethod('GET');
      }
      $url = Constant::API_ENTRY.'/'.$url;
      $request->setUri($url);
      $response = $client->send($request);
      if(200 !== $response->getStatusCode()){
         $errorType = ErrorType::getInstance();
         Kernel\throw_exception(new Exception(
            $errorType->msg('E_REQUEST_ERROR'),
            $errorType->code('E_REQUEST_ERROR')
         ), $errorType);
      }
      $ret = json_decode($response->getBody(), true);
      return $ret;
   }

   public static function openAuthorizerPage()
   {
      $meta = self::getAppIdAndAppKey();
      $url = Constant::API_ENTRY.'/'.Constant::AUTHORIZE_POINT.'?'.http_build_query(array(
            'client_id' => $meta->appid,
            'redirect_uri' => 'http://changyan.gongzuoyi.net/ChangYan/Callback',
            'response_type' => 'code',
            'display' => Constant::DISPLAY_T_WEB,
            'platform_id' => 0
         ));
      echo <<<PAGE
<script language = "javascript">

var winWidth = 600;
var winHeight = 210;
var topPos = (window.screen.height-30-winHeight)/2;
var leftPost = (window.screen.width-10-winWidth)/2;
window.open('$url','畅言认证',
'height='+winHeight+',width='+winWidth+',top='+topPos+',left='+leftPost+',toolbar=no,menubar=no,scrollbars=no,resizable=no,location=no, status=no')
</script>
PAGE;
   }

   /**
    * 关闭当前的认证页面
    */
   public static function closeAuthorizerPage()
   {
      echo <<<PAGE
<script language = "javascript">
window.close();
</script>
PAGE;
   }

   /**
    * 从配置文件获取相关信息
    *
    * @return \Phalcon\Config
    */
   protected static function getAppIdAndAppKey()
   {
      $netCfg = ConfigProxy::getFrameworkConfig('Net');
      if(!isset($netCfg['changYan']) || !isset($netCfg['changYan']['appid']) || !isset($netCfg['changYan']['appkey'])){
         $errorType = ErrorType::getInstance();
         Kernel\throw_exception(new Exception(
            $errorType->msg('E_SDK_CONFIG_NOT_EXIST'),
            $errorType->code('E_SDK_CONFIG_NOT_EXIST')
         ));
      }
      return $netCfg->changYan;
   }

   /**
    * @return \Zend\Http\Client
    */
   protected function getHttpClient()
   {
      if(null == $this->client){
         $this->client = new HttpClient();
      }
      return $this->client;
   }

   /**
    * @return \Phalcon\Cache\Backend\File
    */
   protected function getCacher()
   {
      if(null == $this->cacher){
         $this->cacher = Kernel\make_cache_object(implode(DS, array('Framework', 'Net', 'ChangYan' ,'AccessToken')), 7000);
      }
      return $this->cacher;
   }
}