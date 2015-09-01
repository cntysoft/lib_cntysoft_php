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
   const API_LIST_TABLE = 'ListTable';
   const API_CREATE_TABLE = 'CreateTable';
   const API_DELETE_TABLE = 'DeleteTable';
   const API_UPDATE_TABLE = 'UpdateTable';
   const API_DESCRIBE_TABLE = 'DescribeTable';
   const API_GET_ROW = 'GetRow';
   const API_PUT_ROW = 'PutRow';
   const API_UPDATE_ROW = 'UpdateRow';
   const API_DELETE_ROW = 'DeleteRow';
   const API_GET_RANGE = 'GetRange';
   const API_BATCH_GET_ROW = 'BatchGetRow';
   const API_BATCH_WRITE_ROW = 'BatchWriteRow';
   protected $entry;
   protected $accessKey;
   protected $accessKeySecret;
   protected $instanceName;

   /**
    * @var \Zend\Http\Client $client
    */
   protected $client;
   private static $clsLoaded = false;

   public function __construct($entry, $instanceName, $accessKey, $accessKeySecret)
   {
      $this->instanceName = $instanceName;
      $this->accessKey = $accessKey;
      $this->accessKeySecret = $accessKeySecret;
      $this->entry = $entry;
      if (null == $instanceName || null == $accessKey || null == $accessKeySecret) {
         $this->setupDefaultAccessCfg();
      }
      self::loadMsgCls();
   }

   /**
    * 获取当前实例所有的数据表
    *
    * @return array
    */
   public function getTableNames()
   {
      $response = $this->requestOtsApi(self::API_LIST_TABLE,
         new Msg\ListTableRequest());
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
      foreach ($primaryKeys as $primaryKey) {
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
      $response = $this->requestOtsApi(self::API_CREATE_TABLE,
         $createTableRequest);
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
    * @param string $tableName
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
    * 根据给定的主键读取单行数据。
    *
    * @param string $tableName
    * @param array $primaryKeys
    * @param array $columns
    * @return Msg\GetRowResponse
    */
   public function getRow($tableName, array $primaryKeys, array $columns = array())
   {
      $requestMsg = new Msg\GetRowRequest();
      $requestMsg->setTableName($tableName);
      foreach ($primaryKeys as $key) {
         $requestMsg->appendPrimaryKey($key);
      }
      foreach ($columns as $col) {
         $requestMsg->appendColumnsToGet($col);
      }
      $response = $this->requestOtsApi(self::API_GET_ROW, $requestMsg);
      $buf = new Msg\GetRowResponse();
      $buf->parseFromString($response->getBody());
      return $buf;
   }

   /**
    * @param string $tableName
    * @param Msg\Condition $condition
    * @param array $primaryKeys
    * @param array $attributeColumns
    * @return  Msg\PutRowResponse
    */
   public function putRow($tableName, Msg\Condition $condition, array $primaryKeys, array $attributeColumns)
   {
      $requestMsg = new Msg\PutRowRequest();
      $requestMsg->setTableName($tableName);
      $requestMsg->setCondition($condition);
      foreach ($primaryKeys as $key) {
         $requestMsg->appendPrimaryKey($key);
      }
      foreach ($attributeColumns as $col) {
         $requestMsg->appendAttributeColumns($col);
      }
      $response = $this->requestOtsApi(self::API_PUT_ROW, $requestMsg);
      $buf = new Msg\PutRowResponse();
      $buf->parseFromString($response->getBody());
      return $buf;
   }

   /**
    * @param string  $tableName
    * @param Msg\Condition $condition
    * @param array $primaryKeys
    * @param array $attributeColumns
    * @return Msg\UpdateRowResponse
    */
   public function updateRow($tableName, Msg\Condition $condition, array $primaryKeys, array $attributeColumns)
   {
      $requestMsg = new Msg\UpdateRowRequest();
      $requestMsg->setTableName($tableName);
      $requestMsg->setCondition($condition);
      foreach ($primaryKeys as $key) {
         $requestMsg->appendPrimaryKey($key);
      }
      foreach ($attributeColumns as $col) {
         $requestMsg->appendAttributeColumns($col);
      }
      $response = $this->requestOtsApi(self::API_UPDATE_ROW, $requestMsg);
      $buf = new Msg\UpdateRowResponse();
      $buf->parseFromString($response->getBody());
      return $buf;
   }

   /**
    * 删除一行数据。
    *
    * @param string $tableName
    * @param Msg\Condition $condition
    * @param array $primaryKeys
    * @return Msg\DeleteRowResponse
    */
   public function deleteRow($tableName, Msg\Condition $condition, array $primaryKeys)
   {
      $requestMsg = new Msg\DeleteRowRequest();
      $requestMsg->setTableName($tableName);
      $requestMsg->setCondition($condition);
      foreach ($primaryKeys as $key) {
         $requestMsg->appendPrimaryKey($key);
      }
      $response = $this->requestOtsApi(self::API_DELETE_ROW, $requestMsg);
      $buf = new Msg\DeleteRowResponse();
      $buf->parseFromString($response->getBody());
      return $buf;
   }

   /**
    * 读取指定主键范围内的数据。
    *
    * @param string $tableName
    * @param int $direction
    * @param array $columnsToGet
    * @param array $inclusiveStartPrimaryKey
    * @param array $exclusiveEndPrimaryKey
    * @param int $limit
    * @return Msg\GetRangeResponse
    */
   public function getRange($tableName, $direction, array $columnsToGet, array $inclusiveStartPrimaryKey, array $exclusiveEndPrimaryKey, $limit = 1)
   {
      $requestMsg = new Msg\GetRangeRequest();
      $requestMsg->setTableName($tableName);
      $requestMsg->setDirection($direction);
      foreach ($columnsToGet as $col) {
         $requestMsg->appendColumnsToGet($col);
      }
      foreach ($inclusiveStartPrimaryKey as $key) {
         $requestMsg->appendColumnsToGet($key);
      }
      foreach ($exclusiveEndPrimaryKey as $key) {
         $requestMsg->appendColumnsToGet($key);
      }
      $requestMsg->setLimit($limit);
      $response = $this->requestOtsApi(self::API_GET_RANGE, $requestMsg);
      $buf = new Msg\GetRangeResponse();
      $buf->parseFromString($response->getBody());
      return $buf;
   }

   /**
    * @param array $tableRequests
    * @return Msg\BatchGetRowResponse
    */
   public function batchGetRow(array $tableRequests)
   {
      $requestMsg = new Msg\BatchGetRowRequest();
      foreach ($tableRequests as $request) {
         $requestMsg->appendTables($request);
      }
      $response = $this->requestOtsApi(self::API_BATCH_GET_ROW, $requestMsg);
      $buf = new Msg\BatchGetRowResponse();
      $buf->parseFromString($response->getBody());
      return $buf;
   }

   /**
    * @param array $tableRequests
    * @return Msg\BatchWriteRowResponse
    */
   public function batchWriteRow(array $tableRequests)
   {
      $requestMsg = new Msg\BatchWriteRowRequest();
      foreach ($tableRequests as $request) {
         $requestMsg->appendTables($request);
      }
      $response = $this->requestOtsApi(self::API_BATCH_WRITE_ROW, $requestMsg);
      $buf = new Msg\BatchWriteRowResponse();
      $buf->parseFromString($response->getBody());
      return $buf;
   }

   /**
    * 查询指定表的结构信息和预留读写吞吐量设置信息。
    *
    * @param $tableName
    * @return Msg\DescribeTableResponse
    */
   public function describeTable($tableName)
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
      $headers->addHeaderLine('x-ots-contentmd5',
         base64_encode(md5($body, true)));
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
      $signature = base64_encode(hash_hmac('sha1', $strToSignature,
            $this->accessKeySecret, true));
      $headers->addHeaderLine('x-ots-signature', $signature);
      $httpClient->setHeaders($headers);
      $request->setUri($this->entry . '/' . $api);

      $response = $httpClient->send($request);
      if (200 != $response->getStatusCode()) {
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
      if (!self::$clsLoaded) {
         include __DIR__ . DS . 'Msg' . DS . 'pb_proto_ots.php';
         self::$clsLoaded = true;
      }
   }

}