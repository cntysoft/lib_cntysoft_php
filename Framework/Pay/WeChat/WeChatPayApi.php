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
use Zend\Http\Client as HttpClient;
use Zend\Http\Request as HttpRequest;

class WeChatPayApi 
{
   /**
    * @var Zend\Http\Client 
    */
   protected static $httpClient = null;
   /**
	 * 统一下单，其中out_trade_no、body、total_fee、trade_type必填,appid、mchid、spbill_create_ip、nonce_str不需要填入
    * 
	 * @param Utils $inputObj
	 * @param int $timeOut
	 * @throws Exception
	 * @return 成功时返回，其他抛异常
	 */
	public static function unifiedOrder($inputObj, $timeOut = 6)
	{
		$url = Constant::UNIFIED_ORDER_URL;

		//检测必填参数
		if(!$inputObj->isValueExist('out_trade_no')) {
         $errorType = ErrorType::getInstance();
         Kernel\throw_exception(new Exception(
            $errorType->msg('E_PAY_WECHAT_UNIFIED_ORDER_NEED_OUT_TRADE_NO'), $errorType->code('E_PAY_WECHAT_UNIFIED_ORDER_NEED_OUT_TRADE_NO')
         ), $errorType);
		}else if(!$inputObj->isValueExist('body')){
         $errorType = ErrorType::getInstance();
         Kernel\throw_exception(new Exception(
            $errorType->msg('E_PAY_WECHAT_UNIFIED_ORDER_NEED_BODY'), $errorType->code('E_PAY_WECHAT_UNIFIED_ORDER_NEED_BODY')
         ), $errorType);
		}else if(!$inputObj->isValueExist('total_fee')) {
         $errorType = ErrorType::getInstance();
         Kernel\throw_exception(new Exception(
            $errorType->msg('E_PAY_WECHAT_UNIFIED_ORDER_NEED_TOTAL_FEE'), $errorType->code('E_PAY_WECHAT_UNIFIED_ORDER_NEED_TOTAL_FEE')
         ), $errorType);
		}else if(!$inputObj->isValueExist('trade_type')) {
         $errorType = ErrorType::getInstance();
         Kernel\throw_exception(new Exception(
            $errorType->msg('E_PAY_WECHAT_UNIFIED_ORDER_NEED_TRADE_TYPE'), $errorType->code('E_PAY_WECHAT_UNIFIED_ORDER_NEED_TRADE_TYPE')
         ), $errorType);
		}
		
		//关联参数
		if($inputObj->getValue('trade_type') == "JSAPI" && !$inputObj->isValueExist('openid')){
         $errorType = ErrorType::getInstance();
         Kernel\throw_exception(new Exception(
            $errorType->msg('E_PAY_WECHAT_UNIFIED_ORDER_NEED_OPEN_ID'), $errorType->code('E_PAY_WECHAT_UNIFIED_ORDER_NEED_OPEN_ID')
         ), $errorType);
		}
		if($inputObj->getValue('trade_type') == "NATIVE" && !$inputObj->isValueExist('product_id')){
         $errorType = ErrorType::getInstance();
         Kernel\throw_exception(new Exception(
            $errorType->msg('E_PAY_WECHAT_UNIFIED_ORDER_NEED_PRODUCT_ID'), $errorType->code('E_PAY_WECHAT_UNIFIED_ORDER_NEED_PRODUCT_ID')
         ), $errorType);
		}
		
		//异步通知url未设置，则使用配置文件中的url
		if(!$inputObj->isValueExist('notify_url')){
			$inputObj->setValue('notify_url', Constant::NOTIFY_URL);//异步通知url
		}
      if('APP' == $inputObj->getValue('trade_type')){
         $inputObj->setValue('appid', Constant::APP_APP_ID);
         $inputObj->setValue('mch_id', Constant::APP_MERCHANT_ID);
      }else{
         $inputObj->setValue('appid', Constant::MP_APP_ID);
         $inputObj->setValue('mch_id', Constant::MP_MERCHANT_ID);
      }
		
      $inputObj->setValue('spbill_create_ip', $_SERVER['REMOTE_ADDR']);
      $inputObj->setValue('nonce_str', self::getNonceStr());
      $inputObj->setValue('sign', $inputObj->makeSign());
      $xml = $inputObj->arrayToXml($inputObj->getValues());
		
		$startTimeStamp = self::getMillisecond();//请求开始时间
		$response = self::postXmlCurl($xml, $url, false, $timeOut);
		$result = Utils::initFromXml($response);
		self::reportCostTime($url, $startTimeStamp, $result);//上报请求花费时间
		
		return $result;
	}
   
