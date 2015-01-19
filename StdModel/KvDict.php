<?php
/**
 * Cntysoft Cloud Software Team
 *
 * @author changwang <chenyongwang1104@163.com>
 * @copyright  Copyright (c) 2010-2011 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license    http://www.cntysoft.com/license/new-bsd     New BSD License
 */
namespace Cntysoft\StdModel;
use Cntysoft\Phalcon\Mvc\Model as BaseModel;
/**
 * 系统数据字典
 */
class KvDict extends BaseModel
{
   private $key;
   private $name;
   private $items;

   public function getSource()
   {
      return 'sys_kvdict';
   }

   public function getKey()
   {
      return $this->key;
   }

   public function getName()
   {
      return $this->name;
   }
   /**
    * @return array
    */
   public function getItems()
   {
      return unserialize($this->items);
   }
   /**
    * @param string $key
    * @return \Cntysoft\StdModel\KvDict
    */
   public function setKey($key)
   {
      $this->key = $key;
      return $this;
   }
   /**
    * @param string $name
    * @return \Cntysoft\StdModel\KvDict
    */
   public function setName($name)
   {
      $this->name = $name;
      return $this;
   }
   /**
    *
    * @param string $items
    * @return \Cntysoft\StdModel\KvDict
    */
   public function setItems(array $items)
   {
      $this->items = serialize($items);
      return $this;
   }
}