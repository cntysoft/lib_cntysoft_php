<?php
/**
 * Cntysoft Cloud Software Team
 * 
 * @author ZhiHui <liuyan2526@qq.com>
 * @copyright Copyright (c) 2010-2015 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license http://www.cntysoft.com/license/new-bsd     New BSD License
*/
namespace Cntysoft\Framework\Pay\WeChatPay;
use Zend\Http\Client as HttpClient;
use Zend\Http\Request as HttpRequest;
use Zend\Http\Client\Adapter\Curl as CurlAdapter;
use Cntysoft\Kernel;
class Api
{
   /**
    * @var  \Zend\Http\Client
    */
   protected $client = null;
   /**
    * @var  \Zend\Http\Client\Adapter\Curl
    */
   protected $adapter = null;
   /**
    * @var string 微信appid
    */
   protected $appid = null;
   /**
    * @var string 微信商家id
    */
   protected $mchid = null;
   /**
    * @var string 微信回调地址
    */
   protected $notify = null;
   /**
    * 构造函数
    */
   public function __construct(array $config)
   {
      if(null == $this->appid || null == $this->mchid || null == $this->notify){
         $this->appid = $config['appid'];
         $this->mchid = $config['mchid'];
         $this->notify = $config['notify'];
      }
   }
   /**
    * 统一下单
    * 
    * @param array $orderParams 支付订单信息
    * @return array 返回信息
    */
   public function unifiedOrder(array $orderParams)
   {
      if (!array_key_exists('body', $orderParams)) {
         $errorType = ErrorType::getInstance();
         Kernel\throw_exception(new Exception(
                 $errorType->msg('E_WECHATPAY_UNIFIEDORDER_NO_PARAMS_BODY'), $errorType->code('E_WECHATPAY_UNIFIEDORDER_NO_PARAMS_BODY')
                 ), $errorType);
      } else if (!array_key_exists('out_trade_no', $orderParams)) {
         $errorType = ErrorType::getInstance();
         Kernel\throw_exception(new Exception(
                 $errorType->msg('E_WECHATPAY_UNIFIEDORDER_NO_PARAMS_OUTTRADENO'), $errorType->code('E_WECHATPAY_UNIFIEDORDER_NO_PARAMS_OUTTRADENO')
                 ), $errorType);
      } else if (!array_key_exists('total_fee', $orderParams)) {
         $errorType = ErrorType::getInstance();
         Kernel\throw_exception(new Exception(
                 $errorType->msg('E_WECHATPAY_UNIFIEDORDER_NO_PARAMS_TOTALFEE'), $errorType->code('E_WECHATPAY_UNIFIEDORDER_NO_PARAMS_TOTALFEE')
                 ), $errorType);
      } else if (!array_key_exists('spbill_create_ip', $orderParams)) {
         $errorType = ErrorType::getInstance();
         Kernel\throw_exception(new Exception(
                 $errorType->msg('E_WECHATPAY_UNIFIEDORDER_NO_PARAMS_CLIENTIP'), $errorType->code('E_WECHATPAY_UNIFIEDORDER_NO_PARAMS_CLIENTIP')
                 ), $errorType);
      } else if (!array_key_exists('trade_type', $orderParams)) {
         $errorType = ErrorType::getInstance();
         Kernel\throw_exception(new Exception(
                 $errorType->msg('E_WECHATPAY_UNIFIEDORDER_NO_PARAMS_TRADETYPE'), $errorType->code('E_WECHATPAY_UNIFIEDORDER_NO_PARAMS_TRADETYPE')
                 ), $errorType);
      }
      $unified = array(
         'appid'            => $this->appid,
         'mch_id'           => $this->mchid,
         'nonce_str'        => ShareFunction::createRandStr(),
         'body'             => $orderParams['body'],
         'out_trade_no'     => $orderParams['out_trade_no'],
         'total_fee'        => $orderParams['total_fee'],
         'spbill_create_ip' => $orderParams['spbill_create_ip'],
         'notify_url'       => $this->notify,
         'trade_type'       => $orderParams['trade_type']
      );
      if($orderParams['trade_type'] == 'JSAPI'){
         $unified['openid'] = $orderParams['openid'];
      }
      if (array_key_exists('device_info', $orderParams)) {
         $unified['device_info'] = $orderParams['device_info'];
      }
      if (array_key_exists('detail', $orderParams)) {
         $unified['detail'] = $orderParams['detail'];
      }
      if (array_key_exists('attach', $orderParams)) {
         $unified['attach'] = $orderParams['attach'];
      }
      if (array_key_exists('fee_type', $orderParams)) {
         $unified['fee_type'] = $orderParams['fee_type'];
      }
      if (array_key_exists('time_start', $orderParams)) {
         $unified['time_start'] = $orderParams['time_start'];
      }
      if (array_key_exists('time_expire', $orderParams)) {
         $unified['time_expire'] = $orderParams['time_expire'];
      }
      if (array_key_exists('goods_tag', $orderParams)) {
         $unified['goods_tag'] = $orderParams['goods_tag'];
      }
      if (array_key_exists('product_id', $orderParams)) {
         $unified['product_id'] = $orderParams['product_id'];
      }
      if (array_key_exists('limit_pay', $orderParams)) {
         $unified['limit_pay'] = $orderParams['limit_pay'];
      }
      if (array_key_exists('openid', $orderParams)) {
         $unified['openid'] = $orderParams['openid'];
      }
      $sign = ShareFunction::createSign($unified);
      $unified['sign'] = $sign;
      $unifiedXml = ShareFunction::arrayToXml($unified);
      $res = $this->curlHttp(Constant::WECHATPAY_UNIFIED_ORDER, true, $unifiedXml);
      $resArr = ShareFunction::arrayFromXml($res);
      if(isset($resArr['err_code']) || isset($resArr['err_code_des'])){
         throw new Exception($resArr['err_code_des']);
      }
      return $resArr;
   }