   /**
	 * 查询订单，其中out_trade_no、transaction_id至少填一个,appid、mchid、spbill_create_ip、nonce_str不需要填入
	 * 
    * @param Utils $inputObj
	 * @param int $timeOut
	 * @throws Exception
	 * @return 成功时返回，其他抛异常
	 */
	public static function orderQuery($inputObj, $timeOut = 6)
	{
		$url = Constant::ORDER_QUERY_URL;
		//检测必填参数
		if(!$inputObj->isValueExist('out_trade_no') && !$inputObj->isValueExist('transaction_id')) {
         $errorType = ErrorType::getInstance();
         Kernel\throw_exception(new Exception(
            $errorType->msg('E_PAY_WECHAT_ORDER_QUERY_NEED_OUT_TRADE_NO_OR_TRANSACTION_ID'), $errorType->code('E_PAY_WECHAT_ORDER_QUERY_NEED_OUT_TRADE_NO_OR_TRANSACTION_ID')
         ), $errorType);
		}
      if('APP' == $inputObj->getValue('trage_type')){
         $inputObj->setValue('appid', Constant::APP_APP_ID);
         $inputObj->setValue('mch_id', Constant::APP_MERCHANT_ID);
      }else{
         $inputObj->setValue('appid', Constant::MP_APP_ID);
         $inputObj->setValue('mch_id', Constant::MP_MERCHANT_ID);
      }
      
      $inputObj->setValue('nonce_str', self::getNonceStr());
		$inputObj->setValue('sign', $inputObj->makeSign());
      $xml = $inputObj->arrayToXml($inputObj->getValues());
		
		$startTimeStamp = self::getMillisecond();//请求开始时间
		$response = self::postXmlCurl($xml, $url, false, $timeOut);
		$result = Utils::initFromXml($response);
		self::reportCostTime($url, $startTimeStamp, $result);//上报请求花费时间
		
		return $result;
	}
   
   /**
	 * 
	 * 关闭订单，其中out_trade_no必填
	 * appid、mchid、spbill_create_ip、nonce_str不需要填入
    * 
	 * @param Utils $inputObj
	 * @param int $timeOut
	 * @throws Exception
	 * @return 成功时返回，其他抛异常
	 */
	public static function closeOrder($inputObj, $timeOut = 6)
	{
		$url = Constant::ORDER_CLOSE_URL;
		//检测必填参数
		if(!$inputObj->isValueExist('out_trade_no')) {
         $errorType = ErrorType::getInstance();
         Kernel\throw_exception(new Exception(
            $errorType->msg('E_PAY_WECHAT_ORDER_CLOSE_NEED_OUT_TRADE_NO'), $errorType->code('E_PAY_WECHAT_ORDER_CLOSE_NEED_OUT_TRADE_NO')
         ), $errorType);
		}
      if('APP' == $inputObj->getValue('trade_type')){
         $inputObj->setValue('appid', Constant::APP_APP_ID);
         $inputObj->setValue('mch_id', Constant::APP_MERCHANT_ID);
      }else{
         $inputObj->setValue('appid', Constant::MP_APP_ID);
         $inputObj->setValue('mch_id', Constant::MP_MERCHANT_ID);
      }
      
      $inputObj->setValue('nonce_str', self::getNonceStr());
		$inputObj->setValue('sign', $inputObj->makeSign());
      $xml = $inputObj->arrayToXml($inputObj->getValues());
		
		$startTimeStamp = self::getMillisecond();//请求开始时间
		$response = self::postXmlCurl($xml, $url, false, $timeOut);
		$result = Utils::initFromXml($response);
		self::reportCostTime($url, $startTimeStamp, $result);//上报请求花费时间
		
		return $result;
	}
   
