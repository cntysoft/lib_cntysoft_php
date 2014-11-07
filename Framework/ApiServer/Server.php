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
use Cntysoft\Phalcon\Mvc\Application;
use Cntysoft\Stdlib\Filesystem;
use Cntysoft\Kernel\StdErrorType;
/**
 * 系统管理框架，服务器类
 */
class Server
{
   const DEFAULT_SCRIPT_NS = '\\SysApiHandler';

   /**
    * @var \Cntysoft\Kernel\App\Caller  $appCaller
    */
   protected $appCaller = null;
   /**
    * @var Server $self
    */
   protected static $self = null;

   /**
    * 系统script加载器
    *
    * @var \Cntysoft\Framework\ApiServer\ScriptBroker $scriptBroker
    */
   protected $scriptBroker = null;
   /**
    * 管理执行代码的名称空间
    *
    * @var String $scriptNs
    */
   protected $scriptNs = '';
   /**
    * @var array $whiteList API调用白名单
    */
   protected $whiteList = array();

   protected function __construct()
   {
      $this->scriptBroker = new ScriptBroker();
      $this->scriptNs = self::DEFAULT_SCRIPT_NS;
      $this->initApiWhiteList();
   }

   /**
    * 进行api调用
    *
    * @param string $type
    * @param array $meta
    * @param int $authTargetType API调用认证类型
    * @return array
    */
   public function doCall($type, array $meta/*, $authTargetType*/)
   {
      $params = isset($meta[\Cntysoft\INVOKE_PARAM_KEY]) ? $meta[\Cntysoft\INVOKE_PARAM_KEY] : array();
      $ret = array();
      if (\Cntysoft\API_CALL_APP == $type) {
         $invokeMeta = $meta[\Cntysoft\INVOKE_META_KEY];
         $module = $invokeMeta['module'];
         $name = $invokeMeta['name'];
         $method = $invokeMeta['method'];
         $appCaller = $this->getAppCaller();
         $invokeKey = $module.'.'.$name.'.'.$method;
         //判断是否在白名单里面
//            if(!in_array($invokeKey, $this->whiteList[\Cntysoft\API_CALL_APP])){
//                Authorize::check($authTargetType, $type, $invokeKey);
//            }
         $ret = (array) $appCaller->ajaxCall($module, $name, $method, $params);
      } elseif (\Cntysoft\API_CALL_SYS == $type) {
         $invokeMeta = $meta[\Cntysoft\INVOKE_META_KEY];
         $name = $invokeMeta['name'];
         $method = $invokeMeta['method'];
         $handler = $this->loadCallHandler($name);
         $fn = $method;
         //判断函数存在不
         if (!method_exists($handler, $fn)) {
            throw new Exception(
               Kernel\StdErrorType::msg('E_API_SYS_HANDLER_NOT_EXIST', $fn), Kernel\StdErrorType::code('E_API_SYS_HANDLER_NOT_EXIST'));
         }
         $invokeKey = $name.'.'.$method;
//            //判断是否在白名单里面
//            if($handler->requireCheckAuthorizeCode() && !in_array($invokeKey, $this->whiteList[\Cntysoft\API_CALL_SYS])){
//                Authorize::check($authTargetType, $type, $invokeKey, $params, $handler);
//            }
         $ret = (array) $handler->$fn($params);
      }
      return $ret;
   }

   /**
    * @return \Cntysoft\Kernel\App\Caller
    */
   protected function getAppCaller()
   {
      if(null == $this->appCaller){
         $this->appCaller = Application::getGlobalDi()->get('AppCaller');
      }
      return $this->appCaller;
   }
   /**
    * 获取执行脚本的目录
    *
    * @return string
    */
   public function getScriptDir()
   {
      return CNTY_ROOT_DIR.DS.'SysApiHandler';
   }

   /**
    * 获取API调用的白名单
    *
    * @return array
    */
   public function getWhiteList()
   {
      return $this->whiteList;
   }

