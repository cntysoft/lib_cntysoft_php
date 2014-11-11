<?php
/**
 * Cntysoft Cloud Software Team
 *
 * @author SOFTBOY <cntysoft@163.com>
 * @copyright  Copyright (c) 2010-2011 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license    http://www.cntysoft.com/license/new-bsd     New BSD License
 */
namespace Cntysoft\Stdlib;
use Cntysoft\Framework\Utils\Scws;
use Cntysoft\Kernel;
/**
 * 一些跟字符串相关的函数
 */
class String
{
   /**
    * 判断一个字符串是否为utf8
    * @param string $string
    * @return boolean
    */
   public static function isUtf8($string)
   {
      $length = strlen($string);
      for ($i = 0; $i < $length; $i++) {
         if (ord($string[$i]) < 0x80) {
            $n = 0;
         } else if ((ord($string[$i]) & 0xE0) == 0xC0) {
            $n = 1;
         } else if ((ord($string[$i]) & 0xF0) == 0xE0) {
            $n = 2;
         } else if ((ord($string[$i]) & 0xF0) == 0xF0) {
            $n = 3;
         } else {
            return false;
         }
         for ($j = 0; $j < $n; $j++) {
            if ((++$i == $length) || ((ord($string[$i]) & 0xC0) != 0x80)) {
               return false;
            }
         }
      }
      return true;
   }

   /**
    * Converts a string to UTF-8 encoding.
    *
    * @param  string $string
    * @return string
    */
   public static function convertToUtf8($string)
   {
      if (!self::isUtf8($string)) {
         if (function_exists('mb_convert_encoding')) {
            $string = mb_convert_encoding($string, 'UTF-8');
         } else {
            $string = utf8_encode($string);
         }
      }
      return $string;
   }

   /**
    * 获取指定的数据的关键字
    *
    * @param string $data
    * @param string $separator 分割符号
    * @param int      $num 关键字数目
    * @return string
    */
   public static function getKeyWords($data, $separator = ' ', $num = 5)
   {
      $parser = new Scws();
      $parser->sendText($data);
      $ret = $parser->getTops($num);
      $words = array();
      foreach ($ret as $item){
         $words[] = $item['word'];
      }
      return  implode($separator, $words);
   }
   /**
    * 获取uuid数据
    *
    * @see http://www.ietf.org/rfc/rfc4122.txt
    * @return string
    */
   public static function uuid()
   {
      $node = Kernel\get_server_env('SERVER_ADDR');
      if (strpos($node, ':') !== false) {
         if (substr_count($node, '::')) {
            $node = str_replace(
               '::', str_repeat(':0000', 8 - substr_count($node, ':')) . ':', $node
            );
         }
         $node = explode(':', $node);
         $ipSix = '';
         foreach ($node as $id) {
            $ipSix .= str_pad(base_convert($id, 16, 2), 16, 0, STR_PAD_LEFT);
         }
         $node = base_convert($ipSix, 2, 10);
         if (strlen($node) < 38) {
            $node = null;
         } else {
            $node = crc32($node);
         }
      } elseif (empty($node)) {
         $host = Kernel\get_server_env('HOSTNAME');
         if (empty($host)) {
            $host = Kernel\get_server_env('HOST');
         }
         if (!empty($host)) {
            $ip = gethostbyname($host);

            if ($ip === $host) {
               $node = crc32($host);
            } else {
               $node = ip2long($ip);
            }
         }
      } elseif ($node !== '127.0.0.1') {
         $node = ip2long($node);
      } else {
         $node = null;
      }

      if (empty($node)) {
         $node = crc32(mktime());
      }
      $pid = getmypid();

      if (!$pid || $pid > 65535) {
         $pid = mt_rand(0, 0xfff) | 0x4000;
      }
      list($timeMid, $timeLow) = explode(' ', microtime());
      $uuid = sprintf(
         "%08x-%04x-%04x-%02x%02x-%04x%08x", (int) $timeLow, (int) substr($timeMid, 2) & 0xffff, mt_rand(0, 0xfff) | 0x4000, mt_rand(0, 0x3f) | 0x80, mt_rand(0, 0xff), $pid, $node
      );
      return $uuid;
   }

   /**
    * Tokenizes a string using $separator, ignoring any instance of $separator that appears between
    * $leftBound and $rightBound
    *
    * @param string $data The data to tokenize
    * @param string $separator The token to split the data on.
    * @param string $leftBound The left boundary to ignore separators in.
    * @param string $rightBound The right boundary to ignore separators in.
    * @return array Array of tokens in $data.
    */
   public static function tokenize($data, $separator = ',', $leftBound = '(', $rightBound = ')')
   {
      if (empty($data) || is_array($data)) {
         return $data;
      }

      $depth = 0;
      $offset = 0;
      $buffer = '';
      $results = array();
      $length = strlen($data);
      $open = false;

      while ($offset <= $length) {
         $tmpOffset = -1;
         $offsets = array(
            strpos($data, $separator, $offset),
            strpos($data, $leftBound, $offset),
            strpos($data, $rightBound, $offset)
         );
         for ($i = 0; $i < 3; $i++) {
            if ($offsets[$i] !== false && ($offsets[$i] < $tmpOffset || $tmpOffset == -1)) {
               $tmpOffset = $offsets[$i];
            }
         }
         if ($tmpOffset !== -1) {
            $buffer .= substr($data, $offset, ($tmpOffset - $offset));
            if ($data{$tmpOffset} == $separator && $depth == 0) {
               $results[] = $buffer;
               $buffer = '';
            } else {
               $buffer .= $data{$tmpOffset};
            }
            if ($leftBound != $rightBound) {
               if ($data{$tmpOffset} == $leftBound) {
                  $depth++;
               }
               if ($data{$tmpOffset} == $rightBound) {
                  $depth--;
               }
            } else {
               if ($data{$tmpOffset} == $leftBound) {
                  if (!$open) {
                     $depth++;
                     $open = true;
                  } else {
                     $depth--;
                     $open = false;
                  }
               }
            }
            $offset = ++$tmpOffset;
         } else {
            $results[] = $buffer . substr($data, $offset);
            $offset = $length + 1;
         }
      }
      if (empty($results) && !empty($buffer)) {
         $results[] = $buffer;
      }

      if (!empty($results)) {
         $data = array_map('trim', $results);
      } else {
         $data = array();
      }
      return $data;
   }
}