   /**
    * 查询订单API
    * 
    * @param array $orderParams
    * @return type
    */
   public function selectOrder(array $orderParams)
   {
      $select = array(
         'appid'     => $this->appid,
         'mch_id'    => $this->mchid,
         'nonce_str' => ShareFunction::createRandStr()
      );
      if (array_key_exists('out_trade_no', $orderParams)) {
         $select['out_trade_no'] = $orderParams['out_trade_no'];
      } else if (array_key_exists('transaction_id', $orderParams)) {
         $select['transaction_id'] = $orderParams['transaction_id'];
      } else {
         $errorType = ErrorType::getInstance();
         Kernel\throw_exception(new Exception(
                 $errorType->msg('E_WECHATPAY_UNIFIEDORDER_NO_PARAMS_OUTTRADENO'), $errorType->code('E_WECHATPAY_UNIFIEDORDER_NO_PARAMS_OUTTRADENO')
                 ), $errorType);
      }
      $sign = ShareFunction::createSign($select);
      $select['sign'] = $sign;
      $selectXml = ShareFunction::arrayToXml($select);
      $res = $this->curlHttp(Constant::WECHATPAY_SELECT_ORDER, true, $selectXml);
      return $res;
   }

   /**
    * 关闭订单API
    * 
    * @param array $orderParams
    * @return type
    */
   public function closeOrder(array $orderParams)
   {
      $select = array(
         'appid'     => $this->appid,
         'mch_id'    => $this->mchid,
         'nonce_str' => ShareFunction::createRandStr()
      );
      if (array_key_exists('out_trade_no', $orderParams)) {
         $select['out_trade_no'] = $orderParams['out_trade_no'];
      } else {
         $errorType = ErrorType::getInstance();
         Kernel\throw_exception(new Exception(
                 $errorType->msg('E_WECHATPAY_UNIFIEDORDER_NO_PARAMS_OUTTRADENO'), $errorType->code('E_WECHATPAY_UNIFIEDORDER_NO_PARAMS_OUTTRADENO')
                 ), $errorType);
      }
      $sign = ShareFunction::createSign($select);
      $select['sign'] = $sign;
      $selectXml = ShareFunction::arrayToXml($select);
      $res = $this->curlHttp(Constant::WECHATPAY_CLOSE_ORDER, true, $selectXml);
      return $res;
   }

