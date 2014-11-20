<?php
/**
 * Cntysoft Cloud Software Team
 *
 * @author SOFTBOY <cntysoft@163.com>
 * @copyright  Copyright (c) 2010-2011 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license    http://www.cntysoft.com/license/new-bsd     New BSD License
 */
namespace Cntysoft\Framework\ApiServer;
use Cntysoft\Kernel;
use Cntysoft\Kernel\StdErrorType;
/**
 * 抽象运行脚本， 每个脚本都能成为次一层的派发器
 */
abstract class AbstractScript
{

   /**
    * 基本的名称空间
    */
   const BASE_NS = Server::DEFAULT_SCRIPT_NS;
   /**
    * 派发参数中子响应类识别KEY， 系统通过这个数组键获取子类名称
    */
   const DISPATCH_KEY = 'key';
   /**
    * 系统派发参数中指定那个键为传递给子类的方法的参数
    */
   const DISPATCH_ARGS = 'args';
   /**
    * 子请求指定哪个派发方法键
    */
   const DISPATCH_METHOD = 'method';
   /**
    * APP应用调用器
    *
    * @var \Cntysoft\Kernel\App\Caller $appCaller
    */
   protected $appCaller = null;
   /**
    * 对象服务管理器
    *
    * @var \Phalcon\DI $di
    */
   protected $di = null;
   /**
    * @var boolean $isDispatcher 判断当前的响应器是否为中间派发器，中间派发器认证直接穿过验证
    */
   protected $isDispatcher = false;
   /**
    * 是否需要验证API的认证码， 这个字段设置一定要谨慎， 否则可能造成安全漏洞
    * 如果不验证，那么也代码里面的函数，千万不能进行危险性很高的操作
    *
    * @var boolean $requireCheckAuthorizeCode
    */
   protected $requireCheckAuthorizeCode = true;
   /**
    * @var array $handllerObjectHash
    */
   protected $handllerObjectHash = array();
   /**
    * 假如是派发器的话， 基本名称空间
    *
    * @var string $targetDispatchNs
    */
   protected $targetDispatchNs = null;

   public function __construct()
   {
      $this->di = Kernel\get_global_di();
      $this->appCaller = $this->di->get('AppCaller');
   }
   /**
    * 是否为派发器
    *
    * @return boolean
    */
   public function isDispatcher()
   {
      return $this->isDispatcher;
   }

   /**
    * 派发子请求
    *
    * @param array $meta
    */
   public function dispatcherRequest(array $meta)
   {
      if(!$this->isDispatcher){
         Kernel\throw_exception( new Exception(
            StdErrorType::msg('E_SYS_NOT_DISPATCHABLE', get_class($this)), StdErrorType::code('E_SYS_NOT_DISPATCHABLE')), \Cntysoft\STD_EXCEPTION_CONTEXT);
      }
      $this->checkRequireParams($meta, array(
         self::DISPATCH_KEY,
         self::DISPATCH_METHOD
      ));
      $subKey = $meta[self::DISPATCH_KEY];
      if(!$this->targetDispatchNs){
         $mainKey = get_class($this);
      }else{
         $mainKey = $this->targetDispatchNs;
      }
      $fullKey = $mainKey.'_'.$subKey;
      if (!array_key_exists($fullKey, $this->handllerObjectHash)) {
         $cls = $this->scriptClsNameProcess($meta,'\\'.$mainKey.'\\'.$subKey);
         if (!class_exists($cls)) {
            Kernel\throw_exception(new Exception(
               StdErrorType::msg('E_CLASS_NOT_EXIST', $cls), StdErrorType::code('E_CLASS_NOT_EXIST')), \Cntysoft\STD_EXCEPTION_CONTEXT);
         }
         $method = $meta[self::DISPATCH_METHOD];
         $handlerObject = new $cls();
         $this->handllerObjectHash[$fullKey] = $handlerObject;
      }else{
         $handlerObject = $this->handllerObjectHash[$fullKey];
      }

      if (!method_exists($handlerObject, $method)) {
         Kernel\throw_exception(new Exception(
            StdErrorType::msg('E_METHOD_NOT_EXIST', $cls, $method), StdErrorType::code('E_METHOD_NOT_EXIST')), \Cntysoft\STD_EXCEPTION_CONTEXT);
      }
      $args = array();
      if (array_key_exists(self::DISPATCH_ARGS, $meta)) {
         $args = (array)$meta[self::DISPATCH_ARGS];
      }
      return $handlerObject->{$method}($args);
   }
   /**
    * 根据自己的需求对派发出去的脚本类名称进行处理
    *
    * @param array $meta
    * @param array $clsName 标准处理过程得到的类的名称
    * @return string
    */
   protected function scriptClsNameProcess(array &$meta, $clsName)
   {
      return $clsName;
   }
   /**
    * 判断这个Sys Api类是否需要检查API认证码
    *
    * @return boolean
    */
   public function requireCheckAuthorizeCode()
   {
      return $this->requireCheckAuthorizeCode;
   }
   /**
    * 检查必要参数
    *
    * @param array $data
    * @param array $fields
    * @throw Exception\InvalidArgumentException
    */
   protected function checkRequireParams(array &$data = array(), array $fields = array())
   {
      Kernel\ensure_array_has_fields($data, $fields);
   }

   /**
    * 获取相关的分页参数
    */
   protected function getPageParams(&$orderBy, &$limit, &$offset, &$params)
   {
      $orderBy = array_key_exists('orderBy', $params) ? $params['orderBy'] : array();
      $limit = array_key_exists('limit', $params) ? $params['limit'] : null;
      $offset = array_key_exists('start', $params) ? $params['start'] : null;
   }

   /**
    * @return \Phalcon\DI
    */
   protected function getDi()
   {
      return $this->di;
   }
}