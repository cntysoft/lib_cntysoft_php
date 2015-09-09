<?php
/**
 * Cntysoft Cloud Software Team
 * 
 * @author SOFTBOY <cntysoft@163.com>
 * @copyright  Copyright (c) 2010-2011 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license    http://www.cntysoft.com/license/new-bsd     New BSD License
 */
namespace Cntysoft\Framework\Cloud\Ali\Oss;
use Cntysoft\Framework\Cloud\Ali\Oss\Utils as OssUtils;
use Cntysoft\Framework\Cloud\Ali\Oss\Constant as OSS_CONST;
use Zend\Http\Request as HttpRequest;
use Zend\Http\Response as HttpResponse;
use Zend\Http\Client as HttpClient;
use Zend\Http\Header;
use Cntysoft\Kernel;
/**
 * Oss客户端类
 */
class OssClient
{

   /**
    * OSS实例的地址
    * 
    * @var string $entry
    */
   protected $entry;

   /**
    * 访问KEY
    * 
    * @var string $accessKey
    */
   protected $accessKey;

   /**
    * 访问key密码
    * 
    * @var string $accessKeySecret
    */
   protected $accessKeySecret;

   /**
    * 临时授权令牌
    * 
    * @var string $securityToken
    */
   protected $securityToken = null;

   /**
    * @var string $enableDomainStyle
    */
   protected $enableDomainStyle = false;

   /**
    * 虚拟域名绑定
    * 
    * @var string $vhost
    */
   protected $vhost = null;

   /**
    *
    * @var int $maxRetries
    */
   protected $maxRetries = 3;

   /**
    * @var int $maxRetries
    */
   protected $redirects = 0;

   /**
    * @var array $allowOssAclTypes
    */
   protected static $allowOssAclTypes = array(
      OSS_CONST::OSS_ACL_TYPE_PRIVATE,
      OSS_CONST::OSS_ACL_TYPE_PUBLIC_READ,
      OSS_CONST::OSS_ACL_TYPE_PUBLIC_READ_WRITE
   );

   /**
    * @var array $successCodes 操作成功代码
    */
   protected static $successCodes = array(200, 201, 204, 206);

   /**
    * 是否使用安全连接
    * 
    * @var string $useSsl
    */
   protected $useSsl = false;

   /**
    * 是否允许临时的授权许可
    * 
    * @var boolean $enableStsInUrl
    */
   protected $enableStsInUrl = false;

   /**
    * @var string $requestUrl
    */
   protected $requestUrl;

   /**
    *
    * @var \Zend\Http\Client  $httpClient
    */
   protected $httpClient;

   public function __construct($entry, $accessKey, $accessKeySecret, $securityToken = null)
   {
      $this->entry = $entry;
      $this->accessKey = $accessKey;
      $this->accessKeySecret = $accessKeySecret;
      //支持sts的security token
      $this->securityToken = $securityToken;
   }

   //服务操作
   /**
    * 获取bucket列表
    * @param array $options (Optional)
    */
   public function listBucket(array $options = array())
   {
      $options[OSS_CONST::OSS_OPT_BUCKET] = '';
      $options[OSS_CONST::OSS_OPT_METHOD] = OSS_CONST::OSS_HTTP_GET;
      $options[OSS_CONST::OSS_OPT_OBJECT] = '/';
      $response = $this->requestOssApi($options);
      return $response;
   }

   //object操作

