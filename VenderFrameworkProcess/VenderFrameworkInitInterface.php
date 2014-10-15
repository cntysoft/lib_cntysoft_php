<?php
/**
 * Cntysoft Cloud Software Team
 * 
 * @author SOFTBOY <cntysoft@163.com>
 * @copyright  Copyright (c) 2010-2011 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license    http://www.cntysoft.com/license/new-bsd     New BSD License
 */
namespace Cntysoft\VenderFrameworkProcess;
/**
 * 第三方框架初始化接口,第三方框架整合进我们的系统必须实现这个接口
 */
interface VenderFrameworkInitInterface
{  
   /**
    * 初始化第三方框架
    */
   public function init();
}