<?php
/**
 * Cntysoft Cloud Software Team
 * 
 * @author SOFTBOY <cntysoft@163.com>
 * @copyright  Copyright (c) 2010-2011 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license    http://www.cntysoft.com/license/new-bsd     New BSD License
 */
namespace Cntysoft\Framework\Qs\Engine;
use Cntysoft\Framework\Qs\ErrorType;
use Zend\Stdlib\ErrorHandler;
use Cntysoft\Kernel;
/**
 * 模板引擎PHP模板解析引擎
 */
class Php implements EngineInterface
{
    /**
     * @var boolean $contextInited
     */
    protected $contextInited = false;
    /**
     * @var \Cntysoft\Framework\Qs\View $view
     */
    protected $view;

    /**
     * @param \Cntysoft\Framework\Qs\View $view
     */
    public function __construct($view)
    {
        $this->view = $view;
    }

    /**
     * @inheritdoc
     */
    public function render($tpl)
    {
        $tpl = Kernel\real_path($tpl);
        if (!file_exists($tpl)) {
           $errorType = ErrorType::getInstance();
           if(SYS_RUNTIME_MODE_PRODUCT == SYS_RUNTIME_MODE) {
              Kernel\throw_exception(
                 new Exception(
                    $errorType->msg('E_TPL_FILE_NOT_EXIST'),
                    $errorType->code('E_TPL_FILE_NOT_EXIST')),$errorType);
           }else {
              die($errorType->msg('E_TPL_FILE_NOT_EXIST', str_replace(CNTY_ROOT_DIR, '', $tpl)));
           }
        }
        $this->setupRenderContext();
        //在模板解析的时候是不允许异常上浮
        try {
            ErrorHandler::start();
            \Qs::setEngine($this);
            include $tpl;
            ErrorHandler::stop(true);
        } catch (\Exception $ex) {
           if (SYS_RUNTIME_MODE_PRODUCT == SYS_RUNTIME_MODE) {
               throw $ex;
           } else {
              echo 'unknow template parse error :  '.Kernel\filter_root_dir($ex->getMessage());
           }
        }
    }

    /**
     * 建立模板引擎执行环境
     */
    public function setupRenderContext()
    {
        if (!$this->contextInited) {
            include __DIR__.'/../Context/Qs.php';
            $loader = new \Phalcon\Loader();
            $loader->registerNamespaces(array(
               'Qs' => CNTY_SYS_LIB_DIR.DS.'Framework'.DS.'Qs'.DS.'Context'
            ))->register();
            //设置路由参数
            //设置其他一些全局的对象
            $this->contextInited = true;
        }
    }

    /**
     * @reurn \Cntysoft\Framework\Qs\View
     */
    public function getView()
    {
        return $this->view;
    }

}