   /**
    * 申请退款API
    * 
    * @param array $orderParams
    * @return type
    */
   public function askForRefund(array $orderParams)
   {
      $select = array(
         'appid'     => $this->appid,
         'mch_id'    => $this->mchid,
         'nonce_str' => ShareFunction::createRandStr()
      );
      if (array_key_exists('out_trade_no', $orderParams)) {
         $select['out_trade_no'] = $orderParams['out_trade_no'];
      } else if (array_key_exists('transaction_id', $orderParams)) {
         $select['transaction_id'] = $orderParams['transaction_id'];
      } else {
         $errorType = ErrorType::getInstance();
         Kernel\throw_exception(new Exception(
                 $errorType->msg('E_WECHATPAY_ASKFORREFUND_NO_PARAMS_ORDERID'), $errorType->code('E_WECHATPAY_ASKFORREFUND_NO_PARAMS_ORDERID')
                 ), $errorType);
      }
      if (array_key_exists('out_refund_no', $orderParams)) {
         $select['out_refund_no'] = $orderParams['out_refund_no'];
      } else {
         $errorType = ErrorType::getInstance();
         Kernel\throw_exception(new Exception(
                 $errorType->msg('E_WECHATPAY_ASKFORREFUND_NO_PARAMS_REFUNDID'), $errorType->code('E_WECHATPAY_ASKFORREFUND_NO_PARAMS_REFUNDID')
                 ), $errorType);
      }
      if (array_key_exists('total_fee', $orderParams)) {
         $select['total_fee'] = $orderParams['total_fee'];
      } else {
         $errorType = ErrorType::getInstance();
         Kernel\throw_exception(new Exception(
                 $errorType->msg('E_WECHATPAY_ASKFORREFUND_NO_PARAMS_TOTALFEE'), $errorType->code('E_WECHATPAY_ASKFORREFUND_NO_PARAMS_TOTALFEE')
                 ), $errorType);
      }
      if (array_key_exists('refund_fee', $orderParams)) {
         $select['refund_fee'] = $orderParams['refund_fee'];
      } else {
         $errorType = ErrorType::getInstance();
         Kernel\throw_exception(new Exception(
                 $errorType->msg('E_WECHATPAY_ASKFORREFUND_NO_PARAMS_REFUNDFEE'), $errorType->code('E_WECHATPAY_ASKFORREFUND_NO_PARAMS_REFUNDFEE')
                 ), $errorType);
      }
      if (array_key_exists('op_user_id', $orderParams)) {
         $select['op_user_id'] = $orderParams['op_user_id'];
      } else {
         $errorType = ErrorType::getInstance();
         Kernel\throw_exception(new Exception(
                 $errorType->msg('E_WECHATPAY_ASKFORREFUND_NO_PARAMS_OPUSERID'), $errorType->code('E_WECHATPAY_ASKFORREFUND_NO_PARAMS_OPUSERID')
                 ), $errorType);
      }
      if (array_key_exists('device_info', $orderParams)) {
         $select['device_info'] = $orderParams['device_info'];
      }
      if (array_key_exists('refund_fee_type', $orderParams)) {
         $select['refund_fee_type'] = $orderParams['refund_fee_type'];
      }
      $sign = ShareFunction::createSign($select);
      $select['sign'] = $sign;
      $selectXml = ShareFunction::arrayToXml($select);
      $res = $this->curlHttp(Constant::WECHATPAY_ASKFOR_REFUND, true, $selectXml);
      return $res;
   }

   /**
    * 查询退款API
    * 
    * @param array $orderParams
    * @return type
    */
   public function selectRefund(array $orderParams)
   {
      $select = array(
         'appid'     => $this->appid,
         'mch_id'    => $this->mchid,
         'nonce_str' => ShareFunction::createRandStr()
      );
      if (array_key_exists('out_trade_no', $orderParams)) {
         $select['out_trade_no'] = $orderParams['out_trade_no'];
      } else if (array_key_exists('transaction_id', $orderParams)) {
         $select['transaction_id'] = $orderParams['transaction_id'];
      } else if (array_key_exists('out_refund_no', $orderParams)) {
         $select['out_refund_no'] = $orderParams['out_refund_no'];
      } else if (array_key_exists('refund_id', $orderParams)) {
         $select['refund_id'] = $orderParams['refund_id'];
      } else {
         $errorType = ErrorType::getInstance();
         Kernel\throw_exception(new Exception(
                 $errorType->msg('E_WECHATPAY_SELECTREFUND_NO_PARAMS_ODDERID'), $errorType->code('E_WECHATPAY_SELECTREFUND_NO_PARAMS_ODDERID')
                 ), $errorType);
      }
      if (array_key_exists('device_info', $orderParams)) {
         $select['device_info'] = $orderParams['device_info'];
      }
      $sign = ShareFunction::createSign($select);
      $select['sign'] = $sign;
      $selectXml = ShareFunction::arrayToXml($select);
      $res = $this->curlHttp(Constant::WECHATPAY_SELECT_REFUND, true, $selectXml);
      return $res;
   }

