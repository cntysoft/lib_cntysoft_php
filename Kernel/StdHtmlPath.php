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
 * 标准的Html一些路径，用于加载Css Js Image文件等等
 *
 * @category   Cntysoft
 * @package    Cntysoft\Kernel
 */
abstract class StdHtmlPath
{
   /**
    * 获取Ui路径
    *
    * @return string
    */
   public static function getUiPath()
   {
      return '/Ui';
   }

   /**
    * 获取Mobile标准的Js路径
    *
    * @return string
    */
   public static function getMobileJsPath()
   {
      return self::getSkinPath().'/Mobile/Js';
   }
    /**
    * 获取Pc标准的Js路径
    *
    * @return string
    */
   public static function getPcJsPath()
   {
      return self::getSkinPath().'/Pc/Js';
   }

   /**
    * 前台共用Js路径
    *
    * @return string
    */
   public static function getFrontJsLibPath()
   {
      return self::getUiPath() . '/JsLib';
   }

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
    * 获取图片路径
    *
    * @return string
    */
   public static function getPcImagePath()
   {
      return self::getSkinPath().'/Pc/Images';
   }
   
   /**
    * 获取图片路径
    *
    * @return string
    */
   public static function getMobileImagePath()
   {
      return self::getSkinPath().'/Mobile/Images';
   }
   /**
    * 获取皮肤路径
    *
    * @return string
    */
   public static function getSkinPath()
   {
      return '/Ui/Skins/'. \Cntysoft\Kernel\get_site_id();
   }

   /**
    * 获取标准的模板路径
    *
    * @return string
    */
   public static function getTemplatesPath()
   {
      return '/Ui/Templates/'. \Cntysoft\Kernel\get_site_id();
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
    * 获取上传文件路径
    *
    * @return string
    */
   public static function getUploadFilePath()
   {
      $basePath = self::getDataPath();
      return $basePath . '/UploadFiles';
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
}