<?php
/**
 * Cntysoft Cloud Software Team
 *
 * @author SOFTBOY <cntysoft@163.com>
 * @copyright  Copyright (c) 2010-2011 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license    http://www.cntysoft.com/license/new-bsd     New BSD License
 */
namespace Cntysoft\Stdlib;
/**
 * 一个树的简单实现, 虽然简单但是基本满足了系统的要求
 */
class Tree
{
   /**
    * 节点数据项
    *
    * @var array $_data
    */
   private $_data = array();
   /**
    * 孩子节点数据
    *
    * @var array $_child
    */
   public $_child = array(-1 => array());
   /**
    * 节点树深度数据
    *
    * @var array $_layer
    */
   private $_layer = array(-1 => -1);
   /**
    * 节点父节点相关信息
    *
    * @var array $_parent
    */
   private $_parent = array();
   /**
    * 节点遍历深度
    *
    * @var int $_travelDepth
    */
   private $_travelDepth = -1;
   /**
    * 遍历是否返回值
    *
    * @var boolean $_travelReturnValue
    */
   private $_travelReturnValue = false;

   /**
    * 构造函数
    *
    * @param  miexed $value
    */
   public function __construct($value)
   {
      $this->setNode(0, -1, $value);
   }

   /**
    * 设置一个新的节点
    *
    * @param int $id
    * @param int $parent
    * @param array $value
    * @return \Cntysoft\Stdlib\Tree
    */
   public function setNode($id, $parent, $value)
   {
      $parent = $parent ? $parent : 0;
      $this->_data[$id] = $value;
      if (empty($this->_child[$parent])) {
         $this->_child[$parent] = array();
      }
      $this->_child[$parent][] = $id;
      $this->_parent[$id] = $parent;
      if (!isset($this->_layer[$parent])) {
         $this->_layer[$id] = 0;
      } else {
         $this->_layer[$id] = $this->_layer[$parent] + 1;
      }
      return $this;
   }

   /**
    * 判断指定的节点ID是否存在
    *
    * @param int $id
    * @return array
    */
   public function isNodeExist($id)
   {
      return array_key_exists($id, $this->_data);
   }

   /**
    * 获取节点的数据
    *
    * @param int $id
    * @return array
    */
   public function getValue($id)
   {
      return $this->_data[$id];
   }

   /**
    * 将树遍历获取一个层次结构的数组
    *
    * @param string $childrenKey 存放字节点的键名称
    * @param \Closure $nodeCallback 每个数组节点生成函数， 参数是节点树引用和当前的节点ID， 一定要返回适配的节点数组
    * @return array
    */
   public function toArray($childrenKey, $nodeCallback)
   {
      $ret = array();
      $this->doToArray(-1, 0, $ret, $nodeCallback, $childrenKey);
      return $ret;
   }

   /**
    * @param int $pid 父结点ID
    * @param int $id
    * @param array $target
    * @param \Closure $callback
    * @param string $childrenKey
    */
   protected function doToArray($pid, $id, &$target, &$callback, $childrenKey)
   {
      $value = (array) $callback($this, $id, $pid);
      if (0 == $id) {
         $target = $value;
         $target[$childrenKey] = array();
         $childTarget = &$target[$childrenKey];
      } else {
         //添加自己
         $target[] = &$value;
      }
      $children = $this->getChild($id);
      if (!empty($children)) {
         //处理子节点
         if (0 !== $id) {
            $value[$childrenKey] = array();
            $childTarget = &$value[$childrenKey];
         }
         foreach ($children as $child) {
            $this->doToArray($id, $child, $childTarget, $callback, $childrenKey);
         }
      }
   }

   /**
    * 深度优先遍历树结构
    *
    * @param \Closure $callback
    * @param int $id
    * @param int $pid
    */
   public function travelTreeDfs($callback, $id = 0, $pid = -1)
   {
      //起点ID为0
      $children = $this->getChild($id);
      if (!empty($children)) {
         foreach ($children as $item) {
            $this->travelTreeDfs($callback, $item, $id);
         }
         $callback($id, $pid, $this);
      } else {
         $callback($id, $pid, $this);
      }
   }

   /**
    * @param int $id
    * @param int $parent
    * @param \Closure $callback
    */
   public function travelTree($callback, $id = 0, $parent = -1)
   {
      $callback($id, $parent, $this);
      $children = $this->getChild($id);
      if (!empty($children)) {
         foreach ($children as $item) {
            $this->travelTree($callback, $item, $id);
         }
      }
   }

   /**
    * 获取父节点
    *
    * @param int $id
    * @param boolean $returnValue
    * @return int | array
    */
   public function getParent($id, $returnValue = false)
   {
      $pid = $this->_parent[$id];
      return $returnValue ? $this->_data[$pid] : $pid;
   }

