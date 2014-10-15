<?php
/**
 * Cntysoft Cloud Software Team
 * 
 * @author SOFTBOY <cntysoft@163.com>
 * @copyright  Copyright (c) 2010-2011 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license    http://www.cntysoft.com/license/new-bsd     New BSD License
 */
namespace Cntysoft\Framework\Qs\Engine;
/**
 * 模板引擎PHP模板解析引擎
 */
interface EngineInterface
{
    /**
     * @param string $tpl
     * @return string
     */
    public function render($tpl);
    /**
     * @return \Cntysoft\Framework\Qs\View
     */
    public function getView();
}