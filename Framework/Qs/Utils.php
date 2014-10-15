<?php
/**
 * Cntysoft Cloud Software Team
 *
 * @author SOFTBOY <cntysoft@163.com>
 * @copyright  Copyright (c) 2010-2011 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license    http://www.cntysoft.com/license/new-bsd     New BSD License
 */
namespace Cntysoft\Framework\Qs;
use Cntysoft\Kernel\StdDir;
use Cntysoft\Kernel\ConfigProxy;
/**
 * 定义一些模板引擎中使用的一些魔术方法
 */
final class Utils
{
   /**
    * 模板引擎配置信息
    *
    * @var array $config
    */
   protected static $config = array();

   /**
    * 判断模板执行文件是否过期
    *
    * @param string $tplFile
    * @return boolean
    */
   public static function tplCacheIsExpire($tplFile)
   {
      $tplFile = (string) $tplFile;
      /**
       * @todo
       */
      return true;
   }

   /**
    * 生成可执行的文件路径
    *
    * @param string $tplFileName
    * @return string
    */
   public static function generateTplScriptName($tplFileName)
   {
      $tplFileName = (string) $tplFileName;
      return self::getQsCachePath().DS.md5($tplFileName).'.php';
   }

   /**
    * 获取Qs模板引擎缓存路经
    *
    * @return string
    */
   public static function getQsCachePath()
   {
      $config = self::getConfig();
      if (array_key_exists('cache_path', $config)) {
         $cachePath = $config['cache_path'];
      }
      else {
         $cachePath = 'Qs';
      }
      return StdDir::getCacheDir() . DS . $cachePath;
   }

   /**
    * 获取模板引擎配置数据
    *
    * @return array
    */
   public static function getConfig()
   {
      if (self::$config == null) {
         self::$config = ConfigProxy::getFrameworkConfig('Qs');
      }
      return self::$config;
   }

   /**
    * 生成一个标准Script标签
    *
    * @param string $base
    * @param string $file
    * @return string
    */
   public static function generateCssLinkTag($base, $file, $id = '', $charset = \Cntysoft\UTF8)
   {
      if (is_array($file)) {
         $ret = '';
         foreach ($file as $item) {
            $item = $base.'/'.$item;
            $ret .= "<link id='{$id}' type='text/css' charset='{$charset}' rel='stylesheet' href='{$item}'>";
         }
         return $ret;
      }
      $file = $base.'/'.$file;
      return "<link id='{$id}' type='text/css' charset='{$charset}' rel='stylesheet' href='{$file}'>";
   }

   /**
    * 生成一个标准Js标签
    *
    * @param string $base
    * @param string $file
    * @return string
    */
   public static function generateJsScriptTag($base, $file, $charset = \Cntysoft\UTF8)
   {
      if (is_array($file)) {
         $ret = '';
         foreach ($file as $item) {
            $item = $base.'/'.$item;
            //这个ret可能有问题
            $ret .= "<script language='javascript' charset='{$charset}' type='text/javascript' src='{$item}'></script>\n";
         }
         return $ret;
      } else {
         $file = $base.'/'.$file;
         return "<script language='javascript' type='text/javascript'  charset='{$charset}' src='{$file}'></script>\n";
      }
   }

}
