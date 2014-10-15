<?php
/**
 * Cntysoft Cloud Software Team
 * 
 * @author SOFTBOY <cntysoft@163.com>
 * @copyright  Copyright (c) 2010-2011 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license    http://www.cntysoft.com/license/new-bsd     New BSD License
 */
namespace Cntysoft\Framework\ApiServer;
use Cntysoft\Phalcon\DI\AbstractPluginManager;
use Cntysoft\Kernel\StdErrorType;
use Cntysoft\Kernel;
/**
 * 系统管理代码管理类
 */
class ScriptBroker extends AbstractPluginManager
{
    /**
     * @inheritdoc
     */
    public function validatePlugin($plugin)
    {
        if (!$plugin instanceof AbstractScript) {
            Kernel\throw_exception(new Exception(
                    StdErrorType::msg('E_OBJECT_TYPE_ERROR', '\Cntysoft\Framework\Manager\AbstractScript'),
                    StdErrorType::code('E_OBJECT_TYPE_ERROR')), \Cntysoft\STD_EXCEPTION_CONTEXT);
        }
        return true;
    }

}