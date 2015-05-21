<?php
/**
 * Cntysoft Cloud Software Team
 * 
 * @author SOFTBOY <cntysoft@163.com>
 * @copyright  Copyright (c) 2010-2011 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license    http://www.cntysoft.com/license/new-bsd     New BSD License
 */
namespace Cntysoft\VenderFrameworkProcess\Scripts\Qiniu;
use Cntysoft\VenderFrameworkProcess\AbstractInitScript;
class InitScript extends AbstractInitScript
{
    /**
     * @inheritDoc
     */
    protected $name = 'Qiniu';
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->setupAutoloader();
    }
    /**
     * @inheritDoc
     */
    protected function setupAutoloader()
    {
        $this->autoLoader->registerNamespaces(array(
           'Qiniu' => CNTY_VENDER_DIR.DS.$this->name
        ), true)->register();
       include CNTY_VENDER_DIR . DS . $this->name . DS . 'functions.php';
    }
}