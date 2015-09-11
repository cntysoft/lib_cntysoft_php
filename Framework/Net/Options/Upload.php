<?php
/**
 * Cntysoft Cloud Software Team
 *
 * @author SOFTBOY <cntysoft@163.com>
 * @copyright  Copyright (c) 2010-2011 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license    http://www.cntysoft.com/license/new-bsd     New BSD License
 */
namespace Cntysoft\Framework\Net\Options;
use Cntysoft\Framework\Net\ErrorType;
use Cntysoft\Stdlib\AbstractOption;
use Cntysoft\Kernel\ConfigProxy;
use Cntysoft\Framework\Net\Exception;
use Cntysoft\Stdlib\Filesystem;
use Cntysoft\Kernel;
/**
 * 文件上传处理类配置对象
 */
class Upload extends AbstractOption
{
   /**
    * @var array $allowedDirs
    */
   protected $allowedDirs = array();
   /**
    * 这个应该探测
    *
    * @var int $maxFileSize
    */
   protected $maxFileSize = 104857600; //默认100M
   /**
    * 允许的文件类型, 以后这个可以从配置中读取
    *
    * @var array $allowFileTypes
    */
   protected $allowFileTypes = array('jpg', 'jpeg', 'gif', 'png', 'doc', 'docx', 'rar', 'zip', '.tar.gz', '.bz2', 'html', 'css', 'js');
   /**
    * 上传文件目标文件夹
    *
    * @var string $uploadDir
    */
   protected $uploadDir;
   /**
    * 文件存在是否直接覆盖
    *
    * @var boolean $overwrite
    */
   protected $overwrite = false;
   /**
    * 是否给文件名称加上一串随机码
    *
    * @var boolean $randomize
    */
   protected $randomize = false;
   /**
    * 是否根据日期创建子文件夹
    *
    * @var boolean $createSubDir
    */
   protected $createSubDir = false;
   /**
    * 指定的上传之后的文件名称
    *
    * @var string $targetName
    */
   protected $targetName = null;
   /**
    * 当前的分段ID
    *
    * @var int  $chunk
    */
   protected $chunk = null;
   /**
    * 总共的分段数
    *
    * @var int $totalChunk
    */
   protected $totalChunk = null;

   /**
    * @var boolean $enableFileRef
    */
   protected $enableFileRef = null;
   /**
    * 是否使用开放储存
    * 
    * @var boolean $useOss
    */
   protected $useOss = true;
   /**
    * 上传组件配置信息
    *
    * @param array $options
    */
   public function __construct(array $options = array())
   {
      $netConfig = ConfigProxy::getFrameworkConfig('Net', ConfigProxy::C_TYPE_FRAMEWORK_SYS);
      $uploadCfg = isset($netConfig->upload) ? $netConfig->upload->toArray() : array();
      $options += $uploadCfg;
      $options['totalChunk'] = $options['total_chunk'];
      unset($options['total_chunk']);
      if(isset($options['allowedDirs'])){
         $this->setAllowedDirs($options['allowedDirs']);
      }

      parent::__construct($options);
      //字段相容性
      if ($this->getTargetName()) {
         $this->setRandomize(false);
      }
   }

   public function getMaxFileSize()
   {
      return $this->maxFileSize;
   }

   public function setMaxFileSize($maxFileSize)
   {
      $this->maxFileSize = $maxFileSize;
   }

   public function getAllowFileTypes()
   {
      return $this->allowFileTypes;
   }

   public function setAllowFileTypes($allowFileTypes)
   {
      $this->allowFileTypes = $allowFileTypes;
   }

   public function getUploadDir()
   {
      if (null == $this->uploadDir) {
         $errorType = ErrorType::getInstance();
         Kernel\throw_exception(new Exception(
            $errorType->msg('E_UPLOAD_PATH_EMPTY'),
            $errorType->code('E_UPLOAD_PATH_EMPTY')
         ), $errorType);
      }
      $path = $this->uploadDir;
      if ($this->createSubDir) {
         if (PHP_OS == \Cntysoft\WINDOWS) {
            $path .= DS . date('Y' . DS . DS . 'm' . DS . DS . 'd');
         } else {
            $path .= DS . date('Y' . DS . 'm' . DS . 'd');
         }
         $realPath = CNTY_ROOT_DIR.$path;
         if (!file_exists($realPath)) {
            Filesystem::createDir($realPath, 0755, true);
         }
      }
      return $path;
   }

   public function getCreateSubDir()
   {
      return $this->createSubDir;
   }

   public function setCreateSubDir($createSubDir)
   {
      $this->createSubDir = $createSubDir;
   }

   public function setUploadDir($uploadDir)
   {
      $isOk = false;
      foreach ($this->allowedDirs as $dir) {
         $dir = Kernel\real_path($dir);
         if ($dir == substr($uploadDir, 0, strlen($dir))) {
            $isOk = true;
            break;
         }
      }
      if (!$isOk) {
         $errorType = ErrorType::getInstance();
         Kernel\throw_exception(new Exception(
            $errorType->msg('E_UPLOAD_DIR_NOT_ALLOWED', $uploadDir), $errorType->code('E_UPLOAD_DIR_NOT_ALLOWED')
         ), $errorType);
      }
      $this->uploadDir = $uploadDir;
   }

   public function getEnableNail()
   {
      return $this->enableNail;
   }

   public function setEnableNail($enableNail)
   {
      $this->enableNail = $enableNail;
      return $this;
   }

   public function getOverwrite()
   {
      return $this->overwrite;
   }

   public function setOverwrite($overwrite)
   {
      $this->overwrite = $overwrite;
   }

   public function getRandomize()
   {
      return $this->randomize;
   }

   public function setRandomize($randomize)
   {
      $this->randomize = $randomize;
   }

   public function setTargetName($targetName)
   {
      $this->targetName = $targetName;
   }

   public function getTargetName()
   {
      return $this->targetName;
   }

   public function setChunk($chunk)
   {
      $this->chunk = (int) $chunk;
   }

   public function getChunk()
   {
      return $this->chunk;
   }

   public function setTotalChunk($num)
   {
      $this->totalChunk = (int) $num;
   }

   public function getTotalChunk()
   {
      return $this->totalChunk;
   }

   public function getAllowedDirs()
   {
      return $this->allowedDirs;
   }

   public function setAllowedDirs(array $dirs)
   {
      $this->allowedDirs = $dirs;
      return $this;
   }

   public function addAllowedDir($dir)
   {
      $this->allowedDirs[] = $dir;
      return $this;
   }

   public function setEnableFileRef($flag)
   {
      $this->enableFileRef = (boolean)$flag;
      return $this;
   }

   public function getEnableFileRef()
   {
      return $this->enableFileRef;
   }
   
   function getUseOss()
   {
      return $this->useOss;
   }

   function setUseOss($useOss)
   {
      $this->useOss = (boolean)$useOss;
   }


}