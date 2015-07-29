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
class Search
{
   /**
    * 设定搜索结果集升降排序的标志，"+"为升序，"-"为降序。
    *
    * @var string
    */
   const SORT_INCREASE = '+';
   const SORT_DECREASE = '-';
   const SEARCH_TYPE_SCAN = "scan";
   const QUERY_TYPE_SCROLL = 'scroll';
   const QUERY_TYPE_SEARCH = 'search';

   const API_ENTRY = 'search';
   const D_FORMAT_XML = 'xml';
   const D_FORMAT_JSON = 'json';
   //查询子句类型
   const QUERY_CONF_CONFIG = 'config';
   const QUERY_CONF_QUERY = 'query';
   const QUERY_CONF_FILTER = 'filter';
   const QUERY_CONF_SORT = 'sort';
   const QUERY_CONF_AGGREGATE = 'aggregate';
   const QUERY_CONF_DISTINCT = 'distinct';
   const QUERY_CONF_KVPAIRS = 'kvpairs';

   const D_START = 0;
   const D_HITS = 20;
   /**
    * @var \Cntysoft\Framework\Cloud\Ali\OpenSearch\ApiCaller $apiCaller
    */
   protected $apiCaller;
   /**
    * @var array
    */
   protected $queryOpts;
   /**
    * @var array $indexs
    */
   protected $indexs = array();
   /**
    * @var array $fetchFields
    */
   protected $fetchFields = array();
   /**
    * @var array $QPName
    */
   protected $QPName = array();
   /**
    * 指定表达式名称，表达式名称和结构在网站中指定。
    *
    * @var string $formulaName
    */
   protected $formulaName = '';

   /**
    * 指定粗排表达式名称，表达式名称和结构在网站中指定。、
    *
    * @var string $firstFormulaName
    */
   protected $firstFormulaName = '';
   /**
    * 指定关闭的方法名称。
    *
    * @var array $functions
    */
   protected $functions = array();

   /**
    * 指定某些字段的一些summary展示规则。
    *
    * 这些字段必需为可分词的text类型的字段。
    *
    * 例如:
    * 指定title字段为： summary_field=>title
    * 指定title长度为50：summary_len=>50
    * 指定title飘红标签：summary_element=>em
    * 指定title省略符号：summary_ellipsis=>...
    * 指定summary缩略段落个数：summary_snipped=>1
    * 那么当前的字段值为：
    * <code>
    * array('title' => array(
    *   'summary_field' => 'title',
    *   'summary_len' => 50,
    *   'summary_element' => 'em',
    *   'summary_ellipsis' => '...',
    *   'summary_snipped' => 1,
    *   'summary_element_prefix' => 'em',
    *   'summary_element_postfix' => '/em')
    * );
    * </code>
    * @var array
    */
   protected $summary = array();
   protected $scrollId = null;
   protected $scroll = null;

   public function __construct($appCaller)
   {
      $this->apiCaller = $appCaller;
   }

   /**
    * 执行搜索
    *
    * 执行向API提出搜索请求。
    * 更多说明请参见 [API 配置config子句]({{!api-reference/query-clause&config-clause!}})
    * @param array $opts 此参数如果被复制，则会把此参数的内容分别赋给相应的变量。此参数的值可能有以下内容：
    * @subparam array query 查询子句所有配置信息
    *
    * @return string 返回搜索结果。
    *
    */
   public function search(array $opts = array())
   {
      $this->extraOpts($opts);
      return $this->call(self::QUERY_TYPE_SEARCH);
   }

