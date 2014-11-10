<?php
/**
 * Cntysoft Cloud Software Team
 *
 * @author SOFTBOY <cntysoft@163.com>
 * @copyright  Copyright (c) 2010-2011 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license    http://www.cntysoft.com/license/new-bsd     New BSD License
 */
namespace Cntysoft\Framework\Net;
use Cntysoft\Framework\Net\Options\Upload as UploadOption;
use Cntysoft\Kernel\StdDir;
use Cntysoft\Kernel;
use Cntysoft\Stdlib\Filesystem;
use Cntysoft\Framework\Utils\Image;
use Phalcon\Events\Manager as EventsManager;

/**
 * 平台的上传处理类，使用百度上传器的处理方法
 * 当前的文件大小限制没有很好的实现
 */
class Upload
{
   /**
    * @var \Phalcon\Events\Manager $eventMgr
    */
   protected $eventMgr;
   /**
    * @var \Cntysoft\Framework\Net\Options\Upload $options
    */
   protected $options = null;
   /**
    * @var \Zend\Filter\File\RenameUpload $filter
    */
   protected $filter;

   /**
    * 这里指定的配置项的优先级最高
    *
    * @param array $option
    */
   public function __construct(array $option = array())
   {
      //这里有可能失败～ 怎么处理这种情况
      $this->fileSizeLimit = ini_get('upload_max_filesize');
      $this->tmpDir = StdDir::getTmpDir() . DS . 'WebUploader';
      //处理下上传目标文件夹
      if (isset($option['uploadDir'])) {
         $option['uploadDir'] = Kernel\real_path($option['uploadDir']);
      }
      $this->options = new UploadOption($option);
      //调整依赖关系
   }

   /**
    * @return \Cntysoft\Framework\Net\Options\Upload
    */
   public function getOptions()
   {
      return $this->options;
   }
   /**
    * 对一个上传文件进行操作，我们的这个上传类是配合swfuploader进行设计的
    * 他可以多文件上传但是处理方式与单文件处理方式一样
    *
    * 当开启生成缩略图的时候一次上传图片返回两个元素的数组
    *
    * <code>
    * array(
    *      array('filename' => 'the name'),//正常的图片
    *      array('filename' => 'the name')//缩略图路径
    * );
    * </code>
    *
    * 当开启文件引用的时候每个数组项会多出一个参数
    *
    * <code>
    * array(
    *      'filename' => '文件名称',
    *      'rid' => '文件引用项'
    * );
    *</code>
    * @param \Phalcon\Http\Request\File $uploadFile
    * @return string
    */
   public function saveUploadFile($uploadFile)
   {
      $sourceFile = $uploadFile->getTempName();
      $targetFile = $this->getTargetFileName($uploadFile);
      if (!file_exists($sourceFile) || $sourceFile == $targetFile) {
         $errorType = ErrorType::getInstance();
         Kernel\throw_exception(new Exception(
            $errorType->msg('E_UPLOAD_FILE_NOT_EXIST', $sourceFile), $errorType->code('E_UPLOAD_FILE_NOT_EXIST')), $errorType);
      }
      $this->checkFileExists($targetFile);
      $eventMgr = $this->getEventMgr();
      $eventMgr->fire('upload:beforeSaveUploadFile', $this, $sourceFile, $targetFile);
      //判断上传的文件是否是图片
      $fileType = $uploadFile->getType();
      $isImage = (0 === strpos($fileType, 'image')) ? true : false;
      //这里的返回值可能是数组，当上传的文件是图片的时候，就会生成缩略图，会有两张
      $savedFiles = array($this->moveUploadFile($sourceFile, $targetFile));
      $enableNail = $this->options->getEnableNail();
      if ($isImage && $enableNail) { //如果不是图片的话，或者没有设置生成缩略图的话，直接返回
         $savedFilename = $savedFiles[0];
         $image = new Image(array(
            'imageFromPath' => $savedFilename
         ));
         //目标上传文件夹
         $targetDir = Filesystem::dirname($savedFilename);
         $filename = Filesystem::basename($savedFilename);
         $filename = explode('.', $filename);
         $nailName = array_shift($filename);
         $savedFiles[] = $image->generateThumbnail($targetDir, $nailName . '_nail');
      }
      $eventMgr = $this->getEventMgr();
      $eventMgr->collectResponses(true);
      $response = $eventMgr->fire('upload:AfterSaveUploadFiles', $this, $savedFiles);
      $ret = array();
      foreach($response as $files){
         $ret = array_merge($ret, $files);
      }

      return $ret;
   }

