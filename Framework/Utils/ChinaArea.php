<?php
/**
 * Cntysoft OpenEngine
 *
 * @author SOFTBOY <cntysoft@163.com>
 * @copyright  Copyright (c) 2010-2011 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license    http://www.cntysoft.com/license/new-bsd     New BSD License
 */
namespace Cntysoft\Framework\Utils;
use Cntysoft\Stdlib\Tree;
use Cntysoft\Kernel;
class ChinaArea
{
    CONST AREA_DATA_FILE = 'ChinaAreaMap.php';
    /**
     * @var \Cntysoft\Stdlib\Tree $areaTree 生成的区域树
     */
    protected $areaTree;

    /**
     * 构造函数
     *
     * @throws \Exception
     */
    public function __construct()
    {
        $mapFile = CNTY_DATA_DIR.DS.'Framework'.DS.'Utils'.DS.'ChinaAreaMap.php';
        if(!file_exists($mapFile)){
            $errorType = ErrorType::getInstance();
            Kernel\throw_exception(new Exception(
               $errorType->msg('E_AREA_MAP_FILE_NOT_EXIST', $mapFile),
               $errorType->code('E_AREA_MAP_FILE_NOT_EXIST')), $errorType);
        }
        $nodes = include $mapFile;
        $this->areaTree = new Tree('China Area Tree');
        $this->loadNodeRecursive(0, $nodes);
    }

    /**
     * 递归加载节点
     *
     * @param int $pid
     * @param array $nodes 当前节点集合
     */
    public function loadNodeRecursive($pid, array &$nodes)
    {
        if(!empty($nodes)){
            foreach ($nodes as $code => $data){
                if(is_string($data)){
                    //添加本身就可以了
                    $this->areaTree->setNode($code, $pid, $data);
                }else if(is_array($data)){
                    //添加本身
                    $this->areaTree->setNode($code, $pid, $data['name']);
                    //添加孩子
                    if(array_key_exists('children', $data)){
                        $children = $data['children'];
                        if( is_array($children) && !empty($children)){
                            $this->loadNodeRecursive($code, $children);
                        }
                    }
                }
            }
        }
    }

    /**
     * 获取所有省份的数据
     *
     * @return array
     */
    public function getProvinces()
    {
        return $this->getChildArea(0);
    }

    /**
     * 获取指定地区的下级地区
     *
     * @param {int} $code
     * @return array
     */
    public function getChildArea($code)
    {
        $items = $this->areaTree->getChild($code);
        $ret = array();
        if(!empty($items)){
            foreach ($items as $code){
                $ret[$code] = $this->areaTree->getValue($code);
            }
        }
        return $ret;
    }

    /**
     * 获取指定的地区信息
     *
     * @param $code
     * @return array
     */
    public function getArea($code)
    {
        return $ret[$code] = $this->areaTree->getValue($code);
    }
}