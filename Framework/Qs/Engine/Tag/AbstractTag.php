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
/**
 * 实现一些基本的功能
 */
abstract class AbstractTag
{
   const TAGLIB_BASE_NS = '\\TagLibrary';
   const META_FILENAME = 'Definition.php';

   const D_KEY_ID = 'id';
   const D_KEY_CATEGORY = 'category';
   const D_KEY_CLASS = 'class';
   const D_KEY_NAMESPACE = 'namespace';
   const D_KEY_STATIC = 'static';
   const D_KEY_DESCRIPTION = 'description';

   /**
    * @var \Cntysoft\Framework\Qs\Engine\EngineInterface $engine
    */
   protected $engine = null;
   /**
    * 标签调用参数
    *
    * @var array $invokeParams
    */
   protected $invokeParams = array();

   /**
    * @param \Cntysoft\Framework\Qs\Engine\EngineInterface $engine
    */
   public function __construct($engine)
   {
      $this->engine = $engine;
   }

   /**
    * 显示解析错误信息
    */
   public function renderError($msg)
   {
      echo sprintf('<span style = "color:red">%s : %s</span>', $this->getTagSignature(), $msg);
   }

   /**
    * @param string $type 标签的种类
    * @param array $params
    * @return array  标签元信息
    */
   protected function &prepareParse($type, array &$params = array())
   {

      if (View::TAG_LABEL !== $type && View::TAG_DS !== $type) {
         return array();
      }
      $tagDir = Kernel\real_path($this->getTagDir($type, $params['id']));
      if (!file_exists($tagDir)) {
         $errorType = ErrorType::getInstance();
         throw new Exception($errorType->msg('E_TAG_NOT_EXIST', $params['id']), $errorType->code('E_TAG_NOT_EXIST'));
      }
      $metaFilename = $tagDir.DS.self::META_FILENAME;
      if (!file_exists($metaFilename)) {
         $errorType = ErrorType::getInstance();
         throw new Exception($errorType->msg('E_TAG_META_NOT_EXIST'), $errorType->code('E_TAG_META_NOT_EXIST'));
      }

      $meta = include $metaFilename;
      if (!is_array($meta)) {
         $errorType = ErrorType::getInstance();
         throw new Exception($errorType->msg('E_TAG_META_ERROR', ' meta info must be php array'), $errorType->code('E_TAG_META_ERROR'));
      }
      $this->checkTagMeta($type, $meta, $params);
      return $meta;
   }

   /**
    * 获取标签的签名
    *
    * @return string
    */
   abstract protected function getTagSignature();
   /**
    * 获取标签的根目录
    *
    * @param string $type 标签类型
    * @return string
    */
   protected function getTagDir($type, $id)
   {
      $tagResolver = View::getTagResolver();
      return $tagResolver->getTagBaseDir().DS.$type.DS.$id;
   }

   /**
    * 检查标签元信息， 检查标签调用是否具有必要参数
    *
    * @param array $meta 标签元信息
    * @param array $params 标签调用参数
    */
   /**
    * 检查label标签的元信息是否合法
    *
    * @param string $type 标签的类型
    * @param array &$meta
    * @param array &$params
    */
   protected function checkTagMeta($type, array &$meta, array &$params)
   {
      $requires = array(
         'id',
         'category'
      );
      $meta += array(
         'static'      => false,
         'evalable'    => false,
         'description' => ''
      );
      if (View::TAG_LABEL == $type) {
         if ($meta['static']) {
            $meta['evalable'] = false; //静态标签不能求值
         } else {
            $requires[] = 'namespace';
            $requires[] = 'class';
         }
      } else if (View::TAG_DS == $type) {
         $requires[] = 'namespace';
         $requires[] = 'class';
      }

      $leak = array();
      Kernel\array_has_requires($meta, $requires, $leak);
      if (!empty($leak)) {
         $errorType = ErrorType::getInstance();
         throw new Exception($errorType->msg('E_TAG_META_ERROR', 'tag meta require fields : '.implode(',', $leak)));
      }

      $requires = array(
         'dataType',
         'require',
         //'default',
         'description'
      );
      //验证attributes字段
      //大结构就不检验了
      if (isset($meta['attributes'])) {

         foreach ($meta['attributes'] as $name => &$attr) {
            $attr += array(
               'default' => null
            );
            $leak = array();
            foreach ($requires as $requireField) {
               if (!isset($attr[$requireField])) {
                  $leak[] = $requireField;
               }
            }
            if (!empty($leak)) {
               $errorType = ErrorType::getInstance();
               throw new Exception($errorType->msg('E_TAG_META_ERROR', 'tag attribue '.$name.' require fields : '.implode(',', $leak)));
            }
         }
      }
      $this->checkRequireParams($meta, $params);
   }

   /**
    * 检查本次调用是否具有必要参数
    *
    * @param array &$meta
    * @param array &$params
    */
   protected function checkRequireParams(array &$meta, array &$params)
   {
      if (isset($meta['attributes'])) {

         //当attribues不为空的时候需要判断是否缺少必要参数
         $leak = array();
         foreach ($meta['attributes'] as $name => $value) {
            if (!isset($params[$name])) {
               //判断是否具有默认值
               if ($value['require']) {
                  if (isset($value['default'])) {
                     $params[$name] = $value['default'];
                     continue;
                  }
                  $leak[] = $name;
               }
            }
         }
         if (!empty($leak)) {
            $errorType = ErrorType::getInstance();
            throw new Exception($errorType->msg('E_TAG_REQUIRE_PATAM_NOT_EXIST', implode(',', $leak), $errorType->code('E_TAG_REQUIRE_PATAM_NOT_EXIST')));
         }
      }
   }

}