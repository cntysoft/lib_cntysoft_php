<?php
/**
 * Cntysoft Cloud Software Team
 *
 * @author SOFTBOY <cntysoft@163.com>
 * @copyright  Copyright (c) 2010-2011 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license    http://www.cntysoft.com/license/new-bsd     New BSD License
 */
namespace Cntysoft\Stdlib;
use Cntysoft\Kernel\StdDir;
use \RecursiveDirectoryIterator;
use \RecursiveIteratorIterator;
use Cntysoft\Kernel\StdErrorType;
use Cntysoft\Kernel;
use Zend\Stdlib\ErrorHandler;
/**
 *  重新封装系统的一些文件系统函数，将其抛出的警告等等包装成异常进行抛出
 */
class Filesystem
{
   /**
    * 将数据写入文件
    *
    * @param string $filename
    * @param string $data
    * @return int | FALSE
    */
   public static function filePutContents($filename, $data, $recursive = false)
   {
      ErrorHandler::start();
      $filename = Kernel\real_path($filename);
      if ($filename == '') {
         Kernel\throw_exception(new Exception(
            StdErrorType::msg('E_FILE_NOT_WRITABLE', dirname($filename)), StdErrorType::code('E_FILE_NOT_WRITABLE')
         ), \Cntysoft\STD_EXCEPTION_CONTEXT);
      }
      $dir = dirname($filename);
      if(!file_exists($dir)) {
         self::createDir($dir, 0755, $recursive);
      }
      $ret = file_put_contents($filename, $data, LOCK_EX);
      ErrorHandler::stop(true);
      return $ret;
   }

   /**
    * 读取文件数据
    *
    * @param string $filename
    * @return string
    */
   public static function fileGetContents($filename)
   {
      ErrorHandler::start();
      $ret = file_get_contents($filename);
      ErrorHandler::stop(true);
      return $ret;
   }

   /**
    * 创建一个空白文件
    *
    * @param string $filename
    * @return boolean
    */
   public static function touch($filename)
   {
      ErrorHandler::start();
      $filename = Kernel\real_path($filename);
      if ($filename == '' || !is_writable(dirname($filename))) {
         Kernel\throw_exception(new Exception(
            StdErrorType::msg('E_FILE_NOT_WRITABLE', dirname($filename)), StdErrorType::code('E_FILE_NOT_WRITABLE')
         ), \Cntysoft\STD_EXCEPTION_CONTEXT);
      }
      $flag = touch($filename);
      ErrorHandler::stop(true);
      return $flag;
   }

   /**
    * 浏览指定文件的内容
    *
    * @param string $filename 需要查看的文件的名称，必须是完整的路径
    * @return string 指定文件的内容
    * @throws Exceptions
    */
   public static function cat($filename)
   {
      $filename = Kernel\real_path($filename);
      if (!file_exists($filename)) {
         Kernel\throw_exception(new Exception(
            StdErrorType::msg('E_FILE_NOT_EXIST'), StdErrorType::code('E_FILE_NOT_EXIST')
         ), \Cntysoft\STD_EXCEPTION_CONTEXT);
      }
      if (!is_file($filename)) {
         Kernel\throw_exception(new Exception(
            StdErrorType::msg('E_FILE_IS_NOT_REGULAR'), StdErrorType::code('E_FILE_IS_NOT_REGULAR')
         ), \Cntysoft\STD_EXCEPTION_CONTEXT);
      }
      if (!is_readable($filename)) {
         Kernel\throw_exception(new Exception(
            StdErrorType::msg('E_FILE_NOT_READABLE'), StdErrorType::code('E_FILE_NOT_READABLE')
         ), \Cntysoft\STD_EXCEPTION_CONTEXT);
      }
      ErrorHandler::start();
      $data = file_get_contents($filename);
      ErrorHandler::stop(true);
      return $data;
   }

   /**
    * 遍历指定路径的一级目录
    *
    * @param  string $dir 以系统为起点
    * @return array
    */
   public static function ls($dir = null, $depth = 1)
   {
      $ret = array();
      $dir = Kernel\real_path($dir);
      self::traverseFs($dir, $depth, function($fileinfo) use(&$ret) {
         $ret[] = $fileinfo;
      });
      return $ret;
   }

