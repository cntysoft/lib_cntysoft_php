<?php
/**
 * Cntysoft Cloud Software Team
 * 
 * @author SOFTBOY <cntysoft@163.com>
 * @copyright  Copyright (c) 2010-2011 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license    http://www.cntysoft.com/license/new-bsd     New BSD License
 */
namespace Cntysoft\Kernel;

use Cntysoft\Stdlib\Filesystem;
use Cntysoft\Kernel\StdDir;

/**
 * 缓存清除类
 */
final class CacheClear
{

   /**
    * 清除App缓存文件
    *
    * @param int $churchId           
    * @param string $module           
    * @param string $name           
    */
   public static function clearAppCache($churchId, $module, $name)
   {
      $dir = implode(DS, array(StdDir::getCacheDir(), $module, $name, $churchId));
      if(file_exists($dir)){
         Filesystem::deleteDirRecusive($dir);
      }
   }
}