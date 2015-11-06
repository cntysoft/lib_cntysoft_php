<?php
/**
 * Cntysoft Cloud Software Team
 * 
 * @author Arvin <cntyfeng@163.com>
 * @copyright Copyright (c) 2010-2015 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license http://www.cntysoft.com/license/new-bsd     New BSD License
 */
namespace Cntysoft\Framework\Pay\WeChat;
use Cntysoft\Kernel;

/**
 * 数据对象基础类，该类中定义数据类行为，包括：
 * 计算/设置/获取签名、输出xml格式的参数、从xml读取数据对象等
 */
class Utils
{
   /**
    * 保存各项配置参数
    * @var array 
    */
   protected $values = array();

   /**
    * 为values设置指定的键值
    * 
    * @param string $key
    * @param string $value
    * @return array
    */
   public function setValue($key, $value)
   {
      $this->values[$key] = $value;

      return $this;
   }

   /**
    * 获取values中指定键值key的值
    * 
    * @param string $key
    * @return string
    */
   public function getValue($key)
   {
      $values = $this->getValues();
      if ($this->isValueExist($key)) {
         return $values[$key];
      }
   }

   /**
    * 判断指定key在values是否存在
    * 
    * @param string $key
    * @return boolean
    */
   public function isValueExist($key)
   {
      $values = $this->getValues();
      return array_key_exists($key, $values);
   }

   /**
    * 获取values的值
    * 
    * @return array
    */
   public function getValues()
   {
      return $this->values;
   }

   /**
    * 为values赋值
    * 
    * @param array $values
    */
   public function setValues(array $values)
   {
      $this->values = $values;
   }
   
   /**
    * 将array转换成xml数据
    * 
    * @return string
    */
   public function arrayToXml($values)
   {
      if (!is_array($values) || count($values) < 0) {
         $errorType = ErrorType::getInstance();
         Kernel\throw_exception(new Exception(
                 $errorType->msg('E_PAY_WECHAT_DATA_ARRAY_ERROR'), $errorType->code('E_PAY_WECHAT_DATA_ARRAY_ERROR')
                 ), $errorType);
      }

      $xml = "<xml>";
      foreach ($this->values as $key => $val) {
         if (is_numeric($val)) {
            $xml.="<" . $key . ">" . $val . "</" . $key . ">";
         } else {
            $xml.="<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
         }
      }
      $xml.="</xml>";
      return $xml;
   }

   /**
    * 从xml获取array数据，并赋值给values
    * 
    * @param string $xml
    * @return array
    */
   public function arrayFromXml($xml)
   {
      if (!$xml) {
         $errorType = ErrorType::getInstance();
         Kernel\throw_exception(new Exception(
                 $errorType->msg('E_PAY_WECHAT_DATA_XML_ERROR'), $errorType->code('E_PAY_WECHAT_DATA_XML_ERROR')
                 ), $errorType);
      }
      //将XML转为array
      //禁止引用外部xml实体
      libxml_disable_entity_loader(true);
      $values = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
      $this->setValues($values);
      
      return $this->getValues();
   }

   /**
    * 格式化参数格式化成url参数
    */
   public function toUrlParams($values)
   {
      $buff = "";
      foreach ($values as $key => $value) {
         if ($key != "sign" && $value != "" && !is_array($value)) {
            $buff .= $key . "=" . $value . "&";
         }
      }

      $buff = trim($buff, "&");
      
      return $buff;
   }
   
   /**
    * 根据具体的规则生成签名，不进行赋值操作
    * 
    * @return string
    */
   public function makeSign()
   {
      $values = $this->getValues();
      //1. 按字典排序参数
      ksort($values); 
      $string = $this->toUrlParams($values);
      //2. 在string后面添加KEY
      if('APP' == $this->getValue('trade_type')){
         $string = $string . '&key='. Constant::APP_KEY;
      }else{
         $string = $string . '&key='. Constant::MP_KEY;
      }
      
      //3.md5加密
      $string = md5($string);
      //4.字符转换为大写
      $result = strtoupper($string);
      
      return $result;
   }
   
   /**
    * 验证签名是否正确
    * 
    * @return boolean
    */
   public function checkSign()
   {
		if(!$this->isValueExist('sign')){
         $errorType = ErrorType::getInstance();
         Kernel\throw_exception(new Exception(
                 $errorType->msg('E_PAY_WECHAT_DATA_SIGN_ERROR'), $errorType->code('E_PAY_WECHAT_DATA_SIGN_ERROR')
                 ), $errorType);
		}
		
		$sign = $this->makeSign();
		if($this->getValue('sign') == $sign){
			return true;
		}
		$errorType = ErrorType::getInstance();
      Kernel\throw_exception(new Exception(
              $errorType->msg('E_PAY_WECHAT_DATA_SIGN_ERROR'), $errorType->code('E_PAY_WECHAT_DATA_SIGN_ERROR')
              ), $errorType);
   }  
   
   /**
	 * 使用数组初始化对象
    * 
	 * @param array $array
	 * @param 是否检测签名 $noCheckSign
	 */
	public static function initFromArray($array, $noCheckSign = false)
	{
		$obj = new self();
		$obj->setValues($array);
		if($noCheckSign == false){
			$obj->checkSign();
		}
      
      return $obj;
	}
   
    /**
     * 将xml转为array
     * 
     * @param string $xml
     */
	public static function initFromXml($xml)
	{	
		$obj = new self();
		$obj->arrayFromXml($xml);
      $values = $obj->getValues();
      if($obj->isValueExist('return_code') && 'SUCCESS' != $values['return_code']){
         return $obj->getValues();
      }
      
      $obj->checkSign();
      return $obj->getValues();
	}
   
}
