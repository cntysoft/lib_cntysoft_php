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
use Cntysoft\Kernel;

/**
 * 封装阿里巴巴NO-SQL服务客户端
 */
class Client
{
   const INTERNAL_API = 'http://gzy-ots.cn-hangzhou.ots-internal.aliyuncs.com';
   const PUB_API = 'http://gzy-ots.cn-hangzhou.ots.aliyuncs.com';
   const MSG_CLS_FILENAME = __DIR__.DS.'Msg'.DS.'pb_proto_ots.php';

   const API_LIST_TABLE = 'ListTable';
   const API_CREATE_TABLE = 'CreateTable';
   const API_DELETE_TABLE = 'DeleteTable';
   const API_UPDATE_TABLE = 'UpdateTable';
   const API_DESCRIBE_TABLE = 'DescribeTable';

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
      $response = $this->requestOtsApi(self::API_LIST_TABLE, new Msg\ListTableRequest());
      $responseBuf = new Msg\ListTableResponse();
      $responseBuf->parseFromString($response->getBody());
      return $responseBuf->getTableNames();
   }

   /**
    * primaryKeys参数的结构
    * <code>
    *  array(
    *    'name' => 'name',
    *     'type' => 'type'
    * );
    * </code>
    * @param string $tableName
    * @param array $primaryKeys
    * @param int $readCapacityUnit
    * @param int $writeCapacityUnit
    * @return Msg\CreateTableResponse
    */
   public function createTable($tableName, array $primaryKeys, $readCapacityUnit = 2, $writeCapacityUnit = 2)
   {
      $tableMeta = new Msg\TableMeta();
      $tableMeta->setTableName($tableName);
      foreach($primaryKeys as $primaryKey){
         $keyMsg = new Msg\ColumnSchema();
         $keyMsg->setName($primaryKey['name']);
         $keyMsg->setType($primaryKey['type']);
         $tableMeta->appendPrimaryKey($keyMsg);
      }
      $capacityUnit = new Msg\CapacityUnit();
      $capacityUnit->setRead((int) $readCapacityUnit);
      $capacityUnit->setWrite((int) $writeCapacityUnit);
      $reservedThroughput = new Msg\ReservedThroughput();
      $reservedThroughput->setCapacityUnit($capacityUnit);
      $createTableRequest = new Msg\CreateTableRequest();
      $createTableRequest->setTableMeta($tableMeta);
      $createTableRequest->setReservedThroughput($reservedThroughput);
      $response = $this->requestOtsApi(self::API_CREATE_TABLE, $createTableRequest);
      $buf = new Msg\CreateTableResponse();
      $buf->parseFromString($response->getBody());
      return $buf;
   }

   /**
    * 更新指定表的读服务能力单元或写服务能力单元设置,新设定将于更新成功一分钟内生效。
    *
    * @param $tableName
    * @param $readCapacityUnit
    * @param $writeCapacityUnit
    * @return Msg\UpdateTableResponse
    */
   public function updateTable($tableName, $readCapacityUnit, $writeCapacityUnit)
   {
      $requestMsg = new Msg\UpdateTableRequest();
      $requestMsg->setTableName($tableName);
      $capacityUnit = new Msg\CapacityUnit();
      $capacityUnit->setRead((int) $readCapacityUnit);
      $capacityUnit->setWrite((int) $writeCapacityUnit);
      $reservedThroughput = new Msg\ReservedThroughput();
      $reservedThroughput->setCapacityUnit($capacityUnit);
      $requestMsg->setReservedThroughput($reservedThroughput);
      $response = $this->requestOtsApi(self::API_UPDATE_TABLE, $requestMsg);
      $buf = new Msg\UpdateTableResponse();
      $buf->parseFromString($response->getBody());
      return $buf;
   }

   /**
    * 删除本实例下指定的表。
    *
    * @param $tableName
    * @return Msg\DeleteTableResponse
    */
   public function deleteTable($tableName)
   {
      $requestMsg = new Msg\DescribeTableRequest();
      $requestMsg->setTableName($tableName);
      $response = $this->requestOtsApi(self::API_DELETE_TABLE, $requestMsg);
      $buf = new Msg\DeleteTableResponse();
      $buf->parseFromString($response->getBody());
      return $buf;
   }

   /**
    * 查询指定表的结构信息和预留读写吞吐量设置信息。
    *
    * @param $tableName
    * @return Msg\DescribeTableResponse
    */
   public function DescribeTable($tableName)
   {
      $requestMsg = new Msg\DescribeTableRequest();
      $requestMsg->setTableName($tableName);
      $response = $this->requestOtsApi(self::API_DESCRIBE_TABLE, $requestMsg);
      $buf = new Msg\DescribeTableResponse();
      $buf->parseFromString($response->getBody());
      return $buf;
   }

   /**
    * @param string $api
    * @param \ProtobufMessage $message
    * @return \Zend\Http\Response
    * @throws \Exception
    */
   protected function requestOtsApi($api, \ProtobufMessage $message)
   {
      $httpClient = $this->getHttpClient();
      //计算几项值
      $request = $httpClient->getRequest();
      $headers = $request->getHeaders();
      $body = $message->serializeToString();
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
         $this->client->setEncType('application/x-www-form-urlencoded');
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

   public static function loadMsgCls()
   {
      include self::MSG_CLS_FILENAME;
   }
}
