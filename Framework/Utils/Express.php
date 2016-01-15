<?php
/**
 * Cntysoft Cloud Software Team
 * 
 * @author Arvin <cntyfeng@163.com>
 * @copyright Copyright (c) 2010-2016 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license http://www.cntysoft.com/license/new-bsd     New BSD License
 */
namespace Cntysoft\Framework\Utils;
use Zend\Http\Client as HttpClient;
use Zend\Http\Request;
class Express
{
   //zend的http请求对象
   protected $httpClient = null;
   //快递100的api调用key值
   protected $apiKey = null;
   //快递公司名称与编码的对应关系
   protected $nameToCode = array(
      'shentong'       => '申通快递',
      'shunfeng'       => '顺丰速运',
      'yuantong'       => '圆通速递',
      'yunda'          => '韵达速递',
      'zhongtong'      => '中通快递',
      'tiantian'       => '天天快递',
      'huitongkuaidi'  => '百世汇通',
      'zhaijisong'     => '宅急送',
      'quanfengkuaidi' => '全峰快递',
      'debangwuliu'    => '德邦',
      'guotongkuaidi'  => '国通快递',
      'ems'            => '邮政EMS'
   );

   //快递查询的接口
   const EXPRESS_SEARCH_URL = 'http://www.kuaidi100.com/query?';
   const EXPRESS_SEARCH_API_URL = 'http://api.kuaidi100.com/api?';

   public function __construct($apiKey = null)
   {
      if ($apiKey) {
         $this->apiKey = $apiKey;
      }
   }

   /**
    * 获取快递公司的编码
    * 
    * @param string $name
    */
   public function getExpressCode($name)
   {
      $all = $this->getNameToCode();

      if (array_key_exists($name, $all)) {
         return $name;
      } else {
         foreach ($all as $key => $value) {
            if (false !== stripos($value, $name)) {
               return $key;
            }
         }
      }

      return '';
   }

   /**
    * 查询指定快递公司的快递信息
    * 
    * @param string $express 公司的名称或者编码
    * @param string $expressNumber
    * @return array
    */
   public function getExpressStatus($express, $expressNumber)
   {
      $expressCode = $this->getExpressCode($express);
      $url = self::EXPRESS_SEARCH_URL . 'type=' . $expressCode . '&postid=' . $expressNumber;
      $httpClient = $this->getHttpClient($url, array(
         'adapter'     => 'Zend\Http\Client\Adapter\Curl',
         'curloptions' => array(
            CURLOPT_HEADER         => 0,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_USERAGENT      => $_SERVER['HTTP_USER_AGENT'],
            CURLOPT_TIMEOUT        => 5
         )
      ));

      $request = new Request();
      $request->setMethod(Request::METHOD_GET);
      $request->setUri($url);
      $httpClient->setRequest($request);
      $response = $httpClient->send();
      $data = json_decode($response->getBody());

      if (!property_exists($data, 'message') || !in_array($data->message, array('ok')) || !property_exists($data, 'data')) {
         if ($this->apiKey) {
            $url = self::EXPRESS_SEARCH_API_URL . 'id=' . $this->apiKey . '&com=' . $expressCode . '&nu=' . $expressNumber;
            $httpClient->setUri($url);
            $request->setUri($url);
            $httpClient->setRequest($request);
            $response = $httpClient->send();
            $data = json_decode($response->getBody());
            if (!property_exists($data, 'message') || !in_array($data->message, array('ok')) || !property_exists($data, 'data')) {
               return array(
                  'code'  => 400,
                  'state' => 0,
                  'data'  => array()
               );
            }
         } else {
            return array(
               'code'  => 400,
               'state' => 0,
               'data'  => array()
            );
         }
      }

      $ret = array();
      foreach ($data->data as $val) {
         $item = array('time' => '', 'context' => '');
         if (property_exists($val, 'time')) {
            $item['time'] = $val->time;
         }
         if (property_exists($val, 'context')) {
            $item['context'] = $val->context;
         }

         array_push($ret, $item);
      }

      return array(
         'code'  => 200,
         'state' => (int) $data->state,
         'data'  => $ret
      );
   }

   /**
    * 获取快递公司的名称与编码的对应关系数组
    * 
    * @return array
    */
   protected function getNameToCode()
   {
      return $this->nameToCode;
   }

   /**
    * @param string $url
    * @param array $params
    * 
    * @return \Zend\Http\Client
    */
   protected function getHttpClient($url, $params = array())
   {
      if (null == $this->httpClient) {
         $this->httpClient = new HttpClient($url, $params);
      }
      return $this->httpClient;
   }

}