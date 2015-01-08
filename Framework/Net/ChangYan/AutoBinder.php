<?php
/**
 * Cntysoft Cloud Software Team
 *
 * @author SOFTBOY <cntysoft@163.com>
 * @copyright  Copyright (c) 2010-2011 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license    http://www.cntysoft.com/license/new-bsd     New BSD License
 */
namespace Cntysoft\Framework\Net\ChangYan;
use Cntysoft\Kernel;
use Cntysoft\Stdlib\Filesystem;
use Zend\Http\Client as HttpClient;
use Zend\Http\Request as HttpRequest;
use Zend\Stdlib\Parameters;
use Cntysoft\Framework\Core\FileRef\Manager as RefManager;
use Gzy\Kernel\StdDir;
use Gzy\Kernel\StdHtmlPath;
/**
 * 搜狐畅言自动绑定
 */
class AutoBinder
{
   /**
    * @var \Zend\Http\Client $client
    */
   protected $client =null;
   /**
    * @var array
    */
   protected $cookies = array();

   /**
    * 登陆之后畅言返回的身份识别令牌
    *
    * @var string $token
    */
   protected $token = null;

   /**
    * 智能绑定
    *
    * @param string $username
    * @param string $password
    * @param array $setting
    */
   public function bind($username, $password, array $setting)
   {
      set_time_limit(0);
      if($this->checkAccountBindStatus($username)){
         $errorType = ErrorType::getInstance();
         Kernel\throw_exception(new Exception(
            $errorType->msg('E_ACCOUNT_ALREADY_BINDED'),
            $errorType->code('E_ACCOUNT_ALREADY_BINDED')
         ));
      }
      $this->login($username, $password);
      $response = $this->requestChangYanPage(Constant::SETTING_COMMON_FURTHER_ENTRY);
      $bodyHtml = $response->getBody();
      preg_match_all('/<input.*?\/>/',$bodyHtml, $match);
      $meta = array();
      foreach($match[0] as $item){
         if(false!=strpos($item, 'appid')){
            $key = 'appid';
         }else if(false != strpos($item, 'appkey')){
            $key= 'appkey';
         }else{
            continue;
         }
         preg_match('/value="(.*?)"/', $item, $value);
         $meta[$key] = $value[1];
      }
      $this->saveAppMetaRequest($meta['appid'], $meta['appkey']);
      $this->applySetting($setting);
   }

   protected function applySetting(array $setting)
   {
      Kernel\ensure_array_has_fields($setting, array(
         'callbackUrl', 'pushBackUrl'
      ));
      $request = new HttpRequest();
      //设置callback
      $request->setPost(new Parameters(array(
         'redirectUrl' => $setting['callbackUrl']
      )));
      $response = $this->requestChangYanPage(Constant::SETTING_CALLBACK_ENTRY, true, $request);
      $ret = json_decode($response->getBody(), true);
      if(!$ret['success']){
         $errorType = ErrorType::getInstance();
         Kernel\throw_exception(new Exception(
            $errorType->msg('E_APPLY_SETTING_ERROR', $ret['msg']),
            $errorType->code('E_APPLY_SETTING_ERROR')
         ));
      }
      $request->setPost(new Parameters(array(
         'pushBackCommentUrl' => $setting['pushBackUrl']
      )));
      $response = $this->requestChangYanPage(Constant::SETTING_PUSH_BACK_ENTRY, true, $request);
      $ret = json_decode($response->getBody(), true);
      if(!$ret['success']){
         $errorType = ErrorType::getInstance();
         Kernel\throw_exception(new Exception(
            $errorType->msg('E_APPLY_SETTING_ERROR', $ret['msg']),
            $errorType->code('E_APPLY_SETTING_ERROR')
         ));
      }
   }

   /**
    * @param string $appid
    * @param string $appkey
    */
   protected function saveAppMetaRequest($appid, $appkey)
   {

   }

   protected function login($username, $password)
   {
      $client = $this->getHttpClient();
      $client->setEncType(HttpClient::ENC_URLENCODED);
      $request = new HttpRequest();
      $request->setMethod('post');
      $request->setUri(Constant::LOGIN_ENTRY);
      $request->setPost(new Parameters(array(
         'ru' => '',
         'name' => $username,
         'password'=> $password
      )));
      $response = $client->send($request);
      if(200 != $response->getStatusCode()){
         $this->throwRequestExp();
      }
      return $response;
   }

   protected function requestChangYanPage($url, $isPost = false, $request = null)
   {
      $client = $this->getHttpClient();
      $client->setOptions(array(
         'encodecookies' => false
      ));
      if(null == $request){
         $request = new HttpRequest();
      }
      if($isPost){
         $request->setMethod('POST');
         $client->setEncType(HttpClient::ENC_URLENCODED);
      }
      $request->setUri($url);
      $response = $client->send($request);
      if(200 != $response->getStatusCode()){
         $this->throwRequestExp();
      }
      return $response;
   }

   /**
    * @param string $username
    * @return boolean
    */
   protected function checkAccountBindStatus($username)
   {
      return false;
   }

   protected function throwRequestExp()
   {
      $errorType = ErrorType::getInstance();
      Kernel\throw_exception(new Exception(
         $errorType->msg('E_REQUEST_ERROR'),
         $errorType->code('E_REQUEST_ERROR')
      ), $errorType);
   }

   /**
    * @return HttpClient
    */
   protected function getHttpClient()
   {
      if($this->client == null){
         $this->client = new HttpClient();
      }
      return $this->client;
   }
}