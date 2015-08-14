<?php
/**
 * Cntysoft Cloud Software Team
 *
 * @author SOFTBOY <cntysoft@163.com>
 * @copyright  Copyright (c) 2010-2011 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license    http://www.cntysoft.com/license/new-bsd     New BSD License
 */
namespace Cntysoft\Framework\Qs;

use Phalcon\Events\Manager as EventsManager;
use Cntysoft\Kernel;
use Cntysoft\Kernel\StdDir;
use Cntysoft\Stdlib\Filesystem;
use Phalcon\Events\ManagerInterface;
use Phalcon\DiInterface;

/**
 * 系统模板显示引擎
 */
class View implements \Phalcon\Events\EventsAwareInterface, \Phalcon\DI\InjectionAwareInterface, \Phalcon\Mvc\ViewInterface
{
   const KEY_RESOLVE_TYPE = '_RESOLVE_TYPE_'; //相关控制器的模板寻找方法KEY
   const KEY_RESOLVE_DATA = '_RESOLVE_DATA_'; //模板寻找数据的键KEY
   const KEY_TPL_VAR = '_TPL_VARS_';
   const KEY_ROUTE_PARAMS = '_ROUTE_PARAMS_'; //路由传递的参数
   const KEY_RENDER_CTRL = '_RENDER_CTRL_'; //渲染控制
   const KEY_DISABLED_VIEW = '_DISABLED_KEY_';
   /**
    * 这个常量主要用在从控制器向模板引擎传递渲染参数，我们使用全局变量进行传递
    */
   const MVC_DISPATCH_OPTION_KEY = '_DISPATCH_OPTION_KEY_';
   //模板寻找策略
   const TPL_RESOLVE_DIRECT = 1;
   const TPL_RESOLVE_MAP = 2;
   const TPL_RESOLVE_FINDER = 3;
   const ENGINE_MODE_NORMAL = 1;
   const ENGINE_MODE_BUILD = 2;
   /**
    * 支持的标签种类
    */
   const TAG_LABEL = 'Label';
   const TAG_DS = 'Ds';
   const TAG_PHPVAR = 'PhpVar';
   const TAG_DS_FIELD = 'DsField';
   const TAG_SITE_CONFIG = 'SiteConfig';
   const TAG_SYS = 'Sys';
   /**
    * 系统访问类型
    */
   const DEVICE_PC = 'Pc';
   const DEVICE_PAD = 'Pad';
   const DEVICE_MOBILE = 'Mobile';
   /**
    * 模板引擎两种模式
    */
   const M_NORMAL = 1;
   const M_BUILD = 2;
   /**
    * @var \Phalcon\Events\ManagerInterface $eventsManager
    */
   protected $eventsManager = null;
   /**
    * @var \Phalcon\DiInterface $di
    */
   protected $di = null;
   /**
    * @var \Cntysoft\Framework\Qs\Engine\EngineInterface $renderEngine
    */
   protected $renderEngine = null;
   /**
    * 模板储存目录
    *
    * @var string $tplRootDir
    */
   protected $tplRootDir = null;
   /**
    * 本次解析模板结果
    *
    * @var string $content
    */
   protected $content = '';
   /**
    * 当前渲染选项数据
    *
    * @var array $renderOpt
    */
   public static $renderOpt = null;
   /**
    * 本次请求的路由信息
    *
    * @var array $routeInfo
    */
   protected $routeInfo = array();
   /**
    * @var  \Phalcon\Cache\Backend\File $cache
    */
   protected $cache = null;
   /**
    *  设备类型，如果没有设置那么我们就自动探测
    *
    * @var string $deviceType
    */
   protected static $deviceType = null;
   /**
    * 设置模板映射
    *
    * @var array $tplMap
    */
   protected static $tplMap = array();
   /**
    * 模板引擎的模式，分为正常模式和生成模式
    *
    * @var int $mode
    */
   protected $mode = self::ENGINE_MODE_NORMAL;
   /**
    * 系统模板方案编号
    *
    * @var int $tplProject
    */
   protected $tplProject = 1;

