<?php
/**
 * Cntysoft Cloud Software Team
 */
namespace Cntysoft\Framework\Cloud\Ali\OpenSearch;
use Cntysoft\Kernel;
use Zend\Http\Client as HttpClient;
use Zend\Http\Header\AcceptEncoding;
/**
 * @author SOFTBOY <cntysoft@163.com>
 * @copyright  Copyright (c) 2010-2011 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license    http://www.cntysoft.com/license/new-bsd     New BSD License
 */
class ApiCaller
{
   const KEY_TYPE_ALIYUN = 'aliyun';
   const KEY_TYPE_OPENSEARCH = 'opensearch';
   const API_PUB_ENTRY = 'http://opensearch-cn-beijing.aliyuncs.com';
   const API_INTERNAL_ENTRY = 'http://intranet.opensearch-cn-beijing.aliyuncs.com';
   const M_GET = 'GET';
   const M_POST = 'POST';
   /**
    * @var \Zend\Http\Client $httpClient
    */
   protected $httpClient;
   protected $timeout = 10;
   protected $keyType;
   protected $signatureVersion = '1.0';
   protected $signatureMethod = 'HMAC-SHA1';
   /**
    * 用户的client id。key_type为opensearch使用
    *
    * 此信息由网站中提供。
    *
    * @var string
    */
   protected $clientId;

   /**
    * 用户的秘钥。key_type为opensearch使用
    *
    * 此信息由网站中提供。
    *
    * @var string
    */
   protected $clientSecret;

   /**
    * 用户阿里云网站中的accessKeyId,key_type为aliyun使用
    *
    * 此信息阿里云网站中提供
    *
    * @var string
    */
   protected $accessKeyId;

   /**
    * 用户阿里云accessKeyId对应的秘钥，key_type为aliyun使用
    *
    * 此信息阿里云网站中提供
    *
    */
   protected $secret;
   /**
    * @var string $version
    */
   protected $version = 'v2';
   /**
    * 是否打开连接
    *
    * @var bool $gzip
    */
   protected $gzip = false;
   public function __construct($key, $secret, array $opts, $keyType = self::KEY_TYPE_ALIYUN)
   {
      $this->keyType = $keyType;
      if ($this->keyType == self::KEY_TYPE_OPENSEARCH){
         $this->clientId = $key;
         $this->clientSecret = $secret;
      } elseif ($this->keyType == self::KEY_TYPE_ALIYUN){
         $this->accessKeyId = $key;
         $this->secret = $secret;
      } else {
         $this->keyType = 'opensearch';
         $this->clientId = $key;
         $this->clientSecret = $secret;
      }
      if (isset($opts['signatureMethod']) && !empty($opts['signatureMethod'])) {
         $this->signatureMethod = $opts['signatureMethod'];
      }
      if (isset($opts['signatureVersion']) && !empty($opts['signatureVersion'])) {
         $this->signatureVersion = $opts['signatureVersion'];
      }
      if (isset($opts['timeout']) && !empty($opts['timeout'])) {
         $this->timeout= $opts['timeout'];
      }

      if (isset($opts['gzip']) && $opts['gzip'] == true) {
         $this->gzip = true;
      }
   }

   /**
    * @param $api
    * @param array $params
    * @param string $method
    * @return array
    */
   public function call($api, array $params = array(), $method = self::M_GET)
   {
      if(SYS_RUNTIME_MODE == SYS_RUNTIME_MODE_DEBUG){
         $entry = self::API_PUB_ENTRY;
      }else if(SYS_RUNTIME_MODE == SYS_RUNTIME_MODE_PRODUCT){
         $entry = self::API_INTERNAL_ENTRY;
      }
      $requestUrl = $entry .'/'.$api;
      if($this->keyType == self::KEY_TYPE_OPENSEARCH){
         $params['client_id'] = $this->clientId;
         $params['nonce'] = $this->nonce();
         $params['sign'] = $this->sign($params);
      }else {
         $params['Version'] = $this->version;
         $params['AccessKeyId'] = $this->accessKeyId;
         $params['SignatureMethod']=$this->signatureMethod;
         $params['SignatureVersion']=$this->signatureVersion;
         $params['SignatureNonce'] = $this->nonceAliyun();
         $params['Timestamp'] = gmdate('Y-m-d\TH:i:s\Z');
         $params['Signature'] = $this->signAliyun($params,$method);
      }
      $httpClient = $this->getHttpClient();
      $httpClient->setUri($requestUrl);
      $httpClient->setMethod($method);
      if(self::M_GET == $method){
         $httpClient->setParameterGet($params);
      }else{
         $httpClient->setParameterPost($params);
      }
      $response = $httpClient->send();
      if($response->getStatusCode() != \Zend\Http\Response::STATUS_CODE_200){
         $errorType = ErrorType::getInstance();
         Kernel\throw_exception(new Exception(
            $errorType->msg('REQUEST_ERROR', $response->getReasonPhrase()),
            $errorType->code('REQUEST_ERROR')
         ));
      }
      return json_decode($response->getBody(), true);
   }

