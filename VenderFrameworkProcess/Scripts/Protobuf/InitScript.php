<?php
/**
 * Cntysoft Cloud Software Team
 *
 * @author SOFTBOY <cntysoft@163.com>
 * @copyright  Copyright (c) 2010-2011 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license    http://www.cntysoft.com/license/new-bsd     New BSD License
 */
namespace Cntysoft\VenderFrameworkProcess\Scripts\Protobuf;
use Cntysoft\VenderFrameworkProcess\AbstractInitScript;
class InitScript extends AbstractInitScript
{
   /**
    * @inheritDoc
    */
   protected $name = 'Protobuf';
   /**
    * @inheritdoc
    */
   public function init()
   {
      $this->setupAutoloader();
      $this->setupProtobuf();
   }
   /**
    * @inheritDoc
    */
   protected function setupAutoloader()
   {
      $this->autoLoader->registerNamespaces(array(
         'DrSlump' => CNTY_VENDER_DIR.DS.$this->name.DS.'DrSlump'
      ), true)->register();
   }

   protected function setupProtobuf()
   {
      \DrSlump\Protobuf::setDefaultCodec(new \DrSlump\Protobuf\Codec\Json());
      \DrSlump\Protobuf::registerCodec('Binary', new \DrSlump\Protobuf\Codec\Binary());
   }
}