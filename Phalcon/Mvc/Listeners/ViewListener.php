<?php
/**
 * Cntysoft Cloud Software Team
 *
 * @author SOFTBOY <cntysoft@163.com>
 * @copyright  Copyright (c) 2010-2011 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license    http://www.cntysoft.com/license/new-bsd     New BSD License
 */
namespace Cntysoft\Phalcon\Mvc\Listeners;
use Cntysoft\Phalcon\Events\ListenerAggregateInterface;
use Cntysoft\Framework\Qs\View;
use Cntysoft\Kernel\ConfigProxy;
use Cntysoft\Kernel;
use Cntysoft\Kernel\StdDir;
/**
 * 绑定一些处理显示的事件监听函数
 */
class ViewListener implements ListenerAggregateInterface
{
   /**
    * @inheritdoc
    */
   public function attach(\Phalcon\Events\ManagerInterface $events)
   {
      $events->attach('application:boot', $this);
   }
   /**
    * 主要是设置模板引擎
    *
    * @param \Phalcon\Events\Event $event
    * @param \Cntysoft\Phalcon\Mvc\Application $application
    */
   public function boot($event, $application)
   {
      $di = $application->getDI();
      $view = new View();
      $qsCfg = ConfigProxy::getFrameworkConfig('Qs');
      //初始化默认的模板映射
      if(isset($qsCfg->tplMap)){
         View::setTplMap($qsCfg->tplMap->toArray());
      }
      //模板根目录移到子类中
      $view->setDI($di);
      $di->setShared('view', $view);
   }
}