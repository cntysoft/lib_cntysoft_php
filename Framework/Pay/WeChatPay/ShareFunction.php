<?php
/**
 * Cntysoft Cloud Software Team
 * 
 * @author ZhiHui <liuyan2526@qq.com>
 * @copyright Copyright (c) 2010-2015 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license http://www.cntysoft.com/license/new-bsd     New BSD License
*/
namespace Cntysoft\Framework\Pay\WeChatPay;
/**
 * 本类是一些公用的方法
 */
class ShareFunction
{
   /**
    * 创建随机的字符串
    * 
    * @return string
    */
   public static function createRandStr()
   {
      $randNum = mt_rand();
      $randStr = md5($randNum);
      return $randStr;
   }
   /**
    * 创建安全验证sign
    * 
    * @param array $params 
    * @return string
    */
   public static function createSign(array $params)
   {
      $sign = '';
      ksort($params);
      foreach ($params as $key => $value) {
         $sign .= '&'.$key.'='.$value;
      }
      $sign = substr($sign, 1);
      $sign .= '&key='.Constant::API_KEY;
      $sign = strtoupper(md5($sign));
      return $sign;
   }
   /**
    * 数组转换xml
    * @param array $values
    * @return string
    */
   public static function arrayToXml(array $values)
   {
      if (!is_array($values) || count($values) < 0) {
         $errorType = ErrorType::getInstance();
         Kernel\throw_exception(new Exception(
                 $errorType->msg('E_PAY_WECHAT_DATA_ARRAY_ERROR'), $errorType->code('E_PAY_WECHAT_DATA_ARRAY_ERROR')
                 ), $errorType);
      }

      $xml = "<xml>";
      foreach ($values as $key => $val) {
         if (is_numeric($val)) {
            $xml.="<" . $key . ">" . $val . "</" . $key . ">";
         } else {
            $xml.="<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
         }
      }
      $xml.="</xml>";
      return $xml;
   }
   /**
    * xml转换数组
    * 
    * @param string $xml
    * @return array
    */
   public static function arrayFromXml($xml)
   {
      if (!$xml) {
         $errorType = ErrorType::getInstance();
         Kernel\throw_exception(new Exception(
                 $errorType->msg('E_PAY_WECHAT_DATA_XML_ERROR'), $errorType->code('E_PAY_WECHAT_DATA_XML_ERROR')
                 ), $errorType);
      }
      libxml_disable_entity_loader(true);
      $values = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
      
      return $values;
   }

   
}