   /**
    * 请求scroll api。
    *
    * 类似search接口，但是不支持sort, aggregate, distinct, formula_name, summary及qp,
    * start 等功能。
    *
    * scroll实现方式：
    * 第一次正常带有指定的子句和参数调用scroll接口，此接口会返回scroll_id信息。
    * 第二次请求时只带此scroll_id信息和scroll参数即可。
    *
    * 类似第一次请求：
    * $search = new Search($client);
    * $search->setSearchIndexs("juhuasuan");
    * $search->setQueryString("default:'酒店'");
    * $search->setScroll("1m");
    * $result = $search->scroll();
    *
    * $array = json_decode($result, true);
    * $scrollId = $array['result']['scroll_id'];
    *
    * 第二次请求：
    * $search = new Search($client);
    * $search->setScroll("1m");
    * $search->setScrollId($scrollId);
    * $result = $search->scroll();
    *
    * @param array $opts 扫描请求所需参数
    * @return string 扫描结果
    */
   public function scroll($opts = array())
   {
      $this->extraOpts($opts);
      return $this->call(self::QUERY_TYPE_SCROLL);
   }

   /**
    * 设置的时候不区分，等到读取的时候进行切分
    *
    * @param array $opts
    */
   protected function extraOpts(array $opts)
   {
      if (!empty($opts) && is_array($opts)) {
         isset($opts['query']) && $this->setQueryParams($opts['query']);
      }
   }

   /**
    *  生成HTTP的请求串，并通过ApiCaller类向API服务发出请求并返回结果。
    *
    * query参数中的query子句和config子句必需的，其它子句可选。
    *
    * @param string $searchType
    * @return array
    */
   protected function call($searchType = self::QUERY_TYPE_SEARCH)
   {
      $params = array(
         'query' => $this->getQueryParamsString(),
         'index_name' => implode(";", $this->indexs)
      );
      if(!empty($this->fetchFields)){
         $params['fetch_fields'] = implode(';', $this->fetchFields);
      }
      if ($searchType == self::QUERY_TYPE_SEARCH) {
         ($f = $this->getFormulaName()) && ($params['formula_name'] = $f);
         ($f = $this->getFirstFormulaName()) && ($params['first_formula_name'] = $f);
         ($s = $this->getSummaryString()) && ($params['summary'] = $s);
         !empty($this->QPName) && ($params['qp'] = implode(',', $this->QPName));
         ($f = $this->getDisabledFunctionString()) && ($params['disable'] = $f);
      } else if ($searchType == self::QUERY_TYPE_SCROLL) {
         ($f = $this->getScroll()) && ($params['scroll'] = $f);
         ($f = $this->getScrollId()) && ($params['scroll_id'] = $f);
         $params['search_type'] = self::SEARCH_TYPE_SCAN;
      }
      return $this->apiCaller->call(self::API_ENTRY, $params, ApiCaller::M_GET);
   }

   public function setSearchIndexs(array $indexs)
   {
      $this->indexs = $indexs;
   }

   public function setFetchFields(array $fields)
   {
      $this->fetchFields = $fields;
   }

   public function setQpNames(array $names)
   {
      $this->QPName = $names;
   }

   /**
    * 设置表达式名称
    * 此表达式名称和结构需要在网站中已经设定。
    * @param string $formulaName 表达式名称。
    */
   public function setFormulaName($formulaName)
   {
      $this->formulaName = $formulaName;
   }

   /**
    * 获取表达式名称
    *
    * 获得当前请求中设置的表达式名称。
    *
    * @return string 返回当前设定的表达式名称。
    */
   public function getFormulaName()
   {
      return $this->formulaName;
   }

   /**
    * 清空精排表达式名称设置
    */
   public function clearFormulaName()
   {
      $this->formulaName = '';
   }

   /**
    * 设置粗排表达式名称
    *
    * 此表达式名称和结构需要在网站中已经设定。
    *
    * @param string $FormulaName 表达式名称。
    */
   public function setFirstFormulaName($formulaName)
   {
      $this->firstFormulaName = $formulaName;
   }

   /**
    * 获取粗排表达式设置
    *
    * 获取当前设置的粗排表达式名称。
    *
    * @return string 返回当前设定的表达式名称。
    */
   public function getFirstFormulaName()
   {
      return $this->firstFormulaName;
   }

   /**
    * 清空粗排表达式名称设置
    */
   public function clearFirstFormulaName()
   {
      $this->firstFormulaName = '';
   }

