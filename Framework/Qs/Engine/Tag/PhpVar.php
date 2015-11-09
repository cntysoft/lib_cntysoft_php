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
use Cntysoft\Stdlib\ArrayUtils;
class PhpVar extends AbstractTag
{
   /**
    * 获取指定的模板变量
    *
    * @param string $key
    * @return string
    */
   public function getVar($key)
   {
      try {
         $this->invokeParams = array('key' => $key);
         if(!isset(View::$renderOpt[View::KEY_TPL_VAR])){
            throw new Exception('template var '.$key.' is not exist');
         }
         $tplVars = View::$renderOpt[View::KEY_TPL_VAR];
         $value = ArrayUtils::get($tplVars, $key);
         if (is_null($value)) {
            throw new Exception('template var '.$key.' is not exist');
         }
         echo $value;
      } catch (\Exception $ex) {
            $this->renderError($ex);
      }

   }

   /**
    * @inheritdoc
    */
   protected function getTagSignature()
   {
      return 'Qs::PhpVar('.$this->invokeParams['key'].')';
   }

}