   /**
	 * 申请退款，其中out_trade_no、transaction_id至少填一个且
	 * out_refund_no、total_fee、refund_fee、op_user_id为必填参数
	 * appid、mchid、spbill_create_ip、nonce_str不需要填入
    * 
	 * @param Utils $inputObj
	 * @param int $timeOut
	 * @throws Exception
	 * @return 成功时返回，其他抛异常
	 */
	public static function refund($inputObj, $timeOut = 6)
	{
		$url = Constant::REFUND_URL;
		//检测必填参数
		if(!$inputObj->isValueExist('out_trade_no') && !$inputObj->isValueExist('transaction_id')) {
         $errorType = ErrorType::getInstance();
         Kernel\throw_exception(new Exception(
            $errorType->msg('E_PAY_WECHAT_REFUND_NEED_OUT_TRADE_NO_OR_TRANSACTION_ID'), $errorType->code('E_PAY_WECHAT_REFUND_NEED_OUT_TRADE_NO_OR_TRANSACTION_ID')
         ), $errorType);
		}else if(!$inputObj->isValueExist('out_refund_no')){
         $errorType = ErrorType::getInstance();
         Kernel\throw_exception(new Exception(
            $errorType->msg('E_PAY_WECHAT_REFUND_NEED_OUT_REFUND_NO'), $errorType->code('E_PAY_WECHAT_REFUND_NEED_OUT_REFUND_NO')
         ), $errorType);
		}else if(!$inputObj->isValueExist('total_fee')){
         $errorType = ErrorType::getInstance();
         Kernel\throw_exception(new Exception(
            $errorType->msg('E_PAY_WECHAT_REFUND_NEED_TOTAL_FEE'), $errorType->code('E_PAY_WECHAT_REFUND_NEED_TOTAL_FEE')
         ), $errorType);
		}else if(!$inputObj->isValueExist('refund_fee')){
         $errorType = ErrorType::getInstance();
         Kernel\throw_exception(new Exception(
            $errorType->msg('E_PAY_WECHAT_REFUND_NEED_REFUND_FEE'), $errorType->code('E_PAY_WECHAT_REFUND_NEED_REFUND_FEE')
         ), $errorType);
		}else if(!$inputObj->isValueExist('op_user_id')){
         $errorType = ErrorType::getInstance();
         Kernel\throw_exception(new Exception(
            $errorType->msg('E_PAY_WECHAT_REFUND_NEED_OP_USER_ID'), $errorType->code('E_PAY_WECHAT_REFUND_NEED_OP_USER_ID')
         ), $errorType);
		}
      if('APP' == $inputObj->getValue('trade_type')){
         $inputObj->setValue('appid', Constant::APP_APP_ID);
         $inputObj->setValue('mch_id', Constant::APP_MERCHANT_ID);
      }else{
         $inputObj->setValue('appid', Constant::MP_APP_ID);
         $inputObj->setValue('mch_id', Constant::MP_MERCHANT_ID);
      }
		
      $inputObj->setValue('nonce_str', self::getNonceStr());
		$inputObj->setValue('sign', $inputObj->makeSign());
      $xml = $inputObj->arrayToXml($inputObj->getValues());
      
		$startTimeStamp = self::getMillisecond();//请求开始时间
		$response = self::postXmlCurl($xml, $url, true, $timeOut);
		$result = Utils::initFromXml($response);
		self::reportCostTime($url, $startTimeStamp, $result);//上报请求花费时间
		
		return $result;
	}
   
