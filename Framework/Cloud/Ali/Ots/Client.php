<?php
/**
 * Cntysoft Cloud Software Team
 *
 * @author SOFTBOY <cntysoft@163.com>
 * @copyright  Copyright (c) 2010-2011 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license    http://www.cntysoft.com/license/new-bsd     New BSD License
 */
namespace Cntysoft\Framework\Cloud\Ali\Ots;
use Cntysoft\Framework\Qs\Engine\Exception;
use Zend\Http\Client as HttpClient;
use Zend\Http\Headers as HttpHeaders;
use Cntysoft\Kernel\ConfigProxy;
use DrSlump\Protobuf\Message;
use DrSlump\Protobuf;
use Cntysoft\Framework\Cloud\Ali\Ots\ApiMessage;
use Cntysoft\Kernel;

/**
 * 封装阿里巴巴NO-SQL服务客户端
 */
class Client
{
   const INTERNAL_API = 'http://gzy-ots.cn-hangzhou.ots-internal.aliyuncs.com';
   const PUB_API = 'http://gzy-ots.cn-hangzhou.ots.aliyuncs.com';

   const API_LIST_TABLE = 'ListTable';


   protected $useInternalApi = false;
   protected $accessKey;
   protected $accessKeySecret;
   protected $instanceName;
   /**
    * @var \Zend\Http\Client $client
    */
   protected $client;

   public function __construct($instanceName = null, $accessKey = null, $accessKeySecret = null)
   {
      $this->instanceName = $instanceName;
      $this->accessKey = $accessKey;
      $this->accessKeySecret = $accessKeySecret;
      if (null == $instanceName || null == $accessKey || null == $accessKeySecret) {
         $this->setupDefaultAccessCfg();
      }
   }

   /**
    * 获取当前实例所有的数据表
    *
    * @return array
    */
   public function getTableNames()
   {
      $response = $this->requestOtsApi(self::API_LIST_TABLE, new ApiMessage\EmptyRequest());
      $responseBuf = new ApiMessage\ListTableResponse();
      $responseBuf->parse($response->getBody(), Protobuf::getCodec('Binary'));
      return $responseBuf->table_names;
   }

   

   /**
    * @param string $api
    * @param Message $message
    * @return \Zend\Http\Response
    * @throws \Exception
    */
   protected function requestOtsApi($api, Message $message)
   {
      $httpClient = $this->getHttpClient();
      //计算几项值
      $request = $httpClient->getRequest();
      $headers = $request->getHeaders();
      $body = $message->serialize();
      $request->setContent($body);
      $headers->addHeaderLine('x-ots-contentmd5', base64_encode(md5($body, true)));
      $signatureHeaderNames = array(
         'x-ots-accesskeyid',
         'x-ots-apiversion',
         'x-ots-contentmd5',
         'x-ots-date',
         'x-ots-instancename'
      );
      $canonicalHeaders = '';
      foreach ($signatureHeaderNames as $hname) {
         $canonicalHeaders .= $hname . ':' . trim($headers->get($hname)->getFieldValue()) . "\n";
      }
      $strToSignature = '/' . $api . "\n" . 'POST' . "\n\n" . $canonicalHeaders;
      $signature = base64_encode(hash_hmac('sha1',$strToSignature, $this->accessKeySecret, true));
      $headers->addHeaderLine('x-ots-signature', $signature);
      $httpClient->setHeaders($headers);
      if ($this->useInternalApi) {
         $request->setUri(self::INTERNAL_API . '/' . $api);
      } else {
         $request->setUri(self::PUB_API . '/' . $api);
      }
      $response = $httpClient->send($request);
      if(200 != $response->getStatusCode()){
         $errorType = ErrorType::getInstance();
         Kernel\throw_exception(new Exception(
            $errorType->msg('E_API_REQUEST_ERROR', $response->getBody()),
            $errorType->code('E_API_REQUEST_ERROR')
         ));
      }
      return $response;
   }

   /**
    * @return HttpClient
    */
   protected function getHttpClient()
   {
      if (null == $this->client) {
         $this->client = new HttpClient();
         $headers = new HttpHeaders();
         $headers->addHeaders(array(
            'x-ots-date' => gmstrftime('%a, %d %b %Y %H:%M:%S GMT', time()),
            'x-ots-apiversion' => '2014-08-08',
            'x-ots-accesskeyid' => $this->accessKey,
            'x-ots-instancename' => $this->instanceName,
         ));
         $this->client->setHeaders($headers);
         $this->client->setMethod('POST');
      }
      return $this->client;
   }

   protected function setupDefaultAccessCfg()
   {
      $cfg = ConfigProxy::getFrameworkConfig('Cloud');
      if (null == $this->instanceName) {
         $this->instanceName = $cfg->ali->ots->instanceName;
      }
      if (null == $this->accessKey) {
         $this->accessKey = $cfg->ali->ots->accessKey;
      }
      if (null == $this->accessKeySecret) {
         $this->accessKeySecret = $cfg->ali->ots->accessKeySecret;
      }
   }
}
