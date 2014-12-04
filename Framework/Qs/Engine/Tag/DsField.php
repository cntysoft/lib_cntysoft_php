<?php
/**
 * Cntysoft Cloud Software Team
 *
 * @author SOFTBOY <cntysoft@163.com>
 * @copyright  Copyright (c) 2010-2011 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license    http://www.cntysoft.com/license/new-bsd     New BSD License
 */
namespace Cntysoft\Framework\Qs\Engine\Tag;
/**
 * 数据源字段获取标签
 */
class DsField extends AbstractTag
{
   /**
    * 获取指定的数据源字段的值
    *
    * @param string $group 与之关联的数据源
    * @param string $key
    * @return string | null
    */
   public function getFieldValue($group, $key, $eval = false)
   {
      try {
         $this->invokeParams = array(
            'group' => $group,
            'key'   => $key
         );
         $pool = Ds::getDsPool();
         if (!isset($pool[$group])) {
            throw new Exception('datasource '.$group.' is not exist');
         }
         if(!array_key_exists($key, $pool[$group])){
            throw new Exception('datasource field '.$key. ' is not exist');
         }
         if($eval) {
            return $pool[$group][$key];
         }
         echo $pool[$group][$key];
      } catch (\Exception $ex) {
         $this->renderError($ex->getMessage());
      }
   }

   /**
    * @inheritdoc
    */
   protected function getTagSignature()
   {
      return 'Qs::DsField('.var_export($this->invokeParams, true).')';
   }

}
