<?php
/**
 * Cntysoft Cloud Software Team
 * 
 * @author SOFTBOY <cntysoft@163.com>
 * @copyright  Copyright (c) 2010-2011 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license    http://www.cntysoft.com/license/new-bsd     New BSD License
 */
namespace Cntysoft\Phalcon\DI;
use Cntysoft\Kernel;
use Cntysoft\Kernel\StdErrorType;
/**
 * 系统插件管理器
 */
abstract class AbstractPluginManager
{
    /**
     * 插件池
     * 
     * @protected array $pluginPool
     */
    protected $pluginPool = array();

    /**
     * @param string $cls 插件的类名
     * @return object
     */
    public function get($cls)
    {
        if (!isset($this->pluginPool[$cls])) {
            if (!class_exists($cls)) {
                Kernel\throw_exception(new Exception(
                        StdErrorType::msg('E_CLASS_NOT_EXIST', $cls), StdErrorType::code('E_CLASS_NOT_EXIST')), \Cntysoft\STD_EXCEPTION_CONTEXT);
            }
            $instance = new $cls();
            $this->validatePlugin($instance);
            $this->pluginPool[$cls] = $instance;
            return $instance;
        }
        return $this->pluginPool[$cls];
    }

    /**
     * 验证插件对是是否是想要的
     */
    abstract public function validatePlugin($plugin);
}