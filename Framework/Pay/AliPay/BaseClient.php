<?php
/**
 * Cntysoft Cloud Software Team
 * 
 * @author Changwang <chenyongwang1104@163.com>
 * @copyright Copyright (c) 2010-2011 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license http://www.cntysoft.com/license/new-bsd     New BSD License
 */
namespace Cntysoft\Framework\Pay\AliPay;
use Zend\Http\Client as HttpClient;
use Cntysoft\Kernel\ConfigProxy;
use Zend\Http\Request;
use Cntysoft\Kernel\StdDir;
class BaseClient
{
    //支付宝就口调用的service
    const SERVICE_TYPE_DIRECT_PAY = 'create_direct_pay_by_user'; //即时到帐
    const SERVICE_TYPE_BANK = 'create_direct_pay_by_user'; //网银支付
    const SERVICE_TYPE_WEB = 'alipay.wap.create.direct.pay.by.user'; //手机网站支付
    const SERVICE_TYPE_VERIFY_NOTIFY = 'notify_verify'; //验证回调
    //调用的支付宝接口网址
    const PAY_HTTP_GATEWAY = 'https://mapi.alipay.com/gateway.do';
    //请求参数的签名方式
    const PARAM_SIGN_TYPE_MD5 = 'MD5';
    const PARAM_SIGN_TYPE_DSA = 'DSA';
    const PARAM_SIGN_TYPE_RSA = 'RSA';
    //默认的字符集
    const INPUT_CHARSET = 'utf-8';
    const DEFAULT_BUY_TYPE = 1;//默认购买类型为商品购买
    //付款类型
    const PAY_TYPE_ALIPAY = 1; //支付宝余额
    const PAY_TYPE_BTC = 2; //个人网银支付
    const PAY_TYPE_BTB = 3; //企业网银支付
    /**
     * 支付宝余额支付的代码
     *
     * @var string
     */
    protected $payAliPayCode = 'ALIPAY';
    /**
     * BTB网银支付的银行代码
     *
     * @var array
     */
    protected $payBankBTBCode = array(
       'ICBCBTB', //中国工商银行（B2B）
       'ABCBTB', //中国农业银行（B2B）
       'CCBBTB', //中国建设银行（B2B）
       'SPDBB2B', //上海浦东发展银行（B2B）
       'BOCBTB', //中国银行（B2B）
       'CMBBTB' //招商银行（B2B）
    );
    
    /**
     * BTC网银支付的银行代码
     *
     * @var array
     */
    protected $payBankBTCCode = array(
       //对私账户转账
       'BOCB2C', //中国银行
       'ICBCB2C', //中国工商银行
       'CMB', //招商银行
       'CCB', //中国建设银行
       'ABC', //中国农业银行
       'SPDB', //上海浦东发展银行
       'CIB', //兴业银行
       'GDB', //广发银行
       'FDB', //富滇银行
       'HZCBB2C', //杭州银行
       'SHBANK', //上海银行
       'NBBANK', //宁波银行
       'SPABANK', //平安银行
       'POSTGC', //中国邮政储蓄银行
       'COMM-DEBIT' //交通银行
    );
    /**
     * Http调用类
     * 
     * @var HttpClient 
     */
    protected $httpClient;
    /**
     * 商户的身份ID
     * 
     * @var string 
     */
    protected $partner;
    /**
     * 调用支付宝接口的类型
     * 
     * @var string
     */
    protected $serviceType;
    /**
     * 异步回调的网址
     * 
     * @var string
     */
    protected $notifyUrl;
    /**
     * 同步通知的地址
     *
     * @var string
     */
    protected $returnUrl;
    /**
     * 账户的安全检验码
     *
     * @var string
     */
    protected $key;

    /**
     * 初始化参数
     * 
     * @param string $notifyUrl   回调通知的网址
     * @param string $returnUrl 同步回跳的网址
     * @param string $serviceType 调用支付宝接口的service类型
     * @param string $partner 商家的识别码
     */
    public function __construct($notifyUrl = null, $returnUrl = null, $serviceType = null)
    {
        $config = ConfigProxy::getFrameworkConfig('Pay');
        $this->key = $config->alipay->key;

        $this->serviceType = $serviceType ? $serviceType : self::SERVICE_TYPE_DIRECT_PAY;

        $this->partner = $config->alipay->partner;

        $this->notifyUrl = $notifyUrl;
    }

