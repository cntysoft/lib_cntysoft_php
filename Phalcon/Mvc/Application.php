<?php
/**
 * Cntysoft Cloud Software Team
 *
 * @author SOFTBOY <cntysoft@163.com>
 * @copyright  Copyright (c) 2010-2011 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license    http://www.cntysoft.com/license/new-bsd     New BSD License
 */
namespace Cntysoft\Phalcon\Mvc;
use Phalcon\Events\Manager as EventManager;
use Cntysoft\Kernel\ConfigProxy;

use Cntysoft\Phalcon\Mvc\Listeners;
/**
 * MVC应用程序类定义，扩展Phalcon程序类，加入自己一些逻辑
 */
class Application extends \Phalcon\Mvc\Application
{
   /**
    *  这个模块是默认的模块，也就是负责前台浏览的一些模块
    *
    * @inheritdoc
    */
   protected $_defaultModule = 'Pages';
   /**
    * 全局对象管理器
    *
    * @var \Phalcon\DI $globalDi
    */
   protected static $globalDi = null;

   public function __construct($dependencyInjector = null)
   {
      $globalConfig = ConfigProxy::getGlobalConfig();
      //设置当前系统的运行模式常量
      if(!isset($globalConfig->systemMode)){
         define('SYS_RUNTIME_MODE', SYS_RUNTIME_MODE_PRODUCT);
      }else{
         $mode = $globalConfig->systemMode;
         if ($mode !== SYS_RUNTIME_MODE_DEBUG && $mode !== SYS_RUNTIME_MODE_PRODUCT) {
            die(sprintf('sys run mode : %x is not support, SYS_RUNTIME_MODE_PRODUCT : %x and SYS_RUNTIME_MODE_DEBUG : %x', $mode, SYS_RUNTIME_MODE_PRODUCT, SYS_RUNTIME_MODE_DEBUG));
         }
         define('SYS_RUNTIME_MODE', $mode);
      }
      self::$globalDi = $dependencyInjector;
      parent::__construct($dependencyInjector);
//      $this->loadCoreFiles();
      $this->beforeInitialized();
      //初始化数据库连接信息
      $this->initApplicationEventManager();
      $this->bindListeners();
      $this->setupSysModules();
      $this->beforeDbConnInitialized();
      $this->initDbConnection();
      $this->afterDbConnInitialized();
   }
   protected function beforeInitialized()
   {}
   /**
    * 在系统初始化之后的钩子函数
    */
   protected function afterDbConnInitialized()
   {
   }

   /**
    * 在系统初始化之后的钩子函数
    */
   protected function beforeDbConnInitialized()
   {
   }

   /**
    * @return \Phalcon\DI
    */
   public static function getGlobalDi()
   {
      return self::$globalDi;
   }

   /**
    * 初始化监听函数
    */
   protected function bindListeners()
   {
      $eventManager = $this->getEventsManager();
      $bootstrapListener = new Listeners\BootstrapListener();
      $viewListener = new Listeners\ViewListener();
      $serviceListener = new Listeners\ServiceListener();
      $serviceListener->attach($eventManager);
      $bootstrapListener->attach($eventManager);
      $viewListener->attach($eventManager);
   }

   /**
    * 加载系统中一些过程式的代码
    */
   protected function loadCoreFiles()
   {
      $files = array(
         CNTY_SYS_LIB_DIR.DS.'Const.php',
         CNTY_SYS_LIB_DIR.DS.'Kernel'.DS.'Funcs'.DS.'Common.php',
         CNTY_SYS_LIB_DIR.DS.'Kernel'.DS.'Funcs'.DS.'Internal.php'
      );
      foreach ($files as $file) {
         include $file;
      }
   }
   /**
    * 初始化系统级别的事件管理器
    */
   protected function initApplicationEventManager()
   {
      $this->di->setShared('eventManager', function() {
         return new EventManager();
      });
      $this->setEventsManager($this->di->getShared('eventManager'));
   }

   /**
    * 设置系统模块
    */
   protected function setupSysModules()
   {
      $globalConfig = ConfigProxy::getGlobalConfig();
      $modules = $globalConfig->modules;
      $this->registerModules($modules->toArray());
   }

   /**
    * 初始化数据库链接钩子函数
    */
   protected function initDbConnection()
   {
   }

}