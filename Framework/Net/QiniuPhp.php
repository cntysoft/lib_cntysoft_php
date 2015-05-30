<?php
/**
 * Cntysoft Cloud Software Team
 *
 * @author Arvin <cntyfeng@163.com>
 * @copyright  Copyright (c) 2010-2011 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license    http://www.cntysoft.com/license/new-bsd     New BSD License
 */
namespace Cntysoft\Framework\Net;

use App\Sys\User\Exception;
use Cntysoft\StdModel\Config;
use Qiniu\Auth;
use Qiniu\Storage\UploadManager;
use Qiniu\Storage\BucketManager;
use Cntysoft\Kernel;
use Cntysoft\Kernel\ConfigProxy;
use Cntysoft\Framework\Core\FileRef\Manager as FileRefManager;

class QiniuPhp
{
   /**
    * appCaller存储对象
    *
    * @var null
    */
   protected static $appCaller = null;

   /**
    * auth的存储对象
    *
    * @var null
    */
   protected static $auth = null;

   /**
    * 七牛云密钥AK
    *
    * @var null
    */
   protected $accessKey = null;

   /**
    * 七牛云密钥SK
    *
    * @var null
    */
   protected $secretKey = null;

   /**
    * 七牛云中分配的空间名
    *
    * @var null
    */
   protected $bucket = null;

   /**
    * 七牛云中的回调配置
    *
    * @var null
    */
   protected $policy = null;

   /**
    * 浏览图片的基础地址
    *
    * @var null
    */
   protected $baseUrl = null;

   public function __construct()
   {
      $config = new ConfigProxy();
      $qiniuConfig = $config->getFrameworkConfig('Net');
      $this->accessKey = $qiniuConfig['qiniu']['accessKey'];
      $this->secretKey = $qiniuConfig['qiniu']['secretKey'];
      $this->bucket = $qiniuConfig['qiniu']['bucket'];
      $this->baseUrl = $qiniuConfig['qiniu']['baseUrl'];
      $this->policy = $qiniuConfig['qiniu']['policy'];
   }

   /**
    * 获取auth的对象
    *
    * @return null|Auth
    */
   public function getAuth()
   {
      if (null == self::$auth) {
         self::$auth = new Auth($this->accessKey, $this->secretKey);
      }

      return self::$auth;
   }

   /**
    * 获取上传凭证
    *
    * @param int $expires
    * @param null $policy
    * @return string
    */
   public function getUpToken($expires = 3600, $policy = null)
   {
      $policy = $policy ? $policy : $this->policy;
      return $this->getAuth()->uploadToken($this->bucket, null, $expires, $policy);
   }

   /**
    *  获取当前Bucket对应的网址
    *
    * @return null | string
    */
   public function getBaseUrl()
   {
      return $this->baseUrl;
   }

   /**
    * 上传图片到七牛
    *
    * @param $upToken
    * @param $key
    * @param $filePath
    * @param null $params
    * @param string $mime
    * @param bool $checkCrc
    * @return array
    * @throws \Exception
    */
   public function uploadFile($upToken, $key, $filePath, $params = null, $mime = 'application/octet-stream', $checkCrc = false)
   {
      $uploadManager = new UploadManager();
      list($ret, $err) = $uploadManager->putFile($upToken, $key, $filePath, $params, $mime, $checkCrc);

      if ($err !== null) {
         return array(
            'filename' => null,
            'code' => $err->code()
         );
      } else {
         return array(
            'filename' => $this->baseUrl . '/' . $ret['key'],
            'code' => 200
         );
      }
   }

   /**
    * 七牛云存储的回调函数，将上传的图片采取文件引用管理
    *
    * @param \Phalcon\Http\Request $request
    * @return array
    * @throws \Exception
    */
   public function handlerCallback($request)
   {
      if (!$request->isPost()) {
         $errorType = ErrorType::getInstance();
         Kernel\throw_exception(new Exception(
            $errorType->msg('E_QINIU_CALLBACK_TYPE_ERROR'), $errorType->code('E_QINIU_CALLBACK_TYPE_ERROR')), $errorType);
      }

      $params = $request->getPost();
      $manager = new FileRefManager();
      $fileName = $params['key'];
      $fileSize = $params['size'];
      $rid = $manager->addTempFileRef(array(
         'fileName' => $fileName,
         'fileSize' => $fileSize
      ));
      return array(
         'rid' => $rid,
         'fileName' => $this->baseUrl . '/' . $fileName
      );
   }

   /**
    * 删除七牛云的一个文件
    *
    * @param $key
    * @return \Qiniu\Storage\成功返回NULL
    * @throws Exception
    */
   public function deleteFile($key)
   {
      $bucketManager = new BucketManager($this->getAuth());
      $ret = $bucketManager->delete($this->bucket, $key);
      if(!is_null($ret)) {//返回值不是NULL，表示删除出错
         throw new Exception($ret);
      }

      return $ret;
   }

   /**
    * 获取appCaller对象
    *
    * @return mixed|null
    */
   protected function getAppCaller()
   {
      if (null == self::$appCaller) {
         $di = Kernel\get_global_di();
         self::$appCaller = $di->getShared('AppCaller');
      }

      return self::$appCaller;
   }
}