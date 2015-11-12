<?php
/**
 * Cntysoft Cloud Software Team
 *
 * @author SOFTBOY <cntysoft@163.com>
 * @copyright  Copyright (c) 2010-2011 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license    http://www.cntysoft.com/license/new-bsd     New BSD License
 */
namespace Cntysoft\Kernel;
use Phalcon\Crypt;
/**
 * COOKIE管理类,单个函数实现单个功能
 */
class CookieManager
{
   /**
    * 默认的COOKIE前缀
    *
    * @var string $prefix
    */
   protected $prefix = '';
   /**
    * @var boolean $encypt
    */
   protected $encrypt = false;
   /**
    * @var string $encyptKey
    */
   protected $encryptKey = '';
   /**
    * @var string $domain
    */
   protected $domain = '/';
   /**
    * 是否只能通过HTTP连接
    *
    * @var boolean $httponly
    */
   protected $httponly = false;
   /**
    * 安全的连接标志
    *
    * @var boolean $secure
    */
   protected $secure = false;
   /**
    * COOKIE的默认路径
    *
    * @var string $path
    */
   protected $path = '';
   /**
    * COOKIE加密对象
    *
    * @var \Phalcon\Crypt $encrypter
    */
   protected $encrypter = null;

   /**
    * @param array $config
    */
   public function __construct(array $config = array())
   {
      if (!empty($config)) {
         $this->secure = $_SERVER['SERVER_PORT'] == 443 ? true : false;
         $this->setConfigFromArray($config);
      }
   }

   /**
    * 获取指定名称
    *
    * @param string $key
    * @param string $prefix
    * @return string
    */
   public function getCookie($key, $prefix = null)
   {
      if (null == $prefix) {
         $prefix = $this->prefix;
      }
      $key = $prefix.$key;
      $value = isset($_COOKIE[$key]) ? $_COOKIE[$key] : null; // 获取指定Cookie
      if ($value && $this->encrypt) {
         $encrypter = $this->getEncrypter();
         $value = $encrypter->decrypt($value);
      }
      return $value;
   }

   /**
    * 设置一个COOKIES变量
    *
    * @param string $key
    * @param string $value
    * @param int $life
    * @param string $prefix
    * @param boolean $httponly
    * @return \Cntysoft\KernelCookieManager
    */
   public function setCookie($key, $value = '', $life = null, $domain = null, $prefix = null, $httponly = null)
   {
      $prefix = null == $prefix ? $this->prefix : $prefix;
      $httponly = null == $httponly ? $this->httponly : $httponly;
      /* 我们的系统必须运行在PHP >= 5.3.12 */

      $expire = $life !== null ? time() + intval($life) : null;
      if ($this->encrypt) {
         $encrypter = $this->getEncrypter();
         $value = $encrypter->encrypt($value);
      }
      $key = $prefix.$key;
      setcookie($key, $value, $expire, $this->path, $domain, $this->secure, $httponly);
      $_COOKIE[$key] = $value;
      return $this;
   }

   /**
    * 删除指定的Cookie
    *
    * @param string $key
    * @param string $prefix
    */
   public function deleteCookie($key, $domain = null, $prefix = null, $httponly = null)
   {
      if (null == $prefix) {
         $prefix = $this->prefix;
      }
      $key = $prefix.$key;
      $httponly = null == $httponly ? $this->httponly : $httponly;
      setcookie(
         $key, '', time() - 3600, $this->path, $domain, $this->secure, $httponly
      );
      unset($_COOKIE[$key]);
   }

   /**
    * 清除所有的COOKIE
    */
   public function clearCookie($domain)
   {
      if (!empty($_COOKIE)) {
         foreach ($_COOKIE as $key => $v) {
            setcookie(
               $key, '', time() - 3600, $this->path, $domain, $this->secure, $this->httponly
            );
            unset($_COOKIE[$key]);
         }
      }
   }

   /**
    * 清楚指定前缀的所有的Cookies
    *
    * @param string $prefix
    */
   public function clearCookieByPrefix($prefix = null)
   {
      if (null == $prefix) {
         $prefix = $this->prefix;
      }
      foreach ($_SESSION as $key => $v) {
         if (0 == stripos($prefix, $key)) {
            setcookie(
               $key, '', time() - 3600, $this->path, $this->domain, $this->secure, $this->httponly
            );
            unset($_COOKIE[$key]);
         }
      }
   }

   /**
    * @param string $prefix
    * @return \Cntysoft\KernelCookieManager
    */
   public function setPrefix($prefix)
   {
      if (!is_string($prefix)) {
         throw_exception(new Exception(
            StdErrorType::msg('E_ARG_TYPE_ERROR', 'string'), StdErrorType::code('E_ARG_TYPE_ERROR')), \Cntysoft\STD_EXCEPTION_CONTEXT);
      }
      $this->prefix = $prefix;
      return $this;
   }

