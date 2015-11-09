<?php
/**
 * Cntysoft Cloud Software Team
 * 
 * @author Arvin <cntyfeng@163.com>
 * @copyright Copyright (c) 2010-2015 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license http://www.cntysoft.com/license/new-bsd     New BSD License
*/
namespace Cntysoft\Framework\Pay\WeChat;
use Zend\Http\Request as HttpRequest;
use Zend\Http\Client as HttpClient;

use Cntysoft\Kernel;
/**
 * JSAPI支付实现类
 * 该类实现了从微信公众平台获取code、通过code获取openid和access_token、
 * 生成jsapi支付js接口所需的参数、生成获取共享收货地址所需的参数
 */

class JsApiPay
{
   /**
	 * 网页授权接口微信服务器返回的数据，返回样例如下
	 * {
	 *  "access_token":"ACCESS_TOKEN",
	 *  "expires_in":7200,
	 *  "refresh_token":"REFRESH_TOKEN",
	 *  "openid":"OPENID",
	 *  "scope":"SCOPE",
	 *  "unionid": "o6_bmasdasdsad6_2sgVt7hMZOPfL"
	 * }
	 * 其中access_token可用于获取共享收货地址
	 * openid是微信支付jsapi支付接口必须的参数
	 * @var array
	 */
	public $data = null;
   
	/**
	 * 获取jsapi支付的参数
	 * @param array $UnifiedOrderResult 统一支付接口返回的数据
	 * @throws WxPayException
	 * 
	 * @return json数据，可直接填入js函数作为参数
	 */
	public function getJsApiParameters($UnifiedOrderResult)
	{
		if(!array_key_exists("appid", $UnifiedOrderResult)
		|| !array_key_exists("prepay_id", $UnifiedOrderResult)
		|| $UnifiedOrderResult['prepay_id'] == "")
		{
         $errorType = new ErrorType();
         Kernel\throw_exception(new Exception(
            $errorType->msg('E_PAY_WECHAT_JSAPI_PARAM_ERROR'), $errorType->code('E_PAY_WECHAT_JSAPI_PARAM_ERROR')
         ), $errorType);
		}
		$jsapi = new Utils();
      $jsapi->setValue('appId', $UnifiedOrderResult["appid"]);
      $timeStamp = time();
      $jsapi->setValue('timeStamp', "$timeStamp");
		$jsapi->set('nonceStr', WeChatPayApi::getNonceStr());
      $jsapi->set('package', "prepay_id=" . $UnifiedOrderResult['prepay_id']);
		$jsapi->set('signType', "MD5");
      $jsapi->set('paySign', Utils::makeSign());
		$jsapi->SetPaySign($jsapi->makeSign());
		$parameters = json_encode($jsapi->getValues());
		return $parameters;
	}
	
	/**
	 * 获取地址js参数
	 * 
	 * @return 获取共享收货地址js函数需要的参数，json格式可以直接做参数使用
	 */
	public function getEditAddressParameters()
	{	
      $utils = new Utils();
		$getData = $this->data;
		$data = array();
		$data["appid"] = Constant::MP_APP_ID;
		$data["url"] = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		$time = time();
		$data["timestamp"] = "$time";
		$data["noncestr"] = '"'.WeChatPayApi::getNonceStr().'"';
		$data["accesstoken"] = $getData["access_token"];
		ksort($data);
		$params = $utils->toUrlParams($data);
		$addrSign = sha1($params);
		
		$afterData = array(
			"addrSign" => $addrSign,
			"signType" => "sha1",
			"scope" => "jsapi_address",
			"appId" => Constant::MP_APP_ID,
			"timeStamp" => $data["timestamp"],
			"nonceStr" => $data["noncestr"]
		);
		$parameters = json_encode($afterData);
		return $parameters;
	}
	
   /**
	 * 通过跳转获取用户的openid，跳转流程如下：
	 * 1、设置自己需要调回的url及其其他参数，跳转到微信服务器https://open.weixin.qq.com/connect/oauth2/authorize
	 * 2、微信服务处理完成之后会跳转回用户redirect_uri地址，此时会带上一些参数，如：code
	 * 
	 * @return 用户的openid
	 */
	public function getOpenId()
	{
		//通过code获得openid
		if (!isset($_GET['code'])){
			//触发微信返回code码
			$baseUrl = urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].$_SERVER['QUERY_STRING']);
         $urlObj["appid"] = Constant::MP_APP_ID;
         $urlObj["redirect_uri"] = "$baseUrl";
         $urlObj["response_type"] = "code";
         $urlObj["scope"] = "snsapi_base";
         $urlObj["state"] = "STATE"."#wechat_redirect";
         $bizString = $this->toUrlParams($urlObj);
         $url = Constant::AUTHORIZE . $bizString;
			Header("Location: $url");
			exit();
		} else {
			//获取code码，以获取openid
		   $code = $_GET['code'];
			$openid = $this->getOpenidFromMp($code);
			return $openid;
		}
	}
   
   /**
	 * 通过code从工作平台获取openid机器access_token
	 * @param string $code 微信跳转回来带上的code
	 * 
	 * @return openid
	 */
	public function getOpenidFromMp($code)
	{
      $urlObj["appid"] = Constant::MP_APP_ID;
		$urlObj["secret"] = Constant::APP_SECRET;
		$urlObj["code"] = $code;
		$urlObj["grant_type"] = "authorization_code";
		$bizString = $this->toUrlParams($urlObj);
		$url = Constant::ACCESS_TOKEN.$bizString;
      
      $options = array(
         'adapter' => 'Zend\Http\Client\Adapter\Curl',
      );
		$curlOptions = array(
         CURLOPT_TIMEOUT => $this->curl_timeout,
         CURLOPT_URL => $url,
         CURLOPT_SSL_VERIFYPEER => false,
         CURLOPT_SSL_VERIFYHOST => false,
         CURLOPT_HEADER => false,
         CURLOPT_RETURNTRANSFER => true
      );

		if(Constant::CURL_PROXY_HOST != "0.0.0.0" 
			&& Constant::CURL_PROXY_PORT != 0){
         $curlOptions[CURLOPT_PROXY] = Constant::CURL_PROXY_HOST;
         $curlOptions[CURLOPT_PROXYPORT] = Constant::CURL_PROXY_PORT;
		}
      $options['curlOptions'] = $curlOptions;
      $httpClient = new HttpClient($url, $options);
      $request = new HttpRequest();
      $request->setUri($url);
      $request->setMethod(HttpRequest::METHOD_POST);
      $httpClient->setRequest($request);
      $response = $httpClient->send();
      $res = $response->getBody();
		//取出openid
		$data = json_decode($res,true);
      $this->data = $data;
		$openid = $data['openid'];
		return $openid;
	}
}

