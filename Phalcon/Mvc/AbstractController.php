<?php
/**
 * Cntysoft Cloud Software Team
 * 
 * @author SOFTBOY <cntysoft@163.com>
 * @copyright  Copyright (c) 2010-2011 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license    http://www.cntysoft.com/license/new-bsd     New BSD License
 */
namespace Cntysoft\Phalcon\Mvc;
use Cntysoft\Framework\Qs\View;
use Cntysoft\Kernel;
/**
 * 抽象控制器类
 */
class AbstractController extends \Phalcon\Mvc\Controller
{
    /**
     * 定义默认的模板寻找方法的渲染策略, 可以在控制器里面单独指定
     */
    CONST DEFAULT_RESOLVE_TYPE = View::TPL_RESOLVE_DIRECT;

    /**
     * @var  \Phalcon\Cache $cache
     */
    protected $cache = null;
    /**
     * @var \Cntysoft\Kernel\App\AppCaller $appCaller
     */
    protected $appCaller = null;

    /**
     * 设置渲染参数
     * 
     * @param array $opt
     */
    protected function setupRenderOpt(array $opt)
    {
        $opt += array(
           View::KEY_RESOLVE_TYPE => self::DEFAULT_RESOLVE_TYPE
        );
        View::setRenderOpt($opt);
    }

    /**
     * @return \Cntysoft\Kernel\App\Caller
     */
    protected function getAppCaller()
    {
        if (null == $this->appCaller) {
            $di = $this->getDI();
            $this->appCaller = $di->getShared('AppCaller');
        }
        return $this->appCaller;
    }

    /**
     * 转向指定的URL地址
     * 
     * @param string $url
     */
    protected function toUrl($url)
    {
        header("location:$url");
    }
    /**
     * @return \Phalcon\Mvc\Router
     */
    protected function getRouter()
    {
       return $this->getDI()->get('router');
    }
}