   /**
    * 添加一条summary信息
    * @param string $fieldName 指定的生效的字段。此字段必需为可分词的text类型的字段。
    * @param string $len 指定结果集返回的词字段的字节长度，一个汉字为2个字节。
    * @param string $element 指定命中的query的标红标签，可以为em等。
    * @param string $ellipsis 指定用什么符号来标注未展示完的数据，例如“...”。
    * @param string $snipped 指定query命中几段summary内容。
    * @param string $elementPrefix 如果指定了此参数，则标红的开始标签以此为准。
    * @param string $elementPostfix 如果指定了此参数，则标红的结束标签以此为准。
    */
   public function addSummary($fieldName, $len = 0, $element = '',
                              $ellipsis = '', $snipped = 0, $elementPrefix = '', $elementPostfix = '')
   {
      if (empty($fieldName)) {
         return false;
      }

      $summary = array();
      $summary['summary_field'] = $fieldName;
      empty($len) || $summary['summary_len'] = (int) $len;
      empty($element) || $summary['summary_element'] = $element;
      empty($ellipsis) || $summary['summary_ellipsis'] = $ellipsis;
      empty($snipped) || $summary['summary_snipped'] = $snipped;
      empty($elementPrefix) || $summary['summary_element_prefix'] = $elementPrefix;
      empty($elementPostfix) || $summary['summary_element_postfix'] = $elementPostfix;

      $this->summary[$fieldName] = $summary;
   }

   /**
    * 获取当前的summary信息
    * 可以通过指定字段名称返回指定字段的summary信息
    *
    * @param string $field 指定的字段，如果此字段为空，则返回整个summary信息，否则返回指定field的summary信息。
    * @return array 返回summary信息。
    */
   public function getSummary($field = '')
   {
      return (!empty($field)) ? $this->summary[$field] : $this->summary;
   }

   public function clearSummary()
   {
      $this->summary = array();
   }

   /**
    * 获取summary字符串
    *
    * 把summary信息生成字符串并返回。
    *
    * @return string 返回字符串的summary信息。
    */
   public function getSummaryString()
   {
      $summary = array();
      if (is_array($s = $this->getSummary()) && !empty($s)) {
         foreach ($this->getSummary() as $summaryAttributes) {
            $item = array();
            if (is_array($summaryAttributes) && !empty($summaryAttributes)) {
               foreach ($summaryAttributes as $k => $v) {
                  $item[] = $k . ":" . $v;
               }
            }
            $summary[] = implode(",", $item);
         }
      }
      return implode(";", $summary);
   }

   /**
    * 设置此次获取的scroll id的期时间。
    *
    * 可以为整形数字，默认为毫秒。也可以用1m表示1min；支持的时间单位包括：
    * w=Week, d=Day, h=Hour, m=minute, s=second
    *
    * @param string|int $scroll
    */
   public function setScroll($scroll)
   {
      $this->scroll = $scroll;
   }

   /**
    * 获取scroll的失效时间。
    *
    * @return string|int
    */
   public function getScroll()
   {
      return $this->scroll;
   }

   /**
    * 设置scroll扫描起始id
    *
    * @param scrollId 扫描起始id
    */
   public function setScrollId($scrollId)
   {
      $this->scrollId = $scrollId;
   }

   /**
    * 获取scroll扫描起始id
    *
    * @return string 扫描起始id
    */
   public function getScrollId()
   {
      return $this->scrollId;
   }

   /**
    * 设置总体的查询条件,这个接口支持所有的查询子句
    *
    * @param array $params
    */
   public function setQueryParams(array $params)
   {
      isset($params[self::QUERY_CONF_CONFIG]) && $this->setQueryConfigParams($params[self::QUERY_CONF_CONFIG]);
      isset($params[self::QUERY_CONF_QUERY]) && $this->setQueryQueryString($params[self::QUERY_CONF_QUERY]);
      isset($params[self::QUERY_CONF_FILTER]) && $this->setQueryFilterString($params[self::QUERY_CONF_FILTER]);
      isset($params[self::QUERY_CONF_KVPAIRS]) && $this->setQueryKvPairParams($params[self::QUERY_CONF_KVPAIRS]);
      isset($params[self::QUERY_CONF_SORT]) && $this->setQuerySortParams($params[self::QUERY_CONF_SORT]);
      isset($params[self::QUERY_CONF_DISTINCT]) && $this->setQueryDistinctParams($params[self::QUERY_CONF_DISTINCT]);
      isset($params[self::QUERY_CONF_AGGREGATE]) && $this->setQueryAggregateParams($params[self::QUERY_CONF_AGGREGATE]);
   }

