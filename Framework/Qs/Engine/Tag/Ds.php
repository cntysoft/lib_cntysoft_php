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
use Cntysoft\Framework\Qs\ErrorType;
use Cntysoft\Kernel;
class Ds extends AbstractTag
{
   CONST DS_SCRIPT_FILENAME = 'Script.php';

   /**
    * 全局数据源
    *
    * @var array $dsPool
    */
   protected static $dsPool = array();

   /**
    * @param string $id 数据源ID
    * @param string $group 数据源分组
    * @paraM  array &$params 数据源参数
    */
   public function register($id, $group, array &$params = array())
   {
      try {
         $params['id'] = $id;
         $params['group'] = $group;
         $this->invokeParams = &$params;
         $meta = $this->prepareParse(View::TAG_DS, $params);
         $tagDir = $this->getTagDir(View::TAG_DS, $id);
         $script = Kernel\real_path($tagDir.DS.self::DS_SCRIPT_FILENAME);
         if (!file_exists($script)) {
            $errorType = ErrorType::getInstance();
            throw new Exception(
               $errorType->msg('E_TAG_SCRIPT_FILE_NOT_EXIST', $this->getTagSignature()), $errorType->code('E_TAG_SCRIPT_FILE_NOT_EXIST'));
         }
         include_once $script;
         $tagResolver = View::getTagResolver();
         $cls = $tagResolver->getTagDsBaseNs().'\\'.$meta['namespace'].'\\'.$meta['class'];
         if (!class_exists($cls)) {
            $errorType = ErrorType::getInstance();
            throw new Exception($errorType->msg('E_TAG_CLS_NOT_EXIST', $cls), $errorType->code('E_TAG_CLS_NOT_EXIST'));
         }
         $ds = new $cls($params, $this->engine);
         self::$dsPool[$group] = $ds->load();
      } catch (\Exception $ex) {
            $this->renderError($ex);
      }
   }
   /**
    * 获取数据源池
    *
    * @return array
    */
   public static function getDsPool()
   {
      return self::$dsPool;
   }
   /**
    * @inheritdoc
    */
   public function getTagSignature()
   {
      return 'Qs::Ds('.var_export($this->invokeParams, true).')';
   }

}