   /**
    * 获取系统所有的Api调用列表
    *
    * @return array
    */
   public function getAllSysScriptApiList()
   {
      $allSysHandlers = $this->getSysHandlerList();
      $ret = array();
      foreach($allSysHandlers as $handler){
         $ret[$handler] = $this->getSysScriptApiList($handler);
      }
      return $ret;
   }

   /**
    * 获取Sys脚本的API列表
    *
    * @return array
    */
   public function getSysScriptApiList($name)
   {
      $allSysHandlers = $this->getSysHandlerList();
      if(!in_array($name, $allSysHandlers)){
         Kernel\throw_exception(new Exception(
            StdErrorType::msg('E_API_SYS_HANDLER_NOT_EXIST', $name),
            StdErrorType::code('E_API_SYS_HANDLER_NOT_EXIST')), \Cntysoft\STD_EXCEPTION_CONTEXT);
      }
      if(strpos($name, '.')){
         $name = str_replace('.', '\\', $name);
      }
      $cls = AbstractScript::BASE_NS.'\\'.$name;
      $refl = new \ReflectionClass($cls);
      $ret = array();
      $index = 0;
      foreach($refl->getMethods() as $method){
         if($method->isPublic() && !$method->isConstructor() && !$method->isDestructor()){
            $name = $method->getName();
            if($name != 'isDispatcher' && $name != 'dispatcherRequest'){
               $ret[$method->getName()] = ++$index;
            }
         }
      }
      return $ret;
   }

   /**
    * 获取所有的Sys响应器名称
    *
    * @return array
    */
   public function getSysHandlerList()
   {
      $dir = $this->getScriptDir();
      $list = Filesystem::ls($dir);
      $ret = array();
      foreach($list as $item){
         $filename = $item->getBasename('.php');
         if($item->isFile() && $item->getExtension() == 'php'){
            $ret[] = $filename;
         }else if($item->isDir() && $filename != 'Exception' && $filename != 'AuthorizeCodes'){
            //派发器
            $subDir = $dir.DS.$filename;
            $subList = Filesystem::ls($subDir);
            foreach($subList as $subItem){
               if ($subItem->isFile() && $subItem->getExtension() == 'php' && $subItem->getBasename() !== 'ErrorType.php') {
                  $ret[] = $filename.'.'.$subItem->getBasename('.php');
               }
            }
         }
      }
      return $ret;
   }

   /**
    * 获取api调用对象
    *
    * @return Server
    */
   public static function getInstance()
   {
      if (null === self::$self) {
         self::$self = new self();
      }
      return self::$self;
   }

   /**
    * 将API添加到
    *
    * @param string $type API的类型
    * @param string $api API原型
    * @return \Cntysoft\Framework\ApiServer\Server
    */
   public function addApi2WhiteList($type, $api)
   {
      if($type !== \Cntysoft\API_CALL_APP && $type !== \Cntysoft\API_CALL_SYS){
         Kernel\throw_exception(new Exception(
            StdErrorType::msg('E_API_INVOKE_TYPE_NOT_SUPPORT', $type),
            StdErrorType::code('E_API_INVOKE_TYPE_NOT_SUPPORT')), \Cntysoft\STD_EXCEPTION_CONTEXT);
      }
      $target = &$this->whiteList[$type];
      if(!in_array($api, $target)){
         $target[] = $api;
      }
      return $this;
   }

   /**
    * 获取处理请求的对象
    *
    * @param string  $name 调用类的元信息
    * @return mixed
    */
   protected function loadCallHandler($name)
   {
      $class = $this->scriptNs.'\\'.$name;
      return $this->scriptBroker->get($class);
   }


   /**
    * 初始化API调用白名单
    */
   protected function initApiWhiteList()
   {
      //这两个键值必须存在
      $this->whiteList = array(
         \Cntysoft\API_CALL_APP => array(
            'Christ.User.Perm/login',
            'Sys.User.Perm/logout',
            'Sys.User.Perm/loginByCookie'
         ),
         \Cntysoft\API_CALL_SYS => array(

         )
      );
   }
}
