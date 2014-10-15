<?php
/**
 * Cntysoft Cloud Software Team
 * 
 * @author SOFTBOY <cntysoft@163.com>
 * @copyright  Copyright (c) 2010-2011 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license    http://www.cntysoft.com/license/new-bsd     New BSD License
 */
namespace Cntysoft\Framework\Qs\Engine\Tag;
use Cntysoft\Framework\Qs\View;
use Cntysoft\Kernel;
/**
 * 抽象的label脚本
 */
abstract class AbstractScript
{
    /**
     * 路由信息
     * 
     * @var array $routeInfo 
     */
    protected $routeInfo = null;
    /**
     * 标签调用的参数
     * 
     * @var array $params
     */
    protected $invokeParams = array();
    /**
     * @var \Cntysoft\Framework\Qs\Engine\Php $engine
     */
    protected $engine = null;
    /**
     * @var \Phalcon\DI $di
     */
    protected $di = null;
    /**
     * @var \Cntysoft\Kernel\App\Caller $appCaller
     */
    protected $appCaller = null;
    /**
     * 教堂ID
     * 
     * @var int $churchId
     */
    protected $churchId = null;
    /**
     * @param array $params
     * @param \Cntysoft\Framework\Qs\Engine $engine
     */
    public function __construct(array $params, $engine)
    {
        $this->invokeParams = $params;
        $this->engine = $engine;
        $this->di = Kernel\get_global_di();
        $this->appCaller = $this->di->get('AppCaller');
        $this->churchId = Kernel\get_church_id();
    }

    /**
     * 获取标签的所有参数
     * 
     * @return array
     */
    public function getParams()
    {
        return $this->invokeParams;
    }

    /**
     * 获取标签的指定参数
     * 
     * @param string $key
     * @return null | mixed
     */
    public function getParam($key)
    {
        if (isset($this->invokeParams[$key])) {
            return $this->invokeParams[$key];
        }
        return null;
    }

    /**
     * @return array 
     */
    public function getRouteInfo()
    {
        if (!$this->routeInfo) {
            $this->routeInfo = $this->engine->getView()->getRouteInfo();
        }
        return $this->routeInfo;
    }
    /**
     * 判断是否在生成模式
     * 
     * @return boolean
     */
    protected function isBuilding()
    {
        return  $this->engine->getView()->getMode() == View::M_BUILD ? true : false;
    }
}