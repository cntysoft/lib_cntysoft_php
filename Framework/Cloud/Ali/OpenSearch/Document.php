<?php
/**
 * Cntysoft Cloud Software Team
 */
namespace Cntysoft\Framework\Cloud\Ali\OpenSearch;
use Cntysoft\Kernel;
/**
 * @author SOFTBOY <cntysoft@163.com>
 * @copyright  Copyright (c) 2010-2011 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license    http://www.cntysoft.com/license/new-bsd     New BSD License
 */
class Document
{
   const API_ENTRY = 'index/doc';
   const DOC_ADD = 'add';
   const DOC_REMOVE = 'delete';
   const DOC_UPDATE = 'update';
   /**
    * push数据时API返回的正确的状态值。
    * @var string
    */
   const PUSH_RETURN_STATUS_OK = 'OK';

   /**
    * push数据时验证签名的方式。
    *
    * 如果此常量为1，且生成签名的query string中包含了items字段，则计算签名的时候items字段
    * 将不被包含在内。否则，所有的字段将都要被计算签名。
    *
    * @var int
    */
   const SIGN_MODE = 1;
   /**
    * @var \Cntysoft\Framework\Cloud\Ali\OpenSearch\ApiCaller $apiCaller
    */
   protected $apiCaller;
   protected $appName;
   protected $apiEntry;

   public function __construct($appName, $appCaller)
   {
      $this->apiCaller = $appCaller;
      $this->appName = $appName;
      $this->apiEntry = self::API_ENTRY.'/'.$this->appName;
   }

   public function add($tableName, array $docs)
   {
      $docs = $this->generate($docs, self::DOC_ADD);
      return $this->upload($tableName, $docs);
   }

   public function update($tableName, array $docs)
   {
      $docs = $this->generate($docs, self::DOC_UPDATE);
      return $this->upload($tableName, $docs);
   }

   public function delete($tableName, array $docs)
   {
      $docs = $this->generate($docs, self::DOC_REMOVE);
      return $this->upload($tableName, $docs);
   }

   protected function upload($tableName, array $docs, $signMode = self::SIGN_MODE)
   {
      if (empty($docs) || !is_array($docs[0])) {
         $errorType = ErrorType::getInstance();
         Kernel\throw_exception(new Exception(
            $errorType->msg('DOC_FORMAT_ERROR'),
            $errorType->code('DOC_FORMAT_ERROR')
         ));
      }
      $params = array(
         'action' => "push",
         'items' => json_encode($docs),
         'table_name' => $tableName
      );

      if ($signMode == self::SIGN_MODE) {
         $params['sign_mode'] = self::SIGN_MODE;
      }
      return $this->apiCaller->call($this->apiEntry, $params, ApiCaller::M_POST);
   }
   /**
    * 重新生成doc文档。
    * @param array $docs doc文档
    * @param string $type 操作类型，有ADD、UPDATE、REMOVE。
    * @return array 返回重新生成的doc文档。
    */
   protected function generate($docs, $type)
   {
      $result = array();
      foreach ($docs as $doc) {
         $item = array('cmd' => $type);
         $item['fields'] = $doc;
         $item['timestamp'] = time();
         $result[] = $item;
      }
      return $result;
   }

}