   /**
	 * 查询退款
	 * 提交退款申请后，通过调用该接口查询退款状态。退款有一定延时，
	 * 用零钱支付的退款20分钟内到账，银行卡支付的退款3个工作日后重新查询退款状态。
    * 
	 * 其中out_refund_no、out_trade_no、transaction_id、refund_id四个参数必填一个
	 * appid、mchid、spbill_create_ip、nonce_str不需要填入
    * 
	 * @param Utils $inputObj
	 * @param int $timeOut
	 * @throws Exception
	 * @return 成功时返回，其他抛异常
	 */
	public static function refundQuery($inputObj, $timeOut = 6)
	{
		$url = Constant::REFUND_QUERY_URL;
		//检测必填参数
		if(!$inputObj->isValueExist('out_refund_no') &&
			!$inputObj->isValueExist('out_trade_no') &&
			!$inputObj->isValueExist('transaction_id') &&
			!$inputObj->isValueExist('refund_id')) {
			$errorType = ErrorType::getInstance();
         Kernel\throw_exception(new Exception(
            $errorType->msg('E_PAY_WECHAT_REFUND_QUERY_NEED_OUT_REFUND_NO_OR_OUT_TRADE_NO_OR_TRANSACTION_ID_OR_REFUND_ID'),
            $errorType->code('E_PAY_WECHAT_REFUND_QUERY_NEED_OUT_REFUND_NO_OR_OUT_TRADE_NO_OR_TRANSACTION_ID_OR_REFUND_ID')
         ), $errorType);
		}
		if('APP' == $inputObj->getValue('trade_type')){
         $inputObj->setValue('appid', Constant::APP_APP_ID);
         $inputObj->setValue('mch_id', Constant::APP_MERCHANT_ID);
      }else{
         $inputObj->setValue('appid', Constant::MP_APP_ID);
         $inputObj->setValue('mch_id', Constant::MP_MERCHANT_ID);
      }
      $inputObj->setValue('nonce_str', self::getNonceStr());
		$inputObj->setValue('sign', $inputObj->makeSign());
      $xml = $inputObj->arrayToXml($inputObj->getValues());
		
		$startTimeStamp = self::getMillisecond();//请求开始时间
		$response = self::postXmlCurl($xml, $url, false, $timeOut);
		$result = Utils::initFromXml($response);
		self::reportCostTime($url, $startTimeStamp, $result);//上报请求花费时间
		
		return $result;
	}
   
   /**
	 * 下载对账单，其中bill_date为必填参数
	 * appid、mchid、spbill_create_ip、nonce_str不需要填入
    * 
	 * @param Utils $inputObj
	 * @param int $timeOut
	 * @throws Exception
	 * @return 成功时返回，其他抛异常
	 */
	public static function downloadBill($inputObj, $timeOut = 6)
	{
		$url = Constant::DOWNLOAD_BILL_URL;
		//检测必填参数
		if(!$inputObj->isValueExist('bill_date')) {
         $errorType = ErrorType::getInstance();
         Kernel\throw_exception(new Exception(
            $errorType->msg('E_PAY_WECHAT_BILL_NEED_BILL_DATE'), $errorType->code('E_PAY_WECHAT_BILL_NEED_BILL_DATE')
         ), $errorType);
		}
		if('APP' == $inputObj->getValue('trade_type')){
         $inputObj->setValue('appid', Constant::APP_APP_ID);
         $inputObj->setValue('mch_id', Constant::APP_MERCHANT_ID);
      }else{
         $inputObj->setValue('appid', Constant::MP_APP_ID);
         $inputObj->setValue('mch_id', Constant::MP_MERCHANT_ID);
      }
      $inputObj->setValue('nonce_str', self::getNonceStr());
		$inputObj->setValue('sign', $inputObj->makeSign());
      $xml = $inputObj->arrayToXml($inputObj->getValues());
		
		$response = self::postXmlCurl($xml, $url, false, $timeOut);
		if(substr($response, 0 , 5) == "<xml>"){
			return "";
		}
		return $response;
	}
   