   /**
    * 获取孩子节点
    *
    * @param int $id
    * @param boolean $returnValue
    * @return array
    */
   public function getChild($id, $returnValue = false)
   {
      $ret = array();
      if (array_key_exists($id, $this->_child)) {
         $cid = $this->_child[$id];
         if ($returnValue) {
            foreach ($cid as $item) {
               $ret[] = $this->_data[$item];
            }
         } else {
            $ret = $cid;
         }
         return $ret;
      }
      return array();
   }

   /**
    * 获取父节点列表
    *
    * @param int $id
    * @param boolean $returnValue
    * @return array
    */
   public function getParents($id, $returnValue = false)
   {
      while ($this->_parent[$id] != -1) {
         $id = $this->_parent[$id];
         if ($returnValue) {
            $parent[] = $this->_data[$id];
         } else {
            $parent[] = $id;
         }
      }
      return $parent;
   }

   /**
    * 获取子节点列表
    *
    * @param int $id
    * @param int $depth 返回的深度 -1 表示无限制层数
    * @return boolean $returnVlaue true则返回节点数据 false只返回节点ID
    * @return array
    */
   public function getChildren($id = 0, $depth = -1, $returnValue = false)
   {
      if (-1 !== $depth) {
         $this->_travelDepth = $depth;
      }
      $this->_travelReturnValue = $returnValue;
      $child = array();
      $this->getList($child, $id, 0, $returnValue);
      $this->_travelReturnValue = false;
      $this->_travelDepth = -1;
      return $child;
   }

   /**
    * 获取指定节点的后面的兄弟
    *
    * @param int $id
    * @return int | null
    */
   public function nextSibling($id)
   {

      $pid = $this->getParent($id);
      $items = $this->getChild($pid);
      while(current($items)){
         if(current($items) == $id){
            break;
         }
         next($items);
      }
      $target = next($items);
      if(false === $target){
         return null;
      }
      return $target;
   }
   /**
    * 获取指定节点的后面的兄弟
    *
    * @param int $id
    * @return int | null
    */
   public function prevSibling($id)
   {
      $pid = $this->getParent($id);
      $items = $this->getChild($pid);
      while(current($items)){
         if(current($items) == $id){
            break;
         }
         next($items);
      }
      $target = prev($items);
      if(false === $target){
         return null;
      }
      return $target;
   }

   /**
    * 清空树内部数据结构
    *
    * @return \Cntysoft\Stdlib\Tree
    */
   public function unsetTree()
   {
      $this->_data = array();
      $this->_child = array(-1 => array());
      $this->_layer = array(-1 => -1);
      $this->_parent = array();
      return $this;
   }

   /**
    * 获取节点的层次偏移
    *
    * @param int $id
    * @param string $space
    * @return int | string
    */
   public function getLayer($id, $space = false)
   {
      return $space ? str_repeat($space, $this->_layer[$id]) : $this->_layer[$id];
   }

   /**
    * 判断一个节点是否为叶子节点
    *
    * @param int $id
    * @return boolean
    */
   public function isLeaf($id)
   {
      if (!isset($this->_data[$id])) {
         throw new Exception(sprintf(
            'node id %d is not exist', $id
         ));
      }
      //子孩子为空就为叶子节点
      return empty($this->_child[$id]);
   }

   /**
    * 获取指定节点的子树对象, 特别注意这里的ID 与 PID跟原来的树是一样的
    *
    * @param int $id 节点id
    * @return \Cntysoft\Stdlib\Tree
    */
   public function getSubTree($id)
   {
      if (!$this->isNodeExist($id)) {
         throw new Exception(sprintf(
            'node id %d is not exist', $id
         ));
      }
      $pid = $this->getParent($id);
      $retTree = null;
      $this->travelTree(function($id, $pid, $tree)use(&$retTree) {
         if ($retTree == null) {
            $retTree = new \Cntysoft\Stdlib\Tree($id, $pid, $tree->getValue($id));
         } else {
            $retTree->setNode($id, $pid, $tree->getValue($id));
         }
      }, $id, $pid);
      return $retTree;
   }

   /**
    * 获取所有节点的id集合
    *
    * @return array
    */
   public function getNodeIds()
   {
      return array_keys($this->_data);
   }

   /**
    * 获取树列表,一维的列表
    *
    * @param array &$tree
    * @param int $root
    * @param int $current
    * @return array
    */
   protected function getList(&$tree, $root = 0, $current = 0)
   {
      $current++;
      if (array_key_exists($root, $this->_child)) {
         foreach ($this->_child[$root] as $id) {
            if ($this->_travelReturnValue) {
               $tree[] = $this->_data[$id];
            } else {
               $tree[] = $id;
            }
            if (-1 == $this->_travelDepth || $current < $this->_travelDepth) {
               if (isset($this->_child[$id])) {
                  $this->getList($tree, $id, $current);
               }
            }
         }
      }
   }

}