   /**
    * 下在账单API
    * 
    * @param array $orderParams
    * @return type
    */
   public function downloadBill(array $orderParams)
   {
      $select = array(
         'appid'     => $this->appid,
         'mch_id'    => $this->mchid,
         'nonce_str' => ShareFunction::createRandStr(),
         'bill_date' => time()
      );
      if (array_key_exists('device_info', $orderParams)) {
         $select['device_info'] = $orderParams['device_info'];
      }
      if (array_key_exists('bill_type', $orderParams)) {
         $select['bill_type'] = $orderParams['bill_type'];
      }
      $sign = ShareFunction::createSign($select);
      $select['sign'] = $sign;
      $selectXml = ShareFunction::arrayToXml($select);
      $res = $this->curlHttp(Constant::WECHATPAY_DOWNLOAD_ORDER, true, $selectXml);
      return $res;
   }

   /**
    * 长连接变短连接API
    * 
    * @param array $orderParams
    * @return type
    */
   public function longlinkToShortlink(array $orderParams)
   {
      $select = array(
         'appid'     => $this->appid,
         'mch_id'    => $this->mchid,
         'nonce_str' => ShareFunction::createRandStr()
      );
      if (array_key_exists('long_url', $orderParams)) {
         $select['long_url'] = $orderParams['long_url'];
      } else {
         $errorType = ErrorType::getInstance();
         Kernel\throw_exception(new Exception(
                 $errorType->msg('E_WECHATPAY_LONGTOSHORT_NO_PARAMS_URL'), $errorType->code('E_WECHATPAY_LONGTOSHORT_NO_PARAMS_URL')
                 ), $errorType);
      }
      $sign = ShareFunction::createSign($select);
      $select['sign'] = $sign;
      $selectXml = ShareFunction::arrayToXml($select);
      $res = $this->curlHttp(Constant::WECHATPAY_LONGLINK_TO_SHORTLINK, true, $selectXml);
      return $res;
   }
   /**
    * curl方式的HTTP请求
    * 
    * @param string $url
    * @param boolean $isPost
    * @param xml $params
    * @return xml
    */
   public function curlHttp($url,$isPost,$params)
   {
      $client = $this->getHttpClient();
      $request = new HttpRequest();
      $adapter = $this->getCurlAdapter();
      $client->setAdapter($adapter);
      $option = array(
         'curloptions' => array(
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_HEADER         => 0
         )
      );
      if ($isPost) {
         $option['curloptions'][CURLOPT_POSTFIELDS] = $params;
         $option['curloptions'][CURLOPT_URL] = $url;
         $adapter->setOptions($option);
         $request->setMethod('POST');
         $request->setUri($url);
         $client->setRequest($request);
      } else {
         $adapter->setOptions($option);
         $request->setMethod('GET');
         $request->setUri($url . '?' . http_build_query($params));
         $client->setRequest($request);
      }
      $response = $client->send();
      if(200 !== $response->getStatusCode()){
         $errorType = ErrorType::getInstance();
         Kernel\throw_exception(new Exception(
            $errorType->msg('E_REQUEST_ERROR'),
            $errorType->code('E_REQUEST_ERROR')
         ), $errorType);
      }
      $ret = $response->getBody();
      return $ret;
   }
   /**
    * 单一模式获取\Zend\Http\Client
    * @return \Zend\Http\Client
    */
   public function getHttpClient()
   {
      if(null == $this->client){
         $this->client = new HttpClient();
      }
      return $this->client;
   }
   /**
    * 单一模式获取\Zend\Http\Client\Adapter\Curl
    * @return \Zend\Http\Client\Adapter\Curl
    */
   public function getCurlAdapter()
   {
      if(null == $this->adapter){
         $this->adapter = new CurlAdapter();
      }
      return $this->adapter;
   }

}