   /**
    * @var \Cntysoft\Framework\Qs\TagResolverInterface
    */
   protected static $tagResolver = null;

   /**
    * @var \Cntysoft\Framework\Qs\AssetResolverInterface
    */
   protected static $assetResolver = null;

   /**
    * @param \Phalcon\DiInterface $dependencyInjector
    * @return \Cntysoft\Framework\Qs\View
    */
   public function setDI(\Phalcon\DiInterface $dependencyInjector)
   {
      $this->di = $dependencyInjector;
      return $this;
   }

   /**
    * @return \Phalcon\DiInterface
    */
   public function getDI()
   {
      return $this->di;
   }

   /**
    * 设置模板文件根目录
    *
    * @param string $dir
    * @return  \Cntysoft\Framework\Qs\View
    */
   public function setTplRootDir($dir)
   {
      $this->tplRootDir = $dir;
      return $this;
   }

   /**
    * @return string
    */
   public function getTplRootDir()
   {
      return $this->tplRootDir;
   }

   /**
    * 开始一次新的解析请求，这个操作主要是清除上次的解析结果
    */
   public function start()
   {
      $this->content = '';
      ob_start();
   }

   /**
    * 渲染请求，在这里我们根据控制器传来的参数进行模板相关的寻找
    *
    * @param string $controllerName
    * @param string $actionName
    * @param array $params 控制器参数
    */
   public function render($controllerName, $actionName, $params = null)
   {
      $renderOpt = self::$renderOpt;
      $renderOpt[self::KEY_ROUTE_PARAMS] = $params;
      $renderOpt += array(
         self::KEY_RESOLVE_TYPE => self::TPL_RESOLVE_DIRECT,
         self::KEY_RENDER_CTRL => true
      );
      if (!$renderOpt[self::KEY_RENDER_CTRL]) {
         return;
      }
      Kernel\ensure_array_has_fields($renderOpt, array(
         self::KEY_RESOLVE_DATA
      ), 'controller action render params need require keys : %s');
      $params['controller'] = $controllerName;
      $params['action'] = $actionName;
      $this->routeInfo = array_merge($this->routeInfo, $params);
      $tpl = $this->resolveTpl($renderOpt[self::KEY_RESOLVE_TYPE], $renderOpt[self::KEY_RESOLVE_DATA]);
      $engine = $this->getRenderEngine();
      $this->content = $engine->render($tpl);
   }

   /**
    * 完成一次渲染请求
    */
   public function finish()
   {
      $this->content = ob_get_clean();
   }

   /**
    * @return string
    */
   public function getContent()
   {
      return $this->content;
   }

   /**
    * @param \Phalcon\Events\ManagerInterface $eventsManager
    * @return  \Cntysoft\Framework\Qs\View
    */
   public function setEventsManager(\Phalcon\Events\ManagerInterface  $eventsManager)
   {
      $this->eventsManager = $eventsManager;
      return $this;
   }

   /**
    * 获取事件管理器
    *
    * @return \Phalcon\Events\ManagerInterface
    */
   public function getEventsManager()
   {
      if (null == $this->eventsManager) {
         $this->eventsManager = new EventsManager();
      }
      return $this->eventsManager;
   }

   /**
    * 设置本次选择选项
    */
   public static function setRenderOpt($opt)
   {
      self::$renderOpt = $opt;
   }

   /**
    * 获取渲染引擎
    *
    * @return  \Cntysoft\Framework\Qs\Engine\EngineInterface
    */
   public function getRenderEngine()
   {
      if (null == $this->renderEngine) {
         $this->renderEngine = new \Cntysoft\Framework\Qs\Engine\Php($this);
      }
      return $this->renderEngine;
   }

   /**
    * 获取路由信息
    *
    * @return array
    */
   public function getRouteInfo()
   {
      return $this->routeInfo;
   }