   /**
    * 通过在http body中添加内容来上传文件，适合比较小的文件
    * 根据api约定，需要在http header中增加content-length字段
    * @param string $bucket (Required)
    * @param string $object (Required)
    * @param array $options (Optional)
    */
   public function uploadFileByContent($bucket, $object, array $options = array())
   {
      $this->precheckBucket($bucket);
      $this->precheckObject($options);
      //内容校验
      OssUtils::validateContent($options);
      $contentType = $this->getMimeType($object);
      $options[OSS_CONST::OSS_OPT_BUCKET] = $bucket;
      $options[OSS_CONST::OSS_OPT_METHOD] = OSS_CONST::OSS_HTTP_PUT;
      $options[OSS_CONST::OSS_OPT_OBJECT] = $object;
      if (!isset($options[OSS_CONST::OSS_OPT_LENGTH])) {
         $options[OSS_CONST::OSS_OPT_CONTENT_LENGTH] = strlen($options[OSS_CONST::OSS_OPT_CONTENT]);
      } else {
         $options[OSS_CONST::OSS_OPT_CONTENT_LENGTH] = $options[OSS_CONST::OSS_OPT_LENGTH];
      }
      if (!isset($options[OSS_CONST::OSS_OPT_CONTENT_TYPE]) && isset($contentType) && !empty($contentType)) {
         $options[OSS_CONST::OSS_OPT_CONTENT_TYPE] = $contentType;
      }
      return $this->requestOssApi($options);
   }

   /**
    * 上传文件，适合比较大的文件
    * @param string $bucket (Required)
    * @param string $object (Required)
    * @param string $file (Required)
    * @param array $options (Optional)
    */
   public function uploadFileByFile($bucket, $object, $file, array $options = array())
   {
      $this->precheckBucket($bucket);
      $this->precheckObject($object);
      if (empty($file)) {
         OssUtils::throwException('E_OSS_FILE_PATH_IS_NOT_ALLOWED_EMPTY');
      }
      //Windows系统下进行转码
      $file = OssUtils::encodingPath($file);
      $options[OSS_CONST::OSS_OPT_FILE_UPLOAD] = $file;
      if (!file_exists($options[OSS_CONST::OSS_OPT_FILE_UPLOAD])) {
         OssUtils::throwException('OSS_FILE_NOT_EXIST',
            array(
            $options[OSS_CONST::OSS_OPT_FILE_UPLOAD]
         ));
      }
      $isCheckMd5 = $this->isCheckMd5($options);
      if ($isCheckMd5) {
         $contentMd5 = base64_encode(md5_file($options[OSS_CONST::OSS_OPT_FILE_UPLOAD],
               true));
         $options[OSS_CONST::OSS_OPT_CONTENT_MD5] = $contentMd5;
      }
      $options[OSS_CONST::OSS_OPT_METHOD] = OSS_CONST::OSS_HTTP_PUT;
      $options[OSS_CONST::OSS_OPT_BUCKET] = $bucket;
      $options[OSS_CONST::OSS_OPT_OBJECT] = $object;
      $options[OSS_CONST::OSS_OPT_CONTENT_TYPE] = $this->getMimeType($file);
      $options[OSS_CONST::OSS_OPT_CONTENT_LENGTH] = filesize($options[OSS_CONST::OSS_OPT_FILE_UPLOAD]);
      $response = $this->requestOssApi($options);
      return $response;
   }

   /**
    * 删除指定的文件
    * 
    * @param string $bucket
    * @param string $object
    * @param array $options
    */
   public function deleteObject($bucket, $object, array $options = array())
   {
      $this->precheckBucket($bucket);
      $this->precheckObject($object);
      $options[OSS_CONST::OSS_OPT_BUCKET] = $bucket;
      $options[OSS_CONST::OSS_OPT_METHOD] = OSS_CONST::OSS_HTTP_DELETE;
      $options[OSS_CONST::OSS_OPT_OBJECT] = $object;
      return $this->requestOssApi($options);
   }