    /**
     * 把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
     * @param $param 需要拼接的数组
     * return 拼接完成以后的字符串
     */
    protected function createLinkstring($param)
    {
        $arg = "";
        foreach ($param as $key => $value) {
            $arg .= $key . "=" . $value . "&";
        }

        //去掉最后一个&字符
        $arg = substr($arg, 0, count($arg) - 2);

        return $arg;
    }

    /**
     * 把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串，并对字符串做urlencode编码
     * @param $param 需要拼接的数组
     * return 拼接完成以后的字符串
     */
    protected function createLinkstringUrlencode($param)
    {
        $arg = "";
        foreach ($param as $key => $value) {
            $arg .= $key . "=" . urlencode($value) . "&";
        }

        //去掉最后一个&字符
        $arg = substr($arg, 0, count($arg) - 2);

        return $arg;
    }

    /**
     * 除去数组中的空值和签名参数
     * @param $param 签名参数组
     * return 去掉空值与签名参数后的新签名参数组
     */
    protected function paramFilter($param)
    {
        $paraFilter = array();
        foreach ($param as $key => $value) {
            if ('sign' == $key || 'sign_type' == $key || '' == $value) {
                continue;
            } else {
                $paraFilter[$key] = $param[$key];
            }
        }

        return $paraFilter;
    }

    /**
     * 对数组排序
     * @param $param 排序前的数组
     * return 排序后的数组
     */
    protected function argSort($param)
    {
        ksort($param);
        reset($param);
        return $param;
    }

    /**
     * Post方式模拟远程HTTP协议提交
     * 
     * @param string $url
     * @param array $param
     * @return \Zend\Http\Response
     */
    public function getHttpResponsePOST($url, $param = array())
    {
        $uri = $url . '?_input_charset=utf-8';
        $cacert_url = $this->getSSLVerifyLocetion();

        $httpClient = $this->getHttpClient($uri, array(
           'adapter'     => 'Zend\Http\Client\Adapter\Curl',
           'curloptions' => array(
              CURLOPT_HEADER         => 0,
              CURLOPT_RETURNTRANSFER => 1,
              CURLOPT_SSL_VERIFYPEER => true,
              CURLOPT_SSL_VERIFYHOST => 2,
              CURLOPT_CAINFO         => $cacert_url,
              CURLOPT_POST           => true,
              CURLOPT_POSTFIELDS     => $param
           )
        ));

        $request = new Request();
        $request->setMethod(Request::METHOD_POST);
        $request->setUri($uri);
        $httpClient->setRequest($request);
        return $httpClient->send();

//        $curl = curl_init($url);
//        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true); //SSL证书认证
//        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2); //严格认证
//        curl_setopt($curl, CURLOPT_CAINFO, $cacert_url); //证书地址
//        curl_setopt($curl, CURLOPT_HEADER, 0); // 过滤HTTP头
//        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 显示输出结果
//        curl_setopt($curl, CURLOPT_POST, true); // post传输数据
//        curl_setopt($curl, CURLOPT_POSTFIELDS, $para); // post传输数据
//        $responseText = curl_exec($curl);
//
//        curl_close($curl);
//
//        return $responseText;
    }

    /**
     * Get方式模拟远程Http提交
     * 
     * @param string $url
     * @return Zend\Http\Response
     */
    public function getHttpResponseGET($url)
    {
        $cacert_url = $this->getSSLVerifyLocetion();

        $httpClient = $this->getHttpClient($url, array(
           'adapter'     => 'Zend\Http\Client\Adapter\Curl',
           'curloptions' => array(
              CURLOPT_HEADER         => 0,
              CURLOPT_RETURNTRANSFER => 1,
              CURLOPT_SSL_VERIFYPEER => true,
              CURLOPT_SSL_VERIFYHOST => 2,
              CURLOPT_CAINFO         => $cacert_url
           )
        ));

        $request = new Request();
        $request->setMethod(Request::METHOD_GET);
        $request->setUri($url);
        $httpClient->setRequest($request);
        return $httpClient->send();

//        $curl = curl_init($url);
//        curl_setopt($curl, CURLOPT_HEADER, 0); // 过滤HTTP头
//        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 显示输出结果
//        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true); //SSL证书认证
//        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2); //严格认证
//        curl_setopt($curl, CURLOPT_CAINFO, $cacert_url); //证书地址
//        $responseText = curl_exec($curl);
//        //var_dump( curl_error($curl) );//如果执行curl过程中出现异常，可打开此开关，以便查看异常内容
//        curl_close($curl);
//        return $responseText;
    }

