<?php
/**
 * Cntysoft Cloud Software Team
 *
 * @author SOFTBOY <cntysoft@163.com>
 * @copyright  Copyright (c) 2010-2011 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license    http://www.cntysoft.com/license/new-bsd     New BSD License
 */
namespace Cntysoft\Framework\Net\Msg;
use Cntysoft\Framework\Net\ChangYan\Exception;
use Zend\Http\Client as HttpClient;
use Zend\Http\Request as HttpRequest;
use Cntysoft\Kernel;
use Zend\Json\Server\Error;
use Zend\Stdlib\Parameters;
use Cntysoft\Kernel\ConfigProxy;
/**
 * 企业精灵
 */
class CompanySpirit
{
   /**
    * @var \Zend\Http\Client $client
    */
   protected $client = null;
   protected $errorMap = array(
      1  => 'USER_OR_PWD_ERROR',
      2  => 'EMPTY_NUMBER',
      3  => 'NUMBER_INVALID',
      4  => 'CONTENT_INVALID',
      5  => 'CONTENT_HAS_INVALID_KEYWORD',
      6  => 'SIGN_NOT_EXIST',
      7 => 'MSG_PLATFORM_CLOSED',
      8 => 'MSG_POOL_EMPTY'
   );
   protected $username;
   protected $password;

   public function __construct($username = null, $password = null)
   {
      $this->username= $username;
      $this->password = $password;
      if(null == $this->username || null == $this->password){
         $cfg = ConfigProxy::getFrameworkConfig('Net');
         if(isset($cfg['companySpirit'])){
            if(isset($cfg['companySpirit']['username'])){
               $this->username= $cfg->companySpirit->username;
            }
            if(isset($cfg['companySpirit']['password'])){
               $this->password = $cfg->companySpirit->password;
            }
         }
      }
   }
   /**
    * 发送短信接口
    *
    * @param array $phoneNumbers
    * @param string $content 短信内容
    * @param string signText 该条短信签名
    */
   public function sendMsg(array $phoneNumbers, $content, $signText)
   {
      $content = $content . '【'.$signText.'】';
      $count = iconv_strlen($content);
      if($count > 260){
         $errorType = ErrorType::getInstance();
         Kernel\throw_exception(new Exception(
            $errorType->msg('E_MSG_TOO_LONG'),
            $errorType->code('E_MSG_TOO_LONG')
         ));
      }
      $numbers = implode(',', $phoneNumbers);
      $ret = $this->requestApiUrl(array(
         'username' => $this->username,
         'password' => $this->password,
         'mobiles' => $numbers,
         'targetdate' => '',
         'content' => $content
      ));
      return $ret->getBody();
   }

   /**
    * 获取剩余短信
    *
    * @return int
    */
   public function getRemainMsgNumber()
   {
      $ret = $this->requestApiUrl(array(
         'username' => $this->username,
         'password' => $this->password,
         'queryoddcount' => 1
      ));
      return (int)$ret->getBody();
   }

   /**
    * @param array $params
    * @return \Zend\Http\Response
    * @throws \Exception
    */
   protected function requestApiUrl(array $params = array())
   {
      $client = $this->getHttpClient();
      $request = new HttpRequest();
      if(!empty($params)){
         $request->setPost(new Parameters($params));
      }
      $request->setMethod('POST');
      $client->setEncType(HttpClient::ENC_URLENCODED);
      $request->setUri(Constant::API_ENTRY);
      $response = $client->send($request);
      if(200 !== $response->getStatusCode()) {
         $errorType = ErrorType::getInstance();
         Kernel\throw_exception(new Exception(
            $errorType->msg('E_REQUEST_ERROR'),
            $errorType->code('E_REQUEST_ERROR')
         ), $errorType);
      }
      return $response;
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

}