   /**
    * 生产当前的aliyun签名方式对应的nonce值
    *
    * NOTE：这个值要保证访问唯一性，建议用如下算法，商家也可以自己设置一个唯一值
    *
    * @return string  返回生产的nonce串
    */
   protected function nonceAliyun()
   {
      $microtime = $this->getMicrotime();
      return $microtime . mt_rand(1000,9999);
   }

   protected function getMicrotime()
   {
      list($usec, $sec) = explode(" ", microtime());
      return floor(((float)$usec + (float)$sec) * 1000);
   }

   /**
    * 根据参数生成当前得签名
    *
    * 如果指定了sign_mode且sign_mode为1，则参数中的items将不会被计算签名
    *
    * @param array $params 返回生成签名
    * @return string
    */
   protected function signAliyun($params = array(), $method = self::M_GET)
   {
      if (isset($params['sign_mode']) && $params['sign_mode'] == 1) {
         unset($params['items']);
      }
      $params = $this->paramsFilter($params);
      $query = '';
      $arg = '';
      if(is_array($params) && !empty($params)){
         while (list ($key, $val) = each ($params)) {
            $arg .= $this->percentEncode($key) . "=" . $this->percentEncode($val) . "&";
         }
         $query = substr($arg, 0, count($arg) - 2);
      }
      $baseString = strtoupper($method).'&%2F&' .$this->percentEncode($query);
      return base64_encode(hash_hmac('sha1', $baseString, $this->secret."&", true));
   }

   /**
    * 过滤阿里云签名中不用来签名的参数,并且排序
    *
    * @param array $params
    * @return array
    *
    */
   protected function paramsFilter($parameters = array())
   {
      $params = array();
      while (list ($key, $val) = each ($parameters)) {
         if ($key == "Signature" ||$val === "" || $val === NULL){
            continue;
         } else {
            $params[$key] = $parameters[$key];
         }
      }
      ksort($params);
      reset($params);
      return $params;
   }

   protected function percentEncode($str)
   {
      // 使用urlencode编码后，将"+","*","%7E"做替换即满足 API规定的编码规范
      $res = urlencode($str);
      $res = preg_replace('/\+/', '%20', $res);
      $res = preg_replace('/\*/', '%2A', $res);
      $res = preg_replace('/%7E/', '~', $res);
      return $res;
   }

   /**
    * 根据参数生成当前的签名。
    *
    * 如果指定了sign_mode且sign_mode为1，则参数中的items将不会被计算签名。
    *
    * @param array $params 返回生成的签名。
    * @return string
    */
   protected function sign($params = array())
   {
      $query = "";
      if (isset($params['sign_mode']) && $params['sign_mode'] == 1) {
         unset($params['items']);
      }
      if (is_array($params) && !empty($params)) {
         ksort($params);
         $query = $this->buildQuery($params);
      }
      return md5($query . $this->clientSecret);
   }

   /**
    * 把数组生成http请求需要的参数。
    * @param array $params
    * @return string
    */
   private function buildQuery($params)
   {
      $args = http_build_query($params, '', '&');
      // remove the php special encoding of parameters
      // see http://www.php.net/manual/en/function.http-build-query.php#78603
      //return preg_replace('/%5B(?:[0-9]|[1-9][0-9]+)%5D=/', '=', $args);
      return $args;
   }

   /**
    * 生成当前的nonce值。
    *
    * NOTE: $time为10位的unix时间戳。
    *
    * @return string 返回生成的nonce串。
    */
   protected function nonce()
   {
      $time = time();
      return md5($this->clientId . $this->clientSecret . $time) . '.' . $time;
   }

   protected function getHttpClient()
   {
      if(null == $this->httpClient){
         $this->httpClient = new HttpClient();
         if($this->gzip){
            $header = new AcceptEncoding();
            $header->addEncoding('gzip');
            $this->httpClient->setHeaders(array(
               $header
            ));
            $this->httpClient->setOptions(array(
               'timeout' => $this->timeout
            ));
         }
      }
      return $this->httpClient;
   }
}