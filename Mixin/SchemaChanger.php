<?php
/**
 * Cntysoft Cloud Software Team
 *
 * @author SOFTBOY <cntysoft@163.com>
 * @copyright  Copyright (c) 2010-2011 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license    http://www.cntysoft.com/license/new-bsd     New BSD License
 */
namespace Cntysoft\Mixin;
trait SchemaChanger
{
   /**
    * @var string $schema
    */
   protected static $schema;

   /**
    * 改变当前使用的数据库
    *
    * @param string $schema
    */
   public static function changeSchema($schema)
   {
      self::$schema = $schema;
   }

}