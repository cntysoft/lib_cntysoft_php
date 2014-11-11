<?php
/**
 * Cntysoft Cloud Software Team
 *
 * @author SOFTBOY <cntysoft@163.com>
 * @copyright  Copyright (c) 2010-2011 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license    http://www.cntysoft.com/license/new-bsd     New BSD License
 */
namespace Cntysoft\Framework\Utils;
use Cntysoft\Kernel;
use Cntysoft\Kernel\StdErrorType;
use Cntysoft\Kernel\StdDir;
/**
 * 简单分词程序，基于Scws分词程序, 进行简单的封装
 */
class Scws
{
   /**
    * @var mixed $resource
    */
   protected $resource = null;
   public function __construct()
   {
      if(!extension_loaded('scws')){
         Kernel\throw_exception(new Exception(
            StdErrorType::msg('E_EXTENSION_NOT_LOADED', 'scws'),
            StdErrorType::code('E_EXTENSION_NOT_LOADED')), \Cntysoft\STD_EXCEPTION_CONTEXT);
      }
      $this->resource = scws_open();
      $dir = StdDir::getDataDir().DS.'Framework'.DS.'Utils'.DS.'Scws';
      scws_set_charset($this->resource, 'utf8');
      scws_set_dict($this->resource, $dir.DS.'dict.utf8.xdb');
      scws_set_rule($this->resource, $dir.DS.'rules.utf8.ini');
   }
   /**
    * 设置需要分词的文本
    *
    * @param string $text
    * @return boolean
    */
   public function sendText($text)
   {
      return scws_send_text($this->resource, $text);
   }
   /**
    * @return array
    */
   public function getResult()
   {
      return scws_get_result($this->resource);
   }
   /**
    * 返回指定的分词结果
    *
    * @param int $limit
    * @return array
    */
   public function getTops($limit)
   {
      return scws_get_tops($this->resource, $limit);
   }
   public function __destruct()
   {
      scws_close($this->resource);
   }
}