   /**
	 * 提交被扫支付API
	 * 收银员使用扫码设备读取微信用户刷卡授权码以后，二维码或条码信息传送至商户收银台，
	 * 由商户收银台或者商户后台调用该接口发起支付。
    * 
	 * 其中body、out_trade_no、total_fee、auth_code参数必填
	 * appid、mchid、spbill_create_ip、nonce_str不需要填入
	 * @param Utils $inputObj
	 * @param int $timeOut
	 */
	public static function micropay($inputObj, $timeOut = 10)
	{
		$url = Constant::MICRO_PARY_URL;
		//检测必填参数
		if(!$inputObj->isValueExist('body')) {
         $errorType = ErrorType::getInstance();
         Kernel\throw_exception(new Exception(
            $errorType->msg('E_PAY_WECHAT_MICRO_PAY_NEED_BODY'), $errorType->code('E_PAY_WECHAT_MICRO_PAY_NEED_BODY')
         ), $errorType);
		} else if(!$inputObj->isValueExist('out_trade_no')) {
         $errorType = ErrorType::getInstance();
         Kernel\throw_exception(new Exception(
            $errorType->msg('E_PAY_WECHAT_MICRO_PAY_NEED_OUT_TRADE_NO'), $errorType->code('E_PAY_WECHAT_MICRO_PAY_NEED_OUT_TRADE_NO')
         ), $errorType);
		} else if(!$inputObj->isValueExist('total_fee')) {
         $errorType = ErrorType::getInstance();
         Kernel\throw_exception(new Exception(
            $errorType->msg('E_PAY_WECHAT_MICRO_PAY_NEED_TOTAL_FEE'), $errorType->code('E_PAY_WECHAT_MICRO_PAY_NEED_TOTAL_FEE')
         ), $errorType);
		} else if(!$inputObj->isValueExist('auth_code')) {
         $errorType = ErrorType::getInstance();
         Kernel\throw_exception(new Exception(
            $errorType->msg('E_PAY_WECHAT_MICRO_PAY_NEED'), $errorType->code('E_PAY_WECHAT_MICRO_PAY_NEED')
         ), $errorType);
		}
		
		$inputObj->setValue('spbill_create_ip', $_SERVER['REMOTE_ADDR']);//终端ip
		if('APP' == $inputObj->getValue('trade_type')){
         $inputObj->setValue('appid', Constant::APP_APP_ID);
         $inputObj->setValue('mch_id', Constant::APP_MERCHANT_ID);
      }else{
         $inputObj->setValue('appid', Constant::MP_APP_ID);
         $inputObj->setValue('mch_id', Constant::MP_MERCHANT_ID);
      }
      $inputObj->setValue('nonce_str', self::getNonceStr());
		$inputObj->setValue('sign', $inputObj->makeSign());
      $xml = $inputObj->arrayToXml($inputObj->getValues());
		
		$startTimeStamp = self::getMillisecond();//请求开始时间
		$response = self::postXmlCurl($xml, $url, false, $timeOut);
		$result = Utils::initFromXml($response);
		self::reportCostTime($url, $startTimeStamp, $result);//上报请求花费时间
		
		return $result;
	}
   
   /**
	 * 撤销订单API接口，其中参数out_trade_no和transaction_id必须填写一个
	 * appid、mchid、spbill_create_ip、nonce_str不需要填入
    * 
	 * @param Utils $inputObj
	 * @param int $timeOut
	 * @throws Exception
	 */
	public static function reverse($inputObj, $timeOut = 6)
	{
		$url = Constant::REVERSE_URL;
		//检测必填参数
		if(!$inputObj->isValueExist('out_trade_no') && !$inputObj->isValueExist('transaction_id')) {
         $errorType = ErrorType::getInstance();
         Kernel\throw_exception(new Exception(
            $errorType->msg('E_PAY_WECHAT_REVERSE_NEED_OUT_TRADE_NO_OR_TRANSACTION_ID'), $errorType->code('E_PAY_WECHAT_REVERSE_NEED_OUT_TRADE_NO_OR_TRANSACTION_ID')
         ), $errorType);
		}
		
		$inputObj->setValue('spbill_create_ip', $_SERVER['REMOTE_ADDR']);//终端ip
		if('APP' == $inputObj->getValue('trade_type')){
         $inputObj->setValue('appid', Constant::APP_APP_ID);
         $inputObj->setValue('mch_id', Constant::APP_MERCHANT_ID);
      }else{
         $inputObj->setValue('appid', Constant::MP_APP_ID);
         $inputObj->setValue('mch_id', Constant::MP_MERCHANT_ID);
      }
      $inputObj->setValue('nonce_str', self::getNonceStr());
		$inputObj->setValue('sign', $inputObj->makeSign());
      $xml = $inputObj->arrayToXml($inputObj->getValues());
		
		$startTimeStamp = self::getMillisecond();//请求开始时间
		$response = self::postXmlCurl($xml, $url, true, $timeOut);
		$result = Utils::initFromXml($response);
		self::reportCostTime($url, $startTimeStamp, $result);//上报请求花费时间
		
		return $result;
	}
   
