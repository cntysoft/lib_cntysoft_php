<?php
/**
 * Cntysoft Cloud Software Team
 *
 * @author SOFTBOY <cntysoft@163.com>
 * @copyright  Copyright (c) 2010-2011 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license    http://www.cntysoft.com/license/new-bsd     New BSD License
 */
namespace Cntysoft\Phalcon\Mvc\Listeners;
use Cntysoft\Phalcon\Events\ListenerAggregateInterface;
use Phalcon\Mvc\Router;
use Phalcon\Mvc\Dispatcher;
use Cntysoft\Kernel\ConfigProxy;
use Cntysoft\Phalcon\Mvc\Exception;
use Cntysoft\Kernel;
use Cntysoft\Kernel\StdErrorType;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Mvc\Dispatcher\Exception as DispatchException;
use Cntysoft\Framework\Qs\View;
use Cntysoft\Framework\Core\Domain\Binder;
/**
 * 系统一些小东西初始化监听类
 */
class BootstrapListener implements ListenerAggregateInterface
{
   /**
    * @inheritdoc
    */
   public function attach(\Phalcon\Events\ManagerInterface $events)
   {
      $events->attach('application:boot', $this);
      $events->attach('application:beforeStartModule', $this);
      $events->attach('application:afterStartModule', $this);
      $events->attach('application:boot', $this);
   }

   /**
    * 注册一些在引导时候进行的操作
    *
    * @param \Phalcon\Events\Event $event
    * @param \Cntysoft\Phalcon\Mvc\Application $application
    */
   public function boot($event, $application)
   {
      $this->registerSysRouteHandler($application);
      $this->registerDispatcherHandler($application);
      $this->registerVenderFrameworkHandler();
   }

   /**
    * @param \Phalcon\Events\Event $event
    * @param \Cntysoft\Phalcon\Mvc\Application $application
    */
   public function afterStartModule($event, $application)
   {
      $this->setupTplMapHandler($event->getData());
   }

   /**
    * 初始化系统级别的一些路由，这些路由不会从配置文件里面来一般硬编码
    *
    * @param \Cntysoft\Phalcon\Mvc\Application $application
    */
   protected function registerSysRouteHandler($application)
   {
      $di = $application->getDI();
      $router = new Router();
      $router->setDefaultAction('index');
      $router->setDefaultController('Category');
      $router->add('/:module/:controller/:action/:params', array(
         'module'     => 1,
         'controller' => 2,
         'action'     => 3,
         'params'     => 4
      ));
      $this->registerModulesRouteConfigHandler($router);
      /**
       * 这里有个很重要的问题,也就是如何保护系统管理界面的登录地址
       * @todo 把系统管理地址的路由信息有安全框架管理
       * 在安全框架没有辨析出来之前暂时用数组保存相关配置信息
       * 后期有程序来管理这些
       */
      $cfg = ConfigProxy::getGlobalConfig();
      $router->add('/'.$cfg->platformEntryPoint, array(
         'module'     => 'Sys',
         'controller' => 'Index',
         'action'     => 'platform'
      ));


//      //添加教堂管理入口
//      $router->add('/'.$cfg->churchEntryPoint, array(
//         'module'     => 'Sys',
//         'controller' => 'Index',
//         'action'     => 'church'
//      ));
//      //测试Sencha CMD
//      $router->add('/Sencha', array(
//         'module'     => 'Sys',
//         'controller' => 'Index',
//         'action'     => 'Sencha'
//      ));
      $this->configRouter($router, $cfg);
      $router->setDI($di);
      $di->setShared('router', $router);
   }

   /**
    * 设置路由相关钩子函数
    *
    * @param \Phalcon\Mvc\Router $router
    * @param \Phalcon\Config $config
    */
   protected function configRouter($router,$config)
   {}

// 放在子类

   /**
//    * 主要是识别域名或者二级域名然后寻找到教堂对应的教堂id
//    *
//    * @param \Cntysoft\Phalcon\Mvc\Application $application
//    */
//   protected function setupChurchId(\Cntysoft\Phalcon\Mvc\Application $application)
//   {
//      $request = $application->getDI()->get('request');
//      $domain = $request->getServerName();
//      $parts = explode('.', $domain);
//      $isOk = true;
//      $last = count($parts) - 1;
//      $churchId = -1;
//      if ($last > 0) {
//         if ($parts[$last - 1].'.'.$parts[$last] !== PLATFORM_DOMAIN) {
//            //有绑定的域名
//            $churchId = Binder::transform($domain);
//         } else {
//            //二级域名
//            $repositoy = new Repository();
//            $churchId = $repositoy->getChurchIdByKey($parts[0]);
//         }
//         $isOk = $churchId <= 0 ? false : true;
//      }else{
//         $isOk = false;
//      }
//      if('localhost' == $domain && SYS_MODE == SYS_M_DEBUG){
//         $isOk = true;
//         $churchId = 1;
//      }
//      if (!$isOk) {
//         if (SYS_MODE == SYS_M_DEBUG) {
//            die('教堂不存在');
//         } else {
//            //重定向到一个页面不存在的页面
//            header('location:/Pages/Exception/churchNotExist');
//         }
//      }
//      Kernel\get_church_id($churchId);
//   }

