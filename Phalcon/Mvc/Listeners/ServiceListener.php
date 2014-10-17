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
use Cntysoft\Kernel\ConfigProxy;
/**
 * 系统一些小东西初始化监听类
 */
class ServiceListener implements ListenerAggregateInterface
{

   /**
    * @inheritdoc
    */
   public function attach(\Phalcon\Events\ManagerInterface $events)
   {
      $events->attach('application:boot', $this);
   }

   /**
    * 注册一些在引导时候进行的操作
    *
    * @param \Phalcon\Events\Event $event
    * @param \Cntysoft\Phalcon\Mvc\Application $application
    */
   public function boot($event, $application)
   {
      $di = $application->getDI();
      $services = $this->getServiceNames();
      foreach ($services as $service) {
         $method = 'setup'.$service;
         $this->{$method}($di);
      }
   }

   /**
    * @param \Phalcon\DI $di
    */
   protected function setupAppCaller($di)
   {
      $di->setShared('AppCaller', function() {

         return new \Cntysoft\Kernel\App\Caller();
      });
   }

   /**
    * @param \Phalcon\DI $di
    */
   protected function setupSessionManager($di)
   {
      $di->setShared('SessionManager', function() {
         $config = ConfigProxy::getGlobalConfig();
         $config = $config->session->toArray();
         $config += array(
            'name'            => 'CNTYSOFT_S_NAME', /* 系统会随机生成几个混淆的名称 */
            'cookie_httponly' => true
         );
         $option = new \Zend\Session\Config\StandardConfig();
         $option->setOptions($config);
         $manager = new  \Zend\Session\SessionManager($option);
         return new \Zend\Session\Container(\Cntysoft\SESSION_NS, $manager);
      });
   }

   /**
    * @param \Phalcon\DI $di
    */
   protected function setupCookieManager($di)
   {
      $di->setShared('CookieManager', function() {
         $config = ConfigProxy::getGlobalConfig();
         return new \Cntysoft\Kernel\CookieManager($config->cookie->toArray());
      });
   }
   /**
    * @return array
    */
   protected function getServiceNames()
   {
      return array(
         'SessionManager',
         'CookieManager',
         'AppCaller'
      );
   }

}