   /**
    * 设置模板引擎使用的路由信息项
    *
    * @param string $key
    * @param mixed $value
    * @return \Cntysoft\Framework\Qs\View
    */
   public function setRouteInfoItem($key, $value)
   {
      $this->routeInfo[$key] = $value;
      return $this;
   }

   /**
    * 模板寻找逻辑， 两种方式一种是直接指定一种是查表
    *
    * @param string $resolveType
    * @param string $resolveData
    * @return string
    */
   public function resolveTpl($resolveType, $resolveData)
   {
      //直接指定的话就不会去区分手机平板还是PC了
      if (null == self::$deviceType) {
         $deviceType = $this->detactDeviceType();
      } else {
         $deviceType = self::$deviceType;
      }
      $baseDir = $this->tplRootDir;
      if (self::TPL_RESOLVE_DIRECT == $resolveType) {
         return $resolveData;
      } else if (self::TPL_RESOLVE_FINDER == $resolveType) {
         return $baseDir . DS . $this->tplProject . DS . $deviceType . DS . $resolveData;
      } else if (self::TPL_RESOLVE_MAP == $resolveType) {
         if (!array_key_exists($resolveData, self::$tplMap)) {
            if (DEPLOY_ENV_PRODUCT == SYS_MODE) {
               $errorType = ErrorType::getInstance();
               Kernel\throw_exception(
                  new Exception(
                     sprintf($errorType->msg('E_TPL_MAP_KEY_NOT_EXIST'), $resolveData),
                     $errorType->code('E_TPL_MAP_KEY_NOT_EXIST')),$errorType);
            } else {
               die('map : ' . $resolveData . ' is not exist');
            }
         }
         return $baseDir . DS . $this->tplProject . DS . $deviceType . DS . self::$tplMap[$resolveData];
      }
   }

   /**
    * 探测访问设备的类型
    *
    * @return string
    */
   protected function detactDeviceType()
   {
      $agent = $_SERVER['HTTP_USER_AGENT'];
      $iphone = strstr(strtolower($agent), 'mobile');
      $android = strstr(strtolower($agent), 'android');
      $windowsPhone = strstr(strtolower($agent), 'phone');
      $androidTablet = false;
      if (strstr(strtolower($agent), 'android')) {
         if (!strstr(strtolower($agent), 'mobile')) {
            $androidTablet = true;
         }
      }
      $ipad = strstr(strtolower($agent), 'ipad');
      if ($androidTablet || $ipad) {
         return self::DEVICE_PAD;
      } elseif ($iphone && !$ipad || $android && !$androidTablet || $windowsPhone) { //If it's a phone and NOT a tablet
         return self::DEVICE_MOBILE;
      } else {
         return self::DEVICE_PC;
      }
   }

   /**
    * 设置模板映射，默认使用合并
    *
    * @param array $map
    */
   public static function setTplMap(array $map)
   {
      self::$tplMap = array_merge(self::$tplMap, $map);
   }

   /**
    * 设置设备类型
    *
    * @param string $deviceType
    */
   public static function setDeviceType($deviceType)
   {
      self::$deviceType = $deviceType;
   }

   /**
    * @return  \Phalcon\Cache\Backend\File
    */
   public function getCache()
   {
      if (!$this->cache) {
         $this->cache = Kernel\make_cache_object('Qs');
      }
      return $this->cache;
   }

   /**
    * 删除所有模板引擎生成缓存文件
    */
   public static function clearCache()
   {
      $cacheDir = \Cntysoft\Kernel\real_path(StdDir::getCacheDir() . DS . 'Qs');
      if (file_exists($cacheDir)) {
         Filesystem::deleteDirRecusive($cacheDir);
      }
   }

   /**
    * 设置模板引擎的模式
    *
    * @param int $mode
    */
   public function setMode($mode)
   {
      $this->mode = (int)$mode;
      return $this;
   }