   /**
    * 关闭某些功能模块。
    *
    * 有如下场景需要考虑：
    * 1、如果要关闭整个qp的功能，则参数为空即可。
    * 2、要指定某个索引关闭某个功能，则可以指定disableValue="processer:index",
    * processer:index为指定关闭某个processer的某个索引功能，其中index为索引名称，多个索引可以用“|”分隔，可以为index1[|index2...]
    * 3、如果要关闭多个processor可以传递数组。
    * qp processor 有如下模块：
    * 1、spell_check: 检查用户查询串中的拼写错误，并给出纠错建议。
    * 2、term_weighting: 分析查询中每个词的重要程度，并将其量化成权重，权重较低的词可能不会参与召回。
    * 3、stop_word: 根据系统内置的停用词典过滤查询中无意义的词
    * 4、synonym: 根据系统提供的通用同义词库和语义模型，对查询串进行同义词扩展，以便扩大召回。
    * example:
    * "" 表示关闭整个qp。
    * "spell_check" 表示关闭qp的拼音纠错功能。
    * "stop_word:index1|index2" 表示关闭qp中索引名为index1和index2上的停用词功能。
    *
    * @param string $functionName 指定的functionName，例如“qp”等
    * @param string|array $disableValue 需要关闭的值
    */
   public function disabledQP($disableValue = "")
   {
      $this->addDisabledFunction("qp", $disableValue);
   }


   /**
    * 添加一项禁止的功能模块
    *
    * @param functionName 功能模块名称
    * @param disableValue 禁用的功能细节
    */
   public function addDisabledFunction($functionName, $disableValue = "")
   {
      if (is_array($disableValue)) {
         $this->functions[$functionName] = $disableValue;
      } else {
         $this->functions[$functionName][] = $disableValue;
      }
   }

   /**
    * 获取所有禁止的功能模块
    *
    * @return array 所哟禁止的功能模块
    */
   public function getDisabledFunction()
   {
      return $this->functions;
   }

   public function clearDisabledFunctions()
   {
      $this->functions = array();
   }
   /**
    * 以字符串的格式返回disable的内容。
    *
    * @return string
    */
   public function getDisabledFunctionString()
   {
      $functions = $this->getDisabledFunction();
      $result = array();
      if (!empty($functions)) {
         foreach ($functions as $functionName => $value) {
            $string = "";
            if (is_array($value) && !empty($value)) {
               $string = implode(",", $value);
            }

            if ($string === "") {
               $result[] = $functionName;
            } else {
               $result[] = $functionName . ":" . $string;
            }
         }
      }
      return implode(";", $result);
   }

   /**
    * 获取所有子句的查询条件字符串
    *
    * @return string
    */
   protected function getQueryParamsString($queryType = self::QUERY_TYPE_SEARCH)
   {
      $ret = [];
      $cfgString = $this->getQueryConfigParamsString();
      $queryString = $this->getQueryQueryString();
      $filterString = $this->getQueryFilterString();
      $kvpairString = $this->getQueryKvPairString();
      $cfgString && $ret[] = 'config='.$cfgString;
      $queryString && $ret[] = 'query='.$queryString;
      $filterString && $ret[] = 'filter='.$filterString;
      $kvpairString && $ret[] = 'kvpairs='.$kvpairString;
      if ($queryType == self::QUERY_TYPE_SEARCH) {
         $sortString = $this->getQuerySortString();
         $distinctString = $this->getQueryDistinctString();
         $aggregateString = $this->getQueryAggregateString();
         $sortString && $ret[] = 'sort='.$sortString;
         $distinctString && $ret[] = 'distinct='.$distinctString;
         $aggregateString && $ret[] = 'aggregate='.$aggregateString;
      }
      return implode('&&', $ret);
   }