   /**
    * 批量删除指定的objects
    * 
    * @param string $bucket
    * @param string $objects
    * @param array $options
    */
   public function deleteObjects($bucket, array $objects, array $options = array())
   {
      $this->precheckBucket($bucket);
      $options[OSS_CONST::OSS_OPT_METHOD] = OSS_CONST::OSS_HTTP_POST;
      $options[OSS_CONST::OSS_OPT_BUCKET] = $bucket;
      $options[OSS_CONST::OSS_OPT_OBJECT] = '/';
      $options[OSS_CONST::OSS_OPT_SUB_RESOURCE] = 'delete';
      $options[OSS_CONST::OSS_OPT_CONTENT_TYPE] = 'application/xml';
      $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><Delete></Delete>');
      // Quiet mode
      if (isset($options['quiet'])) {
         $quiet = 'false';
         if (is_bool($options['quiet'])) { //Boolean
            $quiet = $options['quiet'] ? 'true' : 'false';
         } elseif (is_string($options['quiet'])) { // String
            $quiet = ($options['quiet'] === 'true') ? 'true' : 'false';
         }
         $xml->addChild('Quiet', $quiet);
      }
      // Add the objects
      foreach ($objects as $object) {
         $sub_object = $xml->addChild('Object');
         $object = OssUtils::xmlEntityReplace($object);
         $sub_object->addChild('Key', $object);
      }
      $options[OSS_CONST::OSS_OPT_CONTENT] = $xml->asXML();
      return $this->requestOssApi($options);
   }

   /**
    * 获取value
    * 
    * @param array $options
    * @param string $key
    * @param string $default
    * @param bool $isCheckEmpty
    * @param bool $isCheckBool
    * @return bool|null
    */
   protected function getValue($options, $key, $default = null, $isCheckEmpty = false, $isCheckBool = false)
   {
      $value = $default;
      if (isset($options[$key])) {
         if ($isCheckEmpty) {
            if (!empty($options[$key])) {
               $value = $options[$key];
            }
         } else {
            $value = $options[$key];
         }
         unset($options[$key]);
      }
      if ($isCheckBool) {
         if ($value !== true && $value !== false) {
            $value = false;
         }
      }
      return $value;
   }

   /**
    * 检测md5
    * @param array $options
    * @return bool|null
    */
   protected function isCheckMd5($options)
   {
      return $this->getValue($options, OSS_CONST::OSS_OPT_CHECK_MD5, false,
            true, true);
   }

   /**
    * 获取mimetype类型
    * 
    * @param string $object
    * @return string
    */
   public function getMimeType($object)
   {
      $extension = explode('.', $object);
      $extension = array_pop($extension);
      return MimeTypes::getMimetype(strtolower($extension));
   }

   /**
    * 获取bucket下的object列表
    * @param string $bucket (Required)
    * @param array $options (Optional)
    * 其中options中的参数如下
    * $options = array(
    *      'max-keys'  => max-keys用于限定此次返回object的最大数，如果不设定，默认为100，max-keys取值不能大于100。
    *      'prefix'    => 限定返回的object key必须以prefix作为前缀。注意使用prefix查询时，返回的key中仍会包含prefix。
    *      'delimiter' => 是一个用于对Object名字进行分组的字符。所有名字包含指定的前缀且第一次出现delimiter字符之间的object作为一组元素
    *      'marker'    => 用户设定结果从marker之后按字母排序的第一个开始返回。
    * )
    * 其中 prefix，marker用来实现分页显示效果，参数的长度必须小于256字节。
    */
   public function listObject($bucket, array $options = array())
   {
      $this->precheckBucket($bucket);
      $options[OSS_CONST::OSS_OPT_BUCKET] = $bucket;
      $options[OSS_CONST::OSS_OPT_METHOD] = self::OSS_HTTP_GET;
      $options[OSS_CONST::OSS_OPT_OBJECT] = '/';
      $options[OSS_CONST::OSS_OPT_HEADERS] = array(
         self::OSS_DELIMITER => isset($options[self::OSS_DELIMITER]) ? $options[self::OSS_DELIMITER] : '/',
         self::OSS_PREFIX => isset($options[self::OSS_PREFIX]) ? $options[self::OSS_PREFIX] : '',
         self::OSS_MAX_KEYS => isset($options[self::OSS_MAX_KEYS]) ? $options[self::OSS_MAX_KEYS] : self::OSS_MAX_KEYS_VALUE,
         self::OSS_MARKER => isset($options[self::OSS_MARKER]) ? $options[self::OSS_MARKER] : '',
      );
      return $this->requestOssApi($options);
   }

   //Multi Part相关操作