   /**
	 * 生成二维码规则,模式一生成支付二维码
	 * appid、mchid、spbill_create_ip、nonce_str不需要填入
    * 
	 * @param Utils $inputObj
	 * @param int $timeOut
	 * @throws Exception
	 * @return 成功时返回，其他抛异常
	 */
	public static function bizpayurl($inputObj, $timeOut = 6)
	{
		if(!$inputObj->isValueExist('product_id')){
			throw new WxPayException("生成二维码，缺少必填参数product_id！");
		}
		if('APP' == $inputObj->getValue('trade_type')){
         $inputObj->setValue('appid', Constant::APP_APP_ID);
         $inputObj->setValue('mch_id', Constant::APP_MERCHANT_ID);
      }else{
         $inputObj->setValue('appid', Constant::MP_APP_ID);
         $inputObj->setValue('mch_id', Constant::MP_MERCHANT_ID);
      }
      $inputObj->setValue('nonce_str', self::getNonceStr());
      $inputObj->setValue('time_stamp', time());
		$inputObj->setValue('sign', $inputObj->makeSign());
		
		return $inputObj->getValues();
	}
   
   /**
	 * 
	 * 转换短链接
	 * 该接口主要用于扫码原生支付模式一中的二维码链接转成短链接(WECHAT://wxpay/s/XXXXXX)，
	 * 减小二维码数据量，提升扫描速度和精确度。
	 * appid、mchid、spbill_create_ip、nonce_str不需要填入
    * 
	 * @param Utils $inputObj
	 * @param int $timeOut
	 * @throws Exception
	 * @return 成功时返回，其他抛异常
	 */
	public static function shorturl($inputObj, $timeOut = 6)
	{
		$url = Constant::SHORT_URL;
		//检测必填参数
		if(!$inputObj->isValueExist('long_url')) {
         $errorType = ErrorType::getInstance();
         Kernel\throw_exception(new Exception(
            $errorType->msg('E_PAY_WECHAT_SHORT_URL_NEED_LONG_URL'), $errorType->code('E_PAY_WECHAT_SHORT_URL_NEED_LONG_URL')
         ), $errorType);
		}
		if('APP' == $inputObj->getValue('trade_type')){
         $inputObj->setValue('appid', Constant::APP_APP_ID);
         $inputObj->setValue('mch_id', Constant::APP_MERCHANT_ID);
      }else{
         $inputObj->setValue('appid', Constant::MP_APP_ID);
         $inputObj->setValue('mch_id', Constant::MP_MERCHANT_ID);
      }
      $inputObj->setValue('nonce_str', self::getNonceStr());
		$inputObj->setValue('sign', $inputObj->makeSign());
      $xml = $inputObj->arrayToXml($inputObj->getValues());
		
		$startTimeStamp = self::getMillisecond();//请求开始时间
		$response = self::postXmlCurl($xml, $url, false, $timeOut);
		$result = Utils::initFromXml($response);
		self::reportCostTime($url, $startTimeStamp, $result);//上报请求花费时间
		
		return $result;
	}
   
   /**
 	 * 支付结果通用通知
    * 
 	 * @param function $callback
 	 * 直接回调函数使用方法: notify(you_function);
 	 * 回调类成员函数方法:notify(array($this, you_function));
 	 * $callback  原型为：function function_name($data){}
 	 */
	public static function notify($callback, &$msg)
	{
		//获取通知的数据
		$xml = file_get_contents('php://input');
		//如果返回成功则验证签名
		try {
			$result = Utils::initFromXml($xml);
		} catch (Exception $e){
			$msg = $e->getMessage();
			return false;
		}
		
		return call_user_func($callback, $result);
	}
   
   /**
	 * 直接输出xml
    * 
	 * @param string $xml
	 */
	public static function replyNotify($xml)
	{
		echo $xml;
	}
   
   /**
	 * 产生随机字符串，不长于32位
    * 
	 * @param int $length
	 * @return string
	 */
	public static function getNonceStr($length = 32) 
	{
		$chars = "abcdefghijklmnopqrstuvwxyz0123456789";  
		$str ="";
		for ( $i = 0; $i < $length; $i++ )  {  
			$str .= substr($chars, mt_rand(0, strlen($chars)-1), 1);  
		} 
      
		return $str;
	}
   
   /**
	 * 获取毫秒级别的时间戳
	 */
	private static function getMillisecond()
	{
		//获取毫秒的时间戳
		$time = explode (" ", microtime());
		$time = $time[1] . ($time[0] * 1000);
		$time2 = explode(".", $time);
		$time = $time2[0];
		return $time;
	}
   