   /**
    * 复制文件
    *
    * @param string $source
    * @param string $target
    * @param int $mode
    * @return boolean
    */
   public static function copyFile($source, $target, $mode = 0755)
   {
      $source = Kernel\real_path($source);
      $target = Kernel\real_path($target);
      //尽量防止抛出ErrorException
      if (!is_readable($source)) {
         Kernel\throw_exception(new Exception(
            StdErrorType::msg('E_FILE_NOT_EXIST', $source), StdErrorType::code('E_FILE_NOT_EXIST')
         ), \Cntysoft\STD_EXCEPTION_CONTEXT);
      }
      //创建文件夹
      $targetDir = dirname($target);
      if(!file_exists($targetDir)){
         self::createDir($targetDir, $mode, true);
      }
      ErrorHandler::start();
      $flag = copy($source, $target);
      chmod($target, $mode);
      ErrorHandler::stop(true);
      return $flag;
   }

   /**
    * 递归复制一个栏目的文件结构
    *
    * @param string $source
    * @param string $target
    */
   public static function copyDir($source, $target, $mode = 0755)
   {
      $source = Kernel\real_path($source);
      $target = Kernel\real_path($target);
      //尽量避免在复制的过程中
      if (!file_exists($source)) {
         Kernel\throw_exception(new Exception(
            StdErrorType::msg('E_FILE_NOT_EXIST', $source), StdErrorType::code('E_FILE_NOT_EXIST')
         ), \Cntysoft\STD_EXCEPTION_CONTEXT);
      }
      if (!is_readable($source)) {
         Kernel\throw_exception(new Exception(
            StdErrorType::msg('E_FILE_NOT_READABLE', $source), StdErrorType::code('E_FILE_NOT_READABLE')
         ), \Cntysoft\STD_EXCEPTION_CONTEXT);
      }
      //判断目标是否可写
      if (!file_exists($target)) {
         Kernel\throw_exception(new Exception(
            StdErrorType::msg('E_FILE_NOT_EXIST', $target), StdErrorType::code('E_FILE_NOT_EXIST')
         ), \Cntysoft\STD_EXCEPTION_CONTEXT);
      }
      //防止把自己复制进自己及其子文件夹里面
      $len = strlen($source);
      if ($source == substr($target, 0, $len)) {
         Kernel\throw_exception(new Exception(
            StdErrorType::msg('E_DEST_NAME_INVALID', $target, $source), StdErrorType::code('E_DEST_NAME_INVALID')
         ), \Cntysoft\STD_EXCEPTION_CONTEXT);
      }
      $old = umask(0);
      //递归复制
      //复制本身

      $target = $target.DS.self::basename($source);
      $target = self::generateFileName($target);
      $self = '\\'.get_class();
      if(!file_exists($target)){
         self::createDir($target, $mode);
      }
      self::traverseFs($source, 0, function($fileinfo)use($target, $len, $self, $mode) {
         $sourceName = $fileinfo->getPathname();
         $str = substr($sourceName, $len);
         $targetName = $target.$str;
         if ($fileinfo->isFile()) {
            $self::copyFile($sourceName, $targetName, $mode);
         } else if ($fileinfo->isDir()) {
            $self::createDir($targetName, $mode);
         }
      }, \RecursiveIteratorIterator::SELF_FIRST);
      umask($old);
   }

   /**
    *  保存数据到指定文件，如果文件不存在将会创建文件
    *
    * @param string $filename 写入数据文件名称
    * @param mixed $data 写入数据
    * @param int $flag 操作标志
    * @return int 写入的字符数
    */
   public static function save($filename, $data, $flag = LOCK_EX)
   {
      $filename = Kernel\real_path($filename);
      if (file_exists($filename) && !is_writable($filename)) {
         Kernel\throw_exception(new \Exception(
            StdErrorType::msg('E_FILE_NOT_WRITABLE', $filename), StdErrorType::code('E_FILE_NOT_WRITABLE')
         ), \Cntysoft\STD_EXCEPTION_CONTEXT);
      }
      ErrorHandler::start();
      $ret = file_put_contents($filename, $data, $flag);
      ErrorHandler::stop(true);
      return $ret;
   }

