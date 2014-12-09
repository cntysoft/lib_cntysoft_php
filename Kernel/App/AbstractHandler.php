<?php
/**
 * Cntysoft Cloud Software Team
 *
 * @author SOFTBOY <cntysoft@163.com>
 * @copyright  Copyright (c) 2010-2011 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license    http://www.cntysoft.com/license/new-bsd     New BSD License
 */
namespace Cntysoft\Kernel\App;
use Cntysoft\Kernel;
use Cntysoft\Kernel\StdErrorType;
use Phalcon\Mvc\Model\Resultset\Simple as SimpleResultset;
abstract class AbstractHandler
{
   /**
    * @var \Cntysoft\Kernel\App\AppObject $appObject
    */
   protected $appObject = null;
   /**
    * @var \Phalcon\DI $di
    */
   protected $di;
   /**
    * APP应用调用器
    *
    * @var \Cntysoft\Kernel\App\Caller $appCaller
    */
   protected $appCaller = null;

   /**
    * 构造函数
    */
   public function __construct()
   {
      $this->di = Kernel\get_global_di();
   }


   /**
    * 获取系统错误上下文菜单
    *
    * @return string
    */
   public function getErrorTypeContext()
   {
      return str_replace('\\', '/', get_class($this));
   }
   /**
    * @return \Cntysoft\Stdlib\ErrorType
    */
   protected function getErrorType()
   {
      $appObject = $this->getAppObject();
      return $appObject->getErrorType();
   }

   /**
    * 获取分页参数
    */
   protected function getPageParams(&$orderBy, &$limit, &$offset, &$params)
   {
      Kernel\set_page_var($orderBy, $limit, $offset, $params);
   }

   /**
    * 检查是否具有必要的参数
    *
    * @throws Exception
    */
   protected function checkRequireFields(array &$params = array(), array $requires = array())
   {
      $leak = array();
      Kernel\array_has_requires($params, $requires, $leak);
      if (!empty($leak)) {
         Kernel\throw_exception(new Exception(
            StdErrorType::msg('E_API_INVOKE_LEAK_ARGS', implode(', ', $leak)), StdErrorType::code('E_API_INVOKE_LEAK_ARGS')), \Cntysoft\STD_EXCEPTION_CONTEXT);
      }
   }

   /**
    * @return \Cntysoft\Kernel\App\AppObject
    */
   protected function getAppObject()
   {
      if(null == $this->appObject){
         $parts = explode('\\', get_class($this));
         $this->appObject = new AppObject($parts[1], $parts[2]);
      }
      return $this->appObject;
   }

   /**
    * @param array $items
    * @return array
    */
   protected function getExtJsGridDataSet($items)
   {
      $ret = array();
      if(is_array($items)){
         $ret = $items;
      }else if($items instanceof SimpleResultset){
         foreach($items as $item){
            $ret[] = $item->toArray();
         }
      }
      return array(
         'total' => count($ret),
         'items' => $ret
      );
   }

   /**
    * @return \Cntysoft\Kernel\App\Caller
    */
   protected function getAppCaller()
   {
      if(null == $this->appCaller){
         $this->appCaller =  $this->di->get('AppCaller');
      }
      return $this->appCaller;
   }
}