   /**
	 * 以post方式提交xml到对应的接口url
	 * 
	 * @param string $xml  需要post的xml数据
	 * @param string $url  url
	 * @param bool $useCert 是否需要证书，默认不需要
	 * @param int $second   url执行超时时间，默认30s
	 * @throws Exception
	 */
	private static function postXmlCurl($xml, $url, $useCert = false, $second = 30)
	{		
      $data = Utils::initFromXml($xml);
      $options = array(
         'timeout' => $second,
         'adapter' => 'Zend\Http\Client\Adapter\Curl'
      );
		$curlOptions = array(
         CURLOPT_TIMEOUT => $second,
         CURLOPT_SSL_VERIFYPEER => true,
         CURLOPT_SSL_VERIFYHOST => 2,
         CURLOPT_HEADER => false,
         CURLOPT_RETURNTRANSFER => true,
         CURLOPT_POST => true,
         CURLOPT_POSTFIELDS => $xml,
         CURLOPT_URL => $url,
         CURLOPT_SSL_VERIFYPEER => true
      );
		//如果有配置代理这里就设置代理
		if('0.0.0.0' != Constant::CURL_PROXY_HOST && 0 != Constant::CURL_PROXY_PORT){
         $curlOptions[CURLOPT_PROXY] = Constant::CURL_PROXY_HOST;
         $curlOptions[CURLOPT_PROXYPORT] = Constant::CURL_PROXY_PORT;
		}

		if($useCert == true){
			//设置证书
			//使用证书：cert 与 key 分别属于两个.pem文件
         $curlOptions[CURLOPT_SSLCERTTYPE] = 'PEM';
         $curlOptions[CURLOPT_SSLKEYTYPE] = 'PEM';
         if('APP' == $data['trade_type']){
            $curlOptions[CURLOPT_SSLCERT] = Constant::APP_SSL_CERT_PATH;
            $curlOptions[CURLOPT_SSLKEY] = Constant::APP_SSL_KEY_PATH;
         }else{
            $curlOptions[CURLOPT_SSLCERT] = Constant::MP_SSL_CERT_PATH;
            $curlOptions[CURLOPT_SSLKEY] = Constant::MP_SSL_KEY_PATH;
         } 
		}
      
      $options['curlOptions'] = $curlOptions;
      $httpClient = self::getHttpClient($url, $options);
      $request = new HttpRequest();
      $request->setUri($url);
      $request->setMethod(HttpRequest::METHOD_POST);
      $httpClient->setRequest($request);
      $response = $httpClient->send();

		//返回结果
		if($response->isOk()){
			return $response->getBody();
		} else {
         $error = $response->getStatusCode();
         $errorType = ErrorType::getInstance();
         Kernel\throw_exception(new Exception(
            $errorType->msg('E_PAY_WECHAT_CURL_ERROR', $error), $errorType->code('E_PAY_WECHAT_CURL_ERROR')
         ), $errorType);
		}
	}
   
   /**
	 * 上报数据， 上报的时候将屏蔽所有异常流程
    * 
	 * @param string $url
	 * @param int $startTimeStamp
	 * @param array $data
	 */
	private static function reportCostTime($url, $startTimeStamp, $data)
	{
		//如果不需要上报数据
		if(0 == Constant::REPORT_LEVEL){
			return;
		} 
		//如果仅失败上报
		if(1 == Constant::REPORT_LEVEL &&
			 array_key_exists("return_code", $data) &&
			 'SUCCESS' == $data["return_code"] &&
			 array_key_exists("result_code", $data) &&
			 'SUCCESS' == $data["result_code"])
		 {
		 	return;
		 }
		 
		//上报逻辑
		$endTimeStamp = self::getMillisecond();
		$objInput = new Utils();
      $objInput->setValue('interface_url', $url);
      $objInput->setValue('execute_time_', $endTimeStamp - $startTimeStamp);
		//返回状态码
		if(array_key_exists("return_code", $data)){
         $objInput->setValue('return_code', $data["return_code"]);
		}
		//返回信息
		if(array_key_exists("return_msg", $data)){
         $objInput->setValue('return_msg', $data["return_msg"]);
		}
		//业务结果
		if(array_key_exists("result_code", $data)){
         $objInput->setValue('result_code', $data["result_code"]);
		}
		//错误代码
		if(array_key_exists("err_code", $data)){
         $objInput->setValue('err_code', $data["err_code"]);
		}
		//错误代码描述
		if(array_key_exists("err_code_des", $data)){
         $objInput->setValue('err_code_des', $data["err_code_des"]);
		}
		//商户订单号
		if(array_key_exists("out_trade_no", $data)){
         $objInput->setValue('out_trade_no', $data["out_trade_no"]);
		}
		//设备号
		if(array_key_exists("device_info", $data)){
         $objInput->setValue('device_info', $data["device_info"]);
		}
		
		try{
			self::report($objInput);
		} catch (Exception $e){
			//不做任何处理
		}
	}
   
