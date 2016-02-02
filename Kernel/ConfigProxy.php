<?php
/**
 * Cntysoft Cloud Software Team
 * 
 * @author SOFTBOY <cntysoft@163.com>
 * @copyright  Copyright (c) 2010-2011 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license    http://www.cntysoft.com/license/new-bsd     New BSD License
 */
namespace Cntysoft\Kernel;
use Phalcon\Config;
use Cntysoft\Stdlib\ArrayUtils;
use Cntysoft\Stdlib\Filesystem;
/**
 * 这个类负责系统范围里面的配置存取,貌似有点占内存,暂时就实现集中读取的功能
 */
class ConfigProxy
{
   const C_TYPE_GLOBAL = 1;
   const C_TYPE_MODULE = 2;
   const C_TYPE_APP = 3;
   const C_TYPE_FRAMEWORK_SYS = 4;
   const C_TYPE_FRAMEWORK_VENDER = 5;

   protected static $cacher = array();
   /**
    * 获取全局配置信息
    * 
    * @return \Phalcon\Config
    */
   public static function getGlobalConfig()
   {
      $name = 'Application.config';
      $path = CNTY_CFG_DIR . DS . 'Application.config';
      $key = md5($name);
      $cacher = self::getCacher('');
      
      if($cacher->exists($key)){
         $config = $cacher->get($key);
      }else{
         if(file_exists($path . '.json')){
            $config = json_decode(Filesystem::fileGetContents($path . '.json'), true);
            $cacher->save($key, $config);
         }else if(file_exists($path . '.php')){
            $config = include $path . '.php';
            Filesystem::filePutContents($path . '.json', json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            $cacher->save($key, $config);
         }else {
            $config = array();
         }
      }
      
      return new Config($config);
   }

   /**
    * 获取指定模型的相关配置信息
    * 
    * @param string $module
    * @return  \Phalcon\Config
    */
   public static function getModuleConfig($module)
   {
      $g = self::getGlobalConfig();
      $modules = $g->modules;
      if (!$modules->offsetExists($module)) {
         throw_exception(new Exception(
                 StdErrorType::msg('E_MODULE_NOT_SUPPORT', $module), StdErrorType::code('E_MODULE_NOT_SUPPORT')), \Cntysoft\STD_EXCEPTION_CONTEXT);
      }
      $name = $module . '.config';
      $path = CNTY_CFG_DIR . DS . 'Module' . DS . $module . '.config';

      $key = md5($name);
      $cacher = self::getCacher('Module');

      if($cacher->exists($key)){
         $config = $cacher->get($key);
      }else{
         if(file_exists($path . '.json')){
            $config = json_decode(Filesystem::fileGetContents($path . '.json'), true);
            $cacher->save($key, $config);
         }else if(file_exists($path . '.php')){
            $config = include $path . '.php';
            Filesystem::filePutContents($path . '.json', json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            $cacher->save($key, $config);
         }else {
            $config = array();
         }
      }

      return new Config($config);
   }

   /**
    * 获取系统所有的模块的配置信息
    * 
    * @return array
    */
   public static function getModuleConfigs()
   {
      $g = self::getGlobalConfig();
      $modules = $g->modules;
      $ret = array();
      foreach($modules as $name => $module){
         $ret[$name] = self::getModuleConfig($name);
      }
      return $ret;
   }

   /**
    * 获取应用程序配置信息, 配置文件不存在暂时就抛出异常
    * 
    * @param string $module
    * @param string $app
    * @return array
    */
   public static function getAppConfig($module, $app)
   {
      /**
       * 不判断module中是否存在某个APP 直接构造配置文件名称
       * @todo 以后加上 配置文件的后缀信息怎么来 暂时硬编码
       */
      $appKey = $module . '\\' . $app;
      $path = self::getAppConfigFilename($module, $app);
      $name = $app.'.config';
      $key = md5($name);
      $cacher = self::getCacher('App' . DS . $module . DS . $app);

      if($cacher->exists($key)){
         $config = $cacher->get($key);
      }else{
         if(file_exists($path . '.json')){
            $config = json_decode(Filesystem::fileGetContents($path . '.json'), true);
            $cacher->save($key, $config);
         }else if(file_exists($path . '.php')){
            $config = include $path . '.php';
            Filesystem::filePutContents($path . '.json', json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            $cacher->save($key, $config);
         }else {
            throw_exception(new Exception(
                 StdErrorType::msg('E_CONFIG_FILE_NOT_EXIST', str_replace(CNTY_ROOT_DIR, '', $name)), StdErrorType::code('E_CONFIG_FILE_NOT_EXIST')), \Cntysoft\STD_EXCEPTION_CONTEXT);
         }
      }

      return new Config($config);
   }

   /**
    * 获取框架的配置信息
    * 
    * @param string $name 框架的名称
    * @param int $type 框架的类型
    * @return \Phalcon\Config
    */
   public static function getFrameworkConfig($name, $type = self::C_TYPE_FRAMEWORK_SYS)
   {
      //获取特定的路径
      $fileNameInfo = self::getFrameworkConfigFilename($name, $type);

      $frameworkKey = $fileNameInfo[0];
      $path = $fileNameInfo[1];
      $name = $name.'.config';
      $key = md5($name);
      $cacher = self::getCacher($fileNameInfo[2]);

      if($cacher->exists($key)){
         $config = $cacher->get($key);
      }else{
         if(file_exists($path . '.json')){
            $config = json_decode(Filesystem::fileGetContents($path . '.json'), true);
            $cacher->save($key, $config);
         }else if(file_exists($path . '.php')){
            $config = include $path . '.php';
            Filesystem::filePutContents($path . '.json', json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            $cacher->save($key, $config);
         }else {
            $config = array();
         }
      }
 
      return new Config($config);
   }

   /**
    * 设置全局的配置信息
    * 
    * @param string $key 配置键值 支持点语法 A.B.C
    * @param array $data
    * @return string 
    */
   public static function setGlobalConfig($key, $data)
   {
      $config = self::getGlobalConfig();
      set_config_item_by_path($config, $key, $data);
      $path = self::getGlobalConfigFilename() . '.json';
      $name = 'Application.config';
      $cacheKey = md5($name);
      $cacher = self::getCacher('');
      $cacher->delete($cacheKey);
      self::writeBackToFile($path, json_encode($config->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
   }

   /**
    * 设置framework的配置信息
    * 
    * @param string $name
    * @param string $key 
    * @param mixed $data
    * @param int $type 框架是内置的还是第三方的

    */
   public static function setFrameworkConfig($name, $key, $data, $type = self::C_TYPE_FRAMEWORK_SYS)
   {
      $config = self::getFrameworkConfig($name, $type);
      $filename = self::getFrameworkConfigFilename($name, $type);
      $path = $filename[1] . '.json';
      $cacheName = $name . '.config';
      $cacheKey = md5($cacheName);
      $cacher = self::getCacher($filename[2]);
      $cacher->delete($cacheKey);
      set_config_item_by_path($config, $key, $data);
      self::writeBackToFile($path, json_encode($config->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
   }

   /**
    * 写入模块的配置信息
    * 
    * @param string $name 模块的名称
    * @param string $key 写入的键值数据
    * @param mixed $data 更改的数据
    */
   public static function setModuleConfig($name, $key, $data)
   {
      $config = self::getModuleConfig($name);
      $path = self::getModuleConfigFilename($name) . '.json';
      $cacheName = $name . '.config';
      $cacheKey = md5($cacheName);
      $cacher = self::getCacher('Module');
      $cacher->delete($cacheKey);
      $map = get_config_item_by_path($config, $key);
      foreach ($data as $key => $value) {
         $map[$key] = $value;
      }
      self::writeBackToFile($path, json_encode($config->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
   }

   /**
    * 一次写入一个模型所有的配置信息， 用的时候要注意，这个会无条件覆盖原有的配置信息
    * 
    * @param string $name 模块的名称
    * @param array $config 所有配置项的数据
    */
   public static function setModuleConfigs($name, array $config)
   {
      $config = self::getModuleConfig($name);
      $path = self::getModuleConfigFilename($name) . '.json';
      $cacheName = $name . '.config';
      $cacheKey = md5($cacheName);
      $cacher = self::getCacher('Module');
      $cacher->delete($cacheKey);
      self::writeBackToFile($path, json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
   }

   /**
    * 写入APP的配置信息
    * 
    * @param string $module APP模块的名称
    * @param string $name APP的名称
    * @param string $key 写入配置键值
    * @param mixed $data 写入配置键下数据
    */
   public static function setAppConfig($module, $name, $key, $data)
   {
      $config = self::getAppConfig($module, $name)->toArray();
      $path = self::getAppConfigFilename($module, $name) . '.json';
      $cacheName = $name.'.config';
      $cacheKey = md5($cacheName);
      $cacher = self::getCacher('App' . DS . $module . DS . $name);
      $cacher->delete($cacheKey);
      ArrayUtils::set($config, $key, $data);
      self::writeBackToFile($path, json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
   }

   /**
    * 一次性存储一个APP配置的配置数据, 用的时候要注意，这个会无条件覆盖原有的配置信息
    * 
    * @param string $module APP模块的名称
    * @param string $name APP的名称
    * @param array $config 配置信息
    */
   public static function setAppConfigs($module, $name, array $config)
   {
      $config = self::getAppConfig($module, $name)->toArray();
      $path = self::getAppConfigFilename($module, $name) . '.json';
      $cacheName = $name.'.config';
      $cacheKey = md5($cacheName);
      $cacher = self::getCacher('App' . DS . $module . DS . $name);
      $cacher->delete($cacheKey);
      self::writeBackToFile($path, json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
   }

   /**
    * 获取全局信息配置文件名称
    * 
    * @return string
    */
   protected static function getGlobalConfigFilename()
   {
      return CNTY_CFG_DIR . DS . 'Application.config';
   }

   /**
    * 获取框架的配置文件名称
    * 
    * @param string $name Framework的
    * @param string $type
    * @return array
    */
   protected static function getFrameworkConfigFilename($name, $type)
   {
      //获取特定的路径
      if ($type == self::C_TYPE_FRAMEWORK_SYS) {
         $key = 'Sys\\' . $name;
         $path = CNTY_CFG_DIR . DS . 'Framework';
         $dir = 'Framework';
      } elseif ($type == self::C_TYPE_FRAMEWORK_VENDER) {
         $key = 'Vender\\' . $name;
         $path = CNTY_CFG_DIR . DS . 'Vender';
         $dir = 'Vender';
      } else {
         throw_exception(new Exception(
                 StdErrorType::msg('E_FRAMEWORK_TYPE_NOT_SUPPORTED', $type), StdErrorType::code('E_FRAMEWORK_TYPE_NOT_SUPPORTED')), \Cntysoft\STD_EXCEPTION_CONTEXT);
      }
      return array($key, $path . DS . $name . '.config', $dir); //后缀暂时硬编码
   }

   /**
    * 获取模型的配置信息文件名称
    * 
    * @param string $name
    * @return string
    */
   protected static function getModuleConfigFilename($name)
   {
      return CNTY_CFG_DIR . DS . 'Module' . DS . $name . '.config';
   }

   /**
    * 获取APP的配置文件的名称
    * 
    * @param string $module APP模块的名称
    * @param string $name App的名称
    */
   protected static function getAppConfigFilename($module, $name)
   {
      $configPath = implode(DS, array(
         CNTY_CFG_DIR,
         'App',
         $module,
         $name
      ));
      return $configPath . '.config'; //暂时硬编码
   }

   /**
    * 将保存之后的数据存入数据库里面
    * 
    * @param string $filename 写入配置信息的文件
    * @param string $data 写入的数组
    */
   protected static function writeBackToFile($filename, $data)
   {
      //不存在就创建, 同时会创建文件夹
      if (!file_exists($filename)) {
         $dir = dirname($filename);
         if (!file_exists($dir)) {
            Filesystem::createDir($dir, 0750, true);
         }
      }
      //文可以保证存在
//      $data = "<?php\nreturn " . var_export($data, true) . ';';
      Filesystem::filePutContents($filename, $data);
   }
   
   protected static function getCacher($dir)
   {
      $key = md5($dir);
      if(!count(self::$cacher) || !array_key_exists($key, self::$cacher)){
         self::$cacher[$key] = make_cache_object('Config'. DS . $dir, 18000);
      }
      
      return self::$cacher[$key];
   }
}
