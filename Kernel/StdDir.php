<?php
/**
 * Cntysoft Cloud Software Team
 *
 * @author SOFTBOY <cntysoft@163.com>
 * @copyright  Copyright (c) 2010-2011 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license    http://www.cntysoft.com/license/new-bsd     New BSD License
 */
namespace Cntysoft\Kernel;

/**
 * 系统文件夹路径，这个是后端系统使用，物理的文件系统路径
 */
abstract class StdDir
{

   /**
    * 获取系统缓存路径
    *
    * @return string
    */
   public static function getCacheDir()
   {
      return CNTY_CACHE_DIR;
   }

   /**
    * 获取系统数据路径
    *
    * @return string
    */
   public static function getDataDir()
   {
      return CNTY_DATA_DIR;
   }

   /**
    * 获取系统日志目录
    *
    * @return string
    */
   public static function geLogDir()
   {
      return CNTY_DATA_DIR.DS.'Log';
   }

   /**
    * 获取Ui路径
    *
    * @return string
    */
   public static function getUiDir()
   {
      return CNTY_UI_DIR;
   }

   /**
    * 获取系统图片目录
    *
    * @return string
    */
   public static function getImagesDir()
   {
      return CNTY_IMAGE_DIR;
   }

   /**
    * 获取模板路径
    *
    * @return string
    */
   public static function getTemplatesDir()
   {
      return CNTY_TEMPLATE_DIR;
   }

   /**
    * 获取皮肤路径
    *
    * @return string
    */
   public static function getSkinDir()
   {
      return CNTY_SKIN_DIR;
   }

   /**
    * 获取pc版模板路径
    *
    * @return string
    */
   public static function getPcTemplatesDir()
   {
      return CNTY_TEMPLATE_DIR.DS.'Pc';
   }

   /**
    * 获取mobile版模板路径
    *
    * @return string
    */
   public static function getMobileTemplatesDir()
   {
      return CNTY_TEMPLATE_DIR.DS.'Mobile';
   }

   /**
    * 获取标签目录
    *
    * @return string
    */
   public static function getTagLibDir()
   {
      return CNTY_TAG_DIR;
   }

   /**
    * 获取系统框架路径
    *
    * @return string
    */
   public static function getSysLibDir()
   {
      return CNTY_SYS_LIB_DIR;
   }

   /**
    * 获取配置路径
    *
    * @return string
    */
   public static function getConfDir()
   {
      return CNTY_CFG_DIR;
   }

   /**
    * 获取系统模块路经
    *
    * @return string
    */
   public static function getModuleDir()
   {
      return CNTY_MODULE_DIR;
   }

   /**
    * 获取应用程序路径
    *
    * @return string
    */
   public static function getAppDir()
   {
      return CNTY_APP_DIR;
   }

   /**
    * 获取系统临时文件路径
    *
    * @return string
    */
   public static function getTmpDir()
   {
      return CNTY_DATA_DIR.DS.'Tmp';
   }

   /**
    * 获取Framework的数据文件夹
    *
    * @param string $name
    *           framework的名称
    * @return string
    */
   public static function getFrameworkDataDir($name)
   {
      return self::getDataDir().DS.'Framework'.DS.$name;
   }

   /**
    * 获取系统静态生成文件存放目录
    *
    * @return string
    */
   public static function getHtmlDir()
   {
      return CNTY_ROOT_DIR.DS.'Html';
   }

   /**
    * 获取系统App根目录
    *
    * @param string $module
    * @param string $name
    * @return string
    */
   public static function getAppRootDir($module, $name)
   {
      return CNTY_APP_ROOT_DIR.DS.$module.DS.$name;
   }

   /**
    * 获取APP数据目录
    *
    * @param
    *           string$module
    * @param string $name
    * @return string
    */
   public static function getAppDataDir($module, $name)
   {
      return self::getAppRootDir($module, $name).DS.'Data';
   }
}