   /**
    * 删除制定路径的文件
    *
    * @param string $filename
    * @return boolean
    */
   public static function deleteFile($filename)
   {
      $filename = Kernel\real_path($filename);
      if (!file_exists($filename)) {
         Kernel\throw_exception(new Exception(
            StdErrorType::msg('E_FILE_NOT_EXIST', $filename), StdErrorType::code('E_FILE_NOT_EXIST')
         ), \Cntysoft\STD_EXCEPTION_CONTEXT);
      }
      ErrorHandler::start();
      $ret = unlink($filename);
      ErrorHandler::stop(true);
      return $ret;
   }

   /**
    * 删除指定路径的文件夹
    *
    * @param string $directory 文件夹路径
    * @return boolean
    * @throws Exception
    */
   public static function deleteDir($directory)
   {
      //这个方法调用的时候Windows下已经将参数转换成了GBK编码
      //$directory = Kernel\real_path($directory);
      if (!file_exists($directory)) {
         Kernel\throw_exception(new Exception(
            StdErrorType::msg('E_DIR_NOT_EXIST', $directory), StdErrorType::code('E_DIR_NOT_EXIST')
         ), \Cntysoft\STD_EXCEPTION_CONTEXT);
      }
      if (!is_dir($directory)) {
         Kernel\throw_exception(new Exception(
            StdErrorType::msg('E_NOT_DIR', $directory), StdErrorType::code('E_NOT_DIR')
         ), \Cntysoft\STD_EXCEPTION_CONTEXT);
      }

      ErrorHandler::start();
      $flag = rmdir($directory);
      ErrorHandler::stop(true);
      return $flag;
   }

   /**
    * 递归删除文件夹,这个函数很危险
    *
    * @param string $directory 要删除的文件夹名称
    */
   public static function deleteDirRecusive($directory)
   {

      $fs = '\Cntysoft\Stdlib\Filesystem';
      $directory = Kernel\real_path($directory);

      self::traverseFs($directory, 0, function($info)use($fs) {
         if ($info->isDir()) {

            $fs::deleteDir($info->getPathname());
         } else if ($info->isFile()) {
            $fs::deleteFile($info->getPathname());
         }
      }, RecursiveIteratorIterator::CHILD_FIRST);
      //删除本身
      self::deleteDir($directory);
   }

   /**
    * 创建一个文件夹
    *
    * @param string $directory
    * @param int $mode
    * @param boolean $recursive
    * @return boolean
    */
   public static function createDir($directory, $mode = 0750, $recursive = false)
   {
      $directory = Kernel\real_path($directory);
      ErrorHandler::start();
      $old = umask(0);
      $flag = mkdir($directory, $mode, $recursive);
      umask($old);
      ErrorHandler::stop(true);
      return $flag;
   }
   /**
    * 打开文件夹
    *
    * @param string $dirname 需要打开的文件夹名称
    * @return resource
    */
   public static function openDir($dirname)
   {
      $dirname = Kernel\real_path($dirname);
      ErrorHandler::start();
      $resource = opendir($dirname);
      ErrorHandler::stop(true);
      return $resource;
   }
   /**
    * 打开一个指定的文件
    *
    * @param string $filename 文件名称
    * @param string $mode 打开文件的模式
    * @return resource
    */
   public static function fopen($filename, $mode)
   {
      $filename = Kernel\real_path($filename);
      ErrorHandler::start();
      $resource = fopen($filename, $mode);
      ErrorHandler::stop(true);
      return $resource;
   }

   /**
    * 关闭一个打开的文件
    *
    * @param resource $handle
    */
   public static function fclose($handle)
   {
      ErrorHandler::start();
      $ret = fclose($handle);
      ErrorHandler::stop(true);
      return $ret;
   }
   /**
    * 给文件或者文件夹重命名
    *
    * @param string $oldName
    * @param string $newName
    * @return boolean
    */
   public static function rename($oldName, $newName)
   {
      $oldName = Kernel\real_path($oldName);
      $newName = Kernel\real_path($newName);
      ErrorHandler::start();
      $ret = rename($oldName, $newName);
      ErrorHandler::stop(true);
      return $ret;
   }
   /**
    * 生成临时文件的名称
    *
    * @param string $name
    * @return string
    */
   public static function generateTmpFilename($name)
   {
      $tmpDir = StdDir::getTmpDir();
      if (!file_exists($tmpDir)) {
         self::createDir($tmpDir);
      }
      ErrorHandler::start();
      $name = tempnam($tmpDir, $name);
      ErrorHandler::stop(true);
      return $name;
   }
   //时间相关的函数
   /**
    * This function returns the time when the data blocks of a file were being written to, that is,
    * the time when the content of the file was changed.
    *
    * @param string $filename
    */
   public static function filemtime($filename)
   {
      $filename = Kernel\real_path($filename);
      ErrorHandler::start();
      $ret = filemtime($filename);
      ErrorHandler::stop(true);
      return $ret;
   }

