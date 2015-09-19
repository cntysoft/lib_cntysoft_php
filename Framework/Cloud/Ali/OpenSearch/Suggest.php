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
}