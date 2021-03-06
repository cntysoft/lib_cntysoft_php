<?php
/**
 * Cntysoft Cloud Software Team
 *
 * @author SOFTBOY <cntysoft@163.com>
 * @copyright  Copyright (c) 2010-2011 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license    http://www.cntysoft.com/license/new-bsd     New BSD License
 */
namespace Cntysoft\Kernel\App;
use Cntysoft\Framework\ApiServer\Exception;
use Cntysoft\Stdlib\ErrorType;
use Cntysoft\Kernel\StdErrorType;
use Cntysoft\Kernel;
use Cntysoft\Kernel\StdDir;
use Cntysoft\Stdlib\Filesystem;
use Cntysoft\Kernel\ConfigProxy;
use Cntysoft\StdModel\Config;

/**
 * APP描述对象
 */
class AppObject
{
   const APP_TYPE_BUILDIN = 0;
   const APP_TYPE_EXT = 1;
   /**
    * APP数据模型目录
    */
   const MODEL_DIR = 'Model';
   /**
    * APP数据目录
    */
   const DATA_DIR = 'Data';

   /**
    * APP权限树文件名称
    */
   const PERM_RES_FILE_NAME = 'PermResourceTree.php';

   /**
    * 请求API函数授权码文件
    */
   const AJAX_HANDLER_AUTHORIZE_CODE_FILENAME = 'AjaxApiAuthorizeCode.php';

   /**
    * 应用元信息文件名称
    */
   const APP_META_FILENAME = 'Meta.php';

   /**
    * APP异常信息定义文件名称
    */
   const ERROR_TYPE_FILE_NAME = 'ErrorType.php';

   protected $module;
   protected $name;

   /**
    * 每个引用程序都能用自己的缓存对象
    *
    * @var  \Phalcon\Cache\Backend\File $cacher
    */
   protected $cacher = null;

   /**
    * 应用程序自身的目录
    *
    * @var string $selfDir
    */
   protected $selfDir;

   /**
    * 错误类型管理程序
    *
    * @var \Cntysoft\Stdlib\ErrorType $errorType
    */
   protected $errorType = null;

   public function __construct($module, $name)
   {
      $this->module = $module;
      $this->name = $name;
   }

   public function getSelfDir()
   {
      if (null == $this->selfDir) {
         $this->selfDir = implode(DS, array(
            CNTY_APP_ROOT_DIR,
            $this->module,
            $this->name
         ));
      }
      return $this->selfDir;
   }

   /**
    * 获取全局系统文件夹目录
    *
    * @return string
    */
   public function getGlobalDataDirForSelf()
   {
      if($this->module == 'Platform'){
         $dir = CNTY_DATA_DIR.DS.$this->module.DS.$this->name;
      }else{
         $dir = CNTY_DATA_DIR.DS.$this->module.DS.$this->name.DS.Kernel\get_site_id();
      }
      if(!file_exists($dir)){
         Filesystem::createDir($dir, 0755, true);
      }
      return $dir;
   }

   /**
    * @inheritdoc
    */
   public function getName()
   {
      return $this->name;
   }

   /**
    * @inheritdoc
    */
   public function getModuleName()
   {
      return $this->module;
   }

   /**
    * 获取APP自身的数据文件夹
    *
    * @return string
    */
   public function getDataDir()
   {
      return $this->getSelfDir().DS.self::DATA_DIR;
   }


   /**
    * 获取模型文件夹
    *
    * @return string
    */
   public function getModelDir()
   {
      return $this->getSelfDir().DS.self::MODEL_DIR;
   }

   /**
    * 获取权限资源文件名
    *
    * @return string
    */
   public function getPermResFilename()
   {
      return self::getDataDir().DS.self::ACL_FILE_NAME;
   }
   /**
    * 获取系统APP键值
    *
    * @return string
    */
   public function getAppKey()
   {
      return $this->module.'.'.$this->name;
   }

   /**
    * 获取引用程序自己的缓存处理对象
    *
    * @return \Phalcon\Cache\Backend\File
    */
   public function getCacheObject()
   {
      if (null == $this->cacher) {
         try {
            $this->cacher = Kernel\make_cache_object($this->getCacheSubDir());
         } catch (\Exception $ex) {
            Kernel\throw_exception(new Exception(
               StdErrorType::msg('E_MAKE_CACHE_ERROR', $this->module.'/'.$this->name, $ex->getMessage()), StdErrorType::code('E_MAKE_CACHE_ERROR')), \Cntysoft\STD_EXCEPTION_CONTEXT);
         }
      }
      return $this->cacher;
   }

   /**
    * 获取应用元信息
    *
    * @param $moduleKey
    * @param $appKey
    * @return array
    */
   public static function getAppMetaInfo($moduleKey, $appKey)
   {
      $appDataDir = StdDir::getAppDataDir($moduleKey, $appKey);
      $filename = $appDataDir .DS.self::APP_META_FILENAME;
      if(!file_exists($filename)){
         Kernel\throw_exception(new Exception(
            StdErrorType::msg('E_APP_META_FILE_NOT_EXIST'),
            StdErrorType::code('E_APP_META_FILE_NOT_EXIST')
         ));
      }
      return include $filename;
   }

   /**
    * 获取APP的错误信息管理器
    *
    * @return \Cntysoft\Stdlib\ErrorType
    */
   public function getErrorType()
   {
      if (null == $this->errorType) {
         $map = array();
         $errorTypeFile = $this->getDataDir().DS.self::ERROR_TYPE_FILE_NAME;
         if (file_exists($errorTypeFile)) {
            $map = (array) include $errorTypeFile;
         }
         $this->errorType = new ErrorType($map);
      }
      return $this->errorType;
   }

   /**
    * 获取本App的配置信息
    *
    * @return array
    */
   public function getConfig()
   {
      return ConfigProxy::getAppConfig($this->module, $this->name);
   }
   /**
    * 获取缓存的子文件夹,可以通过继承来改变
    *
    * @return string
    */
   protected function getCacheSubDir()
   {
      return $this->module.DS.$this->name;
   }

}