<?php
/**
 * Cntysoft Cloud Software Team
 * 
 * @author SOFTBOY <cntysoft@163.com>
 * @copyright  Copyright (c) 2010-2011 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license    http://www.cntysoft.com/license/new-bsd     New BSD License
 */
namespace Cntysoft\Framework\Cloud\Ali\OpenSearch;
use Cntysoft\Kernel;
/**
 * opensearch 下拉提示搜索接口。
 *
 * 用户需要在控制台中配置好下拉提示，并且已经生效，才有可能通过此接口获取结果。
 *
 * example：
 * <code>
 * $suggest = new Suggest($client);
 * $suggest->setIndexName("indexName");
 * $suggest->setSuggestName("suggestName");
 * $suggest->setHits(10);
 * $suggest->setQuery($query);
 *
 * echo $suggest->search();
 * </code>
 *
 * 或
 *
 * <code>
 * $suggest = new Suggest($client);
 *
 * $opts = array(
 *     "index_name" => "index_name",
 *     "suggest_name" => "suggest_name",
 *     "hit" => 10,
 *     "query" => "query"
 * );
 *
 * echo $suggest->search($opts);
 * </code>
 *
 */
class Suggest
{
   const API_ENTRY = 'suggest';
   /**
    * @var \Cntysoft\Framework\Cloud\Ali\OpenSearch\ApiCaller $apiCaller
    */
   protected $apiCaller;

   /**
    * @var string $indexName 索引的名称
    */
   protected $indexName = null;

   /**
    * @var string $suggestName 下拉提示的名称
    */
   protected $suggestName = null;

   /**
    *
    * @var int $hits 返回信息的条数
    */
   protected $hits = 10;

   /**
    * @var string $query 查询关键字名称
    */
   protected $query = null;

   public function __construct($appCaller)
   {
      $this->apiCaller = $appCaller;
   }

   /**
    * 设定下拉提示对应的应用名称
    *
    * @param string $indexName 指定的应用名称
    */
   public function setIndexName($indexName)
   {
      $this->indexName = $indexName;
   }

   /**
    * 获取下拉提示对应的应用名称
    *
    * @return string 返回应用名称
    */
   public function getIndexName()
   {
      return $this->indexName;
   }

   /**
    * 设定下拉提示名称
    *
    * @param string $suggestName 指定的下拉提示名称。
    */
   public function setSuggestName($suggestName)
   {
      $this->suggestName = $suggestName;
   }

   /**
    * 获取下拉提示名称
    *
    * @return string 返回下拉提示名称。
    */
   public function getSuggestName()
   {
      return $this->suggestName;
   }

   /**
    * 设定返回结果条数
    *
    * @param int $hits 返回结果的条数。
    */
   public function setHits($hits)
   {
      $hits = (int) $hits;
      if ($hits < 0) {
         $hits = 0;
      }
      $this->hits = $hits;
   }

   /**
    * 获取返回结果条数
    *
    * @return int 返回条数。
    */
   public function getHits()
   {
      return $this->hits;
   }

   /**
    * 设定要查询的关键词
    *
    * @param string $query 要查询的关键词。
    */
   public function setQuery($query)
   {
      $this->query = $query;
   }

   /**
    * 获取要查询的关键词
    *
    * @return string 返回要查询的关键词。
    */
   public function getQuery()
   {
      return $this->query;
   }

   /**
    * 发出查询请求
    *
    * @param array $opts options参数列表
    * @subparam             index_name 应用名称
    * @subparam             suggest_name 下拉提示名称
    * @subparam             hits 返回结果条数
    * @subparam  		   query 查询关键词
    * @return string 返回api返回的结果。
    */
   public function search($opts = array())
   {
      if (!empty($opts)) {
         if (isset($opts['indexName']) && $opts['indexName'] !== '') {
            $this->setIndexName($opts['indexName']);
         }

         if (isset($opts['suggestName']) && $opts['suggestName'] !== '') {
            $this->setSuggestName($opts['suggestName']);
         }

         if (isset($opts['hits']) && $opts['hits'] !== '') {
            $this->setHits($opts['hits']);
         }

         if (isset($opts['query']) && $opts['query'] !== '') {
            $this->setQuery($opts['query']);
         }
      }

      $params = array(
         "index_name" => $this->getIndexName(),
         "suggest_name" => $this->getSuggestName(),
         "hit" => $this->getHits(),
         "query" => $this->getQuery()
      );
      return $this->apiCaller->call(self::API_ENTRY, $params);
   }

}