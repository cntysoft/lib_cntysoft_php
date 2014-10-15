<?php
/**
 * Cntysoft Cloud Software Team
 * 
 * @author SOFTBOY <cntysoft@163.com>
 * @copyright  Copyright (c) 2010-2011 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license    http://www.cntysoft.com/license/new-bsd     New BSD License
 */
namespace Cntysoft\VenderFrameworkProcess;
use Cntysoft\Kernel\ConfigProxy;
use Cntysoft\Kernel;
use Cntysoft\Kernel\StdErrorType;
/**
 * 处理一些配置项的获取
 */
abstract class AbstractInitScript implements VenderFrameworkInitInterface
{
   /**
    * 框架的名称
    * 
    * @var string $name
    */
   protected $name = '';
   
   /**
    * @var \Phalcon\DI $di
    */
   protected $di = null;
   /**
    * @var \Phalcon\Loader  $autoLoader
    */
   protected $autoLoader = null;
   /**
    * 构造函数
    * 
    * @throws \Cntysoft\VenderFrameworkProcess\Exception
    */
   public function __construct()
   {
      if('' == trim($this->name)){
          Kernel\throw_exception(new Exception(
            StdErrorType::msg('E_VENDER_FRAMEWORK_NAME_EMPTY'),
            StdErrorType::code('E_VENDER_FRAMEWORK_NAME_EMPTY')
         ), \Cntysoft\STD_EXCEPTION_CONTEXT);
      }
      $this->di = Kernel\get_global_di();
      $this->autoLoader = $this->di->getShared('loader');
   }
   
   /**
    * 获取框架配置信息
    * 
    * @return array
    */
   protected function getFrameworkConfig()
   {
      return ConfigProxy::getFrameworkConfig(
         $this->name, 
         ConfigProxy::C_TYPE_FRAMEWORK_VENDER
      );
   }
}