   /**
    * 初始化multi-part upload
    * 
    * @param string $bucket (Required) Bucket名称
    * @param string $object (Required) Object名称
    * @param array $options (Optional) Key-Value数组
    * @return \Zend\Http\Response
    */
   public function initiateMultipartUpload($bucket, $object, array $options = array())
   {
      $this->precheckBucket($bucket);
      $this->precheckObject($object);
      $options[OSS_CONST::OSS_OPT_METHOD] = OSS_CONST::OSS_HTTP_POST;
      $options[OSS_CONST::OSS_OPT_BUCKET] = $bucket;
      $options[OSS_CONST::OSS_OPT_OBJECT] = $object;
      $options[OSS_CONST::OSS_OPT_SUB_RESOURCE] = 'uploads';
      if (!isset($options[OSS_CONST::OSS_OPT_HEADERS])) {
         $options[OSS_CONST::OSS_OPT_HEADERS] = array();
      }
      return $this->requestOssApi($options);
   }

   /**
    * 
    * @param type $bucket
    * @param type $object
    * @param array $options
    */
   public function initMultiPartUploadForUploadId($bucket, $object, array $options = array())
   {
      $response = $this->initiateMultipartUpload($bucket, $object, $options);
      if (!$this->responseIsOk($response)) {
         OssUtils::throwException('E_INIT_MULTI_PARTUPLOAD_FAIL');
      }
      $xml = new \SimpleXmlIterator($response->getContent());
      return (string) $xml->UploadId;
   }

   /**
    * 开始分块的上传
    * 
    * @param string $bucket (Required) Bucket名称
    * @param string $object (Required) Object名称
    * @param string $uploadId (Required) uploadId
    * @param array $options (Optional) Key-Value数组
    */
   public function uploadPart($bucket, $object, $uploadId, array $options = array())
   {
      $this->precheckBucket($bucket);
      $this->precheckObject($object);
      $this->precheckParam($options,
         array(
         OSS_CONST::OSS_OPT_CONTENT,
         OSS_CONST::OSS_OPT_PART_NUM
      ));
      $options[OSS_CONST::OSS_OPT_METHOD] = OSS_CONST::OSS_HTTP_PUT;
      $options[OSS_CONST::OSS_OPT_BUCKET] = $bucket;
      $options[OSS_CONST::OSS_OPT_OBJECT] = $object;
      $options[OSS_CONST::OSS_OPT_UPLOAD_ID] = $uploadId;
      if (isset($options[OSS_CONST::OSS_OPT_LENGTH])) {
         $options[OSS_CONST::OSS_OPT_CONTENT_LENGTH] = $options[OSS_CONST::OSS_OPT_LENGTH];
      }
      return $this->requestOssApi($options);
   }

   /**
    * 完成multi-part上传
    * 
    * @param string $bucket (Required) Bucket名称
    * @param string $object (Required) Object名称
    * @param string $uploadId (Required) uploadId
    * @param array  $parts 可以是一个上传成功part的数组
    * @param array $options (Optional) Key-Value数组
    */
   public function completeMultipartUpload($bucket, $object, $uploadId, array $parts, array $options = array())
   {
      $this->precheckBucket($bucket);
      $this->precheckObject($object);
      $options[OSS_CONST::OSS_OPT_METHOD] = OSS_CONST::OSS_HTTP_POST;
      $options[OSS_CONST::OSS_OPT_BUCKET] = $bucket;
      $options[OSS_CONST::OSS_OPT_OBJECT] = $object;
      $options[OSS_CONST::OSS_OPT_UPLOAD_ID] = $uploadId;
      $options[OSS_CONST::OSS_OPT_CONTENT_TYPE] = 'application/xml';
      $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><CompleteMultipartUpload></CompleteMultipartUpload>');
      foreach ($parts as $node) {
         $part = $xml->addChild('Part');
         $part->addChild('PartNumber', $node['PartNumber']);
         $part->addChild('ETag', $node['ETag']);
      }
      $options[OSS_CONST::OSS_OPT_CONTENT] = $xml->asXML();
      return $this->requestOssApi($options);
   }

