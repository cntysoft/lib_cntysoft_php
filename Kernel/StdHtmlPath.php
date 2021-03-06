<?php
/**
 * Cntysoft Cloud Software Team
 * 
 * @author SOFTBOY <cntysoft@163.com>
 * @copyright  Copyright (c) 2010-2011 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license    http://www.cntysoft.com/license/new-bsd     New BSD License
 */
namespace Cntysoft\Kernel;
use Cntysoft\Kernel;
/**
 * 标准的Html一些路径，用于加载Css Js Image文件等等
 *
 * @category   Cntysoft
 * @package    Cntysoft\Kernel
 */
abstract class StdHtmlPath
{
   /**
    * 获取标准的Js路径
    *
    * @return string
    */
   public static function getJsPath()
   {
      return '/JsLibrary';
   }

   /**
    * 获取皮肤路径
    *
    * @return string
    */
   public static function getSkinPath()
   {
      return '/Statics/Skins';
   }

   /**
    * 获取标准的模板路径
    *
    * @return string
    */
   public static function getTemplatesPath()
   {
      return '/Statics/Templates';
   }

   /**
    * 获取数据路径
    *
    * @return string
    */
   public static function getDataPath()
   {
      return '/Data';
   }

   /**
    * 获取标签库路径
    *
    * @return string
    */
   public static function getTagLibPath()
   {
      return '/TagLibrary';
   }

   /**
    * 获取模块的路径
    *
    * @return string
    */
   public static function getModulePath()
   {
      return '/Modules';
   }

   /**
    * 获取系统文件上传路径
    *
    * @return string
    */
   public static function getUploadFilesPath()
   {
      return self::getDataPath().DS.'UploadFiles';
   }
}