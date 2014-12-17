<?php
/**
 * Cntysoft Cloud Software Team
 *
 * @author SOFTBOY <cntysoft@163.com>
 * @copyright  Copyright (c) 2010-2011 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license    http://www.cntysoft.com/license/new-bsd     New BSD License
 */
namespace Cntysoft\Kernel;

use Phalcon\Http\Response;

/**
 * 抛出异常
 *
 * @param \Exception $e
 * @param string $context
 *           异常上下文
 */
function throw_exception(\Exception $e, $context = null)
{
   if ($context instanceof \Cntysoft\Stdlib\ErrorType) {
      $context = normalize_errortype_context($context);
   }
   if ($context) {
      $d = g_data(\Cntysoft\API_CALL_EXP_KEY);
      if ($d === null) {
         $d = array();
      } else {
         $d = array(
            $d
         );
      }
      $d = array_merge($d, array(
         'context' => $context
      ));
      g_data(\Cntysoft\API_CALL_EXP_KEY, $d);
   }
   throw $e;
}

/**
 * 系统平台内部使用，主要用来传递异常不能传递的错误信息
 *
 * @param string $key
 * @param array $data
 * @return mixed
 */
function g_data($key, $data = null)
{
   static $_data = array();
   if (null !== $data) {
      $_data[$key] = $data;
      return $data;
   }
   if (array_key_exists($key, $_data)) {
      $value = $_data[$key];
      return $value;
   }
   return null;
}

/**
 * 规格化errorType上下文
 *
 * @param \Cntysoft\Stdlib\ErrorType $errorType
 * @return string
 */
function normalize_errortype_context(\Cntysoft\Stdlib\ErrorType $errorType)
{
   return str_replace('\\', '.', get_class($errorType));
}

/**
 * 获取系统支持的API调用种类
 *
 * @return array
 */
function get_api_call_types()
{
   return array(
      \Cntysoft\API_CALL_APP,
      \Cntysoft\API_CALL_SYS
   );
}

/**
 * 生成一个api相应数组
 *
 * @param boolean $status
 * @param array $data
 * @return \Phalcon\Response
 */
function generate_response($status, array $data)
{
   $reponse = new Response();
   $reponse->setJsonContent(array(
      'status' => (boolean) $status,
      'data' => $data
   ));
   return $reponse;
}

/**
 * 生成一个json输出
 *
 * @param array $data
 * @return \Phalcon\Response
 */
function generate_raw_response(array $data)
{
   $reponse = new Response();
   $reponse->setJsonContent($data);
   return $reponse;
}

/**
 * 生成系统错误响应数组结构
 *
 * @param string $msg
 * @return \Phalcon\Response
 */
function generate_error_response($msg, array $ext = array())
{
   /**
    * 是否检查系统级别合法性
    *
    * @todo
    *
    */
   $error = array(
      'status' => false,
      'msg' => $msg
   );
   if (! empty($ext)) {
      $error = array_merge($error, $ext);
   }
   $reponse = new Response();
   $reponse->setJsonContent($error);
   return $reponse;
}

/**
 * 获取机器特征KEY, 这个方法是否安全 到时候还要讨论
 *
 * @return string
 */
function get_trait_token()
{
   // 中间是否要加入一个随机串 然后加密呢？
   return md5(get_client_ip().get_server_env('HTTP_USER_AGENT'));
}

/**
 * 获取模型必要字段集合
 *
 * @param mixed $model
 * @param array $skip
 *           需要跳过的字段集合
 * @return array
 */
function get_model_require_fields($model, array $skip = array())
{
   $di = get_global_di();
   $md = $di->getShared('modelsMetadata');
   if (is_string($model)) {
      $model = new $model();
   }
   $requires = $md->readMetaData($model);
   $requires = $requires[3];
   $ret = array();
   foreach ($requires as $require) {
      if (!in_array($require, $skip)) {
         $ret[] = $require;
      }
   }
   return $ret;
}
/**
 * 获取随机的字符串
 * @param int       $length  要生成的随机字符串长度
 * @param boolean $specialSymbol
 * @return string
 */
function get_random_str($length = 5, $specialSymbol = false)
{
   $arr = array('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', "~@#$%^&*(){}[]|");
   if($specialSymbol){
      $string = implode('', $arr);
   }else{
      $string = $arr[0];
   }
   $count = strlen($string) - 1;
   $ret = '';
   for ($i = 0; $i < $length; $i++) {
      $ret .= $string[rand(0, $count)];
   }
   return $ret;
}

/**
 * 获取系统数据库链接信息
 *
 * @return \Phalcon\Config
 */
function get_db_connection_cfg()
{
   $g = ConfigProxy::getGlobalConfig();
   return $g->db;
}

/**
 * 获取mysql服务器的一些信息
 *
 * @return array
 */
function get_mysql_meta_info()
{
   $cfg = get_db_connection_cfg();
   $conn = new \mysqli($cfg->host, $cfg->username, $cfg->password);
   $clientInfo = explode(' ', $conn->client_info);
   $info = array(
      'server' => $conn->server_info,
      'client' => $clientInfo[0],
      'host' => $conn->host_info,
      'proto' => $conn->protocol_version
   );
   $conn->close();
   return $info;
}

/**
 * 确保扩展存在
 *
 * @param string $extension
 */
function ensure_extension($extension)
{
   if (! extension_loaded($extension)) {
      throw_exception(new Exception(StdErrorType::msg('E_EXTENSION_NOT_LOADED', $extension), StdErrorType::code('E_EXTENSION_NOT_LOADED')), \Cntysoft\STD_EXCEPTION_CONTEXT);
   }
}

/**
 * 获取系统服务器URL
 *
 * @return string
 */
function get_server_url()
{
   return $_SERVER['HTTP_HOST'];
}

/**
 * 过滤ErrorException中的敏感信息，然后重新抛出
 *
 * @param string $errorException
 */
function rethrow_error_exception(\ErrorException $e)
{
   $msg = $e->getMessage();
   $pos = strripos($msg, ':');
   $msg = trim(substr($msg, $pos + 1));
   throw new \ErrorException($msg, 0, $e->getSeverity(), $e->getFile(), $e->getLine());
}

/**
 *
 * @return string
 */
function get_orm_cache_dir()
{
   return real_path(StdDir::getCacheDir().DS.'Orm');
}

/**
 *
 * @return string
 */
function get_common_cache_dir()
{
   return real_path(StdDir::getCacheDir().DS.'Common');
}


/**
 * 清空一个数据表
 *
 * @param string $table
 */
function truncate_table($table)
{
   $db = get_db_adapter();
   $db->execute(sprintf('TRUNCATE TABLE `%s`', $table));
}