   /**
    * 设置配置子句相关查询条件
    *
    * @param array $params
    */
   public function setQueryConfigParams(array $params)
   {
      if(isset($this->query[self::QUERY_CONF_CONFIG])){
         $this->query[self::QUERY_CONF_CONFIG] = array();
      }
      $targetConf = &$this->query[self::QUERY_CONF_CONFIG];
      isset($params['start']) && ($targetConf['start'] = (int)$params['start']);
      isset($params['hit']) && ($targetConf['hit'] = (int)$params['hit']);
      isset($params['format']) && ($targetConf['format'] = $params['format']);
      isset($params['rerank_size']) && ($targetConf['rerank_size'] = (int)$params['rerank_size']);
   }

   /**
    * @return array
    */
   public function getQueryConfigParams()
   {
      return isset($this->query[self::QUERY_CONF_CONFIG]) ? $this->query[self::QUERY_CONF_CONFIG] : array();
   }

   /**
    * 返回Config子句的字符串表示
    *
    * @return string
    */
   protected function getQueryConfigParamsString()
   {
      $params = $this->getQueryConfigParams();
      $params += array(
         'hit' => self::D_HITS,
         'start' => self::D_START,
         'format' => self::D_FORMAT_JSON
      );
      $ret = array();
      foreach($params as $key => $value){
         $ret[] = $key.':'.$value;
      }
      return implode(',', $ret);
   }

   /**
    * 设置查询子句的相关配置信息
    *
    * @param string $query
    */
   public function setQueryQueryString($query)
   {
      $this->query[self::QUERY_CONF_QUERY] = $query;
   }

   public function getQueryQueryString()
   {
      return isset($this->query[self::QUERY_CONF_QUERY]) ? $this->query[self::QUERY_CONF_QUERY] : '';
   }

   /**
    * 设置过滤子句相关配置信息
    *
    * @param string $filter
    */
   public function setQueryFilterString($filter)
   {
      $this->query[self::QUERY_CONF_FILTER] = $filter;
   }

   public function getQueryFilterString()
   {
      return isset($this->query[self::QUERY_CONF_FILTER]) ? $this->query[self::QUERY_CONF_FILTER] : '';
   }

   /**
    * 设置排序子句相关配置信息
    *
    * @param array $params
    */
   public function setQuerySortParams(array $params)
   {
      $this->queryOpts[self::QUERY_CONF_SORT] = $params;
   }

   public function getQuerySortParams()
   {
      return isset($this->query[self::QUERY_CONF_SORT]) ? $this->query[self::QUERY_CONF_SORT] : array();
   }

   public function getQuerySortString()
   {
      if(isset($this->queryOpts[self::QUERY_CONF_SORT]) && !empty($this->queryOpts[self::QUERY_CONF_SORT])){
         return implode(';', $this->queryOpts[self::QUERY_CONF_SORT]);
      }else{
         return '';
      }
   }

