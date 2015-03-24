<?php
/**
 * Cntysoft Cloud Software Team
 *
 * @author SOFTBOY <cntysoft@163.com>
 * @copyright  Copyright (c) 2010-2011 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license    http://www.cntysoft.com/license/new-bsd     New BSD License
 */
namespace Cntysoft\Phalcon\Mvc;
use Cntysoft\Kernel;
use Cntysoft\Kernel\StdErrorType;
use Phalcon\Mvc\Model as AbstractModel;
/**
 * 主要加上一些异常相关的信息
 */
class Model extends AbstractModel
{
   /**
    * 我们在这里可以加上表的前缀
    *
    * @return string
    */
   public function getTablePrefix()
   {

   }

   /**
    * @inheritdoc
    */
   public function save($data = null, $whiteList = null)
   {
      $ret = parent::save($data, $whiteList);
      if (!$ret) {
         $this->throwFailException();
      }
      return $ret;
   }

   /**
    * @inheritdoc
    */
   public function update($data = null, $whiteList = null)
   {
      $ret = parent::update($data, $whiteList);
      if (!$ret) {
         $this->throwFailException();
      }
      return $ret;
   }

   /**
    * @inheritdoc
    */
   public function create($data = null, $whiteList = null)
   {
      $ret = parent::create($data, $whiteList);
      if (!$ret) {
         $this->throwFailException();
      }
      return $ret;
   }

   /**
    * @inheritdoc
    */
   public function delete()
   {
      $ret = parent::delete();
      if (!$ret) {
         $this->throwFailException();
      }
      return $ret;
   }

   /**
    * 获取模型必要字段集合
    *
    * @param array $skip 需要跳过的字段集合
    * @return array
    */
   public function getRequireFields(array $skip = array())
   {
      $md = $this->getModelsMetaData();
      $requires = $md->readMetaData($this);
      $requires = $requires[3];
      $ret = array();
      foreach ($requires as $require) {
         if (!in_array($require, $skip)) {
            $ret[] = $require;
         }
      }
      return $ret;
   }

   /**
    * 获取所有的字段集合
    *
    * @return array
    */
   public function getDataFields()
   {
      $md = $this->getModelsMetaData();
      return $md->getAttributes($this);
   }

   /**
    * 是否使用设置器设置值
    *
    * @param  array $data
    * @return \Cntysoft\Phalcon\Mvc\Model
    */
   public function assignBySetter($data)
   {
      foreach ($data as $key => $value) {
         $method = 'set'.ucfirst($key);
         $this->{$method}($value);
      }
      return $this;
   }
   /**
    * 清除表里面所有的信息， 重置auto increment, 这个函数很危险谨慎使用
    * 这个函数暂时只支持mysql
    */
   public static function clearAllRecords()
   {
      $modelManagers = Kernel\get_models_manager();
      $modelManagers->executeQuery(sprintf('DELETE FROM %s', get_called_class()));
   }
   /**
    * @param boolean $byGetter
    * @return array
    */
   public function toArray($byGetter = false, array $skips = array())
   {
      if (!$byGetter) {
         return parent::toArray();
      }
      $ret = array();
      //通过反射
      $refl = new \ReflectionObject($this);
      $props = $refl->getProperties();
      foreach ($props as $property) {
         $name = $property->name;
         if(in_array($name, $skips)){
            continue;
         }
         $method = 'get'.ucfirst($name);
         if (method_exists($this, $method)) {
            $ret[$name] = $this->{$method}();
         }
      }
      return $ret;
   }

   /**
    * 当操作失败的时候抛出异常
    */
   protected function throwFailException()
   {
      $msg = 'target class : '.get_class($this);
      foreach ($this->getMessages() as $message) {
         $msg .= ' '.$message;
      }
      Kernel\throw_exception(new Exception(
         StdErrorType::msg('E_DB_OPT_ERROR', $msg), StdErrorType::code('E_DB_OPT_ERROR')), \Cntysoft\STD_EXCEPTION_CONTEXT);
   }

   /**
    * 生成范围条件
    *
    * @TODO 这个方法有漏洞，当$range数组里面的值必须是int的时候就会生成出错
    * @param string $key
    * @param array $range
    * @return string
    */
   public static function generateRangeCond($key, array $range)
   {
      return sprintf("%s in ('%s')", $key, implode("', '", $range));
   }

}