   /**
    * @return string
    */
   public function getPrefix()
   {
      return $this->prefix;
   }

   /**
    * 设置是否对COOKIE加密
    *
    * @param boolean $flag
    * @return \Cntysoft\KernelCookieManager
    */
   public function setEncrypt($flag)
   {
      $this->encrypt = (boolean) $flag;
      return $this;
   }

   /**
    * @return boolean
    */
   public function getEncrypt()
   {
      return $this->encrypt;
   }

   /**
    * 设置COOKIE加密密码
    *
    * @param string $key
    * @param boolean $setOnEncrypter 防止首次设置
    * @return \Cntysoft\KernelCookieManager
    */
   public function setEncryptKey($key, $setOnEncrypter = false)
   {
      if (!is_string($key)) {
         throw_exception(new Exception(
            StdErrorType::msg('E_ARG_TYPE_ERROR', 'string'), StdErrorType::code('E_ARG_TYPE_ERROR')), \Cntysoft\STD_EXCEPTION_CONTEXT);
      }
      $this->encryptKey = $key;
      if ($setOnEncrypter) {
         $encrypter = $this->getEncrypter();
         $encrypter->setKey($key);
      }
      return $this;
   }

   /**
    * @return string
    */
   public function getEncryptKey()
   {
      return $this->encryptKey;
   }

   /**
    * @param string $domain
    * @return \Cntysoft\KernelCookieManager
    */
   public function setDomain($domain)
   {
      if ($domain && !is_string($domain)) {
         throw_exception(new Exception(
            StdErrorType::msg('E_ARG_TYPE_ERROR', 'string'), StdErrorType::code('E_ARG_TYPE_ERROR')), \Cntysoft\STD_EXCEPTION_CONTEXT);
      }
      $this->domain = $domain;
      return $this;
   }

   /**
    * @return string
    */
   public function getDomain()
   {
      return $this->domain;
   }

   /**
    * @param boolean $flag
    * @return \Cntysoft\KernelCookieManager
    */
   public function setHttpOnly($flag)
   {
      $this->httponly = (boolean) $flag;
      return $this;
   }

   /**
    * @return boolean
    */
   public function getHttpOnly()
   {
      return $this->httponly;
   }

   /**
    * @param string $path
    * @return \Cntysoft\KernelCookieManager
    */
   public function setPath($path)
   {
      if (!is_string($path)) {
         throw_exception(new Exception(
            StdErrorType::msg('E_ARG_TYPE_ERROR', 'string'), StdErrorType::code('E_ARG_TYPE_ERROR')), \Cntysoft\STD_EXCEPTION_CONTEXT);
      }
      $this->path = $path;
      return $this;
   }

   /**
    * @return string
    */
   public function getPath()
   {
      return $this->path;
   }

   /**
    * @return boolean
    */
   public function getSecure()
   {
      return $this->secure;
   }

   /**
    * @param int $life
    * @return \Cntysoft\KernelCookieManager
    */
   public function setLife($life)
   {
      if (!is_int($life)) {
         throw_exception(new Exception(
            StdErrorType::msg('E_ARG_TYPE_ERROR', 'int'), StdErrorType::code('E_ARG_TYPE_ERROR')), \Cntysoft\STD_EXCEPTION_CONTEXT);
      }
      $this->life = $life;
      return $this;
   }

   /**
    * 获取Cookie生存周期
    *
    * @return int
    */
   public function getLife()
   {
      return $this->life;
   }

   /**
    * @param array $config
    * @return \Cntysoft\Kernel\CookieManager
    */
   public function setConfigFromArray(array $config)
   {
      if (!is_array($config) && !$config instanceof \Traversable) {
         throw_exception(new Exception(
            StdErrorType::msg('E_ARG_TYPE_ERROR', 'array or Traversable'), StdErrorType::code('E_ARG_TYPE_ERROR')), \Cntysoft\STD_EXCEPTION_CONTEXT);
      }
      invoke_setter($this, $config);
      return $this;
   }

   /**
    * @return \Phalcon\Crypt
    */
   protected function getEncrypter()
   {
      if (null == $this->encrypter) {
         /**
          * 检查密码的正确性
          */
         if (!is_string($this->encryptKey)) {
            throw_exception(new Exception(
               StdErrorType::msg('E_ARG_TYPE_ERROR', 'string'), StdErrorType::code('E_ARG_TYPE_ERROR')), \Cntysoft\STD_EXCEPTION_CONTEXT);
         }
         $this->encrypter = new Crypt();
         $this->encrypter->setKey($this->encryptKey);
      }
      return $this->encrypter;
   }

}