   /**
	 * 测速上报，该方法内部封装在report中，使用时请注意异常流程
    * 
	 * 其中interface_url、return_code、result_code、user_ip、execute_time_必填
	 * appid、mchid、spbill_create_ip、nonce_str不需要填入
	 * @param Utils $inputObj
	 * @param int $timeOut
	 * @throws Exception
	 * @return 成功时返回，其他抛异常
	 */
	public static function report($inputObj, $timeOut = 1)
	{
		$url = "";
		//检测必填参数
		if(!$inputObj->isValueExist('interface_url')) {
         $errorType = ErrorType::getInstance();
         Kernel\throw_exception(new Exception(
            $errorType->msg('E_PAY_WECHAT_REPORT_NEED_INTERFACE_URL'), $errorType->code('E_PAY_WECHAT_REPORT_NEED_INTERFACE_URL')
         ), $errorType);
		} if(!$inputObj->isValueExist('return_code')) {
         $errorType = ErrorType::getInstance();
         Kernel\throw_exception(new Exception(
            $errorType->msg('E_PAY_WECHAT_REPORT_NEED_RETURN_CODE'), $errorType->code('E_PAY_WECHAT_REPORT_NEED_RETURN_CODE')
         ), $errorType);
		} if(!$inputObj->isValueExist('result_code')) {
         $errorType = ErrorType::getInstance();
         Kernel\throw_exception(new Exception(
            $errorType->msg('E_PAY_WECHAT_REPORT_NEED_RESULT_CODE'), $errorType->code('E_PAY_WECHAT_REPORT_NEED_RESULT_CODE')
         ), $errorType);
		} if(!$inputObj->isValueExist('user_ip')) {
         $errorType = ErrorType::getInstance();
         Kernel\throw_exception(new Exception(
            $errorType->msg('E_PAY_WECHAT_REPORT_NEED_USER_IP'), $errorType->code('E_PAY_WECHAT_REPORT_NEED_USER_IP')
         ), $errorType);
		} if(!$inputObj->isValueExist('execute_time_')) {
         $errorType = ErrorType::getInstance();
         Kernel\throw_exception(new Exception(
            $errorType->msg('E_PAY_WECHAT_REPORT_NEED_EXECUTE_TIME_'), $errorType->code('E_PAY_WECHAT_REPORT_NEED_EXECUTE_TIME_')
         ), $errorType);
		}
      if('APP' == $inputObj->getValue('trade_type')){
         $inputObj->setValue('appid', Constant::APP_APP_ID);
         $inputObj->setValue('mch_id', Constant::APP_MERCHANT_ID);
      }else{
         $inputObj->setValue('appid', Constant::MP_APP_ID);
         $inputObj->setValue('mch_id', Constant::MP_MERCHANT_ID);
      }
      $inputObj->setValue('user_ip', $_SERVER['REMOTE_ADDR']);
      $inputObj->setValue('time', date("YmdHis"));
      $inputObj->setValue('nonce_str', self::getNonceStr());
      $sign = $inputObj->makeSign();
		$inputObj->setValue('sign', $sign);
		$xml = $inputObj->arrayToXml($inputObj->getValues());
		
		$startTimeStamp = self::getMillisecond();//请求开始时间
		$response = self::postXmlCurl($xml, $url, false, $timeOut);
		return $response;
	}
   
   /**
    * @return \Zend\Http\Client
    */
   protected static function getHttpClient($url, $params)
   {
      if (null == self::$httpClient) {
         self::$httpClient = new HttpClient($url, $params);
      }
      return self::$httpClient;
   }
}
