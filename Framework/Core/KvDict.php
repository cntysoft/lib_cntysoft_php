<?php
/**
 * Cntysoft OpenEngine
 * 
 * @author changwang <chenyongwang1104@163.com>
 * @copyright  Copyright (c) 2010-2011 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license    http://www.cntysoft.com/license/new-bsd     New BSD License
 */
namespace Cntysoft\Framework\Core;
use Cntysoft\Kernel;
use Cntysoft\StdModel\KvDict as KvDictModel;
/**
 * 系统一个键值数据字典
 */
class KvDict
{
    /**
     * 增加一个新的映射
     * 
     * @param string $key 编程使用的识别KEY
     * @param string $name 一个描述性的名称
     * @param array $items
     * @return \Cntysoft\Framework\Core\KvDict
     */
    public function addMap($key, $name, array $items = array())
    {
        if (KvDictModel::findFirst(array(
           'key = ?0',
           'bind' => array(
              0 => $key
           )
        ))) {
            $errorType = ErrorType::getInstance();
            Kernel\throw_exception(new Exception(
                    $errorType->msg('E_KV_KEY_EXIST', $key), $errorType->code('E_KV_KEY_EXIST')), $errorType
            );
        }
        $mode = new KvDictModel();
        $mode->assignBySetter(array(
           'key'   => $key,
           'name'  => $name,
           'items' => $items
        ));
        $mode->create();
    }

    /**
     * 修改映射的名字
     * 
     * @param string $key
     * @param string $name
     */
    public function changeMapName($key, $name)
    {
        $model = KvDictModel::findFirst(array(
           'key = ?0',
           'bind' => array(
              0 => $key
           )
        ));
        if (!$model) {
            $errorType = ErrorType::getInstance();
            Kernel\throw_exception(new Exception(
                     $errorType->msg('E_KV_KEY_NOT_EXIST', $key), $errorType->code('E_KV_KEY_NOT_EXIST'), $errorType
            ));
        }
        $model->setName($name);
        $model->update();
    }

    /**
     * 删除一个映射项
     * 
     * @param string $key 映射识别KEY
     * @return \Cntysoft\Framework\Core\KvDict
     */
    public function removeMap($key)
    {
        $model = KvDictModel::findFirst(array(
           'key = ?0',
           'bind' => array(
              0 => $key
           )
        ));
        if ($model) {
            $model->delete();
        }
        return $this;
    }

    /**
     * 获取映射数据项
     *
     * @param string $key
     * @return array
     */
    public function getMapItems($key)
    {
        $model = KvDictModel::findFirst(array(
           'key = ?0',
           'bind' => array(
              0 => $key
           )
        ));
        if (!$model) {
            $errorType = ErrorType::getInstance();
            Kernel\throw_exception(new Exception(
                     $errorType->msg('E_KV_KEY_NOT_EXIST', $key), $errorType->code('E_KV_KEY_NOT_EXIST'), $errorType
            ));
        }
        $items = $model->getItems();
        if (!is_array($items)) {
            return array();
        }
        return $items;
    }
    /**
     * 获取指定映射
     * 
     * @param type $key
     * @return type
     */
    public function getMap($key)
    {
        $model = KvDictModel::findFirst(array(
           'key = ?0',
           'bind' => array(
              0 => $key
           )
        ));
        if (!$model) {
            $errorType = ErrorType::getInstance();
            Kernel\throw_exception(new Exception(
                     $errorType->msg('E_KV_KEY_NOT_EXIST', $key), $errorType->code('E_KV_KEY_NOT_EXIST'), $errorType
            ));
        }
        return $model;
    }

    /**
     *  更改指定键值的数据项
     *
     * @param string $key 映射识别KEY
     * @param array $items 数据项
     * @return \Cntysoft\Framework\Core\KvDict
     */
    public function alterMapItems($key, array $items)
    {
        $model = KvDictModel::findFirstByKey($key);
        if (!$model) {
            $errorType = ErrorType::getInstance();
            Kernel\throw_exception(new Exception(
                    $errorType->msg('E_KV_KEY_NOT_EXIST', $key), $errorType->code('E_KV_KEY_NOT_EXIST'), $errorType
            ));
        }
        $model->setItems($items);
        $model->update();
        return $this;
    }

    /**
     * 获取所有的值
     */
    public function getAll()
    {
        return KvDictModel::find();
    }
}