   /**
    * 中止上传mulit-part upload
    * 
    * @param string $bucket (Required) Bucket名称
    * @param string $object (Required) Object名称
    * @param string $uploadId (Required) uploadId
    * @param array $options (Optional) Key-Value数组
    * @return ResponseCore
    */
   public function abortMultipartUpload($bucket, $object, $uploadId, array $options = array())
   {
      $this->precheckBucket($bucket);
      $this->precheckObject($object);
      $options[OSS_CONST::OSS_OPT_METHOD] = self::OSS_HTTP_DELETE;
      $options[OSS_CONST::OSS_OPT_BUCKET] = $bucket;
      $options[OSS_CONST::OSS_OPT_OBJECT] = $object;
      $options[OSS_CONST::OSS_OPT_UPLOAD_ID] = $uploadId;
      return $this->requestOssApi($options);
   }

   /**
    * @param array $options
    * @param array $requires
    * @throws \Cntysoft\Framework\Cloud\Ali\Oss\Exception
    */
   protected function precheckParam(array $options, array $requires)
   {
      $leak = array();
      Kernel\array_has_requires($options, $requires, $leak);
      if (!empty($leak)) {
         OssUtils::throwException('E_OPTION_REQUIRE_FILED',
            array(implode(',', $leak)));
      }
   }

   /**
    * 检查bucket是否为空
    * 
    * @param string $bucket
    * @throws \Cntysoft\Framework\Cloud\Ali\Oss\Exception
    */
   protected function precheckBucket($bucket)
   {
      if (empty($bucket)) {
         OssUtils::throwException('E_OSS_BUCKET_IS_NOT_ALLOWED_EMPTY');
      }
   }

   /**
    * 检查object是否为空
    * 
    * @param array $object
    * @throws \Cntysoft\Framework\Cloud\Ali\Oss\Exception
    */
   protected function precheckObject($object)
   {
      if (empty($object)) {
         OssUtils::throwException('E_OSS_OBJECT_IS_NOT_ALLOWED_EMPTY');
      }
   }

