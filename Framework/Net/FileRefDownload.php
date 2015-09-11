<?php
/**
 * Cntysoft Cloud Software Team
 *
 * @author SOFTBOY <cntysoft@163.com>
 * @copyright  Copyright (c) 2010-2011 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license    http://www.cntysoft.com/license/new-bsd     New BSD License
 */
namespace Cntysoft\Framework\Net;
use Zend\Uri\Http as HttpUrl;
use Cntysoft\Stdlib\Filesystem;
use Cntysoft\Kernel;
use Cntysoft\Kernel\ConfigProxy;
/**
 * 文件下载类定义, 这个下载是程序自动下载远端文件， 一般下载图片比较多
 *
 */
abstract class FileRefDownload
{
   /**
    * The curl session handle
    *
    * @var resource|null
    */
   protected $curl = null;
   /**
    * 超时时间
    *
    * @var int $timeout
    */
   protected $timeout = 120;
   /**
    * 允许的上传的目标文件夹
    * 
    * @var array $allowDirs
    */
   protected $allowUploadDirs = null;
   public function __construct()
   {
      if (!extension_loaded('curl')) {
         Kernel\throw_exception(new Exception(
            Kernel\StdErrorType::msg('E_EXTENSION_NOT_LOADED', 'curl'), Kernel\StdErrorType::code('E_EXTENSION_NOT_LOADED')), \Cntysoft\STD_EXCEPTION_CONTEXT);
      }
      $cfg = ConfigProxy::getFrameworkConfig('Net');
      $this->allowUploadDirs = $cfg->upload->allowedDirs->toArray();
   }

   /**
    * 下载指定的网络文件到系统文件目录
    *
    * @param string $fileUrl
    * @param string $savedDir 目标保存的文件夹
    */
   public function download($fileUrl, $savedDir, $useOss = false)
   {
      if (null == $this->curl) {
         $this->curl = curl_init();
         curl_setopt($this->curl, CURLOPT_VERBOSE, 1);
         curl_setopt($this->curl, CURLOPT_TIMEOUT, $this->timeout);
      }
      $orgTime = ini_get('max_execution_time');
      $url = new HttpUrl($fileUrl);
      $attachmentFilename = $this->getSavedFilename($url->getPath(), $savedDir, $useOss);
      try {   
         curl_setopt($this->curl, CURLOPT_URL, $fileUrl);
         $fd = Filesystem::fopen($attachmentFilename, 'wb');
         curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
         curl_setopt($this->curl, CURLOPT_FILE, $fd);
         set_time_limit(0);
         if (!curl_exec($this->curl)) {
            curl_close($this->curl);
            $errorType = ErrorType::getInstance();
            Kernel\throw_exception(new Exception(
               $errorType->msg('E_CURL_ERROR', curl_error($this->curl)), $errorType->code('E_CURL_ERROR')), $errorType);
         }else {
            curl_close($this->curl);
         }
         $fileinfo = stat($attachmentFilename);
         $refInfo = array(
            'filename' => Filesystem::basename($url->getPath()),
            'filesize' => $fileinfo['size'],
            'attachment' => str_replace('\\', '/', str_replace(CNTY_ROOT_DIR, '', $attachmentFilename)),
            'targetFile' => $attachmentFilename
         );
         $refInfo = $this->afterFileSavedHandler($refInfo, $savedDir, $useOss);
         Filesystem::fclose($fd);
         set_time_limit($orgTime);
         return $refInfo;
      } catch (\Exception $ex) {
         set_time_limit($orgTime);
         //删除文件
         if (file_exists($attachmentFilename)) {
            Filesystem::deleteFile($attachmentFilename);
         }
         $errorType = ErrorType::getInstance();
         Kernel\throw_exception($ex, $errorType);
      }
   }

   /**
    * 获取真实的文件保存的名称
    *
    * @param string $filename 相对文件路径
    * @param string $savedDir 目标保存的文件夹
    * @param boolean $useOss 是否使用OSS服务器
    * @return string
    */
   protected function getSavedFilename($filename, $savedDir, $useOss = false)
   {
      if(!Kernel\check_target_upload_is_valid($savedDir, $this->allowUploadDirs)){
         $errorType = ErrorType::getInstance();
         Kernel\throw_exception(new Exception(
            $errorType->msg('E_UPLOAD_DIR_NOT_ALLOWED', $targetDir),
            $errorType->code('E_UPLOAD_DIR_NOT_ALLOWED')));
      }
   }

   /**
    * 文件保存之后调用的回调函数
    *
    * @param array $refInfo
    * @param string $savedDir 目标保存的文件夹
    * @param boolean $useOss 是否使用OSS服务器
    * @return array
    */
   abstract protected function afterFileSavedHandler(array $refInfo,$savedDir, $useOss = false);
   public function __destruct()
   {
      if (is_resource($this->curl)) {
         curl_close($this->curl);
      }
      $this->curl = null;
   }
}