<?php
/**
 * Cntysoft Cloud Software Team
 * 
 * @author SOFTBOY <cntysoft@163.com>
 * @copyright  Copyright (c) 2010-2011 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license    http://www.cntysoft.com/license/new-bsd     New BSD License
 */
namespace Cntysoft\VenderFrameworkProcess\Scripts\VsImage;
use Cntysoft\VenderFrameworkProcess\AbstractInitScript;
class InitScript extends AbstractInitScript
{
    /**
     * @inheritDoc
     */
    protected $name = 'VsImage';
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
           'PHPImageWorkshop' => CNTY_VENDER_DIR.DS.$this->name
        ), true)->register();
    }
}