   /**
    * 保存上传中的文件，如果是图片文件的话，直接生成缩略图
    *
    * @param string $sourceFile 来源文件名称
    * @param string $targetFile 目标保存文件名称
    * @return array 返回上传的文件信息
    */
   protected function moveUploadFile($sourceFile, $targetFile)
   {
      //目标上传文件夹
      $targetDir = Filesystem::dirname($targetFile);
      //这行代码有中文就会有问题 dirname没有问题
      $filename = str_replace($targetDir . DS, '', $targetFile);
      if (!file_exists($targetDir)) {
         Filesystem::createDir($targetDir, 0750, true);
      }
      $tmp = $this->tmpDir;
      if (!file_exists($tmp)) {
         Filesystem::createDir($tmp, 0750, true);
      }
      $maxFileAge = 5 * 3600; // Temp file age in seconds
      //暂时没有吃透这个有什么用
//        $md5Filename = $tmp.DS.'md5list.txt';
//        $md5File = @file($md5Filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
//        $md5File = $md5File ? $md5File : array();
      $chunk = $this->options->getChunk();
      $totalChunk = $this->options->getTotalChunk();
      $tmpDir = Filesystem::openDir($tmp);
      $filePath = $tmp . DS . $filename; //上传文件在分片临时目录里面的临时文件的名称
      //将上传文件从系统临时目录复制到我们系统的临时目录
      while (($file = readdir($tmpDir)) !== false) {
         $tmpfilePath = $tmp . DIRECTORY_SEPARATOR . $file;
         // If temp file is current file proceed to the next
         if ($tmpfilePath == "{$filePath}_{$chunk}.part" || $tmpfilePath == "{$filePath}_{$chunk}.parttmp") {
            continue;
         }
         // Remove temp file if it is older than the max age and is not the current file
         if (preg_match('/\.(part|parttmp)$/', $file) && (Filesystem::filemtime($tmpfilePath) < time() - $maxFileAge)) {
            Filesystem::deleteFile($tmpfilePath);
         }
      }
      $tmpOut = Filesystem::fopen("{$filePath}_{$chunk}.parttmp", 'wb');
      $in = Filesystem::fopen($sourceFile, 'rb');
      while ($buff = fread($in, 4096)) {
         fwrite($tmpOut, $buff);
      }
      fclose($tmpOut);
      fclose($in);
      //完全复制成功后改名称
      Filesystem::rename("{$filePath}_{$chunk}.parttmp", "{$filePath}_{$chunk}.part");
      //查看分片是否全部上传完成
      $index = 0;
      $done = true;
      for ($index = 0; $index < $totalChunk; $index++) {
         if (!file_exists(Kernel\real_path("{$filePath}_{$index}.part"))) {
            $done = false;
            break;
         }
      }
      //将分片复制到最终的文件中
      if ($done) {
         if ($this->options->getRandomize()) {
            //随机码加在前面
            $filename = uniqid() . '_' . $filename;
            $targetFile = $targetDir . DS . $filename;
         }
         $out = Filesystem::fopen($targetFile, "wb");
         if (flock($out, LOCK_EX)) {
            for ($index = 0; $index < $totalChunk; $index++) {
               if (!$in = Filesystem::fopen("{$filePath}_{$index}.part", "rb")) {
                  break;
               }
               while ($buff = fread($in, 4096)) {
                  fwrite($out, $buff);
               }
               Filesystem::fclose($in);
               Filesystem::deleteFile("{$filePath}_{$index}.part");
            }
            flock($out, LOCK_UN);
         }
         Filesystem::fclose($out);
         //这里是否要检测文件的大小，分片没超过限制，可能导致合并之后会超过
         return $targetFile;
      } else {
         $errorType = ErrorType::getInstance();
         //需要等待更多的数据分片
         Kernel\throw_exception(new Exception(
            $errorType->msg('E_WAIT_MORE_CHUNK', basename($targetFile)), $errorType->code('E_WAIT_MORE_CHUNK')), $errorType);
      }
   }

   /**
    * 检查目标文件是否存在
    *
    * @param string $filename
    */
   protected function checkFileExists($filename)
   {
      if (file_exists($filename)) {
         if ($this->options->getOverwrite()) {
            Filesystem::deleteFile($filename);
         } else {
            $errorType = ErrorType::getInstance();
            Kernel\throw_exception(new Exception(
               $errorType->msg('E_FILE_CANOT_OVERWRITE', $filename), $errorType->code('E_FILE_CANOT_OVERWRITE')), $errorType);
         }
      }
   }

   /**
    * 获取最终的上传文件名称，分片可能造成多次上传请求
    *
    * @param \Phalcon\Http\Request\File $uploadFile 上传文件信息数组
    */
   protected function getTargetFileName($uploadFile)
   {
      $uploadDir = CNTY_ROOT_DIR.$this->options->getUploadDir();
      //首先探测是否上传的时候指定的目标的文件夹名称
      $uploadFilename = $this->options->getTargetName();
      if (!$uploadFilename) {
         $uploadFilename = Kernel\real_path($uploadFile->getName()); //这个地方要将前台JS发来的文件名进行编码转换，中文文件名的文件上传问题解决
      }
      //这里可能有文件名称不存在的情况吗？
      return $uploadDir . DS . $uploadFilename;
   }

   /**
    * @return \Phalcon\Events\Manager
    */
   public function getEventMgr()
   {
      if(!$this->eventMgr){
         $this->eventMgr = new EventsManager();
      }
      return $this->eventMgr;
   }
}