   /**
    * @return int
    */
   public function getMode()
   {
      return $this->mode;
   }

   /**
    * 获取模板变量
    *
    * @return mixed
    */
   public static function getTplVar($key = null)
   {
      $tplVar = isset(self::$renderOpt[self::KEY_TPL_VAR]) ? self::$renderOpt[self::KEY_TPL_VAR] : array();
      if (null == $key) {
         return $tplVar;
      }
      if (isset($tplVar[$key])) {
         return $tplVar[$key];
      }
      return null;
   }

   /**
    * 设置系统的模板选项
    *
    * @param $tplProject
    * @return $this
    */
   public function setTplProject($tplProject)
   {
      $this->tplProject = (int)$tplProject;
      return $this;
   }


   /**
    * @param TagResolverInterface $finder
    */
   public static function setTagResolver(TagResolverInterface $finder)
   {
      if (self::$tagResolver != $finder) {
         self::$tagResolver = $finder;
      }
   }

   /**
    * @return TagResolverInterface
    */
   public static function getTagResolver()
   {
      if (null == self::$tagResolver) {
         $errorType = ErrorType::getInstance();
         Kernel\throw_exception(
            new Exception(
               $errorType->msg('E_TAG_DIR_FINDER_NOT_SET'),
               $errorType->code('E_TAG_DIR_FINDER_NOT_SET')), $errorType);
      }
      return self::$tagResolver;
   }

   /**
    * @param AssetResolverInterface $finder
    */
   public static function setAssetResolver(AssetResolverInterface $finder)
   {
      if (self::$assetResolver != $finder) {
         self::$assetResolver = $finder;
      }
   }

   /**
    * @return AssetResolverInterface
    */
   public static function getAssetResolver()
   {
      if (null == self::$assetResolver) {
         $errorType = ErrorType::getInstance();
         Kernel\throw_exception(
            new Exception(
               $errorType->msg('E_TAG_DIR_FINDER_NOT_SET'),
               $errorType->code('E_TAG_DIR_FINDER_NOT_SET')), $errorType);
      }
      return self::$assetResolver;
   }

   /**
    * @return int
    */
   public function getTplProject()
   {
      return $this->tplProject;
   }

   //暂时不用的接口
   public function setViewsDir($viewsDir)
   {

   }

   public function getViewsDir()
   {

   }

   public function setLayoutsDir($layoutsDir)
   {

   }

   public function getLayoutsDir()
   {

   }

   public function setPartialsDir($partialsDir)
   {

   }

   public function getPartialsDir()
   {

   }

   public function setBasePath($basePath)
   {

   }

   public function setRenderLevel($level)
   {

   }

   public function setMainView($viewPath)
   {

   }

   public function getMainView()
   {

   }

   public function setLayout($layout)
   {

   }

   public function getLayout()
   {

   }

   public function setTemplateBefore($templateBefore)
   {

   }

   public function cleanTemplateBefore()
   {

   }

   public function setTemplateAfter($templateAfter)
   {

   }

   public function cleanTemplateAfter()
   {

   }

   public function setParamToView($key, $value)
   {

   }

   public function setVar($key, $value)
   {

   }

   public function getParamsToView()
   {

   }

   public function getControllerName()
   {

   }

   public function getActionName()
   {

   }

   public function getParams()
   {

   }
   /**
    * @param array $engines
    */
   public function registerEngines(array $engines)
   {

   }

   public function pick($renderView)
   {

   }

   public function partial($partialPath, $params=null)
   {

   }

   public function cache($options = null)
   {

   }

   public function setContent($content)
   {

   }

   public function getActiveRenderPath()
   {

   }

   public function disable()
   {

   }

   public function enable()
   {

   }

   public function reset()
   {

   }

   public function getCurrentRenderLevel()
   {

   }

   public function getRenderLevel()
   {

   }

   public function isDisabled()
   {

   }
   public function getBasePath()
   {
      
   }

}
//给Qs名称空间定义几个常量
namespace Qs;