   /**
    * 设置统计子句相关配置信息
    * group_key：必选参数。field为要进行统计的字段名，必须勾选可搜索，目前支持int类、float类及string类型的字段做统计。
    * agg_fun：必选参数。func可以为count()、sum(id)、max(id)、min(id)四种系统函数，含义分别为：文档个数、对id字段求和、取id字段最大值、取id字段最小值；支持同时进行多个函数的统计，中间用英文井号（#）分隔；sum、max、min的内容支持基本的算术运算；
    * range：表示分段统计，可用于分布统计，支持多个range参数。表示number1~number2及大于number2的区间情况。不支持string类型的字段分布统计。
    * agg_filter：非必须参数，表示仅统计满足特定条件的文档；
    * agg_sampler_threshold：非必须参数，抽样统计的阈值。表示该值之前的文档会依次统计，该值之后的文档会进行抽样统计；
    * agg_sampler_step：非必须参数，抽样统计的步长。表示从agg_sampler_threshold后的文档将间隔agg_sampler_step个文档统计一次。对于sum和count类型的统计会把阈值后的抽样统计结果最后乘以步长进行估算，估算的结果再加上阈值前的统计结果就是最后的统计结果。
    * max_group：最大返回组数，默认为1000。
    *
    * @param array $params
    */
   public function setQueryAggregateParams(array $params)
   {
      if(isset($this->query[self::QUERY_CONF_AGGREGATE])){
         $this->query[self::QUERY_CONF_AGGREGATE] = array();
      }
      $targetConf = &$this->query[self::QUERY_CONF_AGGREGATE];
      if(!isset($params[0])){
         $params = array(
            $params
         );
      }
      foreach($params as $param){
         $item = array();
         $item['group_key'] = $param['group_key'];
         $item['agg_fun'] = $param['agg_fun'];
         isset($param['range']) && ($item['range'] = $param['range']);
         isset($param['agg_filter']) && ($item['agg_filter'] = $param['agg_filter']);
         isset($param['agg_sampler_threshold']) && ($item['agg_sampler_threshold'] = $param['agg_sampler_threshold']);
         isset($param['agg_sampler_step']) && ($item['agg_sampler_step'] = (int)$param['agg_sampler_step']);
         isset($param['max_group']) && ($item['max_group'] = (int)$param['max_group']);
         $targetConf[] = $item;
      }
   }

   public function getQueryAggregateParams()
   {
      return isset($this->query[self::QUERY_CONF_AGGREGATE]) ? $this->query[self::QUERY_CONF_AGGREGATE] : array();
   }

   public function getQueryAggregateString()
   {
      $params = $this->getQueryAggregateParams();
      $ret = [];
      foreach($params as $param){
         $item = [];
         foreach($param as $key => $value){
            $item[] = $key.':'.$value;
         }
         $ret[] = implode(',', $item);
      }
      return implode(';', $ret);
   }

   /**
    * 设置聚合子句相关配置信息
    *
    * @param array $params
    */
   public function setQueryDistinctParams(array $params)
   {
      if(isset($this->query[self::QUERY_CONF_DISTINCT])){
         $this->query[self::QUERY_CONF_DISTINCT] = array();
      }
      $targetConf = &$this->query[self::QUERY_CONF_DISTINCT];
      //必须的直接用
      $targetConf['dist_key'] = $params['dist_key'];
      isset($params['dist_times']) && ($targetConf['dist_times'] = (int)$params['dist_times']);
      isset($params['dist_count']) && ($targetConf['dist_count'] = (int)$params['dist_count']);
      isset($params['reserved']) && ($targetConf['reserved'] = (bool)$params['reserved']);
      isset($params['update_total_hit']) && ($targetConf['update_total_hit'] = (bool)$params['update_total_hit']);
      isset($params['dist_filter']) && ($targetConf['dist_filter'] = $params['dist_filter']);
      isset($params['grade']) && ($targetConf['grade'] = (bool)$params['grade']);
   }

   /**
    * @return array
    */
   public function getQueryDistinctParams()
   {
      return isset($this->query[self::QUERY_CONF_DISTINCT]) ? $this->query[self::QUERY_CONF_DISTINCT] : array();
   }

   public function getQueryDistinctString()
   {
      $params = $this->getQueryDistinctParams();
      $ret = array();
      foreach($params as $key => $value){
         $ret[] = $key.':'.$value;
      }
      return implode(',', $ret);
   }

   /**
    * 设置自定义参数kvpairs子句相关配置信息
    *
    * @param array $kvpairs
    */
   public function setQueryKvPairParams(array $kvpairs)
   {
      $this->queryOpts[self::QUERY_CONF_KVPAIRS] = $kvpairs;
   }

   public function getQueryKvPairParams()
   {
      return isset($this->queryOpts[self::QUERY_CONF_KVPAIRS])?$this->queryOpts[self::QUERY_CONF_KVPAIRS] : array();
   }

   public function getQueryKvPairString()
   {
      $params = $this->getQueryKvPairParams();
      $ret = [];
      foreach($params as $key => $value){
         $ret[] = $key.':'.$value;
      }
      return implode(',', $ret);
   }
}