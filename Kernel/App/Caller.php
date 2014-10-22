<?php
/**
 * Cntysoft Cloud Software Team
 * 
 * @author SOFTBOY <cntysoft@163.com>
 * @copyright  Copyright (c) 2010-2011 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license    http://www.cntysoft.com/license/new-bsd     New BSD License
 */
namespace Cntysoft\Kernel\App;
use Cntysoft\Kernel;
use Cntysoft\Kernel\StdErrorType;
/**
 * 系统APP接口调用器
 */
class Caller
{
    /**
     * 应用程序对象池
     * 
     * @var array $pool 
     */
    protected $pool = array();
    /**
     * ajax handler 处理器对象容器
     * 
     * @var array $ajaxPool  
     */
    protected $ajaxPool = array();

    /**
     * 调用指定的APP程序
     * 
     * @param string $module APP的模块的名称
     * @param string $name APP的名称
     * @param string $cls      APP分系统的名称
     * @param string $method API函数的名称
     * @return array
     */
    public function call($module, $name, $cls, $method, array $params = array())
    {
        $app = $this->getAppObject($module, $name, $cls);

        if (!method_exists($app, $method)) {
            Kernel\throw_exception(new \Exception(
                    StdErrorType::msg('E_APP_API_NOT_EXIST', $module.'.'.$name, $method), StdErrorType::code('E_APP_API_NOT_EXIST')
                    ), \Cntysoft\STD_EXCEPTION_CONTEXT);
        }
        return Kernel\call_fn_with_params($app, $method, $params);
    }

    /**
     * 响应前台系统ajax调用
     * 
     * @param string $module
     * @param string $name
     * @param string $method
     * @param array $params
     */
    public function ajaxCall($module, $name, $method, array $params = array())
    {
        $parts = explode('/', $method);
        if (2 == count($parts)) {
            $cls = $parts[0];
            $method = $parts[1];
        } else {
            $cls = 'DefaultHandler';
        }
        $handler = $this->getAjaxHandler($module, $name, $cls);
        if (!method_exists($handler, $method)) {
            Kernel\throw_exception(new Exception(
                    StdErrorType::msg('E_APP_AJAX_HANDLER_NOT_EXIST', $method), StdErrorType::code('E_APP_AJAX_HANDLER_NOT_EXIST')), \Cntysoft\STD_EXCEPTION_CONTEXT);
        }
        return $handler->{$method}($params);
    }

    /**
     * 获取AjaxHandler处理器对象
     * 
     * @param string $module
     * @param string $name
     * @return \Cntysoft\Kernel\App\AbstractHandler
     */
    public function getAjaxHandler($module, $name, $cls)
    {
         $key = implode('\\', array($module, $name, $cls));
          if (!isset($this->ajaxPool[$key])) {
            $cls = '\\'.implode('\\', array('App', $module, $name, 'AjaxHandler', $cls));
            if (!class_exists($cls)) {
                Kernel\throw_exception(new Exception(
                        Kernel\StdErrorType::msg('E_APP_NOT_EXIST',$cls), Kernel\StdErrorType::code('E_APP_NOT_EXIST')
                        ), \Cntysoft\STD_EXCEPTION_CONTEXT);
            }
            $ajaxHandler = new $cls();
            if (!$ajaxHandler instanceof \Cntysoft\Kernel\App\AbstractHandler) {
                Kernel\throw_exception(new Exception(
                        Kernel\StdErrorType::msg('E_OBJECT_TYPE_ERROR', '\Cntysoft\Kernel\App\AbstractHandler'), Kernel\StdErrorType::code('E_OBJECT_TYPE_ERROR')
                        ), \Cntysoft\STD_EXCEPTION_CONTEXT);
            }
            $this->ajaxPool[$key] = $ajaxHandler;
            return $ajaxHandler;
        }
        return $this->ajaxPool[$key];
    }

    /**
     * 获取App对象
     * 
     * @param string $module
     * @param string $name
     * @return \Cntysoft\Kernel\App\AbstractApp
     */
    public function getAppObject($module, $name, $cls)
    {
        $key = implode('\\', array($module, $name, $cls));
        if (!isset($this->pool[$key])) {
            $cls = '\\App\\'.$key;
            if (!class_exists($cls)) {
                Kernel\throw_exception(new Exception(
                        Kernel\StdErrorType::msg('E_APP_NOT_EXIST', str_replace('\\', '.', $cls)), Kernel\StdErrorType::code('E_APP_NOT_EXIST')
                        ), \Cntysoft\STD_EXCEPTION_CONTEXT);
            }
            $appObj = new $cls();
            if (!$appObj instanceof \Cntysoft\Kernel\App\AbstractLib) {
                Kernel\throw_exception(new Exception(
                        Kernel\StdErrorType::msg('E_OBJECT_TYPE_ERROR', '\Cntysoft\Kernel\App\AbstractLib'), Kernel\StdErrorType::code('E_OBJECT_TYPE_ERROR')
                        ), \Cntysoft\STD_EXCEPTION_CONTEXT);
            }
            $this->pool[$key] = $appObj;
            return $appObj;
        }
        return $this->pool[$key];
    }

}