   /**
    * 获取路径的父路径，这个原生的函数在WINDOWS下对中文支持有问题
    *
    * @param string $filename
    * @return string
    */
   public static function dirname($filename)
   {
      if(PHP_OS == \Cntysoft\WINDOWS){
         $filename = Kernel\convert_2_utf8($filename); //确保编码是UTF8
         $pos = iconv_strrpos($filename, DS, 'utf-8');
         if(!$pos){
            return '';
         }
         return Kernel\real_path(iconv_substr($filename, 0, $pos, 'utf-8'));
      }
      return  Kernel\real_path(dirname($filename));
   }

   /**
    * 获取文件路径的基本名称，这个原生的函数在WINDOWS下对中文支持有问题
    *  Linux系统中的中文路径好像也有问题
    *
    * @param string $filename
    * @return string
    */
   public static function basename($filename)
   {
      $filename = Kernel\convert_2_utf8($filename); //确保编码是UTF8
//      if(PHP_OS == \Cntysoft\WINDOWS){
         $pos = iconv_strrpos($filename, DS, 'utf-8');
         if(!$pos){
            return Kernel\real_path($filename);
         }
         $dir = iconv_substr($filename, 0, $pos+1, 'utf-8');
         $basename = str_replace($dir, '', $filename);
         return Kernel\real_path($basename);
//      }
      //return  Kernel\real_path( basename($filename));
   }

   /**
    * 遍历文件系统结构
    *
    * @param string $path 需要遍历的路径
    * @param int $depth 遍历的深度 为0的话 深度不限
    * @param Closure $function 遍历执行的函数
    * @param int $flag
    */
   public static function traverseFs($path, $depth = 1, $function = null, $flag = RecursiveIteratorIterator::SELF_FIRST)
   {

      if (!file_exists($path)) {
         Kernel\throw_exception(new Exception(
            StdErrorType::msg('E_DIR_NOT_EXIST', $path), StdErrorType::code('E_DIR_NOT_EXIST')
         ), \Cntysoft\STD_EXCEPTION_CONTEXT);
      }
      if (!is_dir($path)) {
         Kernel\throw_exception(new Exception(
            StdErrorType::msg('E_NOT_DIR', $path), StdErrorType::code('E_NOT_DIR')
         ), \Cntysoft\STD_EXCEPTION_CONTEXT);
      }
      $depth--;

      if ($depth < -1) {
         $errorType = ErrorType::getInstance();
         Kernel\throw_exception(new Exception(
            sprintf('depth must >= 0 , %d given', $depth)
         ), $errorType);


      }
      if (null != $function && !is_callable($function)) {
         Kernel\throw_exception(new Exception(
            StdErrorType::msg('E_NOT_CALLABLE', $function), StdErrorType::code('E_NOT_CALLABLE')
         ), \Cntysoft\STD_EXCEPTION_CONTEXT);
      }
      //需要检查可以执行吗？
      $directory = new RecursiveDirectoryIterator($path);
      $iterator = new RecursiveIteratorIterator($directory, $flag);
      foreach ($iterator as $key => $value) {
         if ($depth != -1 && $iterator->getDepth() > $depth) {
            continue;
         }
         if ($value->isDir()) {
            $filename = $value->getFilename();
            if ('.' == $filename || '..' == $filename) {
               continue;
            }
         }
         if (is_callable($function)) {
            $function($value, $iterator->getDepth());
         }
      }
   }
   /**
    * 生成唯一的文件名称
    *
    * @param string $filename
    * @return string
    */
   protected static function generateFileName($filename)
   {
      $filename = Kernel\real_path($filename);
      //加上文件夹是否存在的判断
      if (file_exists($filename) && (is_file($filename) || is_dir($filename))) {
         $filename = Kernel\real_path(Kernel\convert_2_utf8($filename).'_副本');
      }
      return $filename;
   }
}