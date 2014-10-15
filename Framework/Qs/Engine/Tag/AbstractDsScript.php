<?php
/**
 * Cntysoft Cloud Software Team
 * 
 * @author SOFTBOY <cntysoft@163.com>
 * @copyright  Copyright (c) 2010-2011 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license    http://www.cntysoft.com/license/new-bsd     New BSD License
 */
namespace Cntysoft\Framework\Qs\Engine\Tag;
/**
 * 抽象的label脚本
 */
abstract class AbstractDsScript extends AbstractScript
{
    /**
     * 加载数据源数据
     * 
     * @return array
     */
    abstract public function load();
}