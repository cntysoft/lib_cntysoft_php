<?php
/**
 * Cntysoft Cloud Software Team
 *
 * @author Arvin <cntyfeng@163.com>
 * @copyright  Copyright (c) 2010-2011 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license    http://www.cntysoft.com/license/new-bsd     New BSD License
 */
namespace Cntysoft\Framework\Net;

use Cntysoft\StdModel\Config;
use Qiniu\Auth;
use Qiniu\Storage\UploadManager;
use Cntysoft\Kernel;
use Cntysoft\Kernel\ConfigProxy;

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
   }

   /**
    * 获取auth的对象
    *
    * @return null|Auth
    */
   public function getAuth()
   {
      if(null == self::$auth){
         self::$auth = new Auth($this->accessKey, $this->secretKey);
      }

      return self::$auth;
   }

   /**
    * 获取上传凭证
    *
    * @param $bucket
    * @param null $key
    * @param int $expires
    * @param null $policy
    * @return string
    */
   public function getUpToken($expires = 3600, $policy = null)
   {
      return $this->getAuth()->uploadToken($this->bucket, null, $expires, $policy);
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
    * @throws \Exception
    */
   public function uploadFile($upToken, $key, $filePath, $params = null, $mime = 'application/octet-stream', $checkCrc = false)
   {
      $uploadManager = new UploadManager();
      list($ret, $err) = $uploadManager->putFile($upToken, $key, $filePath, $params, $mime, $checkCrc);

      if($err!==null){
         return array(
            'filename' => null,
            'code' => $err->code()
         );
      }else{
         return array(
            'filename' => $this->baseUrl . '/' . $ret['key'],
            'code' => 200
         );
      }
   }

   /**
    * 获取appCaller对象
    *
    * @return mixed|null
    */
   protected function getAppCaller()
   {
      if(null == self::$appCaller){
         $di = Kernel\get_global_di();
         self::$appCaller = $di->getShared('AppCaller');
      }

      return self::$appCaller;
   }
}