<?php
/**
 * Cntysoft Cloud Software Team
 * 
 * @author Arvin <cntyfeng@163.com>
 * @copyright Copyright (c) 2010-2015 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license http://www.cntysoft.com/license/new-bsd     New BSD License
*/
namespace Cntysoft\Framework\Net\Sms;

use Cntysoft\Kernel\ConfigProxy;
use Zend\Json\Json;

class YunPian
{
   protected $apikey = null;
   
   public function __construct($apikey = null)
   {
      if(null == $apikey){
         $config = new ConfigProxy();
         $cfg = $config::getFrameworkConfig('Net');
         if($cfg['yunpian'] && $cfg['yunpian']['apikey']){
            $this->apikey = $cfg['yunpian']['apikey'];
         }
      }else {
         $this->apikey = $apikey;
      }
   }
   
   /**
    * 发送短信的具体实现方法
    * 
    * @param string $url
    * @param string $query
    * @return string
    */
   public function sockPost($url, $query)
   {
      $data = "";
      $info = parse_url($url);
      $fp = fsockopen($info["host"], 80, $errno, $errstr, 30);
      if(!$fp){
         return $data;
      }
      $head = "POST " . $info['path'] . " HTTP/1.0\r\n";
      $head .= "Host: " . $info['host'] . "\r\n";
      $head .= "Referer: http://" . $info['host'] . $info['path'] . "\r\n";
      $head .= "Content-type: application/x-www-form-urlencoded\r\n";
      $head .= "Content-Length: " . strlen(trim($query)) . "\r\n";
      $head .= "\r\n";
      $head .= trim($query);
      $write = fputs($fp,$head);
      $header = "";
      while ($str = trim(fgets($fp, 4096))) {
         $header .= $str;
      }
      while (!feof($fp)) {
         $data .= fgets($fp,4096);
      }
      return $data;
   }


   /**
    * 使用已有的模板发送短信
    * 
    * @param integer $tplId
    * @param string $tplValue <p>
    * 参数的格式  #key1#=value1&#key2#=value2
    * </p>
    * @param array $phones <p>
    * 发送短信的手机号码, 支持多个同时发送
    * </p>
    * @return array
    */
   public function tplSendSms($tplId, $tplValue, array $phones)
   {
      $url = Constant::BASE_URL . '/' . Constant::VERSION . '/sms/tpl_send.json';
      $tplValue = urlencode($tplValue);
      $mobile = implode(',', $phones);
      $query = "apikey=" . $this->getApiKey() . '&tpl_id=' . $tplId . '&mobile=' .$mobile .'&tpl_value=' . $tplValue;
      
      $ret =  $this->sockPost($url, $query);
      return Json::decode($ret, Json::TYPE_ARRAY);
   }
   
   /**
    * 获取apikey值
    * 
    * @return string
    */
   public function getApiKey()
   {
      return $this->apikey;
   }
}