   /**
    * 请求OSS远端的API功能
    * 
    * @param array $opts
    * @return \Zend\Http\Response
    */
   protected function requestOssApi(array $opts)
   {
      //验证Bucket,list_bucket时不需要验证
      if (!( ('/' == $opts[OSS_CONST::OSS_OPT_OBJECT]) && ('' == $opts[OSS_CONST::OSS_OPT_BUCKET]) && ('GET' == $opts[OSS_CONST::OSS_OPT_METHOD])) && !OssUtils::validateBucket($opts[OSS_CONST::OSS_OPT_BUCKET])) {
         OssUtils::throwException('E_OSS_BUCKET_NAME_INVALID',
            array(
            $opts[OSS_CONST::OSS_OPT_BUCKET]
         ));
      }
      //验证Object
      if (isset($opts[OSS_CONST::OSS_OPT_OBJECT]) && !OssUtils::validateObject($opts[OSS_CONST::OSS_OPT_OBJECT])) {
         OssUtils::throwException('E_OSS_OBJECT_NAME_INVALID',
            array(
            $opts[OSS_CONST::OSS_OPT_OBJECT]
         ));
      }
      //Object编码为UTF-8
      $tmpObject = $opts[OSS_CONST::OSS_OPT_OBJECT];
      try {
         if (OssUtils::isGb2312($opts[OSS_CONST::OSS_OPT_OBJECT])) {
            $tmpObject = iconv('GB2312', "UTF-8//IGNORE",
               $opts[OSS_CONST::OSS_OPT_OBJECT]);
         } elseif (OssUtils::check_char($opts[OSS_CONST::OSS_OPT_OBJECT], true)) {
            $tmpObject = iconv('GBK', "UTF-8//IGNORE",
               $opts[OSS_CONST::OSS_OPT_OBJECT]);
         }
      } catch (Exception $e) {
         try {
            $tmpObject = iconv(mb_detect_encoding($tmpObject), "UTF-8",
               $tmpObject);
         } catch (Exception $e) {
            
         }
      }
      $opts[OSS_CONST::OSS_OPT_OBJECT] = $tmpObject;
      //验证ACL
      if (isset($opts[OSS_CONST::OSS_OPT_HEADERS][OSS_CONST::OSS_HEADER_ACL]) && !empty($opts[OSS_CONST::OSS_OPT_HEADERS][OSS_CONST::OSS_HEADER_ACL])) {
         if (!in_array(strtolower($opts[OSS_CONST::OSS_OPT_HEADERS][OSS_CONST::OSS_HEADER_ACL]),
               self::$OSS_ACL_TYPES)) {
            OssUtils::throwException('E_OSS_ACL_INVALID',
               array(
               $opts[OSS_CONST::OSS_OPT_HEADERS][OSS_CONST::OSS_HEADER_ACL]
            ));
         }
      }
      //定义scheme
      $scheme = $this->useSsl ? 'https://' : 'http://';
      if ($this->enableDomainStyle) {
         $hostname = $this->vhost ? $this->vhost : (($opts[OSS_CONST::OSS_OPT_BUCKET] == '') ? $this->entry : ($opts[OSS_CONST::OSS_OPT_BUCKET] . '.') . $this->entry);
      } else {
         $hostname = (isset($opts[OSS_CONST::OSS_OPT_BUCKET]) && '' !== $opts[OSS_CONST::OSS_OPT_BUCKET]) ? $opts[OSS_CONST::OSS_OPT_BUCKET] . '.' . $this->entry : $this->entry;
      }
      //请求参数
      $signableResource = '';
      $queryStringParams = array();
      $signableQueryStringParams = array();
      $stringToSign = '';

      $headers = array(
         OSS_CONST::OSS_HEADER_CONTENT_MD5 => '',
         OSS_CONST::OSS_HEADER_CONTENT_TYPE => isset($opts[OSS_CONST::OSS_OPT_CONTENT_TYPE]) ? $opts[OSS_CONST::OSS_OPT_CONTENT_TYPE] : 'application/x-www-form-urlencoded',
         OSS_CONST::OSS_HEADER_DATE => isset($opts[OSS_CONST::OSS_OPT_DATE]) ? $opts[OSS_CONST::OSS_OPT_DATE] : gmdate('D, d M Y H:i:s \G\M\T'),
         OSS_CONST::OSS_HEADER_HOST => $hostname,
      );

      if (isset($opts[OSS_CONST::OSS_OPT_CONTENT_MD5])) {
         $headers[OSS_CONST::OSS_HEADER_CONTENT_MD5] = $opts[OSS_CONST::OSS_OPT_CONTENT_MD5];
      }

      //增加stsSecurityToken
      if ((!is_null($this->securityToken)) && (!$this->enableStsInUrl)) {
         $headers[OSS_CONST::OSS_HEADER_SECURITY_TOKEN] = $this->securityToken;
      }
      if (isset($opts[OSS_CONST::OSS_OPT_OBJECT]) && '/' !== $opts[OSS_CONST::OSS_OPT_OBJECT]) {
         $signableResource = '/' . str_replace(array('%2F', '%25'),
               array('/', '%'), rawurlencode($opts[OSS_CONST::OSS_OPT_OBJECT]));
      } else {
         $signableResource = '/';
      }
      if (isset($opts[OSS_CONST::OSS_OPT_QUERY_STRING])) {
         $queryStringParams = array_merge($queryStringParams,
            $opts[OSS_CONST::OSS_OPT_QUERY_STRING]);
      }
      $queryString = OssUtils::toQueryString($queryStringParams);
      $signableList = array(
         OSS_CONST::OSS_OPT_PART_NUM,
         'response-content-type',
         'response-content-language',
         'response-cache-control',
         'response-content-encoding',
         'response-expires',
         'response-content-disposition',
         OSS_CONST::OSS_OPT_UPLOAD_ID,
      );

      foreach ($signableList as $item) {
         if (isset($opts[$item])) {
            $signableQueryStringParams[$item] = $opts[$item];
         }
      }

      if ($this->enableStsInUrl && (!is_null($this->securityToken))) {
         $signableQueryStringParams["security-token"] = $this->securityToken;
      }
      $signableQueryString = OssUtils::toQueryString($signableQueryStringParams);
      //合并 HTTP headers
      if (isset($opts[OSS_CONST::OSS_OPT_HEADERS])) {
         $headers = array_merge($headers, $opts[OSS_CONST::OSS_OPT_HEADERS]);
      }
      //生成请求URL
      $conjunction = '?';
      $nonSignableResource = '';
      if (isset($opts[OSS_CONST::OSS_OPT_SUB_RESOURCE])) {
         $signableResource .= $conjunction . $opts[OSS_CONST::OSS_OPT_SUB_RESOURCE];
         $conjunction = '&';
      }
      if ($signableQueryString !== '') {
         $signableQueryString = $conjunction . $signableQueryString;
         $conjunction = '&';
      }
      if ($queryString !== '') {
         $nonSignableResource .= $conjunction . $queryString;
         $conjunction = '&';
      }
      $httpClient = $this->getHttpClient();
      $request = new HttpRequest();
      $httpHeaders = $request->getHeaders();
      //var_dump($signableResource);
      $this->requestUrl = $scheme . $hostname . $signableResource . $signableQueryString . $nonSignableResource;
      $request->setUri($this->requestUrl);
      $userAgent = 'cntysoft-oss-client' . " (" . php_uname('s') . "/" . php_uname('r') . "/" . php_uname('m') . ";" . PHP_VERSION . ")";
      $httpHeaders->addHeader(new Header\UserAgent($userAgent));
      // Streaming uploads
      if (isset($opts[OSS_CONST::OSS_OPT_FILE_UPLOAD])) {
         $fstrem = fopen($opts[OSS_CONST::OSS_OPT_FILE_UPLOAD], 'r');
         $request->setContent($fstrem);
         $headers[OSS_CONST::OSS_HEADER_CONTENT_TYPE] = 'application/x-www-form-urlencoded';
         $length = filesize($opts[OSS_CONST::OSS_OPT_FILE_UPLOAD]);
         if (isset($headers[OSS_CONST::OSS_OPT_CONTENT_TYPE]) && ($headers[OSS_CONST::OSS_OPT_CONTENT_TYPE] === 'application/x-www-form-urlencoded')) {
            $mimeType = $this->getMimeType($opts[OSS_CONST::OSS_OPT_FILE_UPLOAD]);
            $headers[OSS_CONST::OSS_OPT_CONTENT_TYPE] = $mimeType;
         }
         if (isset($opts[OSS_CONST::OSS_OPT_CONTENT_LENGTH])) {
            $length = $opts[OSS_CONST::OSS_OPT_CONTENT_LENGTH];
         }
         $headers[OSS_CONST::OSS_HEADER_CONTENT_LENGTH] = $length;
      }

//      if (isset($opts[self::OSS_FILE_DOWNLOAD])) {
//         if (is_resource($opts[self::OSS_FILE_DOWNLOAD])) {
//            $request->set_write_stream($opts[self::OSS_FILE_DOWNLOAD]);
//         } else {
//            $request->set_write_file($opts[self::OSS_FILE_DOWNLOAD]);
//         }
//      }

      if (isset($opts[OSS_CONST::OSS_OPT_METHOD])) {
         $request->setMethod($opts[OSS_CONST::OSS_OPT_METHOD]);
         $stringToSign .= $opts[OSS_CONST::OSS_OPT_METHOD] . "\n";
      }

      if (isset($opts[OSS_CONST::OSS_OPT_CONTENT])) {
         $request->setContent($opts[OSS_CONST::OSS_OPT_CONTENT]);
         if ($headers[OSS_CONST::OSS_HEADER_CONTENT_TYPE] === 'application/x-www-form-urlencoded') {
            $headers[OSS_CONST::OSS_HEADER_CONTENT_TYPE] = 'application/octet-stream';
         }

         $headers[OSS_CONST::OSS_HEADER_CONTENT_LENGTH] = strlen($opts[OSS_CONST::OSS_OPT_CONTENT]);
         $headers[OSS_CONST::OSS_HEADER_CONTENT_MD5] = base64_encode(md5($opts[OSS_CONST::OSS_OPT_CONTENT],
               true));
      }
      uksort($headers, 'strnatcasecmp');
      foreach ($headers as $headerKey => $headerValue) {
         $headerValue = str_replace(array("\r", "\n"), '', $headerValue);
         if ($headerValue !== '') {
            $httpHeaders->addHeaderLine($headerKey, $headerValue);
         }

         if (
            strtolower($headerKey) === 'content-md5' ||
            strtolower($headerKey) === 'content-type' ||
            strtolower($headerKey) === 'date' ||
            (isset($opts[OSS_CONST::OSS_OPT_PREAUTH]) && (integer) $opts[OSS_CONST::OSS_OPT_PREAUTH] > 0)
         ) {
            $stringToSign .= $headerValue . "\n";
         } elseif (substr(strtolower($headerKey), 0, 6) === OSS_CONST::OSS_DEFAULT_PREFIX) {
            $stringToSign .= strtolower($headerKey) . ':' . $headerValue . "\n";
         }
      }

      $stringToSign .= '/' . $opts[OSS_CONST::OSS_OPT_BUCKET];
      $stringToSign .= $this->enableDomainStyle ? ($opts[OSS_CONST::OSS_OPT_BUCKET] != '' ? ($opts[OSS_CONST::OSS_OPT_OBJECT] == '/' ? '/' : '') : '') : '';
      $stringToSign .= rawurldecode($signableResource) . urldecode($signableQueryString);
      $signature = base64_encode(hash_hmac('sha1', $stringToSign,
            $this->accessKeySecret, true));
      $httpHeaders->addHeaderLine('Authorization',
         'OSS ' . $this->accessKey . ':' . $signature);
      if (isset($opts[OSS_CONST::OSS_OPT_PREAUTH]) && (integer) $opts[OSS_CONST::OSS_OPT_PREAUTH] > 0) {
         $signedUrl = $this->requestUrl . $conjunction . OSS_CONST::OSS_URL_ACCESS_KEY_ID . '=' . rawurlencode($this->accessKey) . '&' . OSS_CONST::OSS_URL_EXPIRES . '=' . $opts[OSS_CONST::OSS_PREAUTH] . '&' . OSS_CONST::OSS_URL_SIGNATURE . '=' . rawurlencode($signature);
         return $signedUrl;
      } elseif (isset($opts[OSS_CONST::OSS_OPT_PREAUTH])) {
         return $this->requestUrl;
      }
//      var_dump($this->requestUrl);
//      var_dump($httpHeaders->toString());
      $request->setHeaders($httpHeaders);
      $httpClient->setRequest($request);
      $response = $httpClient->send();
      $responseHeaders = $response->getHeaders();
      $responseHeaders->addHeaderLine('oss-request-url', $this->requestUrl);
      $responseHeaders->addHeaderLine('oss-redirects', $this->redirects);
      $responseHeaders->addHeaderLine('oss-stringtosign', $stringToSign);
      $responseHeaders->addHeaderLine('oss-requestheaders', $stringToSign);

      //retry if OSS Internal Error
      if ((integer) $response->getStatusCode() === 500) {
         if ($this->redirects <= $this->maxRetries) {
            //设置休眠
            $delay = (integer) (pow(4, $this->redirects) * 100000);
            usleep($delay);
            $this->redirects++;
            $response = $this->requestOssApi($opts);
         }
      }
      $this->redirects = 0;
      //var_dump($response);
      return $response;
   }

   /**
    * @param HttpResponse $response
    * @return boolean
    */
   protected function responseIsOk(HttpResponse $response)
   {
      return in_array($response->getStatusCode(), self::$successCodes) ? true : false;
   }

   /**
    * @return \Zend\Http\Client
    */
   protected function getHttpClient()
   {
      if (null == $this->httpClient) {
         $this->httpClient = new HttpClient();
      }
      return $this->httpClient;
   }

}