   /**
    * 初始化系统派发器
    *
    * @param \Cntysoft\Phalcon\Mvc\Application $application
    */
   protected function registerDispatcherHandler($application)
   {
      //暂时不知道是否需要修改，先放个默认的
      $di = $application->getDi();
      $eventsManager = new EventsManager();
      $eventsManager->attach("dispatch:beforeException", function($event, $dispatcher, $exception)use($di) {
         //Handle 404 exceptions
         //派发异常
         if ($exception instanceof DispatchException) {
            if (SYS_MODE == SYS_M_DEBUG) {
               if ('HtmlController' == $dispatcher->getHandlerClass()) {
                  Kernel\throw_exception(new Exception(
                     StdErrorType::msg('E_HTML_IS_NOT_BUILD', $_SERVER['REQUEST_URI']), StdErrorType::code('E_HTML_IS_NOT_BUILD')));
               } else {
                  throw $exception;
               }
            } else {
               $dispatcher->forward(array(
                  'module'     => 'Front',
                  'controller' => 'Exception',
                  'action'     => 'pageNotExist'
               ));
               return false;
            }
         }
         //Alternative way, controller or action doesn't exist
         if ($event->getType() == 'beforeException') {
            if (SYS_MODE == SYS_M_DEBUG) {
               throw $exception;
            } else {
               switch ($exception->getCode()) {
                  case \Phalcon\Dispatcher::EXCEPTION_HANDLER_NOT_FOUND:
                  case \Phalcon\Dispatcher::EXCEPTION_ACTION_NOT_FOUND:
                     $dispatcher->forward(array(
                        'module'     => 'Pages',
                        'controller' => 'Exception',
                        'action'     => 'pageNotExist'
                     ));
                     return false;
               }
            }
         }
      });
      $dispatcher = new Dispatcher();
      $dispatcher->setEventsManager($eventsManager);
      $di->setShared('dispatcher', $dispatcher);
   }

   /**
    * 注册所有模块的路由信息
    *
    * @param \Phalcon\Mvc\Router $router
    */
   protected function registerModulesRouteConfigHandler(Router $router)
   {
      //这里只需要注册路由相关信息
      $globalConfig = ConfigProxy::getGlobalConfig();
      $modules = $globalConfig->modules;
      foreach ($modules as $mname => $module) {
         if ($module->hasConfig) {
            $mcfg = ConfigProxy::getModuleConfig($mname);
            if (isset($mcfg->routes)) {
               foreach ($mcfg->routes as $route) {
                  $router->add($route->rule, $route->option->toArray());
               }
            }
         }
      }
   }

   /**
    * 注册系统使用的第三方框架
    */
   protected function registerVenderFrameworkHandler()
   {
      $config = ConfigProxy::getGlobalConfig();
      if ($config->offsetExists(\Cntysoft\CNF_VENDER_FRAMEWORK)) {
         $frameworks = $config->offsetGet(\Cntysoft\CNF_VENDER_FRAMEWORK);
         foreach ($frameworks as $framework) {
            $script = $this->getInitScriptObject($framework);
            $script->init();
         }
      }
   }

   /**
    * 设置模板映射
    */
   protected function setupTplMapHandler($module)
   {
      $config = ConfigProxy::getModuleConfig($module);
      if (isset($config->tplMap)) {
         View::setTplMap($config->tplMap->toArray());
      }
   }

   /**
    * @param string $name
    * @throws \Cntysoft\Phalcon\Mvc\Exception
    * @return  \Cntysoft\VenderFrameworkProcess\VenderFrameworkInitInterface
    */
   protected function getInitScriptObject($name)
   {
      //脚本是在系统的autoloader控制之内
      $ns = '\\Cntysoft\\VenderFrameworkProcess\\Scripts'.'\\'.$name;
      $class = $ns.'\\'.'InitScript';
      if (!class_exists($class)) {
         Kernel\throw_exception(new Exception(
            StdErrorType::msg('E_CLASS_NOT_EXIST', $class), StdErrorType::code('E_CLASS_NOT_EXIST')), \Cntysoft\STD_EXCEPTION_CONTEXT);
      }
      $object = new $class();
      if (!$object instanceof \Cntysoft\VenderFrameworkProcess\VenderFrameworkInitInterface) {
         Kernel\throw_exception(new Exception(
            StdErrorType::msg('E_OBJECT_TYPE_ERROR', 'Cntysoft\VenderFrameworkProcess\VenderFrameworkInitInterface', StdErrorType::code('E_OBJECT_TYPE_ERROR'))), \Cntysoft\STD_EXCEPTION_CONTEXT);
      }
      return $object;
   }

}