    /**
     * 生成提交的表单, 用于在即时到帐, 网银支付的支付方式中
     * 
     * <code>
     *  array (
     *   'out_trade_no' => '', //订单的编号
     *   'subject' => '', //商品的名称
     *   'payment_type' => '', //支付的类型, 默认为 1 (商品购买)
     *   'total_fee' => '', //支付的总金额
     *   'it_b_pay' => '' //交易结束的时间, 默认为1天(1d)
     *  )
     * </code>
     * @param array $params
     * @return string
     */
    public function buildReuestForm($params)
    {
        $params = $params + array(
           'service'         => $this->serviceType,
           'partner'         => $this->partner,
           'sign_type'       => self::PARAM_SIGN_TYPE_MD5,
           '__input_charset' => self::INPUT_CHARSET,
           'notify_url'      => $this->notifyUrl,
           'seller_id'       => $this->partner,
        );
        //待请求参数数组
        $params = $this->buildRequestParam($params);

        $sHtml = "<form id='alipaysubmit' name='alipaysubmit' action='" . self::PAY_HTTP_GATEWAY . "?__input_charset=" . self::INPUT_CHARSET . "' method='post'>";
        foreach ($params as $key => $val) {
            $sHtml.= "<input type='hidden' name='" . $key . "' value='" . $val . "'/>";
        }

        //submit按钮控件请不要含有name属性
        $sHtml = $sHtml . "<input type='submit' value='' style='display:none;'></form>";

        $sHtml = $sHtml . "<script>document.forms['alipaysubmit'].submit();</script>";

        return $sHtml;
    }

    /**
     * @return \Zend\Http\Client
     */
    protected function getHttpClient($params = array())
    {
        if (null == $this->httpClient) {
            $this->httpClient = new HttpClient($params);
        }
        return $this->httpClient;
    }

    /**
     * 对请求的参数进行加密, 默认方式为 MD5
     * 
     * @param array $params
     * @return array
     */
    protected function buildRequestParam($params)
    {
        $sign = $this->generateMD5Sign($params);

        $params['sign'] = $sign;
        $params['sign_type'] = self::PARAM_SIGN_TYPE_MD5;

        return $params;
    }

    /**
     * 生成MD5加密的签名
     * 
     * @param array $params
     * @return string
     */
    protected function generateMD5Sign($params)
    {
        $params = $this->paramFilter($params);
        $sorts = $this->argSort($params);
        $string = $this->createLinkstring($sorts);

        return md5($string . $this->key);
    }
    
    protected function generateRSASign($params)
    {
        
    }

    /**
     * 验证支付宝回调的签名
     * 
     * @param array $params
     * @param string $notifySign
     * @return boolean
     */
    public function verifyNotifySign($params, $notifySign, $signType = 'MD5')
    {
        switch(strtoupper(trim($signType))) {
            case self::PARAM_SIGN_TYPE_MD5:
                $sign = $this->generateMD5Sign($params);
                break;
            case self::PARAM_SIGN_TYPE_RSA:
                $sign = $this->generateRSASign($params);
                break;
        }

        return $sign == $notifySign;
    }

    /**
     * 验证支付宝回调的源
     * 
     * @param string $notifyId
     * @return \Zend\Http\Response
     */
    public function verifyNotifySource($notifyId)
    {
        $httpClient = $this->getHttpClient();
        $request = new Request();

        $linkString = $this->createLinkstring(array(
           'service'   => self::SERVICE_TYPE_VERIFY_NOTIFY,
           'partner'   => $this->partner,
           'notify_id' => $notifyId
        ));
        $uri = self::PAY_HTTP_GATEWAY . '?' . $linkString;
        $request->setUri($uri);
        $request->setMethod(Request::METHOD_GET);

        $httpClient->setRequest($request);
        return $httpClient->send();
    }

    /**
     * 获取SSL加密证书的路径
     * 
     * @return string
     */
    protected function getSSLVerifyLocetion()
    {
        return StdDir::getFrameworkDataDir('Pay') . DS . 'AliPay' . DS . 'cacert.pem';
    }

    /**
     * 验证订单支付的类型和银行代码
     * 
     * @param int $type
     * @param string $bankCode
     * @return boolean
     */
    public function checkPayType($type, $bankCode)
    {
        $ret = true;
        switch($type) {
            case self::PAY_TYPE_ALIPAY:
                $ret = $bankCode !== $this->payAliPayCode ? false : true;
                break;
            case self::PAY_TYPE_BTB:
                $ret = !in_array($bankCode, $this->payBankBTBCode) ? false : true;
                break;
            case self::PAY_TYPE_BTC:
                $ret = !in_array($bankCode, $this->payBankBTCCode) ? false : true;
                break;
            default :
                $ret = false;
        }
        
        return $ret;
    }

}