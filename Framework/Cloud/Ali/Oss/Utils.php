<?php
/**
 * Cntysoft Cloud Software Team
 * 
 * @author SOFTBOY <cntysoft@163.com>
 * @copyright  Copyright (c) 2010-2011 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license    http://www.cntysoft.com/license/new-bsd     New BSD License
 */
namespace Cntysoft\Framework\Cloud\Ali\Oss;
use Cntysoft\Kernel;
use Cntysoft\Stdlib\XML2Array;
use Cntysoft\Framework\Cloud\Ali\Oss\Constant as OSS_CONST;
use Zend\Http\Response;
/**
 * OSS工具类
 */
class Utils
{
   /**
    * @var array $successCodes 操作成功代码
    */
   protected static $successCodes = array(200, 201, 204, 206);
  

   //oss默认响应头
   static $OSS_DEFAULT_REAPONSE_HEADERS = array(
      'date', 'content-type', 'content-length', 'connection', 'accept-ranges', 'cache-control', 'content-disposition', 'content-encoding', 'content-language',
      'etag', 'expires', 'last-modified', 'server'
   );
   
   /**
    * 抛出异常，指定的异常
    * 
    * @param type $key
    * @param array $args
    */
   public static function throwException($key, array $args = array())
   {
      $errorType = ErrorType::getInstance();
      if(empty($args)){
         $errorMsg = $errorType->msg($key);
      }else{
         array_unshift($args, $key);
         $errorMsg = call_user_func_array(array($errorType, 'msg'), $args);
      }
      Kernel\throw_exception(new Exception(
         $errorMsg, $errorType->code($key)));
   }

   public static function getObjectListMarkerFromXml($xml, &$marker)
   {
      $xml = new \SimpleXMLElement($xml);
      $isTruncated = $xml->IsTruncated;
      $objectList = array();
      $marker = $xml->NextMarker;
      foreach ($xml->Contents as $content) {
         array_push($objectList, $content->Key);
      }
      return $objectList;
   }
   /**
    * 
    * @param \Zend\Http\Response $response
    * @param type $msg
    * @param type $isSimplePrint
    */
   public static function printResponse($response, $msg = "", $isSimplePrint = true)
   {
      if ($isSimplePrint) {
         if ((int) ($response->getStatusCode() / 100) == 2) {
            echo $msg . " OK\n";
         } else {
            echo "ret:" . $response->getStatusCode() . "\n";
            echo $msg . " FAIL\n";
         }
      } else {
         echo '|-----------------------Start---------------------------------------------------------------------------------------------------' . "\n";
         echo '|-Status:' . $response->getStatusCode() . "\n";
         echo '|-Body:' . "\n";
         $body = $response->getContent() . "\n";
         echo $body . "\n";
         echo "|-Header:\n";
         print_r($response->getHeaders()->toArray());
         echo '-----------------------End-----------------------------------------------------------------------------------------------------' . "\n\n";
      }
   }

   /* %******************************************************************************************************% */
   //工具类相关

   /**
    * 生成query params
    * @param array $array 关联数组
    * @return string 返回诸如 key1=value1&key2=value2
    */
   public static function toQueryString($options = array())
   {
      $temp = array();
      uksort($options, 'strnatcasecmp');
      foreach ($options as $key => $value) {
         if (is_string($key) && !is_array($value)) {
            $temp[] = rawurlencode($key) . '=' . rawurlencode($value);
         }
      }
      return implode('&', $temp);
   }

   /**
    * @param $str
    * @return string
    */
   public static function hexToBase64($str)
   {
      $result = '';
      for ($i = 0; $i < strlen($str); $i += 2) {
         $result .= chr(hexdec(substr($str, $i, 2)));
      }
      return base64_encode($result);
   }

   public static function xmlEntityReplace($subject)
   {
      $search = array('<', '>', '&', '\'', '"');
      $replace = array('&lt;', '&gt;', '&amp;', '&apos;', '&quot;');
      return str_replace($search, $replace, $subject);
   }

   /**
    * @param $subject
    * @return mixed
    */
   public static function replaceInvalidXmlChar($subject)
   {
      $search = array(
         '&#01;', '&#02;', '&#03;', '&#04;', '&#05;', '&#06;', '&#07;', '&#08;', '&#09;', '&#10;', '&#11;', '&#12;', '&#13;',
         '&#14;', '&#15;', '&#16;', '&#17;', '&#18;', '&#19;', '&#20;', '&#21;', '&#22;', '&#23;', '&#24;', '&#25;', '&#26;',
         '&#27;', '&#28;', '&#29;', '&#30;', '&#31;', '&#127;'
      );
      $replace = array(
         '%01', '%02', '%03', '%04', '%05', '%06', '%07', '%08', '%09', '%0A', '%0B', '%0C', '%0D',
         '%0E', '%0F', '%10', '%11', '%12', '%13', '%14', '%15', '%16', '%17', '%18', '%19', '%1A',
         '%1B', '%1C', '%1D', '%1E', '%1F', '%7F'
      );

      return str_replace($search, $replace, $subject);
   }

   /**
    * @param $str
    * @return int
    */
   public static function chkChinese($str)
   {
      return preg_match('/[\x80-\xff]./', $str);
   }

   /**
    * 检测是否GB2312编码
    * 
    * @return boolean false UTF-8编码  TRUE GB2312编码
    */
   public static function isGb2312($str)
   {
      for ($i = 0; $i < strlen($str); $i++) {
         $v = ord($str[$i]);
         if ($v > 127) {
            if (($v >= 228) && ($v <= 233)) {
               if (($i + 2) >= (strlen($str) - 1))
                  return true;  // not enough characters  
               $v1 = ord($str[$i + 1]);
               $v2 = ord($str[$i + 2]);
               if (($v1 >= 128) && ($v1 <= 191) && ($v2 >= 128) && ($v2 <= 191))
                  return false;   //UTF-8编码  
               else
                  return true;    //GB编码  
            }
         }
      }
   }

   /**
    * 检测是否GBK编码
    * @param string $str 
    * @param boolean $gbk
    * @author xiaobing
    * @since 2012-06-04
    * @return boolean 
    */
   public static function checkChar($str, $gbk = true)
   {
      for ($i = 0; $i < strlen($str); $i++) {
         $v = ord($str[$i]);
         if ($v > 127) {
            if (($v >= 228) && ($v <= 233)) {
               if (($i + 2) >= (strlen($str) - 1))
                  return $gbk ? true : FALSE;  // not enough characters
               $v1 = ord($str[$i + 1]);
               $v2 = ord($str[$i + 2]);
               if ($gbk) {
                  return (($v1 >= 128) && ($v1 <= 191) && ($v2 >= 128) && ($v2 <= 191)) ? FALSE : TRUE; //GBK
               } else {
                  return (($v1 >= 128) && ($v1 <= 191) && ($v2 >= 128) && ($v2 <= 191)) ? TRUE : FALSE;
               }
            }
         }
      }
      return $gbk ? true : false;
   }

   /**
    * 检验bucket名称是否合法
    * bucket的命名规范：
    * 1. 只能包括小写字母，数字
    * 2. 必须以小写字母或者数字开头
    * 3. 长度必须在3-63字节之间
    * @param string $bucket (Required)
    */
   public static function validateBucket($bucket)
   {
      $pattern = '/^[a-z0-9][a-z0-9-]{2,62}$/';
      if (!preg_match($pattern, $bucket)) {
         return false;
      }
      return true;
   }

   /**
    * 检验object名称是否合法
    * object命名规范:
    * 1. 规则长度必须在1-1023字节之间
    * 2. 使用UTF-8编码
    * @param string $object (Required)
    */
   public static function validateObject($object)
   {
      $pattern = '/^.{1,1023}$/';
      if (empty($object) || !preg_match($pattern, $object)) {
         return false;
      }
      return true;
   }
   /**
    * 检测上传文件的内容
    * @param array $options (Optional)
    * @throws \Cntysoft\Framework\Cloud\Ali\Oss\Exception
    */
   public static function validateContent($options)
   {
      if (isset($options[OSS_CONST::OSS_OPT_CONTENT])) {
         if ($options[OSS_CONST::OSS_OPT_CONTENT] == '' || !is_string($options[OSS_CONST::OSS_OPT_CONTENT])) {
            self::throwException('E_OSS_INVALID_HTTP_BODY_CONTENT');
         }
      } else {
         self::throwException('E_OSS_NOT_SET_HTTP_CONTENT');
      }
   }

   /**
    * @param $options
    * @throws \Cntysoft\Framework\Cloud\Ali\Oss\Exception
    */
   public static function validateContentLength($options)
   {
      if (isset($options[self::OSS_LENGTH]) && is_numeric($options[self::OSS_LENGTH])) {
         if (!$options[self::OSS_LENGTH] > 0) {
            self::throwException('E_OSS_CONTENT_LENGTH_MUST_MORE_THAN_ZERO');
         }
      } else {
         self::throwException('E_OSS_INVALID_CONTENT_LENGTH');
      }
   }

   /**
    * 设置http header
    * 
    * @param string $key (Required)
    * @param string $value (Required)
    * @param array $options (Required)
    * @throws OSS_Exception
    * @author xiaobing
    * @return void
    */
   public static function set_options_header($key, $value, &$options)
   {
      if (isset($options[self::OSS_HEADERS])) {
         if (!is_array($options[self::OSS_HEADERS])) {
            throw new OSS_Exception(OSS_INVALID_OPTION_HEADERS, '-600');
         }
      } else {
         $options[self::OSS_HEADERS] = array();
      }
      $options[self::OSS_HEADERS][$key] = $value;
   }

   /**
    * 检测是否windows系统，因为windows系统默认编码为GBK
    * @return bool
    */
   public static function isWin()
   {
      return strtoupper(substr(PHP_OS, 0, 3)) == "WIN";
   }

   /**
    * 主要是由于windows系统编码是gbk，遇到中文时候，如果不进行转换处理会出现找不到文件的问题
    * 
    * @param $filePath
    * @return string
    */
   public static function encodingPath($filePath)
   {
      if (self::chkChinese($filePath) && self::isWin()) {
         $filePath = iconv('utf-8', 'gbk', $filePath);
      }
      return $filePath;
   }

   /**
    * 将字符串表示的response对象转换成数组形式
    * 
    * @param \Zend\Http\Response $response
    * @return array
    * @throws Exception
    */
   public static function parseResponse($response, $format = "array")
   {
      //如果启用响应结果转换，则进行转换，否则原样返回
      $body = $response->getContent();
      $headers = $response->getHeaders()->toArray();
      switch (strtolower($format)) {
         case 'array':
            $body = empty($body) ? $body : XML2Array::createArray($body);
            break;
         case "json":
            $body = empty($body) ? $body : json_encode(XML2Array::createArray($body));
            break;
         default:
            break;
      }

      return array(
         'success' => self::responseIsOk($response),
         'status' => $response->getStatusCode(),
         'header' => $headers,
         'body' => $body
      );
   }
   
    /**
    * @param \Zend\Http\Response $response
    * @return boolean
    */
   public static function responseIsOk($response)
   {
      return in_array($response->getStatusCode(), self::$successCodes) ? true : false;
   }
}